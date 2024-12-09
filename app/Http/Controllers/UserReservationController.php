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
use Midtrans\Transaction as MidtransTransaction;

class UserReservationController extends Controller

{

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 400,
                'message' => 'Unauthorized',
            ], 400);
        }

        $reservations = Reservation::where('user_id', $user->id)
            ->leftJoin('payments', 'reservations.id', '=', 'payments.reservation_id')
            ->select('reservations.*', 'payments.snap_token as snap_token', 'payments.total as payment_total',)->get();

        $reservations->each(function ($reservation) {
            if (Carbon::now()->greaterThan($reservation->created_at->addMinutes(30)) && $reservation->status !== 'PAID' && $reservation->status !== 'CANCELLED') {
                $reservation->status = 'EXPIRED';
                $reservation->save();
            }
        });


        $reservations = Reservation::where('user_id', $user->id)
            ->leftJoin('payments', 'reservations.id', '=', 'payments.reservation_id')
            ->leftJoin('machines', 'reservations.machine_id', '=', 'machines.id')
            ->select('reservations.id', 'reservations.created_at', 'reservations.machine_number', 'reservations.status', 'reservations.reservation_date', 'reservations.reservation_end', 'machines.name as machine_name')
            ->orderBy('reservations.created_at')
            ->get();

        foreach ($reservations as $reservation) {
            $normalizeMachineName = $reservation->machine_name === 'WASHING' ? 'Pencuci' : 'Pengering';
            $reservation->title = "Reservasi Mesin " . $normalizeMachineName . " " . $reservation->machine_number;
            unset($reservation['machine_name']);
            unset($reservation['machine_number']);
            unset($reservation['created_at']);
        };

        $completed = $reservations->filter(function ($reservation) {
            return $reservation->status === 'PAID' && Carbon::now()->greaterThan($reservation->reservation_end);
        })->values();

        $on_going = $reservations->filter(function ($reservation) {
            return $reservation->status === 'PAID' && Carbon::now()->lessThanOrEqualTo($reservation->reservation_end);
        })->values();

        $cancelled = $reservations->filter(function ($reservation) {
            return $reservation->status === 'CANCELLED' || $reservation->status === 'EXPIRED' || ($reservation->status !== 'PAID' && Carbon::now()->greaterThan($reservation->created_at->addMinutes(30)));
        })->values();

        $unpaid = $reservations->filter(function ($reservation) {
            return $reservation->status === 'PENDING';
        })->values();

        $data = [
            'completed' => $completed,
            'on_going' => $on_going,
            'cancelled' => $cancelled,
            'unpaid' => $unpaid
        ];

        return response()->json([
            'status' => 200,
            'message' => 'Riwayat Reservasi',
            'data' => $data,
        ], 200);
    }

    public function show(Request $request, $id)
    {
        try {
            Config::$serverKey = config('midtrans.server_key');
            Config::$clientKey = config('midtrans.client_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Unauthorized',
                ], 400);
            }

            $reservation = Reservation::leftJoin('payments', 'reservations.id', '=', 'payments.reservation_id')
                ->select('reservations.id', 'reservations.created_at', 'reservations.machine_number', 'reservations.status', 'reservations.reservation_date', 'reservations.reservation_end', 'payments.snap_token as snap_token', 'payments.total as payment_total', 'payments.payment_method', 'payments.paid_at', 'payments.direct_url',)
                ->find($id);

            if (!$reservation) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Tidak ada data',
                ], 400);
            }
            $payment = Payment::where('reservation_id', $reservation->id)->first();
            $status = null; // @var object|null $status
            if (!$payment) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Tidak ada data',
                ], 400);
            }

            if ($payment->paid_at !== null) {
                $status = MidtransTransaction::status($reservation->id);
            }

            if ($reservation->status !== 'PAID' || $payment->payment_method === null || $payment->paid_at === null) {
                try {
                    $status = MidtransTransaction::status($reservation->id);
                    if (isset($status->transaction_status) && $status->transaction_status === 'settlement') {
                        $payment->payment_method = $status->payment_type;
                        $payment->paid_at = Carbon::parse($status->settlement_time)->format('Y-m-d\TH:i:s.u\Z');
                        $payment->save();

                        $reservation->status = 'PAID';
                        $reservation->save();
                    }
                } catch (\Exception $e) {
                }
            }

            // Tentukan custom status untuk reservasi berdasarkan statusnya
            $reservation = Reservation::leftJoin('payments', 'reservations.id', '=', 'payments.reservation_id')
                ->leftJoin('machines', 'reservations.machine_id', '=', 'machines.id')
                ->select('reservations.id', 'reservations.created_at', 'reservations.machine_number', 'machines.name as machine_name', 'reservations.status', 'reservations.reservation_date', 'reservations.reservation_end', 'payments.snap_token as snap_token', 'payments.total as payment_total', 'payments.payment_method', 'payments.paid_at', 'payments.direct_url',)
                ->find($id);

            $normalizeMachineName = $reservation->machine_name === "WASHING" ? "Pencuci" : "Pengering";
            $reservation->title = "Reservasi Mesin " . $normalizeMachineName . " " . $reservation->machine_number;

            unset($reservation['machine_name']);
            unset($reservation['machine_number']);

            if ($reservation->status === 'PAID' && Carbon::now()->greaterThan($reservation->reservation_end)) {
                $reservation->custom_status = 'COMPLETED';
            } else if ($reservation->status === 'PAID' && Carbon::now()->lessThanOrEqualTo($reservation->reservation_end)) {
                $reservation->custom_status = 'ON_GOING';
            } else if ($reservation->status === 'CANCELLED' || $reservation->status === 'EXPIRED' || ($reservation->status !== 'PAID' && Carbon::now()->greaterThan($reservation->created_at->addMinutes(30)))) {
                $reservation->custom_status = 'CANCELLED';
            } else if ($reservation->status === 'PENDING') {
                $reservation->custom_status = 'UNPAID';
            }

            $reservationData = $reservation->toArray();

            return response()->json([
                'status' => 200,
                'message' => 'Detail Reservasi',
                'data' => $reservationData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ]);
        }
    }


    public function store(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

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
            ->where('reservation_date', Carbon::parse($request->date . ' ' . $request->time)->format('Y-m-d\TH:i:s.u') . 'Z')
            ->where('machine_number', $request->machine_number)
            ->where(function ($query) {
                $query->where('status', 'PAID')->orWhere('status', 'PENDING');
            })
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
            $reservation->reservation_date = Carbon::parse($request->date . ' ' . $request->time)->format('Y-m-d\TH:i:s.u') . 'Z';
            $reservation->reservation_end = Carbon::parse($request->date . ' ' . $request->time)->addMinutes(30)->format('Y-m-d\TH:i:s.u') . 'Z';
            // return response()->json([
            //     'reservation_date' => Carbon::parse($request->date . ' ' . $request->time)->format('Y-m-d\TH:i:s.u') . 'Z',
            //     'reservation_end' => Carbon::parse($request->date . ' ' . $request->time)->addMinutes(30)->format('Y-m-d\TH:i:s.u') . 'Z',
            // ]);
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

            // Generate Snap Token and Redirect URL
            $snapToken = Snap::getSnapToken($transaction);
            $redirect_url = Snap::createTransaction($transaction)->redirect_url;

            // Create payment object
            $payment = new Payment();
            $payment->reservation_id = $reservation->id;
            $payment->total = $reservation->total;
            $payment->payment_method = null;
            $payment->snap_token = $snapToken;
            $payment->paid_at = null;
            $payment->direct_url = $redirect_url;
            $payment->save();

            // Modify response to include payment inside reservation
            $reservationData = $reservation->toArray();
            $reservationData['payment'] = $payment;

            return response()->json([
                'status' => 200,
                'message' => 'Reservation created successfully',
                'data' => [
                    'reservation' => $reservationData,  // Embed payment inside reservation
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

    public function update(Request $request, $id)
    {
        $reservation = Reservation::find($id);
        if (!$reservation) {
            return response()->json([
                'status' => 400,
                'message' => 'Tidak ada data',
            ], 400);
        }

        if ($reservation->status === 'PAID') {
            return response()->json([
                'status' => 400,
                'message' => 'Reservasi sudah dibayar, tidak bisa dibatalkan',
            ], 400);
        } else {
            $reservation->status = 'CANCELLED';
            $reservation->save();
            return response()->json([
                'status' => 200,
                'message' => 'Reservasi berhasil dibatalkan',
            ], 200);
        }
    }
}
