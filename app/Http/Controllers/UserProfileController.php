<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    /**
     * Show the authenticated user's profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        try {
            $user = Auth::user();
            $user->image_url = $user->profile_picture ?  url('storage/images/profile_pictures/' . $user->profile_picture) : null;

            return response()->json([
                'status' => 200,
                'message' => 'Profil pengguna',
                'data' => $user,
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status' => 500,
                'message' => 'Ada kesalahan sistem',
                'data' => null,
            ], 500);
        }
    }

    /**
     * Update the authenticated user's profile (except picture).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            // Validasi input untuk nama, email, dan phone
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255,' . Auth::id(),
                'phone' => 'required|string|max:20',
            ]);

            if ($request->has('email') && User::where('email', $request->email)->exists() && $request->email != Auth::user()->email) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Email sudah terdaftar',
                    'data' => null
                ], 400);
            }
            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Semua data harus diisi',
                    'data' => $validator->errors(),
                ], 400);
            }

            $user = Auth::user();

            if ($user instanceof User) {
                $user->fill([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                ]);
                $user->save();
                $user->image_url = $user->profile_picture ?  url('storage/images/profile_pictures/' . $user->profile_picture) : null;

                return response()->json([
                    'status' => 200,
                    'message' => 'Profil berhasil diperbarui',
                    'data' => $user,
                ], 200);
            } else {
                throw new Exception('User not found');
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Ada kesalahan sistem',
                'data' =>  $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the authenticated user's profile picture (Base64 encoded).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updatePicture(Request $request)
    {
        try {
            // Validasi input gambar
            $validator = Validator::make($request->all(), [
                'profile_picture' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi file gambar
            ]);

            if ($validator->fails()) {
                // Jika validasi gagal, log error dan return response
                Log::error('Validation failed for profile picture:', $validator->errors()->toArray());
                return response()->json([
                    'status' => 400,
                    'message' => 'Gambar tidak valid atau tidak ditemukan',
                    'data' => $validator->errors(),
                ], 400);
            }

            // Memeriksa apakah file profile_picture ada
            if ($request->hasFile('profile_picture')) {
                $image = $request->file('profile_picture');

                // Log File Info untuk memastikan file diterima
                Log::info('Received file:', [
                    'file_name' => $image->getClientOriginalName(),
                    'file_size' => $image->getSize(),
                    'file_extension' => $image->getClientOriginalExtension()
                ]);

                // Membuat nama file unik
                $imageName = uniqid('profile_') . '.' . $image->getClientOriginalExtension();

                // Menyimpan gambar ke public/images/profile_pictures
                $image->storeAs('images/profile_pictures', $imageName, 'public');

                // Mengambil user yang sedang login
                $user = Auth::user();

                // Menghapus gambar lama jika ada
                if ($user->profile_picture && Storage::disk('public')->exists('images/profile_pictures/' . $user->profile_picture)) {
                    Storage::disk('public')->delete('images/profile_pictures/' . $user->profile_picture);
                }

                // Mengupdate kolom profile_picture di user
                $user->profile_picture = $imageName;
                if ($user instanceof User) {
                    $user->save();
                }


                $user->image_url = $user->profile_picture ?  url('storage/images/profile_pictures/' . $user->profile_picture) : null;


                return response()->json([
                    'status' => 200,
                    'message' => 'Foto profil berhasil diperbarui',
                    'data' => $user,
                ], 200);
            } else {
                // Jika file tidak ditemukan
                return response()->json([
                    'status' => 400,
                    'message' => 'Gambar tidak ditemukan',
                    'data' => null,
                ], 400);
            }
        } catch (Exception $e) {
            // Menambahkan log error untuk debugging
            Log::error('Error updating profile picture: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTrace(),
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'Ada kesalahan sistem',
                'data' => null,
            ], 500);
        }
    }



    /**
     * Delete the authenticated user's profile picture.
     *
     * @return \Illuminate\Http\Response
     */
    public function deletePicture()
    {
        try {
            // Mendapatkan user yang sedang login
            $user = Auth::user();

            // Memeriksa apakah user memiliki foto profil
            if (!$user->profile_picture) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Tidak ada foto profil untuk dihapus',
                    'data' => null,
                ], 400);
            }

            // Menyusun path gambar profil yang ada
            $profilePicturePath = 'images/profile_pictures/' . $user->profile_picture;

            // Menghapus gambar profil dari storage jika ada
            if (Storage::disk('public')->exists($profilePicturePath)) {
                Storage::disk('public')->delete($profilePicturePath);
            }

            // Menghapus referensi gambar dari kolom profile_picture
            $user->profile_picture = null;
            if ($user instanceof User) {
                $user->save();
            }

            return response()->json([
                'status' => 200,
                'message' => 'Foto profil berhasil dihapus',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            // Menambahkan log error untuk debugging
            Log::error('Error deleting profile picture: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTrace(),
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'Ada kesalahan sistem',
                'data' => null,
            ], 500);
        }
    }
}
