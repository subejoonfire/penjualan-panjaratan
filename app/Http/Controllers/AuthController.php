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
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20',
            'nickname' => 'nullable|string|max:100',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:customer',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
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

        $verification_token = Str::random(6);
        $phone_verification_token = rand(100000, 999999);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'nickname' => $request->nickname,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'verification_token' => $verification_token,
            'phone_verification_token' => $phone_verification_token,
            'status_verifikasi_email' => false,
            'status_verifikasi_wa' => false,
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
        return view('auth.verify-email', compact('user'));
    }

    public function sendEmailVerification()
    {
        $user = Auth::user();
        $this->sendEmailVerificationInternal($user);
        return back()->with('success', 'Kode verifikasi email telah dikirim ulang.');
    }

    private function sendEmailVerificationInternal($user)
    {
        $token = $user->verification_token;
        $email = $user->email;
        $name = $user->username;
        Mail::raw("Halo $name,\n\nKode verifikasi email Anda: $token\n\nMasukkan kode ini di halaman verifikasi email.", function ($message) use ($email) {
            $message->to($email)
                ->subject('Verifikasi Email - Penjualan Panjaratan');
        });
    }

    public function checkEmailVerification(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        $user = Auth::user();
        if ($request->token === $user->verification_token) {
            $user->update([
                'status_verifikasi_email' => true,
                'email_verified_at' => now(),
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
        return view('auth.verify-wa', compact('user'));
    }

    public function sendWaVerification()
    {
        $user = Auth::user();
        $token = $user->phone_verification_token;
        $phone = $user->phone;
        $message = "Kode verifikasi WhatsApp Anda: $token\nPenjualan Panjaratan";
        $fonnteToken = config('services.fonnte.token');
        $response = Http::withHeaders([
            'Authorization' => $fonnteToken,
        ])->asForm()->post('https://api.fonnte.com/send', [
            'target' => $phone,
            'message' => $message,
        ]);
        return back()->with('success', 'Kode verifikasi WhatsApp telah dikirim ulang.');
    }

    public function checkWaVerification(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        $user = Auth::user();
        if ($request->token == $user->phone_verification_token) {
            $user->update([
                'status_verifikasi_wa' => true,
                'phone_verified_at' => now(),
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
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'nickname' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
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