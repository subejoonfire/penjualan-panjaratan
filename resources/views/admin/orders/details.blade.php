<!-- Order Information -->
<div class="bg-gray-50 p-4 rounded-lg mb-4">
    <h4 class="font-medium text-gray-900 mb-3">Informasi Pesanan</h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <p class="text-sm text-gray-600">Nomor Pesanan</p>
            <p class="font-medium">{{ $order->order_number }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Status</p>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                @elseif($order->status === 'delivered') bg-green-100 text-green-800
                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                @endif">
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
        <div>
            <p class="text-sm text-gray-600">Total</p>
            <p class="font-medium text-lg text-green-600">Rp {{ number_format($order->grandtotal) }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Tanggal Pesanan</p>
            <p class="font-medium">{{ $order->created_at->format('d M Y, H:i') }}</p>
        </div>
    </div>
</div>

<!-- Customer Information -->
<div class="bg-gray-50 p-4 rounded-lg mb-4">
    <h4 class="font-medium text-gray-900 mb-3">Informasi Pelanggan</h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <p class="text-sm text-gray-600">Nama</p>
            <p class="font-medium">{{ $order->cart->user->username }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Email</p>
            <p class="font-medium">{{ $order->cart->user->email }}</p>
        </div>
        <div class="col-span-2">
            <p class="text-sm text-gray-600">Alamat Pengiriman</p>
            <p class="font-medium">{{ $order->shipping_address }}</p>
        </div>
    </div>
</div>

<!-- Payment Information -->
@if($order->transaction)
<div class="bg-gray-50 p-4 rounded-lg mb-4">
    <h4 class="font-medium text-gray-900 mb-3">Informasi Pembayaran</h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <p class="text-sm text-gray-600">Metode Pembayaran</p>
            <p class="font-medium">
                {{ $order->transaction->getPaymentMethodLabelAttribute() }}
            </p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Status Pembayaran</p>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                @if($order->transaction->transactionstatus === 'paid') bg-green-100 text-green-800
                @elseif($order->transaction->transactionstatus === 'pending') bg-yellow-100 text-yellow-800
                @elseif($order->transaction->transactionstatus === 'failed') bg-red-100 text-red-800
                @else bg-gray-100 text-gray-800
                @endif">
                @switch($order->transaction->transactionstatus)
                    @case('paid') Dibayar @break
                    @case('pending') Menunggu @break
                    @case('failed') Gagal @break
                    @case('refunded') Dikembalikan @break
                    @default {{ ucfirst($order->transaction->transactionstatus) }}
                @endswitch
            </span>
        </div>
        <div>
            <p class="text-sm text-gray-600">Jumlah</p>
            <p class="font-medium">Rp {{ number_format($order->transaction->amount) }}</p>
        </div>
        @if($order->transaction->paid_at)
        <div>
            <p class="text-sm text-gray-600">Tanggal Pembayaran</p>
            <p class="font-medium">{{ $order->transaction->paid_at->format('d M Y, H:i') }}</p>
        </div>
        @endif
    </div>
</div>
@endif

<!-- Order Items -->
<div class="bg-gray-50 p-4 rounded-lg">
    <h4 class="font-medium text-gray-900 mb-3">Item Pesanan</h4>
    <div class="space-y-3">
        @foreach($order->cart->cartDetails as $item)
        <div class="flex items-center space-x-4 bg-white p-3 rounded-lg">
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
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">{{ $item->product->productname }}</p>
                <p class="text-sm text-gray-500">{{ $item->product->category->category }}</p>
                <p class="text-sm text-gray-500">Penjual: {{ $item->product->seller->username }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-medium text-gray-900">{{ $item->quantity }}x</p>
                <p class="text-sm text-gray-500">Rp {{ number_format($item->productprice) }}</p>
                <p class="text-sm font-medium text-gray-900">Rp {{ number_format($item->productprice * $item->quantity) }}</p>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Order Summary -->
    <div class="mt-4 pt-4 border-t border-gray-200">
        <div class="flex justify-between items-center">
            <span class="text-base font-medium text-gray-900">Total Pesanan</span>
            <span class="text-lg font-bold text-gray-900">Rp {{ number_format($order->grandtotal) }}</span>
        </div>
    </div>
</div>