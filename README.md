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

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
