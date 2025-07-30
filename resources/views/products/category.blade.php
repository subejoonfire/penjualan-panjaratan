@extends('layouts.app')

@section('title', 'Products in ' . $category->category)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                        <span class="text-gray-500">{{ $category->category }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ $category->category }}</h1>
            <p class="mt-2 text-gray-600">{{ $products->total() }} products found</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-7 gap-6">
            <!-- Sidebar Filters -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg p-6">
                    <!-- Search in Category -->
                    <form method="GET" action="{{ route('products.category', $category) }}">
                        <div class="mb-6">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search in {{ $category->category }}</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Search products...">
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
                                <option value="latest" {{ $sortBy == 'latest' ? 'selected' : '' }}>Latest</option>
                                <option value="price_low" {{ $sortBy == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ $sortBy == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="name" {{ $sortBy == 'name' ? 'selected' : '' }}>Name A-Z</option>
                                <option value="popular" {{ $sortBy == 'popular' ? 'selected' : '' }}>Most Popular</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            Apply Filters
                        </button>
                    </form>

                    <!-- Other Categories -->
                    <div class="mt-8">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Other Categories</h3>
                        <div class="space-y-2">
                            @foreach($categories as $cat)
                            @if($cat->id !== $category->id)
                            <a href="{{ route('products.category', $cat) }}" 
                                class="block text-sm text-gray-600 hover:text-blue-600 py-1">
                                {{ $cat->category }} ({{ $cat->products_count }})
                            </a>
                            @endif
                            @endforeach
                        </div>
                    </div>
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
                                <span>{{ $product->seller->nickname ?? $product->seller->username }}</span>
                                <span>Stock: {{ $product->productstock }}</span>
                            </div>

                            <div class="flex space-x-2">
                                <a href="{{ route('products.show', $product) }}" 
                                    class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                                    View Details
                                </a>
                                @auth
                                @if(auth()->user()->isCustomer() && $product->productstock > 0)
                                <button type="button" onclick="addToCart({{ $product->id }}, event)" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
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
                    <p class="text-gray-600">Try adjusting your filters or search terms.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Show alert function
    function showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300 ${
            type === 'error' ? 'bg-red-600 text-white' : 
            type === 'success' ? 'bg-green-600 text-white' : 
            'bg-blue-600 text-white'
        }`;
        alertDiv.textContent = message;
        
        document.body.appendChild(alertDiv);
        
        // Show alert
        setTimeout(() => {
            alertDiv.classList.remove('translate-x-full');
        }, 100);
        
        // Hide and remove alert
        setTimeout(() => {
            alertDiv.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(alertDiv);
            }, 300);
        }, 3000);
    }

    // Add to cart function
    function addToCart(productId, event) {
        if (event) event.preventDefault();
        
        // Find the button that was clicked
        const button = event ? event.target.closest('button') : null;
        const originalText = button ? button.innerHTML : '';
        
        // Disable button and show loading animation
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }
        
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('quantity', 1);
        
        fetch(`/customer/cart/add/${productId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('Cart response:', data);
            if (data.success) {
                // Show success animation
                if (button) {
                    button.innerHTML = '<i class="fas fa-check"></i>';
                    button.classList.remove('bg-green-600', 'hover:bg-green-700');
                    button.classList.add('bg-blue-600');
                }
                
                // Show success message
                showAlert(data.message || 'Berhasil menambahkan ke keranjang', 'success');
                
                // Reset button after 2 seconds
                setTimeout(() => {
                    if (button) {
                        button.innerHTML = '<i class="fas fa-cart-plus"></i>';
                        button.classList.remove('bg-blue-600');
                        button.classList.add('bg-green-600', 'hover:bg-green-700');
                        button.disabled = false;
                    }
                }, 2000);
            } else {
                showAlert(data.message || 'Gagal menambahkan ke keranjang', 'error');
                if (button) {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            }
        })
        .catch(error => {
            console.error('Cart error:', error);
            showAlert('Terjadi kesalahan saat menambahkan ke keranjang', 'error');
            if (button) {
                button.innerHTML = originalText;
                button.disabled = false;
            }
        });
    }
</script>
@endsection