<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Advanced product search with caching and optimization
     */
    public function search(Request $request)
    {
        $searchTerm = trim($request->get('q', ''));
        
        if (empty($searchTerm) || strlen($searchTerm) < 2) {
            return redirect()->route('products.index');
        }

        // Create optimized cache key
        $cacheKey = 'search:' . md5(serialize([
            'q' => $searchTerm,
            'category' => $request->get('category'),
            'min_price' => $request->get('min_price'),
            'max_price' => $request->get('max_price'),
            'rating' => $request->get('rating'),
            'sort' => $request->get('sort', 'relevance'),
            'page' => $request->get('page', 1)
        ]));

        $result = cache()->remember($cacheKey, 600, function () use ($request, $searchTerm) {
            
            $query = Product::query()
                ->select([
                    'id', 'productname', 'productprice', 'productstock', 
                    'idcategories', 'iduserseller', 'created_at',
                    // Add relevance scoring
                    DB::raw("
                        CASE 
                            WHEN productname LIKE ? THEN 1
                            WHEN productname LIKE ? THEN 2
                            WHEN productdescription LIKE ? THEN 3
                            ELSE 4
                        END as relevance_score
                    ")
                ])
                ->where('is_active', true)
                ->where('productstock', '>', 0)
                ->where(function ($q) use ($searchTerm) {
                    $q->where('productname', 'like', '%' . $searchTerm . '%')
                      ->orWhere('productdescription', 'like', '%' . $searchTerm . '%');
                })
                ->setBindings([
                    $searchTerm,
                    $searchTerm . '%',
                    '%' . $searchTerm . '%'
                ]);

            // Apply filters
            $this->applyFilters($query, $request);

            // Apply sorting
            $this->applySorting($query, $request->get('sort', 'relevance'));

            // Load relationships efficiently
            return $query->with([
                'category:id,categoryname',
                'images:id,idproduct,imagepath' => function ($imgQuery) {
                    $imgQuery->take(1);
                },
                'seller:id,username'
            ])->paginate(12);
        });

        // Get cached filter data
        $categories = cache()->remember('categories_active', 3600, function () {
            return Category::whereHas('products', function ($query) {
                $query->where('is_active', true)->where('productstock', '>', 0);
            })->get(['id', 'categoryname']);
        });

        $priceRange = cache()->remember('search_price_range_' . md5($searchTerm), 1800, function () use ($searchTerm) {
            return Product::where('is_active', true)
                ->where('productstock', '>', 0)
                ->where(function ($q) use ($searchTerm) {
                    $q->where('productname', 'like', '%' . $searchTerm . '%')
                      ->orWhere('productdescription', 'like', '%' . $searchTerm . '%');
                })
                ->selectRaw('MIN(productprice) as min_price, MAX(productprice) as max_price')
                ->first();
        });

        return view('products.search', [
            'products' => $result,
            'categories' => $categories,
            'priceRange' => $priceRange,
            'searchTerm' => $searchTerm,
            'sortBy' => $request->get('sort', 'relevance'),
            'totalResults' => $result->total()
        ]);
    }

    /**
     * Get search suggestions for autocomplete
     */
    public function suggestions(Request $request)
    {
        $searchTerm = trim($request->get('q', ''));

        if (strlen($searchTerm) < 2) {
            return response()->json([]);
        }

        $cacheKey = 'search_suggestions:' . md5($searchTerm);
        
        $suggestions = cache()->remember($cacheKey, 3600, function () use ($searchTerm) {
            // Get product name suggestions
            $productSuggestions = Product::where('is_active', true)
                ->where('productstock', '>', 0)
                ->where('productname', 'like', $searchTerm . '%')
                ->select('id', 'productname')
                ->limit(5)
                ->get()
                ->map(function ($product) {
                    return [
                        'type' => 'product',
                        'id' => $product->id,
                        'name' => $product->productname,
                        'url' => route('products.show', $product)
                    ];
                });

            // Get category suggestions
            $categorySuggestions = Category::where('categoryname', 'like', $searchTerm . '%')
                ->select('id', 'categoryname')
                ->limit(3)
                ->get()
                ->map(function ($category) {
                    return [
                        'type' => 'category',
                        'id' => $category->id,
                        'name' => $category->categoryname,
                        'url' => route('products.category', $category)
                    ];
                });

            return $productSuggestions->concat($categorySuggestions)->take(8);
        });

        return response()->json($suggestions);
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, Request $request)
    {
        // Category filter
        if ($request->filled('category')) {
            $query->where('idcategories', $request->category);
        }

        // Price range filter
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->when($request->filled('min_price'), function ($q) use ($request) {
                return $q->where('productprice', '>=', $request->min_price);
            })->when($request->filled('max_price'), function ($q) use ($request) {
                return $q->where('productprice', '<=', $request->max_price);
            });
        }

        // Rating filter
        if ($request->filled('rating') && $request->rating > 0) {
            $query->whereHas('reviews', function ($q) use ($request) {
                $q->groupBy('idproduct')
                  ->havingRaw('AVG(rating) >= ?', [$request->rating]);
            });
        }

        // Stock filter
        if ($request->filled('in_stock') && $request->in_stock) {
            $query->where('productstock', '>', 0);
        }
    }

    /**
     * Apply sorting to query
     */
    private function applySorting($query, $sortBy)
    {
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('productprice');
                break;
            case 'price_high':
                $query->orderByDesc('productprice');
                break;
            case 'name':
                $query->orderBy('productname');
                break;
            case 'latest':
                $query->orderByDesc('created_at');
                break;
            case 'popular':
                $query->withSoldCount()->orderByDesc('sold_count');
                break;
            default: // relevance
                $query->orderBy('relevance_score');
                break;
        }
    }
}