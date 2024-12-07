<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Payment;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Midtrans\Config;
use Midtrans\Snap;

class UserReservationController extends Controller

{

    public function store(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;  // optional, jika ingin sanitize input
        Config::$is3ds = true; // optional, jika ingin menggunakan 3DS

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 400,
                'message' => 'Unauthorized',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'machine_id' => 'required',
            'machine_number' => 'required|numeric',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Bad Request',
                'errors' => $validator->errors(),
            ], 400);
        }

        $machine = Machine::find($request->machine_id);

        if (!$machine) {
            return response()->json([
                'status' => 400,
                'message' => 'Mesin tidak ditemukan',
            ], 400);
        }

        if ($request->machine_number > $machine->total_machine || $request->machine_number < 1) {
            return response()->json([
                'status' => 400,
                'message' => 'Nomor mesin tidak valid',
            ], 400);
        }

        $reservation = Reservation::where('machine_id', $request->machine_id)
            ->where('reservation_date', $request->date . ' ' . $request->time)
            ->where('machine_number', $request->machine_number)
            ->first();

        if ($reservation) {
            return response()->json([
                'status' => 400,
                'message' => 'Sudah ada reservasi untuk mesin ini pada waktu tersebut',
            ], 400);
        }

        try {
            $reservation = new Reservation();
            $reservation->machine_id = $request->machine_id;
            $reservation->machine_number = $request->machine_number;
            $reservation->reservation_date = $request->date . ' ' . $request->time;
            $reservation->reservation_end = Carbon::parse($request->date . ' ' . $request->time)->addMinutes(30)->format('Y-m-d H:i');
            $reservation->status = 'PENDING';
            $reservation->user_id = $user->id;
            $reservation->total = $machine->price;
            $reservation->save();

            $orderId = $reservation->id;
            $transactionDetails = [
                'order_id' => $orderId,
                'gross_amount' => $reservation->total,
            ];

            $transaction = [
                'transaction_details' => $transactionDetails,
            ];
            // 'reservation_id', 'total', 'payment_method', 'snap_token', 'paid_at'
            $snapToken = Snap::getSnapToken($transaction);
            $redirect_url = Snap::createTransaction($transaction)->redirect_url;
            $payment = new Payment();

            $payment->reservation_id = $reservation->id;
            $payment->total = $reservation->total;
            $payment->payment_method = null;
            $payment->snap_token = $snapToken;
            $payment->paid_at = null;
            $payment->save();
            $payment->redirect_url = $redirect_url;

            return response()->json([
                'status' => 200,
                'message' => 'Reservation created successfully',
                'data' => [
                    'reservation' => $reservation,
                    'payment' => $payment
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
