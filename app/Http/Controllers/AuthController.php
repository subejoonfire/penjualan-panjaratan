<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

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
        // Rate limiting - 5 attempts per minute
        $key = 'login_attempts:' . $request->ip();
        if (cache()->get($key, 0) >= 5) {
            return back()->withErrors([
                'login' => 'Terlalu banyak percobaan login. Coba lagi dalam 1 menit.',
            ])->withInput();
        }

        $validator = Validator::make($request->all(), [
            'login' => 'required|string|max:255',
            'password' => 'required|min:6|max:255',
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
            
            // Clear rate limiting on successful login
            cache()->forget($key);
            
            return $this->redirectToDashboard();
        }

        // Increment failed login attempts
        $attempts = cache()->get($key, 0) + 1;
        cache()->put($key, $attempts, 60); // 1 minute expiry

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
        // Rate limiting for registration - 3 attempts per 5 minutes
        $key = 'register_attempts:' . $request->ip();
        if (cache()->get($key, 0) >= 3) {
            return back()->withErrors([
                'general' => 'Terlalu banyak percobaan registrasi. Coba lagi dalam 5 menit.',
            ])->withInput();
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:50|unique:users,username|regex:/^[a-zA-Z0-9_]+$/',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20|regex:/^[0-9\-\+\(\)\s]+$/',
            'nickname' => 'nullable|string|max:100',
            'password' => 'required|string|min:8|max:255|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
            'role' => 'required|in:customer,seller',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.regex' => 'Username hanya boleh mengandung huruf, angka, dan underscore.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'phone.regex' => 'Format nomor telepon tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
        ]);

        if ($validator->fails()) {
            // Increment registration attempts on validation failure
            $attempts = cache()->get($key, 0) + 1;
            cache()->put($key, $attempts, 300); // 5 minutes expiry
            
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::create([
                'username' => strtolower(trim($request->username)),
                'email' => strtolower(trim($request->email)),
                'phone' => $request->phone ? trim($request->phone) : null,
                'nickname' => $request->nickname ? trim($request->nickname) : null,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            // Clear rate limiting on successful registration
            cache()->forget($key);

            // Auto login setelah registrasi
            Auth::login($user);

            return $this->redirectToDashboard();
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('Registration failed: ' . $e->getMessage());
            
            return back()->withErrors([
                'general' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi.',
            ])->withInput();
        }
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