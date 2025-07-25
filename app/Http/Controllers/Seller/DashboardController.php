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

        $totalRevenue = Transaction::whereHas('order.cart.cartDetails.product', function ($query) use ($user) {
            $query->where('iduserseller', $user->id);
        })->where('transactionstatus', 'paid')->sum('amount');

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
            ->where('stock', '<', 10)
            ->where('is_active', true)
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();

        return view('seller.dashboard', compact(
            'totalProducts',
            'activeProducts',
            'totalOrders',
            'totalRevenue',
            'pendingOrders',
            'recentOrders',
            'topProducts',
            'monthlyRevenue',
            'lowStockProducts'
        ));
    }

    /**
     * Display seller's products
     */
    public function products(Request $request)
    {
        $user = Auth::user();
        $query = $user->products()->with(['category', 'images']);

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

        $products = $query->orderBy('created_at', 'desc')->paginate(12);
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
            'description' => 'required|string',
            'productprice' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'idcategories' => 'required|exists:categories,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $product = Product::create([
            'productname' => $request->productname,
            'description' => $request->description,
            'productprice' => $request->productprice,
            'stock' => $request->stock,
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
                    'imageurl' => $path,
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
            'description' => 'required|string',
            'productprice' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'idcategories' => 'required|exists:categories,id',
            'is_active' => 'boolean',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $product->update([
            'productname' => $request->productname,
            'description' => $request->description,
            'productprice' => $request->productprice,
            'stock' => $request->stock,
            'idcategories' => $request->idcategories,
            'is_active' => $request->has('is_active')
        ]);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'idproduct' => $product->id,
                    'imageurl' => $path,
                    'is_primary' => $product->images()->count() === 0
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
        $query = Order::with(['cart.user', 'cart.cartDetails.product', 'transaction'])
            ->whereHas('cart.cartDetails.product', function ($q) use ($user) {
                $q->where('iduserseller', $user->id);
            });

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('seller.orders.index', compact('orders'));
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
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated successfully');
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
            ->having('sold_quantity', '>', 0)
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
            'imageurl' => $path,
            'is_primary' => $product->images()->count() === 0
        ]);

        return back()->with('success', 'Image uploaded successfully');
    }

    /**
     * Delete product image
     */
    public function deleteImage(ProductImage $image)
    {
        $user = Auth::user();

        if ($image->product->iduserseller !== $user->id) {
            abort(403, 'Unauthorized');
        }

        Storage::disk('public')->delete($image->imageurl);
        $image->delete();

        return back()->with('success', 'Image deleted successfully');
    }
}