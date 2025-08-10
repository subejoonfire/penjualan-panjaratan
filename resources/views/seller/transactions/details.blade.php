<!-- Transaction Details Content -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Transaction & Payment Info -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Transaction Information -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
            <div class="flex items-start justify-between mb-4">
                <h4 class="text-lg font-semibold text-green-900 flex items-center">
                    <i class="fas fa-credit-card mr-2"></i>
                    Informasi Transaksi
                </h4>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $transaction->transactionstatus === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $transaction->transactionstatus === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $transaction->transactionstatus === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                    {{ $transaction->transactionstatus === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                    @switch($transaction->transactionstatus)
                        @case('paid') 
                            <i class="fas fa-check mr-1"></i>Lunas 
                        @break
                        @case('pending') 
                            <i class="fas fa-clock mr-1"></i>Pending 
                        @break
                        @case('cancelled') 
                            <i class="fas fa-ban mr-1"></i>Dibatalkan 
                        @break
                        @case('failed') 
                            <i class="fas fa-times mr-1"></i>Gagal 
                        @break
                        @default {{ ucfirst($transaction->transactionstatus) }}
                    @endswitch
                </span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-green-700 font-medium">Nomor Transaksi</p>
                    <p class="text-green-900 font-semibold">{{ $transaction->transaction_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-green-700 font-medium">Metode Pembayaran</p>
                    <p class="text-green-900">
                        {{ $transaction->getPaymentMethodLabelAttribute() }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-green-700 font-medium">Total Transaksi</p>
                    <p class="text-green-900 font-bold">Rp {{ number_format($transaction->amount) }}</p>
                </div>
                <div>
                    <p class="text-sm text-green-700 font-medium">Tanggal Transaksi</p>
                    <p class="text-green-900">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
            <h4 class="text-lg font-semibold text-blue-900 mb-4 flex items-center">
                <i class="fas fa-user-circle mr-2"></i>
                Informasi Pelanggan
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-blue-700 font-medium">Nama</p>
                    <p class="text-blue-900 font-semibold">{{ $transaction->order->cart->user->nickname ?? $transaction->order->cart->user->username }}</p>
                </div>
                <div>
                    <p class="text-sm text-blue-700 font-medium">Email</p>
                    <p class="text-blue-900">{{ $transaction->order->cart->user->email }}</p>
                </div>
                @if($transaction->order->cart->user->phone)
                <div>
                    <p class="text-sm text-blue-700 font-medium">Telepon</p>
                    <p class="text-blue-900">{{ $transaction->order->cart->user->phone }}</p>
                </div>
                @endif
                <div>
                    <p class="text-sm text-blue-700 font-medium">Bergabung</p>
                    <p class="text-blue-900">{{ $transaction->order->cart->user->created_at->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
            <h4 class="text-lg font-semibold text-green-900 mb-4 flex items-center">
                <i class="fas fa-map-marker-alt mr-2"></i>
                Alamat Pengiriman
            </h4>
            <div class="bg-white rounded-lg p-4 shadow-sm">
                @if($transaction->order->shipping_address)
                    <div class="whitespace-pre-line text-green-900">{{ $transaction->order->shipping_address }}</div>
                @elseif($transaction->order->cart->user->addresses && $transaction->order->cart->user->addresses->count() > 0)
                    @php $address = $transaction->order->cart->user->addresses->first(); @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($address->label)
                        <div class="md:col-span-2">
                            <p class="text-sm text-green-700 font-medium">Label</p>
                            <p class="text-green-900 font-semibold">{{ $address->label }}</p>
                        </div>
                        @endif
                        <div class="md:col-span-2">
                            <p class="text-sm text-green-700 font-medium">Alamat Lengkap</p>
                            <p class="text-green-900">{{ $address->full_address }}</p>
                        </div>
                        @if($address->city)
                        <div>
                            <p class="text-sm text-green-700 font-medium">Kota</p>
                            <p class="text-green-900">{{ $address->city }}</p>
                        </div>
                        @endif
                        @if($address->postal_code)
                        <div>
                            <p class="text-sm text-green-700 font-medium">Kode Pos</p>
                            <p class="text-green-900">{{ $address->postal_code }}</p>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-map-marker-alt text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-500">Alamat pengiriman tidak tersedia</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Related Order Information -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 border border-purple-200">
            <h4 class="text-lg font-semibold text-purple-900 mb-4 flex items-center">
                <i class="fas fa-shopping-bag mr-2"></i>
                Pesanan Terkait
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-purple-700 font-medium">Nomor Pesanan</p>
                    <p class="text-purple-900 font-semibold">{{ $transaction->order->order_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-purple-700 font-medium">Status Pesanan</p>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                        {{ $transaction->order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $transaction->order->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $transaction->order->status === 'shipped' ? 'bg-purple-100 text-purple-800' : '' }}
                        {{ $transaction->order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $transaction->order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                        @switch($transaction->order->status)
                            @case('pending') Menunggu @break
                            @case('processing') Diproses @break
                            @case('shipped') Dikirim @break
                            @case('delivered') Selesai @break
                            @case('cancelled') Dibatalkan @break
                            @default {{ ucfirst($transaction->order->status) }}
                        @endswitch
                    </span>
                </div>
                <div>
                    <p class="text-sm text-purple-700 font-medium">Tanggal Pesanan</p>
                    <p class="text-purple-900">{{ $transaction->order->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-purple-700 font-medium">Item dari Anda</p>
                    <p class="text-purple-900 font-bold">{{ $sellerItems->count() }} produk</p>
                </div>
            </div>
        </div>

        <!-- Payment Timeline -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-history mr-2"></i>
                Timeline Pembayaran
            </h4>
            <div class="space-y-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-3 h-3 bg-blue-500 rounded-full"></div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Transaksi Dibuat</p>
                        <p class="text-xs text-gray-500">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                @if($transaction->transactionstatus !== 'pending')
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-3 h-3 
                        {{ $transaction->transactionstatus === 'paid' ? 'bg-green-500' : 'bg-red-500' }} rounded-full"></div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">
                            Status: {{ ucfirst($transaction->transactionstatus) }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $transaction->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="space-y-6">
        <!-- Revenue Breakdown -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-calculator mr-2"></i>
                Rincian Pendapatan Anda
            </h4>
            <div class="space-y-3">
                @php
                    $totalSellerAmount = 0;
                    $totalSellerItems = 0;
                @endphp
                @foreach($sellerItems as $item)
                    @php
                        $subtotal = $item->quantity * $item->productprice;
                        $totalSellerAmount += $subtotal;
                        $totalSellerItems += $item->quantity;
                    @endphp
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $item->product->productname }}</p>
                            <p class="text-xs text-gray-500">{{ $item->quantity }} × Rp {{ number_format($item->productprice) }}</p>
                        </div>
                        <p class="text-sm font-medium text-gray-900">Rp {{ number_format($subtotal) }}</p>
                    </div>
                @endforeach
                
                <div class="border-t pt-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Subtotal</span>
                        <span class="text-sm font-medium text-gray-900">Rp {{ number_format($totalSellerAmount) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Komisi Platform (5%)</span>
                        <span class="text-sm text-red-600">-Rp {{ number_format($totalSellerAmount * 0.05) }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                        <span class="text-base font-semibold text-gray-900">Pendapatan Bersih</span>
                        <span class="text-base font-bold text-green-600">Rp {{ number_format($totalSellerAmount * 0.95) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Summary -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-receipt mr-2"></i>
                Ringkasan Transaksi
            </h4>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Total Item Anda</span>
                    <span class="text-sm font-medium text-gray-900">{{ $totalSellerItems }} item</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Total Produk Anda</span>
                    <span class="text-sm font-medium text-gray-900">{{ $sellerItems->count() }} produk</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Status Pembayaran</span>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                        {{ $transaction->transactionstatus === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $transaction->transactionstatus === 'paid' ? 'Lunas' : 'Pending' }}
                    </span>
                </div>
                @if($transaction->transactionstatus === 'paid')
                <div class="bg-green-50 rounded-lg p-3 mt-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <p class="text-sm text-green-800 font-medium">
                            Pembayaran telah diterima. Pendapatan akan ditransfer dalam 1-3 hari kerja.
                        </p>
                    </div>
                </div>
                @elseif($transaction->transactionstatus === 'pending')
                <div class="bg-yellow-50 rounded-lg p-3 mt-4">
                    <div class="flex items-center">
                        <i class="fas fa-hourglass-half text-yellow-500 mr-2"></i>
                        <p class="text-sm text-yellow-800 font-medium">
                            Menunggu konfirmasi pembayaran dari pelanggan.
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        @if($transaction->transactionstatus === 'paid')
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-bolt mr-2"></i>
                Aksi Cepat
            </h4>
            <div class="space-y-3">
                <a href="{{ route('seller.orders.index', ['search' => $transaction->order->order_number]) }}"
                   class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Lihat Pesanan Terkait
                </a>
                <button onclick="window.print()"
                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-blue-300 shadow-sm text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100">
                    <i class="fas fa-print mr-2"></i>
                    Cetak Bukti Transaksi
                </button>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Product Details -->
<div class="mt-8">
    <h4 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
        <i class="fas fa-shopping-bag mr-2"></i>
        Produk Anda dalam Transaksi Ini
    </h4>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($sellerItems as $item)
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    @if($item->product->images->count() > 0)
                        <img src="{{ url('storage/' . $item->product->images->first()->image) }}" 
                             alt="{{ $item->product->productname }}"
                             class="w-16 h-16 rounded-lg object-cover">
                    @else
                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-image text-gray-400"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <h5 class="text-sm font-medium text-gray-900 truncate">{{ $item->product->productname }}</h5>
                                            <p class="text-xs text-gray-500 mt-1">{{ $item->product->category ? $item->product->category->category : 'Kategori Tidak Ditemukan' }}</p>
                    <div class="mt-2 flex items-center justify-between">
                        <p class="text-sm text-gray-600">{{ $item->quantity }}x</p>
                        <p class="text-sm font-medium text-gray-900">Rp {{ number_format($item->productprice) }}</p>
                    </div>
                    <div class="mt-2">
                        <p class="text-sm font-semibold text-blue-600">
                            Subtotal: Rp {{ number_format($item->quantity * $item->productprice) }}
                        </p>
                    </div>
                    <div class="mt-2">
                        <p class="text-xs text-green-600 font-medium">
                            Komisi: Rp {{ number_format(($item->quantity * $item->productprice) * 0.95) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>