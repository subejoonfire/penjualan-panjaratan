# Penjualan Panjaratan - E-commerce Application

Aplikasi e-commerce lengkap yang dibangun dengan Laravel 11, menampilkan sistem multi-role dengan fitur modern dan responsive menggunakan Tailwind CSS.

## 🚀 Fitur Utama

### Role-Based Access Control
- **Admin**: Dashboard lengkap, manajemen user, produk, pesanan, dan transaksi
- **Seller**: Manajemen produk, pesanan, dan laporan penjualan
- **Customer**: Belanja, keranjang, checkout, riwayat pesanan, dan review

### Fitur E-commerce
- ✅ Katalog produk dengan kategori
- ✅ Sistem keranjang belanja
- ✅ Proses checkout yang mudah
- ✅ Manajemen pesanan
- ✅ Sistem pembayaran multi-metode
- ✅ Review dan rating produk
- ✅ Sistem notifikasi
- ✅ Manajemen alamat pengguna
- ✅ Upload gambar produk
- ✅ Dashboard statistik untuk setiap role

## 🛠 Teknologi yang Digunakan

- **Backend**: Laravel 11
- **Frontend**: Blade Templates + Tailwind CSS
- **JavaScript**: Alpine.js
- **Database**: SQLite (default) / MySQL
- **Icons**: Font Awesome
- **Authentication**: Laravel Auth

## 📋 Struktur Database

### Entitas Utama
- `users` - Data pengguna dengan role (admin, seller, customer)
- `categories` - Kategori produk
- `products` - Data produk
- `product_images` - Gambar produk
- `product_reviews` - Review produk
- `user_addresses` - Alamat pengguna
- `carts` & `cart_details` - Keranjang belanja
- `orders` - Pesanan
- `transactions` & `detail_transactions` - Transaksi
- `notifications` - Notifikasi pengguna

## 🚀 Cara Instalasi

### 1. Clone Repository
```bash
git clone <repository-url>
cd penjualan-panjaratan
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Setup Database
```bash
# Untuk SQLite (default)
touch database/database.sqlite

# Atau edit .env untuk MySQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=penjualan_panjaratan
# DB_USERNAME=root
# DB_PASSWORD=
```

### 5. Jalankan Migration & Seeder
```bash
php artisan migrate --seed
```

### 6. Setup Storage
```bash
php artisan storage:link
```

### 7. Jalankan Aplikasi
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## 👤 Akun Demo

Setelah menjalankan seeder, Anda dapat login dengan akun berikut:

### Admin
- **Email**: admin@panjaratan.com
- **Password**: admin123

### Seller
- **Email**: seller1@panjaratan.com
- **Password**: seller123

### Customer
- **Email**: customer1@panjaratan.com (atau customer2, customer3, dst.)
- **Password**: customer123

## 📁 Struktur Proyek

```
penjualan-panjaratan/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/           # Controller untuk admin
│   │   │   ├── Seller/          # Controller untuk seller
│   │   │   ├── Customer/        # Controller untuk customer
│   │   │   └── AuthController.php
│   │   └── Middleware/
│   │       └── CheckRole.php    # Middleware role-based access
│   └── Models/                  # Model dengan relasi lengkap
├── database/
│   ├── migrations/              # Migration database
│   └── seeders/                 # Seeder dengan data dummy
├── resources/
│   └── views/
│       ├── layouts/             # Layout master
│       ├── auth/                # Halaman auth
│       ├── admin/               # Views admin
│       ├── seller/              # Views seller
│       └── customer/            # Views customer
└── routes/
    └── web.php                  # Routes dengan middleware
```

## 🎨 Fitur UI/UX

- **Responsive Design**: Menggunakan Tailwind CSS
- **Modern Interface**: Clean dan user-friendly
- **Role-based Navigation**: Menu berbeda untuk setiap role
- **Real-time Notifications**: Sistem notifikasi terintegrasi
- **Interactive Components**: Menggunakan Alpine.js
- **Loading States**: Feedback visual untuk user
- **Error Handling**: Pesan error yang informatif

## 🔧 Fitur Tambahan

### Untuk Admin
- Dashboard statistik lengkap
- Manajemen user (CRUD)
- Manajemen produk dan kategori
- Monitoring pesanan dan transaksi
- Sistem broadcast notifikasi

### Untuk Seller
- Dashboard penjualan
- Manajemen produk pribadi
- Upload multiple gambar produk
- Tracking pesanan
- Laporan penjualan

### Untuk Customer
- Katalog produk dengan filter
- Keranjang belanja interaktif
- Multiple alamat pengiriman
- Riwayat pesanan
- Sistem review dan rating
- Notifikasi real-time

## 🛡 Keamanan

- **CSRF Protection**: Token CSRF pada semua form
- **Role-based Access**: Middleware untuk kontrol akses
- **Input Validation**: Validasi komprehensif
- **Password Hashing**: Menggunakan bcrypt
- **SQL Injection Protection**: Eloquent ORM

## 📱 API Endpoints

Aplikasi menyediakan API endpoints untuk AJAX requests:

- `GET /api/cart/count` - Jumlah item di keranjang
- `GET /api/cart/items` - Data item keranjang
- `GET /api/notifications/unread` - Notifikasi belum dibaca
- `GET /api/products/search/suggestions` - Saran pencarian produk

## 🎯 Testing

Untuk testing aplikasi:

1. **Manual Testing**: Gunakan akun demo yang tersedia
2. **Feature Testing**: Test setiap fitur dengan role berbeda
3. **Responsive Testing**: Test di berbagai ukuran layar

## 📈 Pengembangan Lanjutan

Fitur yang bisa ditambahkan:
- Payment Gateway Integration (Midtrans, etc.)
- Email Notifications
- SMS Notifications
- Wishlist/Favorites
- Discount/Coupon System
- Advanced Search & Filters
- Product Variants
- Inventory Management
- Shipping Calculator
- Multi-vendor Support

## 🤝 Kontribusi

1. Fork repository
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 Lisensi

Distributed under the MIT License. See `LICENSE` for more information.

## 📞 Kontak

- **Developer**: [Your Name]
- **Email**: [your.email@example.com]
- **Project Link**: [https://github.com/yourusername/penjualan-panjaratan]

---

**Catatan**: Aplikasi ini dibuat untuk tujuan pembelajaran dan demonstrasi. Untuk penggunaan production, pastikan untuk melakukan security audit dan performance optimization yang diperlukan.
