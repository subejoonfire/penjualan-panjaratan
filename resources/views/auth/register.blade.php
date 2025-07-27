@extends('layouts.app')

@section('title', 'Daftar - Penjualan Panjaratan')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">
                Buat Akun Anda
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Bergabunglah dengan marketplace Penjualan Panjaratan
            </p>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <form class="space-y-6" action="{{ route('register') }}" method="POST">
                @csrf
                
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">
                        Nama Pengguna
                    </label>
                    <div class="mt-1">
                        <input id="username" name="username" type="text" autocomplete="username" required
                               value="{{ old('username') }}"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('username') border-red-300 @enderror">
                    </div>
                    @error('username')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Alamat Email
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="{{ old('email') }}"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('email') border-red-300 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Kata Sandi
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('password') border-red-300 @enderror">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        Konfirmasi Kata Sandi
                    </label>
                    <div class="mt-1">
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>

                <!-- Hidden Role Field (Customer Only) -->
                <input type="hidden" name="role" value="customer">

                <!-- Role Information -->
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">
                                Pendaftaran Pembeli
                            </h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Anda akan mendaftar sebagai pembeli. Jika ingin menjadi penjual, silakan hubungi administrator setelah registrasi.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Phone (Optional) -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">
                        Nomor Telepon <span class="text-gray-400">(Opsional)</span>
                    </label>
                    <div class="mt-1">
                        <input id="phone" name="phone" type="tel" autocomplete="tel"
                               value="{{ old('phone') }}"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('phone') border-red-300 @enderror"
                               placeholder="08123456789">
                    </div>
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Terms and Conditions -->
                <div class="flex items-center">
                    <input id="terms" name="terms" type="checkbox" required
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="terms" class="ml-2 block text-sm text-gray-900">
                        Saya setuju dengan 
                        <a href="#" class="text-blue-600 hover:text-blue-500">Syarat dan Ketentuan</a>
                        dan 
                        <a href="#" class="text-blue-600 hover:text-blue-500">Kebijakan Privasi</a>
                    </label>
                </div>
                @error('terms')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Buat Akun
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative mb-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Sudah punya akun?</span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('login') }}"
                       class="w-full inline-flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        Masuk ke akun Anda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection