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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique(); // Username unik
            $table->string('email')->unique(); // Email unik
            $table->string('phone', 20); // Nomor telepon WA wajib
            $table->string('nickname', 100)->nullable(); // Nama panggilan
            $table->string('password'); // Password
            $table->enum('role', ['admin', 'seller', 'customer'])->default('customer'); // Role pengguna
            $table->timestamp('email_verified_at')->nullable(); // Verifikasi email
            $table->timestamp('phone_verified_at')->nullable(); // Verifikasi telepon
            $table->string('verification_token', 100)->nullable(); // Token verifikasi email
            $table->string('phone_verification_token', 100)->nullable(); // Token verifikasi WA
            $table->boolean('status_verifikasi_email')->default(false); // Status verifikasi email
            $table->boolean('status_verifikasi_wa')->default(false); // Status verifikasi WA
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
