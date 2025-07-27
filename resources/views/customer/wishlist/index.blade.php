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
            <div class="bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
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
                
                <!-- Product Info -->
                <div class="p-4">
                    <h3 class="text-sm font-medium text-gray-900 truncate mb-2">
                        <a href="{{ route('products.show', $product) }}" class="hover:text-blue-600">
                            {{ $product->productname }}
                        </a>
                    </h3>
                    
                    <p class="text-lg font-bold text-blue-600 mb-2">
                        Rp {{ number_format($product->productprice) }}
                    </p>
                    
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-3">
                        <span>{{ $product->seller->nickname ?? $product->seller->username }}</span>
                        <span>{{ $product->category->category }}</span>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="space-y-2">
                        @if($product->productstock > 0)
                        <form action="{{ route('customer.cart.add', $product) }}" method="POST" class="add-to-cart-form">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" 
                                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors text-sm">
                                <i class="fas fa-shopping-cart mr-2"></i>
                                Tambah ke Keranjang
                            </button>
                        </form>
                        @else
                        <button disabled 
                            class="w-full bg-gray-400 text-white py-2 px-4 rounded-md cursor-not-allowed text-sm">
                            Stok Habis
                        </button>
                        @endif
                        
                        <a href="{{ route('products.show', $product) }}" 
                            class="block w-full text-center border border-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-50 transition-colors text-sm">
                            Lihat Detail
                        </a>
                    </div>
                    
                    <!-- Added to Wishlist Date -->
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