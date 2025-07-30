@extends('layouts.app')

@section('title', 'Lupa Password')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="flex justify-center">
                <img class="h-20 w-auto" src="{{ asset('images/logo.png') }}" alt="Penjualan Panjaratan" onerror="this.style.display='none'">
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Lupa Password
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Masukkan email atau nomor WhatsApp untuk reset password
            </p>
        </div>

        <form class="mt-8 space-y-6" action="{{ route('password.send-reset-code') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <!-- Pilihan Metode Reset -->
                <div>
                    <label class="text-sm font-medium text-gray-700">Pilih Metode Reset:</label>
                    <div class="mt-2 space-y-2">
                        <label class="inline-flex items-center">
                            <input type="radio" class="form-radio text-indigo-600" name="reset_method" value="email" checked>
                            <span class="ml-2 text-sm text-gray-700">Email</span>
                        </label>
                        <label class="inline-flex items-center ml-6">
                            <input type="radio" class="form-radio text-indigo-600" name="reset_method" value="phone">
                            <span class="ml-2 text-sm text-gray-700">WhatsApp</span>
                        </label>
                    </div>
                    @error('reset_method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Input Email/Phone -->
                <div>
                    <label for="identifier" class="sr-only">Email atau WhatsApp</label>
                    <input id="identifier" name="identifier" type="text" required 
                           class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm @error('identifier') border-red-500 @enderror" 
                           placeholder="Masukkan email atau nomor WhatsApp" 
                           value="{{ old('identifier') }}">
                    @error('identifier')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tombol Submit -->
            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Kirim Kode Reset
                </button>
            </div>

            <!-- Link Kembali -->
            <div class="text-center">
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Kembali ke Login
                </a>
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
    const emailRadio = document.querySelector('input[value="email"]');
    const phoneRadio = document.querySelector('input[value="phone"]');
    const identifierInput = document.getElementById('identifier');
    
    function updatePlaceholder() {
        if (emailRadio.checked) {
            identifierInput.placeholder = 'Masukkan alamat email';
            identifierInput.type = 'email';
        } else {
            identifierInput.placeholder = 'Masukkan nomor WhatsApp';
            identifierInput.type = 'tel';
        }
    }
    
    emailRadio.addEventListener('change', updatePlaceholder);
    phoneRadio.addEventListener('change', updatePlaceholder);
    
    // Set initial placeholder
    updatePlaceholder();
});
</script>
@endsection