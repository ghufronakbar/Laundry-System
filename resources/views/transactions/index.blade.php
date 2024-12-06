@extends('layouts.app')

@section('content')
<div class="flex">
    <x-sidebar />
    <div class="w-full min-h-screen p-8 py-16 sm:py-20 md:py-24 lg:py-8 flex flex-col gap-8">

        <h1 class="text-black text-3xl font-semibold">Riwayat Transaksi</h1>

        <div id="transaction-table">
            @if ($transactions->isEmpty())
                <p>No transactions found.</p>
            @else
                <table class="table-auto w-full border-collapse" id="transactionsTable">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border">ID Reservasi</th>
                            <th class="px-4 py-2 border">Name</th>
                            <th class="px-4 py-2 border">Mesin</th>
                            <th class="px-4 py-2 border">Total</th>
                            <th class="px-4 py-2 border">Waktu Reservasi</th>
                            <th class="px-4 py-2 border">Status</th>
                            <th class="px-4 py-2 border"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr class="transaction-row">
                                <td class="px-4 py-2 border transaction-id">{{ $transaction->reservation_id }}</td>
                                <td class="px-4 py-2 border user-name">{{ $transaction->user_name }}</td>
                                <td class="px-4 py-2 border machine-name">{{ $transaction->machine_name }} {{ $transaction->machine_number }}</td>
                                <td class="px-4 py-2 border">{{ $transaction->payment_total ? 'Rp ' . number_format($transaction->payment_total, 0, ',', '.') : '-' }}</td>
                                <td class="px-4 py-2 border">{{ formatDate($transaction->reservation_date) }} {{ formatTime($transaction->reservation_date) }}</td>
                                <td class="px-4 py-2 border status">{{ $transaction->status }}</td>
                                <td class="px-4 py-2 border">
                                    <a href="{{ route('transactions.show', $transaction->reservation_id) }}" class="text-blue-500">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
