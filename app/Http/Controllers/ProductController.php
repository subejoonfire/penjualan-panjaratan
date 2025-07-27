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
        // Cache key based on request parameters
        $cacheKey = 'products_index_' . md5(json_encode($request->all()));
        
        // Try to get from cache first (cache for 5 minutes)
        $result = cache()->remember($cacheKey, 300, function () use ($request) {
            $query = Product::with(['category:id,categoryname', 'images:id,idproduct,imagepath', 'seller:id,username'])
                ->select(['id', 'productname', 'productprice', 'productstock', 'idcategories', 'iduserseller', 'created_at'])
                ->where('is_active', true)
                ->where('productstock', '>', 0);

            // Search by product name
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('productname', 'like', '%' . $searchTerm . '%')
                      ->orWhere('productdescription', 'like', '%' . $searchTerm . '%');
                });
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
                    $query->withSoldCount()->orderBy('sold_count', 'desc');
                    break;
                default: // latest
                    $query->orderBy('created_at', 'desc');
            }

            return $query->paginate(12);
        });

        // Cache categories separately
        $categories = cache()->remember('categories_with_product_count', 3600, function () {
            return Category::withCount(['products' => function ($query) {
                $query->where('is_active', true)->where('productstock', '>', 0);
            }])->get(['id', 'categoryname']);
        });

        // Cache price range
        $priceRange = cache()->remember('products_price_range', 3600, function () {
            return Product::where('is_active', true)
                ->where('productstock', '>', 0)
                ->selectRaw('MIN(productprice) as min_price, MAX(productprice) as max_price')
                ->first();
        });

        $products = $result;
        $sortBy = $request->get('sort', 'latest');
        
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

        // Cache product details for 30 minutes
        $cacheKey = "product_details_{$product->id}";
        $productData = cache()->remember($cacheKey, 1800, function () use ($product) {
            $product->load([
                'category:id,categoryname',
                'seller:id,username,nickname',
                'images:id,idproduct,imagepath',
                'reviews' => function ($query) {
                    $query->with('user:id,username,nickname')->latest();
                }
            ]);

            // Calculate review statistics in one query
            $reviewStats = $product->reviews()
                ->selectRaw('
                    COUNT(*) as total_reviews,
                    AVG(rating) as average_rating,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as rating_5,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as rating_4,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as rating_3,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as rating_2,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as rating_1
                ')
                ->first();

            $totalReviews = $reviewStats->total_reviews ?? 0;
            $averageRating = $reviewStats->average_rating ?? 0;

            // Build rating distribution
            $ratingDistribution = [];
            for ($i = 5; $i >= 1; $i--) {
                $count = $reviewStats->{'rating_' . $i} ?? 0;
                $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                $ratingDistribution[$i] = [
                    'count' => $count,
                    'percentage' => $percentage
                ];
            }

            return [
                'product' => $product,
                'averageRating' => round($averageRating, 1),
                'totalReviews' => $totalReviews,
                'ratingDistribution' => $ratingDistribution
            ];
        });

        // Increment view count asynchronously (don't block page load)
        dispatch(function () use ($product) {
            $product->incrementViewCount();
        })->afterResponse();

        // Cache related products
        $relatedProducts = cache()->remember("related_products_{$product->idcategories}_{$product->id}", 3600, function () use ($product) {
            return Product::where('idcategories', $product->idcategories)
                ->where('id', '!=', $product->id)
                ->where('is_active', true)
                ->where('productstock', '>', 0)
                ->with(['images:id,idproduct,imagepath'])
                ->select(['id', 'productname', 'productprice', 'productstock'])
                ->limit(4)
                ->get();
        });

        // Check if user can review this product (only if logged in)
        $canReview = false;
        if (auth()->check() && auth()->user()->isCustomer()) {
            $cacheKey = "can_review_{$product->id}_" . auth()->id();
            $canReview = cache()->remember($cacheKey, 300, function () use ($product) {
                $hasPurchased = auth()->user()->orders()
                    ->where('status', 'delivered')
                    ->whereHas('cart.cartDetails', function ($query) use ($product) {
                        $query->where('idproduct', $product->id);
                    })
                    ->exists();

                $hasReviewed = $product->reviews()
                    ->where('iduser', auth()->id())
                    ->exists();

                return $hasPurchased && !$hasReviewed;
            });
        }

        return view('products.show', array_merge($productData, [
            'relatedProducts' => $relatedProducts,
            'canReview' => $canReview
        ]));
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
                $query->withSoldCount()->orderBy('sold_count', 'desc');
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

        // Filter by rating
        if ($request->filled('rating')) {
            $query->whereHas('reviews', function($q) use ($request) {
                $q->groupBy('idproduct')
                  ->havingRaw('AVG(rating) >= ?', [$request->rating]);
            });
        }

        // Filter by stock availability
        if ($request->filled('in_stock')) {
            $query->where('productstock', '>', 0);
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
