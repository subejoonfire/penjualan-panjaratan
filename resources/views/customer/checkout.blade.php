@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
<div class="py-3 sm:py-6">
    <div class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Pembayaran</h1>
            <p class="mt-1 sm:mt-2 text-sm sm:text-base text-gray-600">Review your order and complete your purchase</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-8">

            <!-- Order Details & Shipping -->
            <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                <!-- Order Items -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                        <h3 class="text-base sm:text-lg font-medium text-gray-900">Order Items ({{ $cartDetails->count() }})</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($cartDetails as $detail)
                        <div class="px-3 sm:px-6 py-3 sm:py-4">
                            <div class="flex items-center space-x-3 sm:space-x-4">
                                <div class="flex-shrink-0">
                                    @if($detail->product->images->count() > 0)
                                    <img src="{{ asset('storage/' . $detail->product->images->first()->image) }}"
                                        alt="{{ $detail->product->productname }}"
                                        class="w-12 h-12 sm:w-16 sm:h-16 rounded-lg object-cover">
                                    @else
                                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 text-sm sm:text-base"></i>
                                    </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 truncate">{{ $detail->product->productname }}
                                    </h4>
                                    <p class="text-xs sm:text-sm text-gray-600">Seller: {{ $detail->product->seller->nickname ?? $detail->product->seller->username }}
                                    </p>
                                    <p class="text-xs sm:text-sm text-gray-600">Qty: {{ $detail->quantity }} × Rp {{
                                        number_format($detail->productprice) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">
                                        Rp {{ number_format($detail->quantity * $detail->productprice) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                        <h3 class="text-base sm:text-lg font-medium text-gray-900">Alamat Pengiriman</h3>
                    </div>
                    <div class="p-3 sm:p-6">
                        @if($addresses->count() > 0)
                        <div class="space-y-3 sm:space-y-4">
                            @foreach($addresses as $address)
                            <label class="flex items-start space-x-2 sm:space-x-3 cursor-pointer">
                                <input type="radio" name="address_id" value="{{ $address->id }}"
                                    class="mt-0.5 sm:mt-1 text-blue-600 focus:ring-blue-500 border-gray-300" {{ ($defaultAddress
                                    && $defaultAddress->id === $address->id) || (!$defaultAddress && $loop->first) ?
                                'checked' : '' }}>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">
                                        Alamat {{ $loop->iteration }}
                                        @if($address->is_default)
                                        <span
                                            class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            Default
                                        </span>
                                        @endif
                                    </div>
                                    <div class="text-xs sm:text-sm text-gray-600 mt-1">
                                        {{ $address->address }}
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @endif

                        <!-- Manual Address Input -->
                        <div class="mt-4 sm:mt-6">
                            <label class="flex items-start space-x-2 sm:space-x-3 cursor-pointer">
                                <input type="radio" name="address_type" value="manual" id="manual_address"
                                    class="mt-0.5 sm:mt-1 text-blue-600 focus:ring-blue-500 border-gray-300" {{
                                    $addresses->count() === 0 ? 'checked' : '' }}>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">Masukkan alamat baru</div>
                                </div>
                            </label>

                            <div class="mt-3 sm:mt-4 manual-address {{ $addresses->count() > 0 ? 'hidden' : '' }}">
                                <textarea name="shipping_address" rows="3"
                                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Masukkan alamat pengiriman lengkap...">{{ old('shipping_address') }}</textarea>
                                @error('shipping_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                        <h3 class="text-base sm:text-lg font-medium text-gray-900">Metode Pembayaran</h3>
                    </div>
                    <div class="p-3 sm:p-6">
                        <div class="space-y-3 sm:space-y-4">
                            @forelse($paymentMethods as $method)
                                @php
                                    $isQris = stripos($method['paymentName'], 'qris') !== false;
                                    $isEwallet = preg_match('/ovo|dana|linkaja|shopeepay|indodana/i', $method['paymentName']);
                                    $isVA = preg_match('/va|virtual account/i', $method['paymentName']);
                                @endphp
                                <label class="flex items-center space-x-3 cursor-pointer border rounded-lg p-2 hover:border-blue-500 transition mb-2">
                                    <input type="radio" name="payment_method" value="{{ $method['paymentMethod'] }}" class="text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <img src="{{ $method['paymentImage'] }}" alt="{{ $method['paymentName'] }}" class="w-10 h-10 object-contain rounded bg-white border mr-2">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-900">{{ $method['paymentName'] }}</span>
                                            @if($isQris)
                                                <span class="ml-2 px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-700">QRIS</span>
                                            @elseif($isEwallet)
                                                <span class="ml-2 px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700">E-Wallet</span>
                                            @elseif($isVA)
                                                <span class="ml-2 px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700">VA</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">Kode: <span class="font-mono">{{ $method['paymentMethod'] }}</span></div>
                                    </div>
                                </label>
                            @empty
                                <div class="text-red-500 text-sm">Gagal memuat metode pembayaran</div>
                            @endforelse
                        </div>
                        @error('payment_method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                        <h3 class="text-base sm:text-lg font-medium text-gray-900">Catatan Pesanan (Opsional)</h3>
                    </div>
                    <div class="p-3 sm:p-6">
                        <textarea name="notes" rows="3"
                            class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Instruksi khusus untuk pesanan Anda...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg p-4 sm:p-6 sticky top-6">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-3 sm:mb-4">Ringkasan Pesanan</h3>

                    <div class="space-y-2 sm:space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Subtotal ({{ $cartDetails->sum('quantity') }} item)</span>
                            <span class="text-sm font-medium">Rp {{ number_format($subtotal) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Ongkos Kirim</span>
                            <span class="text-sm font-medium">Rp {{ number_format($shippingCost) }}</span>
                        </div>
                        <div class="border-t pt-2 sm:pt-3">
                            <div class="flex justify-between">
                                <span class="text-base sm:text-lg font-medium text-gray-900">Total</span>
                                <span class="text-base sm:text-lg font-medium text-gray-900">Rp {{ number_format($total) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 sm:mt-6">
                        <button type="button" onclick="processCheckout()"
                            class="w-full bg-blue-600 text-white py-2.5 sm:py-3 px-4 rounded-md text-sm sm:text-base font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-credit-card mr-2"></i>
                            Buat Pesanan
                        </button>
                    </div>

                    <div class="mt-3 sm:mt-4">
                        <a href="{{ route('customer.cart.index') }}"
                            class="w-full bg-gray-100 text-gray-700 py-2.5 sm:py-3 px-4 rounded-md text-sm sm:text-base font-medium hover:bg-gray-200 text-center block">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Keranjang
                        </a>
                    </div>

                    <!-- Security Info -->
                    <div class="mt-4 sm:mt-6 p-3 sm:p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                            <span class="text-sm text-gray-600">Checkout aman</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Informasi pembayaran Anda dienkripsi dan aman.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle address selection
        const addressRadios = document.querySelectorAll('input[name="address_id"]');
        const manualRadio = document.querySelector('input[name="address_type"][value="manual"]');
        const manualAddressDiv = document.querySelector('.manual-address');
        const shippingAddressTextarea = document.querySelector('textarea[name="shipping_address"]');
        
        // Show/hide manual address input
        function toggleManualAddress() {
            if (manualRadio && manualRadio.checked) {
                manualAddressDiv.classList.remove('hidden');
                shippingAddressTextarea.required = true;
                // Clear address_id selection
                addressRadios.forEach(radio => radio.checked = false);
            } else {
                manualAddressDiv.classList.add('hidden');
                shippingAddressTextarea.required = false;
            }
        }
        
        // Handle saved address selection
        addressRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    manualRadio.checked = false;
                    toggleManualAddress();
                }
            });
        });
        
        // Handle manual address selection
        if (manualRadio) {
            manualRadio.addEventListener('change', toggleManualAddress);
        }
        
        // Initial state
        toggleManualAddress();

        // Hapus/freeze JS fetch payment method
    });

    function processCheckout() {
        const button = event.target;
        const originalText = button.innerHTML;
        
        // Disable button and show loading
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
        
        // Get form data
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Get selected address
        const selectedAddress = document.querySelector('input[name="address_id"]:checked');
        if (selectedAddress) {
            formData.append('address_id', selectedAddress.value);
        }
        
        // Get manual address
        const manualAddress = document.querySelector('textarea[name="shipping_address"]');
        if (manualAddress && manualAddress.value.trim()) {
            formData.append('shipping_address', manualAddress.value.trim());
        }
        
        // Get notes
        const notes = document.querySelector('textarea[name="notes"]');
        if (notes && notes.value.trim()) {
            formData.append('notes', notes.value.trim());
        }
        
        fetch(`${window.location.origin}/customer/checkout`, {
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
                    message: data.message || 'Pesanan berhasil dibuat',
                    confirmText: 'OK',
                    showCancel: false,
                    onConfirm: () => {
                        window.location.href = data.redirect_url || '/customer/orders';
                    }
                });
            } else {
                showModalNotification({
                    type: 'error',
                    title: 'Gagal!',
                    message: data.message || 'Gagal membuat pesanan',
                    confirmText: 'OK',
                    showCancel: false
                });
                button.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Checkout error:', error);
            showModalNotification({
                type: 'error',
                title: 'Error!',
                message: 'Terjadi kesalahan saat memproses checkout',
                confirmText: 'OK',
                showCancel: false
            });
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
</script>
@endsection