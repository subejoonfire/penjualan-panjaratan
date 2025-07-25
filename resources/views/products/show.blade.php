@extends('layouts.app')

@section('title', 'Detail Produk')

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
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="{{ route('products.category', $product->category) }}" class="text-gray-700 hover:text-blue-600">
                            {{ $product->category->categoryname }}
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500">{{ Str::limit($product->productname, 30) }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Product Images -->
            <div class="space-y-4">
                @if($product->images->count() > 0)
                    <!-- Main Image -->
                    <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg overflow-hidden">
                        <img id="mainImage" src="{{ asset('storage/' . $product->images->first()->imageurl) }}" 
                             alt="{{ $product->productname }}" 
                             class="w-full h-96 object-cover">
                    </div>
                    
                    <!-- Thumbnail Images -->
                    @if($product->images->count() > 1)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($product->images as $image)
                            <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg overflow-hidden cursor-pointer hover:opacity-75"
                                 onclick="changeMainImage('{{ asset('storage/' . $image->imageurl) }}')">
                                <img src="{{ asset('storage/' . $image->imageurl) }}" 
                                     alt="{{ $product->productname }}" 
                                     class="w-full h-20 object-cover">
                            </div>
                        @endforeach
                    </div>
                    @endif
                @else
                    <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-image text-gray-400 text-6xl mb-4"></i>
                            <p class="text-gray-500">No image available</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Product Information -->
            <div class="space-y-6">
                <!-- Title and Price -->
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detail Produk</h1>
                    <div class="mt-4 flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-bold text-blue-600">Rp {{ number_format($product->price) }}</p>
                            <p class="text-sm text-gray-500 mt-1">Price per item</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->is_active ? 'Available' : 'Unavailable' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Product Details -->
                <div class="border-t border-gray-200 pt-6">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Category</dt>
                            <dd class="mt-1">
                                <a href="{{ route('products.category', $product->category) }}" 
                                   class="text-blue-600 hover:text-blue-500">
                                    {{ $product->category->categoryname }}
                                </a>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Seller</dt>
                            <dd class="mt-1 text-gray-700">{{ $product->seller->username }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Stock</dt>
                            <dd class="mt-1">
                                <span class="text-lg font-semibold
                                    {{ $product->stock > 10 ? 'text-green-600' : ($product->stock > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $product->stock }} {{ $product->stock === 1 ? 'item' : 'items' }} available
                                </span>
                                @if($product->stock <= 10 && $product->stock > 0)
                                    <p class="text-sm text-yellow-600 mt-1">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Only {{ $product->stock }} left in stock!
                                    </p>
                                @elseif($product->stock === 0)
                                    <p class="text-sm text-red-600 mt-1">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Out of stock
                                    </p>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Add to Cart Section -->
                @auth
                    @if(auth()->user()->role === 'customer' && $product->is_active && $product->stock > 0)
                    <div class="border-t border-gray-200 pt-6">
                        <form action="{{ route('customer.cart.add', $product) }}" method="POST" class="space-y-4">
                            @csrf
                            <!-- Quantity Selector -->
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                    Quantity
                                </label>
                                <div class="flex items-center space-x-2">
                                    <button type="button" onclick="decreaseQuantity()" 
                                            class="p-2 border border-gray-300 rounded-md hover:bg-gray-50">
                                        <i class="fas fa-minus text-sm"></i>
                                    </button>
                                    <input type="number" name="quantity" id="quantity" min="1" max="{{ $product->stock }}" 
                                           value="1" 
                                           class="w-20 text-center border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <button type="button" onclick="increaseQuantity()" 
                                            class="p-2 border border-gray-300 rounded-md hover:bg-gray-50">
                                        <i class="fas fa-plus text-sm"></i>
                                    </button>
                                    <span class="text-sm text-gray-500">Max: {{ $product->stock }}</span>
                                </div>
                            </div>

                            <!-- Add to Cart Button -->
                            <button type="submit" 
                                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                                <i class="fas fa-shopping-cart mr-2"></i>
                                Tambah ke Keranjang
                            </button>
                        </form>
                    </div>
                    @elseif(auth()->user()->role === 'seller' && auth()->user()->id === $product->seller_id)
                    <div class="border-t border-gray-200 pt-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-800">
                                        This is your product. You can 
                                        <a href="{{ route('seller.products.edit', $product) }}" class="font-medium underline">
                                            edit it here
                                        </a>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @else
                <div class="border-t border-gray-200 pt-6">
                    <div class="bg-gray-50 border border-gray-200 rounded-md p-4 text-center">
                        <p class="text-gray-700 mb-4">Please login to purchase this product</p>
                        <div class="space-x-4">
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Login
                            </a>
                            <a href="{{ route('register') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Register
                            </a>
                        </div>
                    </div>
                </div>
                @endauth

                <!-- Product Statistics -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $product->views ?? 0 }}</div>
                            <div class="text-sm text-gray-500">Views</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $product->sold ?? 0 }}</div>
                            <div class="text-sm text-gray-500">Sold</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">
                                {{ $product->reviews()->count() > 0 ? number_format($product->reviews()->avg('rating'), 1) : 'N/A' }}
                            </div>
                            <div class="text-sm text-gray-500">Rating</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        <div class="mt-12">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Product Description</h3>
                </div>
                <div class="px-6 py-6">
                    <div class="prose max-w-none">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        @if($product->reviews()->count() > 0)
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        Customer Reviews ({{ $product->reviews()->count() }})
                    </h3>
                </div>
                <div class="px-6 py-6 space-y-6">
                    @foreach($product->reviews()->with('user')->latest()->limit(5)->get() as $review)
                    <div class="flex space-x-4">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                <i class="fas fa-user text-gray-600"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-medium text-gray-900">{{ $review->user->username }}</h4>
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-sm {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                    <span class="ml-2 text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-700">{{ $review->comment }}</p>
                        </div>
                    </div>
                    @endforeach
                    
                    @if($product->reviews()->count() > 5)
                    <div class="text-center pt-4">
                        <button class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                            View all {{ $product->reviews()->count() }} reviews
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <div class="mt-12">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Related Products</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                <div class="bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                        @if($relatedProduct->images->count() > 0)
                            <img src="{{ asset('storage/' . $relatedProduct->images->first()->imageurl) }}" 
                                 alt="{{ $relatedProduct->productname }}" 
                                 class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-2xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ $relatedProduct->productname }}</h4>
                        <p class="text-lg font-bold text-blue-600 mt-1">Rp {{ number_format($relatedProduct->price) }}</p>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-xs text-gray-500">{{ $relatedProduct->seller->username }}</span>
                            <a href="{{ route('products.show', $relatedProduct) }}" 
                               class="text-blue-600 hover:text-blue-500 text-sm">
                                View
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function changeMainImage(imageUrl) {
    document.getElementById('mainImage').src = imageUrl;
}

function increaseQuantity() {
    const input = document.getElementById('quantity');
    const max = parseInt(input.getAttribute('max'));
    const current = parseInt(input.value);
    if (current < max) {
        input.value = current + 1;
    }
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    const current = parseInt(input.value);
    if (current > 1) {
        input.value = current - 1;
    }
}
</script>
@endsection