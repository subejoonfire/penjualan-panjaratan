@extends('layouts.app')
@section('title', 'Verifikasi Email')
@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">Verifikasi Email Anda</h2>
            <p class="mt-2 text-sm text-gray-600">Kode verifikasi telah dikirim ke <span class="font-semibold">{{ $user->email }}</span></p>
        </div>
    </div>
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            @if(session('success'))
                <div class="mb-4 text-green-700 bg-green-100 border border-green-200 rounded p-2 text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 text-red-700 bg-red-100 border border-red-200 rounded p-2 text-sm">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('verification.email.check') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="token" class="block text-sm font-medium text-gray-700">Kode Verifikasi</label>
                    <input id="token" name="token" type="text" maxlength="6" required autofocus class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Verifikasi Email</button>
                </div>
            </form>
            <form method="POST" action="{{ route('verification.email.send') }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-blue-600 rounded-md shadow-sm text-sm font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Kirim Ulang Kode</button>
            </form>
        </div>
    </div>
</div>
@endsection