<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::with(['product.images', 'product.category', 'product.seller'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(12);
            
        return view('customer.wishlist.index', compact('wishlists'));
    }
    
    public function add(Product $product)
    {
        $user = Auth::user();
        
        // Check if product already in wishlist
        $exists = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();
            
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Produk sudah ada di wishlist'
            ]);
        }
        
        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke wishlist'
        ]);
    }
    
    public function remove(Product $product)
    {
        $removed = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->delete();
            
        if ($removed) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus dari wishlist'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Produk tidak ditemukan di wishlist'
        ]);
    }
    
    public function toggle(Product $product)
    {
        $user = Auth::user();
        
        $wishlist = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();
            
        if ($wishlist) {
            $wishlist->delete();
            return response()->json([
                'success' => true,
                'action' => 'removed',
                'message' => 'Produk dihapus dari wishlist'
            ]);
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $product->id
            ]);
            return response()->json([
                'success' => true,
                'action' => 'added',
                'message' => 'Produk ditambahkan ke wishlist'
            ]);
        }
    }
    
    public function count()
    {
        $count = Wishlist::where('user_id', Auth::id())->count();
        
        return response()->json([
            'count' => $count
        ]);
    }
}