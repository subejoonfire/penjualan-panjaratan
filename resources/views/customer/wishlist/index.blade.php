@extends('layouts.app')

@section('title', 'Wishlist - Produk Favorit')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Wishlist</h1>
            <p class="mt-2 text-gray-600">Produk-produk yang Anda simpan sebagai favorit</p>
        </div>

        @if($wishlists->count() > 0)
        <!-- Wishlist Items -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 gap-6">
            @foreach($wishlists as $wishlist)
            @php $product = $wishlist->product; @endphp
            <div class="bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow flex flex-col h-full">
                <!-- Product Image -->
                <div class="relative aspect-w-1 aspect-h-1 bg-gray-200">
                    @if($product->images->count() > 0)
                    <img src="{{ asset('storage/' . $product->images->first()->image) }}"
                        alt="{{ $product->productname }}" class="w-full h-48 object-cover">
                    @else
                    <div class="w-full h-48 flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-2xl"></i>
                    </div>
                    @endif
                    <!-- Remove from Wishlist Button -->
                    <button onclick="removeFromWishlist({{ $product->id }})" 
                        class="absolute top-2 right-2 p-2 bg-red-600 text-white rounded-full hover:bg-red-700 transition-colors">
                        <i class="fas fa-heart text-sm"></i>
                    </button>
                    <!-- Stock Status -->
                    @if($product->productstock <= 0)
                    <div class="absolute top-2 left-2 bg-red-600 text-white text-xs px-2 py-1 rounded">
                        Habis
                    </div>
                    @elseif($product->productstock <= 10)
                    <div class="absolute top-2 left-2 bg-yellow-600 text-white text-xs px-2 py-1 rounded">
                        Stok Terbatas
                    </div>
                    @endif
                </div>
                <!-- Product Info & Actions -->
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
                            @if($product->productstock > 0)
                            <form action="{{ route('customer.cart.add', $product) }}" method="POST" class="flex-1 add-to-cart-form">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" 
                                    class="w-full bg-blue-600 text-white px-3 py-2 rounded-md text-xs font-medium hover:bg-blue-700 flex items-center justify-center">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </form>
                            @else
                            <button disabled 
                                class="w-full bg-gray-400 text-white px-3 py-2 rounded-md cursor-not-allowed text-xs flex items-center justify-center">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-3">
                        Ditambahkan {{ $wishlist->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($wishlists->hasPages())
        <div class="mt-8">
            {{ $wishlists->links() }}
        </div>
        @endif
        
        @else
        <!-- Empty Wishlist -->
        <div class="text-center py-12">
            <div class="max-w-md mx-auto">
                <i class="fas fa-heart text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Wishlist Kosong</h3>
                <p class="text-gray-600 mb-6">
                    Anda belum menambahkan produk apapun ke wishlist. 
                    Jelajahi produk-produk menarik dan simpan yang Anda sukai!
                </p>
                <a href="{{ route('products.index') }}" 
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-shopping-bag mr-2"></i>
                    Jelajahi Produk
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function removeFromWishlist(productId) {
    confirmAction('Hapus produk dari wishlist?', function() {
        fetch(`/customer/wishlist/remove/${productId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showAlert('Gagal menghapus dari wishlist', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan', 'error');
        });
    });
}

// Handle add to cart forms
document.querySelectorAll('.add-to-cart-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const button = this.querySelector('button[type="submit"]');
        const originalText = button.innerHTML;
        
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menambahkan...';
        
        fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                button.innerHTML = '<i class="fas fa-check mr-2"></i>Ditambahkan!';
                button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                button.classList.add('bg-green-600');
                
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
            console.error('Error:', error);
            showAlert('Terjadi kesalahan', 'error');
            button.innerHTML = originalText;
            button.disabled = false;
        });
    });
});
</script>
@endsection