<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;

class UserAuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        try {
            // Validasi data request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'phone' => 'required|string',
            ]);

            if($request->has('email') && User::where('email', $request->email)->exists()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Email sudah terdaftar',
                    'data' => null
                ]);
            }
            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Semua data harus diisi',
                    'data' => $validator->errors(),
                ], 400);
            }

            // Membuat user baru
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
            ]);

            // Generate token untuk user
            $token = $user->createToken('LaravelApp')->plainTextToken;

            // Response sukses
            return response()->json([
                'status' => 201,
                'message' => 'Registration successful',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 201);

        } catch (Exception $e) {
            // Jika terjadi kesalahan sistem
            return response()->json([
                'status' => 500,
                'message' => 'Ada kesalahan sistem',
                'data' => null,
            ], 500);
        }
    }

    /**
     * Login user and return access token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            // Validasi data request
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            // Jika validasi gagal
            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Email dan password harus diisi',
                    'data' => $validator->errors(),
                ], 400);
            }

            // Cek apakah user ada di database
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Email atau password salah',
                    'data' => null,
                ], 400);
            }

            // Generate token untuk user
            $token = $user->createToken('LaravelApp')->plainTextToken;

            // Response sukses
            return response()->json([
                'status' => 200,
                'message' => 'Login successful',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 200);

        } catch (Exception $e) {
            // Jika terjadi kesalahan sistem
            return response()->json([
                'status' => 500,
                'message' => 'Ada kesalahan sistem',
                'data' => null,
            ], 500);
        }
    }
}
