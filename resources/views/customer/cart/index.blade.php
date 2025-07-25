@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Keranjang</h1>
            <p class="mt-2 text-gray-600">Review your items before checkout</p>
        </div>

        @if($cartDetails->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900">Cart Items ({{ $cartDetails->count() }})</h3>
                                <form action="{{ route('customer.cart.clear') }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-sm text-red-600 hover:text-red-500"
                                            onclick="return confirm('Are you sure you want to clear your cart?')">
                                        Clear Cart
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
                                                <a href="{{ route('products.show', $detail->product) }}" class="hover:text-blue-600">
                                                    {{ $detail->product->productname }}
                                                </a>
                                            </h4>
                                            <p class="text-sm text-gray-600 mt-1">Seller: {{ $detail->product->seller->username }}</p>
                                            <p class="text-lg font-medium text-gray-900 mt-2">Rp {{ number_format($detail->product->price) }}</p>
                                            
                                            <!-- Stock Status -->
                                            @if($detail->product->stock < $detail->quantity)
                                                <p class="text-sm text-red-600 mt-1">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                    Only {{ $detail->product->stock }} left in stock
                                                </p>
                                            @elseif($detail->product->stock < 10)
                                                <p class="text-sm text-yellow-600 mt-1">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                    Low stock: {{ $detail->product->stock }} remaining
                                                </p>
                                            @endif
                                        </div>

                                        <!-- Quantity Controls -->
                                        <div class="flex items-center space-x-3">
                                            <div class="flex items-center border border-gray-300 rounded-md">
                                                <form action="{{ route('customer.cart.update', $detail) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="quantity" value="{{ max(1, $detail->quantity - 1) }}">
                                                    <button type="submit" 
                                                            class="px-3 py-2 text-gray-600 hover:text-gray-800 {{ $detail->quantity <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                            {{ $detail->quantity <= 1 ? 'disabled' : '' }}>
                                                        <i class="fas fa-minus text-sm"></i>
                                                    </button>
                                                </form>
                                                
                                                <span class="px-4 py-2 text-gray-900 font-medium">{{ $detail->quantity }}</span>
                                                
                                                <form action="{{ route('customer.cart.update', $detail) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="quantity" value="{{ $detail->quantity + 1 }}">
                                                    <button type="submit" 
                                                            class="px-3 py-2 text-gray-600 hover:text-gray-800 {{ $detail->quantity >= $detail->product->stock ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                            {{ $detail->quantity >= $detail->product->stock ? 'disabled' : '' }}>
                                                        <i class="fas fa-plus text-sm"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Subtotal -->
                                        <div class="text-right">
                                            <p class="text-lg font-medium text-gray-900">
                                                Rp {{ number_format($detail->quantity * $detail->product->price) }}
                                            </p>
                                        </div>

                                        <!-- Remove Button -->
                                        <div>
                                            <form action="{{ route('customer.cart.remove', $detail) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-500"
                                                        onclick="return confirm('Remove this item from cart?')">
                                                    <i class="fas fa-trash text-sm"></i>
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
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal ({{ $cartDetails->sum('quantity') }} items)</span>
                                <span class="font-medium">Rp {{ number_format($total) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping</span>
                                <span class="font-medium">Rp {{ number_format(15000) }}</span>
                            </div>
                            <div class="border-t pt-3">
                                <div class="flex justify-between">
                                    <span class="text-lg font-medium text-gray-900">Total</span>
                                    <span class="text-lg font-medium text-gray-900">Rp {{ number_format($total + 15000) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            @php
                                $hasOutOfStock = $cartDetails->contains(function($detail) {
                                    return $detail->product->stock < $detail->quantity;
                                });
                                $hasInactiveProducts = $cartDetails->contains(function($detail) {
                                    return !$detail->product->is_active;
                                });
                            @endphp

                            @if($hasOutOfStock || $hasInactiveProducts)
                                <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
                                    <div class="flex">
                                        <i class="fas fa-exclamation-triangle text-red-400 mt-0.5 mr-2"></i>
                                        <div class="text-sm text-red-700">
                                            @if($hasOutOfStock)
                                                <p>Some items in your cart are out of stock or exceed available quantity.</p>
                                            @endif
                                            @if($hasInactiveProducts)
                                                <p>Some products are no longer available.</p>
                                            @endif
                                            <p class="mt-1">Please update your cart before proceeding to checkout.</p>
                                        </div>
                                    </div>
                                </div>
                                <button disabled class="w-full bg-gray-400 text-white py-3 px-4 rounded-md font-medium cursor-not-allowed">
                                    Update Cart Required
                                </button>
                            @else
                                <a href="{{ route('customer.checkout') }}" 
                                   class="w-full bg-blue-600 text-white py-3 px-4 rounded-md font-medium hover:bg-blue-700 text-center block">
                                    Pembayaran
                                </a>
                            @endif
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('products.index') }}" 
                               class="w-full bg-gray-100 text-gray-700 py-3 px-4 rounded-md font-medium hover:bg-gray-200 text-center block">
                                Continue Shopping
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
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Your cart is empty</h3>
                    <p class="text-gray-600 mb-6">Start adding some products to your cart!</p>
                    <a href="{{ route('products.index') }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i>
                        Browse Products
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
// Auto-update quantity on input change
document.addEventListener('DOMContentLoaded', function() {
    const quantityInputs = document.querySelectorAll('input[name="quantity"]');
    
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const form = this.closest('form');
            if (form) {
                form.submit();
            }
        });
    });
});
</script>
@endsection