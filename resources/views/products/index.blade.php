@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('content')
<div class="py-6">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header with Search -->
        <div class="mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Produk</h1>
                    <p class="mt-2 text-gray-600">Temukan produk menarik dari penjual kami</p>
                    {{-- @guest
                    <div
                        class="mt-4 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 bg-blue-50 border border-blue-200 rounded-lg p-4 shadow-sm">
                        <span class="text-blue-700 font-semibold text-sm sm:text-base">Belum punya akun? Daftar atau
                            login untuk pengalaman belanja lebih lengkap!</span>
                        <div class="flex gap-2 mt-2 sm:mt-0">
                            <a href="{{ route('login') }}"
                                class="px-4 py-2 rounded-md bg-blue-600 text-white font-semibold hover:bg-blue-700 transition text-sm">Masuk</a>
                            <a href="{{ route('register') }}"
                                class="px-4 py-2 rounded-md bg-white border border-blue-600 text-blue-600 font-semibold hover:bg-blue-50 transition text-sm">Daftar</a>
                        </div>
                    </div>
                    @endguest --}}
                </div>

                <!-- Search Bar (Always Visible) -->
                <div class="mt-4 lg:mt-0 lg:ml-6">
                    <form method="GET" action="{{ route('products.index') }}" class="flex items-center space-x-2">
                        <div class="relative">
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                placeholder="Cari produk..."
                                class="w-full lg:w-80 pl-10 pr-4 py-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                        <!-- Hidden inputs to preserve other filters -->
                        <input type="hidden" name="category" id="category" value="{{ request('category') }}">
                        <input type="hidden" name="min_price" id="min_price_hidden" value="{{ request('min_price') }}">
                        <input type="hidden" name="max_price" id="max_price_hidden" value="{{ request('max_price') }}">
                        <input type="hidden" name="sort" id="sort" value="{{ request('sort') }}">
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Mobile Filter Toggle -->
        <div class="lg:hidden mb-4" x-data="{ mobileFilterOpen: false }">
            <button type="button" @click="mobileFilterOpen = !mobileFilterOpen"
                class="w-full bg-white shadow rounded-lg p-3 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-900">
                    <i class="fas fa-filter mr-2"></i>Filter & Kategori
                </span>
                <i class="fas fa-chevron-down transition-transform duration-200"
                    :class="mobileFilterOpen ? 'rotate-180' : ''"></i>
            </button>

            <!-- Mobile Filter Content -->
            <div x-show="mobileFilterOpen" x-transition class="mt-2 bg-white shadow rounded-lg p-4">
                <form id="mobile-filter" method="GET" action="{{ route('products.index') }}" class="space-y-4">
                    <!-- Preserve search -->
                    <input type="hidden" name="search" value="{{ request('search') }}">

                    <!-- Categories -->
                    <div>
                        <label for="mobile-category"
                            class="block text-xs font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="category" id="mobile-category"
                            class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category')==$category->id ? 'selected' : ''
                                }}>
                                {{ $category->category }} ({{ $category->products_count }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price Range -->
                    @if($priceRange && $priceRange->max_price > 0)
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Rentang Harga</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="relative">
                                <span class="absolute left-2 top-1.5 text-xs text-gray-500">Rp</span>
                                <input type="text" name="min_price" id="min_price_mobile" value="{{ request('min_price') ? number_format(request('min_price'), 0, ',', '.') : '' }}" placeholder="Min"
                                    min="0" max="{{ $priceRange->max_price }}"
                                    class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 py-1 pl-7 pr-2 h-8">
                            </div>
                            <div class="relative">
                                <span class="absolute left-2 top-1.5 text-xs text-gray-500">Rp</span>
                                <input type="text" name="max_price" id="max_price_mobile" value="{{ request('max_price') ? number_format(request('max_price'), 0, ',', '.') : '' }}" placeholder="Maks"
                                    min="0" max="{{ $priceRange->max_price }}"
                                    class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 py-1 pl-7 pr-2 h-8">
                            </div>
                        </div>
                        <div class="mt-1 text-xs text-gray-500">
                            Rp {{ number_format($priceRange->min_price) }} - Rp {{ number_format($priceRange->max_price)
                            }}
                        </div>
                    </div>
                    @endif

                    <div class="flex space-x-2">
                        <button type="submit"
                            class="flex-1 bg-blue-600 text-white py-2 px-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            Terapkan
                        </button>
                        @if(request()->hasAny(['category', 'min_price', 'max_price']))
                        <a href="{{ route('products.index', ['search' => request('search')]) }}"
                            class="flex-1 bg-gray-300 text-gray-700 py-2 px-3 rounded-md hover:bg-gray-400 text-center text-sm">
                            Reset
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:grid lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-6 lg:gap-6">
            <!-- Filters Sidebar -->
            <div class="hidden lg:block lg:col-span-1 xl:col-span-1 2xl:col-span-1">
                <div class="bg-white shadow rounded-lg sticky top-6" x-data="{ filterOpen: true }">
                    <!-- Filter Header with Toggle -->
                    <div class="p-3 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-base font-medium text-gray-900">Filter</h3>
                        <button @click="filterOpen = !filterOpen" type="button"
                            class="text-gray-400 hover:text-gray-600 p-1 rounded">
                            <i class="fas fa-chevron-down transition-transform duration-200"
                                :class="filterOpen ? 'rotate-180' : ''"></i>
                        </button>
                    </div>

                    <!-- Filter Content -->
                    <div x-show="filterOpen" x-transition class="p-4">
                        <form id="desktop-filter" method="GET" action="{{ route('products.index') }}" class="space-y-4">
                            <!-- Preserve search -->
                            <input type="hidden" name="search" value="{{ request('search') }}">

                            <!-- Categories -->
                            <div>
                                <label for="category"
                                    class="block text-xs font-medium text-gray-700 mb-1">Kategori</label>
                                <select name="category" id="category"
                                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category')==$category->id ?
                                        'selected' : '' }}>
                                        {{ $category->category }} ({{ $category->products_count }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Price Range -->
                            @if($priceRange && $priceRange->max_price > 0)
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Rentang Harga</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="relative">
                                        <span class="absolute left-2 top-1.5 text-xs text-gray-500">Rp</span>
                                        <input type="text" name="min_price" id="min_price" value="{{ request('min_price') ? number_format(request('min_price'), 0, ',', '.') : '' }}" placeholder="Min" min="0" max="{{ $priceRange->max_price }}"
                                            class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 py-1 pl-7 pr-2 h-8">
                                    </div>
                                    <div class="relative">
                                        <span class="absolute left-2 top-1.5 text-xs text-gray-500">Rp</span>
                                        <input type="text" name="max_price" id="max_price" value="{{ request('max_price') ? number_format(request('max_price'), 0, ',', '.') : '' }}" placeholder="Maks" min="0" max="{{ $priceRange->max_price }}"
                                            class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 py-1 pl-7 pr-2 h-8">
                                    </div>
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    Rp {{ number_format($priceRange->min_price) }} - Rp {{
                                    number_format($priceRange->max_price) }}
                                </div>
                            </div>
                            @endif

                            <div class="pt-3">
                                <button type="submit"
                                    class="w-full bg-blue-600 text-white py-2 px-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                    Terapkan
                                </button>
                                @if(request()->hasAny(['category', 'min_price', 'max_price']))
                                <a href="{{ route('products.index', ['search' => request('search')]) }}"
                                    class="w-full mt-2 bg-gray-300 text-gray-700 py-2 px-3 rounded-md hover:bg-gray-400 text-center block text-sm">
                                    Reset
                                </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Products Content -->
            <div class="lg:col-span-4 xl:col-span-5 2xl:col-span-5">
                <!-- Sort & View Options -->
                <div class="bg-white shadow rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-700">
                                @if(request()->hasAny(['search', 'category', 'min_price', 'max_price']))
                                {{ $products->total() }} hasil ditemukan
                                @else
                                {{ $products->total() }} produk
                                @endif
                            </span>
                        </div>

                        <div class="flex items-center space-x-4">
                            <label for="sort" class="text-sm text-gray-700">Urutkan:</label>
                            <select name="sort" id="sort" onchange="updateSort(this.value)"
                                class="text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="latest" {{ $sortBy==='latest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="popular" {{ $sortBy==='popular' ? 'selected' : '' }}>Populer</option>
                                <option value="price_low" {{ $sortBy==='price_low' ? 'selected' : '' }}>Harga ↑</option>
                                <option value="price_high" {{ $sortBy==='price_high' ? 'selected' : '' }}>Harga ↓
                                </option>
                                <option value="name" {{ $sortBy==='name' ? 'selected' : '' }}>Nama A-Z</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div id="products-container"
                    class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 2xl:grid-cols-6 gap-3 sm:gap-6">
                    <!-- Products will be loaded here via JavaScript -->
                </div>

                <!-- Loading Spinner -->
                <div id="loading-spinner" class="flex justify-center items-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>

                <!-- No Products Message -->
                <div id="no-products" class="hidden bg-white shadow rounded-lg">
                    <div class="px-6 py-12 text-center">
                        <i class="fas fa-search text-gray-400 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Produk Ditemukan</h3>
                        <p class="text-gray-600 mb-6">
                            Tidak ada produk yang sesuai dengan kriteria pencarian Anda. Coba sesuaikan filter Anda.
                        </p>
                        <button onclick="resetFilters()"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-refresh mr-2"></i>
                            Reset Semua
                        </button>
                    </div>
                </div>

                <!-- Load More Button -->
                <div id="load-more-container" class="mt-8 text-center hidden">
                    <button id="load-more-btn" onclick="loadMoreProducts()"
                        class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-2.5 rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm">
                        <i class="fas fa-chevron-down text-xs"></i>
                        <span id="load-more-text">Muat Lebih Banyak</span>
                        <div id="load-more-spinner" class="hidden">
                            <div class="animate-spin rounded-full h-3 w-3 border-b-2 border-white"></div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Global variables for product loading
    let currentPage = 1;
    let isLoading = false;
    let hasMorePages = true;

    // Load products function
    function loadProducts(page = 1, reset = false) {
        if (isLoading) return;
        
        isLoading = true;
        
        if (reset) {
            currentPage = 1;
            hasMorePages = true;
            document.getElementById('products-container').innerHTML = '';
            document.getElementById('no-products').classList.add('hidden');
            document.getElementById('load-more-container').classList.add('hidden');
        }
        
        // Show loading spinner
        document.getElementById('loading-spinner').classList.remove('hidden');
        
        // Build query parameters
        const params = new URLSearchParams();
        params.append('page', page);
        
        const searchInput = document.getElementById('search');
        if (searchInput && searchInput.value) {
            params.append('search', searchInput.value);
        }
        
        const categorySelect = document.getElementById('category');
        if (categorySelect && categorySelect.value) {
            params.append('category', categorySelect.value);
        }
        
        const minPriceInput = document.getElementById('min_price_hidden'); // Changed to hidden input
        if (minPriceInput && minPriceInput.value) {
            params.append('min_price', minPriceInput.value);
        }
        
        const maxPriceInput = document.getElementById('max_price_hidden'); // Changed to hidden input
        if (maxPriceInput && maxPriceInput.value) {
            params.append('max_price', maxPriceInput.value);
        }
        
        const sortSelect = document.getElementById('sort');
        if (sortSelect && sortSelect.value) {
            params.append('sort', sortSelect.value);
        }
        
        // Fetch products
        fetch(`{{ route('api.products.list') }}?${params.toString()}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                isLoading = false;
                document.getElementById('loading-spinner').classList.add('hidden');
                
                if (data.error) {
                    console.error('API Error:', data.error);
                    document.getElementById('no-products').classList.remove('hidden');
                    document.getElementById('no-products').querySelector('h3').textContent = 'Terjadi Kesalahan';
                    document.getElementById('no-products').querySelector('p').textContent = data.error;
                    return;
                }
                
                if (data.products.length === 0 && page === 1) {
                    document.getElementById('no-products').classList.remove('hidden');
                    return;
                }
                
                // Render products
                renderProducts(data.products, reset);
                
                // Update pagination info
                hasMorePages = data.pagination.has_more_pages;
                currentPage = data.pagination.current_page;
                
                // Show/hide load more button
                if (hasMorePages) {
                    document.getElementById('load-more-container').classList.remove('hidden');
                } else {
                    document.getElementById('load-more-container').classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error loading products:', error);
                isLoading = false;
                document.getElementById('loading-spinner').classList.add('hidden');
                
                // Show error message
                document.getElementById('no-products').classList.remove('hidden');
                document.getElementById('no-products').querySelector('h3').textContent = 'Terjadi Kesalahan';
                document.getElementById('no-products').querySelector('p').textContent = 'Terjadi kesalahan saat memuat produk. Silakan coba lagi.';
            });
    }

    // Render products function
    function renderProducts(products, reset = false) {
        const container = document.getElementById('products-container');
        
        products.forEach(product => {
            const productCard = createProductCard(product);
            container.appendChild(productCard);
        });
    }

    // Create product card function
    function createProductCard(product) {
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
                cartButton = `<button type="button" onclick="addToCart(${product.id})" class="flex-1 bg-blue-600 text-white px-1.5 sm:px-2 py-1 sm:py-1.5 rounded text-xs font-medium hover:bg-blue-700 flex items-center justify-center"><i class="fas fa-shopping-cart text-xs"></i></button>`;
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
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-900 mb-1 line-clamp-2 min-h-[2rem] sm:min-h-[2.5rem]">
                        <a href="${product.url}" class="hover:text-blue-600">${product.name}</a>
                    </h3>
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

    // Load more products function
    function loadMoreProducts() {
        if (!hasMorePages || isLoading) return;
        
        const loadMoreBtn = document.getElementById('load-more-btn');
        const loadMoreText = document.getElementById('load-more-text');
        const loadMoreSpinner = document.getElementById('load-more-spinner');
        
        loadMoreBtn.disabled = true;
        loadMoreText.textContent = 'Memuat...';
        loadMoreSpinner.classList.remove('hidden');
        
        loadProducts(currentPage + 1, false);
        
        setTimeout(() => {
            loadMoreBtn.disabled = false;
            loadMoreText.textContent = 'Muat Lebih Banyak';
            loadMoreSpinner.classList.add('hidden');
        }, 1000);
    }

    // Reset filters function
    function resetFilters() {
        const searchInput = document.getElementById('search');
        const categorySelect = document.getElementById('category');
        const minPriceInput = document.getElementById('min_price_hidden'); // Changed to hidden input
        const maxPriceInput = document.getElementById('max_price_hidden'); // Changed to hidden input
        const sortSelect = document.getElementById('sort');
        
        if (searchInput) searchInput.value = '';
        if (categorySelect) categorySelect.value = '';
        if (minPriceInput) minPriceInput.value = '';
        if (maxPriceInput) maxPriceInput.value = '';
        if (sortSelect) sortSelect.value = 'latest';
        
        loadProducts(1, true);
    }

    // Update sort function
    function updateSort(sortValue) {
        const sortSelect = document.getElementById('sort');
        if (sortSelect) {
            sortSelect.value = sortValue;
        }
        loadProducts(1, true);
    }

    // Initialize products loading when page loads
    document.addEventListener('DOMContentLoaded', function() {
        loadProducts(1, true);
        
        // Add event listeners for filters
        const searchInput = document.getElementById('search');
        const categorySelect = document.getElementById('category');
        const minPriceInput = document.getElementById('min_price_hidden'); // Changed to hidden input
        const maxPriceInput = document.getElementById('max_price_hidden'); // Changed to hidden input
        const sortSelect = document.getElementById('sort');
        
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadProducts(1, true);
                }, 500);
            });
        }
        
        if (categorySelect) {
            categorySelect.addEventListener('change', function() {
                loadProducts(1, true);
            });
        }
        
        if (minPriceInput) {
            minPriceInput.addEventListener('change', function() {
                loadProducts(1, true);
            });
        }
        
        if (maxPriceInput) {
            maxPriceInput.addEventListener('change', function() {
                loadProducts(1, true);
            });
        }
        
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                loadProducts(1, true);
            });
        }
    });

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

    // Load cart count function (for customer only)
    @if(auth()->check() && auth()->user()->isCustomer())
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
    @endif

    // Mobile filter toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile filter functionality can be added here if needed
        
        // Auto-hide filter on small screens when clicking outside
        const filterButton = document.querySelector('[x-data] button');
        if (filterButton) {
            // Filter functionality is handled by Alpine.js x-data="{ filterOpen: true }"
        }
    });

    function formatRupiah(angka) {
        let number_string = angka.replace(/[^\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/g);
        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }

    function setRupiahInput(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        input.addEventListener('input', function(e) {
            let value = formatRupiah(this.value);
            this.value = value;
        });
    }

    function cleanRupiahInput(inputId) {
        const input = document.getElementById(inputId);
        if (input) {
            input.value = input.value.replace(/\./g, '');
        }
    }

    function setupFilterFormClean(formSelector, minId, maxId) {
        const form = document.querySelector(formSelector);
        if (form) {
            form.addEventListener('submit', function(e) {
                cleanRupiahInput(minId);
                cleanRupiahInput(maxId);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        setRupiahInput('min_price');
        setRupiahInput('max_price');
        setRupiahInput('min_price_mobile');
        setRupiahInput('max_price_mobile');
        setupFilterFormClean('form[action="{{ route('products.index') }}"]#desktop-filter', 'min_price', 'max_price');
        setupFilterFormClean('form[action="{{ route('products.index') }}"]#mobile-filter', 'min_price_mobile', 'max_price_mobile');
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