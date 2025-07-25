<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductReview;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of products (public)
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images', 'seller'])
            ->where('is_active', true)
            ->where('productstock', '>', 0);

        // Search by product name
        if ($request->filled('search')) {
            $query->where('productname', 'like', '%' . $request->search . '%');
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('idcategories', $request->category);
        }
        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('productprice', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('productprice', '<=', $request->max_price);
        }
        // Sort products
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('productprice', 'asc');
                break;
            case 'price_high':
                $query->orderBy('productprice', 'desc');
                break;
            case 'name':
                $query->orderBy('productname', 'asc');
                break;
            case 'popular':
                $query->withCount(['cartDetails as sold_count' => function ($q) {
                    $q->whereHas('cart.order.transaction', function ($transaction) {
                        $transaction->where('transactionstatus', 'paid');
                    });
                }])->orderBy('sold_count', 'desc');
                break;
            default: // latest
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12);
        $categories = Category::withCount('products')->get();

        // Get price range for filters
        $priceRange = Product::where('is_active', true)
            ->where('productstock', '>', 0)
            ->selectRaw('MIN(productprice) as min_price, MAX(productprice) as max_price')
            ->first();
        return view('products.index', compact(
            'products',
            'categories',
            'priceRange',
            'sortBy'
        ));
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        // Check if product is active
        if (!$product->is_active) {
            abort(404, 'Product not found');
        }

        $product->load([
            'category',
            'seller',
            'images',
            'reviews.user'
        ]);

        // Get related products from same category
        $relatedProducts = Product::where('idcategories', $product->idcategories)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('productstock', '>', 0)
            ->with(['images'])
            ->limit(4)
            ->get();

        // Calculate average rating
        $averageRating = $product->reviews()->avg('rating') ?? 0;
        $totalReviews = $product->reviews()->count();

        // Get rating distribution
        $ratingDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $product->reviews()->where('rating', $i)->count();
            $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
            $ratingDistribution[$i] = [
                'count' => $count,
                'percentage' => $percentage
            ];
        }

        // Check if user can review this product
        $canReview = false;
        if (auth()->check() && auth()->user()->isCustomer()) {
            $hasPurchased = auth()->user()->carts()
                ->whereHas('order.transaction', function ($query) {
                    $query->where('transactionstatus', 'paid');
                })
                ->whereHas('cartDetails', function ($query) use ($product) {
                    $query->where('idproduct', $product->id);
                })
                ->exists();

            $hasReviewed = $product->reviews()
                ->where('iduser', auth()->id())
                ->exists();

            $canReview = $hasPurchased && !$hasReviewed;
        }

        return view('products.show', compact(
            'product',
            'relatedProducts',
            'averageRating',
            'totalReviews',
            'ratingDistribution',
            'canReview'
        ));
    }

    /**
     * Display products by category
     */
    public function byCategory(Category $category, Request $request)
    {
        $query = $category->products()
            ->with(['images', 'seller'])
            ->where('is_active', true)
            ->where('productstock', '>', 0);

        // Search within category
        if ($request->filled('search')) {
            $query->where('productname', 'like', '%' . $request->search . '%');
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('productprice', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('productprice', '<=', $request->max_price);
        }

        // Sort products
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('productprice', 'asc');
                break;
            case 'price_high':
                $query->orderBy('productprice', 'desc');
                break;
            case 'name':
                $query->orderBy('productname', 'asc');
                break;
            case 'popular':
                $query->withCount(['cartDetails as sold_count' => function ($q) {
                    $q->whereHas('cart.order.transaction', function ($transaction) {
                        $transaction->where('transactionstatus', 'paid');
                    });
                }])->orderBy('sold_count', 'desc');
                break;
            default: // latest
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12);
        $categories = Category::withCount('products')->get();

        // Get price range for this category
        $priceRange = $category->products()
            ->where('is_active', true)
            ->where('productstock', '>', 0)
            ->selectRaw('MIN(productprice) as min_price, MAX(productprice) as max_price')
            ->first();

        return view('products.category', compact(
            'products',
            'category',
            'categories',
            'priceRange',
            'sortBy'
        ));
    }

    /**
     * Search products
     */
    public function search(Request $request)
    {
        $searchTerm = $request->get('q', '');

        if (empty($searchTerm)) {
            return redirect()->route('products.index');
        }

        $query = Product::with(['category', 'images', 'seller'])
            ->where('is_active', true)
            ->where('productstock', '>', 0);

        // Search in product name and description
        $query->where(function ($q) use ($searchTerm) {
            $q->where('productname', 'like', '%' . $searchTerm . '%')
                ->orWhere('productdescription', 'like', '%' . $searchTerm . '%');
        });

        // Filter by category
        if ($request->filled('category')) {
            $query->where('idcategories', $request->category);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('productprice', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('productprice', '<=', $request->max_price);
        }

        // Sort products
        $sortBy = $request->get('sort', 'relevance');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('productprice', 'asc');
                break;
            case 'price_high':
                $query->orderBy('productprice', 'desc');
                break;
            case 'name':
                $query->orderBy('productname', 'asc');
                break;
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            default: // relevance
                // Order by exact matches first, then partial matches
                $query->orderByRaw("
                    CASE 
                        WHEN productname = ? THEN 1
                        WHEN productname LIKE ? THEN 2
                        WHEN description LIKE ? THEN 3
                        ELSE 4
                    END
                ", [$searchTerm, $searchTerm . '%', '%' . $searchTerm . '%']);
        }

        $products = $query->paginate(12);
        $categories = Category::withCount('products')->get();

        // Get price range for search results
        $priceRange = Product::where('is_active', true)
            ->where('productstock', '>', 0)
            ->where(function ($q) use ($searchTerm) {
                $q->where('productname', 'like', '%' . $searchTerm . '%')
                    ->orWhere('productdescription', 'like', '%' . $searchTerm . '%');
            })
            ->selectRaw('MIN(productprice) as min_price, MAX(productprice) as max_price')
            ->first();

        return view('products.search', compact(
            'products',
            'categories',
            'priceRange',
            'searchTerm',
            'sortBy'
        ));
    }

    /**
     * Get search suggestions for AJAX
     */
    public function searchSuggestions(Request $request)
    {
        $searchTerm = $request->get('q', '');

        if (strlen($searchTerm) < 2) {
            return response()->json([]);
        }

        $suggestions = Product::where('is_active', true)
            ->where('productstock', '>', 0)
            ->where('productname', 'like', '%' . $searchTerm . '%')
            ->select('id', 'productname')
            ->limit(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->productname,
                    'url' => route('products.show', $product)
                ];
            });

        return response()->json($suggestions);
    }
}
