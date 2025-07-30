<div class="space-y-6">
    <!-- Transaction Information -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="font-medium text-gray-900 mb-3 flex items-center">
            <i class="fas fa-credit-card text-blue-600 mr-2"></i>
            Informasi Transaksi
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Nomor Transaksi</p>
                <p class="text-sm font-medium text-gray-900">{{ $transaction->transaction_number }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">ID Transaksi</p>
                <p class="text-sm font-medium text-gray-900">#{{ $transaction->id }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Jumlah</p>
                <p class="text-lg font-bold text-gray-900">Rp {{ number_format($transaction->amount) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Status</p>
                @switch($transaction->transactionstatus)
                    @case('pending')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1"></i>Pending
                        </span>
                        @break
                    @case('paid')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check mr-1"></i>Dibayar
                        </span>
                        @break
                    @case('cancelled')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times mr-1"></i>Dibatalkan
                        </span>
                        @break
                    @case('failed')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Gagal
                        </span>
                        @break
                    @default
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ $transaction->transactionstatus }}
                        </span>
                @endswitch
            </div>
            <div>
                <p class="text-sm text-gray-600">Metode Pembayaran</p>
                <div class="flex items-center mt-1">
                    @switch($transaction->payment_method)
                        @case('bank_transfer')
                            <i class="fas fa-university text-blue-600 mr-2"></i>
                            <span class="text-sm text-gray-900">Transfer Bank</span>
                            @break
                        @case('credit_card')
                            <i class="fas fa-credit-card text-green-600 mr-2"></i>
                            <span class="text-sm text-gray-900">Kartu Kredit</span>
                            @break
                        @case('e_wallet')
                            <i class="fas fa-wallet text-purple-600 mr-2"></i>
                            <span class="text-sm text-gray-900">E-Wallet</span>
                            @break
                        @case('cod')
                            <i class="fas fa-money-bill text-orange-600 mr-2"></i>
                            <span class="text-sm text-gray-900">Bayar di Tempat</span>
                            @break
                        @default
                            <i class="fas fa-question text-gray-600 mr-2"></i>
                            <span class="text-sm text-gray-900">{{ $transaction->payment_method }}</span>
                    @endswitch
                </div>
            </div>
            <div>
                <p class="text-sm text-gray-600">Tanggal Transaksi</p>
                <p class="text-sm text-gray-900">{{ $transaction->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>

    @if($transaction->order)
    <!-- Order Information -->
    <div class="bg-blue-50 p-4 rounded-lg">
        <h4 class="font-medium text-gray-900 mb-3 flex items-center">
            <i class="fas fa-shopping-bag text-blue-600 mr-2"></i>
            Informasi Pesanan
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Nomor Pesanan</p>
                <p class="text-sm font-medium text-gray-900">{{ $transaction->order->order_number ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Status Pesanan</p>
                @switch($transaction->order->status)
                    @case('pending')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1"></i>Pending
                        </span>
                        @break
                    @case('processing')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-cog mr-1"></i>Diproses
                        </span>
                        @break
                    @case('shipped')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-shipping-fast mr-1"></i>Dikirim
                        </span>
                        @break
                    @case('delivered')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>Selesai
                        </span>
                        @break
                    @case('cancelled')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times mr-1"></i>Dibatalkan
                        </span>
                        @break
                    @default
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ $transaction->order->status }}
                        </span>
                @endswitch
            </div>
            <div>
                <p class="text-sm text-gray-600">Total Pesanan</p>
                <p class="text-sm font-medium text-gray-900">Rp {{ number_format($transaction->order->grandtotal ?? 0) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Tanggal Pesanan</p>
                <p class="text-sm text-gray-900">{{ $transaction->order->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>

    @if($transaction->order->cart && $transaction->order->cart->user)
    <!-- Customer Information -->
    <div class="bg-green-50 p-4 rounded-lg">
        <h4 class="font-medium text-gray-900 mb-3 flex items-center">
            <i class="fas fa-user text-green-600 mr-2"></i>
            Informasi Pelanggan
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Nama Pengguna</p>
                <p class="text-sm font-medium text-gray-900">{{ $transaction->order->cart->user->username }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Email</p>
                <p class="text-sm text-gray-900">{{ $transaction->order->cart->user->email }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Telepon</p>
                <p class="text-sm text-gray-900">{{ $transaction->order->cart->user->phone ?? 'Tidak tersedia' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Bergabung Sejak</p>
                <p class="text-sm text-gray-900">{{ $transaction->order->cart->user->created_at->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Shipping Address -->
    <div class="bg-orange-50 p-4 rounded-lg">
        <h4 class="font-medium text-gray-900 mb-3 flex items-center">
            <i class="fas fa-map-marker-alt text-orange-600 mr-2"></i>
            Alamat Pengiriman
        </h4>
        @if($transaction->order->shipping_address)
            <div class="bg-white rounded p-3 border border-orange-200">
                <div class="whitespace-pre-line text-gray-900">{{ $transaction->order->shipping_address }}</div>
            </div>
        @elseif($transaction->order->cart->user->addresses && $transaction->order->cart->user->addresses->count() > 0)
            @php $address = $transaction->order->cart->user->addresses->first(); @endphp
            <div class="bg-white rounded p-3 border border-orange-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($address->label)
                    <div class="md:col-span-2">
                        <p class="text-sm text-orange-700 font-medium">Label</p>
                        <p class="text-gray-900 font-semibold">{{ $address->label }}</p>
                    </div>
                    @endif
                    <div class="md:col-span-2">
                        <p class="text-sm text-orange-700 font-medium">Alamat Lengkap</p>
                        <p class="text-gray-900">{{ $address->full_address }}</p>
                    </div>
                    @if($address->city)
                    <div>
                        <p class="text-sm text-orange-700 font-medium">Kota</p>
                        <p class="text-gray-900">{{ $address->city }}</p>
                    </div>
                    @endif
                    @if($address->postal_code)
                    <div>
                        <p class="text-sm text-orange-700 font-medium">Kode Pos</p>
                        <p class="text-gray-900">{{ $address->postal_code }}</p>
                    </div>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-white rounded p-3 border border-orange-200 text-center">
                <i class="fas fa-map-marker-alt text-gray-400 text-2xl mb-2"></i>
                <p class="text-gray-500">Alamat pengiriman tidak tersedia</p>
            </div>
        @endif
    </div>

    @if($transaction->order->cart->cartDetails->count() > 0)
    <!-- Order Items -->
    <div class="bg-white border border-gray-200 rounded-lg">
        <div class="px-4 py-3 border-b border-gray-200">
            <h4 class="font-medium text-gray-900 flex items-center">
                <i class="fas fa-box text-gray-600 mr-2"></i>
                Item Pesanan ({{ $transaction->order->cart->cartDetails->count() }} item)
            </h4>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($transaction->order->cart->cartDetails as $detail)
            <div class="px-4 py-3">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        @if($detail->product->images->count() > 0)
                        <img src="{{ asset('storage/' . $detail->product->images->first()->image) }}"
                            alt="{{ $detail->product->productname }}"
                            class="w-12 h-12 rounded-lg object-cover">
                        @else
                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-image text-gray-400"></i>
                        </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ $detail->product->productname }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ $detail->quantity }} x Rp {{ number_format($detail->productprice) }}
                        </p>
                        <p class="text-xs text-gray-400">
                            Penjual: {{ $detail->product->seller->username ?? 'N/A' }}
                        </p>
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
    @endif
    @endif
    @endif

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
        @if($transaction->transactionstatus === 'pending')
        <button onclick="updateTransactionStatus({{ $transaction->id }}, 'paid')" 
            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
            <i class="fas fa-check mr-2"></i>Mark as Paid
        </button>
        <button onclick="updateTransactionStatus({{ $transaction->id }}, 'cancelled')" 
            class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
            <i class="fas fa-times mr-2"></i>Cancel
        </button>
        @endif
        <button onclick="closeTransactionModal()" 
            class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
            <i class="fas fa-times mr-2"></i>Tutup
        </button>
    </div>
</div>