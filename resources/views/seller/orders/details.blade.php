<!-- Order Details Content -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Customer & Shipping Info -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Customer Information -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
            <div class="flex items-start justify-between mb-4">
                <h4 class="text-lg font-semibold text-blue-900 flex items-center">
                    <i class="fas fa-user-circle mr-2"></i>
                    Informasi Pelanggan
                </h4>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-blue-700 font-medium">Nama</p>
                    <p class="text-blue-900 font-semibold">{{ $order->cart->user->nickname ?? $order->cart->user->username }}</p>
                </div>
                <div>
                    <p class="text-sm text-blue-700 font-medium">Email</p>
                    <p class="text-blue-900">{{ $order->cart->user->email }}</p>
                </div>
                @if($order->cart->user->phone)
                <div>
                    <p class="text-sm text-blue-700 font-medium">Telepon</p>
                    <p class="text-blue-900">{{ $order->cart->user->phone }}</p>
                </div>
                @endif
                <div>
                    <p class="text-sm text-blue-700 font-medium">Bergabung</p>
                    <p class="text-blue-900">{{ $order->cart->user->created_at->format('d M Y') }}</p>
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
                @if($order->shipping_address)
                    <div class="whitespace-pre-line text-green-900">{{ $order->shipping_address }}</div>
                @elseif($order->cart->user->addresses && $order->cart->user->addresses->count() > 0)
                    @php $address = $order->cart->user->addresses->first(); @endphp
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

        <!-- Order Timeline -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-history mr-2"></i>
                Timeline Pesanan
            </h4>
            <div class="space-y-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-3 h-3 bg-blue-500 rounded-full"></div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Pesanan Dibuat</p>
                        <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                @if($order->status !== 'pending')
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-3 h-3 bg-green-500 rounded-full"></div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Status: {{ ucfirst($order->status) }}</p>
                        <p class="text-xs text-gray-500">{{ $order->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="space-y-6">
        <!-- Order Info -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-receipt mr-2"></i>
                Detail Pesanan
            </h4>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Nomor Pesanan</span>
                    <span class="text-sm font-medium text-gray-900">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Tanggal Pesanan</span>
                    <span class="text-sm font-medium text-gray-900">{{ $order->created_at->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Waktu</span>
                    <span class="text-sm font-medium text-gray-900">{{ $order->created_at->format('H:i') }}</span>
                </div>
                @if($order->transaction)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Status Pembayaran</span>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                        {{ $order->transaction->transactionstatus === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $order->transaction->transactionstatus === 'paid' ? 'Lunas' : 'Pending' }}
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- Products Summary -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-box mr-2"></i>
                Ringkasan Produk Anda
            </h4>
            <div class="space-y-3">
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
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $item->product->productname }}</p>
                            <p class="text-xs text-gray-500">{{ $item->quantity }} × Rp {{ number_format($item->productprice) }}</p>
                        </div>
                        <p class="text-sm font-medium text-gray-900">Rp {{ number_format($subtotal) }}</p>
                    </div>
                @endforeach
                <div class="border-t pt-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Total Item</span>
                        <span class="text-sm font-medium text-gray-900">{{ $totalItems }} item</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-base font-semibold text-gray-900">Total Anda</span>
                        <span class="text-base font-bold text-blue-600">Rp {{ number_format($totalAmount) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Details -->
<div class="mt-8">
    <h4 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
        <i class="fas fa-shopping-bag mr-2"></i>
        Produk Anda dalam Pesanan Ini
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
                    <p class="text-xs text-gray-500 mt-1">{{ $item->product->category->category }}</p>
                    <div class="mt-2 flex items-center justify-between">
                        <p class="text-sm text-gray-600">{{ $item->quantity }}x</p>
                        <p class="text-sm font-medium text-gray-900">Rp {{ number_format($item->productprice) }}</p>
                    </div>
                    <div class="mt-2">
                        <p class="text-sm font-semibold text-blue-600">
                            Subtotal: Rp {{ number_format($item->quantity * $item->productprice) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>