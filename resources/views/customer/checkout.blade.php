@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
<div class="py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Pembayaran</h1>
            <p class="mt-2 text-gray-600">Periksa pesanan Anda dan selesaikan pembayaran</p>
        </div>

        <form action="{{ route('customer.checkout.process') }}" method="POST"
            class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf

            <!-- Order Details & Shipping -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Daftar Barang ({{ $cartDetails->count() }})</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($cartDetails as $detail)
                        <div class="px-6 py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    @if($detail->product->images->count() > 0)
                                    <img src="{{ url('storage/' . $detail->product->images->first()->imageurl) }}"
                                        alt="{{ $detail->product->productname }}"
                                        class="w-16 h-16 rounded-lg object-cover">
                                    @else
                                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $detail->product->productname }}
                                    </h4>
                                    <p class="text-sm text-gray-600">Penjual: {{ $detail->product->seller->username }}
                                    </p>
                                    <p class="text-sm text-gray-600">Jumlah: {{ $detail->quantity }} × Rp {{
                                        number_format($detail->product->productprice) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">
                                        Rp {{ number_format($detail->quantity * $detail->product->productprice) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Alamat Pengiriman</h3>
                    </div>
                    <div class="p-6">
                        @if($addresses->count() > 0)
                        <div class="space-y-4">
                            @foreach($addresses as $address)
                            <label class="flex items-start space-x-3 cursor-pointer">
                                <input type="radio" name="address_id" value="{{ $address->id }}"
                                    class="mt-1 text-blue-600 focus:ring-blue-500 border-gray-300" {{ ($defaultAddress
                                    && $defaultAddress->id === $address->id) || (!$defaultAddress && $loop->first) ?
                                'checked' : '' }}>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $address->address_label ?? 'Alamat ' . $loop->iteration }}
                                        @if($address->is_default)
                                        <span
                                            class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            Default
                                        </span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-600 mt-1">
                                        {{ $address->address }}<br>
                                        {{ $address->city }}, {{ $address->postal_code }}
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @endif

                        <!-- Manual Address Input -->
                        <div class="mt-6">
                            <label class="flex items-start space-x-3 cursor-pointer">
                                <input type="radio" name="address_type" value="manual"
                                    class="mt-1 text-blue-600 focus:ring-blue-500 border-gray-300" {{
                                    $addresses->count() === 0 ? 'checked' : '' }}>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">Masukkan alamat baru</div>
                                </div>
                            </label>

                            <div class="mt-4 manual-address {{ $addresses->count() > 0 ? 'hidden' : '' }}">
                                <textarea name="shipping_address" rows="3"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Masukkan alamat lengkap pengiriman...">{{ old('shipping_address') }}</textarea>
                                @error('shipping_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Metode Pembayaran</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="radio" name="payment_method" value="transfer"
                                    class="text-blue-600 focus:ring-blue-500 border-gray-300" checked>
                                <div class="flex items-center">
                                    <i class="fas fa-university text-blue-600 text-lg mr-3"></i>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Transfer Bank</div>
                                        <div class="text-sm text-gray-600">Transfer ke rekening bank kami</div>
                                    </div>
                                </div>
                            </label>

                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="radio" name="payment_method" value="cod"
                                    class="text-blue-600 focus:ring-blue-500 border-gray-300">
                                <div class="flex items-center">
                                    <i class="fas fa-money-bill-wave text-green-600 text-lg mr-3"></i>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Bayar di Tempat (COD)</div>
                                        <div class="text-sm text-gray-600">Bayar saat pesanan diterima</div>
                                    </div>
                                </div>
                            </label>

                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="radio" name="payment_method" value="ewallet"
                                    class="text-blue-600 focus:ring-blue-500 border-gray-300">
                                <div class="flex items-center">
                                    <i class="fas fa-mobile-alt text-purple-600 text-lg mr-3"></i>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Dompet Digital</div>
                                        <div class="text-sm text-gray-600">OVO, GoPay, DANA, dll.</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('payment_method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Catatan Pesanan (Opsional)</h3>
                    </div>
                    <div class="p-6">
                        <textarea name="notes" rows="3"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Instruksi khusus untuk pesanan Anda...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg p-6 sticky top-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Pesanan</h3>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal ({{ $cartDetails->sum('quantity') }} barang)</span>
                            <span class="font-medium">Rp {{ number_format($subtotal) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ongkos Kirim</span>
                            <span class="font-medium">Rp {{ number_format($shippingCost) }}</span>
                        </div>
                        <div class="border-t pt-3">
                            <div class="flex justify-between">
                                <span class="text-lg font-medium text-gray-900">Total</span>
                                <span class="text-lg font-medium text-gray-900">Rp {{ number_format($total) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit"
                            class="w-full bg-blue-600 text-white py-3 px-4 rounded-md font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-credit-card mr-2"></i>
                            Buat Pesanan
                        </button>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('customer.cart.index') }}"
                            class="w-full bg-gray-100 text-gray-700 py-3 px-4 rounded-md font-medium hover:bg-gray-200 text-center block">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Keranjang
                        </a>
                    </div>

                    <!-- Security Info -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                            <span class="text-sm text-gray-600">Pembayaran Aman</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Informasi pembayaran Anda terenkripsi dan aman.
                        </p>
                    </div>
                </div>
            </div>
        </form>
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
});
</script>
@endsection