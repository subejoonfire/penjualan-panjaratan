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

    <!-- Alpine.js cloak CSS -->
    <style>
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        @auth
        <!-- Mobile Nav Bar -->
        <nav class="mobile-nav-bar" style="display:none">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                </a>
                <a href="{{ route('admin.users.index') }}" class="mobile-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                </a>
                <a href="{{ route('admin.products.index') }}" class="mobile-nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="mobile-nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="fas fa-list"></i>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="mobile-nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                </a>
                <a href="{{ route('admin.transactions.index') }}" class="mobile-nav-item {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                    <i class="fas fa-credit-card"></i>
                </a>
            @elseif(auth()->user()->isSeller())
                <a href="{{ route('seller.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                </a>
                <a href="{{ route('seller.products.index') }}" class="mobile-nav-item {{ request()->routeIs('seller.products.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                </a>
                <a href="{{ route('seller.orders.index') }}" class="mobile-nav-item {{ request()->routeIs('seller.orders.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                </a>
                <a href="{{ route('seller.sales') }}" class="mobile-nav-item {{ request()->routeIs('seller.sales') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                </a>
                <a href="{{ route('seller.transactions.index') }}" class="mobile-nav-item {{ request()->routeIs('seller.transactions.*') ? 'active' : '' }}">
                    <i class="fas fa-credit-card"></i>
                </a>
            @elseif(auth()->user()->isCustomer())
                <a href="{{ route('customer.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                </a>
                <a href="{{ route('products.index') }}" class="mobile-nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag"></i>
                </a>
                <a href="{{ route('customer.wishlist.index') }}" class="mobile-nav-item {{ request()->routeIs('customer.wishlist.*') ? 'active' : '' }}">
                    <i class="fas fa-heart"></i>
                </a>
                <a href="{{ route('customer.orders.index') }}" class="mobile-nav-item {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}">
                    <i class="fas fa-list-alt"></i>
                </a>
            @endif
            <!-- Notifikasi & Cart -->
            <a href="{{ auth()->user()->isAdmin() ? route('admin.notifications.index') : (auth()->user()->isSeller() ? route('seller.notifications.index') : route('customer.notifications.index')) }}" class="mobile-nav-item relative {{ request()->routeIs(auth()->user()->role.'.notifications.*') ? 'active' : '' }}">
                <i class="fas fa-bell"></i>
                <span class="notification-count absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1 min-w-[1rem] h-4 flex items-center justify-center" style="display: none;">0</span>
            </a>
            @if(auth()->user()->isCustomer())
            <a href="{{ route('customer.cart.index') }}" class="mobile-nav-item relative {{ request()->routeIs('customer.cart.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1 min-w-[1rem] h-4 flex items-center justify-center" style="display: none;">0</span>
            </a>
            @endif
            <!-- Profile Button -->
            <button onclick="openMobileProfileModal()" class="mobile-nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
            </button>
        </nav>

        <!-- Mobile Profile Modal -->
        <div id="mobileProfileModal" class="mobile-modal hidden">
            <div class="mobile-modal-overlay" onclick="closeMobileProfileModal()"></div>
            <div class="mobile-modal-content">
                <div class="mobile-modal-header">
                    <h3 class="mobile-modal-title">Menu Profile</h3>
                    <button onclick="closeMobileProfileModal()" class="mobile-modal-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="mobile-modal-body">
                    <a href="{{ route('profile') }}" class="mobile-modal-item">
                        <i class="fas fa-user"></i>
                        <span>Profil</span>
                    </a>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.notifications.index') }}" class="mobile-modal-item">
                        <i class="fas fa-bell"></i>
                        <span>Notifikasi</span>
                    </a>
                    @elseif(auth()->user()->isSeller())
                    <a href="{{ route('seller.notifications.index') }}" class="mobile-modal-item">
                        <i class="fas fa-bell"></i>
                        <span>Notifikasi</span>
                    </a>
                    @else
                    <a href="{{ route('customer.notifications.index') }}" class="mobile-modal-item">
                        <i class="fas fa-bell"></i>
                        <span>Notifikasi</span>
                    </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="mobile-modal-form">
                        @csrf
                        <button type="submit" class="mobile-modal-item">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Desktop Nav -->
        <nav class="bg-white shadow-lg border-b border-gray-200 desktop-nav">
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
                        <a href="{{ route('admin.dashboard') }}"
                            class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <a href="{{ route('admin.users.index') }}"
                            class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="fas fa-users mr-2"></i>Pengguna
                        </a>
                        <a href="{{ route('admin.products.index') }}"
                            class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                            <i class="fas fa-box mr-2"></i>Produk
                        </a>
                        <a href="{{ route('admin.categories.index') }}"
                            class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <i class="fas fa-list mr-2"></i>Categories
                        </a>
                        <a href="{{ route('admin.orders.index') }}"
                            class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart mr-2"></i>Pesanan
                        </a>
                        <a href="{{ route('admin.transactions.index') }}"
                            class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                            <i class="fas fa-credit-card mr-2"></i>Transaksi
                        </a>
                        @elseif(auth()->user()->isSeller())
                        <a href="{{ route('seller.dashboard') }}"
                            class="nav-link {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <a href="{{ route('seller.products.index') }}"
                            class="nav-link {{ request()->routeIs('seller.products.*') ? 'active' : '' }}">
                            <i class="fas fa-box mr-2"></i>Produk Saya
                        </a>
                        <a href="{{ route('seller.orders.index') }}"
                            class="nav-link {{ request()->routeIs('seller.orders.*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart mr-2"></i>Pesanan
                        </a>
                        <a href="{{ route('seller.sales') }}"
                            class="nav-link {{ request()->routeIs('seller.sales') ? 'active' : '' }}">
                            <i class="fas fa-chart-line mr-2"></i>Penjualan
                        </a>
                        <a href="{{ route('seller.transactions.index') }}"
                            class="nav-link {{ request()->routeIs('seller.transactions.*') ? 'active' : '' }}">
                            <i class="fas fa-credit-card mr-2"></i>Transaksi
                        </a>
                        @elseif(auth()->user()->isCustomer())
                        <a href="{{ route('customer.dashboard') }}"
                            class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <a href="{{ route('products.index') }}"
                            class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-bag mr-2"></i>Belanja
                        </a>
                        <a href="{{ route('customer.wishlist.index') }}"
                            class="nav-link {{ request()->routeIs('customer.wishlist.*') ? 'active' : '' }}">
                            <i class="fas fa-heart mr-2"></i>Wishlist
                        </a>
                        <a href="{{ route('customer.orders.index') }}"
                            class="nav-link {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}">
                            <i class="fas fa-list-alt mr-2"></i>Pesanan
                        </a>
                        @endif
                    </div>

                    <!-- User Menu -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
                                <i class="fas fa-bell text-lg"></i>
                                <span
                                    class="notification-count absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1 min-w-[1.25rem] h-5 flex items-center justify-center" style="display: none;">0</span>
                            </button>

                            <!-- Notification Dropdown -->
                            <div x-show="open" @click.away="open = false" x-transition
                                x-cloak
                                class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50">
                                <div class="px-4 py-2 border-b">
                                    <h3 class="text-sm font-medium text-gray-900">Notifikasi</h3>
                                </div>
                                <div id="notificationList" class="max-h-64 overflow-y-auto">
                                    <!-- Notifications will be loaded here -->
                                    <div class="px-4 py-3 text-sm text-gray-500 text-center">
                                        Memuat notifikasi...
                                    </div>
                                </div>
                                <div class="px-4 py-2 border-t border-gray-200">
                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('admin.notifications.index') }}"
                                            class="block text-sm text-blue-600 hover:text-blue-500 text-center">
                                            Lihat Semua Notifikasi
                                        </a>
                                    @elseif(auth()->user()->isSeller())
                                        <a href="{{ route('seller.notifications.index') }}"
                                            class="block text-sm text-blue-600 hover:text-blue-500 text-center">
                                            Lihat Semua Notifikasi
                                        </a>
                                    @else
                                        <a href="{{ route('customer.notifications.index') }}"
                                            class="block text-sm text-blue-600 hover:text-blue-500 text-center">
                                            Lihat Semua Notifikasi
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Cart (Customer Only) -->
                        @if(auth()->user()->isCustomer())
                        <div class="relative">
                            <a href="{{ route('customer.cart.index') }}"
                                class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none {{ request()->routeIs('customer.cart.*') ? 'text-blue-600' : '' }}">
                                <i class="fas fa-shopping-cart text-lg"></i>
                                <span class="cart-count absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1 min-w-[1.25rem] h-5 flex items-center justify-center" style="display: none;">0</span>
                            </a>
                        </div>
                        @endif

                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">{{ substr(auth()->user()->nickname ?? auth()->user()->username, 0, 1) }}</span>
                                </div>
                                <span class="hidden md:block text-sm font-medium">{{ auth()->user()->nickname ?? auth()->user()->username }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition
                                x-cloak
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route('profile') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Profil
                                </a>
                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.notifications.index') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-bell mr-2"></i>Notifikasi
                                </a>
                                @elseif(auth()->user()->isSeller())
                                <a href="{{ route('seller.notifications.index') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-bell mr-2"></i>Notifikasi
                                </a>
                                @else
                                <a href="{{ route('customer.notifications.index') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-bell mr-2"></i>Notifikasi
                                </a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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
                    <p>&copy; {{ date('Y') }} Harlan Muradi. All rights reserved.</p>
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

    <!-- Load Cart Count for Customers and Notifications for All Users -->
    @auth
    <script>
        // Load notification count for all users
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

            // Load notifications when dropdown is opened
            function loadNotifications() {
                fetch('{{ route('api.notifications.unread') }}')
                    .then(response => response.json())
                    .then(data => {
                        const notificationList = document.getElementById('notificationList');
                        if (notificationList) {
                            if (data.notifications && data.notifications.length > 0) {
                                notificationList.innerHTML = data.notifications.map(notification => {
                                    // Get notification icon based on type
                                    let icon = 'fas fa-bell';
                                    let iconColor = 'text-blue-500';
                                    
                                    switch(notification.type) {
                                        case 'order':
                                            icon = 'fas fa-shopping-cart';
                                            iconColor = 'text-green-500';
                                            break;
                                        case 'payment':
                                            icon = 'fas fa-credit-card';
                                            iconColor = 'text-yellow-500';
                                            break;
                                        case 'product':
                                            icon = 'fas fa-box';
                                            iconColor = 'text-purple-500';
                                            break;
                                        case 'promotion':
                                            icon = 'fas fa-percentage';
                                            iconColor = 'text-red-500';
                                            break;
                                        case 'system':
                                            icon = 'fas fa-cog';
                                            iconColor = 'text-gray-500';
                                            break;
                                        case 'review':
                                            icon = 'fas fa-star';
                                            iconColor = 'text-orange-500';
                                            break;
                                        default:
                                            icon = 'fas fa-bell';
                                            iconColor = 'text-blue-500';
                                    }
                                    
                                    // Truncate notification text
                                    const maxLength = 60;
                                    let displayText = notification.notification;
                                    let showMore = '';
                                    
                                    if (displayText.length > maxLength) {
                                        displayText = displayText.substring(0, maxLength) + '...';
                                        showMore = '<span class="text-blue-600 hover:text-blue-800 text-xs cursor-pointer ml-1">lihat</span>';
                                    }
                                    
                                    return `
                                        <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer ${notification.readstatus ? '' : 'bg-blue-50'}" onclick="viewNotificationDetail(${notification.id})">
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0">
                                                    <i class="${icon} ${iconColor} text-sm"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 ${notification.readstatus ? '' : 'font-bold'}">${notification.title}</p>
                                                    <p class="text-sm text-gray-600">${displayText}${showMore}</p>
                                                    <div class="flex items-center justify-between mt-1">
                                                        <p class="text-xs text-gray-500">${notification.created_at}</p>
                                                        ${!notification.readstatus ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Baru</span>' : ''}
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <button onclick="event.stopPropagation(); markNotificationAsRead(${notification.id})" class="text-gray-400 hover:text-gray-600">
                                                        <i class="fas fa-chevron-right text-xs"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                }).join('');
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

            // View notification detail
            function viewNotificationDetail(notificationId) {
                // Mark as read and redirect to detail page
                fetch(`/api/notifications/${notificationId}/read`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirect to appropriate notification detail page based on user role
                        const userRole = '{{ auth()->user()->role }}';
                        window.location.href = `/${userRole}/notifications/${notificationId}`;
                    }
                })
                .catch(error => console.error('Error viewing notification:', error));
            }

            // Mark notification as read
            function markNotificationAsRead(notificationId) {
                fetch(`/api/notifications/${notificationId}/read`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadNotificationCount(); // Refresh count
                        loadNotifications(); // Refresh list
                    }
                })
                .catch(error => console.error('Error marking notification as read:', error));
            }

            // Load notification count on page load
            document.addEventListener('DOMContentLoaded', function() {
                loadNotificationCount();
                
                // Add click event to notification button
                const notificationButton = document.querySelector('.notification-count').closest('button');
                if (notificationButton) {
                    notificationButton.addEventListener('click', function() {
                        setTimeout(loadNotifications, 100); // Small delay to ensure dropdown is open
                    });
                }
            });

            @if(auth()->user()->isCustomer())
            // Load cart count for customers only
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

            // Load cart count on page load for customers
            document.addEventListener('DOMContentLoaded', function() {
                loadCartCount();
            });
            @endif
    </script>
    @endauth

    <!-- Mobile Profile Modal Script -->
    <script>
        function openMobileProfileModal() {
            console.log('Opening mobile profile modal...');
            const modal = document.getElementById('mobileProfileModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Prevent background scroll
            }
        }

        function closeMobileProfileModal() {
            console.log('Closing mobile profile modal...');
            const modal = document.getElementById('mobileProfileModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = ''; // Restore scroll
            }
        }

        // Close modal when pressing Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileProfileModal();
            }
        });
    </script>

    <!-- Custom Styles -->
    <style>
        .nav-link {
            @apply text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200;
        }
        .nav-link.active {
            @apply text-blue-600 bg-blue-50;
        }
        body {
            overflow-x: hidden;
        }
        
        /* Mobile Modal */
        .mobile-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .mobile-modal.hidden {
            display: none;
        }
        .mobile-modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        .mobile-modal-content {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 300px;
            position: relative;
            z-index: 1;
        }
        .mobile-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 20px 0 20px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 15px;
            margin-bottom: 0;
        }
        .mobile-modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }
        .mobile-modal-close {
            background: none;
            border: none;
            font-size: 18px;
            color: #6b7280;
            cursor: pointer;
            padding: 5px;
            border-radius: 6px;
        }
        .mobile-modal-close:hover {
            background: #f3f4f6;
            color: #374151;
        }
        .mobile-modal-body {
            padding: 15px 0 20px 0;
        }
        .mobile-modal-item {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 15px 20px;
            color: #374151;
            text-decoration: none;
            border: none;
            background: none;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .mobile-modal-item:hover {
            background: #f9fafb;
        }
        .mobile-modal-item i {
            font-size: 18px;
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }
        .mobile-modal-form {
            margin: 0;
        }
        
        /* Mobile nav bar */
        @media (max-width: 768px) {
            .mobile-nav-bar {
                display: flex;
                flex-direction: row;
                justify-content: flex-start;
                overflow-x: auto;
                overflow-y: hidden;
                gap: 0.25rem;
                background: #fff;
                border-bottom: 1px solid #e5e7eb;
                position: sticky;
                top: 0;
                z-index: 50;
                padding: 0.5rem 0.75rem;
                box-shadow: 0 2px 8px 0 rgba(0,0,0,0.03);
                white-space: nowrap;
            }
            .mobile-nav-bar::-webkit-scrollbar {
                display: none;
            }
            .mobile-nav-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                min-width: 40px;
                min-height: 44px;
                padding: 0.25rem 0.375rem;
                border-radius: 0.5rem;
                color: #64748b;
                font-size: 1.125rem;
                background: none;
                border: none;
                max-width: 60px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .mobile-nav-item.active {
                background: #e0e7ff;
                color: #2563eb;
            }
            .mobile-nav-label {
                display: none;
            }
            .desktop-nav {
                display: none !important;
            }
            .mobile-nav-bar {
                display: flex !important;
            }
        }
        @media (min-width: 769px) {
            .mobile-nav-bar, .mobile-modal {
                display: none !important;
            }
            .desktop-nav {
                display: flex !important;
            }
        }
    </style>
</body>

</html>