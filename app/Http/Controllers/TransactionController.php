<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StockAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display the transaction input page.
     */
    public function index(Request $request)
    {
        $products = Product::with(['category', 'rack'])
            ->where(['is_active' => true])
            ->orderByRaw('product_name')
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
            
        if ($request->has('json') || $request->wantsJson()) {
            return response()->json($products);
        }
            
        $categories = Category::orderByRaw('category_name')->get();
        
        return view('transaksi.index', compact('products', 'categories'));
    }

    /**
     * Store a newly created transaction.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'printed_nota' => 'required|boolean',
            'is_special_price' => 'nullable|boolean',
            'discount' => 'nullable|numeric|min:0',
        ]);

        try {
            $transactionId = DB::transaction(function () use ($request) {
                // 1. Validate stock for all items first
                foreach ($request->items as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception('Stok ' . $product->product_name . ' tidak cukup. Tersisa: ' . $product->stock);
                    }
                }

                // 2. Calculate total
                $itemsTotal = 0;
                foreach ($request->items as $item) {
                    $itemsTotal += $item['quantity'] * $item['unit_price'];
                }
                
                $discount = floatval($request->input('discount', 0));
                $total = max(0, $itemsTotal - $discount);
                $isSpecial = $request->is_special_price || $discount > 0;

                // 3. Create transaction
                $transaction = Transaction::create([
                    'kasir_id' => auth()->id(),
                    'transaction_date' => now(),
                    'total_amount' => $total,
                    'is_special_price' => $isSpecial,
                    'printed_nota' => $request->printed_nota,
                ]);

                // 4. Create items + update stock
                foreach ($request->items as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                    
                    TransactionItem::create([
                        'transaction_id' => $transaction->transaction_id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $item['quantity'] * $item['unit_price'],
                    ]);

                    $product->decrement('stock', $item['quantity']);
                    $product->refresh();

                    // 5. Check and create stock alert
                    if ($product->stock <= $product->min_stock) {
                        $existingAlert = StockAlert::where(['product_id' => $product->product_id])
                            ->whereDate('alert_date', today())
                            ->where(['is_dismissed' => false])
                            ->first();

                        if (!$existingAlert) {
                            StockAlert::create([
                                'product_id' => $product->product_id,
                                'alert_date' => now(),
                                'current_stock' => $product->stock,
                                'min_stock' => $product->min_stock,
                            ]);
                        }
                    }
                }

                return $transaction->transaction_id;
            });

            return response()->json([
                'success' => true,
                'transaction_id' => $transactionId,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the printed receipt for the transaction.
     */
    public function nota($id)
    {
        $transaction = Transaction::with([
            'kasir',
            'items.product'
        ])->findOrFail($id);
        
        return view('transaksi.nota', compact('transaction'));
    }
}
