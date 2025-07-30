@extends('layouts.app')

@section('title', 'Verifikasi Kode Reset')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="flex justify-center">
                <img class="h-20 w-auto" src="{{ asset('images/logo.png') }}" alt="Penjualan Panjaratan" onerror="this.style.display='none'">
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Verifikasi Kode Reset
            </h2>
            @if(session('reset_data'))
                <p class="mt-2 text-center text-sm text-gray-600">
                    Masukkan kode yang telah dikirim ke 
                    @if(session('reset_data')['method'] === 'email')
                        <span class="font-medium">{{ session('reset_data')['identifier'] }}</span>
                    @else
                        <span class="font-medium">WhatsApp {{ session('reset_data')['identifier'] }}</span>
                    @endif
                </p>
            @endif
        </div>

        <form class="mt-8 space-y-6" action="{{ route('password.verify-reset-code') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <!-- Input Kode Verifikasi -->
                <div>
                    <label for="token" class="sr-only">Kode Verifikasi</label>
                    <input id="token" name="token" type="text" required maxlength="6"
                           class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm text-center text-lg font-mono tracking-widest @error('token') border-red-500 @enderror" 
                           placeholder="000000"
                           autocomplete="off">
                    @error('token')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Kode -->
                <div class="text-center text-sm text-gray-500">
                    <p>Kode verifikasi berlaku selama <span class="font-medium">15 menit</span></p>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Verifikasi Kode
                </button>
            </div>

            <!-- Link Actions -->
            <div class="text-center space-y-2">
                <div>
                    <a href="{{ route('password.send-reset-code') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Kirim Ulang Kode
                    </a>
                </div>
                <div>
                    <a href="{{ route('password.request') }}" class="font-medium text-gray-600 hover:text-gray-500">
                        Kembali ke Form Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mt-4 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->has('error'))
            <div class="mt-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md">
                {{ $errors->first('error') }}
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tokenInput = document.getElementById('token');
    
    // Auto format input untuk kode verifikasi
    tokenInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
        if (value.length > 6) {
            value = value.substring(0, 6);
        }
        e.target.value = value;
    });
    
    // Auto focus
    tokenInput.focus();
});
</script>
@endsection