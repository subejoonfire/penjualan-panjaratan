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
            $query->where('category', 'like', '%' . $request->search . '%');
        }

        $categories = $query->orderBy('category', 'asc')->paginate(20);

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
            'category' => 'required|string|max:255|unique:categories,category',
        ]);

        Category::create([
            'category' => $request->category,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dibuat');
    }

    /**
     * Display the specified category
     */
    public function show(Category $category)
    {
        $category->load(['products' => function ($query) {
            $query->with(['seller', 'images'])->latest()->limit(10);
        }]);

        // Category statistics
        $stats = [
            'total_products' => $category->products()->count(),
            'active_products' => $category->products()->where('is_active', true)->count(),
            'total_stock' => $category->products()->sum('productstock'),
            'avg_price' => $category->products()->avg('productprice'),
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
            'category' => 'required|string|max:255|unique:categories,category,' . $category->id,
        ]);

        $category->update([
            'category' => $request->category,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus kategori yang memiliki produk. Silakan pindahkan atau hapus produk terlebih dahulu.');
        }

        $categoryName = $category->category;
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$categoryName}' berhasil dihapus");
    }

    /**
     * Get category statistics for AJAX
     */
    public function stats()
    {
        $stats = Category::select('category')
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->category,
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
            ->pluck('category')
            ->toArray();

        if (count($categoriesWithProducts) > 0) {
            return back()->with('error', 'Tidak dapat menghapus kategori yang memiliki produk: ' . implode(', ', $categoriesWithProducts));
        }

        $deletedCount = Category::whereIn('id', $categoryIds)->delete();

        return back()->with('success', "Berhasil menghapus {$deletedCount} kategori");
    }
}
