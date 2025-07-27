@extends('layouts.app')

@section('title', 'Profil - Penjualan Panjaratan')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Pengaturan Profil</h1>
            <p class="mt-2 text-gray-600">Kelola informasi akun dan preferensi Anda</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Summary Card -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-20 w-20">
                                <div class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600 text-2xl"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ $user->username }}</h3>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($user->role === 'admin') bg-red-100 text-red-800
                                    @elseif($user->role === 'seller') bg-blue-100 text-blue-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    @switch($user->role)
                                        @case('admin')
                                            Admin
                                            @break
                                        @case('seller')
                                            Penjual
                                            @break
                                        @case('customer')
                                            Pembeli
                                            @break
                                        @default
                                            {{ ucfirst($user->role) }}
                                    @endswitch
                                </span>
                            </div>
                        </div>
                        
                        <div class="mt-6 border-t border-gray-200 pt-6">
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Anggota sejak</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->created_at->format('d F Y') }}</dd>
                                </div>
                                @if($user->phone)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Telepon</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->phone }}</dd>
                                </div>
                                @endif
                                @if($user->nickname)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nama Panggilan</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->nickname }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        @if($user->role === 'seller')
                        <div class="mt-6 border-t border-gray-200 pt-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Statistik Penjual</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-blue-600">{{ $user->products()->count() }}</div>
                                    <div class="text-xs text-gray-500">Produk</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-bold text-green-600">{{ $user->products()->where('is_active', true)->count() }}</div>
                                    <div class="text-xs text-gray-500">Aktif</div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($user->role === 'customer')
                        <div class="mt-6 border-t border-gray-200 pt-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Statistik Pembeli</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-blue-600">{{ $user->carts()->whereHas('order')->count() }}</div>
                                    <div class="text-xs text-gray-500">Pesanan</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-bold text-purple-600">{{ $user->reviews()->count() }}</div>
                                    <div class="text-xs text-gray-500">Ulasan</div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informasi Profil</h3>
                        <p class="text-sm text-gray-600">Perbarui informasi akun Anda</p>
                    </div>
                    
                    <form action="{{ route('profile.update') }}" method="POST" class="px-6 py-6 space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Pengguna <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="username" id="username" 
                                   value="{{ old('username', $user->username) }}" required
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
                                          @error('username') border-red-300 @enderror">
                            @error('username')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" 
                                   value="{{ old('email', $user->email) }}" required
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
                                          @error('email') border-red-300 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nickname -->
                        <div>
                            <label for="nickname" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Tampilan / Nama Panggilan
                            </label>
                            <input type="text" name="nickname" id="nickname" 
                                   value="{{ old('nickname', $user->nickname) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
                                          @error('nickname') border-red-300 @enderror"
                                   placeholder="Bagaimana Anda ingin ditampilkan">
                            @error('nickname')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Ini akan ditampilkan menggantikan nama pengguna Anda di beberapa tempat
                            </p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Telepon
                            </label>
                            <input type="tel" name="phone" id="phone" 
                                   value="{{ old('phone', $user->phone) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
                                          @error('phone') border-red-300 @enderror"
                                   placeholder="08123456789">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-save mr-2"></i>
                                Perbarui Profil
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Password Section -->
                <div class="mt-8 bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Ubah Kata Sandi</h3>
                        <p class="text-sm text-gray-600">Perbarui kata sandi Anda untuk menjaga keamanan akun</p>
                    </div>
                    
                    <form action="{{ route('profile.update') }}" method="POST" class="px-6 py-6 space-y-6">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="change_password" value="1">

                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Kata Sandi Saat Ini <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="current_password" id="current_password" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
                                          @error('current_password') border-red-300 @enderror">
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Kata Sandi Baru <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="new_password" id="new_password" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
                                          @error('new_password') border-red-300 @enderror">
                            @error('new_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm New Password -->
                        <div>
                            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                <i class="fas fa-key mr-2"></i>
                                Ubah Kata Sandi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection