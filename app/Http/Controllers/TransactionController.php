<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display the transaction input page.
     */
    public function index()
    {
        $products = Product::with(['category', 'rack'])
            ->where('is_active', true)
            ->orderBy('product_name')
            ->get()
            ->map(fn($p) => [
                'id'        => $p->product_id,
                'name'      => $p->product_name,
                'price'     => (float) $p->sell_price,
                'buy_price' => (float) $p->buy_price,
                'stock'     => $p->stock,
                'category'  => $p->category->category_name,
                'rack_code' => $p->rack?->rack_code ?? '-',
            ]);
            
        $categories = Category::orderBy('category_name')->get();
        
        return view('transaksi.index', compact('products', 'categories'));
    }

    /**
     * Store a newly created transaction.
     */
    public function store(Request $request)
    {
        return redirect()->route('transaksi.index');
    }

    /**
     * Display the printed receipt for the transaction.
     */
    public function nota($id)
    {
        return view('transaksi.nota');
    }
}
