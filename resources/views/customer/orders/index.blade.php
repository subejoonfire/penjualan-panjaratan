@extends('layouts.app')

@section('title', 'Pesanan Saya - ' . env('MAIL_FROM_NAME', 'Penjualan Panjaratan'))

@section('content')
<div class="py-3 sm:py-6">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Pesanan Saya</h1>
            <p class="mt-1 sm:mt-2 text-sm sm:text-base text-gray-600">Lacak dan kelola pesanan Anda</p>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-4 sm:mb-6">
            <div class="p-3 sm:p-6">
                <form method="GET" action="{{ route('customer.orders.index') }}" class="space-y-3 sm:space-y-4">
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                        <div class="flex-1">
                            <label for="status" class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status"
                                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status')==='pending' ? 'selected' : '' }}>Menunggu
                                </option>
                                                            <option value="processing" {{ request('status')==='processing' ? 'selected' : '' }}>
                                Diproses</option>
                                <option value="shipped" {{ request('status')==='shipped' ? 'selected' : '' }}>Dikirim
                                </option>
                                <option value="delivered" {{ request('status')==='delivered' ? 'selected' : '' }}>
                                    Selesai</option>
                                <option value="cancelled" {{ request('status')==='cancelled' ? 'selected' : '' }}>
                                    Dibatalkan</option>
                            </select>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2 sm:items-end">
                            <button type="submit"
                                class="w-full sm:w-auto bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                <i class="fas fa-filter mr-1"></i>Filter
                            </button>
                            @if(request()->hasAny(['status']))
                            <a href="{{ route('customer.orders.index') }}"
                                class="w-full sm:w-auto bg-gray-600 text-white px-3 sm:px-4 py-2 rounded-md hover:bg-gray-700 text-sm text-center">
                                <i class="fas fa-times mr-1"></i>Reset
                            </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders List -->
        <div class="space-y-3 sm:space-y-6">
            @forelse($orders as $order)
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <!-- Mobile Layout -->
                <div class="block md:hidden">
                    <div class="p-3">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-medium text-gray-900">{{ $order->order_number }}</h3>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium
                                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                    @endif">
                                @switch($order->status)
                                @case('pending')
                                Menunggu
                                @break
                                @case('processing')
                                Diproses
                                @break
                                @case('shipped')
                                Dikirim
                                @break
                                @case('delivered')
                                Selesai
                                @break
                                @case('cancelled')
                                Dibatalkan
                                @break
                                @default
                                {{ ucfirst($order->status) }}
                                @endswitch
                            </span>
                        </div>

                        <div class="flex items-center space-x-2 mb-2">
                            @if($order->cart && $order->cart->cartDetails->count() > 0)
                            @php $firstDetail = $order->cart->cartDetails->first(); @endphp
                            <div class="flex-shrink-0">
                                @if($firstDetail->product->images->count() > 0)
                                <img src="{{ asset('storage/' . $firstDetail->product->images->first()->image) }}"
                                    alt="{{ $firstDetail->product->productname }}"
                                    class="w-8 h-8 rounded-lg object-cover">
                                @else
                                <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-xs"></i>
                                </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-900 truncate">
                                    {{ $firstDetail->product->productname }}
                                    @if($order->cart->cartDetails->count() > 1)
                                    <span class="text-gray-500">+{{ $order->cart->cartDetails->count() - 1 }}
                                        item</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-600">{{ $order->cart->cartDetails->sum('quantity') }} item
                                </p>
                            </div>
                            @else
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-box text-gray-400 text-xs"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-medium text-gray-900">Detail pesanan tidak tersedia</p>
                            </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <p class="text-sm font-bold text-gray-900">Rp {{ number_format($order->grandtotal) }}</p>
                        </div>

                        <div class="flex flex-col space-y-1.5">
                            <a href="{{ route('customer.orders.show', $order) }}"
                                class="w-full bg-blue-600 text-white px-3 py-1.5 rounded-md hover:bg-blue-700 text-center text-xs">
                                Lihat Detail
                            </a>
                            @if($order->status === 'pending')
                            <button type="button"
                                class="w-full bg-red-600 text-white px-3 py-1.5 rounded-md hover:bg-red-700 text-xs"
                                onclick="cancelOrder({{ $order->id }})">
                                Batalkan Pesanan
                            </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Desktop Layout -->
                <div class="hidden md:block">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Pesanan #{{ $order->order_number }}</h3>
                                <p class="text-sm text-gray-600">Dipesan pada {{ $order->created_at->format('d M Y,
                                    H:i') }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                        @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                        @endif">
                                    @switch($order->status)
                                    @case('pending')
                                    Menunggu
                                    @break
                                    @case('processing')
                                    Diproses
                                    @break
                                    @case('shipped')
                                    Dikirim
                                    @break
                                    @case('delivered')
                                    Selesai
                                    @break
                                    @case('cancelled')
                                    Dibatalkan
                                    @break
                                    @default
                                    {{ ucfirst($order->status) }}
                                    @endswitch
                                </span>
                                <a href="{{ route('customer.orders.show', $order) }}"
                                    class="text-blue-600 hover:text-blue-700 font-medium">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-4">
                                    @if($order->cart && $order->cart->cartDetails->count() > 0)
                                    @php $firstDetail = $order->cart->cartDetails->first(); @endphp
                                    <div class="flex-shrink-0">
                                        @if($firstDetail->product->images->count() > 0)
                                        <img src="{{ asset('storage/' . $firstDetail->product->images->first()->image) }}"
                                            alt="{{ $firstDetail->product->productname }}"
                                            class="w-12 h-12 rounded-lg object-cover">
                                        @else
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $firstDetail->product->productname }}
                                            @if($order->cart->cartDetails->count() > 1)
                                            <span class="text-gray-500">dan {{ $order->cart->cartDetails->count() - 1 }}
                                                item lainnya</span>
                                            @endif
                                        </p>
                                        <p class="text-sm text-gray-600">{{ $order->cart->cartDetails->sum('quantity')
                                            }}
                                            item</p>
                                    </div>
                                    @else
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-box text-gray-400"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Detail pesanan tidak tersedia</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-900">Rp {{ number_format($order->grandtotal) }}
                                </p>
                                @if($order->status === 'pending')
                                <div class="mt-2 space-x-2">
                                    <button type="button" class="text-sm text-red-600 hover:text-red-700"
                                        onclick="cancelOrder({{ $order->id }})">
                                        Batalkan Pesanan
                                    </button>
                                </div>
                                @elseif($order->status === 'delivered')
                                <div class="mt-2">
                                    <a href="{{ route('customer.orders.show', $order) }}"
                                        class="text-sm text-blue-600 hover:text-blue-700">
                                        Beri Ulasan
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-shopping-bag text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Pesanan</h3>
                    <p class="text-gray-600 mb-6">
                        @if(request('status'))
                        Tidak ada pesanan dengan status "{{ request('status') }}".
                        @else
                        Anda belum melakukan pesanan apapun. Mulai berbelanja sekarang!
                        @endif
                    </p>
                    @if(request('status'))
                    <a href="{{ route('customer.orders.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 mr-3">
                        Lihat Semua Pesanan
                    </a>
                    @endif
                    <a href="{{ route('products.index') }}"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-shopping-bag mr-2"></i>
                        Mulai Berbelanja
                    </a>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="mt-8">
            {{ $orders->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<script>
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
</script>
@endsection