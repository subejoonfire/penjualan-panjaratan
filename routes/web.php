<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

Route::get('customer/payment-method', [\App\Http\Controllers\Customer\PaymentController::class, 'getPaymentMethods'])->name('customer/payment-method');

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

    // Password Reset Routes
    Route::get('/password/reset', [\App\Http\Controllers\PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/password/send-reset-code', [\App\Http\Controllers\PasswordResetController::class, 'sendResetCode'])
        ->name('password.send-reset-code')
        ->middleware('throttle:10,1'); // Max 10 attempts per minute
    Route::get('/password/verify-code', [\App\Http\Controllers\PasswordResetController::class, 'showVerifyResetCodeForm'])->name('password.reset.verify.form');
    Route::post('/password/verify-code', [\App\Http\Controllers\PasswordResetController::class, 'verifyResetCode'])
        ->name('password.verify-reset-code')
        ->middleware('throttle:20,1'); // Max 20 attempts per minute
    Route::get('/password/reset-form', [\App\Http\Controllers\PasswordResetController::class, 'showResetPasswordForm'])->name('password.reset.form');
    Route::post('/password/reset', [\App\Http\Controllers\PasswordResetController::class, 'resetPassword'])
        ->name('password.reset')
        ->middleware('throttle:10,1'); // Max 10 attempts per minute
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
    Route::get('/notifications', [AdminDashboardController::class, 'notifications'])->name('notifications.index');
    Route::get('/notifications/{notification}', [AdminDashboardController::class, 'showNotification'])->name('notifications.show');
    Route::put('/notifications/{notification}/read', [AdminDashboardController::class, 'markAsRead'])->name('notifications.read');
    Route::put('/notifications/mark-all-read', [AdminDashboardController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
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
    Route::get('/orders/{order}/details', [SellerDashboardController::class, 'orderDetails'])->name('orders.details');
    Route::put('/orders/{order}/status', [SellerDashboardController::class, 'updateOrderStatus'])->name('orders.status');
    Route::get('/sales', [SellerDashboardController::class, 'sales'])->name('sales');
    Route::get('/transactions', [SellerDashboardController::class, 'transactions'])->name('transactions.index');
    Route::get('/transactions/{transaction}/details', [SellerDashboardController::class, 'transactionDetails'])->name('transactions.details');
    Route::post('/products/{product}/images', [SellerDashboardController::class, 'uploadImage'])->name('products.images.upload');
    Route::put('/products/images/{image}/primary', [SellerDashboardController::class, 'setPrimaryImage'])->name('products.images.primary');
    Route::get('/notifications', [SellerDashboardController::class, 'notifications'])->name('notifications.index');
    Route::get('/notifications/{notification}', [SellerDashboardController::class, 'showNotification'])->name('notifications.show');
    Route::put('/notifications/{notification}/read', [SellerDashboardController::class, 'markAsRead'])->name('notifications.read');
    Route::put('/notifications/mark-all-read', [SellerDashboardController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
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
    Route::get('/notifications/{notification}', [CustomerDashboardController::class, 'showNotification'])->name('notifications.show');
    Route::put('/notifications/{notification}/read', [CustomerDashboardController::class, 'markAsRead'])->name('notifications.read');
    Route::put('/notifications/mark-all-read', [CustomerDashboardController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Wishlist routes
    Route::get('/wishlist', [\App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add/{product}', [\App\Http\Controllers\WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/wishlist/remove/{product}', [\App\Http\Controllers\WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('/wishlist/toggle/{product}', [\App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('payments', [\App\Http\Controllers\Customer\PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/pay/{transaction}', [\App\Http\Controllers\Customer\PaymentController::class, 'pay'])->name('payments.pay');
    Route::post('payments/callback', [\App\Http\Controllers\Customer\PaymentController::class, 'callback'])->name('payments.callback');
    Route::post('checkout/direct/{productId}', [\App\Http\Controllers\CartController::class, 'directCheckout'])->name('checkout.direct');
    Route::get('/customer/payment-methods', [\App\Http\Controllers\Customer\PaymentController::class, 'getPaymentMethods']);
    Route::get('/customer/checkout', [\App\Http\Controllers\Customer\PaymentController::class, 'checkout'])->name('customer.checkout');
});

Route::middleware('auth')->group(function () {
    Route::resource('orders', OrderController::class);
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::put('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

// Verifikasi Email & WA
Route::middleware('auth')->group(function () {
    Route::get('/verify/email', [AuthController::class, 'showEmailVerificationNotice'])->name('verification.email.notice');
    Route::post('/verify/email/send', [AuthController::class, 'sendEmailVerification'])->name('verification.email.send');
    Route::post('/verify/email/check', [AuthController::class, 'checkEmailVerification'])->name('verification.email.check');

    Route::get('/verify/wa', [AuthController::class, 'showWaVerificationNotice'])->name('verification.wa.notice');
    Route::post('/verify/wa/send', [AuthController::class, 'sendWaVerification'])->name('verification.wa.send');
    Route::post('/verify/wa/check', [AuthController::class, 'checkWaVerification'])->name('verification.wa.check');
});

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{category}', [ProductController::class, 'byCategory'])->name('products.category');
Route::get('/search', [ProductController::class, 'search'])->name('products.search');

// Route API produk untuk guest dan user login
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/products', [ProductController::class, 'getProducts'])->name('products.list');
    Route::get('/products/recommended', [ProductController::class, 'getRecommendedProducts'])->name('products.recommended');
});

Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    Route::get('/cart/count', [CartController::class, 'getCartCount'])->name('cart.count');
    Route::get('/cart/items', [CartController::class, 'getCartItems'])->name('cart.items');
    Route::get('/wishlist', [\App\Http\Controllers\WishlistController::class, 'getWishlist'])->name('wishlist.list');
    Route::get('/notifications/count', function () {
        $user = auth()->user();
        return response()->json(['count' => $user->unread_notification_count]);
    })->name('notifications.count');
    Route::get('/notifications/unread', function () {
        $user = auth()->user();

        // Get recent notifications for dropdown using optimized method
        $notifications = $user->getRecentNotifications(5)->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'notification' => $notification->notification,
                'type' => $notification->type,
                'readstatus' => $notification->readstatus,
                'created_at' => $notification->created_at->diffForHumans()
            ];
        });

        // Get cart count for customer
        $cart_count = 0;
        if ($user->isCustomer()) {
            $cart = $user->activeCart;
            if ($cart) {
                $cart_count = $cart->cartDetails()->count();
            }
        }
        return response()->json([
            'count' => $user->unread_notification_count,
            'notifications' => $notifications,
            'cart_count' => $cart_count
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