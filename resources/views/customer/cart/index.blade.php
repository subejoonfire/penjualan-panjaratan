@extends('layouts.app')

@section('title', 'Keranjang Belanja - Penjualan Panjaratan')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Keranjang Belanja</h1>
            <p class="mt-2 text-gray-600">Tinjau item Anda sebelum checkout</p>
        </div>

        @if($cartDetails->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Item Keranjang ({{ $cartDetails->count() }})
                            </h3>
                            <form action="{{ route('customer.cart.clear') }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-500"
                                    onclick="return confirm('Apakah Anda yakin ingin mengosongkan keranjang?')">
                                    Kosongkan Keranjang
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @foreach($cartDetails as $detail)
                        <div class="px-6 py-6">
                            <div class="flex items-center space-x-4">
                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    @if($detail->product->images->count() > 0)
                                    <img src="{{ asset('storage/' . $detail->product->images->first()->imageurl) }}"
                                        alt="{{ $detail->product->productname }}"
                                        class="w-24 h-24 rounded-lg object-cover">
                                    @else
                                    <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 text-xl"></i>
                                    </div>
                                    @endif
                                </div>

                                <!-- Product Details -->
                                <div class="flex-1">
                                    <h4 class="text-lg font-medium text-gray-900">
                                        <a href="{{ route('products.show', $detail->product) }}"
                                            class="hover:text-blue-600">
                                            {{ $detail->product->productname }}
                                        </a>
                                    </h4>
                                    <p class="text-sm text-gray-600">oleh {{ $detail->product->seller->username }}</p>
                                    <p class="text-sm text-gray-500">{{ $detail->product->category->category }}</p>

                                    <div class="mt-2 flex items-center space-x-4">
                                        <span class="text-lg font-bold text-blue-600">
                                            Rp {{ number_format($detail->product->productprice) }}
                                        </span>
                                        @if($detail->product->productstock < $detail->quantity)
                                            <span class="text-sm text-red-600 font-medium">
                                                Stok tidak mencukupi (Tersedia: {{ $detail->product->productstock }})
                                            </span>
                                            @endif
                                    </div>
                                </div>

                                <!-- Quantity Controls -->
                                <div class="flex items-center space-x-3">
                                    <form action="{{ route('customer.cart.update', $detail) }}" method="POST"
                                        class="flex items-center space-x-2">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" onclick="decreaseQuantity(this)"
                                            class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">
                                            <i class="fas fa-minus text-xs"></i>
                                        </button>
                                        <input type="number" name="quantity" value="{{ $detail->quantity }}" min="1"
                                            max="{{ $detail->product->productstock }}"
                                            class="w-16 text-center border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"
                                            onchange="this.form.submit()">
                                        <button type="button" onclick="increaseQuantity(this)"
                                            class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">
                                            <i class="fas fa-plus text-xs"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Subtotal -->
                                <div class="text-right">
                                    <p class="text-lg font-medium text-gray-900">
                                        Rp {{ number_format($detail->quantity * $detail->product->productprice) }}
                                    </p>
                                </div>

                                <!-- Remove Button -->
                                <div>
                                    <form action="{{ route('customer.cart.remove', $detail) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700 p-2"
                                            onclick="return confirm('Hapus item ini dari keranjang?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg p-6 sticky top-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Pesanan</h3>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal ({{ $cartDetails->sum('quantity') }} item)</span>
                            <span class="font-medium">Rp {{ number_format($subtotal) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-600">Ongkos Kirim</span>
                            <span class="font-medium">Rp {{ number_format($shippingCost) }}</span>
                        </div>

                        @if($tax > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pajak</span>
                            <span class="font-medium">Rp {{ number_format($tax) }}</span>
                        </div>
                        @endif

                        <div class="border-t pt-3">
                            <div class="flex justify-between">
                                <span class="text-lg font-medium text-gray-900">Total</span>
                                <span class="text-lg font-bold text-blue-600">Rp {{ number_format($total) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        @php
                        $hasInsufficientStock = $cartDetails->contains(function($detail) {
                        return $detail->product->productstock < $detail->quantity;
                            });
                            @endphp

                            @if($hasInsufficientStock)
                            <div class="bg-red-50 border border-red-200 rounded-md p-3">
                                <p class="text-sm text-red-600">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Beberapa item memiliki stok tidak mencukupi. Silakan sesuaikan kuantitas.
                                </p>
                            </div>
                            @else
                            <a href="{{ route('customer.checkout') }}"
                                class="w-full bg-blue-600 text-white py-3 px-4 rounded-md font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-center block">
                                Lanjutkan ke Checkout
                            </a>
                            @endif

                            <a href="{{ route('products.index') }}"
                                class="w-full bg-gray-100 text-gray-700 py-3 px-4 rounded-md font-medium hover:bg-gray-200 text-center block">
                                Lanjut Belanja
                            </a>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Empty Cart -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-12 text-center">
                <i class="fas fa-shopping-cart text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Keranjang Anda Kosong</h3>
                <p class="text-gray-600 mb-6">Sepertinya Anda belum menambahkan item apapun ke keranjang.</p>
                <a href="{{ route('products.index') }}"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
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
</script>
@endsection