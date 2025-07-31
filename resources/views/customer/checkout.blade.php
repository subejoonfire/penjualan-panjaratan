@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="py-3 sm:py-6">
    <div class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Checkout</h1>
            <p class="mt-1 sm:mt-2 text-sm sm:text-base text-gray-600">Review pesanan dan selesaikan pembelian Anda</p>
        </div>

        <form id="checkoutForm" method="POST" action="{{ route('customer.checkout.process') }}">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-8">

                <!-- Order Details & Shipping -->
                <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                    <!-- Order Items -->
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                            <h3 class="text-base sm:text-lg font-medium text-gray-900">Item Pesanan ({{ $cartDetails->count() }})</h3>
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
                                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ $detail->product->productname }}</h4>
                                        <p class="text-xs sm:text-sm text-gray-600">Seller: {{ $detail->product->seller->nickname ?? $detail->product->seller->username }}</p>
                                        <p class="text-xs sm:text-sm text-gray-600">Qty: {{ $detail->quantity }} × Rp {{ number_format($detail->productprice) }}</p>
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
                        <div class="px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-base sm:text-lg font-medium text-gray-900">Alamat Pengiriman</h3>
                            <button type="button" onclick="openAddressModal()" 
                                class="text-sm bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700 transition">
                                <i class="fas fa-plus mr-1"></i>Tambah Alamat
                            </button>
                        </div>
                        <div class="p-3 sm:p-6">
                            <div id="addressList" class="space-y-3 sm:space-y-4">
                                @if($addresses->count() > 0)
                                    @foreach($addresses as $address)
                                    <label class="flex items-start space-x-2 sm:space-x-3 cursor-pointer">
                                        <input type="radio" name="address_id" value="{{ $address->id }}"
                                            class="mt-0.5 sm:mt-1 text-blue-600 focus:ring-blue-500 border-gray-300" 
                                            {{ ($defaultAddress && $defaultAddress->id === $address->id) || (!$defaultAddress && $loop->first) ? 'checked' : '' }}>
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-900">
                                                Alamat {{ $loop->iteration }}
                                                @if($address->is_default)
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
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
                                @else
                                    <div class="text-center py-4">
                                        <p class="text-gray-500 text-sm">Belum ada alamat tersimpan</p>
                                        <button type="button" onclick="openAddressModal()" 
                                            class="mt-2 text-sm text-blue-600 hover:text-blue-700">
                                            Tambah alamat pertama
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <!-- Manual Address Input -->
                            <div class="mt-4 sm:mt-6">
                                <label class="flex items-start space-x-2 sm:space-x-3 cursor-pointer">
                                    <input type="radio" name="address_type" value="manual" id="manual_address"
                                        class="mt-0.5 sm:mt-1 text-blue-600 focus:ring-blue-500 border-gray-300" 
                                        {{ $addresses->count() === 0 ? 'checked' : '' }}>
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
                                <label class="flex items-center space-x-3 cursor-pointer border rounded-lg p-2 hover:border-blue-500 transition">
                                    <input type="radio" name="payment_method" value="bank_transfer" class="text-blue-600 focus:ring-blue-500 border-gray-300" checked>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900">Transfer Bank</div>
                                        <div class="text-xs text-gray-500">Transfer melalui bank</div>
                                    </div>
                                </label>
                                
                                <label class="flex items-center space-x-3 cursor-pointer border rounded-lg p-2 hover:border-blue-500 transition">
                                    <input type="radio" name="payment_method" value="credit_card" class="text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900">Kartu Kredit</div>
                                        <div class="text-xs text-gray-500">Visa, Mastercard, dll</div>
                                    </div>
                                </label>
                                
                                <label class="flex items-center space-x-3 cursor-pointer border rounded-lg p-2 hover:border-blue-500 transition">
                                    <input type="radio" name="payment_method" value="e_wallet" class="text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900">E-Wallet</div>
                                        <div class="text-xs text-gray-500">OVO, DANA, GoPay, dll</div>
                                    </div>
                                </label>
                                
                                <label class="flex items-center space-x-3 cursor-pointer border rounded-lg p-2 hover:border-blue-500 transition">
                                    <input type="radio" name="payment_method" value="cod" class="text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900">Cash on Delivery (COD)</div>
                                        <div class="text-xs text-gray-500">Bayar saat barang diterima</div>
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
                            <button type="submit" id="checkoutBtn"
                                class="w-full bg-blue-600 text-white py-2.5 sm:py-3 px-4 rounded-md text-sm sm:text-base font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-credit-card mr-2"></i>
                                <span id="checkoutBtnText">Buat Pesanan</span>
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
        </form>
    </div>
</div>

<!-- Address Modal -->
<div id="addressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tambah Alamat Baru</h3>
                <button type="button" onclick="closeAddressModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="addressForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                    <textarea name="address" rows="4" required
                        class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Masukkan alamat lengkap..."></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_default" class="text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Set sebagai alamat default</span>
                    </label>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAddressModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        Simpan Alamat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Notification Modal -->
