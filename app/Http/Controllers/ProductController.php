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
                $query->withSoldCount()->orderBy('sold_count', 'desc');
                break;
            default: // latest
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(25);
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

        // Increment view count
        $product->incrementViewCount();

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
                ->whereHas('order', function ($query) {
                    $query->where('status', 'delivered');
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
                $query->withSoldCount()->orderBy('sold_count', 'desc');
                break;
            default: // latest
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(25);
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

        $products = $query->paginate(25);
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

    /**
     * Get products for API (for JavaScript loading)
     */
    public function getProducts(Request $request)
    {
        try {
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
                    $query->withSoldCount()->orderBy('sold_count', 'desc');
                    break;
                default: // latest
                    $query->orderBy('created_at', 'desc');
            }

            $products = $query->paginate(25);

            $productsData = $products->getCollection()->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->productname,
                    'description' => $product->productdescription,
                    'price' => $product->productprice,
                    'price_formatted' => number_format($product->productprice),
                    'stock' => $product->productstock,
                    'category' => $product->category ? $product->category->category : 'Kategori tidak ditemukan',
                    'seller' => [
                        'id' => $product->seller ? $product->seller->id : 0,
                        'name' => $product->seller ? ($product->seller->nickname ?? $product->seller->username) : 'Penjual tidak ditemukan',
                    ],
                    'image' => $product->images->count() > 0 
                        ? asset('storage/' . $product->images->first()->image)
                        : null,
                    'url' => route('products.show', $product),
                    'created_at' => $product->created_at->diffForHumans(),
                    'avg_rating' => $product->reviews()->avg('rating') ?? 0,
                    'reviews_count' => $product->reviews()->count(),
                ];
            });

            return response()->json([
                'products' => $productsData,
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'has_more_pages' => $products->hasMorePages(),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getProducts: ' . $e->getMessage());
            return response()->json([
                'products' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 25,
                    'total' => 0,
                    'has_more_pages' => false,
                ],
                'error' => 'Terjadi kesalahan saat memuat produk'
            ], 500);
        }
    }

    /**
     * Get recommended products for dashboard
     */
    public function getRecommendedProducts(Request $request)
    {
        try {
            $query = Product::with(['category', 'images', 'seller'])
                ->where('is_active', true)
                ->where('productstock', '>', 0)
                ->orderBy('created_at', 'desc')
                ->limit(6);

            $products = $query->get();

            $productsData = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->productname,
                    'description' => $product->productdescription,
                    'price' => $product->productprice,
                    'price_formatted' => number_format($product->productprice),
                    'stock' => $product->productstock,
                    'category' => $product->category ? $product->category->category : 'Kategori tidak ditemukan',
                    'seller' => [
                        'id' => $product->seller ? $product->seller->id : 0,
                        'name' => $product->seller ? ($product->seller->nickname ?? $product->seller->username) : 'Penjual tidak ditemukan',
                    ],
                    'image' => $product->images->count() > 0 
                        ? asset('storage/' . $product->images->first()->image)
                        : null,
                    'url' => route('products.show', $product),
                    'created_at' => $product->created_at->diffForHumans(),
                    'avg_rating' => $product->reviews()->avg('rating') ?? 0,
                    'reviews_count' => $product->reviews()->count(),
                ];
            });

            return response()->json([
                'products' => $productsData
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getRecommendedProducts: ' . $e->getMessage());
            return response()->json([
                'products' => [],
                'error' => 'Terjadi kesalahan saat memuat produk rekomendasi'
            ], 500);
        }
    }
}
