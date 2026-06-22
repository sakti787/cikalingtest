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
            $query->where([['product_name', 'like', '%' . $request->search . '%']]);
        }
        
        if ($request->category_id) {
            $query->where(['category_id' => $request->category_id]);
        }
        
        if ($request->filled('is_active')) {
            $query->where(['is_active' => $request->is_active]);
        }
        
        $products = $query->orderByRaw('product_name')
            ->paginate(10)
            ->withQueryString();
            
        $categories = Category::orderByRaw('category_name')->get();
        
        return view('produk.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::orderByRaw('category_name')->get();
        $racks = Rack::with('category')
            ->where(['is_custom_box' => false])
            ->orderByRaw('rack_code')
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
        
        $product = Product::create($validated + ['is_active' => true]);
        
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'activity_type' => 'product_add',
            'description' => 'Menambahkan produk baru "' . $product->product_name . '" dengan stok ' . $product->stock,
        ]);
        
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
        $categories = Category::orderByRaw('category_name')->get();
        $racks = Rack::with('category')
            ->where(['is_custom_box' => false])
            ->orderByRaw('rack_code')
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

        $oldStock = $product->stock;
        $oldPrice = $product->sell_price;
        $oldName = $product->product_name;
        
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

        $changes = [];
        if ($oldStock != $request->stock) {
            $changes[] = "stok berubah dari {$oldStock} menjadi {$request->stock}";
        }
        if ($oldPrice != $request->sell_price) {
            $changes[] = "harga jual berubah dari Rp " . number_format($oldPrice, 0, ',', '.') . " menjadi Rp " . number_format($request->sell_price, 0, ',', '.');
        }
        if ($oldName != $request->product_name) {
            $changes[] = "nama produk diubah dari \"{$oldName}\" menjadi \"{$request->product_name}\"";
        }
        
        if (!empty($changes)) {
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'activity_type' => 'product_update',
                'description' => 'Memperbarui produk "' . $product->product_name . '": ' . implode(', ', $changes),
            ]);
        }
        
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
        
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'activity_type' => 'product_deactivate',
            'description' => 'Menonaktifkan produk "' . $product->product_name . '"',
        ]);
        
        return redirect()->back()
            ->with('success', 'Produk ' . $product->product_name . ' dinonaktifkan.');
    }

    public function search(Request $request)
    {
        $q = $request->input('q', '');
        $results = collect();
        
        if ($q !== '') {
            $results = Product::with(['category', 'rack'])
                ->where(['is_active' => true])
                ->where([['product_name', 'like', '%' . $q . '%']])
                ->orderByRaw('product_name')
                ->limit(20)
                ->get();
        }
        
        return view('produk.cari', compact('results', 'q'));
    }
}
