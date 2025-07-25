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
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                                    <i class="fas fa-users mr-2"></i>Users
                                </a>
                                <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                                    <i class="fas fa-box mr-2"></i>Products
                                </a>
                                <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                                    <i class="fas fa-shopping-cart mr-2"></i>Orders
                                </a>
                            @elseif(auth()->user()->isSeller())
                                <a href="{{ route('seller.dashboard') }}" class="nav-link {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}">
                                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                </a>
                                <a href="{{ route('seller.products.index') }}" class="nav-link {{ request()->routeIs('seller.products.*') ? 'active' : '' }}">
                                    <i class="fas fa-box mr-2"></i>My Products
                                </a>
                                <a href="{{ route('seller.orders.index') }}" class="nav-link {{ request()->routeIs('seller.orders.*') ? 'active' : '' }}">
                                    <i class="fas fa-shopping-cart mr-2"></i>Orders
                                </a>
                                <a href="{{ route('seller.sales') }}" class="nav-link {{ request()->routeIs('seller.sales') ? 'active' : '' }}">
                                    <i class="fas fa-chart-line mr-2"></i>Sales
                                </a>
                            @elseif(auth()->user()->isCustomer())
                                <a href="{{ route('customer.dashboard') }}" class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                </a>
                                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                                    <i class="fas fa-shopping-bag mr-2"></i>Shop
                                </a>
                                <a href="{{ route('customer.cart.index') }}" class="nav-link {{ request()->routeIs('customer.cart.*') ? 'active' : '' }}">
                                    <i class="fas fa-shopping-cart mr-2"></i>Cart
                                    <span class="cart-count bg-red-500 text-white text-xs rounded-full px-2 py-1 ml-1">0</span>
                                </a>
                                <a href="{{ route('customer.orders.index') }}" class="nav-link {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}">
                                    <i class="fas fa-list-alt mr-2"></i>My Orders
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
                                        <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
                                    </div>
                                    <div class="max-h-64 overflow-y-auto">
                                        <!-- Notifications will be loaded here -->
                                        <div class="px-4 py-3 text-sm text-gray-500 text-center">
                                            No new notifications
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- User Profile Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-medium">
                                        {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                                    </div>
                                    <span class="ml-2 text-gray-700 font-medium">{{ auth()->user()->nickname ?? auth()->user()->username }}</span>
                                    <i class="fas fa-chevron-down ml-2 text-xs text-gray-500"></i>
                                </button>
                                
                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i>Profile
                                    </a>
                                    <div class="border-t border-gray-100"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile menu button -->
                        <div class="md:hidden flex items-center">
                            <button type="button" class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                                <span class="sr-only">Open main menu</span>
                                <i class="fas fa-bars"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu -->
                <div class="mobile-menu md:hidden hidden">
                    <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-gray-50">
                        <!-- Mobile navigation links will be here -->
                    </div>
                </div>
            </nav>
        @endauth

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </span>
            </div>
        @endif

        <!-- Main Content -->
        <main class="flex-1">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Penjualan Panjaratan</h3>
                        <p class="text-gray-300">Platform e-commerce terpercaya untuk kebutuhan belanja online Anda.</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('products.index') }}" class="text-gray-300 hover:text-white">Products</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">About Us</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">Contact</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Contact Info</h3>
                        <p class="text-gray-300">Email: info@panjaratan.com</p>
                        <p class="text-gray-300">Phone: +62 123 456 789</p>
                    </div>
                </div>
                <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                    <p class="text-gray-300">&copy; {{ date('Y') }} Penjualan Panjaratan. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')

    <!-- Custom Styles -->
    <style>
        .nav-link {
            @apply text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200;
        }
        .nav-link.active {
            @apply text-blue-600 bg-blue-50;
        }
    </style>

    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.querySelector('.mobile-menu-button');
            const mobileMenu = document.querySelector('.mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            // Load cart count for customers
            @auth
                @if(auth()->user()->isCustomer())
                    loadCartCount();
                    loadNotificationCount();
                @endif
            @endauth
        });

        // Load cart count
        function loadCartCount() {
            fetch('{{ route('api.cart.count') }}')
                .then(response => response.json())
                .then(data => {
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.count;
                        cartCount.style.display = data.count > 0 ? 'flex' : 'none';
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

        // CSRF token setup for AJAX requests
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };

        // Setup AJAX headers
        fetch.defaults = {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        };
    </script>
</body>
</html>