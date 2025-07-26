<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Penjualan Panjaratan')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        @auth
            <nav class="bg-white shadow-lg border-b border-gray-200">
                <div class="w-full px-2 sm:px-4 lg:px-6 xl:px-8">
                    <div class="flex justify-between h-16">
                        <!-- Logo & Brand -->
                        <div class="flex items-center">
                            <a href="{{ route(auth()->user()->role . '.dashboard') }}" class="flex items-center">
                                <i class="fas fa-store text-2xl text-blue-600 mr-3"></i>
                                <span class="text-xl font-bold text-gray-800">Penjualan Panjaratan</span>
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden md:flex items-center space-x-8">
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                    <i class="fas fa-users mr-2"></i>Pengguna
                                </a>
                                <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                                    <i class="fas fa-box mr-2"></i>Produk
                                </a>
                                <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                    <i class="fas fa-list mr-2"></i>Categories
                                </a>
                                <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                                    <i class="fas fa-shopping-cart mr-2"></i>Pesanan
                                </a>
                            @elseif(auth()->user()->isSeller())
                                <a href="{{ route('seller.dashboard') }}" class="nav-link {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}">
                                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                </a>
                                <a href="{{ route('seller.products.index') }}" class="nav-link {{ request()->routeIs('seller.products.*') ? 'active' : '' }}">
                                    <i class="fas fa-box mr-2"></i>Produk Saya
                                </a>
                                <a href="{{ route('seller.orders.index') }}" class="nav-link {{ request()->routeIs('seller.orders.*') ? 'active' : '' }}">
                                    <i class="fas fa-shopping-cart mr-2"></i>Pesanan
                                </a>
                                <a href="{{ route('seller.sales') }}" class="nav-link {{ request()->routeIs('seller.sales') ? 'active' : '' }}">
                                    <i class="fas fa-chart-line mr-2"></i>Penjualan
                                </a>
                            @elseif(auth()->user()->isCustomer())
                                <a href="{{ route('customer.dashboard') }}" class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                </a>
                                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                                    <i class="fas fa-shopping-bag mr-2"></i>Belanja
                                </a>
                                <a href="{{ route('customer.cart.index') }}" class="nav-link {{ request()->routeIs('customer.cart.*') ? 'active' : '' }}">
                                    <i class="fas fa-shopping-cart mr-2"></i>Keranjang
                                    <span class="cart-count bg-red-500 text-white text-xs rounded-full px-2 py-1 ml-1">0</span>
                                </a>
                                <a href="{{ route('customer.orders.index') }}" class="nav-link {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}">
                                    <i class="fas fa-list-alt mr-2"></i>Pesanan Saya
                                </a>
                            @endif
                        </div>

                        <!-- User Menu -->
                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
                                    <i class="fas fa-bell text-lg"></i>
                                    <span class="notification-count absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1 min-w-[1.25rem] h-5 flex items-center justify-center">0</span>
                                </button>
                                
                                <!-- Notification Dropdown -->
                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50">
                                    <div class="px-4 py-2 border-b">
                                        <h3 class="text-sm font-medium text-gray-900">Notifikasi</h3>
                                    </div>
                                    <div id="notificationList" class="max-h-64 overflow-y-auto">
                                        <!-- Notifications will be loaded here -->
                                        <div class="px-4 py-3 text-sm text-gray-500 text-center">
                                            Memuat notifikasi...
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- User Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">{{ substr(auth()->user()->username, 0, 1) }}</span>
                                    </div>
                                    <span class="hidden md:block text-sm font-medium">{{ auth()->user()->username }}</span>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>
                                
                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i>Profil
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="block">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i>Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        @endauth

        <!-- Main Content -->
        <main class="flex-1">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center text-gray-500 text-sm">
                    <p>&copy; {{ date('Y') }} Penjualan Panjaratan. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Modal Notification Component -->
    @include('components.modal-notification')

    <!-- Flash Messages -->
    @if(session('success'))
        <script>
            showAlert('{{ session('success') }}', 'success');
        </script>
    @endif

    @if(session('error'))
        <script>
            showAlert('{{ session('error') }}', 'error');
        </script>
    @endif

    @if(session('warning'))
        <script>
            showAlert('{{ session('warning') }}', 'warning');
        </script>
    @endif

    @if(session('info'))
        <script>
            showAlert('{{ session('info') }}', 'info');
        </script>
    @endif

    @stack('scripts')

    <!-- Load Cart Count for Customers -->
    @auth
        @if(auth()->user()->isCustomer())
        <script>
            // Load cart count on page load
            document.addEventListener('DOMContentLoaded', function() {
                loadCartCount();
            });

            function loadCartCount() {
                fetch('{{ route('api.cart.count') }}')
                    .then(response => response.json())
                    .then(data => {
                        const cartCount = document.querySelector('.cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.count;
                            cartCount.style.display = data.count > 0 ? 'inline-flex' : 'none';
                        }
                    })
                    .catch(error => console.error('Error loading cart count:', error));
            }

            // Load notification count
            function loadNotificationCount() {
                fetch('{{ route('api.notifications.unread') }}')
                    .then(response => response.json())
                    .then(data => {
                        const notificationCount = document.querySelector('.notification-count');
                        if (notificationCount) {
                            notificationCount.textContent = data.count;
                            notificationCount.style.display = data.count > 0 ? 'flex' : 'none';
                        }
                    })
                    .catch(error => console.error('Error loading notification count:', error));
            }

            // Load notification count on page load
            loadNotificationCount();
            
            // Load notifications when dropdown is opened
            document.addEventListener('DOMContentLoaded', function() {
                const notificationButton = document.querySelector('[x-data*="open"] button');
                const notificationList = document.getElementById('notificationList');
                
                if (notificationButton && notificationList) {
                    notificationButton.addEventListener('click', function() {
                        loadNotifications();
                    });
                }
            });
            
            function loadNotifications() {
                fetch('{{ route('api.notifications.unread') }}')
                    .then(response => response.json())
                    .then(data => {
                        const notificationList = document.getElementById('notificationList');
                        if (notificationList) {
                            if (data.notifications && data.notifications.length > 0) {
                                notificationList.innerHTML = data.notifications.map(notification => `
                                    <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-bell text-blue-500 text-sm"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900">${notification.title}</p>
                                                <p class="text-sm text-gray-600">${notification.notification}</p>
                                                <p class="text-xs text-gray-500 mt-1">${notification.created_at}</p>
                                            </div>
                                        </div>
                                    </div>
                                `).join('');
                            } else {
                                notificationList.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 text-center">Tidak ada notifikasi baru</div>';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error loading notifications:', error);
                        const notificationList = document.getElementById('notificationList');
                        if (notificationList) {
                            notificationList.innerHTML = '<div class="px-4 py-3 text-sm text-red-500 text-center">Gagal memuat notifikasi</div>';
                        }
                    });
            }
        </script>
        @endif
    @endauth

    <!-- Custom Styles -->
    <style>
        .nav-link {
            @apply text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200;
        }

        .nav-link.active {
            @apply text-blue-600 bg-blue-50;
        }

        /* Prevent horizontal scrolling */
        body {
            overflow-x: hidden;
        }
    </style>
</body>
</html>