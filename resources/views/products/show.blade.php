@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')
<div class="py-6">
    <div class="w-full px-4 sm:px-6 lg:px-8">
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Product Images -->
            <div class="space-y-4">
                @if($product->images->count() > 0)
                <!-- Main Image -->
                <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg overflow-hidden">
                    <img id="mainImage" src="{{ asset('storage/' . $product->images->first()->image) }}"
                        alt="{{ $product->productname }}" class="w-full h-96 object-cover">
                </div>

                <!-- Thumbnail Images -->
                @if($product->images->count() > 1)
                <div class="grid grid-cols-4 gap-2">
                    @foreach($product->images as $image)
                    <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg overflow-hidden cursor-pointer hover:opacity-75"
                        onclick="changeMainImage('{{ asset('storage/' . $image->image) }}')">
                        <img src="{{ asset('storage/' . $image->image) }}" alt="{{ $product->productname }}"
                            class="w-full h-20 object-cover">
                    </div>
                    @endforeach
                </div>
                @endif
                @else
                <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-image text-gray-400 text-6xl mb-4"></i>
                        <p class="text-gray-500">Tidak ada gambar tersedia</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Product Information -->
            <div class="space-y-6">
                <!-- Title and Price -->
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $product->productname }}</h1>
                    <p class="text-sm text-gray-500 mt-1">Detail Produk</p>
                    <div class="mt-4 flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-bold text-blue-600">Rp {{ number_format($product->productprice) }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">Harga per item</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->is_active ? 'Tersedia' : 'Tidak Tersedia' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Product Details -->
                <div class="border-t border-gray-200 pt-6">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Kategori</dt>
                            <dd class="mt-1">
                                <a href="{{ route('products.category', $product->category) }}"
                                    class="text-blue-600 hover:text-blue-500">
                                    {{ $product->category->category }}
                                </a>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Penjual</dt>
                            <dd class="mt-1 text-gray-700">{{ $product->seller->username }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Stok</dt>
                            <dd class="mt-1">
                                <span
                                    class="text-lg font-semibold
                                    {{ $product->productstock > 10 ? 'text-green-600' : ($product->productstock > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $product->productstock }} {{ $product->productstock === 1 ? 'item' : 'items' }}
                                    tersedia
                                </span>
                                @if($product->productstock <= 10 && $product->productstock > 0)
                                    <p class="text-sm text-yellow-600 mt-1">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Hanya tersisa {{ $product->productstock }} item!
                                    </p>
                                    @elseif($product->productstock === 0)
                                    <p class="text-sm text-red-600 mt-1">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Stok habis
                                    </p>
                                    @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Add to Cart Section -->
                @auth
                @if(auth()->user()->role === 'customer' && $product->is_active && $product->productstock > 0)
                <div class="border-t border-gray-200 pt-6">
                    <form action="{{ route('customer.cart.add', $product) }}" method="POST" class="space-y-4">
                        @csrf
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
                                <input type="number" name="quantity" id="quantity" min="1"
                                    max="{{ $product->productstock }}" value="1"
                                    class="w-20 text-center border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <button type="button" onclick="increaseQuantity()"
                                    class="p-2 border border-gray-300 rounded-md hover:bg-gray-50">
                                    <i class="fas fa-plus text-sm"></i>
                                </button>
                                <span class="text-sm text-gray-500">Max: {{ $product->productstock }}</span>
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
                        <p class="text-gray-700 mb-4">Silakan masuk untuk membeli produk ini</p>
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

                <!-- Product Statistics -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $product->view_count ?? 0 }}</div>
                            <div class="text-sm text-gray-500">Dilihat</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $product->sold_count ?? 0 }}</div>
                            <div class="text-sm text-gray-500">Terjual</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">
                                {{ $product->reviews()->count() > 0 ? number_format($product->reviews()->avg('rating'),
                                1) : 'N/A' }}
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
                                @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-lg {{ $i <= round($averageRating) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                            <span class="text-lg font-semibold text-gray-900">{{ number_format($averageRating, 1) }}</span>
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
                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $data['percentage'] }}%"></div>
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
                                    <h4 class="text-sm font-medium text-gray-900">{{ $review->user->username }}</h4>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-sm {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                        <span class="ml-2 text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
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
                    <p class="text-gray-500">Belum ada ulasan. Jadilah yang pertama memberikan ulasan untuk produk ini!</p>
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
                    <form action="{{ route('customer.products.reviews.store', $product) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Penilaian</label>
                            <div class="flex items-center space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                <button type="button" onclick="setRating({{ $i }})" 
                                    class="rating-star focus:outline-none" data-rating="{{ $i }}">
                                    <i class="fas fa-star text-2xl text-gray-300 hover:text-yellow-400 transition-colors"></i>
                                </button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="rating" value="5" required>
                            @error('rating')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="productreviews" class="block text-sm font-medium text-gray-700 mb-2">Ulasan (Opsional)</label>
                            <textarea name="productreviews" id="productreviews" rows="4" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Bagikan pengalaman Anda dengan produk ini...">{{ old('productreviews') }}</textarea>
                            @error('productreviews')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                Kirim Ulasan
                            </button>
                        </div>
                    </form>
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
                            <span class="text-xs text-gray-500">{{ $relatedProduct->seller->username }}</span>
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
</script>
@endsection