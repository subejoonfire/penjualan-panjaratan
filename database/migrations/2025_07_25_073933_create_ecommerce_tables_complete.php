<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migration untuk semua tabel aplikasi e-commerce Penjualan Panjaratan
     */
    public function up(): void
    {
        // Tabel Categories - Kategori produk
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('category', 100)->unique(); // Nama kategori
            $table->timestamps();
        });

        // Tabel UserAddresses - Alamat pengguna
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iduser')->constrained('users')->onDelete('cascade'); // Relasi ke users
            $table->text('address'); // Alamat lengkap
            $table->boolean('is_default')->default(false); // Alamat utama
            $table->timestamps();
        });

        // Tabel Products - Produk
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('productname', 200); // Nama produk
            $table->text('productdescription'); // Deskripsi produk
            $table->decimal('productprice', 12, 2); // Harga produk
            $table->integer('productstock')->default(0); // Stok produk
            $table->foreignId('idcategories')->constrained('categories')->onDelete('cascade'); // Relasi ke categories
            $table->foreignId('iduserseller')->constrained('users')->onDelete('cascade'); // Relasi ke users (seller)
            $table->boolean('is_active')->default(true); // Status aktif produk
            $table->integer('view_count')->default(0); // Jumlah dilihat

            $table->timestamps();
        });

        // Tabel ProductImages - Gambar produk
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idproduct')->constrained('products')->onDelete('cascade'); // Relasi ke products
            $table->string('image', 255); // Path gambar
            $table->boolean('is_primary')->default(false); // Gambar utama
            $table->timestamps();
        });

        // Tabel ProductReviews - Review produk
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idproduct')->constrained('products')->onDelete('cascade'); // Relasi ke products
            $table->foreignId('iduser')->constrained('users')->onDelete('cascade'); // Relasi ke users (reviewer)
            $table->text('productreviews'); // Isi review
            $table->integer('rating')->default(5); // Rating 1-5
            $table->timestamps();
        });

        // Tabel Carts - Keranjang belanja
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iduser')->constrained('users')->onDelete('cascade'); // Relasi ke users
            $table->enum('checkoutstatus', ['active', 'completed'])->default('active'); // Status checkout
            $table->timestamps();
        });

        // Tabel CartDetails - Detail keranjang
        Schema::create('cart_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idcart')->constrained('carts')->onDelete('cascade'); // Relasi ke carts
            $table->foreignId('idproduct')->constrained('products')->onDelete('cascade'); // Relasi ke products
            $table->integer('quantity')->default(1); // Jumlah produk
            $table->decimal('productprice', 12, 2); // Harga saat ditambahkan ke cart
            $table->timestamps();
        });

        // Tabel Orders - Pesanan
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idcart')->constrained('carts')->onDelete('cascade'); // Relasi ke carts
            $table->string('order_number', 50)->unique(); // Nomor pesanan
            $table->decimal('grandtotal', 12, 2); // Total harga
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending'); // Status pesanan
            $table->text('shipping_address'); // Alamat pengiriman
            $table->timestamp('status_updated_at')->nullable(); // Waktu terakhir update status
            $table->timestamps();
        });

        // Tabel Transactions - Transaksi
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idorder')->constrained('orders')->onDelete('cascade'); // Relasi ke orders
            $table->string('transaction_number', 50)->unique(); // Nomor transaksi
            $table->enum('transactionstatus', ['pending', 'paid', 'failed', 'refunded'])->default('pending'); // Status transaksi
            $table->enum('payment_method', ['bank_transfer', 'credit_card', 'e_wallet', 'cod'])->default('bank_transfer'); // Metode pembayaran
            $table->decimal('amount', 12, 2); // Jumlah pembayaran
            $table->timestamp('paid_at')->nullable(); // Waktu pembayaran
            $table->timestamps();
        });

        // Tabel DetailTransactions - Detail transaksi
        Schema::create('detail_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idtransaction')->constrained('transactions')->onDelete('cascade'); // Relasi ke transactions
            $table->string('productdescription', 255); // Deskripsi detail
            $table->decimal('amount', 12, 2); // Jumlah
            $table->enum('type', ['product', 'shipping', 'tax', 'discount'])->default('product'); // Tipe detail
            $table->timestamps();
        });

        // Tabel Notifications - Notifikasi
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iduser')->constrained('users')->onDelete('cascade'); // Relasi ke users
            $table->string('title', 200); // Judul notifikasi
            $table->text('notification'); // Isi notifikasi
            $table->enum('type', ['order', 'payment', 'product', 'system'])->default('system'); // Tipe notifikasi
            $table->boolean('readstatus')->default(false); // Status baca
            $table->timestamps();
        });

        // Tabel Wishlists - Daftar keinginan/favorit produk
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relasi ke users
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Relasi ke products
            $table->timestamps();
            
            // Ensure user can't add same product to wishlist twice
            $table->unique(['user_id', 'product_id']);
        });

        // Add soft deletes to categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to user_addresses table
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to products table
        Schema::table('products', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to product_images table
        Schema::table('product_images', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to product_reviews table
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to carts table
        Schema::table('carts', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to cart_details table
        Schema::table('cart_details', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to transactions table
        Schema::table('transactions', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to detail_transactions table
        Schema::table('detail_transactions', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to notifications table
        Schema::table('notifications', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to wishlists table
        Schema::table('wishlists', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order due to foreign key constraints
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('detail_transactions');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart_details');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('user_addresses');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('users');
        
        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('detail_transactions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('cart_details', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};