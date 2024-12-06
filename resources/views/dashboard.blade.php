@extends('layouts.app')

@section('content')
<div class="flex">
    <x-sidebar />
    <div class="w-full min-h-screen p-8 py-16 sm:py-20 md:py-24 lg:py-8 flex flex-col gap-8">
        <h1 class="text-black text-3xl font-semibold">Dashboard</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white p-8 rounded-lg">
                <h2 class="text-2xl font-semibold mb-4">Reservasi Hari Ini</h2>
                <p class="text-gray-600">Mesin Pencuci: {{ $count_reservation_washing_today }}</p>
                <p class="text-gray-600">Mesin Pengering: {{ $count_reservation_drying_today }}</p>
            </div>
            <div class="bg-white p-8 rounded-lg">
                <h2 class="text-2xl font-semibold mb-4">Pemasukan</h2>
                <p class="text-gray-600">Bulan Ini: {{ 'Rp ' . number_format($income_monthly, 0, ',', '.') }}</p>
                <p class="text-gray-600">7 Hari Terakhir: {{ 'Rp ' . number_format($income_weekly, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white p-8 rounded-lg">
                <h2 class="text-2xl font-semibold mb-4">Transaksi</h2>
                <p class="text-gray-600">Total: {{ $total_all_transactions }}</p>
                <p class="text-gray-600">Transaksi Dibatalkan: {{ $total_cancelled_transactions }}</p>
            </div>
            <div class="bg-white p-8 rounded-lg">
                <h2 class="text-2xl font-semibold mb-4">Mesin Pencuci</h2>
                <p class="text-gray-600">Total: {{ $total_washing_machines }}</p>
                <p class="text-gray-600 text-sm">Sedang Digunakan: {{ $unavailable_washing_machines }}/{{ $total_washing_machines }}</p>
            </div>
            <div class="bg-white p-8 rounded-lg">
                <h2 class="text-2xl font-semibold mb-4">Mesin Pengering</h2>
                <p class="text-gray-600">Total: {{ $total_drying_machines }}</p>
                <p class="text-gray-600 text-sm">Sedang Digunakan: {{ $unavailable_drying_machines }}/{{ $total_drying_machines }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
