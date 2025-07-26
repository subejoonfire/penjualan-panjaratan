@extends('layouts.app')

@section('title', 'Search Results for "' . $searchTerm . '"')

@section('content')
<div class="py-6">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-blue-600">
                        <i class="fas fa-home mr-2"></i>
                        Products
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500">Search Results</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Search Results</h1>
            <p class="mt-2 text-gray-600">
                @if($products->total() > 0)
                {{ $products->total() }} products found for "<span class="font-semibold">{{ $searchTerm }}</span>"
                @else
                No products found for "<span class="font-semibold">{{ $searchTerm }}</span>"
                @endif
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-7 gap-6">
            <!-- Sidebar Filters -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg p-6">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('products.search') }}">
                        <div class="mb-6">
                            <label for="q" class="block text-sm font-medium text-gray-700 mb-2">Search Products</label>
                            <input type="text" name="q" id="q" value="{{ $searchTerm }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Search products..." required>
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-6">
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select name="category" id="category" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->category }} ({{ $category->products_count }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Price Range</h3>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="min_price" value="{{ request('min_price') }}"
                                    placeholder="Min" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <input type="number" name="max_price" value="{{ request('max_price') }}"
                                    placeholder="Max" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            @if($priceRange)
                            <p class="text-xs text-gray-500 mt-1">
                                Range: Rp {{ number_format($priceRange->min_price) }} - Rp {{ number_format($priceRange->max_price) }}
                            </p>
                            @endif
                        </div>

                        <!-- Sort -->
                        <div class="mb-6">
                            <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                            <select name="sort" id="sort" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="relevance" {{ $sortBy == 'relevance' ? 'selected' : '' }}>Relevance</option>
                                <option value="latest" {{ $sortBy == 'latest' ? 'selected' : '' }}>Latest</option>
                                <option value="price_low" {{ $sortBy == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ $sortBy == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="name" {{ $sortBy == 'name' ? 'selected' : '' }}>Name A-Z</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            Search
                        </button>
                    </form>

                    <!-- Applied Filters -->
                    @if(request()->hasAny(['category', 'min_price', 'max_price', 'sort']))
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Applied Filters</h3>
                        <div class="space-y-2">
                            @if(request('category'))
                            @php
                                $selectedCategory = $categories->firstWhere('id', request('category'));
                            @endphp
                            @if($selectedCategory)
                            <div class="flex items-center justify-between text-sm">
                                <span>Category: {{ $selectedCategory->category }}</span>
                                <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="text-red-600 hover:text-red-500">×</a>
                            </div>
                            @endif
                            @endif
                            
                            @if(request('min_price') || request('max_price'))
                            <div class="flex items-center justify-between text-sm">
                                <span>Price: 
                                    @if(request('min_price'))Rp {{ number_format(request('min_price')) }}@endif
                                    @if(request('min_price') && request('max_price')) - @endif
                                    @if(request('max_price'))Rp {{ number_format(request('max_price')) }}@endif
                                </span>
                                <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}" class="text-red-600 hover:text-red-500">×</a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Products Grid -->
            <div class="lg:col-span-4 xl:col-span-5 2xl:col-span-6">
                @if($products->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                    @foreach($products as $product)
                    <div class="bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                            @if($product->images->count() > 0)
                            <img src="{{ asset('storage/' . $product->images->first()->image) }}"
                                alt="{{ $product->productname }}" class="w-full h-64 object-cover">
                            @else
                            <div class="w-full h-64 flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-4xl"></i>
                            </div>
                            @endif
                        </div>

                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 truncate">
                                {{ $product->productname }}
                            </h3>
                            
                            <div class="flex items-center mb-2">
                                @if($product->reviews->count() > 0)
                                <div class="flex items-center mr-2">
                                    @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-sm {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-600">({{ $product->review_count }})</span>
                                @else
                                <span class="text-sm text-gray-500">No reviews yet</span>
                                @endif
                            </div>

                            <p class="text-2xl font-bold text-blue-600 mb-2">
                                Rp {{ number_format($product->productprice, 0, ',', '.') }}
                            </p>

                            <div class="flex items-center justify-between text-sm text-gray-600 mb-3">
                                <span>{{ $product->seller->username }}</span>
                                <span>{{ $product->category->category }}</span>
                            </div>

                            <div class="flex space-x-2">
                                <a href="{{ route('products.show', $product) }}" 
                                    class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                                    View Details
                                </a>
                                @auth
                                @if(auth()->user()->isCustomer() && $product->productstock > 0)
                                <form action="{{ route('customer.cart.add', $product) }}" method="POST" class="flex-shrink-0">
                                    @csrf
                                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </form>
                                @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->withQueryString()->links() }}
                </div>
                @else
                <div class="text-center py-12">
                    <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No products found</h3>
                    <p class="text-gray-600">
                        Try searching with different keywords or check the spelling.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('products.index') }}" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            Browse All Products
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection