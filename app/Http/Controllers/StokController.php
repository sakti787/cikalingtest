<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\TransactionItem;
use Illuminate\Http\Request;

class StokController extends Controller
{
    /**
     * Display stock alerts and predictions.
     */
    public function index()
    {
        // QUERY 1 — alert products:
        $alertProducts = Product::with('category')
            ->where('is_active', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->get()
            ->map(function($p) {
                $status = match(true) {
                    $p->stock === 0 => 'habis',
                    $p->stock <= (int)($p->min_stock / 2) => 'kritis',
                    default => 'rendah',
                };
                return array_merge($p->toArray(), [
                    'status' => $status,
                    'category_name' => $p->category->category_name
                ]);
            });

        // QUERY 2 — prediction (only products with sales history):
        $predictions = Product::with('category')
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->get()
            ->map(function($p) {
                $sold7Days = TransactionItem::where('product_id', $p->product_id)
                    ->whereHas('transaction', fn($q) => 
                        $q->where('transaction_date', '>=', now()->subDays(7))
                    )
                    ->sum('quantity');
                
                $avgDaily = $sold7Days / 7;
                if ($avgDaily <= 0) return null;
                
                $hariTersisa = (int) floor($p->stock / $avgDaily);
                return array_merge($p->toArray(), [
                    'avg_daily' => round($avgDaily, 1),
                    'hari_tersisa' => $hariTersisa,
                    'category_name' => $p->category->category_name,
                ]);
            })
            ->filter(fn($p) => $p && $p['hari_tersisa'] <= 30)
            ->sortBy('hari_tersisa')
            ->values();

        return view('stok.index', compact('alertProducts', 'predictions'));
    }

    public function editMinStock($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return view('stok.edit-min-stock', compact('product'));
    }

    public function updateMinStock(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $request->validate([
            'min_stock' => 'required|integer|min:0|max:9999'
        ]);
        $old = $product->min_stock;
        $product->update(['min_stock' => $request->min_stock]);
        
        return redirect()->route('stok.index')
            ->with('success', "Min stok {$product->product_name} diubah dari {$old} → {$request->min_stock}.");
    }

    public function dismissAlert($id)
    {
        return redirect()->route('stok.index');
    }
}
