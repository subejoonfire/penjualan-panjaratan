@extends('layouts.app')

@section('title', 'Dashboard Pembeli - Penjualan Panjaratan')

@section('content')
<div class="py-3 sm:py-6">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Dasbor</h1>
            <p class="mt-1 sm:mt-2 text-sm sm:text-base text-gray-600">Selamat datang kembali, {{ auth()->user()->nickname ??
                auth()->user()->username }}!</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
            <!-- Total Orders -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-3 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-shopping-bag text-white text-xs sm:text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3 sm:ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Pesanan</dt>
                                <dd class="text-base sm:text-lg font-medium text-gray-900">{{ number_format($totalOrders) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-3 sm:px-5 py-2 sm:py-3">
                    <div class="text-xs sm:text-sm">
                        <a href="{{ route('customer.orders.index') }}"
                            class="font-medium text-blue-600 hover:text-blue-500">
                            Lihat pesanan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Total Spent -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-3 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-money-bill-wave text-white text-xs sm:text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3 sm:ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Pengeluaran</dt>
                                <dd class="text-sm sm:text-lg font-medium text-gray-900">Rp {{ number_format($totalSpent) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-3 sm:px-5 py-2 sm:py-3">
                    <div class="text-xs sm:text-sm">
                        <a href="{{ route('customer.orders.index') }}"
                            class="font-medium text-green-600 hover:text-green-500">
                            Lihat riwayat
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-3 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-clock text-white text-xs sm:text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3 sm:ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Pesanan Menunggu</dt>
                                <dd class="text-base sm:text-lg font-medium text-gray-900">{{ number_format($pendingOrders) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-3 sm:px-5 py-2 sm:py-3">
                    <div class="text-xs sm:text-sm">
                        <a href="{{ route('customer.orders.index') }}"
                            class="font-medium text-yellow-600 hover:text-yellow-500">
                            Lacak pesanan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cart Items -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-3 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-white text-xs sm:text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3 sm:ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Item di Keranjang</dt>
                                <dd class="text-base sm:text-lg font-medium text-gray-900">{{ number_format($cartItemsCount) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-3 sm:px-5 py-2 sm:py-3">
                    <div class="text-xs sm:text-sm">
                        <a href="{{ route('customer.cart.index') }}"
                            class="font-medium text-purple-600 hover:text-purple-500">
                            Lihat keranjang
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg mb-6 sm:mb-8">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                <h3 class="text-base sm:text-lg font-medium text-gray-900">Aksi Cepat</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                    <a href="{{ route('products.index') }}"
                        class="flex items-center p-3 sm:p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <div class="flex-shrink-0">
                            <i class="fas fa-search text-blue-600 text-lg sm:text-xl"></i>
                        </div>
                        <div class="ml-3 sm:ml-4">
                            <p class="text-sm font-medium text-blue-600">Jelajahi Produk</p>
                            <p class="text-xs sm:text-sm text-gray-500">Temukan item baru</p>
                        </div>
                    </a>

                    <a href="{{ route('customer.orders.index') }}"
                        class="flex items-center p-3 sm:p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <div class="flex-shrink-0">
                            <i class="fas fa-list text-green-600 text-lg sm:text-xl"></i>
                        </div>
                        <div class="ml-3 sm:ml-4">
                            <p class="text-sm font-medium text-green-600">Pesanan Saya</p>
                            <p class="text-xs sm:text-sm text-gray-500">Lacak pembelian Anda</p>
                        </div>
                    </a>

                    <a href="{{ route('customer.notifications.index') }}"
                        class="flex items-center p-3 sm:p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <div class="flex-shrink-0">
                            <i class="fas fa-bell text-purple-600 text-lg sm:text-xl"></i>
                        </div>
                        <div class="ml-3 sm:ml-4">
                            <p class="text-sm font-medium text-purple-600">Notifikasi</p>
                            <p class="text-xs sm:text-sm text-gray-500">{{ $unreadNotifications }} belum dibaca</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white shadow rounded-lg mb-6 sm:mb-8">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900">Pesanan Terbaru</h3>
                    <a href="{{ route('customer.orders.index') }}"
                        class="text-xs sm:text-sm font-medium text-blue-600 hover:text-blue-500">
                        Lihat semua
                    </a>
                </div>
            </div>
            <div class="overflow-hidden">
                @if($recentOrders->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($recentOrders as $order)
                    <li class="px-4 sm:px-6 py-3 sm:py-4 hover:bg-gray-50">
                        <!-- Mobile Layout -->
                        <div class="block sm:hidden">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-box text-gray-600 text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="ml-2">
                                        <p class="text-sm font-medium text-gray-900">{{ $order->order_number }}</p>
                                        <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('customer.orders.show', $order) }}"
                                    class="text-blue-600 hover:text-blue-500">
                                    <i class="fas fa-chevron-right text-sm"></i>
                                </a>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-500">{{ $order->cart->cartDetails->count() }} item • Rp
                                        {{ number_format($order->grandtotal) }}</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                            @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                            @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                            @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Desktop Layout -->
                        <div class="hidden sm:flex sm:items-center sm:justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-box text-gray-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $order->order_number }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->cart->cartDetails->count() }} item • Rp
                                        {{ number_format($order->grandtotal) }}</p>
                                    <p class="text-xs text-gray-400">{{ $order->created_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                            @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                            @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                            @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                                <a href="{{ route('customer.orders.show', $order) }}"
                                    class="text-blue-600 hover:text-blue-500">
                                    <i class="fas fa-chevron-right text-sm"></i>
                                </a>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="px-4 sm:px-6 py-6 sm:py-8 text-center">
                    <i class="fas fa-shopping-bag text-gray-400 text-3xl sm:text-4xl mb-4"></i>
                    <p class="text-sm sm:text-base text-gray-500">Anda belum melakukan pesanan apapun</p>
                    <a href="{{ route('products.index') }}"
                        class="mt-2 inline-flex items-center px-3 sm:px-4 py-2 border border-transparent text-xs sm:text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Mulai Berbelanja
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Favorite Products -->
        @if($favoriteProducts->count() > 0)
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                <h3 class="text-base sm:text-lg font-medium text-gray-900">Produk yang Pernah Anda Ulas</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div id="favorite-products-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3 sm:gap-6">
                    <!-- Favorite products will be loaded here via JavaScript -->
                </div>

                <!-- Loading Spinner -->
                <div id="loading-spinner" class="flex justify-center items-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>

                <!-- No Products Message -->
                <div id="no-products" class="hidden bg-white shadow rounded-lg">
                    <div class="px-6 py-12 text-center">
                        <i class="fas fa-box text-gray-400 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Produk Favorit</h3>
                        <p class="text-gray-600 mb-6">Belum ada produk favorit yang tersedia saat ini.</p>
                        <a href="{{ route('products.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-search mr-2"></i>
                            Lihat Semua Produk
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
// Load favorite products function
function loadFavoriteProducts() {
    const container = document.getElementById('favorite-products-container');
    const loadingSpinner = document.getElementById('loading-spinner');
    const noProducts = document.getElementById('no-products');
    
    // Show loading spinner
    loadingSpinner.classList.remove('hidden');
    noProducts.classList.add('hidden');
    
    fetch(`${window.location.origin}/api/products/recommended`)
        .then(response => response.json())
        .then(data => {
            loadingSpinner.classList.add('hidden');
            
            if (data.products.length === 0) {
                noProducts.classList.remove('hidden');
                return;
            }
            
            // Render products
            renderFavoriteProducts(data.products);
        })
        .catch(error => {
            console.error('Error loading favorite products:', error);
            loadingSpinner.classList.add('hidden');
            noProducts.classList.remove('hidden');
        });
}

// Render favorite products function
function renderFavoriteProducts(products) {
    const container = document.getElementById('favorite-products-container');
    
    products.forEach(product => {
        const productCard = createFavoriteProductCard(product);
        container.appendChild(productCard);
    });
}

// Create favorite product card function
function createFavoriteProductCard(product) {
    const card = document.createElement('div');
    card.className = 'bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow flex flex-col h-full';
    
    // Create stock status badge
    let stockBadge = '';
    if (product.stock <= 0) {
        stockBadge = '<div class="absolute top-1 left-1 bg-red-600 text-white text-xs px-1.5 py-0.5 rounded">Habis</div>';
    } else if (product.stock <= 10) {
        stockBadge = '<div class="absolute top-1 left-1 bg-yellow-600 text-white text-xs px-1.5 py-0.5 rounded">Stok Terbatas</div>';
    }
    
    // Create rating stars
    let ratingStars = '';
    for (let i = 1; i <= 5; i++) {
        const starClass = i <= product.avg_rating ? 'text-yellow-400' : 'text-gray-300';
        ratingStars += `<i class="fas fa-star text-xs ${starClass}"></i>`;
    }
    
    // Create cart button
    let cartButton = '';
    const isCustomer = {{ auth()->check() && auth()->user()->isCustomer() ? 'true' : 'false' }};
    const loginUrl = '{{ route('login') }}';
    
    if (isCustomer) {
        if (product.stock > 0) {
            cartButton = `<button type="button" onclick="addToCart(${product.id}, event)" class="flex-1 bg-blue-600 text-white px-1.5 sm:px-2 py-1 sm:py-1.5 rounded text-xs font-medium hover:bg-blue-700 flex items-center justify-center"><i class="fas fa-shopping-cart text-xs"></i></button>`;
        } else {
            cartButton = `<button disabled class="flex-1 bg-gray-400 text-white px-1.5 sm:px-2 py-1 sm:py-1.5 rounded cursor-not-allowed text-xs flex items-center justify-center"><i class="fas fa-shopping-cart text-xs"></i></button>`;
        }
    } else {
        cartButton = `<a href="${loginUrl}" class="flex-1 bg-blue-600 text-white px-1.5 sm:px-2 py-1 sm:py-1.5 rounded text-xs font-medium hover:bg-blue-700 flex items-center justify-center"><i class="fas fa-shopping-cart text-xs"></i></a>`;
    }
    
    card.innerHTML = `
        <div class="relative aspect-w-1 aspect-h-1 bg-gray-200">
            <a href="${product.url}">
                ${product.image ? `<img src="${product.image}" alt="${product.name}" class="w-full h-32 sm:h-48 object-cover">` : `<div class="w-full h-32 sm:h-48 flex items-center justify-center"><i class="fas fa-image text-gray-400 text-lg sm:text-2xl"></i></div>`}
            </a>
            ${stockBadge}
        </div>
        <div class="flex flex-col flex-1 justify-between p-2 sm:p-3">
            <div>
                <h4 class="text-xs sm:text-sm font-semibold text-gray-900 mb-1 line-clamp-2 min-h-[2rem] sm:min-h-[2.5rem]">${product.name}</h4>
                <p class="text-xs text-gray-600 mb-1">${product.category}</p>
                <p class="text-xs text-gray-500 mb-2 line-clamp-2 min-h-[1.5rem] sm:min-h-[2rem]">${product.description}</p>
                <div class="mb-2">
                    <span class="text-xs sm:text-sm font-bold text-blue-600">Rp ${product.price_formatted}</span>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center">
                        <div class="flex items-center">
                            ${ratingStars}
                        </div>
                        <span class="ml-1 text-xs text-gray-500">(${product.reviews_count})</span>
                    </div>
                    <div class="text-xs text-gray-500">Terjual ${Math.floor(Math.random() * 100)}</div>
                </div>
            </div>
            <div class="flex flex-col gap-1 sm:gap-2 mt-2">
                <div class="flex gap-1 sm:gap-2 w-full">
                    <button type="button" onclick="window.location.href='${product.url}'" class="flex-1 bg-gray-100 text-gray-700 px-1.5 sm:px-2 py-1 sm:py-1.5 rounded text-xs font-medium hover:bg-gray-200 flex items-center justify-center">Detail</button>
                    ${cartButton}
                </div>
            </div>
        </div>
    `;
    
    return card;
}

// Add to cart function
function addToCart(productId, event) {
    if (event) event.preventDefault();
    // Find the button that was clicked
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    
    // Disable button and show loading animation
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('quantity', 1);

    fetch(`${window.location.origin}/customer/cart/add/${productId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Cart response:', data);
        if (data.success) {
            // Show success animation
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            button.classList.add('bg-green-600');
            
            // Update cart count
            if (typeof loadCartCount === 'function') {
                setTimeout(loadCartCount, 500);
            }
            
            // Reset button after 2 seconds
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-600');
                button.classList.add('bg-blue-600', 'hover:bg-blue-700');
                button.disabled = false;
            }, 2000);
        } else {
            showModalNotification(data.message || 'Gagal menambahkan ke keranjang', 'error');
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Cart error:', error);
        showModalNotification('Terjadi kesalahan saat menambahkan ke keranjang', 'error');
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadFavoriteProducts();
});
</script>
@endsection