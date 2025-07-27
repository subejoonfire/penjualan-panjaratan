<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Products table indexes for performance
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'productstock'], 'products_active_stock_idx');
            $table->index(['idcategories', 'is_active'], 'products_category_active_idx');
            $table->index(['iduserseller', 'is_active'], 'products_seller_active_idx');
            $table->index(['productprice'], 'products_price_idx');
            $table->index(['created_at'], 'products_created_idx');
            $table->index(['productname'], 'products_name_idx');
        });

        // Orders table indexes
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['status', 'updated_at'], 'orders_status_updated_idx');
            $table->index(['created_at'], 'orders_created_idx');
        });

        // Cart details indexes
        Schema::table('cart_details', function (Blueprint $table) {
            $table->index(['idproduct'], 'cart_details_product_idx');
            $table->index(['idcart'], 'cart_details_cart_idx');
        });

        // Transactions indexes
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['transactionstatus', 'created_at'], 'transactions_status_created_idx');
            $table->index(['idorder'], 'transactions_order_idx');
        });

        // Product reviews indexes
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->index(['idproduct', 'rating'], 'reviews_product_rating_idx');
            $table->index(['iduser', 'idproduct'], 'reviews_user_product_idx');
        });

        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'created_at'], 'users_role_created_idx');
            $table->index(['email'], 'users_email_idx');
            $table->index(['username'], 'users_username_idx');
        });

        // Notifications indexes
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['iduser', 'readstatus'], 'notifications_user_read_idx');
            $table->index(['iduser', 'created_at'], 'notifications_user_created_idx');
        });

        // Categories indexes
        Schema::table('categories', function (Blueprint $table) {
            $table->index(['created_at'], 'categories_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_active_stock_idx');
            $table->dropIndex('products_category_active_idx');
            $table->dropIndex('products_seller_active_idx');
            $table->dropIndex('products_price_idx');
            $table->dropIndex('products_created_idx');
            $table->dropIndex('products_name_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_updated_idx');
            $table->dropIndex('orders_created_idx');
        });

        Schema::table('cart_details', function (Blueprint $table) {
            $table->dropIndex('cart_details_product_idx');
            $table->dropIndex('cart_details_cart_idx');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_status_created_idx');
            $table->dropIndex('transactions_order_idx');
        });

        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropIndex('reviews_product_rating_idx');
            $table->dropIndex('reviews_user_product_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_created_idx');
            $table->dropIndex('users_email_idx');
            $table->dropIndex('users_username_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_read_idx');
            $table->dropIndex('notifications_user_created_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_created_idx');
        });
    }
};