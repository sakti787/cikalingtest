<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\PriceHistory;
use App\Models\Product;
use App\Models\Rack;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'rack']);
        
        if ($request->search) {
            $query->where('product_name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        $products = $query->orderBy('product_name')
            ->paginate(10)
            ->withQueryString();
            
        $categories = Category::orderBy('category_name')->get();
        
        return view('produk.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::orderBy('category_name')->get();
        $racks = Rack::with('category')
            ->orderBy('rack_code')
            ->get();
            
        return view('produk.create', compact('categories', 'racks'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,category_id',
            'rack_id' => 'nullable|exists:racks,rack_id',
            'sell_price' => 'required|numeric|min:0',
            'buy_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
        ]);
        
        Product::create($validated + ['is_active' => true]);
        
        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show($id)
    {
        return view('produk.show');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::orderBy('category_name')->get();
        $racks = Rack::with('category')
            ->orderBy('rack_code')
            ->get();
            
        return view('produk.edit', compact('product', 'categories', 'racks'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $validated = $request->validate([
            'product_name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,category_id',
            'rack_id' => 'nullable|exists:racks,rack_id',
            'sell_price' => 'required|numeric|min:0',
            'buy_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
        ]);
        
        if ($request->sell_price != $product->sell_price) {
            PriceHistory::create([
                'product_id' => $product->product_id,
                'old_price'  => $product->sell_price,
                'new_price'  => $request->sell_price,
                'changed_by' => auth()->id(),
                'changed_at' => now(),
            ]);
        }
        
        $product->update($validated);
        
        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        return redirect()->route('produk.index');
    }

    /**
     * Deactivate the specified product.
     */
    public function deactivate($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => false]);
        
        return redirect()->back()
            ->with('success', 'Produk ' . $product->product_name . ' dinonaktifkan.');
    }

    public function search(Request $request)
    {
        return view('produk.search');
    }
}
