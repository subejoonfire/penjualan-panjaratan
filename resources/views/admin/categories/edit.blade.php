@extends('layouts.app')

@section('title', 'Edit Category - Admin Dashboard')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Category</h1>
                    <p class="mt-2 text-gray-600">Update category information</p>
                </div>
                <a href="{{ route('admin.categories.index') }}"
                    class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Categories
                </a>
            </div>
        </div>

        <!-- Current Category Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-tag text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-blue-900">{{ $category->category }}</h3>
                    <p class="text-sm text-blue-700">
                        Created {{ $category->created_at->format('M d, Y') }} •
                        {{ $category->products_count ?? $category->products()->count() }} products
                    </p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="px-6 py-6">
                    <!-- Category Name -->
                    <div class="mb-6">
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Category Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="category" id="category"
                            value="{{ old('category', $category->category) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
                                      @error('category') border-red-300 @enderror"
                            placeholder="Enter category name...">
                        @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">
                            Choose a unique and descriptive name for this category
                        </p>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description <span class="text-gray-400">(Optional)</span>
                        </label>
                        <textarea name="description" id="description" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
                                         @error('productdescription') border-red-300 @enderror"
                            placeholder="Enter category description...">{{ old('productdescription', $category->productdescription) }}</textarea>
                        @error('productdescription')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">
                            Provide a brief description of what products belong to this category
                        </p>
                    </div>

                    <!-- Warning if category has products -->
                    @if($category->products()->count() > 0)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Important Notice</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>This category contains {{ $category->products()->count() }} products.
                                        Changing the category name will affect how these products are categorized and
                                        displayed.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Category Statistics -->
                    <div class="bg-gray-50 border border-gray-200 rounded-md p-4 mb-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Category Statistics</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $category->products()->count() }}</div>
                                <div class="text-xs text-gray-500">Total Products</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{
                                    $category->products()->where('is_active', true)->count() }}</div>
                                <div class="text-xs text-gray-500">Active Products</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">{{
                                    number_format($category->products()->sum('stock')) }}</div>
                                <div class="text-xs text-gray-500">Total Stock</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600">
                                    @if($category->products()->count() > 0)
                                    Rp {{ number_format($category->products()->avg('productprice')) }}
                                    @else
                                    -
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">Avg Price</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.categories.show', $category) }}"
                            class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-eye mr-1"></i>
                            View Category Details
                        </a>
                        @if($category->products()->count() > 0)
                        <a href="{{ route('products.category', $category) }}"
                            class="text-green-600 hover:text-green-800 text-sm">
                            <i class="fas fa-box mr-1"></i>
                            View Products
                        </a>
                        @endif
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.categories.index') }}"
                            class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancel
                        </a>
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-save mr-2"></i>
                            Update Category
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Preview Section -->
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Preview</h3>
                <p class="text-sm text-gray-600">See how your updated category will appear</p>
            </div>
            <div class="px-6 py-6">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12">
                            <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-tag text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div id="preview-name" class="text-lg font-medium text-gray-900">
                                {{ $category->category }}
                            </div>
                            <div id="preview-description" class="text-sm text-gray-500">
                                {{ $category->productdescription ?: 'Category description will appear here' }}
                            </div>
                            <div class="mt-1">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $category->products()->count() }} products
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('category');
    const descriptionInput = document.getElementById('productdescription');
    const previewName = document.getElementById('preview-name');
    const previewDescription = document.getElementById('preview-description');

    // Update preview in real-time
    nameInput.addEventListener('input', function() {
        const value = this.value.trim();
        previewName.textContent = value || '{{ $category->category }}';
    });

    descriptionInput.addEventListener('input', function() {
        const value = this.value.trim();
        previewDescription.textContent = value || '{{ $category->productdescription ?: "Category description will appear here" }}';
    });
});
</script>
@endsection