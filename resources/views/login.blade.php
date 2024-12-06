@extends('layouts.app')

@section('content')
<div class="w-full min-h-screen flex items-center justify-center">
    <form class="flex flex-col gap-4 bg-white p-8 rounded-lg min-w-[80%] md:min-w-[50%] lg:min-w-[40%]" method="POST" action="{{ route('login') }}">
        @csrf

        <h1 class="font-semibold text-4xl mb-8">MyLaundry</h1>

        <div class="flex flex-col gap-2">
            <label class="font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="bg-gray-100 p-2 rounded-lg" required/>
        </div>

        <div class="flex flex-col gap-2">
            <label class="font-medium">Password</label>
            <input type="password" name="password" class="bg-gray-100 p-2 rounded-lg" required/>
        </div>

        @if(session('error'))
        <div class="text-red-500">
            {{ session('error') }}
        </div>
        @endif


        <button type="submit" class="bg-primary p-2 text-white rounded-lg mt-2">Login</button>
    </form>
</div>
@endsection
