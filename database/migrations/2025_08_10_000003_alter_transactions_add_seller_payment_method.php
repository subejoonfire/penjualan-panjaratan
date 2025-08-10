<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('seller_payment_method_id')->nullable()->after('payment_method')->constrained('seller_payment_methods');
            $table->string('payment_method_label', 200)->nullable()->after('seller_payment_method_id');
            $table->text('payment_instructions')->nullable()->after('payment_method_label');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('seller_payment_method_id');
            $table->dropColumn(['payment_method_label', 'payment_instructions']);
        });
    }
};