<div id="notificationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div id="notificationIcon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4">
                <i id="notificationIconClass" class="text-2xl"></i>
            </div>
            <h3 id="notificationTitle" class="text-lg font-medium text-gray-900 mb-2"></h3>
            <p id="notificationMessage" class="text-sm text-gray-500 mb-4"></p>
            <div class="flex justify-center">
                <button id="notificationConfirmBtn" onclick="closeNotificationModal()"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    OK
                </button>
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

        // Handle form submission
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();
            processCheckout();
        });

        // Handle address form submission
        document.getElementById('addressForm').addEventListener('submit', function(e) {
            e.preventDefault();
            saveAddress();
        });
    });

    function openAddressModal() {
        document.getElementById('addressModal').classList.remove('hidden');
    }

    function closeAddressModal() {
        document.getElementById('addressModal').classList.add('hidden');
        document.getElementById('addressForm').reset();
    }

    function saveAddress() {
        const form = document.getElementById('addressForm');
        const formData = new FormData(form);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        fetch('{{ route("customer.addresses.store") }}', {
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
                showNotification('success', 'Berhasil!', data.message);
                closeAddressModal();
                loadAddresses();
            } else {
                showNotification('error', 'Gagal!', data.message);
            }
        })
        .catch(error => {
            console.error('Error saving address:', error);
            showNotification('error', 'Error!', 'Terjadi kesalahan saat menyimpan alamat');
        });
    }

    function loadAddresses() {
        fetch('{{ route("customer.addresses.list") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateAddressList(data.addresses);
            }
        })
        .catch(error => {
            console.error('Error loading addresses:', error);
        });
    }

    function updateAddressList(addresses) {
        const addressList = document.getElementById('addressList');
        if (addresses.length === 0) {
            addressList.innerHTML = `
                <div class="text-center py-4">
                    <p class="text-gray-500 text-sm">Belum ada alamat tersimpan</p>
                    <button type="button" onclick="openAddressModal()" 
                        class="mt-2 text-sm text-blue-600 hover:text-blue-700">
                        Tambah alamat pertama
                    </button>
                </div>
            `;
        } else {
            let html = '';
            addresses.forEach((address, index) => {
                html += `
                    <label class="flex items-start space-x-2 sm:space-x-3 cursor-pointer">
                        <input type="radio" name="address_id" value="${address.id}"
                            class="mt-0.5 sm:mt-1 text-blue-600 focus:ring-blue-500 border-gray-300" 
                            ${address.is_default || index === 0 ? 'checked' : ''}>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">
                                Alamat ${index + 1}
                                ${address.is_default ? '<span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Default</span>' : ''}
                            </div>
                            <div class="text-xs sm:text-sm text-gray-600 mt-1">
                                ${address.address}
                            </div>
                        </div>
                    </label>
                `;
            });
            addressList.innerHTML = html;
        }
    }

    function processCheckout() {
        const button = document.getElementById('checkoutBtn');
        const buttonText = document.getElementById('checkoutBtnText');
        const originalText = buttonText.innerHTML;
        
        // Disable button and show loading
        button.disabled = true;
        buttonText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
        
        // Get form data
        const formData = new FormData(document.getElementById('checkoutForm'));
        
        fetch('{{ route("customer.checkout.process") }}', {
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
                showNotification('success', 'Berhasil!', data.message || 'Pesanan berhasil dibuat', () => {
                    window.location.href = data.redirect_url || '/customer/orders';
                });
            } else {
                showNotification('error', 'Gagal!', data.message || 'Gagal membuat pesanan');
                buttonText.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Checkout error:', error);
            showNotification('error', 'Error!', 'Terjadi kesalahan saat memproses checkout');
            buttonText.innerHTML = originalText;
            button.disabled = false;
        });
    }

    function showNotification(type, title, message, onConfirm = null) {
        const modal = document.getElementById('notificationModal');
        const icon = document.getElementById('notificationIcon');
        const iconClass = document.getElementById('notificationIconClass');
        const titleEl = document.getElementById('notificationTitle');
        const messageEl = document.getElementById('notificationMessage');
        const confirmBtn = document.getElementById('notificationConfirmBtn');

        // Set icon and colors based on type
        if (type === 'success') {
            icon.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4 bg-green-100';
            iconClass.className = 'fas fa-check text-green-600 text-2xl';
        } else if (type === 'error') {
            icon.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4 bg-red-100';
            iconClass.className = 'fas fa-times text-red-600 text-2xl';
        } else {
            icon.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4 bg-blue-100';
            iconClass.className = 'fas fa-info text-blue-600 text-2xl';
        }

        titleEl.textContent = title;
        messageEl.textContent = message;

        // Set confirm button action
        if (onConfirm) {
            confirmBtn.onclick = () => {
                closeNotificationModal();
                onConfirm();
            };
        } else {
            confirmBtn.onclick = closeNotificationModal;
        }

        modal.classList.remove('hidden');
    }

    function closeNotificationModal() {
        document.getElementById('notificationModal').classList.add('hidden');
    }
</script>
@endsection