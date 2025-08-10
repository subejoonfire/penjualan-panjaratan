<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iduserseller')->constrained('users')->onDelete('cascade');
            $table->foreignId('payment_category_id')->constrained('payment_categories');
            $table->string('method_name', 100);
            $table->string('account_name', 150)->nullable();
            $table->string('account_number', 150)->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_payment_methods');
    }
};