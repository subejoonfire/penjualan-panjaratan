@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="flex justify-center">
                <img class="h-20 w-auto" src="{{ asset('images/logo.png') }}" alt="Penjualan Panjaratan" onerror="this.style.display='none'">
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Reset Password
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Masukkan password baru untuk akun Anda
            </p>
        </div>

        <form class="mt-8 space-y-6" action="{{ route('password.reset') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <!-- Password Baru -->
                <div>
                    <label for="password" class="sr-only">Password Baru</label>
                    <input id="password" name="password" type="password" required 
                           class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm @error('password') border-red-500 @enderror" 
                           placeholder="Password baru (minimal 6 karakter)">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label for="password_confirmation" class="sr-only">Konfirmasi Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required 
                           class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                           placeholder="Konfirmasi password baru">
                </div>

                <!-- Password Requirements -->
                <div class="text-sm text-gray-500">
                    <p class="font-medium">Syarat Password:</p>
                    <ul class="mt-1 space-y-1 list-disc list-inside">
                        <li>Minimal 6 karakter</li>
                        <li>Kombinasi huruf dan angka direkomendasikan</li>
                    </ul>
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
                    Reset Password
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
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    
    // Auto focus
    passwordInput.focus();
    
    // Password strength indicator (optional)
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = getPasswordStrength(password);
        // You can add visual feedback here
    });
    
    function getPasswordStrength(password) {
        let strength = 0;
        if (password.length >= 6) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        return strength;
    }
});
</script>
@endsection