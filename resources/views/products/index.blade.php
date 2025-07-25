@extends('layouts.app')

@section('title', 'Browse Products - Penjualan Panjaratan')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Browse Products</h1>
            <p class="mt-2 text-gray-600">Discover amazing products from our sellers</p>
        </div>

        <div class="lg:grid lg:grid-cols-4 lg:gap-8">
            <!-- Filters Sidebar -->
            <div class="hidden lg:block">
                <div class="bg-white shadow rounded-lg p-6 sticky top-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Filters</h3>
                    
                    <form method="GET" action="{{ route('products.index') }}" class="space-y-6">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Search products..."
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Categories -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select name="category" id="category" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->categoryname }} ({{ $category->products_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price Range -->
                        @if($priceRange && $priceRange->max_price > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <input type="number" name="min_price" value="{{ request('min_price') }}"
                                           placeholder="Min" min="0" max="{{ $priceRange->max_price }}"
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <input type="number" name="max_price" value="{{ request('max_price') }}"
                                           placeholder="Max" min="0" max="{{ $priceRange->max_price }}"
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="mt-2 text-xs text-gray-500">
                                Range: Rp {{ number_format($priceRange->min_price) }} - Rp {{ number_format($priceRange->max_price) }}
                            </div>
                        </div>
                        @endif

                        <div class="pt-4">
                            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Apply Filters
                            </button>
                            @if(request()->hasAny(['search', 'category', 'min_price', 'max_price']))
                                <a href="{{ route('products.index') }}" class="w-full mt-2 bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 text-center block">
                                    Clear Filters
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Products Content -->
            <div class="lg:col-span-3">
                <!-- Sort & View Options -->
                <div class="bg-white shadow rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-700">
                                @if(request()->hasAny(['search', 'category', 'min_price', 'max_price']))
                                    {{ $products->total() }} results found
                                @else
                                    {{ $products->total() }} products
                                @endif
                            </span>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <label for="sort" class="text-sm text-gray-700">Sort by:</label>
                            <select name="sort" id="sort" onchange="updateSort(this.value)" 
                                    class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="latest" {{ $sortBy === 'latest' ? 'selected' : '' }}>Latest</option>
                                <option value="popular" {{ $sortBy === 'popular' ? 'selected' : '' }}>Most Popular</option>
                                <option value="price_low" {{ $sortBy === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ $sortBy === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="name" {{ $sortBy === 'name' ? 'selected' : '' }}>Name A-Z</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow group">
                                <!-- Product Image -->
                                <div class="aspect-w-1 aspect-h-1 relative">
                                    <a href="{{ route('products.show', $product) }}">
                                        @if($product->images->count() > 0)
                                            <img src="{{ asset('storage/' . $product->images->first()->imageurl) }}" 
                                                 alt="{{ $product->productname }}" 
                                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400 text-3xl"></i>
                                            </div>
                                        @endif
                                    </a>
                                    
                                    <!-- Quick Actions -->
                                    @auth
                                        @if(auth()->user()->isCustomer())
                                            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <form action="{{ route('customer.cart.add', $product) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" 
                                                            class="bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                            title="Add to Cart">
                                                        <i class="fas fa-shopping-cart text-sm"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @endauth
                                </div>

                                <!-- Product Details -->
                                <div class="p-4">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2 truncate">
                                        <a href="{{ route('products.show', $product) }}" class="hover:text-blue-600">
                                            {{ $product->productname }}
                                        </a>
                                    </h3>
                                    
                                    <p class="text-sm text-gray-600 mb-2">{{ $product->category->categoryname }}</p>
                                    <p class="text-sm text-gray-500 mb-3">by {{ $product->seller->username }}</p>
                                    
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-lg font-bold text-blue-600">Rp {{ number_format($product->price) }}</span>
                                        <span class="text-sm text-gray-500">Stock: {{ $product->stock }}</span>
                                    </div>

                                    <!-- Rating & Reviews -->
                                    @if($product->reviews->count() > 0)
                                        @php
                                            $avgRating = $product->reviews->avg('rating');
                                        @endphp
                                        <div class="flex items-center mb-3">
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star text-xs {{ $i <= $avgRating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="ml-2 text-sm text-gray-600">({{ $product->reviews->count() }})</span>
                                        </div>
                                    @endif

                                    <!-- Actions -->
                                    <div class="flex space-x-2">
                                        <a href="{{ route('products.show', $product) }}" 
                                           class="flex-1 bg-gray-100 text-gray-700 px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-200 text-center">
                                            View Details
                                        </a>
                                        @auth
                                            @if(auth()->user()->isCustomer() && $product->stock > 0)
                                                <form action="{{ route('customer.cart.add', $product) }}" method="POST" class="flex-1">
                                                    @csrf
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" 
                                                            class="w-full bg-blue-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                                                        Add to Cart
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" 
                                               class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700 text-center">
                                                Login to Buy
                                            </a>
                                        @endauth
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
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Products Found</h3>
                            <p class="text-gray-600 mb-6">
                                @if(request()->hasAny(['search', 'category', 'min_price', 'max_price']))
                                    No products match your search criteria. Try adjusting your filters.
                                @else
                                    No products are available at the moment.
                                @endif
                            </p>
                            @if(request()->hasAny(['search', 'category', 'min_price', 'max_price']))
                                <a href="{{ route('products.index') }}" 
                                   class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-refresh mr-2"></i>
                                    Clear Filters
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

// Mobile filter toggle (if needed)
document.addEventListener('DOMContentLoaded', function() {
    // Add mobile filter functionality here if needed
});
</script>
@endsection