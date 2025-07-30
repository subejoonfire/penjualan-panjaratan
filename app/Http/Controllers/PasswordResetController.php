<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
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

        $identifier = $request->identifier;
        $method = $request->reset_method;

        // Cari user berdasarkan email atau phone
        $user = null;
        if ($method === 'email') {
            $user = User::where('email', $identifier)->first();
            if (!$user) {
                return back()->withErrors(['identifier' => 'Email tidak ditemukan.'])->withInput();
            }
        } else {
            $user = User::where('phone', $identifier)->first();
            if (!$user) {
                return back()->withErrors(['identifier' => 'Nomor WhatsApp tidak ditemukan.'])->withInput();
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
                $message = 'Kode reset password baru telah dikirim ke email Anda.';
            } catch (\Exception $e) {
                return back()->withErrors(['identifier' => 'Gagal mengirim email. Silakan coba lagi.'])->withInput();
            }
        } else {
            // Simpan token untuk phone
            DB::table('phone_password_reset_tokens')->updateOrInsert(
                ['phone' => $user->phone],
                [
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]
            );

            // Kirim WhatsApp
            try {
                $this->sendWhatsAppResetCode($user->phone, $token);
                $message = 'Kode reset password baru telah dikirim ke WhatsApp Anda.';
            } catch (\Exception $e) {
                return back()->withErrors(['identifier' => 'Gagal mengirim WhatsApp. Silakan coba lagi.'])->withInput();
            }
        }

        // Simpan data reset di session untuk step berikutnya
        session([
            'reset_data' => [
                'identifier' => $identifier,
                'method' => $method,
                'created_at' => now()->timestamp // Track waktu untuk debugging
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
            'token' => 'required|string',
        ], [
            'token.required' => 'Kode verifikasi wajib diisi.',
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
        $token = strtoupper($request->token);

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
            return back()->withErrors(['token' => 'Kode verifikasi salah atau sudah expired.']);
        }

        // Simpan verified token di session untuk form reset password
        session(['verified_reset_data' => array_merge($resetData, [
            'verified_at' => now()->timestamp // Track waktu verifikasi
        ])]);

        return redirect()->route('password.reset.form')->with('success', 'Kode verifikasi berhasil. Silakan masukkan password baru Anda.');
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
            $user = User::where('phone', $identifier)->first();
        }

        if (!$user) {
            session()->forget(['reset_data', 'verified_reset_data']);
            return redirect()->route('password.request')->withErrors(['error' => 'User tidak ditemukan. Silakan mulai ulang proses reset password.']);
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

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }

    /**
     * Mengirim kode WhatsApp untuk reset password
     */
    private function sendWhatsAppResetCode($phone, $token)
    {
        $message = "Kode reset password Anda: *{$token}*\n\nGunakan kode ini untuk mereset password akun Penjualan Panjaratan Anda. Kode berlaku selama 15 menit.\n\nJangan bagikan kode ini kepada siapa pun.";
        
        // Menggunakan Fonnte API
        $response = Http::post('https://api.fonnte.com/send', [
            'target' => $phone,
            'message' => $message,
            'token' => config('services.fonnte.token', env('FONNTE_TOKEN'))
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to send WhatsApp message');
        }

        return $response->json();
    }
}