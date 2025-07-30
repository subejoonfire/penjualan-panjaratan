<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi Kode Reset - Penjualan Panjaratan</title>

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
                Verifikasi Kode Reset
            </h2>
            @if(session('reset_data'))
                <p class="mt-2 text-center text-sm text-gray-600">
                    Masukkan kode yang telah dikirim ke 
                    @if(session('reset_data')['method'] === 'email')
                        <span class="font-medium text-blue-600">{{ session('reset_data')['identifier'] }}</span>
                    @else
                        <span class="font-medium text-green-600">WhatsApp {{ session('reset_data')['identifier'] }}</span>
                    @endif
                </p>
            @endif
        </div>

        <!-- Main Form -->
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <form class="p-8 space-y-6" action="{{ route('password.verify-reset-code') }}" method="POST">
                @csrf
                
                <!-- Input Kode Verifikasi -->
                <div>
                    <label for="token" class="block text-sm font-medium text-gray-700">Kode Verifikasi</label>
                    <div class="mt-1 relative">
                        <input id="token" name="token" type="text" required maxlength="6"
                               class="appearance-none rounded-md relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm text-center text-lg font-mono tracking-widest @error('token') border-red-500 @enderror" 
                               placeholder="000000"
                               autocomplete="off">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-key text-gray-400"></i>
                        </div>
                    </div>
                    @error('token')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Kode -->
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center justify-center">
                        <i class="fas fa-clock text-blue-500 mr-2"></i>
                        <p class="text-sm text-blue-700">
                            Kode verifikasi berlaku selama <span class="font-medium">15 menit</span>
                        </p>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-check text-blue-500 group-hover:text-blue-400"></i>
                        </span>
                        Verifikasi Kode
                    </button>
                </div>

                <!-- Link Actions -->
                <div class="text-center space-y-3">
                    <div>
                        <a href="{{ route('password.send-reset-code') }}" class="font-medium text-blue-600 hover:text-blue-500">
                            <i class="fas fa-redo mr-1"></i>
                            Kirim Ulang Kode
                        </a>
                    </div>
                    <div>
                        <a href="{{ route('password.request') }}" class="font-medium text-gray-600 hover:text-gray-500">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Kembali ke Form Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

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

        @if($errors->has('error'))
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">{{ $errors->first('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

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
        
        // Auto submit when 6 digits entered
        tokenInput.addEventListener('input', function(e) {
            if (e.target.value.length === 6) {
                // Optional: Auto submit form when 6 digits are entered
                // e.target.form.submit();
            }
        });
    });
    </script>
</body>

</html>