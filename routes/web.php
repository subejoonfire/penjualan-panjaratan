<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| Web Routes - Aplikasi E-commerce Penjualan Panjaratan
|--------------------------------------------------------------------------
|
| Routes untuk aplikasi e-commerce dengan role-based access control
| Terdapat 3 role: admin, seller, dan customer
|
*/

// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes (Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Logout (Authenticated users only)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile routes (All authenticated users)
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users.index');

    // Product Management
    Route::get('/products', [AdminDashboardController::class, 'products'])->name('products.index');

    // Order Management
    Route::get('/orders', [AdminDashboardController::class, 'orders'])->name('orders.index');

    // Transaction Management
    Route::get('/transactions', [AdminDashboardController::class, 'transactions'])->name('transactions.index');

    // Notification Management
    Route::post('/notifications/send', [AdminDashboardController::class, 'sendNotification'])->name('notifications.send');

    // Category Management
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::post('/categories/bulk-delete', [\App\Http\Controllers\Admin\CategoryController::class, 'bulkDelete'])->name('categories.bulk-delete');
});

/*
|--------------------------------------------------------------------------
| Seller Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:seller'])->prefix('seller')->name('seller.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');

    // Product Management for Seller
    Route::get('/products', [SellerDashboardController::class, 'products'])->name('products.index');
    Route::get('/products/create', [SellerDashboardController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [SellerDashboardController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{product}/edit', [SellerDashboardController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{product}', [SellerDashboardController::class, 'updateProduct'])->name('products.update');
    Route::get('/products/images/{image}', [SellerDashboardController::class, 'deleteImage'])->name('products.images.delete');
    Route::get('/products/images/{image}/primary', [SellerDashboardController::class, 'setPrimaryImage'])->name('products.images.primary');

    // Order Management for Seller
    Route::get('/orders', [SellerDashboardController::class, 'orders'])->name('orders.index');
    Route::put('/orders/{order}/status', [SellerDashboardController::class, 'updateOrderStatus'])->name('orders.status');

    // Sales Report
    Route::get('/sales', [SellerDashboardController::class, 'sales'])->name('sales');

    // Product Image Management
    Route::post('/products/{product}/images', [SellerDashboardController::class, 'uploadImage'])->name('products.images.upload');
    Route::delete('/products/images/{image}', [SellerDashboardController::class, 'deleteImage'])->name('products.images.delete');
});

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

    // Cart Management
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update/{cartDetail}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{cartDetail}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Checkout
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [CartController::class, 'processCheckout'])->name('checkout.process');

    // Order Management for Customer
    Route::get('/orders', [CustomerDashboardController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{order}', [CustomerDashboardController::class, 'orderDetail'])->name('orders.show');
    Route::put('/orders/{order}/cancel', [CustomerDashboardController::class, 'cancelOrder'])->name('orders.cancel');

    // Address Management
    // Route::resource('addresses', \App\Http\Controllers\Customer\AddressController::class);
    // Route::put('/addresses/{address}/default', [\App\Http\Controllers\Customer\AddressController::class, 'setDefault'])->name('addresses.default');

    // Product Reviews
    Route::post('/products/{product}/reviews', [CustomerDashboardController::class, 'addReview'])->name('products.reviews.store');

    // Notifications
    Route::get('/notifications', [CustomerDashboardController::class, 'notifications'])->name('notifications.index');
    Route::put('/notifications/{notification}/read', [CustomerDashboardController::class, 'markAsRead'])->name('notifications.read');
    Route::put('/notifications/mark-all-read', [CustomerDashboardController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

/*
|--------------------------------------------------------------------------
| Order Routes (Authenticated users)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::resource('orders', OrderController::class);
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::put('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

/*
|--------------------------------------------------------------------------
| Public Routes (No authentication required)
|--------------------------------------------------------------------------
*/
// Product Catalog (Public)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{category}', [ProductController::class, 'byCategory'])->name('products.category');

// Search
Route::get('/search', [ProductController::class, 'search'])->name('products.search');

/*
|--------------------------------------------------------------------------
| API Routes (for AJAX requests)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    // Cart API
    Route::get('/cart/count', [CartController::class, 'getCartCount'])->name('cart.count');
    Route::get('/cart/items', [CartController::class, 'getCartItems'])->name('cart.items');

    // Notification API - Dihapus karena sudah menggunakan provider

    // Product search suggestions
    Route::get('/products/search/suggestions', [ProductController::class, 'searchSuggestions'])->name('products.search.suggestions');
});
