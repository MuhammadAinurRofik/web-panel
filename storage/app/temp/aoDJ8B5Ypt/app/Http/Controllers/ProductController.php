<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
{
    $category = $request->query('category');
    
    if ($category) {
        // Filter produk berdasarkan kategori
        $products = Product::where('category', $category)->get();
    } else {
        // Ambil semua produk jika tidak ada filter
        $products = Product::all();
    }

    return view('products.index', compact('products', 'category'));
}
}

