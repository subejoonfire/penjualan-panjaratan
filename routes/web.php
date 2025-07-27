<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'seller':
                return redirect()->route('seller.dashboard');
            case 'customer':
                return redirect()->route('customer.dashboard');
            default:
                return redirect()->route('products.index');
        }
    }
    return redirect()->route('products.index');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users.index');
    Route::get('/products', [AdminDashboardController::class, 'products'])->name('products.index');
    Route::post('/products/{product}/toggle-status', [AdminDashboardController::class, 'toggleProductStatus'])->name('products.toggle-status');
    Route::get('/orders', [AdminDashboardController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{order}/details', [AdminDashboardController::class, 'orderDetails'])->name('orders.details');
    Route::put('/orders/{order}/status', [AdminDashboardController::class, 'updateOrderStatus'])->name('orders.update-status');
    Route::get('/transactions', [AdminDashboardController::class, 'transactions'])->name('transactions.index');
    Route::get('/transactions/{transaction}/details', [AdminDashboardController::class, 'transactionDetails'])->name('transactions.details');
    Route::put('/transactions/{transaction}/status', [AdminDashboardController::class, 'updateTransactionStatus'])->name('transactions.update-status');
    Route::delete('/transactions/{transaction}', [AdminDashboardController::class, 'deleteTransaction'])->name('transactions.destroy');
    Route::get('/transactions/export', [AdminDashboardController::class, 'exportTransactions'])->name('transactions.export');
    Route::post('/notifications/send', [AdminDashboardController::class, 'sendNotification'])->name('notifications.send');
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::post('/categories/bulk-delete', [\App\Http\Controllers\Admin\CategoryController::class, 'bulkDelete'])->name('categories.bulk-delete');
});

Route::middleware(['auth', 'role:seller'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/products', [SellerDashboardController::class, 'products'])->name('products.index');
    Route::get('/products/create', [SellerDashboardController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [SellerDashboardController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{product}/edit', [SellerDashboardController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{product}', [SellerDashboardController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/images/{image}', [SellerDashboardController::class, 'deleteImage'])->name('products.images.delete');
    Route::get('/orders', [SellerDashboardController::class, 'orders'])->name('orders.index');
    Route::put('/orders/{order}/status', [SellerDashboardController::class, 'updateOrderStatus'])->name('orders.status');
    Route::get('/sales', [SellerDashboardController::class, 'sales'])->name('sales');
    Route::post('/products/{product}/images', [SellerDashboardController::class, 'uploadImage'])->name('products.images.upload');
    Route::delete('/products/images/{image}', [SellerDashboardController::class, 'deleteImage'])->name('products.images.delete');
});

Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update/{cartDetail}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{cartDetail}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [CartController::class, 'processCheckout'])->name('checkout.process');
    Route::get('/orders', [CustomerDashboardController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{order}', [CustomerDashboardController::class, 'orderDetail'])->name('orders.show');
    Route::put('/orders/{order}/cancel', [CustomerDashboardController::class, 'cancelOrder'])->name('orders.cancel');
    Route::post('/products/{product}/reviews', [CustomerDashboardController::class, 'addReview'])->name('products.reviews.store');
    Route::get('/notifications', [CustomerDashboardController::class, 'notifications'])->name('notifications.index');
    Route::put('/notifications/{notification}/read', [CustomerDashboardController::class, 'markAsRead'])->name('notifications.read');
    Route::put('/notifications/mark-all-read', [CustomerDashboardController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    
    // Wishlist routes
    Route::get('/wishlist', [\App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add/{product}', [\App\Http\Controllers\WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/wishlist/remove/{product}', [\App\Http\Controllers\WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('/wishlist/toggle/{product}', [\App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

Route::middleware('auth')->group(function () {
    Route::resource('orders', OrderController::class);
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::put('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{category}', [ProductController::class, 'byCategory'])->name('products.category');
Route::get('/search', [ProductController::class, 'search'])->name('products.search');

Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    Route::get('/cart/count', [CartController::class, 'getCartCount'])->name('cart.count');
    Route::get('/cart/items', [CartController::class, 'getCartItems'])->name('cart.items');
    Route::get('/notifications/unread', function () {
        $user = auth()->user();
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'notification' => $notification->notification,
                    'type' => $notification->type,
                    'readstatus' => $notification->readstatus,
                    'created_at' => $notification->created_at->diffForHumans()
                ];
            });
        
        return response()->json([
            'count' => $user->notifications()->where('readstatus', false)->count(), // Show total count, not just unread
            'notifications' => $notifications
        ]);
    })->name('notifications.unread');
    
    Route::put('/notifications/{notification}/read', function ($notificationId) {
        $user = auth()->user();
        $notification = $user->notifications()->findOrFail($notificationId);
        $notification->update(['readstatus' => true]);
        
        return response()->json(['success' => true]);
    })->name('notifications.read');
    
    Route::get('/products/search/suggestions', [ProductController::class, 'searchSuggestions'])->name('products.search.suggestions');
});