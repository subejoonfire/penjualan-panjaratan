<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index(Request $request)
    {
        $query = Category::withCount('products');

        // Search by category name
        if ($request->filled('search')) {
            $query->where('categoryname', 'like', '%' . $request->search . '%');
        }

        $categories = $query->orderBy('categoryname', 'asc')->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $request->validate([
            'categoryname' => 'required|string|max:255|unique:categories,categoryname',
            'description' => 'nullable|string|max:1000',
        ]);

        Category::create([
            'categoryname' => $request->categoryname,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully');
    }

    /**
     * Display the specified category
     */
    public function show(Category $category)
    {
        $category->load(['products' => function($query) {
            $query->with(['seller', 'images'])->latest()->limit(10);
        }]);

        // Category statistics
        $stats = [
            'total_products' => $category->products()->count(),
            'active_products' => $category->products()->where('is_active', true)->count(),
            'total_stock' => $category->products()->sum('stock'),
            'avg_price' => $category->products()->avg('price'),
        ];

        return view('admin.categories.show', compact('category', 'stats'));
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'categoryname' => 'required|string|max:255|unique:categories,categoryname,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update([
            'categoryname' => $request->categoryname,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Cannot delete category that has products. Please move or delete products first.');
        }

        $categoryName = $category->categoryname;
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "Category '{$categoryName}' deleted successfully");
    }

    /**
     * Get category statistics for AJAX
     */
    public function stats()
    {
        $stats = Category::select('categoryname')
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->get()
            ->map(function($category) {
                return [
                    'name' => $category->categoryname,
                    'products_count' => $category->products_count,
                ];
            });

        return response()->json($stats);
    }

    /**
     * Bulk delete categories
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id'
        ]);

        $categoryIds = $request->category_ids;
        
        // Check if any category has products
        $categoriesWithProducts = Category::whereIn('id', $categoryIds)
            ->has('products')
            ->pluck('categoryname')
            ->toArray();

        if (count($categoriesWithProducts) > 0) {
            return back()->with('error', 'Cannot delete categories that have products: ' . implode(', ', $categoriesWithProducts));
        }

        $deletedCount = Category::whereIn('id', $categoryIds)->delete();

        return back()->with('success', "Successfully deleted {$deletedCount} categories");
    }
}