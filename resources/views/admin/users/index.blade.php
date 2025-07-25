@extends('layouts.app')

@section('title', 'Manajemen Pengguna - Dashboard Admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Manajemen Pengguna</h1>
            <p class="mt-2 text-gray-600">Kelola semua pengguna dalam sistem</p>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-64">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari Pengguna</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="Cari berdasarkan username, email, atau nickname..."
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="min-w-48">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Peran</label>
                        <select name="role" id="role" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Peran</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="seller" {{ request('role') === 'seller' ? 'selected' : '' }}>Penjual</option>
                            <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Pembeli</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'role']))
                            <a href="{{ route('admin.users.index') }}" class="ml-2 bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                                Bersihkan
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-users text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Pengguna</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $users->total() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pembeli</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $users->where('role', 'customer')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-store text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Penjual</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $users->where('role', 'seller')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-shield-alt text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Admin</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $users->where('role', 'admin')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktivitas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->username }}</div>
                                            @if($user->nickname)
                                                <div class="text-sm text-gray-500">{{ $user->nickname }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($user->role === 'admin') bg-red-100 text-red-800
                                        @elseif($user->role === 'seller') bg-purple-100 text-purple-800
                                        @else bg-blue-100 text-blue-800
                                        @endif">
                                        @if($user->role === 'admin') Admin
                                        @elseif($user->role === 'seller') Penjual
                                        @else Pembeli
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                    @if($user->phone)
                                        <div class="text-sm text-gray-500">{{ $user->phone }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($user->role === 'seller')
                                        <div>{{ $user->products_count }} produk</div>
                                    @elseif($user->role === 'customer')
                                        <div>{{ $user->carts_count }} pesanan</div>
                                    @endif
                                    <div>{{ $user->notifications_count }} notifikasi</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button class="text-blue-600 hover:text-blue-900" 
                                                onclick="showUserDetails('{{ $user->id }}')">
                                            Lihat
                                        </button>
                                        @if($user->id !== auth()->id())
                                            <button class="text-red-600 hover:text-red-900"
                                                    onclick="suspendUser('{{ $user->id }}')">
                                                Suspend
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Pengguna</h3>
                                    <p class="text-gray-600">Tidak ada pengguna yang sesuai dengan kriteria filter Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- User Details Modal -->
<div id="userModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Detail Pengguna</h3>
                <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="userDetails">
                <!-- User details will be loaded here -->
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin text-gray-400 text-2xl mb-2"></i>
                    <p class="text-gray-600">Memuat detail pengguna...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showUserDetails(userId) {
    document.getElementById('userModal').classList.remove('hidden');
    document.getElementById('userDetails').innerHTML = `
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin text-gray-400 text-2xl mb-2"></i>
            <p class="text-gray-600">Memuat detail pengguna...</p>
        </div>
    `;

    // Fetch user details via AJAX
    fetch(`{{ route('admin.users.show', '') }}/${userId}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
        .then(response => response.json())
        .then(data => {
            const user = data.user;
            const stats = data.stats;
            
            document.getElementById('userDetails').innerHTML = `
                <div class="space-y-6">
                    <!-- User Info -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center space-x-4">
                            <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                                <i class="fas fa-user text-gray-600 text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-medium text-gray-900">${user.username}</h4>
                                ${user.nickname ? `<p class="text-gray-600">${user.nickname}</p>` : ''}
                                <p class="text-sm text-gray-500">
                                    ${user.role === 'admin' ? 'Admin' : user.role === 'seller' ? 'Penjual' : 'Pembeli'}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div>
                        <h5 class="text-sm font-medium text-gray-900 mb-3">Informasi Kontak</h5>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-gray-400 w-5"></i>
                                <span class="ml-3 text-sm text-gray-900">${user.email}</span>
                            </div>
                            ${user.phone ? `
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-gray-400 w-5"></i>
                                    <span class="ml-3 text-sm text-gray-900">${user.phone}</span>
                                </div>
                            ` : ''}
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div>
                        <h5 class="text-sm font-medium text-gray-900 mb-3">Statistik</h5>
                        <div class="grid grid-cols-2 gap-4">
                            ${user.role === 'seller' ? `
                                <div class="bg-purple-50 p-3 rounded-lg">
                                    <div class="text-sm text-purple-600">Total Produk</div>
                                    <div class="text-2xl font-bold text-purple-900">${stats.total_products}</div>
                                </div>
                            ` : ''}
                            ${user.role === 'customer' ? `
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <div class="text-sm text-blue-600">Total Pesanan</div>
                                    <div class="text-2xl font-bold text-blue-900">${stats.total_orders}</div>
                                </div>
                            ` : ''}
                            <div class="bg-green-50 p-3 rounded-lg">
                                <div class="text-sm text-green-600">Total Notifikasi</div>
                                <div class="text-2xl font-bold text-green-900">${stats.total_notifications}</div>
                            </div>
                            <div class="bg-yellow-50 p-3 rounded-lg">
                                <div class="text-sm text-yellow-600">Notifikasi Belum Dibaca</div>
                                <div class="text-2xl font-bold text-yellow-900">${stats.unread_notifications}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Info -->
                    <div>
                        <h5 class="text-sm font-medium text-gray-900 mb-3">Informasi Akun</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Bergabung pada:</span>
                                <span class="text-gray-900">${new Date(user.created_at).toLocaleDateString('id-ID')}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Terakhir diperbarui:</span>
                                <span class="text-gray-900">${new Date(user.updated_at).toLocaleDateString('id-ID')}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('userDetails').innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle text-red-400 text-2xl mb-2"></i>
                    <p class="text-red-600">Gagal memuat detail pengguna.</p>
                </div>
            `;
        });
}

function closeUserModal() {
    document.getElementById('userModal').classList.add('hidden');
}

function suspendUser(userId) {
    if (confirm('Apakah Anda yakin ingin men-suspend pengguna ini?')) {
        alert('Fitur suspend pengguna akan diimplementasikan di sini');
    }
}
</script>
@endsection