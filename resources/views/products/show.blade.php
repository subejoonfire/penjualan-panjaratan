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
                        Produk
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="{{ route('products.category', $product->category) }}"
                            class="text-gray-700 hover:text-blue-600">
                            {{ $product->category->category }}
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Product Images -->
            <div class="lg:col-span-1">
                @if($product->images->count() > 0)
                <!-- Main Image -->
                <div class="bg-gray-200 rounded-lg overflow-hidden mb-4">
                    <img id="mainImage" src="{{ asset('storage/' . $product->images->first()->image) }}"
                        alt="{{ $product->productname }}" class="w-full h-80 object-cover">
                </div>

                <!-- Thumbnail Images -->
                @if($product->images->count() > 1)
                <div class="grid grid-cols-4 gap-2">
                    @foreach($product->images as $image)
                    <div class="bg-gray-200 rounded-md overflow-hidden cursor-pointer hover:opacity-75 border-2 border-transparent hover:border-blue-500 transition-colors"
                        onclick="changeMainImage('{{ asset('storage/' . $image->image) }}')">
                        <img src="{{ asset('storage/' . $image->image) }}" alt="{{ $product->productname }}"
                            class="w-full h-16 object-cover">
                    </div>
                    @endforeach
                </div>
                @endif
                @else
                <div class="bg-gray-200 rounded-lg h-80 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-image text-gray-400 text-4xl mb-2"></i>
                        <p class="text-gray-500 text-sm">No image available</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Product Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Product Title -->
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->productname }}</h1>

                    <!-- Quick Info -->
                    <div class="flex items-center space-x-4 text-sm text-gray-600 mb-4">
                        <span class="flex items-center">
                            <i class="fas fa-tag mr-1"></i>
                            <a href="{{ route('products.category', $product->category) }}"
                                class="text-blue-600 hover:text-blue-500">
                                {{ $product->category->category }}
                            </a>
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-store mr-1"></i>
                            {{ $product->seller->nickname ?? $product->seller->username }}
                        </span>
                        @if($totalReviews > 0)
                        <span class="flex items-center">
                            <div class="flex items-center mr-1">
                                @for($i = 1; $i <= 5; $i++) <i
                                    class="fas fa-star text-xs {{ $i <= round($averageRating) ? 'text-yellow-400' : 'text-gray-300' }}">
                                    </i>
                                    @endfor
                            </div>
                            <span>{{ number_format($averageRating, 1) }} ({{ $totalReviews }} reviews)</span>
                        </span>
                        @endif
                    </div>

                    <!-- Price and Status -->
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($product->productprice) }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->is_active ? 'Tersedia' : 'Tidak Tersedia' }}
                            </span>
                        </div>
                    </div>

                    <!-- Stock Info -->
                    <div class="mb-4">
                        <span class="text-sm font-medium text-gray-700">Stock: </span>
                        <span
                            class="text-sm font-semibold
                            {{ $product->productstock > 10 ? 'text-green-600' : ($product->productstock > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $product->productstock }} items available
                        </span>
                        @if($product->productstock <= 10 && $product->productstock > 0)
                            <p class="text-sm text-yellow-600 mt-1">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Only {{ $product->productstock }} left in stock!
                            </p>
                            @elseif($product->productstock === 0)
                            <p class="text-sm text-red-600 mt-1">
                                <i class="fas fa-times-circle mr-1"></i>
                                Out of stock
                            </p>
                            @endif
                    </div>
                </div>



                <!-- Action Buttons -->
                @auth
                @if(auth()->user()->role === 'customer')
                <div class="border-t border-gray-200 pt-6">
                    <!-- Wishlist and Cart Actions -->
                    <div class="space-y-4">
                        @php
                        $isInWishlist = auth()->user() && \App\Models\Wishlist::where('user_id',
                        auth()->id())->where('product_id', $product->id)->exists();
                        @endphp

                        @if($product->is_active && $product->productstock > 0)
                        <!-- Quantity Selector -->
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                Jumlah
                            </label>
                            <div class="flex items-center space-x-2">
                                <button type="button" onclick="decreaseQuantity()"
                                    class="p-2 border border-gray-300 rounded-md hover:bg-gray-50">
                                    <i class="fas fa-minus text-sm"></i>
                                </button>
                                <input type="number" id="quantity" min="1"
                                    max="{{ $product->productstock }}" value="1"
                                    class="w-20 text-center border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <button type="button" onclick="increaseQuantity()"
                                    class="p-2 border border-gray-300 rounded-md hover:bg-gray-50">
                                    <i class="fas fa-plus text-sm"></i>
                                </button>
                                <span class="text-sm text-gray-500">Max: {{ $product->productstock }}</span>
                            </div>
                        </div>

                        <!-- Action Buttons Grid -->
                        <div class="grid grid-cols-1 gap-3">
                            <!-- Add to Cart Button -->
                            <button type="button" onclick="addToCart({{ $product->id }}, event)"
                                class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                                <i class="fas fa-shopping-cart mr-2"></i>
                                Tambah ke Keranjang
                            </button>

                            <!-- Buy Now Button -->
                            <button type="button" onclick="buyNow({{ $product->id }}, event)"
                                class="w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 font-medium">
                                <i class="fas fa-credit-card mr-2"></i>
                                Beli Sekarang
                            </button>

                            <!-- Wishlist Button -->
                            <button onclick="toggleWishlist({{ $product->id }})" id="wishlistBtn"
                                class="w-full {{ $isInWishlist ? 'bg-red-600 hover:bg-red-700' : 'bg-gray-600 hover:bg-gray-700' }} text-white py-3 px-4 rounded-md transition-colors font-medium">
                                <i class="fas fa-heart mr-2"></i>
                                <span id="wishlistText">{{ $isInWishlist ? 'Hapus dari Produk Disukai' : 'Tambah ke Produk Disukai'
                                    }}</span>
                            </button>
                        </div>
                        @else
                        <!-- Out of Stock Message -->
                        <div class="bg-gray-100 border border-gray-300 rounded-md p-4 text-center">
                            <p class="text-gray-600 font-medium">
                                <i class="fas fa-times-circle mr-2"></i>
                                Produk sedang tidak tersedia
                            </p>
                        </div>

                        <!-- Wishlist Button (even when out of stock) -->
                        <button onclick="toggleWishlist({{ $product->id }})" id="wishlistBtn"
                            class="w-full {{ $isInWishlist ? 'bg-red-600 hover:bg-red-700' : 'bg-gray-600 hover:bg-gray-700' }} text-white py-3 px-4 rounded-md transition-colors font-medium">
                            <i class="fas fa-heart mr-2"></i>
                            <span id="wishlistText">{{ $isInWishlist ? 'Hapus dari Produk Disukai' : 'Tambah ke Produk Disukai'
                                }}</span>
                        </button>
                        @endif
                    </div>
                </div>
                @elseif(auth()->user()->role === 'seller' && auth()->user()->id === $product->iduserseller)
                <div class="border-t border-gray-200 pt-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-800">
                                    Ini adalah produk Anda. Anda dapat
                                    <a href="{{ route('seller.products.edit', $product) }}"
                                        class="font-medium underline">
                                        mengeditnya di sini
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
                        <p class="text-gray-700 mb-4">Silakan login untuk membeli produk ini</p>
                        <div class="space-x-4">
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Daftar
                            </a>
                        </div>
                    </div>
                </div>
                @endauth
            </div>
        </div>

        <!-- Product Statistics -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Statistik Produk</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $product->view_count ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Dilihat</div>
                    </div>
                    <div class="text-center">
                                                        <div class="text-2xl font-bold text-gray-900">{{ $product->sold_count }}</div>
                        <div class="text-sm text-gray-500">Terjual</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">
                            {{ $totalReviews > 0 ? number_format($averageRating, 1) : 'N/A' }}
                        </div>
                        <div class="text-sm text-gray-500">Rating</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        <div class="mt-12">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Deskripsi Produk</h3>
                </div>
                <div class="px-6 py-6">
                    <div class="prose max-w-none">
                        {!! nl2br(e($product->productdescription)) !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">
                            Ulasan Pelanggan ({{ $totalReviews }})
                        </h3>
                        @if($totalReviews > 0)
                        <div class="flex items-center">
                            <div class="flex items-center mr-2">
                                @for($i = 1; $i <= 5; $i++) <i
                                    class="fas fa-star text-lg {{ $i <= round($averageRating) ? 'text-yellow-400' : 'text-gray-300' }}">
                                    </i>
                                    @endfor
                            </div>
                            <span class="text-lg font-semibold text-gray-900">{{ number_format($averageRating, 1)
                                }}</span>
                            <span class="text-sm text-gray-500 ml-1">dari 5</span>
                        </div>
                        @endif
                    </div>
                </div>

                @if($totalReviews > 0)
                <!-- Rating Distribution -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="space-y-2">
                        @foreach($ratingDistribution as $rating => $data)
                        <div class="flex items-center text-sm">
                            <span class="w-3">{{ $rating }}</span>
                            <i class="fas fa-star text-yellow-400 mx-1"></i>
                            <div class="flex-1 mx-3">
                                <div class="bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-400 h-2 rounded-full"
                                        style="width: {{ $data['percentage'] }}%"></div>
                                </div>
                            </div>
                            <span class="w-8 text-right">{{ $data['count'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Reviews List -->
                <div class="px-6 py-6">
                    <div class="space-y-6">
                        @foreach($product->reviews()->with('user')->latest()->limit(5)->get() as $review)
                        <div class="flex space-x-4">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $review->user->nickname ??
                                        $review->user->username }}</h4>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++) <i
                                            class="fas fa-star text-sm {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}">
                                            </i>
                                            @endfor
                                            <span class="ml-2 text-sm text-gray-500">{{
                                                $review->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                @if($review->productreviews)
                                <p class="mt-2 text-sm text-gray-700">{{ $review->productreviews }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach

                        @if($totalReviews > 5)
                        <div class="text-center pt-4">
                            <button class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                                Lihat semua {{ $totalReviews }} ulasan
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                @else
                <div class="px-6 py-8 text-center">
                    <i class="fas fa-star text-gray-400 text-3xl mb-2"></i>
                    <p class="text-gray-500">Belum ada ulasan. Jadilah yang pertama memberikan ulasan untuk produk ini!
                    </p>
                </div>
                @endif
            </div>
        </div>

        <!-- Add Review Form -->
        @auth
        @if($canReview)
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Tulis Ulasan</h3>
                </div>
                <div class="px-6 py-6">
                    <div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Penilaian</label>
                            <div class="flex items-center space-x-1">
                                @for($i = 1; $i <= 5; $i++) <button type="button" onclick="setRating({{ $i }})"
                                    class="rating-star focus:outline-none" data-rating="{{ $i }}">
                                    <i
                                        class="fas fa-star text-2xl text-gray-300 hover:text-yellow-400 transition-colors"></i>
                                    </button>
                                    @endfor
                            </div>
                            <input type="hidden" id="rating" value="5" required>
                            @error('rating')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="productreviews" class="block text-sm font-medium text-gray-700 mb-2">Ulasan
                                (Opsional)</label>
                            <textarea id="productreviews" rows="4"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Bagikan pengalaman Anda dengan produk ini...">{{ old('productreviews') }}</textarea>
                            @error('productreviews')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="button" onclick="submitReview({{ $product->id }})"
                                class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                Kirim Ulasan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @elseif(auth()->user()->isCustomer())
        <div class="mt-8">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                <i class="fas fa-info-circle text-gray-400 text-2xl mb-2"></i>
                <p class="text-gray-600">Anda perlu membeli dan menerima produk ini sebelum dapat memberikan ulasan.</p>
            </div>
        </div>
        @endif
        @endauth

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <div class="mt-12">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Produk Terkait</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 2xl:grid-cols-8 gap-4">
                @foreach($relatedProducts as $relatedProduct)
                <div class="bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                        @if($relatedProduct->images->count() > 0)
                        <img src="{{ asset('storage/' . $relatedProduct->images->first()->image) }}"
                            alt="{{ $relatedProduct->productname }}" class="w-full h-48 object-cover">
                        @else
                        <div class="w-full h-48 flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-2xl"></i>
                        </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ $relatedProduct->productname }}</h4>
                        <p class="text-lg font-bold text-blue-600 mt-1">Rp {{
                            number_format($relatedProduct->productprice) }}</p>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-xs text-gray-500">{{ $relatedProduct->seller->nickname ??
                                $relatedProduct->seller->username }}</span>
                            <a href="{{ route('products.show', $relatedProduct) }}"
                                class="text-blue-600 hover:text-blue-500 text-sm">
                                Lihat
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

    function setRating(rating) {
        document.getElementById('rating').value = rating;
        
        // Update star display
        const stars = document.querySelectorAll('.rating-star i');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    }

    // Initialize rating to 5 stars on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('rating')) {
            setRating(5);
        }
    });

    // Wishlist functionality
    function toggleWishlist(productId) {
        const btn = document.getElementById('wishlistBtn');
        const text = document.getElementById('wishlistText');
        const originalText = text.textContent;
        
        btn.disabled = true;
        text.textContent = 'Memproses...';
        
        fetch(`/customer/wishlist/toggle/${productId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.action === 'added') {
                    btn.classList.remove('bg-gray-600', 'hover:bg-gray-700');
                    btn.classList.add('bg-red-600', 'hover:bg-red-700');
                    text.textContent = 'Hapus dari Produk Disukai';
                } else {
                    btn.classList.remove('bg-red-600', 'hover:bg-red-700');
                    btn.classList.add('bg-gray-600', 'hover:bg-gray-700');
                    text.textContent = 'Tambah ke Produk Disukai';
                }
                
                // Show toast notification
                showModalNotification({
                    type: 'success',
                    title: 'Berhasil!',
                    message: data.message || 'Berhasil menambahkan ke keranjang',
                    confirmText: 'OK',
                    showCancel: false
                });
            } else {
                text.textContent = originalText;
                showModalNotification({
                    type: 'error',
                    title: 'Gagal!',
                    message: data.message || 'Gagal menambahkan ke keranjang',
                    confirmText: 'OK',
                    showCancel: false
                });
            }
            btn.disabled = false;
        })
        .catch(error => {
            console.error('Error:', error);
            text.textContent = originalText;
            btn.disabled = false;
            showModalNotification({
                type: 'error',
                title: 'Error!',
                message: 'Terjadi kesalahan saat menambahkan ke keranjang: ' + error.message,
                confirmText: 'OK',
                showCancel: false
            });
        });
    }
    
    function showToast(message) {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        // Show toast
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);
        
        // Hide and remove toast
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }



    // Add to cart function
    function addToCart(productId, event) {
        if (event) event.preventDefault();
        
        // Get quantity
        const quantity = document.getElementById('quantity').value;
        
        // Find the button that was clicked
        const button = event ? event.target.closest('button') : null;
        const originalText = button ? button.innerHTML : '';
        
        // Disable button and show loading animation
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
        }
        
        const formData = new FormData();
        formData.append('quantity', quantity);
        
        fetch(`${window.location.origin}/customer/cart/add/${productId}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Cart response:', data);
            if (data.success) {
                // Show success animation
                if (button) {
                    button.innerHTML = '<i class="fas fa-check mr-2"></i>Berhasil!';
                    button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    button.classList.add('bg-green-600');
                }
                
                // Show success message
                showModalNotification({
                    type: 'success',
                    title: 'Berhasil!',
                    message: data.message || 'Berhasil menambahkan ke keranjang',
                    confirmText: 'OK',
                    showCancel: false
                });
                
                // Refresh cart count
                refreshCartCount();
                
                // Reset button after 2 seconds
                setTimeout(() => {
                    if (button) {
                        button.innerHTML = '<i class="fas fa-shopping-cart mr-2"></i>Tambah ke Keranjang';
                        button.classList.remove('bg-green-600');
                        button.classList.add('bg-blue-600', 'hover:bg-blue-700');
                        button.disabled = false;
                    }
                }, 2000);
            } else {
                showModalNotification({
                    type: 'error',
                    title: 'Gagal!',
                    message: data.message || 'Gagal menambahkan ke keranjang',
                    confirmText: 'OK',
                    showCancel: false
                });
                if (button) {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            }
        })
        .catch(error => {
            console.error('Cart error:', error);
            showModalNotification({
                type: 'error',
                title: 'Error!',
                message: 'Terjadi kesalahan saat menambahkan ke keranjang: ' + error.message,
                confirmText: 'OK',
                showCancel: false
            });
            if (button) {
                button.innerHTML = originalText;
                button.disabled = false;
            }
        });
    }

    // Refresh cart count function
    function refreshCartCount() {
        fetch(`${window.location.origin}/api/cart/count`)
            .then(response => response.json())
            .then(data => {
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = data.count;
                    cartCount.style.display = data.count > 0 ? 'inline-flex' : 'none';
                }
            })
            .catch(error => console.error('Error refreshing cart count:', error));
    }

    // Buy Now function (direct checkout)
    function buyNow(productId, event) {
        if (event) event.preventDefault();
        
        // Get quantity
        const quantity = document.getElementById('quantity').value;
        
        // Find the button that was clicked
        const button = event ? event.target.closest('button') : null;
        const originalText = button ? button.innerHTML : '';
        
        // Disable button and show loading animation
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
        }
        
        const formData = new FormData();
        formData.append('quantity', quantity);
        
        fetch(`${window.location.origin}/customer/cart/add/${productId}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Cart response:', data);
            if (data.success) {
                // Show success message
                showModalNotification({
                    type: 'success',
                    title: 'Berhasil!',
                    message: 'Produk berhasil ditambahkan ke keranjang. Mengalihkan ke checkout...',
                    confirmText: 'OK',
                    showCancel: false,
                    onConfirm: () => {
                        // Redirect to checkout
                        window.location.href = `${window.location.origin}/customer/checkout`;
                    }
                });
                
                // Refresh cart count
                refreshCartCount();
            } else {
                showModalNotification({
                    type: 'error',
                    title: 'Gagal!',
                    message: data.message || 'Gagal menambahkan ke keranjang',
                    confirmText: 'OK',
                    showCancel: false
                });
                if (button) {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            }
        })
        .catch(error => {
            console.error('Cart error:', error);
            showModalNotification({
                type: 'error',
                title: 'Error!',
                message: 'Terjadi kesalahan saat menambahkan ke keranjang: ' + error.message,
                confirmText: 'OK',
                showCancel: false
            });
            if (button) {
                button.innerHTML = originalText;
                button.disabled = false;
            }
        });
    }

    // Submit review function
    function submitReview(productId) {
        const rating = document.getElementById('rating').value;
        const review = document.getElementById('productreviews').value;
        const button = event.target;
        const originalText = button.textContent;
        
        // Disable button and show loading
        button.disabled = true;
        button.textContent = 'Mengirim...';
        
        const formData = new FormData();
        formData.append('rating', rating);
        formData.append('productreviews', review);
        
        fetch(`${window.location.origin}/customer/products/${productId}/reviews`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showModalNotification({
                    type: 'success',
                    title: 'Berhasil!',
                    message: data.message || 'Ulasan berhasil dikirim',
                    confirmText: 'OK',
                    showCancel: false,
                    onConfirm: () => {
                        window.location.reload();
                    }
                });
            } else {
                showModalNotification({
                    type: 'error',
                    title: 'Gagal!',
                    message: data.message || 'Gagal mengirim ulasan',
                    confirmText: 'OK',
                    showCancel: false
                });
                button.textContent = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Review error:', error);
            showModalNotification({
                type: 'error',
                title: 'Error!',
                message: 'Terjadi kesalahan saat mengirim ulasan',
                confirmText: 'OK',
                showCancel: false
            });
            button.textContent = originalText;
            button.disabled = false;
        });
    }
</script>
@endsection