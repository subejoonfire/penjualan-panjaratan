<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    /**
     * Display seller dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Seller statistics
        $totalProducts = $user->products()->count();
        $activeProducts = $user->products()->where('is_active', true)->count();

        $totalOrders = Order::whereHas('cart.cartDetails.product', function ($query) use ($user) {
            $query->where('iduserseller', $user->id);
        })->count();

        // Calculate total revenue from delivered orders with paid transactions
        $totalRevenue = Order::whereHas('cart.cartDetails.product', function ($query) use ($user) {
            $query->where('iduserseller', $user->id);
        })
        ->whereHas('transaction', function ($query) {
            $query->where('transactionstatus', 'paid');
        })
        ->where('status', 'delivered')
        ->get()
        ->sum(function ($order) use ($user) {
            return $order->cart->cartDetails
                ->where('product.iduserseller', $user->id)
                ->sum(function ($item) {
                    return $item->quantity * $item->productprice;
                });
        });

        $pendingOrders = Order::whereHas('cart.cartDetails.product', function ($query) use ($user) {
            $query->where('iduserseller', $user->id);
        })->where('status', 'pending')->count();

        // Recent orders for seller's products
        $recentOrders = Order::with(['cart.user', 'cart.cartDetails.product', 'transaction'])
            ->whereHas('cart.cartDetails.product', function ($query) use ($user) {
                $query->where('iduserseller', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Top selling products
        $topProducts = $user->products()
            ->withCount(['cartDetails as sold_quantity' => function ($query) {
                $query->whereHas('cart.order.transaction', function ($q) {
                    $q->where('transactionstatus', 'paid');
                });
            }])
            ->orderBy('sold_quantity', 'desc')
            ->limit(5)
            ->get();

        // Monthly revenue (last 6 months)
        $monthlyRevenue = Transaction::whereHas('order.cart.cartDetails.product', function ($query) use ($user) {
            $query->where('iduserseller', $user->id);
        })
            ->where('transactionstatus', 'paid')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw("strftime('%m', created_at) as month"),
                DB::raw("strftime('%Y', created_at) as year"),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Low stock products
        $lowStockProducts = $user->products()
            ->where('productstock', '<', 10)
            ->where('is_active', true)
            ->orderBy('productstock', 'asc')
            ->limit(5)
            ->get();

        // Statistik view produk (top 10)
        $topViewedProducts = $user->products()
            ->orderBy('view_count', 'desc')
            ->limit(10)
            ->get(['productname', 'view_count']);

        return view('seller.dashboard', compact(
            'totalProducts',
            'activeProducts',
            'totalOrders',
            'totalRevenue',
            'pendingOrders',
            'recentOrders',
            'topProducts',
            'monthlyRevenue',
            'lowStockProducts',
            'topViewedProducts'
        ));
    }

    /**
     * Display seller's products
     */
    public function products(Request $request)
    {
        $user = Auth::user();
        $query = $user->products()->with(['category', 'images'])->withSoldCount();

        // Filter by category
        if ($request->filled('category')) {
            $query->where('idcategories', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search by product name
        if ($request->filled('search')) {
            $query->where('productname', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('is_active', 'desc')->orderBy('created_at', 'desc')->paginate(12);
        $categories = Category::all();

        return view('seller.products.index', compact('products', 'categories'));
    }

    /**
     * Show form to create product
     */
    public function createProduct()
    {
        $categories = Category::all();
        return view('seller.products.create', compact('categories'));
    }

    /**
     * Store new product
     */
    public function storeProduct(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'productname' => 'required|string|max:255',
            'productdescription' => 'required|string',
            'productprice' => 'required|numeric|min:0',
            'productstock' => 'required|integer|min:0',
            'idcategories' => 'required|exists:categories,id',
            'images' => 'required|array|min:1', // minimal 1 gambar
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $product = Product::create([
            'productname' => $request->productname,
            'productdescription' => $request->productdescription,
            'productprice' => $request->productprice,
            'productstock' => $request->productstock,
            'idcategories' => $request->idcategories,
            'iduserseller' => $user->id,
            'is_active' => true
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'idproduct' => $product->id,
                    'image' => $path,
                    'is_primary' => ProductImage::where('idproduct', $product->id)->count() === 0
                ]);
            }
        }

        return redirect()->route('seller.products.index')->with('success', 'Product created successfully');
    }

    /**
     * Show form to edit product
     */
    public function editProduct(Product $product)
    {
        $user = Auth::user();

        if ($product->iduserseller !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $categories = Category::all();
        $product->load('images');

        return view('seller.products.edit', compact('product', 'categories'));
    }

    /**
     * Update product
     */
    public function updateProduct(Request $request, Product $product)
    {
        $user = Auth::user();

        if ($product->iduserseller !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'productname' => 'required|string|max:255',
            'productdescription' => 'required|string',
            'productprice' => 'required|numeric|min:0',
            'productstock' => 'required|integer|min:0',
            'idcategories' => 'required|exists:categories,id',
            'is_active' => 'required|in:0,1',
            'images' => 'array|max:5', // Maksimal 5 gambar baru
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Validasi total gambar tidak lebih dari 6
        $currentImageCount = $product->images()->count();
        $newImageCount = $request->hasFile('images') ? count($request->file('images')) : 0;
        
        if ($currentImageCount + $newImageCount > 6) {
            return back()->withErrors(['images' => 'Total gambar tidak boleh lebih dari 6. Saat ini: ' . $currentImageCount . ', akan ditambah: ' . $newImageCount]);
        }

        $product->update([
            'productname' => $request->productname,
            'productdescription' => $request->productdescription,
            'productprice' => $request->productprice,
            'productstock' => $request->productstock,
            'idcategories' => $request->idcategories,
            'is_active' => $request->is_active == '1'
        ]);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'idproduct' => $product->id,
                    'image' => $path,
                    'is_primary' => false // New images won't be primary by default
                ]);
            }
        }

        return back()->with('success', 'Product updated successfully');
    }

    /**
     * Display orders for seller's products
     */
    public function orders(Request $request)
    {
        $user = Auth::user();
        $query = Order::with(['cart.user.addresses', 'cart.cartDetails.product.images', 'transaction'])
            ->whereHas('cart.cartDetails.product', function ($q) use ($user) {
                $q->where('iduserseller', $user->id);
            });

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by order number or customer name
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('cart.user', function ($userQuery) use ($request) {
                      $userQuery->where('username', 'like', '%' . $request->search . '%')
                                ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate statistics
        $allOrders = Order::whereHas('cart.cartDetails.product', function ($q) use ($user) {
            $q->where('iduserseller', $user->id);
        })->get();

        $stats = [
            'total' => $allOrders->count(),
            'pending' => $allOrders->where('status', 'pending')->count(),
            'processing' => $allOrders->where('status', 'processing')->count(),
            'shipped' => $allOrders->where('status', 'shipped')->count(),
            'delivered' => $allOrders->where('status', 'delivered')->count(),
            'cancelled' => $allOrders->where('status', 'cancelled')->count(),
        ];

        // Calculate total revenue from delivered orders with paid transactions
        $totalRevenue = $allOrders->where('status', 'delivered')
            ->filter(function ($order) {
                return $order->transaction && $order->transaction->transactionstatus === 'paid';
            })
            ->sum(function ($order) use ($user) {
                return $order->cart->cartDetails
                    ->where('product.iduserseller', $user->id)
                    ->sum(function ($item) {
                        return $item->quantity * $item->productprice;
                    });
            });

        return view('seller.orders.index', compact('orders', 'stats', 'totalRevenue'));
    }

    /**
     * Get order details for modal
     */
    public function orderDetails(Order $order)
    {
        $user = Auth::user();

        // Check if order contains seller's products
        $hasSellerProducts = $order->cart->cartDetails()
            ->whereHas('product', function ($query) use ($user) {
                $query->where('iduserseller', $user->id);
            })->exists();

        if (!$hasSellerProducts) {
            abort(403, 'Unauthorized');
        }

        $order->load([
            'cart.user.addresses', 
            'cart.cartDetails.product.images', 
            'cart.cartDetails.product.category',
            'transaction'
        ]);

        // Get only seller's products from this order
        $sellerItems = $order->cart->cartDetails->filter(function ($item) use ($user) {
            return $item->product->iduserseller === $user->id;
        });

        return response()->json([
            'success' => true,
            'order' => $order,
            'sellerItems' => $sellerItems,
            'html' => view('seller.orders.details', compact('order', 'sellerItems'))->render()
        ]);
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $user = Auth::user();

        // Check if order contains seller's products
        $hasSellerProducts = $order->cart->cartDetails()
            ->whereHas('product', function ($query) use ($user) {
                $query->where('iduserseller', $user->id);
            })->exists();

        if (!$hasSellerProducts) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        // Cek apakah masih bisa mengupdate status (dalam 6 jam sejak update terakhir)
        $lastUpdated = $order->updated_at;
        $sixHoursAgo = now()->subHours(6);
        
        // Jika status sudah pernah diupdate dan sudah lebih dari 6 jam
        if ($lastUpdated->lt($sixHoursAgo) && $order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat mengupdate status. Sudah lebih dari 6 jam sejak update terakhir.'
            ], 400);
        }

        // Validasi transisi status yang diperbolehkan
        $allowedTransitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'], 
            'shipped' => ['delivered'],
            'delivered' => [], // Tidak bisa diubah lagi
            'cancelled' => [] // Tidak bisa diubah lagi
        ];

        $currentStatus = $order->status;
        $newStatus = $request->status;

        if (!in_array($newStatus, $allowedTransitions[$currentStatus])) {
            return response()->json([
                'success' => false,
                'message' => 'Transisi status tidak diperbolehkan dari ' . $currentStatus . ' ke ' . $newStatus
            ], 400);
        }

        // Cek apakah status bisa diupdate
        if (!$order->canUpdateStatus($request->status)) {
            return response()->json([
                'success' => false,
                'message' => 'Status tidak dapat diupdate. Sudah lebih dari 3 jam sejak update terakhir atau transisi status tidak diperbolehkan.'
            ], 400);
        }

        $order->updateStatus($request->status);

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diupdate'
        ]);
    }

    /**
     * Display sales report
     */
    public function sales(Request $request)
    {
        $user = Auth::user();

        // Date range filter
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now());

        // Sales statistics
        $totalSales = Transaction::whereHas('order.cart.cartDetails.product', function ($query) use ($user) {
            $query->where('iduserseller', $user->id);
        })
            ->where('transactionstatus', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $totalOrders = Order::whereHas('cart.cartDetails.product', function ($query) use ($user) {
            $query->where('iduserseller', $user->id);
        })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Daily sales data
        $dailySales = Transaction::whereHas('order.cart.cartDetails.product', function ($query) use ($user) {
            $query->where('iduserseller', $user->id);
        })
            ->where('transactionstatus', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw("date(created_at) as date"),
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Product sales data
        $productSales = Product::where('iduserseller', $user->id)
            ->withCount(['cartDetails as sold_quantity' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('cart.order.transaction', function ($q) use ($startDate, $endDate) {
                    $q->where('transactionstatus', 'paid')
                        ->whereBetween('created_at', [$startDate, $endDate]);
                });
            }])
            ->whereHas('cartDetails.cart.order.transaction', function ($query) use ($startDate, $endDate) {
                $query->where('transactionstatus', 'paid')
                    ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->orderBy('sold_quantity', 'desc')
            ->limit(10)
            ->get();

        return view('seller.sales.index', compact(
            'totalSales',
            'totalOrders',
            'dailySales',
            'productSales',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Upload product image
     */
    public function uploadImage(Request $request, Product $product)
    {
        $user = Auth::user();

        if ($product->iduserseller !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $path = $request->file('image')->store('products', 'public');

        ProductImage::create([
            'idproduct' => $product->id,
            'image' => $path,
            'is_primary' => $product->images()->count() === 0
        ]);

        return back()->with('success', 'Image uploaded successfully');
    }

    /**
     * Delete product image
     */
    public function deleteImage(ProductImage $image)
    {
        try {
            $user = Auth::user();

            if ($image->product->iduserseller !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            Storage::disk('public')->delete($image->image);
            $image->delete();

            return response()->json(['success' => true, 'message' => 'Gambar berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus gambar'], 500);
        }
    }

    /**
     * Set gambar produk sebagai utama
     */
    public function setPrimaryImage(ProductImage $image)
    {
        try {
            $user = Auth::user();
            if ($image->product->iduserseller !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            
            // Reset all images for this product to not primary
            ProductImage::where('idproduct', $image->idproduct)->update(['is_primary' => false]);
            
            // Set this image as primary
            $image->update(['is_primary' => true]);
            
            return response()->json(['success' => true, 'message' => 'Gambar utama berhasil diubah']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengubah gambar utama'], 500);
        }
    }

    /**
     * Display notifications for seller
     */
    public function notifications(Request $request)
    {
        $user = Auth::user();
        $query = $user->notifications();

        // Filter by read status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('readstatus', false);
            } elseif ($request->status === 'read') {
                $query->where('readstatus', true);
            }
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get notification statistics
        $stats = [
            'total' => $user->notifications()->count(),
            'unread' => $user->unread_notification_count,
            'today' => $user->notifications()->whereDate('created_at', today())->count(),
        ];

        // Get notification types for filter
        $types = $user->notifications()
            ->select('type')
            ->distinct()
            ->pluck('type')
            ->filter()
            ->toArray();

        return view('seller.notifications.index', compact('notifications', 'stats', 'types'));
    }

    /**
     * Show specific notification
     */
    public function showNotification($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($notificationId);

        // Mark as read
        if (!$notification->readstatus) {
            $notification->update(['readstatus' => true]);
        }

        return view('seller.notifications.show', compact('notification'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($notificationId);
        $notification->update(['readstatus' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications()->update(['readstatus' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Display transactions for seller
     */
    public function transactions(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::with(['order.cart.user', 'order.cart.cartDetails.product.images'])
            ->whereHas('order.cart.cartDetails.product', function ($q) use ($user) {
                $q->where('iduserseller', $user->id);
            });

        // Filter by status
        if ($request->filled('status')) {
            $query->where('transactionstatus', $request->status);
        }

        // Search by transaction number or customer name
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('transaction_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('order.cart.user', function ($userQuery) use ($request) {
                      $userQuery->where('username', 'like', '%' . $request->search . '%')
                                ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate statistics
        $allTransactions = Transaction::whereHas('order.cart.cartDetails.product', function ($q) use ($user) {
            $q->where('iduserseller', $user->id);
        })->get();

        $stats = [
            'total' => $allTransactions->count(),
            'pending' => $allTransactions->where('transactionstatus', 'pending')->count(),
            'paid' => $allTransactions->where('transactionstatus', 'paid')->count(),
            'cancelled' => $allTransactions->where('transactionstatus', 'cancelled')->count(),
            'failed' => $allTransactions->where('transactionstatus', 'failed')->count(),
        ];

        // Calculate total revenue from paid transactions
        $totalRevenue = $allTransactions->where('transactionstatus', 'paid')
            ->sum(function ($transaction) use ($user) {
                return $transaction->order->cart->cartDetails
                    ->where('product.iduserseller', $user->id)
                    ->sum(function ($item) {
                        return $item->quantity * $item->productprice;
                    });
            });

        return view('seller.transactions.index', compact('transactions', 'stats', 'totalRevenue'));
    }

    /**
     * Get transaction details for modal
     */
    public function transactionDetails(Transaction $transaction)
    {
        $user = Auth::user();

        // Check if transaction contains seller's products
        $hasSellerProducts = $transaction->order->cart->cartDetails()
            ->whereHas('product', function ($query) use ($user) {
                $query->where('iduserseller', $user->id);
            })->exists();

        if (!$hasSellerProducts) {
            abort(403, 'Unauthorized');
        }

        $transaction->load([
            'order.cart.user.addresses', 
            'order.cart.cartDetails.product.images', 
            'order.cart.cartDetails.product.category'
        ]);

        // Get only seller's products from this transaction
        $sellerItems = $transaction->order->cart->cartDetails->filter(function ($item) use ($user) {
            return $item->product->iduserseller === $user->id;
        });

        return response()->json([
            'success' => true,
            'transaction' => $transaction,
            'sellerItems' => $sellerItems,
            'html' => view('seller.transactions.details', compact('transaction', 'sellerItems'))->render()
        ]);
    }
}
