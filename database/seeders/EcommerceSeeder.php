<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\UserAddress;
use App\Models\ProductImage;
use App\Models\ProductReview;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\DetailTransaction;
use App\Models\Notification;
use App\Models\Wishlist;

class EcommerceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeder untuk mengisi data dummy aplikasi e-commerce Penjualan Panjaratan
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // Menggunakan locale Indonesia

        // 1. Seed Users dengan berbagai role
        $this->seedUsers($faker);

        // 2. Seed Categories
        $this->seedCategories();

        // 3. Seed User Addresses
        $this->seedUserAddresses($faker);

        // 4. Seed Products
        $this->seedProducts($faker);

        // 5. Seed Product Images
        $this->seedProductImages($faker);

        // 6. Seed Product Reviews
        $this->seedProductReviews($faker);

        // 7. Seed Carts & Cart Details
        $this->seedCartsAndDetails($faker);

        // 8. Seed Orders
        $this->seedOrders($faker);

        // 9. Seed Transactions
        $this->seedTransactions($faker);

        // 10. Seed Detail Transactions
        $this->seedDetailTransactions($faker);

        // 11. Seed Notifications
        $this->seedNotifications($faker);

        // 12. Seed Wishlists
        $this->seedWishlists($faker);
    }

    /**
     * Seed Users dengan role admin, seller, dan customer
     */
    private function seedUsers($faker)
    {
        // Admin user
        User::create([
            'username' => 'admin',
            'email' => 'admin@panjaratan.com',
            'phone' => '081234567890',
            'verification_token' => null,
            'phone_verification_token' => null,
            'nickname' => 'Administrator',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);

        // Seller users
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'username' => "seller{$i}",
                'email' => "seller{$i}@panjaratan.com",
                'phone' => $faker->phoneNumber,
                'verification_token' => null,
                'phone_verification_token' => null,
                'nickname' => $faker->name,
                'password' => Hash::make('seller123'),
                'role' => 'seller',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]);
        }

        // Customer users
        for ($i = 1; $i <= 20; $i++) {
            User::create([
                'username' => "customer{$i}",
                'email' => "customer{$i}@panjaratan.com",
                'phone' => $faker->phoneNumber,
                'verification_token' => null,
                'phone_verification_token' => null,
                'nickname' => $faker->name,
                'password' => Hash::make('customer123'),
                'role' => 'customer',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]);
        }
    }

    /**
     * Seed Categories produk
     */
    private function seedCategories()
    {
        $categories = [
            'Elektronik',
            'Fashion Pria',
            'Fashion Wanita',
            'Kesehatan & Kecantikan',
            'Rumah & Taman',
            'Olahraga & Outdoor',
            'Otomotif',
            'Buku & Alat Tulis',
            'Makanan & Minuman',
            'Mainan & Hobi'
        ];

        foreach ($categories as $category) {
            Category::create(['category' => $category]);
        }
    }

    /**
     * Seed User Addresses
     */
    private function seedUserAddresses($faker)
    {
        $users = User::where('role', '!=', 'admin')->get();

        foreach ($users as $user) {
            // Setiap user memiliki 1-3 alamat
            $addressCount = $faker->numberBetween(1, 3);

            for ($i = 0; $i < $addressCount; $i++) {
                UserAddress::create([
                    'iduser' => $user->id,
                    'address' => $faker->address,
                    'is_default' => $i === 0, // Alamat pertama sebagai default
                ]);
            }
        }
    }

    /**
     * Seed Products
     */
    private function seedProducts($faker)
    {
        $categories = Category::all();
        $sellers = User::where('role', 'seller')->get();

        $productNames = [
            'Smartphone Android Terbaru',
            'Laptop Gaming High Performance',
            'Kemeja Casual Pria',
            'Dress Elegant Wanita',
            'Serum Vitamin C',
            'Meja Kerja Minimalis',
            'Sepatu Lari Profesional',
            'Ban Mobil Berkualitas',
            'Novel Bestseller',
            'Kopi Premium Arabica',
            'Action Figure Collectible',
            'Headphone Wireless',
            'Tas Ransel Travel',
            'Jam Tangan Smartwatch',
            'Skincare Set Lengkap'
        ];

        foreach ($productNames as $index => $productName) {
            // Buat beberapa varian untuk setiap nama produk
            for ($variant = 1; $variant <= 3; $variant++) {
                Product::create([
                    'productname' => $productName . " - Varian {$variant}",
                    'productdescription' => $faker->paragraph(3),
                    'productprice' => $faker->numberBetween(50000, 5000000),
                    'productstock' => $faker->numberBetween(0, 100),
                    'idcategories' => $categories->random()->id,
                    'iduserseller' => $sellers->random()->id,
                    'is_active' => $faker->boolean(90),
                ]);
            }
        }
    }

    /**
     * Seed Product Images
     */
    private function seedProductImages($faker)
    {
        $products = Product::all();

        foreach ($products as $product) {
            // Setiap produk memiliki 1-4 gambar
            $imageCount = $faker->numberBetween(1, 4);

            for ($i = 0; $i < $imageCount; $i++) {
                ProductImage::create([
                    'idproduct' => $product->id,
                    'image' => 'products/' . $faker->uuid . '.jpg',
                    'is_primary' => $i === 0, // Gambar pertama sebagai primary
                ]);
            }
        }
    }

    /**
     * Seed Product Reviews
     */
    private function seedProductReviews($faker)
    {
        $products = Product::all();
        $customers = User::where('role', 'customer')->get();

        foreach ($products as $product) {
            // Setiap produk memiliki 0-10 review
            $reviewCount = $faker->numberBetween(0, 10);

            for ($i = 0; $i < $reviewCount; $i++) {
                ProductReview::create([
                    'idproduct' => $product->id,
                    'iduser' => $customers->random()->id,
                    'productreviews' => $faker->paragraph(2),
                    'rating' => $faker->numberBetween(1, 5),
                ]);
            }
        }
    }

    /**
     * Seed Carts dan Cart Details
     */
    private function seedCartsAndDetails($faker)
    {
        $customers = User::where('role', 'customer')->get();
        $products = Product::where('is_active', true)->get();

        foreach ($customers as $customer) {
            // Setiap customer memiliki 1 cart aktif
            $cart = Cart::create([
                'iduser' => $customer->id,
                'checkoutstatus' => $faker->randomElement(['active', 'completed']),
            ]);

            // Setiap cart memiliki 1-5 item
            $itemCount = $faker->numberBetween(1, 5);

            for ($i = 0; $i < $itemCount; $i++) {
                $product = $products->random();

                CartDetail::create([
                    'idcart' => $cart->id,
                    'idproduct' => $product->id,
                    'quantity' => $faker->numberBetween(1, 3),
                    'productprice' => $product->productprice,
                ]);
            }
        }
    }

    /**
     * Seed Orders
     */
    private function seedOrders($faker)
    {
        $carts = Cart::where('checkoutstatus', 'completed')->get();

        foreach ($carts as $cart) {
            $cartDetails = CartDetail::where('idcart', $cart->id)->get();
            $grandTotal = $cartDetails->sum(function ($detail) {
                return $detail->quantity * $detail->productprice;
            });

            Order::create([
                'idcart' => $cart->id,
                'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad($cart->id, 6, '0', STR_PAD_LEFT),
                'grandtotal' => $grandTotal,
                'status' => $faker->randomElement(['pending', 'processing', 'shipped', 'delivered']),
                'shipping_address' => $faker->address,
            ]);

            // Update cart status to completed
            $cart->update(['checkoutstatus' => 'completed']);
        }
    }

    /**
     * Seed Transactions
     */
    private function seedTransactions($faker)
    {
        $orders = Order::all();

        foreach ($orders as $order) {
            Transaction::create([
                'idorder' => $order->id,
                'transaction_number' => 'TRX-' . date('Ymd') . '-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                'transactionstatus' => $faker->randomElement(['pending', 'paid', 'failed']),
                'payment_method' => $faker->randomElement(['bank_transfer', 'credit_card', 'e_wallet', 'cod']),
                'amount' => $order->grandtotal,
                'paid_at' => $faker->boolean(70) ? $faker->dateTimeBetween('-30 days', 'now') : null,
            ]);
        }
    }

    /**
     * Seed Detail Transactions
     */
    private function seedDetailTransactions($faker)
    {
        $transactions = Transaction::all();

        foreach ($transactions as $transaction) {
            // Detail produk
            DetailTransaction::create([
                'idtransaction' => $transaction->id,
                'productdescription' => 'Subtotal Produk',
                'amount' => $transaction->amount * 0.9, // 90% dari total
                'type' => 'product',
            ]);

            // Detail ongkir
            DetailTransaction::create([
                'idtransaction' => $transaction->id,
                'productdescription' => 'Ongkos Kirim',
                'amount' => $transaction->amount * 0.1, // 10% dari total
                'type' => 'shipping',
            ]);
        }
    }

    /**
     * Seed Notifications
     */
    private function seedNotifications($faker)
    {
        $users = User::where('role', '!=', 'admin')->get();

        $notificationTypes = [
            'order' => [
                'Pesanan Berhasil Dibuat',
                'Pesanan Sedang Diproses',
                'Pesanan Telah Dikirim',
                'Pesanan Telah Diterima'
            ],
            'payment' => [
                'Pembayaran Berhasil',
                'Pembayaran Gagal',
                'Menunggu Konfirmasi Pembayaran'
            ],
            'product' => [
                'Produk Favorit Sedang Diskon',
                'Stok Produk Hampir Habis',
                'Produk Baru Tersedia'
            ],
            'system' => [
                'Selamat Datang di ' . env('MAIL_FROM_NAME', 'Penjualan Panjaratan'),
                'Update Sistem Terbaru',
                'Promo Spesial Bulan Ini'
            ]
        ];

        foreach ($users as $user) {
            // Setiap user memiliki 3-10 notifikasi
            $notifCount = $faker->numberBetween(3, 10);

            for ($i = 0; $i < $notifCount; $i++) {
                $type = $faker->randomElement(array_keys($notificationTypes));
                $title = $faker->randomElement($notificationTypes[$type]);

                Notification::create([
                    'iduser' => $user->id,
                    'title' => $title,
                    'notification' => $faker->paragraph(2),
                    'type' => $type,
                    'readstatus' => $faker->boolean(60),
                ]);
            }
        }
    }

    /**
     * Seed Wishlists
     */
    private function seedWishlists($faker)
    {
        $customers = User::where('role', 'customer')->get();
        $products = Product::where('is_active', true)->get();

        foreach ($customers as $customer) {
            // Tidak semua customer memiliki wishlist, hanya 70% yang memiliki wishlist
            if ($faker->boolean(70)) {
                // Setiap customer yang memiliki wishlist akan memiliki 1-8 produk dalam wishlist
                $wishlistCount = $faker->numberBetween(1, 8);
                $selectedProducts = $products->random($wishlistCount);

                foreach ($selectedProducts as $product) {
                    // Cek apakah kombinasi user_id dan product_id sudah ada (karena ada unique constraint)
                    $existingWishlist = Wishlist::where('user_id', $customer->id)
                        ->where('product_id', $product->id)
                        ->first();

                    if (!$existingWishlist) {
                        Wishlist::create([
                            'user_id' => $customer->id,
                            'product_id' => $product->id,
                            'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }

        // Tambahkan beberapa wishlist khusus untuk produk-produk populer
        $popularProducts = Product::where('is_active', true)
            ->inRandomOrder()
            ->limit(5)
            ->get();

        foreach ($popularProducts as $product) {
            // Setiap produk populer akan ditambahkan ke wishlist oleh 5-15 customer random
            $fansCount = $faker->numberBetween(5, 15);
            $randomCustomers = $customers->random($fansCount);

            foreach ($randomCustomers as $customer) {
                // Cek apakah sudah ada di wishlist
                $existingWishlist = Wishlist::where('user_id', $customer->id)
                    ->where('product_id', $product->id)
                    ->first();

                if (!$existingWishlist) {
                    Wishlist::create([
                        'user_id' => $customer->id,
                        'product_id' => $product->id,
                        'created_at' => $faker->dateTimeBetween('-2 months', 'now'),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
