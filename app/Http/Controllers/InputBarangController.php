<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Rack;
use Illuminate\Http\Request;

class InputBarangController extends Controller
{
    /**
     * Show the form for creating a new product/goods entry.
     */
    public function create()
    {
        $categories = Category::orderByRaw('category_name')->get();
        $racks = Rack::with('category')
            ->where(['is_custom_box' => false])
            ->withCount(['products as active_count' => fn($q) => 
                $q->where(['is_active' => true])
            ])
            ->orderByRaw('rack_code')
            ->get()
            ->map(fn($r) => [
                'id'          => $r->rack_id,
                'code'        => $r->rack_code,
                'category_id' => $r->category_id,
                'category'    => $r->category?->category_name ?? 'Kosong',
                'capacity'    => $r->capacity,
                'used'        => $r->active_count,
                'available'   => $r->capacity - $r->active_count,
            ]);
        
        return view('input-barang.create', compact('categories', 'racks'));
    }

    /**
     * Store a newly created product/goods entry in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:200',
            'category_id'  => 'required|exists:categories,category_id',
            'stock'        => 'required|integer|min:1',
            'buy_price'    => 'required|numeric|min:0',
            'rack_id'      => 'nullable|exists:racks,rack_id',
        ]);
        
        $rack = $request->rack_id 
            ? Rack::find($request->rack_id) 
            : null;
        
        $categoryMismatch = $rack && 
            $rack->category_id != $request->category_id;
        
        // Default sell_price = buy_price * 1.3 (30% margin) rounded to hundreds
        $sellPrice = round($request->buy_price * 1.3, -2);
        
        $product = Product::create([
            'product_name' => $request->product_name,
            'category_id'  => $request->category_id,
            'rack_id'      => $request->rack_id,
            'buy_price'    => $request->buy_price,
            'sell_price'   => $sellPrice,
            'stock'        => $request->stock,
            'min_stock'    => 10, // default, pemilik can change later
            'is_active'    => true,
        ]);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'activity_type' => 'product_add',
            'description' => 'Menginput barang baru "' . $request->product_name . '" dengan stok awal ' . $request->stock,
        ]);
        
        if ($categoryMismatch) {
            return redirect()->route('rak.index')
                ->with('warning', 'Barang disimpan dengan catatan: rak tidak sesuai kategori. Harap pindahkan ke rak yang tepat.');
        } else {
            return redirect()->route('rak.index')
                ->with('success', 'Barang baru berhasil diinput dan peta rak diperbarui.');
        }
    }
}
