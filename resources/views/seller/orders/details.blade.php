<!-- Order Details Content -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
    <!-- Customer & Shipping Info -->
    <div class="lg:col-span-2 space-y-4 sm:space-y-6">
        <!-- Customer Information -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 sm:p-6 border border-blue-200">
            <div class="flex items-start justify-between mb-3 sm:mb-4">
                <h4 class="text-base sm:text-lg font-semibold text-blue-900 flex items-center">
                    <i class="fas fa-user-circle mr-1 sm:mr-2 text-sm sm:text-base"></i>
                    <span class="hidden sm:inline">Informasi Pelanggan</span>
                    <span class="sm:hidden">Pelanggan</span>
                </h4>
                <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full text-xs font-medium
                    {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $order->status === 'shipped' ? 'bg-purple-100 text-purple-800' : '' }}
                    {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                    @switch($order->status)
                        @case('pending') Menunggu @break
                        @case('processing') Diproses @break
                        @case('shipped') Dikirim @break
                        @case('delivered') Selesai @break
                        @case('cancelled') Dibatalkan @break
                        @default {{ ucfirst($order->status) }}
                    @endswitch
                </span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                <div>
                    <p class="text-xs sm:text-sm text-blue-700 font-medium">Nama</p>
                    <p class="text-sm sm:text-base text-blue-900 font-semibold truncate">{{ $order->cart->user->nickname ?? $order->cart->user->username }}</p>
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-blue-700 font-medium">Email</p>
                    <p class="text-sm sm:text-base text-blue-900 truncate">{{ $order->cart->user->email }}</p>
                </div>
                @if($order->cart->user->phone)
                <div>
                    <p class="text-xs sm:text-sm text-blue-700 font-medium">Telepon</p>
                    <p class="text-sm sm:text-base text-blue-900">{{ $order->cart->user->phone }}</p>
                </div>
                @endif
                <div>
                    <p class="text-xs sm:text-sm text-blue-700 font-medium">Bergabung</p>
                    <p class="text-sm sm:text-base text-blue-900">{{ $order->cart->user->created_at->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 sm:p-6 border border-green-200">
            <h4 class="text-base sm:text-lg font-semibold text-green-900 mb-3 sm:mb-4 flex items-center">
                <i class="fas fa-map-marker-alt mr-1 sm:mr-2 text-sm sm:text-base"></i>
                <span class="hidden sm:inline">Alamat Pengiriman</span>
                <span class="sm:hidden">Alamat</span>
            </h4>
            <div class="bg-white rounded-lg p-3 sm:p-4 shadow-sm">
                @if($order->shipping_address)
                    <div class="whitespace-pre-line text-sm sm:text-base text-green-900">{{ $order->shipping_address }}</div>
                @elseif($order->cart->user->addresses && $order->cart->user->addresses->count() > 0)
                    @php $address = $order->cart->user->addresses->first(); @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                        @if($address->label)
                        <div class="md:col-span-2">
                            <p class="text-xs sm:text-sm text-green-700 font-medium">Label</p>
                            <p class="text-sm sm:text-base text-green-900 font-semibold">{{ $address->label }}</p>
                        </div>
                        @endif
                        <div class="md:col-span-2">
                            <p class="text-xs sm:text-sm text-green-700 font-medium">Alamat Lengkap</p>
                            <p class="text-sm sm:text-base text-green-900">{{ $address->full_address }}</p>
                        </div>
                        @if($address->city)
                        <div>
                            <p class="text-xs sm:text-sm text-green-700 font-medium">Kota</p>
                            <p class="text-sm sm:text-base text-green-900">{{ $address->city }}</p>
                        </div>
                        @endif
                        @if($address->postal_code)
                        <div>
                            <p class="text-xs sm:text-sm text-green-700 font-medium">Kode Pos</p>
                            <p class="text-sm sm:text-base text-green-900">{{ $address->postal_code }}</p>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-3 sm:py-4">
                        <i class="fas fa-map-marker-alt text-gray-400 text-xl sm:text-2xl md:text-3xl mb-2"></i>
                        <p class="text-sm sm:text-base text-gray-500">Alamat pengiriman tidak tersedia</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Timeline -->
        <div class="bg-gray-50 rounded-lg p-4 sm:p-6">
            <h4 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center">
                <i class="fas fa-history mr-1 sm:mr-2 text-sm sm:text-base"></i>
                <span class="hidden sm:inline">Timeline Pesanan</span>
                <span class="sm:hidden">Timeline</span>
            </h4>
            <div class="space-y-3 sm:space-y-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-2.5 h-2.5 sm:w-3 sm:h-3 bg-blue-500 rounded-full"></div>
                    <div class="ml-3 sm:ml-4">
                        <p class="text-xs sm:text-sm font-medium text-gray-900">Pesanan Dibuat</p>
                        <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                @if($order->status !== 'pending')
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-2.5 h-2.5 sm:w-3 sm:h-3 bg-green-500 rounded-full"></div>
                    <div class="ml-3 sm:ml-4">
                        <p class="text-xs sm:text-sm font-medium text-gray-900">Status: {{ ucfirst($order->status) }}</p>
                        <p class="text-xs text-gray-500">{{ $order->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="space-y-4 sm:space-y-6">
        <!-- Order Info -->
        <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
            <h4 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center">
                <i class="fas fa-receipt mr-1 sm:mr-2 text-sm sm:text-base"></i>
                <span class="hidden sm:inline">Detail Pesanan</span>
                <span class="sm:hidden">Detail</span>
            </h4>
            <div class="space-y-2 sm:space-y-3">
                <div class="flex justify-between">
                    <span class="text-xs sm:text-sm text-gray-600">Nomor Pesanan</span>
                    <span class="text-xs sm:text-sm font-medium text-gray-900 truncate ml-2">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs sm:text-sm text-gray-600">Tanggal Pesanan</span>
                    <span class="text-xs sm:text-sm font-medium text-gray-900">{{ $order->created_at->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs sm:text-sm text-gray-600">Waktu</span>
                    <span class="text-xs sm:text-sm font-medium text-gray-900">{{ $order->created_at->format('H:i') }}</span>
                </div>
                @if($order->transaction)
                <div class="flex justify-between items-center">
                    <span class="text-xs sm:text-sm text-gray-600">Status Pembayaran</span>
                    <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full text-xs font-medium
                        {{ $order->transaction->transactionstatus === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $order->transaction->transactionstatus === 'paid' ? 'Lunas' : 'Pending' }}
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- Products Summary -->
        <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
            <h4 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center">
                <i class="fas fa-box mr-1 sm:mr-2 text-sm sm:text-base"></i>
                <span class="hidden sm:inline">Ringkasan Produk Anda</span>
                <span class="sm:hidden">Produk Anda</span>
            </h4>
            <div class="space-y-2 sm:space-y-3">
                @php
                    $totalAmount = 0;
                    $totalItems = 0;
                @endphp
                @foreach($sellerItems as $item)
                    @php
                        $subtotal = $item->quantity * $item->productprice;
                        $totalAmount += $subtotal;
                        $totalItems += $item->quantity;
                    @endphp
                    <div class="flex justify-between items-start">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ $item->product->productname }}</p>
                            <p class="text-xs text-gray-500">{{ $item->quantity }} × Rp {{ number_format($item->productprice) }}</p>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-gray-900 ml-2">Rp {{ number_format($subtotal) }}</p>
                    </div>
                @endforeach
                <div class="border-t pt-2 sm:pt-3">
                    <div class="flex justify-between">
                        <span class="text-xs sm:text-sm text-gray-600">Total Item</span>
                        <span class="text-xs sm:text-sm font-medium text-gray-900">{{ $totalItems }} item</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm sm:text-base font-semibold text-gray-900">Total Anda</span>
                        <span class="text-sm sm:text-base font-bold text-blue-600">Rp {{ number_format($totalAmount) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Details -->
<div class="mt-6 sm:mt-8">
    <h4 class="text-base sm:text-lg font-semibold text-gray-900 mb-4 sm:mb-6 flex items-center">
        <i class="fas fa-shopping-bag mr-1 sm:mr-2 text-sm sm:text-base"></i>
        <span class="hidden sm:inline">Produk Anda dalam Pesanan Ini</span>
        <span class="sm:hidden">Produk Anda</span>
    </h4>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        @foreach($sellerItems as $item)
        <div class="bg-white border border-gray-200 rounded-lg p-3 sm:p-4 hover:shadow-md transition-shadow">
            <div class="flex items-start space-x-3 sm:space-x-4">
                <div class="flex-shrink-0">
                    @if($item->product->images->count() > 0)
                        <img src="{{ url('storage/' . $item->product->images->first()->image) }}" 
                             alt="{{ $item->product->productname }}"
                             class="w-12 h-12 sm:w-16 sm:h-16 rounded-lg object-cover">
                    @else
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-sm sm:text-base"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <h5 class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ $item->product->productname }}</h5>
                    <p class="text-xs text-gray-500 mt-1">{{ $item->product->category->category }}</p>
                    <div class="mt-1.5 sm:mt-2 flex items-center justify-between">
                        <p class="text-xs sm:text-sm text-gray-600">{{ $item->quantity }}x</p>
                        <p class="text-xs sm:text-sm font-medium text-gray-900">Rp {{ number_format($item->productprice) }}</p>
                    </div>
                    <div class="mt-1.5 sm:mt-2">
                        <p class="text-xs sm:text-sm font-semibold text-blue-600">
                            Subtotal: Rp {{ number_format($item->quantity * $item->productprice) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>