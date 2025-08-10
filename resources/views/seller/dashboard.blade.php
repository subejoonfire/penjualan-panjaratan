@extends('layouts.app')

@section('title', 'Dashboard Penjual - ' . env('MAIL_FROM_NAME', 'Penjualan Panjaratan'))

@section('content')
<div class="py-3 sm:py-6">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Dashboard Penjual</h1>
            <p class="mt-1 sm:mt-2 text-sm sm:text-base text-gray-600">Selamat datang kembali, {{ auth()->user()->nickname ??
                auth()->user()->username }}!</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
            <!-- Total Products -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-3 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-box text-white text-xs sm:text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3 sm:ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Produk</dt>
                                <dd class="text-base sm:text-lg font-medium text-gray-900">{{ number_format($totalProducts) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-3 sm:px-5 py-2 sm:py-3">
                    <div class="text-xs sm:text-sm">
                        <span class="text-green-600 font-medium">{{ number_format($activeProducts) }}</span>
                        <span class="text-gray-500"> produk aktif</span>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-3 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-shopping-bag text-white text-xs sm:text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3 sm:ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Pesanan</dt>
                                <dd class="text-base sm:text-lg font-medium text-gray-900">{{ number_format($totalOrders) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-3 sm:px-5 py-2 sm:py-3">
                    <div class="text-xs sm:text-sm">
                        <span class="text-yellow-600 font-medium">{{ number_format($pendingOrders) }}</span>
                        <span class="text-gray-500"> menunggu</span>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-3 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-money-bill-wave text-white text-xs sm:text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3 sm:ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Pendapatan</dt>
                                <dd class="text-sm sm:text-lg font-medium text-gray-900">Rp {{ number_format($totalRevenue) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-3 sm:px-5 py-2 sm:py-3">
                    <div class="text-xs sm:text-sm">
                        <a href="{{ route('seller.orders.index') }}"
                            class="font-medium text-yellow-600 hover:text-yellow-500">
                            Lihat laporan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Monthly Revenue -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-3 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-chart-line text-white text-xs sm:text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3 sm:ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Bulan Ini</dt>
                                <dd class="text-sm sm:text-lg font-medium text-gray-900">
                                    Rp {{ number_format($monthlyRevenue->where('month', date('m'))->where('year', date('Y'))->first()->total ?? 0) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-3 sm:px-5 py-2 sm:py-3">
                    <div class="text-xs sm:text-sm">
                        <a href="{{ route('seller.orders.index') }}"
                            class="font-medium text-purple-600 hover:text-purple-500">
                            Lihat detail
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg mb-6 sm:mb-8">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                <h3 class="text-base sm:text-lg font-medium text-gray-900">Aksi Cepat</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                    <a href="{{ route('seller.products.create') }}"
                        class="flex flex-col sm:flex-row items-center p-3 sm:p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <div class="flex-shrink-0 mb-2 sm:mb-0">
                            <i class="fas fa-plus text-blue-600 text-lg sm:text-xl"></i>
                        </div>
                        <div class="text-center sm:text-left sm:ml-4">
                            <p class="text-xs sm:text-sm font-medium text-blue-600">Tambah Produk</p>
                            <p class="text-xs text-gray-500 hidden sm:block">Buat listing baru</p>
                        </div>
                    </a>

                    <a href="{{ route('seller.products.index') }}"
                        class="flex flex-col sm:flex-row items-center p-3 sm:p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <div class="flex-shrink-0 mb-2 sm:mb-0">
                            <i class="fas fa-list text-green-600 text-lg sm:text-xl"></i>
                        </div>
                        <div class="text-center sm:text-left sm:ml-4">
                            <p class="text-xs sm:text-sm font-medium text-green-600">Produk Saya</p>
                            <p class="text-xs text-gray-500 hidden sm:block">Kelola inventaris</p>
                        </div>
                    </a>

                    <a href="{{ route('seller.orders.index') }}"
                        class="flex flex-col sm:flex-row items-center p-3 sm:p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <div class="flex-shrink-0 mb-2 sm:mb-0">
                            <i class="fas fa-shopping-cart text-purple-600 text-lg sm:text-xl"></i>
                        </div>
                        <div class="text-center sm:text-left sm:ml-4">
                            <p class="text-xs sm:text-sm font-medium text-purple-600">Pesanan</p>
                            <p class="text-xs text-gray-500 hidden sm:block">Proses pesanan</p>
                        </div>
                    </a>

                    <a href="{{ route('seller.sales') }}"
                        class="flex flex-col sm:flex-row items-center p-3 sm:p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                        <div class="flex-shrink-0 mb-2 sm:mb-0">
                            <i class="fas fa-chart-bar text-yellow-600 text-lg sm:text-xl"></i>
                        </div>
                        <div class="text-center sm:text-left sm:ml-4">
                            <p class="text-xs sm:text-sm font-medium text-yellow-600">Laporan Penjualan</p>
                            <p class="text-xs text-gray-500 hidden sm:block">Lihat analitik</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <!-- Monthly Sales Chart -->
            <div class="bg-white shadow rounded-lg p-3 sm:p-6">
                <h3 class="text-sm sm:text-lg font-medium text-gray-900 mb-3 sm:mb-4">Penjualan Bulanan</h3>
                <div class="relative h-32 sm:h-48">
                    <canvas id="monthlySalesChart"></canvas>
                </div>
            </div>

            <!-- Product Performance Chart -->
            <div class="bg-white shadow rounded-lg p-3 sm:p-6">
                <h3 class="text-sm sm:text-lg font-medium text-gray-900 mb-3 sm:mb-4">Performa Produk</h3>
                <div class="relative h-32 sm:h-48">
                    <canvas id="productPerformanceChart"></canvas>
                </div>
            </div>

            <!-- Order Status Distribution -->
            <div class="bg-white shadow rounded-lg p-3 sm:p-6">
                <h3 class="text-sm sm:text-lg font-medium text-gray-900 mb-3 sm:mb-4">Status Pesanan</h3>
                <div class="relative h-32 sm:h-48">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Statistik View Produk -->
        <div class="bg-white shadow rounded-lg mb-6 sm:mb-8">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base sm:text-lg font-medium text-gray-900">Statistik Dilihat Produk</h3>
            </div>
            <div class="p-3 sm:p-6">
                <canvas id="productViewsChart" height="100"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-8 mb-6 sm:mb-8">
            <!-- Recent Orders -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Pesanan Terbaru</h3>
                        <a href="{{ route('seller.orders.index') }}"
                            class="text-sm font-medium text-blue-600 hover:text-blue-500">
                            Lihat semua
                        </a>
                    </div>
                </div>
                <div class="overflow-hidden">
                    @if($recentOrders->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($recentOrders->take(5) as $order)
                        <li class="px-6 py-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-box text-gray-600 text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $order->order_number }}</p>
                                        <p class="text-xs text-gray-500">{{ $order->cart->user->nickname ?? $order->cart->user->username }}</p>
                                        <p class="text-xs text-gray-400">{{ $order->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">Rp {{
                                        number_format($order->grandtotal) }}</p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                                @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                                @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                                @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="px-6 py-8 text-center">
                        <i class="fas fa-shopping-bag text-gray-400 text-3xl mb-4"></i>
                        <p class="text-gray-500">Belum ada pesanan</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Top Selling Products -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Produk Terlaris</h3>
                </div>
                <div class="overflow-hidden">
                    @if($topProducts->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($topProducts as $product)
                        <li class="px-6 py-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($product->images->count() > 0)
                                        <img src="{{ asset('storage/' . $product->images->first()->image) }}"
                                            alt="{{ $product->productname }}" class="w-8 h-8 rounded object-cover">
                                        @else
                                        <div class="w-8 h-8 bg-gray-200 rounded flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400 text-xs"></i>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{
                                            Str::limit($product->productname, 25) }}</p>
                                        <p class="text-xs text-gray-500">Rp {{ number_format($product->productprice) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ $product->sold_quantity }} terjual
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $product->productstock }} stok</p>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="px-6 py-8 text-center">
                        <i class="fas fa-chart-bar text-gray-400 text-3xl mb-4"></i>
                        <p class="text-gray-500">Belum ada data penjualan</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        @if($lowStockProducts->count() > 0)
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                    <h3 class="text-base sm:text-lg font-medium text-gray-900">Peringatan Stok Rendah</h3>
                </div>
            </div>
            <div class="p-3 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 gap-3 sm:gap-4">
                    @foreach($lowStockProducts as $product)
                    <div class="border border-yellow-200 bg-yellow-50 rounded-lg p-3 sm:p-4">
                        <div class="flex items-center">
                            @if($product->images->count() > 0)
                            <img src="{{ asset('storage/' . $product->images->first()->image) }}"
                                alt="{{ $product->productname }}" class="w-10 h-10 sm:w-12 sm:h-12 rounded object-cover">
                            @else
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-200 rounded flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-sm"></i>
                            </div>
                            @endif
                            <div class="ml-2 sm:ml-3 flex-1">
                                <p class="text-xs sm:text-sm font-medium text-gray-900">{{ Str::limit($product->productname, 15) }}
                                </p>
                                <p class="text-xs text-gray-600">Stok: <span class="font-medium text-red-600">{{
                                        $product->productstock }}</span></p>
                                <a href="{{ route('seller.products.edit', $product) }}"
                                    class="text-xs text-blue-600 hover:text-blue-500">
                                    Perbarui Stok
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Chart Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Monthly Sales Chart
        const monthlySalesCtx = document.getElementById('monthlySalesChart').getContext('2d');
        const monthlyData = @json($monthlyRevenue);
        
        const months = [];
        const sales = [];
        
        // Prepare last 6 months data
        for (let i = 5; i >= 0; i--) {
            const date = new Date();
            date.setMonth(date.getMonth() - i);
            months.push(date.toLocaleDateString('id-ID', { month: 'short' }));
            
            // Find data for this month
            const monthData = monthlyData.find(item => 
                item.month == (date.getMonth() + 1) && item.year == date.getFullYear()
            );
            sales.push(monthData ? monthData.total : 0);
        }
        
        new Chart(monthlySalesCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Penjualan',
                    data: sales,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Penjualan: Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                        }
                    }
                }
            }
        });

        // Product Performance Chart (Top 5 products)
        const productCtx = document.getElementById('productPerformanceChart').getContext('2d');
        const topProducts = @json($topProducts);
        
        const productNames = topProducts.map(product => product.productname.substring(0, 15) + '...');
        const productSales = topProducts.map(product => product.sold_quantity || 0);
        
        new Chart(productCtx, {
            type: 'bar',
            data: {
                labels: productNames,
                datasets: [{
                    label: 'Terjual',
                    data: productSales,
                    backgroundColor: [
                        '#EF4444', '#F97316', '#EAB308', '#22C55E', '#3B82F6'
                    ],
                    borderColor: [
                        '#DC2626', '#EA580C', '#CA8A04', '#16A34A', '#2563EB'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Order Status Chart
        const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
        
        // Calculate order status distribution
        const orderStatuses = {
            pending: {{ $pendingOrders }},
            processing: 0, // Add from controller if needed
            shipped: 0,    // Add from controller if needed
            delivered: 0,  // Add from controller if needed
            completed: {{ $totalOrders - $pendingOrders }}
        };
        
        new Chart(orderStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Menunggu', 'Selesai'],
                datasets: [{
                    data: [orderStatuses.pending, orderStatuses.completed],
                    backgroundColor: [
                        '#FCD34D', // yellow
                        '#34D399'  // green
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    });
</script>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('productViewsChart').getContext('2d');
    const productViewsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topViewedProducts->pluck('productname')) !!},
            datasets: [{
                label: 'Jumlah Dilihat',
                data: {!! json_encode($topViewedProducts->pluck('view_count')) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                borderRadius: 6,
                maxBarThickness: 40
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Dilihat: ' + context.parsed.y + ' kali';
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: { display: false },
                    ticks: { autoSkip: false, maxRotation: 45, minRotation: 0 }
                },
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Jumlah Dilihat' }
                }
            }
        }
    });
</script>
@endpush
@endsection