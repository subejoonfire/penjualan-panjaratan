<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lupa Password - Penjualan Panjaratan</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    
    <!-- Preload critical resources -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 p-8">
        <!-- Header -->
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-600">
                <i class="fas fa-store text-white text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Lupa Password
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Masukkan email atau nomor WhatsApp untuk reset password
            </p>
        </div>

        <!-- Main Form -->
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <form class="p-8 space-y-6" action="{{ route('password.send-reset-code') }}" method="POST">
                @csrf
                
                <!-- Pilihan Metode Reset -->
                <div>
                    <label class="text-sm font-medium text-gray-700">Pilih Metode Reset:</label>
                    <div class="mt-3 space-y-3">
                        <label class="inline-flex items-center">
                            <input type="radio" class="form-radio text-blue-600 h-4 w-4" name="reset_method" value="email" checked>
                            <span class="ml-3 text-sm text-gray-700 flex items-center">
                                <i class="fas fa-envelope text-blue-500 mr-2"></i>
                                Email
                            </span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" class="form-radio text-blue-600 h-4 w-4" name="reset_method" value="phone">
                            <span class="ml-3 text-sm text-gray-700 flex items-center">
                                <i class="fab fa-whatsapp text-green-500 mr-2"></i>
                                WhatsApp
                            </span>
                        </label>
                    </div>
                    @error('reset_method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Input Email/Phone -->
                <div>
                    <label for="identifier" class="block text-sm font-medium text-gray-700">Email atau WhatsApp</label>
                    <div class="mt-1 relative">
                        <input id="identifier" name="identifier" type="text" required 
                               class="appearance-none rounded-md relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('identifier') border-red-500 @enderror" 
                               placeholder="Masukkan email atau nomor WhatsApp" 
                               value="{{ old('identifier') }}">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400" id="identifier-icon"></i>
                        </div>
                    </div>
                    @error('identifier')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tombol Submit -->
                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-paper-plane text-blue-500 group-hover:text-blue-400"></i>
                        </span>
                        Kirim Kode Reset
                    </button>
                </div>

                <!-- Link Kembali -->
                <div class="text-center">
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                        Kembali ke Login
                    </a>
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

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
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
        const emailRadio = document.querySelector('input[value="email"]');
        const phoneRadio = document.querySelector('input[value="phone"]');
        const identifierInput = document.getElementById('identifier');
        const identifierIcon = document.getElementById('identifier-icon');
        
        function updatePlaceholder() {
            if (emailRadio.checked) {
                identifierInput.placeholder = 'Masukkan alamat email';
                identifierInput.type = 'email';
                identifierIcon.className = 'fas fa-envelope text-gray-400';
            } else {
                identifierInput.placeholder = 'Masukkan nomor WhatsApp (contoh: 08123456789)';
                identifierInput.type = 'tel';
                identifierIcon.className = 'fab fa-whatsapp text-gray-400';
            }
        }
        
        emailRadio.addEventListener('change', updatePlaceholder);
        phoneRadio.addEventListener('change', updatePlaceholder);
        
        // Set initial state
        updatePlaceholder();
        
        // Auto focus
        identifierInput.focus();
        
        // Form validation with better UX
        const form = document.querySelector('form');
        const submitButton = form.querySelector('button[type="submit"]');
        
        form.addEventListener('submit', function(e) {
            const method = document.querySelector('input[name="reset_method"]:checked').value;
            const identifier = identifierInput.value.trim();
            
            // Disable submit button to prevent double submission
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
            
            if (!identifier) {
                e.preventDefault();
                showError('Silakan masukkan email atau nomor WhatsApp');
                resetSubmitButton();
                return;
            }
            
            if (method === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(identifier)) {
                    e.preventDefault();
                    showError('Format email tidak valid');
                    resetSubmitButton();
                    return;
                }
            } else {
                const phoneRegex = /^[0-9]{10,13}$/;
                const cleanPhone = identifier.replace(/[^0-9]/g, '');
                if (!phoneRegex.test(cleanPhone)) {
                    e.preventDefault();
                    showError('Format nomor WhatsApp tidak valid. Gunakan 10-13 digit angka');
                    resetSubmitButton();
                    return;
                }
            }
        });
        
        function showError(message) {
            // Remove existing error alerts
            const existingAlerts = document.querySelectorAll('.alert-error');
            existingAlerts.forEach(alert => alert.remove());
            
            // Create new error alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert-error bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md mt-4';
            alertDiv.innerHTML = `
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">${message}</p>
                    </div>
                </div>
            `;
            
            form.appendChild(alertDiv);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
        
        function resetSubmitButton() {
            submitButton.disabled = false;
            submitButton.innerHTML = '<span class="absolute left-0 inset-y-0 flex items-center pl-3"><i class="fas fa-paper-plane text-blue-500 group-hover:text-blue-400"></i></span>Kirim Kode Reset';
        }
    });
    </script>
</body>

</html>