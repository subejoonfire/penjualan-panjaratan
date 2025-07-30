<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_access_forgot_password_page()
    {
        $response = $this->get('/password/reset');
        
        $response->assertStatus(200);
        $response->assertViewIs('auth.forgot-password');
    }

    public function test_user_can_send_reset_code_via_email()
    {
        Mail::fake();
        
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'phone' => '628123456789'
        ]);

        $response = $this->post('/password/send-reset-code', [
            'identifier' => 'test@example.com',
            'reset_method' => 'email'
        ]);

        $response->assertRedirect('/password/verify-code');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com'
        ]);
    }

    public function test_user_can_send_reset_code_via_whatsapp()
    {
        // Set simulation mode for testing
        putenv('FONNTE_TOKEN=isi_token_fonnte_anda_disini');
        
        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => true], 200)
        ]);
        
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'phone' => '628123456789'
        ]);

        $response = $this->post('/password/send-reset-code', [
            'identifier' => '628123456789',
            'reset_method' => 'phone'
        ]);

        $response->assertRedirect('/password/verify-code');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('phone_password_reset_tokens', [
            'phone' => '628123456789'
        ]);
    }

    public function test_user_cannot_send_reset_code_with_invalid_email()
    {
        $response = $this->post('/password/send-reset-code', [
            'identifier' => 'invalid-email',
            'reset_method' => 'email'
        ]);

        $response->assertSessionHasErrors(['identifier']);
    }

    public function test_user_cannot_send_reset_code_with_invalid_phone()
    {
        $response = $this->post('/password/send-reset-code', [
            'identifier' => '123',
            'reset_method' => 'phone'
        ]);

        $response->assertSessionHasErrors(['identifier']);
    }

    public function test_user_cannot_send_reset_code_for_nonexistent_email()
    {
        $response = $this->post('/password/send-reset-code', [
            'identifier' => 'nonexistent@example.com',
            'reset_method' => 'email'
        ]);

        $response->assertSessionHasErrors(['identifier']);
    }

    public function test_user_cannot_send_reset_code_for_nonexistent_phone()
    {
        $response = $this->post('/password/send-reset-code', [
            'identifier' => '628999999999',
            'reset_method' => 'phone'
        ]);

        $response->assertSessionHasErrors(['identifier']);
    }

    public function test_user_can_verify_reset_code()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'phone' => '628123456789'
        ]);

        // Create reset token
        $token = 'ABC123';
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make($token),
            'created_at' => now()
        ]);

        // Set session data
        session(['reset_data' => [
            'identifier' => 'test@example.com',
            'method' => 'email',
            'created_at' => now()->timestamp
        ]]);

        $response = $this->post('/password/verify-code', [
            'token' => 'ABC123'
        ]);

        $response->assertRedirect('/password/reset-form');
        $response->assertSessionHas('success');
    }

    public function test_user_cannot_verify_invalid_reset_code()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'phone' => '628123456789'
        ]);

        // Create reset token
        $token = 'ABC123';
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make($token),
            'created_at' => now()
        ]);

        // Set session data
        session(['reset_data' => [
            'identifier' => 'test@example.com',
            'method' => 'email',
            'created_at' => now()->timestamp
        ]]);

        $response = $this->post('/password/verify-code', [
            'token' => 'INVALID'
        ]);

        $response->assertSessionHasErrors(['token']);
    }

    public function test_user_can_reset_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'phone' => '628123456789'
        ]);

        // Set verified session data
        session(['verified_reset_data' => [
            'identifier' => 'test@example.com',
            'method' => 'email',
            'created_at' => now()->timestamp
        ]]);

        $response = $this->post('/password/reset', [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('success');
        
        // Verify password was changed
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_user_cannot_reset_password_with_mismatched_confirmation()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'phone' => '628123456789'
        ]);

        // Set verified session data
        session(['verified_reset_data' => [
            'identifier' => 'test@example.com',
            'method' => 'email',
            'created_at' => now()->timestamp
        ]]);

        $response = $this->post('/password/reset', [
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword'
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_user_cannot_reset_password_with_short_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'phone' => '628123456789'
        ]);

        // Set verified session data
        session(['verified_reset_data' => [
            'identifier' => 'test@example.com',
            'method' => 'email',
            'created_at' => now()->timestamp
        ]]);

        $response = $this->post('/password/reset', [
            'password' => '123',
            'password_confirmation' => '123'
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_reset_tokens_are_cleaned_up_after_password_reset()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'phone' => '628123456789'
        ]);

        // Create reset token
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make('ABC123'),
            'created_at' => now()
        ]);

        // Set verified session data
        session(['verified_reset_data' => [
            'identifier' => 'test@example.com',
            'method' => 'email',
            'created_at' => now()->timestamp
        ]]);

        $this->post('/password/reset', [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        // Verify token was deleted
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'test@example.com'
        ]);
    }

    public function test_session_is_cleared_after_password_reset()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'phone' => '628123456789'
        ]);

        // Set verified session data
        session(['verified_reset_data' => [
            'identifier' => 'test@example.com',
            'method' => 'email',
            'created_at' => now()->timestamp
        ]]);

        $this->post('/password/reset', [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        // Verify session was cleared
        $this->assertNull(session('verified_reset_data'));
        $this->assertNull(session('reset_data'));
    }

    public function test_phone_number_formatting()
    {
        // Set simulation mode for testing
        putenv('FONNTE_TOKEN=isi_token_fonnte_anda_disini');
        
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'phone' => '628123456789'
        ]);

        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => true], 200)
        ]);

        // Test with different phone formats
        $phoneFormats = [
            '08123456789',    // Should become 628123456789
            '628123456789',   // Should stay 628123456789
            '8123456789'      // Should become 628123456789
        ];

        foreach ($phoneFormats as $phone) {
            $response = $this->post('/password/send-reset-code', [
                'identifier' => $phone,
                'reset_method' => 'phone'
            ]);

            $response->assertRedirect('/password/verify-code');
            $response->assertSessionHas('success');
        }
    }
}