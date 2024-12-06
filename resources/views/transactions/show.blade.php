@extends('layouts.app')

@section('content')
<div class="flex">
    <x-sidebar />
    <div class="w-full min-h-screen p-8 py-16 sm:py-20 md:py-24 lg:py-8 flex flex-col gap-8">
        <h1 class="text-black text-3xl font-semibold">Riwayat Transaksi</h1>
        <div class="w-full flex flex-col md:flex-row gap-4">
            <div class="w-full flex flex-col md:w-1/2 lg:w-2/3 border border-gray-300 rounded-lg p-4 gap-2">
                <h2 class="text-xl font-semibold mb-4">Detail Reservasi</h2>
                <table class="table-auto w-full border-collapse">
                    <tbody>
                        <tr>
                            <td class="px-4 py-2 border">ID Reservasi</td>
                            <td class="px-4 py-2 border">{{ $transaction->reservation_id }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border">Mesin</td>
                            <td class="px-4 py-2 border">{{ $transaction->machine_name }} {{ $transaction->machine_number }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border">Status</td>
                            <td class="px-4 py-2 border">{{ $transaction->status }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border">Tanggal Reservasi</td>
                            <td class="px-4 py-2 border">{{ formatDate($transaction->reservation_date) }} {{ formatTime($transaction->reservation_date) }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border">Nama</td>
                            <td class="px-4 py-2 border">{{ $transaction->user_name }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="w-full flex flex-col md:w-1/2 lg:w-1/3 border border-gray-300 rounded-lg p-4 gap-2">
                <h2 class="text-xl font-semibold mb-4">Detail Pembayaran</h2>
                <table class="table-auto w-full border-collapse">
                    <tbody>
                        <tr>
                            <td class="px-4 py-2 border">ID Pembayaran</td>
                            <td class="px-4 py-2 border">{{ $transaction->payment_id }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border">Total</td>
                            <td class="px-4 py-2 border">{{ $transaction->payment_total ? 'Rp ' . number_format($transaction->payment_total, 0, ',', '.') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border">Metode Pembayaran</td>
                            <td class="px-4 py-2 border">{{ $transaction->payment_method ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 border">Waktu Pembayaran</td>
                            <td class="px-4 py-2 border">{{ formatDate($transaction->paid_at) }} {{ formatTime($transaction->paid_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
</div>
</div>
@endsection
