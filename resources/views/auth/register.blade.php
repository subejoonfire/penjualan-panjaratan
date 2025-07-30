<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar - Penjualan Panjaratan</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-600 mb-6">
                    <i class="fas fa-store text-white text-xl"></i>
                </div>
                <h2 class="text-3xl font-extrabold text-gray-900">
                    Buat Akun Anda
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Bergabunglah dengan marketplace Penjualan Panjaratan
                </p>
            </div>

            <!-- Registration Form -->
            <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                <form action="{{ route('register') }}" method="POST" class="p-8">
                    @csrf

                    <!-- Two Column Layout for Desktop, Single Column for Mobile -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Username -->
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700">
                                    Nama Pengguna <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 relative">
                                    <input id="username" name="username" type="text" autocomplete="username" required
                                        value="{{ old('username') }}"
                                        class="appearance-none block w-full px-3 py-2 pl-10 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('username') border-red-300 @enderror"
                                        placeholder="contoh: usernameku">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Username tidak boleh mengandung spasi dan akan
                                    digunakan untuk login.</p>
                                @error('username')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nickname -->
                            <div>
                                <label for="nickname" class="block text-sm font-medium text-gray-700">
                                    Nama Panggilan
                                </label>
                                <div class="mt-1 relative">
                                    <input id="nickname" name="nickname" type="text" autocomplete="nickname"
                                        value="{{ old('nickname') }}"
                                        class="appearance-none block w-full px-3 py-2 pl-10 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('nickname') border-red-300 @enderror"
                                        placeholder="contoh: John Doe">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-id-card text-gray-400"></i>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Nama panggilan boleh mengandung spasi dan akan
                                    ditampilkan di profil Anda.</p>
                                @error('nickname')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    Alamat Email <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 relative">
                                    <input id="email" name="email" type="email" autocomplete="email" required
                                        value="{{ old('email') }}"
                                        class="appearance-none block w-full px-3 py-2 pl-10 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('email') border-red-300 @enderror"
                                        placeholder="contoh@email.com">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                </div>
                                @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Phone (WA) -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">
                                    Nomor WhatsApp <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 relative">
                                    <input id="phone" name="phone" type="tel" autocomplete="tel" required
                                        value="{{ old('phone') }}"
                                        class="appearance-none block w-full px-3 py-2 pl-10 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('phone') border-red-300 @enderror"
                                        placeholder="08xxxxxxxxxx">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fab fa-whatsapp text-gray-400"></i>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Nomor WhatsApp akan digunakan untuk verifikasi
                                    akun.</p>
                                @error('phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    Kata Sandi <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 relative">
                                    <input id="password" name="password" type="password" autocomplete="new-password"
                                        required
                                        class="appearance-none block w-full px-3 py-2 pl-10 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('password') border-red-300 @enderror"
                                        placeholder="Minimal 6 karakter">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                </div>
                                @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password Confirmation -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                    Konfirmasi Kata Sandi <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 relative">
                                    <input id="password_confirmation" name="password_confirmation" type="password"
                                        autocomplete="new-password" required
                                        class="appearance-none block w-full px-3 py-2 pl-10 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        placeholder="Ulangi kata sandi">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Role Field -->
                    <input type="hidden" name="role" value="customer">

                    <!-- Role Information -->
                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">
                                    Pendaftaran Pembeli
                                </h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Anda akan mendaftar sebagai pembeli. Jika ingin menjadi penjual, silakan hubungi
                                        administrator setelah registrasi.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mt-6 flex items-center">
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
                    <div class="mt-6">
                        <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            Buat Akun
                        </button>
                    </div>

                    <!-- Login Link -->
                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                Masuk di sini
                            </a>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-md">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md">
                <p class="font-medium">Terdapat kesalahan dalam form:</p>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Footer -->
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} Harlan Muradi. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Auto-focus on first input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });

        // Handle form validation feedback
        document.querySelector('form').addEventListener('submit', function(e) {
            const terms = document.getElementById('terms');
            if (!terms.checked) {
                e.preventDefault();
                alert('Anda harus menyetujui Syarat dan Ketentuan untuk melanjutkan.');
                terms.focus();
            }
        });
    </script>
</body>

</html>