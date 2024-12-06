<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // Menampilkan daftar transaksi
    public function index()
    {
        // Mengambil data reservation, users, payments, dan machines dengan join
        $transactions = Reservation::join('users', 'reservations.user_id', '=', 'users.id')
            ->join('payments', 'reservations.id', '=', 'payments.reservation_id')
            ->join('machines', 'reservations.machine_id', '=', 'machines.id')  // Menambahkan join ke tabel machines
            ->select(
                'reservations.id as reservation_id',
                'reservations.machine_number',
                'reservations.status',
                'reservations.reservation_date',
                'payments.total as payment_total',
                'payments.payment_method',
                'payments.snap_token',
                'payments.paid_at',
                'users.name as user_name',
                'users.email as user_email',
                'machines.name as machine_name',
                'machines.price as machine_price'
            )
            ->get();

        return view('transactions.index', compact('transactions'));
    }

    public function show($id)
    {
        // Ambil detail transaksi berdasarkan ID
        $transaction = Reservation::join('users', 'reservations.user_id', '=', 'users.id')
            ->join('payments', 'reservations.id', '=', 'payments.reservation_id')
            ->join('machines', 'reservations.machine_id', '=', 'machines.id')
            ->where('reservations.id', $id)
            ->select(
                'reservations.id as reservation_id',
                'reservations.machine_number',
                'reservations.status',
                'reservations.reservation_date',
                'payments.total as payment_total',
                'payments.payment_method',
                'payments.snap_token',
                'payments.paid_at',
                'payments.id as payment_id',
                'users.name as user_name',
                'users.email as user_email',
                'machines.name as machine_name',
                'machines.price as machine_price'
            )
            ->first();

        return view('transactions.show', compact('transaction'));
    }
}
