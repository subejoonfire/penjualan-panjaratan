@extends('layouts.app')

@section('title', 'Produk yang Disukai - Produk Favorit')

@section('content')
<div class="py-3 sm:py-6">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Produk yang Disukai</h1>
            <p class="mt-1 sm:mt-2 text-sm sm:text-base text-gray-600">Produk-produk yang Anda simpan sebagai favorit</p>
        </div>

        <!-- Wishlist Items -->
        <div id="wishlist-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3 sm:gap-6">
            <!-- Wishlist products will be loaded here via JavaScript -->
        </div>

        <!-- Loading Spinner -->
        <div id="loading-spinner" class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>

        <!-- Empty Wishlist -->
        <div id="empty-wishlist" class="hidden text-center py-12">
            <div class="max-w-md mx-auto">
                <i class="fas fa-heart text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Wishlist Kosong</h3>
                <p class="text-gray-600 mb-6">
                    Anda belum menambahkan produk apapun ke wishlist.
                    Jelajahi produk-produk menarik dan simpan yang Anda sukai!
                </p>
                <a href="{{ route('products.index') }}"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-shopping-bag mr-2"></i>
                    Jelajahi Produk
                </a>
            </div>
        </div>
</div>
</div>

<script>
    // Load cart count function (for customer only)
    @auth
    @if(auth()->user()->isCustomer())
    function loadCartCount() {
        fetch('{{ route('api.cart.count') }}')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Cart count response not ok');
                }
                return response.json();
            })
            .then(data => {
                const cartCounts = document.querySelectorAll('.cart-count');
                cartCounts.forEach(cartCount => {
                    cartCount.textContent = data.count || 0;
                    cartCount.style.display = (data.count && data.count > 0) ? 'inline-flex' : 'none';
                });
            })
            .catch(error => {
                console.error('Error loading cart count:', error);
            });
    }
    @endif
    @endauth

    function removeFromWishlist(productId) {
    confirmAction('Hapus produk dari wishlist?', function() {
        fetch(`/customer/wishlist/remove/${productId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
            if (data.success) {
                location.reload();
            } else {
                showAlert(data.message || 'Gagal menghapus dari wishlist', 'error');
            }
        })
        .catch(error => {
            console.error('Wishlist error:', error);
            showAlert('Terjadi kesalahan saat menghapus dari wishlist', 'error');
        });
    });
}

    // Add to cart function
    function addToCart(productId) {
        // Find the button that was clicked
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        
        // Disable button and show loading animation
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('quantity', 1);

        fetch(`/customer/cart/add/${productId}`, {
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
                showAlert(data.message || 'Gagal menambahkan ke keranjang', 'error');
                button.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Cart error:', error);
            showAlert('Terjadi kesalahan saat menambahkan ke keranjang', 'error');
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }

    // Load wishlist function
    function loadWishlist() {
        const container = document.getElementById('wishlist-container');
        const loadingSpinner = document.getElementById('loading-spinner');
        const emptyWishlist = document.getElementById('empty-wishlist');
        
        // Show loading spinner
        loadingSpinner.classList.remove('hidden');
        emptyWishlist.classList.add('hidden');
        
        fetch('{{ route('api.wishlist.list') }}')
            .then(response => response.json())
            .then(data => {
                loadingSpinner.classList.add('hidden');
                
                if (data.wishlists.length === 0) {
                    emptyWishlist.classList.remove('hidden');
                    return;
                }
                
                // Render wishlist products
                renderWishlistProducts(data.wishlists);
            })
            .catch(error => {
                console.error('Error loading wishlist:', error);
                loadingSpinner.classList.add('hidden');
                emptyWishlist.classList.remove('hidden');
            });
    }

    // Render wishlist products function
    function renderWishlistProducts(wishlists) {
        const container = document.getElementById('wishlist-container');
        
        wishlists.forEach(wishlist => {
            const productCard = createWishlistProductCard(wishlist);
            container.appendChild(productCard);
        });
    }

    // Create wishlist product card function
    function createWishlistProductCard(wishlist) {
        const card = document.createElement('div');
        card.className = 'bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow flex flex-col h-full';
        
        // Create stock status badge
        let stockBadge = '';
        if (wishlist.stock <= 0) {
            stockBadge = '<div class="absolute top-1 left-1 bg-red-600 text-white text-xs px-1.5 py-0.5 rounded">Habis</div>';
        } else if (wishlist.stock <= 10) {
            stockBadge = '<div class="absolute top-1 left-1 bg-yellow-600 text-white text-xs px-1.5 py-0.5 rounded">Stok Terbatas</div>';
        }
        
        // Create rating stars
        let ratingStars = '';
        for (let i = 1; i <= 5; i++) {
            const starClass = i <= wishlist.avg_rating ? 'text-yellow-400' : 'text-gray-300';
            ratingStars += `<i class="fas fa-star text-xs ${starClass}"></i>`;
        }
        
        // Create cart button
        let cartButton = '';
        const isCustomer = {{ auth()->check() && auth()->user()->isCustomer() ? 'true' : 'false' }};
        const loginUrl = '{{ route('login') }}';
        
        if (isCustomer) {
            if (wishlist.stock > 0) {
                cartButton = `<button type="button" onclick="addToCart(${wishlist.product_id})" class="flex-1 bg-blue-600 text-white px-1.5 sm:px-2 py-1 sm:py-1.5 rounded text-xs font-medium hover:bg-blue-700 flex items-center justify-center"><i class="fas fa-shopping-cart text-xs"></i></button>`;
            } else {
                cartButton = `<button disabled class="flex-1 bg-gray-400 text-white px-1.5 sm:px-2 py-1 sm:py-1.5 rounded cursor-not-allowed text-xs flex items-center justify-center"><i class="fas fa-shopping-cart text-xs"></i></button>`;
            }
        } else {
            cartButton = `<a href="${loginUrl}" class="flex-1 bg-blue-600 text-white px-1.5 sm:px-2 py-1 sm:py-1.5 rounded text-xs font-medium hover:bg-blue-700 flex items-center justify-center"><i class="fas fa-shopping-cart text-xs"></i></a>`;
        }
        
        card.innerHTML = `
            <div class="relative aspect-w-1 aspect-h-1 bg-gray-200">
                <a href="${wishlist.url}">
                    ${wishlist.image ? `<img src="${wishlist.image}" alt="${wishlist.name}" class="w-full h-28 sm:h-36 object-cover">` : `<div class="w-full h-28 sm:h-36 flex items-center justify-center"><i class="fas fa-image text-gray-400 text-lg sm:text-2xl"></i></div>`}
                </a>
                <button onclick="removeFromWishlist(${wishlist.product_id})" class="absolute top-1 right-1 p-1.5 sm:p-2 bg-red-600 text-white rounded-full hover:bg-red-700 transition-colors">
                    <i class="fas fa-heart text-xs sm:text-sm"></i>
                </button>
                ${stockBadge}
            </div>
            <div class="flex flex-col flex-1 justify-between p-2 sm:p-3">
                <div>
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-900 mb-1 line-clamp-2 min-h-[2rem] sm:min-h-[2.5rem]">
                        <a href="${wishlist.url}" class="hover:text-blue-600">${wishlist.name}</a>
                    </h3>
                    <p class="text-xs text-gray-600 mb-1">${wishlist.category}</p>
                    <p class="text-xs text-gray-500 mb-2 line-clamp-2 min-h-[1.5rem] sm:min-h-[2rem]">${wishlist.description}</p>
                    <div class="mb-2">
                        <span class="text-xs sm:text-sm font-bold text-blue-600">Rp ${wishlist.price_formatted}</span>
                    </div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <div class="flex items-center">
                                ${ratingStars}
                            </div>
                            <span class="ml-1 text-xs text-gray-500">(${wishlist.reviews_count})</span>
                        </div>
                        <div class="text-xs text-gray-500">Terjual ${Math.floor(Math.random() * 100)}</div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1 sm:mt-2">Ditambahkan ${wishlist.created_at}</p>
                </div>
                <div class="flex flex-col gap-1 sm:gap-2 mt-2">
                    <div class="flex gap-1 sm:gap-2 w-full">
                        <button type="button" onclick="window.location.href='${wishlist.url}'" class="flex-1 bg-gray-100 text-gray-700 px-1.5 sm:px-2 py-1 sm:py-1.5 rounded text-xs font-medium hover:bg-gray-200 flex items-center justify-center">Detail</button>
                        ${cartButton}
                    </div>
                </div>
            </div>
        `;
        
        return card;
    }

    // Remove from wishlist function
    function removeFromWishlist(productId) {
        const card = event.target.closest('.bg-white');
        const originalOpacity = card.style.opacity;
        
        // Fade out animation
        card.style.transition = 'opacity 0.3s ease';
        card.style.opacity = '0.5';

        fetch(`/customer/wishlist/remove/${productId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove card with animation
                card.style.opacity = '0';
                setTimeout(() => {
                    card.remove();
                    
                    // Check if no more products
                    const remainingCards = document.querySelectorAll('#wishlist-container .bg-white');
                    if (remainingCards.length === 0) {
                        document.getElementById('empty-wishlist').classList.remove('hidden');
                    }
                }, 300);
                
                showAlert('Produk berhasil dihapus dari wishlist', 'success');
            } else {
                card.style.opacity = originalOpacity;
                showAlert(data.message || 'Gagal menghapus dari wishlist', 'error');
            }
        })
        .catch(error => {
            console.error('Wishlist error:', error);
            card.style.opacity = originalOpacity;
            showAlert('Terjadi kesalahan saat menghapus dari wishlist', 'error');
        });
    }

    // Load cart count function
    function loadCartCount() {
        console.log('Loading cart count...');
        fetch('{{ route('api.cart.count') }}')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Cart count response not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Cart count data:', data);
                const cartCounts = document.querySelectorAll('.cart-count');
                console.log('Found cart count elements:', cartCounts.length);
                cartCounts.forEach((cartCount, index) => {
                    console.log(`Updating cart count ${index}:`, data.count);
                    cartCount.textContent = data.count || 0;
                    cartCount.style.display = (data.count && data.count > 0) ? 'inline-flex' : 'none';
                });
            })
            .catch(error => {
                console.error('Error loading cart count:', error);
            });
    }

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        loadWishlist();
    });
</script>
@endsection

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }


</style>