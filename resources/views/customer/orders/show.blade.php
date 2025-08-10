@extends('layouts.app')

@section('title', 'Detail Pesanan - ' . $order->order_number)

@section('content')
<div class="py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detail Pesanan</h1>
                    <p class="mt-2 text-gray-600">Nomor Pesanan #{{ $order->order_number }}</p>
                </div>
                <a href="{{ route('customer.orders.index') }}"
                    class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Pesanan
                </a>
            </div>
        </div>

        <!-- Order Status Banner -->
        <div class="mb-8">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Status Pesanan</h3>
                        <p class="text-sm text-gray-600">Ditempatkan pada {{ $order->created_at->format('F d, Y \a\t
                            H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                            @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                            @elseif($order->status === 'delivered') bg-green-100 text-green-800
                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                        @if($order->status === 'pending')
                        <button type="button"
                            onclick="cancelOrder({{ $order->id }})"
                            class="text-sm text-red-600 hover:text-red-500 mt-2">
                            Batalkan Pesanan
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Order Progress -->
                <div class="mt-6">
                    <div class="flex items-center justify-between">
                        <!-- Pending -->
                        <div class="flex flex-col sm:flex-row items-center">
                            <div
                                class="flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center
                                {{ in_array($order->status, ['pending', 'processing', 'shipped', 'delivered']) ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-500' }}">
                                <i class="fas fa-clock text-xs sm:text-sm"></i>
                            </div>
                            <span class="mt-1 sm:mt-0 sm:ml-2 text-xs sm:text-sm font-medium text-gray-900 text-center">Tertunda</span>
                        </div>

                        <!-- Arrow -->
                        <div class="flex-1 mx-1 sm:mx-4 mt-0 sm:mt-0">
                            <div
                                class="h-0.5 {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'bg-blue-600' : 'bg-gray-300' }}">
                            </div>
                        </div>

                        <!-- Processing -->
                        <div class="flex flex-col sm:flex-row items-center">
                            <div
                                class="flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center
                                {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-500' }}">
                                <i class="fas fa-check text-xs sm:text-sm"></i>
                            </div>
                            <span class="mt-1 sm:mt-0 sm:ml-2 text-xs sm:text-sm font-medium text-gray-900 text-center">Diproses</span>
                        </div>

                        <!-- Arrow -->
                        <div class="flex-1 mx-1 sm:mx-4 mt-0 sm:mt-0">
                            <div
                                class="h-0.5 {{ in_array($order->status, ['shipped', 'delivered']) ? 'bg-blue-600' : 'bg-gray-300' }}">
                            </div>
                        </div>

                        <!-- Shipped -->
                        <div class="flex flex-col sm:flex-row items-center">
                            <div
                                class="flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center
                                {{ in_array($order->status, ['shipped', 'delivered']) ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-500' }}">
                                <i class="fas fa-truck text-xs sm:text-sm"></i>
                            </div>
                            <span class="mt-1 sm:mt-0 sm:ml-2 text-xs sm:text-sm font-medium text-gray-900 text-center">Dikirim</span>
                        </div>

                        <!-- Arrow -->
                        <div class="flex-1 mx-1 sm:mx-4 mt-0 sm:mt-0">
                            <div class="h-0.5 {{ $order->status === 'delivered' ? 'bg-green-600' : 'bg-gray-300' }}">
                            </div>
                        </div>

                        <!-- Delivered -->
                        <div class="flex flex-col sm:flex-row items-center">
                            <div
                                class="flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center
                                {{ $order->status === 'delivered' ? 'bg-green-600 text-white' : 'bg-gray-300 text-gray-500' }}">
                                <i class="fas fa-home text-xs sm:text-sm"></i>
                            </div>
                            <span class="mt-1 sm:mt-0 sm:ml-2 text-xs sm:text-sm font-medium text-gray-900 text-center">Selesai</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Items -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Item Pesanan ({{
                            $order->cart->cartDetails->count() }})</h3>
                    </div>
                    <div class="px-6 py-6">
                        <div class="space-y-6">
                            @foreach($order->cart->cartDetails as $item)
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 h-16 w-16">
                                    @if($item->product->images->count() > 0)
                                    <img src="{{ asset('storage/' . $item->product->images->first()->image) }}"
                                        alt="{{ $item->product->productname }}"
                                        class="h-16 w-16 rounded-lg object-cover">
                                    @else
                                    <div class="h-16 w-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('products.show', $item->product) }}"
                                            class="hover:text-blue-600">
                                            {{ $item->product->productname }}
                                        </a>
                                    </h4>
                                                                            <p class="text-sm text-gray-500">Penjual: {{ $item->product->seller->nickname ?? $item->product->seller->username }}</p>
                                    <p class="text-sm text-gray-500">Kategori: {{ $item->product->category ? $item->product->category->category : 'Kategori Tidak Ditemukan'
                                        }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-900">Jumlah: {{ $item->quantity }}</p>
                                    <p class="text-sm font-medium text-gray-900">Rp {{
                                        number_format($item->productprice) }}</p>
                                    <p class="text-sm text-gray-500">Subtotal: Rp {{ number_format($item->quantity *
                                        $item->productprice) }}</p>
                                </div>
                                @if($order->status === 'delivered')
                                <div class="flex-shrink-0">
                                    @if($item->product->reviews()->where('iduser', auth()->id())->exists())
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-star mr-1"></i>
                                        Direview
                                    </span>
                                    @else
                                    <button
                                        onclick="openReviewModal('{{ $item->product->id }}', '{{ $item->product->productname }}')"
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200">
                                        <i class="fas fa-star mr-1"></i>
                                        Review
                                    </button>
                                    @endif
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Payment Information -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informasi Pembayaran</h3>
                    </div>
                    <div class="px-6 py-6">
                        @if($order->transaction)
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID Transaksi</dt>
                                <dd class="text-sm text-gray-900">{{ $order->transaction->transaction_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Metode Pembayaran</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ $order->transaction ? ($order->transaction->getPaymentMethodLabelAttribute() ?? ucfirst(str_replace('_', ' ', $order->transaction->payment_method))) : '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status Pembayaran</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($order->transaction->transactionstatus === 'paid') bg-green-100 text-green-800
                                        @elseif($order->transaction->transactionstatus === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->transaction->transactionstatus === 'failed') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($order->transaction->transactionstatus) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Jumlah</dt>
                                <dd class="text-sm font-medium text-gray-900">Rp {{
                                    number_format($order->transaction->amount) }}</dd>
                            </div>
                        </dl>
                        @else
                        <p class="text-sm text-gray-500">Informasi pembayaran tidak tersedia</p>
                        @endif
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Ringkasan Pesanan</h3>
                    </div>
                    <div class="px-6 py-6">
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Subtotal</dt>
                                <dd class="text-sm text-gray-900">Rp {{
                                    number_format($order->cart->cartDetails->sum(function($item) { return
                                    $item->quantity * $item->productprice; })) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Pengiriman</dt>
                                <dd class="text-sm text-gray-900">Rp {{ number_format($order->shipping_cost ?? 0) }}
                                </dd>
                            </div>
                            <div class="flex justify-between border-t border-gray-200 pt-3">
                                <dt class="text-base font-medium text-gray-900">Total</dt>
                                <dd class="text-base font-medium text-gray-900">Rp {{
                                    number_format($order->grandtotal) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informasi Pengiriman</h3>
                    </div>
                    <div class="px-6 py-6">
                        @if($order->shipping_address)
                        <div class="text-sm">
                            <p class="font-medium text-gray-900">{{ $order->recipient_name ?? auth()->user()->username
                                }}</p>
                            <p class="text-gray-700 mt-1">{{ $order->shipping_address }}</p>
                            @if($order->shipping_phone)
                            <p class="text-gray-700 mt-1">Telepon: {{ $order->shipping_phone }}</p>
                            @endif
                        </div>
                        @else
                        <p class="text-sm text-gray-500">Informasi pengiriman tidak tersedia</p>
                        @endif
                    </div>
                </div>

                @if($order->notes)
                <!-- Order Notes -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Catatan Pesanan</h3>
                    </div>
                    <div class="px-6 py-6">
                        <p class="text-sm text-gray-700">{{ $order->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center h-full w-full hidden">
    <div class="relative mx-auto p-5 border w-96 max-h-[90vh] shadow-lg rounded-md bg-white overflow-y-auto">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tulis Ulasan</h3>
                <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div>
                <div class="mb-4">
                    <p id="productName" class="text-sm text-gray-600 mb-2"></p>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                    <div class="flex space-x-1">
                        @for($i = 1; $i <= 5; $i++) <button type="button" onclick="setRating({{ $i }})"
                            class="star-btn focus:outline-none">
                            <i class="fas fa-star text-2xl text-gray-300" id="star-{{ $i }}"></i>
                            </button>
                            @endfor
                    </div>
                    <input type="hidden" id="ratingInput" value="0">
                </div>
                <div class="mb-4">
                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Komentar</label>
                    <textarea id="comment" rows="4" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Bagikan pengalaman Anda dengan produk ini..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeReviewModal()"
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="button" onclick="submitReviewModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Kirim Ulasan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentRating = 0;

    let currentProductId = null;
    
    function openReviewModal(productId, productName) {
        currentProductId = productId;
        document.getElementById('productName').textContent = 'Produk: ' + productName;
        document.getElementById('reviewModal').classList.remove('hidden');
        resetRating();
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').classList.add('hidden');
        document.getElementById('comment').value = '';
        resetRating();
    }

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

    function resetRating() {
        currentRating = 0;
        document.getElementById('ratingInput').value = 0;
        for (let i = 1; i <= 5; i++) {
            const star = document.getElementById('star-' + i);
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        }
    }

    function cancelOrder(orderId) {
        showModalNotification({
            type: 'warning',
            title: 'Konfirmasi Pembatalan',
            message: 'Apakah Anda yakin ingin membatalkan pesanan ini?',
            confirmText: 'Ya, Batalkan',
            cancelText: 'Tidak',
            onConfirm: () => {
                fetch(`${window.location.origin}/customer/orders/${orderId}/cancel`, {
                    method: 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showModalNotification({
                            type: 'success',
                            title: 'Berhasil!',
                            message: data.message || 'Pesanan berhasil dibatalkan',
                            confirmText: 'OK',
                            showCancel: false,
                            onConfirm: () => {
                                window.location.reload();
                            }
                        });
                    } else {
                        showModalNotification({
                            type: 'error',
                            title: 'Gagal!',
                            message: data.message || 'Gagal membatalkan pesanan',
                            confirmText: 'OK',
                            showCancel: false
                        });
                    }
                })
                .catch(error => {
                    console.error('Cancel order error:', error);
                    showModalNotification({
                        type: 'error',
                        title: 'Error!',
                        message: 'Terjadi kesalahan saat membatalkan pesanan',
                        confirmText: 'OK',
                        showCancel: false
                    });
                });
            }
        });
    }

    function submitReviewModal() {
        if (!currentProductId) {
            showModalNotification({
                type: 'error',
                title: 'Error!',
                message: 'Product ID tidak ditemukan',
                confirmText: 'OK',
                showCancel: false
            });
            return;
        }

        const rating = document.getElementById('ratingInput').value;
        const comment = document.getElementById('comment').value;

        if (rating == 0) {
            showModalNotification({
                type: 'error',
                title: 'Error!',
                message: 'Silakan pilih rating terlebih dahulu',
                confirmText: 'OK',
                showCancel: false
            });
            return;
        }

        const formData = new FormData();
        formData.append('rating', rating);
        formData.append('productreviews', comment);

        fetch(`${window.location.origin}/customer/products/${currentProductId}/reviews`, {
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
                    message: data.message || 'Ulasan berhasil dikirim',
                    confirmText: 'OK',
                    showCancel: false,
                    onConfirm: () => {
                        closeReviewModal();
                        window.location.reload();
                    }
                });
            } else {
                showModalNotification({
                    type: 'error',
                    title: 'Gagal!',
                    message: data.message || 'Gagal mengirim ulasan',
                    confirmText: 'OK',
                    showCancel: false
                });
            }
        })
        .catch(error => {
            console.error('Review error:', error);
            showModalNotification({
                type: 'error',
                title: 'Error!',
                message: 'Terjadi kesalahan saat mengirim ulasan',
                confirmText: 'OK',
                showCancel: false
            });
        });
    }
</script>
@endsection