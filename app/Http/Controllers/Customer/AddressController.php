<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Store a newly created address
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'address' => 'required|string|max:500',
                'is_default' => 'boolean'
            ]);

            $user = Auth::user();

            // Jika alamat baru diset sebagai default, unset alamat default lainnya
            if ($request->is_default) {
                $user->addresses()->update(['is_default' => false]);
            }

            // Jika ini adalah alamat pertama, set sebagai default
            if ($user->addresses()->count() === 0) {
                $request->merge(['is_default' => true]);
            }

            $address = $user->addresses()->create([
                'address' => $request->address,
                'is_default' => $request->is_default ?? false
            ]);

            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Alamat berhasil ditambahkan',
                    'address' => $address
                ]);
            }

            return back()->with('success', 'Alamat berhasil ditambahkan');

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified address
     */
    public function update(Request $request, UserAddress $address)
    {
        try {
            // Pastikan alamat milik user yang sedang login
            if ($address->iduser !== Auth::id()) {
                abort(403);
            }

            $request->validate([
                'address' => 'required|string|max:500',
                'is_default' => 'boolean'
            ]);

            // Jika alamat diset sebagai default, unset alamat default lainnya
            if ($request->is_default) {
                Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            }

            $address->update([
                'address' => $request->address,
                'is_default' => $request->is_default ?? false
            ]);

            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Alamat berhasil diperbarui',
                    'address' => $address
                ]);
            }

            return back()->with('success', 'Alamat berhasil diperbarui');

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified address
     */
    public function destroy(UserAddress $address)
    {
        try {
            // Pastikan alamat milik user yang sedang login
            if ($address->iduser !== Auth::id()) {
                abort(403);
            }

            // Jika ini adalah alamat default dan ada alamat lain, set alamat lain sebagai default
            if ($address->is_default && Auth::user()->addresses()->count() > 1) {
                $newDefault = Auth::user()->addresses()->where('id', '!=', $address->id)->first();
                if ($newDefault) {
                    $newDefault->update(['is_default' => true]);
                }
            }

            $address->delete();

            if (request()->ajax() || request()->wantsJson() || request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Alamat berhasil dihapus'
                ]);
            }

            return back()->with('success', 'Alamat berhasil dihapus');

        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson() || request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Set address as default
     */
    public function setDefault(UserAddress $address)
    {
        try {
            // Pastikan alamat milik user yang sedang login
            if ($address->iduser !== Auth::id()) {
                abort(403);
            }

            // Unset semua alamat default
            Auth::user()->addresses()->update(['is_default' => false]);

            // Set alamat ini sebagai default
            $address->update(['is_default' => true]);

            if (request()->ajax() || request()->wantsJson() || request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Alamat berhasil diset sebagai default'
                ]);
            }

            return back()->with('success', 'Alamat berhasil diset sebagai default');

        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson() || request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get user addresses for AJAX
     */
    public function getAddresses()
    {
        $user = Auth::user();
        $addresses = $user->addresses()->orderBy('is_default', 'desc')->get();

        return response()->json([
            'success' => true,
            'addresses' => $addresses
        ]);
    }
}