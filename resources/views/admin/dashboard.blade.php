@extends('layouts.app')

@section('title', 'Admin Dashboard - ' . env('MAIL_FROM_NAME', 'Penjualan Panjaratan'))

@section('content')
<div class="py-3 sm:py-6">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="mt-1 sm:mt-2 text-sm sm:text-base text-gray-600">Selamat datang kembali, {{ auth()->user()->nickname ??
                auth()->user()->username }}!</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
            <!-- Total Users -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-3 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-users text-white text-xs sm:text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3 sm:ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Pengguna</dt>
                                <dd class="text-base sm:text-lg font-medium text-gray-900">{{ number_format($totalUsers) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-3 sm:px-5 py-2 sm:py-3">
                    <div class="text-xs sm:text-sm">
                        <span class="text-green-600 font-medium">{{ number_format($totalCustomers) }}</span>
                        <span class="text-gray-500"> pelanggan, </span>
                        <span class="text-blue-600 font-medium">{{ number_format($totalSellers) }}</span>
                        <span class="text-gray-500"> penjual</span>
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-3 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 bg-green-500 rounded-md flex items-center justify-center">
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
                        <span class="text-green-600 font-medium">{{ number_format($totalActiveProducts) }}</span>
                        <span class="text-gray-500"> produk aktif</span>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-3 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-white text-xs sm:text-sm"></i>
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
                        <span class="text-yellow-600 font-medium">{{ $orderStats['pending'] ?? 0 }}</span>
                        <span class="text-gray-500"> menunggu</span>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
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
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Pendapatan</dt>
                                <dd class="text-sm sm:text-lg font-medium text-gray-900">Rp {{ number_format($totalRevenue, 0, ',',
                                    '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-3 sm:px-5 py-2 sm:py-3">
                    <div class="text-xs sm:text-sm">
                        <span class="text-green-600 font-medium">{{ $transactionStats['paid'] ?? 0 }}</span>
                        <span class="text-gray-500"> transaksi terbayar</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <!-- Order Status Chart -->
            <div class="bg-white shadow rounded-lg p-3 sm:p-6">
                <h3 class="text-sm sm:text-lg font-medium text-gray-900 mb-3 sm:mb-4">Distribusi Status Pesanan</h3>
                <div class="relative h-48 sm:h-64">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>

            <!-- Transaction Status Chart -->
            <div class="bg-white shadow rounded-lg p-3 sm:p-6">
                <h3 class="text-sm sm:text-lg font-medium text-gray-900 mb-3 sm:mb-4">Distribusi Status Transaksi</h3>
                <div class="relative h-48 sm:h-64">
                    <canvas id="transactionStatusChart"></canvas>
                </div>
            </div>

            <!-- Monthly Revenue Trend -->
            <div class="bg-white shadow rounded-lg p-3 sm:p-6">
                <h3 class="text-sm sm:text-lg font-medium text-gray-900 mb-3 sm:mb-4">Tren Pendapatan Bulanan</h3>
                <div class="relative h-48 sm:h-64">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <!-- Recent Orders -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Pesanan Terbaru</h3>
                </div>
                <div class="overflow-hidden">
                    <div class="max-h-96 overflow-y-auto">
                        @forelse($recentOrders as $order)
                        <div class="px-6 py-4 border-b border-gray-100 last:border-b-0">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $order->order_number }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->cart->user->username ?? 'N/A' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ $order->formatted_grandtotal }}</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                            @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                            @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                        {{ $order->status_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="px-6 py-4 text-center text-gray-500">
                            Belum ada pesanan terbaru
                        </div>
                        @endforelse
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50">
                    <a href="{{ route('admin.orders.index') }}"
                        class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        Lihat semua pesanan →
                    </a>
                </div>
            </div>

            <!-- Top Products -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Produk Terlaris</h3>
                </div>
                <div class="overflow-hidden">
                    <div class="max-h-96 overflow-y-auto">
                        @forelse($topProducts as $product)
                        <div class="px-6 py-4 border-b border-gray-100 last:border-b-0">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $product->productname }}
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $product->seller->nickname ??
                                        $product->seller->username ?? 'N/A' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ $product->sold_quantity }} terjual
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $product->formatted_price }}</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="px-6 py-4 text-center text-gray-500">
                            Belum ada data penjualan
                        </div>
                        @endforelse
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50">
                    <a href="{{ route('admin.products.index') }}"
                        class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        Lihat semua produk →
                    </a>
                </div>
            </div>
        </div>

        <!-- Category Statistics -->
        <div class="bg-white shadow rounded-lg mb-6 sm:mb-8">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                <h3 class="text-base sm:text-lg font-medium text-gray-900">Ikhtisar Kategori</h3>
            </div>
            <div class="p-3 sm:p-6">
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 xl:grid-cols-10 gap-3 sm:gap-4">
                    @foreach($categoryStats as $category)
                    <div class="text-center">
                        <div
                            class="w-10 h-10 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-1 sm:mb-2">
                            <i class="fas fa-tag text-white text-sm sm:text-lg"></i>
                        </div>
                        <h4 class="text-xs sm:text-sm font-medium text-gray-900">{{ $category->category }}</h4>
                        <p class="text-xs sm:text-sm text-gray-500">{{ $category->products_count }} produk</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Order Status Chart
        const orderCtx = document.getElementById('orderStatusChart').getContext('2d');
        const orderData = @json($orderStats);
        
        new Chart(orderCtx, {
            type: 'doughnut',
            data: {
                labels: ['Menunggu', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'],
                datasets: [{
                    data: [
                        orderData.pending || 0,
                        orderData.processing || 0,
                        orderData.shipped || 0,
                        orderData.delivered || 0,
                        orderData.cancelled || 0
                    ],
                    backgroundColor: [
                        '#FCD34D', // yellow
                        '#60A5FA', // blue
                        '#A78BFA', // purple
                        '#34D399', // green
                        '#F87171'  // red
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

        // Transaction Status Chart
        const transactionCtx = document.getElementById('transactionStatusChart').getContext('2d');
        const transactionData = @json($transactionStats);
        
        new Chart(transactionCtx, {
            type: 'pie',
            data: {
                labels: ['Menunggu', 'Dibayar', 'Gagal', 'Dikembalikan'],
                datasets: [{
                    data: [
                        transactionData.pending || 0,
                        transactionData.paid || 0,
                        transactionData.failed || 0,
                        transactionData.refunded || 0
                    ],
                    backgroundColor: [
                        '#FCD34D', // yellow
                        '#34D399', // green
                        '#F87171', // red
                        '#9CA3AF'  // gray
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

        // Revenue Chart (need to get monthly data from controller)
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        
        // Get last 6 months data (you'll need to pass this from controller)
        const months = [];
        const revenues = [];
        
        // For now, create dummy data - replace with actual data from controller
        for (let i = 5; i >= 0; i--) {
            const date = new Date();
            date.setMonth(date.getMonth() - i);
            months.push(date.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' }));
            revenues.push(Math.random() * 10000000); // Replace with real data
        }
        
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: revenues,
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
                                return 'Pendapatan: Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                        }
                    }
                }
            }
        });
    });

    // Refresh dashboard data every 30 seconds
    setInterval(function() {
        // You can add AJAX calls here to refresh specific sections
        console.log('Dashboard data refresh...');
    }, 30000);
</script>
@endpush