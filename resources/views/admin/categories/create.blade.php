@extends('layouts.app')

@section('title', 'Add New Category - Admin Dashboard')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Add New Category</h1>
                    <p class="mt-2 text-gray-600">Create a new product category</p>
                </div>
                <a href="{{ route('admin.categories.index') }}"
                    class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Categories
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="px-6 py-6">
                    <!-- Category Name -->
                    <div class="mb-6">
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Category Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="category" id="category" value="{{ old('category') }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500
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
                                         @error('description') border-red-300 @enderror"
                            placeholder="Enter category description...">{{ old('description') }}</textarea>
                        @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">
                            Provide a brief description of what products belong to this category
                        </p>
                    </div>

                    <!-- Tips -->
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Category Guidelines</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Use clear and specific category names</li>
                                        <li>Avoid duplicating existing categories</li>
                                        <li>Keep descriptions concise but informative</li>
                                        <li>Categories cannot be deleted if they contain products</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.categories.index') }}"
                        class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </a>
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>
                        Create Category
                    </button>
                </div>
            </form>
        </div>

        <!-- Preview Section -->
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Preview</h3>
                <p class="text-sm text-gray-600">See how your category will appear</p>
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
                                Category Name
                            </div>
                            <div id="preview-description" class="text-sm text-gray-500">
                                Category description will appear here
                            </div>
                            <div class="mt-1">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    0 products
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
    const descriptionInput = document.getElementById('description');
    const previewName = document.getElementById('preview-name');
    const previewDescription = document.getElementById('preview-description');

    // Update preview in real-time
    nameInput.addEventListener('input', function() {
        const value = this.value.trim();
        previewName.textContent = value || 'Category Name';
    });

    descriptionInput.addEventListener('input', function() {
        const value = this.value.trim();
        previewDescription.textContent = value || 'Category description will appear here';
    });

    // Initialize preview with existing values
    if (nameInput.value) {
        previewName.textContent = nameInput.value;
    }
    if (descriptionInput.value) {
        previewDescription.textContent = descriptionInput.value;
    }
});
</script>
@endsection