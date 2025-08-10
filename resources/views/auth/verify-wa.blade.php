<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi WhatsApp - {{ env('MAIL_FROM_NAME', 'Penjualan Panjaratan') }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 p-8">
        <!-- Header -->
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-600">
                <i class="fas fa-store text-white text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Verifikasi WhatsApp Anda
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Kode verifikasi telah dikirim ke 
                <span class="font-medium text-green-600">{{ $user->phone }}</span>
            </p>
        </div>

        <!-- Main Form -->
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="p-8 space-y-6">
                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm">{{ $errors->first() }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Verification Form -->
                <form method="POST" action="{{ route('verification.wa.check') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Input Kode Verifikasi -->
                    <div>
                        <label for="token" class="block text-sm font-medium text-gray-700">Kode Verifikasi</label>
                        <div class="mt-1 relative">
                            <input id="token" name="token" type="text" maxlength="10" required autofocus
                                   class="appearance-none rounded-md relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-center text-lg font-mono tracking-widest" 
                                   placeholder="ABC123"
                                   autocomplete="off">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                <i class="fab fa-whatsapp text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-check text-green-500 group-hover:text-green-400"></i>
                            </span>
                            Verifikasi WhatsApp
                        </button>
                    </div>
                </form>

                <!-- Resend Code Form -->
                <form method="POST" action="{{ route('verification.wa.send') }}">
                    @csrf
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-green-600 rounded-md shadow-sm text-sm font-medium text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                        <i class="fas fa-redo mr-2"></i>
                        Kirim Ulang Kode
                    </button>
                </form>

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Keluar / Logout
                    </button>
                </form>

                <!-- Info -->
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center justify-center">
                        <i class="fab fa-whatsapp text-green-500 mr-2"></i>
                        <p class="text-sm text-green-700">
                            Periksa pesan WhatsApp Anda untuk kode verifikasi
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center">
            <p class="text-xs text-gray-500">
                &copy; {{ date('Y') }} Harlan Muradi. All rights reserved.
            </p>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tokenInput = document.getElementById('token');
        
        // Auto focus
        tokenInput.focus();
        
        // Convert to uppercase for better readability
        tokenInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
    });
    </script>
</body>

</html>