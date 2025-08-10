<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Jobs\SendVerificationWaJob;
use App\Mail\PasswordResetCode;

class PasswordResetController extends Controller
{
    /**
     * Menampilkan form lupa password
     */
    public function showForgotPasswordForm()
    {
        // Clear any existing session data untuk fresh start
        session()->forget(['reset_data', 'verified_reset_data']);
        
        return view('auth.forgot-password');
    }

    /**
     * Mengirim kode reset password via email atau WhatsApp
     */
    public function sendResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'reset_method' => 'required|in:email,phone',
        ], [
            'identifier.required' => 'Email atau nomor WhatsApp wajib diisi.',
            'reset_method.required' => 'Metode reset wajib dipilih.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $identifier = trim($request->identifier);
        $method = $request->reset_method;

        // Validasi format berdasarkan metode
        if ($method === 'email') {
            if (!filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                return back()->withErrors(['identifier' => 'Format email tidak valid.'])->withInput();
            }
        } else {
            // Validasi format nomor WhatsApp (Indonesia)
            $phone = preg_replace('/[^0-9]/', '', $identifier);
            
            // Check length
            if (strlen($phone) < 10 || strlen($phone) > 13) {
                return back()->withErrors(['identifier' => 'Format nomor WhatsApp tidak valid. Gunakan 10-13 digit angka.'])->withInput();
            }
            
            // Check if it's a valid Indonesian mobile number
            $validPrefixes = ['62', '08', '8'];
            $isValidPrefix = false;
            
            foreach ($validPrefixes as $prefix) {
                if (strpos($phone, $prefix) === 0) {
                    $isValidPrefix = true;
                    break;
                }
            }
            
            if (!$isValidPrefix) {
                return back()->withErrors(['identifier' => 'Format nomor WhatsApp tidak valid. Gunakan format 08xxx atau 62xxx.'])->withInput();
            }
            
            // Pastikan format 62xxx
            if (substr($phone, 0, 1) === '0') {
                $phone = '62' . substr($phone, 1);
            } elseif (substr($phone, 0, 2) !== '62') {
                $phone = '62' . $phone;
            }
            
            $identifier = $phone;
        }

        // Cari user berdasarkan email atau phone dengan cache untuk performance
        $user = null;
        if ($method === 'email') {
            $user = cache()->remember("user_email_{$identifier}", 300, function () use ($identifier) {
                return User::where('email', $identifier)->first();
            });
            if (!$user) {
                return back()->withErrors(['identifier' => 'Email tidak ditemukan dalam sistem.'])->withInput();
            }
        } else {
            // Cari user dengan multiple format untuk kompatibilitas
            $user = null;
            $phoneFormats = [
                $identifier, // Format yang sudah dinormalisasi (62xxx)
                substr($identifier, 2), // Format tanpa 62 (8xxx)
                '0' . substr($identifier, 2), // Format dengan 0 (08xxx)
            ];
            
            foreach ($phoneFormats as $phoneFormat) {
                $user = User::where('phone', $phoneFormat)->first();
                if ($user) {
                    break;
                }
            }
            
            if (!$user) {
                return back()->withErrors(['identifier' => 'Nomor WhatsApp tidak ditemukan dalam sistem.'])->withInput();
            }
        }

        // Generate token
        $token = strtoupper(Str::random(6));

        if ($method === 'email') {
            // Simpan token untuk email
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]
            );

            // Kirim email
            try {
                Mail::to($user->email)->send(new PasswordResetCode($user, $token));
                $message = 'Kode reset password telah dikirim ke email Anda. Silakan cek inbox atau folder spam.';
                Log::info('Password reset email sent successfully', ['email' => $user->email]);
            } catch (\Exception $e) {
                Log::error('Failed to send password reset email', [
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);
                return back()->withErrors(['identifier' => 'Gagal mengirim email. Silakan coba lagi atau gunakan metode WhatsApp.'])->withInput();
            }
        } else {
            // Simpan token untuk phone - gunakan identifier yang sudah dinormalisasi
            DB::table('phone_password_reset_tokens')->updateOrInsert(
                ['phone' => $identifier],
                [
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]
            );



            // Kirim WhatsApp
            try {
                $result = $this->sendWhatsAppResetCode($identifier, $token);
                $message = 'Kode reset password telah dikirim ke WhatsApp Anda. Silakan cek pesan masuk.';
                Log::info('Password reset WhatsApp sent successfully', [
                    'phone' => $identifier,
                    'result' => $result
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send password reset WhatsApp', [
                    'phone' => $identifier,
                    'error' => $e->getMessage()
                ]);
                return back()->withErrors(['identifier' => 'Gagal mengirim WhatsApp. Silakan coba lagi atau gunakan metode email.'])->withInput();
            }
        }

        // Simpan data reset di session untuk step berikutnya dengan cleanup
        session()->forget(['reset_data', 'verified_reset_data']); // Clear any existing data
        session([
            'reset_data' => [
                'identifier' => $identifier,
                'method' => $method,
                'created_at' => now()->timestamp,
                'user_id' => $user->id // Add user ID for additional security
            ]
        ]);

        return redirect()->route('password.reset.verify.form')
            ->with('success', $message);
    }

    /**
     * Menampilkan form verifikasi kode reset
     */
    public function showVerifyResetCodeForm()
    {
        $resetData = session('reset_data');
        
        if (!$resetData) {
            return redirect()->route('password.request')->withErrors(['error' => 'Session tidak ditemukan. Silakan mulai ulang proses reset password.']);
        }

        // Check session timeout (30 menit)
        if (isset($resetData['created_at']) && (now()->timestamp - $resetData['created_at']) > 1800) {
            session()->forget(['reset_data', 'verified_reset_data']);
            return redirect()->route('password.request')->withErrors(['error' => 'Session sudah expired (30 menit). Silakan mulai ulang proses reset password.']);
        }

        return view('auth.verify-reset-code');
    }

    /**
     * Verifikasi kode reset dan tampilkan form password baru
     */
    public function verifyResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:10',
        ], [
            'token.required' => 'Kode verifikasi wajib diisi.',
            'token.max' => 'Kode verifikasi maksimal 10 karakter.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $resetData = session('reset_data');
        if (!$resetData) {
            return redirect()->route('password.request')->withErrors(['error' => 'Session tidak ditemukan. Silakan mulai ulang proses reset password.']);
        }

        // Check session timeout (30 menit)
        if (isset($resetData['created_at']) && (now()->timestamp - $resetData['created_at']) > 1800) {
            session()->forget(['reset_data', 'verified_reset_data']);
            return redirect()->route('password.request')->withErrors(['error' => 'Session sudah expired (30 menit). Silakan mulai ulang proses reset password.']);
        }

        $identifier = $resetData['identifier'];
        $method = $resetData['method'];
        $token = strtoupper(trim($request->token));



        // Verifikasi token
        $isValidToken = false;
        if ($method === 'email') {
            $resetRecord = DB::table('password_reset_tokens')
                ->where('email', $identifier)
                ->where('created_at', '>=', now()->subMinutes(15)) // Token valid 15 menit
                ->first();

            if ($resetRecord && Hash::check($token, $resetRecord->token)) {
                $isValidToken = true;
            }
        } else {
            $resetRecord = DB::table('phone_password_reset_tokens')
                ->where('phone', $identifier)
                ->where('created_at', '>=', now()->subMinutes(15)) // Token valid 15 menit
                ->first();

            if ($resetRecord && Hash::check($token, $resetRecord->token)) {
                $isValidToken = true;
            }
        }



        if (!$isValidToken) {
            return back()->withErrors(['token' => 'Kode verifikasi salah atau sudah expired. Silakan coba lagi.'])->withInput();
        }

        // Simpan verified token di session untuk form reset password
        session(['verified_reset_data' => array_merge($resetData, [
            'verified_at' => now()->timestamp
        ])]);

        return redirect()->route('password.reset.form')->with('success', 'Kode verifikasi berhasil! Silakan masukkan password baru Anda.');
    }

    /**
     * Menampilkan form reset password
     */
    public function showResetPasswordForm()
    {
        $verifiedResetData = session('verified_reset_data');
        
        if (!$verifiedResetData) {
            return redirect()->route('password.request')->withErrors(['error' => 'Session verifikasi tidak ditemukan. Silakan mulai ulang proses reset password.']);
        }

        // Check session timeout (30 menit dari created_at)
        if (isset($verifiedResetData['created_at']) && (now()->timestamp - $verifiedResetData['created_at']) > 1800) {
            session()->forget(['reset_data', 'verified_reset_data']);
            return redirect()->route('password.request')->withErrors(['error' => 'Session sudah expired (30 menit). Silakan mulai ulang proses reset password.']);
        }

        return view('auth.reset-password');
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $resetData = session('verified_reset_data');
        if (!$resetData) {
            return redirect()->route('password.request')->withErrors(['error' => 'Session verifikasi tidak ditemukan. Silakan mulai ulang proses reset password.']);
        }

        // Check session timeout (30 menit dari created_at)
        if (isset($resetData['created_at']) && (now()->timestamp - $resetData['created_at']) > 1800) {
            session()->forget(['reset_data', 'verified_reset_data']);
            return redirect()->route('password.request')->withErrors(['error' => 'Session sudah expired (30 menit). Silakan mulai ulang proses reset password.']);
        }

        $identifier = $resetData['identifier'];
        $method = $resetData['method'];

        // Cari user
        $user = null;
        if ($method === 'email') {
            $user = User::where('email', $identifier)->first();
        } else {
            // Cari user dengan multiple format untuk kompatibilitas
            $phoneFormats = [
                $identifier, // Format yang sudah dinormalisasi (62xxx)
                substr($identifier, 2), // Format tanpa 62 (8xxx)
                '0' . substr($identifier, 2), // Format dengan 0 (08xxx)
            ];
            
            foreach ($phoneFormats as $phoneFormat) {
                $user = User::where('phone', $phoneFormat)->first();
                if ($user) {
                    break;
                }
            }
        }

        if (!$user) {
            session()->forget(['reset_data', 'verified_reset_data']);
            return redirect()->route('password.request')->withErrors(['error' => 'User tidak ditemukan. Silakan mulai ulang proses reset password.']);
        }

        // Additional security check: verify user ID matches session
        if (isset($resetData['user_id']) && $resetData['user_id'] !== $user->id) {
            session()->forget(['reset_data', 'verified_reset_data']);
            return redirect()->route('password.request')->withErrors(['error' => 'Data session tidak valid. Silakan mulai ulang proses reset password.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Hapus token reset
        if ($method === 'email') {
            DB::table('password_reset_tokens')->where('email', $identifier)->delete();
        } else {
            DB::table('phone_password_reset_tokens')->where('phone', $identifier)->delete();
        }

        // Clear session
        session()->forget(['reset_data', 'verified_reset_data']);

        // Log successful password reset
        Log::info('Password reset successful', [
            'user_id' => $user->id,
            'method' => $method,
            'identifier' => $identifier,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);



        return redirect()->route('login')->with('success', 'Password berhasil diubah! Silakan login dengan password baru Anda.');
    }

    /**
     * Mengirim kode WhatsApp untuk reset password
     */
    private function sendWhatsAppResetCode($phone, $token)
    {
        $message = "🔐 *KODE RESET PASSWORD*\n\n";
        $message .= "Kode reset password Anda: *{$token}*\n\n";
                    $message .= "Gunakan kode ini untuk mereset password akun " . env('MAIL_FROM_NAME', 'Penjualan Panjaratan') . " Anda.\n";
        $message .= "⏰ Kode berlaku selama 15 menit.\n\n";
        $message .= "⚠️ Jangan bagikan kode ini kepada siapa pun.\n";
        $message .= "🔒 Jika Anda tidak meminta reset password, abaikan pesan ini.";

        $fonnteToken = env('FONNTE_TOKEN');

        if (!$fonnteToken) {
            throw new \Exception('Fonnte token not configured');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $fonnteToken,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
            ]);

            if (!$response->successful()) {
                throw new \Exception('Fonnte API error: ' . $response->status());
            }

            $result = $response->json();
            
            if (isset($result['status']) && $result['status'] === false) {
                throw new \Exception('WhatsApp API error: ' . ($result['message'] ?? 'Unknown error'));
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}