@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header with Search -->
        <div class="mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Produk</h1>
                    <p class="mt-2 text-gray-600">Temukan produk menarik dari penjual kami</p>
                </div>
                
                <!-- Search Bar (Always Visible) -->
                <div class="mt-4 lg:mt-0 lg:ml-6">
                    <form method="GET" action="{{ route('products.index') }}" class="flex items-center space-x-2">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari produk..."
                                class="w-full lg:w-80 pl-10 pr-4 py-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                        <!-- Hidden inputs to preserve other filters -->
                        <input type="hidden" name="category" value="{{ request('category') }}">
                        <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                        <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                <form method="GET" action="{{ route('products.index') }}" class="space-y-4">
                    <!-- Preserve search -->
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    
                    <!-- Categories -->
                    <div>
                        <label for="mobile-category" class="block text-xs font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="category" id="mobile-category"
                            class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category')==$category->id ? 'selected' : '' }}>
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
                            <input type="number" name="min_price" value="{{ request('min_price') }}"
                                placeholder="Min" min="0" max="{{ $priceRange->max_price }}"
                                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <input type="number" name="max_price" value="{{ request('max_price') }}"
                                placeholder="Maks" min="0" max="{{ $priceRange->max_price }}"
                                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="mt-1 text-xs text-gray-500">
                            Rp {{ number_format($priceRange->min_price) }} - Rp {{ number_format($priceRange->max_price) }}
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

        <div class="lg:grid lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-7 lg:gap-6">
            <!-- Filters Sidebar -->
            <div class="hidden lg:block">
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
                        <form method="GET" action="{{ route('products.index') }}" class="space-y-4">
                            <!-- Preserve search -->
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            
                            <!-- Categories -->
                            <div>
                                <label for="category" class="block text-xs font-medium text-gray-700 mb-1">Kategori</label>
                                <select name="category" id="category"
                                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category')==$category->id ? 'selected' : '' }}>
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
                                    <div>
                                        <input type="number" name="min_price" value="{{ request('min_price') }}"
                                            placeholder="Min" min="0" max="{{ $priceRange->max_price }}"
                                            class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <input type="number" name="max_price" value="{{ request('max_price') }}"
                                            placeholder="Maks" min="0" max="{{ $priceRange->max_price }}"
                                            class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    Rp {{ number_format($priceRange->min_price) }} - Rp {{ number_format($priceRange->max_price) }}
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
            <div class="lg:col-span-4 xl:col-span-5 2xl:col-span-6">
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
                                <option value="price_high" {{ $sortBy==='price_high' ? 'selected' : '' }}>Harga ↓</option>
                                <option value="name" {{ $sortBy==='name' ? 'selected' : '' }}>Nama A-Z</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                @if($products->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                    @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow group flex flex-col h-full">
                        <!-- Product Image -->
                        <div class="aspect-w-1 aspect-h-1 relative">
                            <a href="{{ route('products.show', $product) }}">
                                @if($product->images->count() > 0)
                                <img src="{{ asset('storage/' . $product->images->first()->image) }}"
                                    alt="{{ $product->productname }}"
                                    class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-3xl"></i>
                                </div>
                                @endif
                            </a>
                        </div>
                        <!-- Product Details & Actions -->
                        <div class="flex flex-col flex-1 justify-between p-4">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 mb-1 truncate">
                                    <a href="{{ route('products.show', $product) }}" class="hover:text-blue-600">
                                        {{ $product->productname }}
                                    </a>
                                </h3>
                                <p class="text-xs text-gray-600 mb-1">{{ $product->category->category }}</p>
                                <p class="text-xs text-gray-500 mb-3">
                                    @php
                                        $desc = strip_tags($product->productdesc);
                                        $words = str_word_count($desc, 2);
                                        $wordKeys = array_keys($words);
                                        if(count($words) > 30) {
                                            $desc = substr($desc, 0, $wordKeys[30]) . '...';
                                        }
                                    @endphp
                                    {{ $desc }}
                                </p>
                            </div>
                            <div class="flex flex-col gap-2 mt-2">
                                <div class="flex gap-2 w-full">
                                    <a href="{{ route('products.show', $product) }}"
                                        class="flex-1 bg-gray-100 text-gray-700 px-3 py-2 rounded-md text-xs font-medium hover:bg-gray-200 text-center">
                                        Detail
                                    </a>
                                    @auth
                                    @if(auth()->user()->isCustomer() && $product->productstock > 0)
                                    <form action="{{ route('customer.cart.add', $product) }}" method="POST" class="flex-1">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit"
                                            class="w-full bg-blue-600 text-white px-3 py-2 rounded-md text-xs font-medium hover:bg-blue-700 flex items-center justify-center">
                                            <i class="fas fa-shopping-cart"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @else
                                    <a href="{{ route('login') }}"
                                        class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-md text-xs font-medium hover:bg-blue-700 text-center">
                                        <i class="fas fa-shopping-cart"></i>
                                    </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                <div class="mt-8">
                    {{ $products->appends(request()->query())->links() }}
                </div>
                @endif
                @else
                <!-- Empty State -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-12 text-center">
                        <i class="fas fa-search text-gray-400 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Produk Ditemukan</h3>
                        <p class="text-gray-600 mb-6">
                            @if(request()->hasAny(['search', 'category', 'min_price', 'max_price']))
                            Tidak ada produk yang sesuai dengan kriteria pencarian Anda. Coba sesuaikan filter Anda.
                            @else
                            Tidak ada produk yang tersedia saat ini.
                            @endif
                        </p>
                        @if(request()->hasAny(['search', 'category', 'min_price', 'max_price']))
                        <a href="{{ route('products.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-refresh mr-2"></i>
                            Reset Semua
                        </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function updateSort(sortValue) {
        const url = new URL(window.location);
        url.searchParams.set('sort', sortValue);
        window.location.href = url.toString();
    }

    // Mobile filter toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile filter functionality can be added here if needed
        
        // Auto-hide filter on small screens when clicking outside
        const filterButton = document.querySelector('[x-data] button');
        if (filterButton) {
            // Filter functionality is handled by Alpine.js x-data="{ filterOpen: true }"
        }
    });
</script>
@endsection