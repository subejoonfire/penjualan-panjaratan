@extends('layouts.app')

@section('title', 'Checkout Langsung - ' . $product->productname)

@section('content')
<div class="py-3 sm:py-6">
    <div class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Checkout Langsung</h1>
            <p class="mt-1 sm:mt-2 text-sm sm:text-base text-gray-600">Beli produk ini langsung tanpa masuk ke keranjang</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-8">
            <!-- Product Details & Shipping -->
            <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                <!-- Product Item -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                        <h3 class="text-base sm:text-lg font-medium text-gray-900">Detail Produk</h3>
                    </div>
                    <div class="p-3 sm:p-6">
                        <div class="flex items-center space-x-3 sm:space-x-4">
                            <div class="flex-shrink-0">
                                @if($product->images->count() > 0)
                                <img src="{{ asset('storage/' . $product->images->first()->image) }}"
                                    alt="{{ $product->productname }}"
                                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg object-cover">
                                @else
                                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-sm sm:text-base"></i>
                                </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 truncate">{{ $product->productname }}</h4>
                                <p class="text-xs sm:text-sm text-gray-600">Seller: {{ $product->seller->nickname ?? $product->seller->username }}</p>
                                <p class="text-xs sm:text-sm text-gray-600">Stok: {{ $product->productstock }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">Rp {{ number_format($product->productprice) }}</p>
                            </div>
                        </div>
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

                            <div id="manual_address_input" class="mt-3 sm:mt-4 {{ $addresses->count() === 0 ? '' : 'hidden' }}">
                                <textarea name="manual_address" rows="3"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    placeholder="Masukkan alamat lengkap pengiriman..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                        <h3 class="text-base sm:text-lg font-medium text-gray-900">Catatan Pesanan (Opsional)</h3>
                    </div>
                    <div class="p-3 sm:p-6">
                        <textarea name="notes" rows="3"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="Tambahkan catatan khusus untuk pesanan ini..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg p-4 sm:p-6 sticky top-6">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-3 sm:mb-4">Ringkasan Pesanan</h3>

                    <!-- Quantity Selector -->
                    <div class="mb-4 sm:mb-6">
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                        <div class="flex items-center space-x-2">
                            <button type="button" onclick="decreaseQuantity()"
                                class="p-2 border border-gray-300 rounded-md hover:bg-gray-50">
                                <i class="fas fa-minus text-sm"></i>
                            </button>
                            <input type="number" id="quantity" name="quantity" min="1" max="{{ $product->productstock }}" value="{{ $quantity }}"
                                class="w-20 text-center border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <button type="button" onclick="increaseQuantity()"
                                class="p-2 border border-gray-300 rounded-md hover:bg-gray-50">
                                <i class="fas fa-plus text-sm"></i>
                            </button>
                            <span class="text-xs text-gray-500">Max: {{ $product->productstock }}</span>
                        </div>
                    </div>

                    <div class="space-y-2 sm:space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Subtotal</span>
                            <span class="text-sm font-medium" id="subtotal">Rp {{ number_format($product->productprice * $quantity) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Ongkos Kirim</span>
                            <span class="text-sm font-medium">Rp 15.000</span>
                        </div>

                        <div class="border-t pt-2 sm:pt-3">
                            <div class="flex justify-between">
                                <span class="text-base sm:text-lg font-medium text-gray-900">Total</span>
                                <span class="text-base sm:text-lg font-bold text-blue-600" id="total">Rp {{ number_format(($product->productprice * $quantity) + 15000) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 sm:mt-6">
                        <button type="button" onclick="processDirectCheckout()"
                            class="w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 font-medium">
                            <i class="fas fa-credit-card mr-2"></i>
                            Buat Pesanan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function decreaseQuantity() {
        const input = document.getElementById('quantity');
        const current = parseInt(input.value);
        if (current > 1) {
            input.value = current - 1;
            updateTotals();
        }
    }

    function increaseQuantity() {
        const input = document.getElementById('quantity');
        const max = parseInt(input.getAttribute('max'));
        const current = parseInt(input.value);
        if (current < max) {
            input.value = current + 1;
            updateTotals();
        }
    }

    function updateTotals() {
        const quantity = parseInt(document.getElementById('quantity').value);
        const price = {{ $product->productprice }};
        const subtotal = quantity * price;
        const shipping = 15000;
        const total = subtotal + shipping;

        document.getElementById('subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
        document.getElementById('total').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    // Manual address toggle
    document.addEventListener('DOMContentLoaded', function() {
        const manualRadio = document.getElementById('manual_address');
        const manualInput = document.getElementById('manual_address_input');
        const addressRadios = document.querySelectorAll('input[name="address_id"]');

        function toggleManualInput() {
            if (manualRadio.checked) {
                manualInput.classList.remove('hidden');
            } else {
                manualInput.classList.add('hidden');
            }
        }

        manualRadio.addEventListener('change', toggleManualInput);
        addressRadios.forEach(radio => {
            radio.addEventListener('change', toggleManualInput);
        });

        // Initial state
        toggleManualInput();
    });

    function processDirectCheckout() {
        const quantity = document.getElementById('quantity').value;
        const addressId = document.querySelector('input[name="address_id"]:checked')?.value;
        const manualAddress = document.querySelector('textarea[name="manual_address"]')?.value;
        const notes = document.querySelector('textarea[name="notes"]')?.value;

        if (!addressId && !manualAddress) {
            showModalNotification({
                type: 'error',
                title: 'Error!',
                message: 'Silakan pilih alamat pengiriman atau masukkan alamat manual',
                confirmText: 'OK',
                showCancel: false
            });
            return;
        }

        const formData = new FormData();
        formData.append('quantity', quantity);
        if (addressId) formData.append('address_id', addressId);
        if (manualAddress) formData.append('manual_address', manualAddress);
        if (notes) formData.append('notes', notes);

        fetch(`${window.location.origin}/customer/direct-checkout/{{ $product->id }}`, {
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
                        window.location.href = data.redirect_url;
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
            }
        })
        .catch(error => {
            console.error('Checkout error:', error);
            showModalNotification({
                type: 'error',
                title: 'Error!',
                message: 'Terjadi kesalahan saat membuat pesanan',
                confirmText: 'OK',
                showCancel: false
            });
        });
    }
</script>
@endsection