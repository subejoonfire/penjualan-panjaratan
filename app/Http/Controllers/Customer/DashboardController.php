<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Notification;
use App\Models\ProductReview;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show customer dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's cart details for cart summary
        $cart = $user->cart;
        $cartItems = [];
        $cartTotal = 0;
        $cartItemsCount = 0;
        
        if ($cart) {
            $cartItems = $cart->cartDetails()->with('product.images')->get();
            $cartTotal = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->productprice;
            });
            $cartItemsCount = $cartItems->sum('quantity'); // Total quantity of all items
        }
        
        // Get orders statistics
        $userOrders = $user->orders();
        $totalOrders = $userOrders->count();
        $pendingOrders = $userOrders->where('status', 'pending')->count();
        $totalSpent = $userOrders->sum('grandtotal');
        
        // Get recent orders
        $recentOrders = $user->orders()
            ->with(['cart.cartDetails.product.images', 'transaction'])
            ->latest()
            ->limit(5)
            ->get();
            
        // Get favorite products (products that user has reviewed)
        $favoriteProducts = \App\Models\Product::whereHas('reviews', function($query) use ($user) {
            $query->where('iduser', $user->id);
        })
        ->with(['images', 'reviews'])
        ->limit(6)
        ->get();
            
        // Get wishlist count
        $wishlistCount = $user->wishlists()->count();
        
        // Get notification data for dashboard
        $unreadNotifications = $user->unread_notification_count;
        
        return view('customer.dashboard', compact(
            'cartItems', 
            'cartTotal', 
            'cartItemsCount',
            'totalOrders',
            'totalSpent', 
            'pendingOrders',
            'recentOrders', 
            'favoriteProducts',
            'wishlistCount',
            'unreadNotifications'
        ));
    }
    
    /**
     * Display customer orders
     */
    public function orders(Request $request)
    {
        $user = Auth::user();
        $query = Order::with(['cart.cartDetails.product', 'transaction'])
            ->whereHas('cart', function($q) use ($user) {
                $q->where('iduser', $user->id);
            });
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('customer.orders.index', compact('orders'));
    }
    
    /**
     * Show order detail
     */
    public function orderDetail(Order $order)
    {
        $user = Auth::user();
        
        // Check if order belongs to user
        if ($order->cart->iduser !== $user->id) {
            abort(403, 'Unauthorized');
        }
        
        $order->load(['cart.cartDetails.product.images', 'transaction']);
        
        return view('customer.orders.show', compact('order'));
    }
    
    /**
     * Cancel order
     */
    public function cancelOrder(Order $order)
    {
        $user = Auth::user();
        
        // Check if order belongs to user
        if ($order->cart->iduser !== $user->id) {
            abort(403, 'Unauthorized');
        }
        
        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'processing'])) {
            return back()->with('error', 'Order cannot be cancelled');
        }
        
        $order->update(['status' => 'cancelled']);
        
        // Create notification
        Notification::create([
            'iduser' => $user->id,
            'title' => 'Order Cancelled',
            'notification' => 'Your order #' . $order->order_number . ' has been cancelled',
            'type' => 'order',
            'readstatus' => false
        ]);
        
        return back()->with('success', 'Pesanan berhasil dibatalkan');
    }
    
    /**
     * Add product review
     */
    public function addReview(Request $request, Product $product)
    {
        $user = Auth::user();
        
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'productreviews' => 'nullable|string|max:1000'
        ]);
        
        // Check if user has purchased this product (has delivered order)
        $hasPurchased = Order::whereHas('cart', function($query) use ($user) {
            $query->where('iduser', $user->id);
        })->whereHas('cart.cartDetails', function($query) use ($product) {
            $query->where('idproduct', $product->id);
        })->where('status', 'delivered')->exists();
        
        if (!$hasPurchased) {
            return back()->with('error', 'Anda hanya dapat memberikan ulasan untuk produk yang telah Anda beli dan terima');
        }
        
        // Check if user already reviewed this product
        $existingReview = ProductReview::where('iduser', $user->id)
            ->where('idproduct', $product->id)
            ->first();
        
        if ($existingReview) {
            $existingReview->update([
                'rating' => $request->rating,
                'productreviews' => $request->productreviews
            ]);
            
            return back()->with('success', 'Ulasan berhasil diperbarui');
        } else {
            ProductReview::create([
                'iduser' => $user->id,
                'idproduct' => $product->id,
                'rating' => $request->rating,
                'productreviews' => $request->productreviews
            ]);
            
            return back()->with('success', 'Ulasan berhasil ditambahkan');
        }
    }
    
    /**
     * Display notifications
     */
    public function notifications()
    {
        $user = Auth::user();
        $notifications = $user->notifications()
            ->latest()
            ->paginate(10);
            
        return view('customer.notifications.index', compact('notifications'));
    }
    
    /**
     * Show notification detail
     */
    public function showNotification(Notification $notification)
    {
        $user = Auth::user();
        
        // Check if notification belongs to the user
        if ($notification->iduser !== $user->id) {
            abort(403, 'Unauthorized');
        }
        
        // Mark as read when viewed
        if (!$notification->readstatus) {
            $notification->update(['readstatus' => true]);
        }
        
        return view('customer.notifications.show', compact('notification'));
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        $user = Auth::user();
        
        if ($notification->iduser !== $user->id) {
            abort(403, 'Unauthorized');
        }
        
        $notification->update(['readstatus' => true]);
        
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', 'Notifikasi ditandai sebagai telah dibaca');
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        $user->unreadNotifications()->update(['readstatus' => true]);
        
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', 'Semua notifikasi ditandai sebagai telah dibaca');
    }
}
