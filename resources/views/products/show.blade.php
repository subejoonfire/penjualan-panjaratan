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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Product Images -->
            <div class="space-y-4">
                @if($product->primaryImage)
                <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg overflow-hidden">
                    <img id="mainImage" src="{{ url('storage/'.$product->primaryImage->image) }}"
                        alt="{{ $product->productname }}" class="w-full h-96 object-cover">
                </div>
                @elseif($product->images->count() > 0)
                <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg overflow-hidden">
                    <img id="mainImage" src="{{ url('storage/'.$product->images->first()->imageurl) }}"
                        alt="{{ $product->productname }}" class="w-full h-96 object-cover">
                </div>
                @else
                <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-image text-gray-400 text-6xl mb-4"></i>
                        <p class="text-gray-500">Tidak ada gambar tersedia</p>
                    </div>
                </div>
                @endif

                <!-- Thumbnail Images -->
                @if($product->images->count() > 1)
                <div class="grid grid-cols-4 gap-2">
                    @foreach($product->images as $image)
                    <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg overflow-hidden cursor-pointer hover:opacity-75"
                        onclick="changeMainImage('{{ $image->imageurl }}')">
                        <img src="{{ $image->imageurl }}" alt="{{ $product->productname }}"
                            class="w-full h-20 object-cover">
                    </div>
                    @endforeach
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
                                        Hanya {{ $product->productstock }} tersisa!
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
                                <span class="text-sm text-gray-500">Maks: {{ $product->productstock }}</span>
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
                                        editnya di sini
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
                            <div class="text-sm text-gray-500">Tampilan</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $product->sold ?? 0 }}</div>
                            <div class="text-sm text-gray-500">Terjual</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">
                                @if($product->reviews()->count() > 0)
                                    {{ number_format($product->average_rating, 1) }}
                                @else
                                    0.0
                                @endif
                            </div>
                            <div class="text-sm text-gray-500">
                                <div class="flex items-center justify-center mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-xs {{ $i <= round($product->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                    <span class="ml-1 text-xs">({{ $product->review_count }})</span>
                                </div>
                            </div>
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
                            Ulasan & Rating ({{ $product->review_count }})
                        </h3>
                        @if($product->review_count > 0)
                            <div class="flex items-center space-x-2">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-sm {{ $i <= round($product->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-600">{{ number_format($product->average_rating, 1) }} dari 5</span>
                            </div>
                        @endif
                    </div>
                </div>

                @if($product->review_count > 0)
                    <!-- Rating Summary -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Distribusi Rating</h4>
                                @for($rating = 5; $rating >= 1; $rating--)
                                    @php
                                        $count = $product->reviews()->where('rating', $rating)->count();
                                        $percentage = $product->review_count > 0 ? ($count / $product->review_count) * 100 : 0;
                                    @endphp
                                    <div class="flex items-center space-x-2 mb-1">
                                        <span class="text-sm text-gray-600 w-8">{{ $rating }}</span>
                                        <i class="fas fa-star text-yellow-400 text-xs"></i>
                                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                                            <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600 w-8">{{ $count }}</span>
                                    </div>
                                @endfor
                            </div>
                            <div class="text-center">
                                <div class="text-4xl font-bold text-gray-900">{{ number_format($product->average_rating, 1) }}</div>
                                <div class="flex items-center justify-center mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= round($product->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                                <p class="text-sm text-gray-600 mt-1">Berdasarkan {{ $product->review_count }} ulasan</p>
                            </div>
                        </div>
                    </div>
                @endif

                @auth
                    @if(auth()->user()->role === 'customer')
                        @php
                            // Cek apakah user sudah beli produk ini
                            $hasPurchased = \App\Models\Order::whereHas('cart', function($query) {
                                $query->where('iduser', auth()->id());
                            })->whereHas('cart.cartDetails', function($query) use ($product) {
                                $query->where('idproduct', $product->id);
                            })->whereHas('transaction', function($query) {
                                $query->where('transactionstatus', 'paid');
                            })->where('status', 'delivered')->exists();

                            // Cek apakah user sudah review produk ini
                            $hasReviewed = $product->reviews()->where('iduser', auth()->id())->exists();
                        @endphp

                        @if($hasPurchased && !$hasReviewed)
                            <!-- Review Form -->
                            <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Berikan Ulasan Anda</h4>
                                <form action="{{ route('products.review.store', $product) }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                        <div class="flex items-center space-x-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <button type="button" onclick="setRating({{ $i }})" class="star-btn focus:outline-none">
                                                    <i class="fas fa-star text-2xl text-gray-300" id="star-{{ $i }}"></i>
                                                </button>
                                            @endfor
                                        </div>
                                        <input type="hidden" name="rating" id="ratingInput" value="0" required>
                                    </div>
                                    <div>
                                        <label for="review" class="block text-sm font-medium text-gray-700 mb-2">Ulasan</label>
                                        <textarea id="review" name="review" rows="3" required
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="Bagikan pengalaman Anda dengan produk ini..."></textarea>
                                    </div>
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                        Kirim Ulasan
                                    </button>
                                </form>
                            </div>
                        @elseif($hasReviewed)
                            <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                                <p class="text-sm text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Anda sudah memberikan ulasan untuk produk ini.
                                </p>
                            </div>
                        @elseif(!$hasPurchased)
                            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Anda dapat memberikan ulasan setelah membeli dan menerima produk ini.
                                </p>
                            </div>
                        @endif
                    @endif
                @endauth

                @if($product->review_count > 0)
                    <!-- Reviews List -->
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
                                <p class="mt-2 text-sm text-gray-700">{{ $review->review }}</p>
                            </div>
                        </div>
                        @endforeach

                        @if($product->review_count > 5)
                        <div class="text-center pt-4">
                            <button onclick="loadMoreReviews()" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                                Lihat semua {{ $product->review_count }} ulasan
                            </button>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="px-6 py-12 text-center">
                        <i class="fas fa-star text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Ulasan</h3>
                        <p class="text-gray-600">Jadilah yang pertama memberikan ulasan untuk produk ini.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <div class="mt-12">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Produk Terkait</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                <div class="bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                        @if($relatedProduct->images->count() > 0)
                        <img src="{{ $relatedProduct->images->first()->imageurl }}"
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
    let currentRating = 0;

    function setRating(rating) {
        currentRating = rating;
        document.getElementById('ratingInput').value = rating;
        
        for (let i = 1; i <= 5; i++) {
            const star = document.getElementById('star-' + i);
            if (i <= rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        }
    }

    function loadMoreReviews() {
        // Implementasi load more reviews via AJAX
        alert('Fitur load more reviews akan diimplementasi');
    }

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