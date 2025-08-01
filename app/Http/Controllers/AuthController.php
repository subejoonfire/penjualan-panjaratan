<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendVerificationEmailJob;
use App\Jobs\SendVerificationWaJob;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        
        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|min:6',
        ], [
            'login.required' => 'Username atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Determine if the input is email or username
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        $credentials = [
            $loginField => $request->login,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            return $this->redirectToDashboard();
        }

        return back()->withErrors([
            'login' => 'Username/Email atau password salah.',
        ])->withInput();
    }

    /**
     * Menampilkan halaman registrasi
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        
        return view('auth.register');
    }

    /**
     * Proses registrasi
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50|unique:users,username|regex:/^[a-zA-Z0-9_-]+$/',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20',
            'nickname' => 'nullable|string|max:100',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:customer',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'username.regex' => 'Username hanya boleh mengandung huruf, angka, underscore (_), dan dash (-). Tidak boleh mengandung spasi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $verification_token = strtoupper(Str::random(6));
        $phone_verification_token = strtoupper(Str::random(6));

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'nickname' => $request->nickname,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'verification_token' => $verification_token,
            'phone_verification_token' => $phone_verification_token,
            // email_verified_at and phone_verified_at will be null initially
        ]);

        Auth::login($user);
        $this->sendEmailVerificationInternal($user);
        return redirect()->route('verification.email.notice');
    }

    public function showEmailVerificationNotice()
    {
        $user = Auth::user();
        if ($user->isEmailVerified()) {
            return redirect()->route('verification.wa.notice');
        }
        // Strict: hanya bisa akses halaman ini jika belum email verified
        if (!$user->isEmailVerified() && !$user->isWaVerified()) {
            return view('auth.verify-email', compact('user'));
        }
        return redirect()->route('verification.wa.notice');
    }

    public function sendEmailVerification()
    {
        $user = Auth::user();
        
        // Generate new token for resend
        $verification_token = strtoupper(Str::random(6));
        $user->update([
            'verification_token' => $verification_token
        ]);
        
        $this->sendEmailVerificationInternal($user);
        return back()->with('success', 'Kode verifikasi email baru telah dikirim ulang.');
    }

    private function sendEmailVerificationInternal($user)
    {
        SendVerificationEmailJob::dispatch($user->id);
    }

    public function checkEmailVerification(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        $user = Auth::user();
        if (strtoupper($request->token) === $user->verification_token) {
            $user->update([
                'email_verified_at' => now(),
                'verification_token' => null, // Clear token after successful verification
            ]);
            return redirect()->route('verification.wa.notice')->with('success', 'Email berhasil diverifikasi.');
        }
        return back()->withErrors(['token' => 'Kode verifikasi salah.']);
    }

    public function showWaVerificationNotice()
    {
        $user = Auth::user();
        if (!$user->isEmailVerified()) {
            return redirect()->route('verification.email.notice');
        }
        if ($user->isWaVerified()) {
            return $this->redirectToDashboard();
        }
        // Strict: hanya bisa akses halaman ini jika sudah email verified dan belum wa verified
        if ($user->isEmailVerified() && !$user->isWaVerified()) {
            // Otomatis kirim token WA jika belum ada token atau token sudah expired
            if (!$user->phone_verification_token) {
                $phone_verification_token = strtoupper(Str::random(6));
                $user->update([
                    'phone_verification_token' => $phone_verification_token
                ]);
                
                SendVerificationWaJob::dispatch($user->id);
                
                return view('auth.verify-wa', compact('user'))->with('success', 'Kode verifikasi WhatsApp telah dikirim otomatis ke nomor Anda.');
            }
            
            return view('auth.verify-wa', compact('user'));
        }
        return $this->redirectToDashboard();
    }

    public function sendWaVerification()
    {
        $user = Auth::user();
        
        // Generate new token for resend
        $phone_verification_token = strtoupper(Str::random(6));
        $user->update([
            'phone_verification_token' => $phone_verification_token
        ]);
        
        SendVerificationWaJob::dispatch($user->id);
        return back()->with('success', 'Kode verifikasi WhatsApp baru telah dikirim ulang.');
    }

    public function checkWaVerification(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        $user = Auth::user();
        if (strtoupper($request->token) === $user->phone_verification_token) {
            $user->update([
                'phone_verified_at' => now(),
                'phone_verification_token' => null, // Clear token after successful verification
            ]);
            return $this->redirectToDashboard();
        }
        return back()->withErrors(['token' => 'Kode verifikasi salah.']);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }

    /**
     * Redirect ke dashboard sesuai role
     */
    private function redirectToDashboard()
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'seller':
                return redirect()->route('seller.dashboard');
            case 'customer':
                return redirect()->route('customer.dashboard');
            default:
                return redirect()->route('login');
        }
    }

    /**
     * Menampilkan halaman profile
     */
    public function profile()
    {
        $user = Auth::user();
        $user->load('addresses');
        
        return view('profile.index', compact('user'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50|unique:users,username,' . $user->id . '|regex:/^[a-zA-Z0-9_-]+$/',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'nickname' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'username.regex' => 'Username hanya boleh mengandung huruf, angka, underscore (_), dan dash (-). Tidak boleh mengandung spasi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $updateData = [
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'nickname' => $request->nickname,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return back()->with('success', 'Profile berhasil diupdate.');
    }
}