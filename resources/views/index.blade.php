@extends('layouts.app')

@section('content')
<div class="flex">
    <x-sidebar />
    <form class="w-full min-h-screen p-8 py-16 sm:py-20 md:py-24 lg:py-8 flex flex-col gap-8" method="POST" action="{{ route('machine.update') }}">
        @csrf
        <h1 class="text-black text-3xl font-semibold">Kelola Mesin</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white p-8 rounded-lg flex flex-col gap-4">
                <h2 class="text-2xl font-semibold">Mesin Pencuci</h2>
                <div class="flex flex-col gap-2">
                    <label class="font-medium mb-2">Total Mesin</label>
                    <input type="text" class="bg-gray-100 p-2 rounded-lg w-full" placeholder="Tarif Layanan" value="{{ $washing_machine->total_machine }}" type="number" inputmode="numeric" name="washing_machine_total"/>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-medium mb-2">Tarif Layanan</label>
                    <input type="text" class="bg-gray-100 p-2 rounded-lg w-full" placeholder="Tarif Layanan" value="{{ $washing_machine->price }}" type="number" inputmode="numeric" name="washing_machine_price"/>
                </div>
            </div>
            <div class="bg-white p-8 rounded-lg flex flex-col gap-4">
                <h2 class="text-2xl font-semibold">Mesin Pengering</h2>
                <div class="flex flex-col gap-2">
                    <label class="font-medium mb-2">Total Mesin</label>
                    <input type="text" class="bg-gray-100 p-2 rounded-lg w-full" placeholder="Tarif Layanan" value="{{ $drying_machine->total_machine }}" type="number" inputmode="numeric" name="drying_machine_total"/>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-medium mb-2">Tarif Layanan</label>
                    <input type="text" class="bg-gray-100 p-2 rounded-lg w-full" placeholder="Tarif Layanan" value="{{ $drying_machine->price }}" type="number" inputmode="numeric" name="drying_machine_price"/>
                </div>
            </div>
        </div>
        @if(session('error'))
        <div class="text-red-500">
            {{ session('error') }}
        </div>
        @endif
        @if(session('success'))
        <div class="text-primary">
            {{ session('success') }}
        </div>
        @endif
        <button class="bg-primary py-2 px-4 text-white rounded-lg w-fit mx-auto" type="submit">Simpan</button>
    </form>
</div>
@endsection
