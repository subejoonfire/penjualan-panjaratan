@extends('layouts.app')

@section('title', 'My Products - Seller Dashboard')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Products</h1>
                    <p class="mt-2 text-gray-600">Manage your product inventory</p>
                </div>
                <a href="{{ route('seller.products.create') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-plus mr-2"></i>
                    Add Product
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('seller.products.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-64">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Products</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Search by product name..."
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="min-w-48">
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category" id="category"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category')==$category->id ? 'selected' : ''
                                }}>
                                {{ $category->category }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-48">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status')==='active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status')==='inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'category', 'status']))
                        <a href="{{ route('seller.products.index') }}"
                            class="ml-2 bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                            Clear
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        @if($products->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <!-- Product Image -->
                <div class="aspect-w-1 aspect-h-1 relative">
                    @if($product->images->count() > 0)
                    <img src="{{ asset('storage/' . $product->images->first()->imageurl) }}"
                        alt="{{ $product->productname }}" class="w-full h-48 object-cover">
                    @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-3xl"></i>
                    </div>
                    @endif

                    <!-- Status Badge -->
                    <div class="absolute top-2 left-2">
                        <span
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <!-- Stock Badge -->
                    @if($product->productstock < 10) <div class="absolute top-2 right-2">
                        <span
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $product->productstock === 0 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $product->productstock === 0 ? 'Out of Stock' : 'Low Stock' }}
                        </span>
                </div>
                @endif
            </div>

            <!-- Product Details -->
            <div class="p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-2 truncate">{{ $product->productname }}</h3>
                <p class="text-sm text-gray-600 mb-2">{{ $product->category->category }}</p>
                <p class="text-lg font-bold text-blue-600 mb-2">Rp {{ number_format($product->productprice) }}</p>

                <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                    <span>Stock: {{ $product->productstock }}</span>
                    <span>{{ $product->images->count() }} images</span>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2">
                    <a href="{{ route('products.show', $product) }}"
                        class="flex-1 bg-gray-100 text-gray-700 px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-200 text-center">
                        View
                    </a>
                    <a href="{{ route('seller.products.edit', $product) }}"
                        class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700 text-center">
                        Edit
                    </a>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-xs text-gray-500">Views</p>
                        <p class="text-sm font-medium text-gray-900">0</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Sold</p>
                        <p class="text-sm font-medium text-gray-900">0</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Rating</p>
                        <p class="text-sm font-medium text-gray-900">-</p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
    <div class="mt-8">
        {{ $products->links() }}
    </div>
    @endif
    @else
    <!-- Empty State -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-12 text-center">
            <i class="fas fa-box text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Products Found</h3>
            <p class="text-gray-600 mb-6">
                @if(request()->hasAny(['search', 'category', 'status']))
                No products match your filter criteria. Try adjusting your filters.
                @else
                You haven't added any products yet. Start by creating your first product listing.
                @endif
            </p>
            @if(!request()->hasAny(['search', 'category', 'status']))
            <a href="{{ route('seller.products.create') }}"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>
                Add Your First Product
            </a>
            @else
            <a href="{{ route('seller.products.index') }}"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700">
                <i class="fas fa-refresh mr-2"></i>
                Clear Filters
            </a>
            @endif
        </div>
    </div>
    @endif
</div>
</div>
@endsection