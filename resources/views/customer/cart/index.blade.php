@extends('layouts.app')

@section('title', 'Keranjang Belanja - Penjualan Panjaratan')

@section('content')
<div class="py-3 sm:py-6">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Keranjang Belanja</h1>
            <p class="mt-1 sm:mt-2 text-sm sm:text-base text-gray-600">Tinjau item Anda sebelum checkout</p>
        </div>

        @if($cartDetails->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-3 sm:px-4 py-2 sm:py-3 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <h3 class="text-sm sm:text-base md:text-lg font-medium text-gray-900">Item Keranjang ({{
                                $cartDetails->count() }})
                            </h3>
                            <div>
                                <button type="button" class="text-xs sm:text-sm text-red-600 hover:text-red-500"
                                    onclick="confirmAction('Apakah Anda yakin ingin mengosongkan keranjang?', function() { document.getElementById('clearCartForm').submit(); })">
                                    Kosongkan Keranjang
                                </button>
                                <form id="clearCartForm" action="{{ route('customer.cart.clear') }}" method="POST"
                                    class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @foreach($cartDetails as $detail)
                        <div class="p-3 sm:p-4">
                            <!-- Mobile Layout -->
                            <div class="block sm:hidden">
                                <div class="flex items-start space-x-2">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0">
                                        @if($detail->product->images->count() > 0)
                                        <img src="{{ asset('storage/' . $detail->product->images->first()->image) }}"
                                            alt="{{ $detail->product->productname }}"
                                            class="w-12 h-12 rounded-lg object-cover">
                                        @else
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400 text-xs"></i>
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Product Details -->
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-xs font-medium text-gray-900 mb-1 leading-tight">
                                            <a href="{{ route('products.show', $detail->product) }}"
                                                class="hover:text-blue-600">
                                                {{ Str::limit($detail->product->productname, 30) }}
                                            </a>
                                        </h4>
                                        <p class="text-xs text-gray-600">oleh {{ $detail->product->seller->nickname ??
                                            $detail->product->seller->username }}</p>
                                        <p class="text-xs text-gray-500">{{ $detail->product->category->category }}</p>

                                        <div class="mt-1 flex items-center justify-between">
                                            <span class="text-sm font-bold text-blue-600">
                                                Rp {{ number_format($detail->productprice) }}
                                            </span>
                                            @if($detail->product->productstock < $detail->quantity)
                                                <span class="text-xs text-red-600 font-medium">
                                                    Stok habis
                                                </span>
                                                @endif
                                        </div>

                                        <!-- Quantity & Actions -->
                                        <div class="mt-2 flex items-center justify-between">
                                            <form action="{{ route('customer.cart.update', $detail) }}" method="POST"
                                                class="flex items-center space-x-1">
                                                @csrf
                                                @method('PUT')
                                                <button type="button" onclick="decreaseQuantity(this)"
                                                    class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">
                                                    <i class="fas fa-minus text-xs"></i>
                                                </button>
                                                <input type="number" name="quantity" value="{{ $detail->quantity }}"
                                                    min="1" max="{{ $detail->product->productstock }}"
                                                    class="w-8 text-center border-gray-300 rounded text-xs"
                                                    onchange="this.form.submit()">
                                                <button type="button" onclick="increaseQuantity(this)"
                                                    class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">
                                                    <i class="fas fa-plus text-xs"></i>
                                                </button>
                                            </form>

                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs font-medium text-gray-900">
                                                    Rp {{ number_format($detail->quantity * $detail->productprice) }}
                                                </span>
                                                <button type="button" class="text-red-600 hover:text-red-700 p-1"
                                                    onclick="confirmAction('Hapus item ini dari keranjang?', function() { document.getElementById('removeItemForm{{ $detail->id }}').submit(); })">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Desktop Layout -->
                            <div class="hidden sm:flex sm:items-center gap-4">
                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    @if($detail->product->images->count() > 0)
                                    <img src="{{ asset('storage/' . $detail->product->images->first()->image) }}"
                                        alt="{{ $detail->product->productname }}"
                                        class="w-20 h-20 md:w-24 md:h-24 rounded-lg object-cover">
                                    @else
                                    <div class="w-20 h-20 md:w-24 md:h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 text-lg md:text-xl"></i>
                                    </div>
                                    @endif
                                </div>
                                <!-- Product Details -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-base md:text-lg font-medium text-gray-900 truncate">
                                        <a href="{{ route('products.show', $detail->product) }}"
                                            class="hover:text-blue-600">
                                            {{ $detail->product->productname }}
                                        </a>
                                    </h4>
                                    <p class="text-sm text-gray-600 truncate">oleh {{ $detail->product->seller->nickname
                                        ?? $detail->product->seller->username }}</p>
                                    <p class="text-sm text-gray-500 truncate">{{ $detail->product->category->category }}
                                    </p>

                                    <div class="mt-2 flex flex-row items-center gap-4">
                                        <span class="text-base md:text-lg font-bold text-blue-600">
                                            Rp {{ number_format($detail->productprice) }}
                                        </span>
                                        @if($detail->product->productstock < $detail->quantity)
                                            <span class="text-sm text-red-600 font-medium">
                                                Stok tidak mencukupi (Tersedia: {{ $detail->product->productstock }})
                                            </span>
                                            @endif
                                    </div>
                                </div>
                                <!-- Quantity Controls & Subtotal -->
                                <div class="flex flex-col items-center gap-3">
                                    <form action="{{ route('customer.cart.update', $detail) }}" method="POST"
                                        class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <button type="button" onclick="decreaseQuantity(this)"
                                            class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">
                                            <i class="fas fa-minus text-sm"></i>
                                        </button>
                                        <input type="number" name="quantity" value="{{ $detail->quantity }}" min="1"
                                            max="{{ $detail->product->productstock }}"
                                            class="w-12 text-center border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500 text-sm"
                                            onchange="this.form.submit()">
                                        <button type="button" onclick="increaseQuantity(this)"
                                            class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">
                                            <i class="fas fa-plus text-sm"></i>
                                        </button>
                                    </form>
                                    <div class="text-right min-w-[80px]">
                                        <p class="text-base md:text-lg font-medium text-gray-900">
                                            Rp {{ number_format($detail->quantity * $detail->productprice) }}
                                        </p>
                                    </div>
                                </div>
                                <!-- Remove Button -->
                                <div class="flex justify-end">
                                    <button type="button" class="text-red-600 hover:text-red-700 p-2"
                                        onclick="confirmAction('Hapus item ini dari keranjang?', function() { document.getElementById('removeItemForm{{ $detail->id }}').submit(); })">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <form id="removeItemForm{{ $detail->id }}"
                                action="{{ route('customer.cart.remove', $detail) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg p-4 sm:p-6 sticky top-6">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-3 sm:mb-4">Ringkasan Pesanan</h3>

                    <div class="space-y-2 sm:space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Subtotal ({{ $cartDetails->count() }} produk)</span>
                            <span class="text-sm font-medium">Rp {{ number_format($subtotal) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Ongkos Kirim</span>
                            <span class="text-sm font-medium">Rp {{ number_format($shippingCost) }}</span>
                        </div>

                        @if($tax > 0)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Pajak</span>
                            <span class="text-sm font-medium">Rp {{ number_format($tax) }}</span>
                        </div>
                        @endif

                        <div class="border-t pt-2 sm:pt-3">
                            <div class="flex justify-between">
                                <span class="text-base sm:text-lg font-medium text-gray-900">Total</span>
                                <span class="text-base sm:text-lg font-bold text-blue-600">Rp {{ number_format($total) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 sm:mt-6 space-y-2 sm:space-y-3">
                        @php
                        $hasInsufficientStock = $cartDetails->contains(function($detail) {
                        return $detail->product->productstock < $detail->quantity;
                            });
                            @endphp

                            @if($hasInsufficientStock)
                            <div class="bg-red-50 border border-red-200 rounded-md p-3">
                                <p class="text-xs sm:text-sm text-red-600">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Beberapa item memiliki stok tidak mencukupi. Silakan sesuaikan kuantitas.
                                </p>
                            </div>
                            @else
                            <a href="{{ route('customer.checkout') }}"
                                class="w-full bg-blue-600 text-white py-2.5 sm:py-3 px-4 rounded-md text-sm sm:text-base font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-center block">
                                Lanjutkan ke Checkout
                            </a>
                            @endif

                            <a href="{{ route('products.index') }}"
                                class="w-full bg-gray-100 text-gray-700 py-2.5 sm:py-3 px-4 rounded-md text-sm sm:text-base font-medium hover:bg-gray-200 text-center block">
                                Lanjut Belanja
                            </a>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Empty Cart -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 sm:px-6 py-8 sm:py-12 text-center">
                <i class="fas fa-shopping-cart text-gray-400 text-4xl sm:text-6xl mb-4"></i>
                <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">Keranjang Anda Kosong</h3>
                <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">Sepertinya Anda belum menambahkan item apapun ke keranjang.</p>
                <a href="{{ route('products.index') }}"
                    class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 border border-transparent text-sm sm:text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-shopping-bag mr-2"></i>
                    Mulai Berbelanja
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    function increaseQuantity(button) {
    const input = button.previousElementSibling;
    const max = parseInt(input.getAttribute('max'));
    const current = parseInt(input.value);
    
    if (current < max) {
        input.value = current + 1;
        input.form.submit();
    }
}

function decreaseQuantity(button) {
    const input = button.nextElementSibling;
    const current = parseInt(input.value);
    
    if (current > 1) {
        input.value = current - 1;
        input.form.submit();
    }
}

// Refresh cart count after any cart action
function refreshCartCount() {
    fetch('{{ route('api.cart.count') }}')
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

// Refresh cart count after form submissions
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[action*="cart"]');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            setTimeout(refreshCartCount, 1000); // Refresh after 1 second
        });
    });
});
</script>
@endsection