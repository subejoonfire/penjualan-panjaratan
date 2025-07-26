<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan soft deletes ke semua tabel
     */
    public function up(): void
    {
        // Add soft deletes to users table
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to user_addresses table
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to products table
        Schema::table('products', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to product_images table
        Schema::table('product_images', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to product_reviews table
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to carts table
        Schema::table('carts', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to cart_details table
        Schema::table('cart_details', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to transactions table
        Schema::table('transactions', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to detail_transactions table
        Schema::table('detail_transactions', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to notifications table
        Schema::table('notifications', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove soft deletes from all tables
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('detail_transactions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('cart_details', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};