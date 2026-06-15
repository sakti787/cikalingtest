<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the pemilik dashboard with operational statistics.
     */
    public function index()
    {
        $today = today();
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();

        // 1. Omset Hari Ini
        $omsetHariIni = Transaction::whereDate('transaction_date', $today)
            ->sum('total_amount');

        // 2. Omset Kemarin
        $omsetKemarin = Transaction::whereDate('transaction_date', $today->copy()->subDay())
            ->sum('total_amount');

        // 3. Profit Hari Ini
        $profitHariIni = TransactionItem::whereHas('transaction', fn($q) => 
            $q->whereDate('transaction_date', $today))
            ->join('products', 'transaction_items.product_id', '=', 'products.product_id')
            ->selectRaw('SUM((transaction_items.unit_price - products.buy_price) * transaction_items.quantity) as profit')
            ->value('profit') ?? 0;

        // 4. Jumlah Transaksi Hari Ini
        $jumlahTransaksi = Transaction::whereDate('transaction_date', $today)->count();

        // 5. Jumlah Produk Stok Rendah
        $produkStokRendah = Product::where('is_active', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->count();

        // 6. Transaksi Terbaru (Limit 5)
        $transaksiTerbaru = Transaction::with('kasir')
            ->orderBy('created_at', 'desc')
            ->limit(5)->get();

        // 7. Produk Terlaris (Limit 5)
        $produkTerlaris = Product::withSum('transactionItems as total_terjual', 'quantity')
            ->orderByDesc('total_terjual')
            ->limit(5)->get();

        return view('dashboard.index', compact(
            'omsetHariIni',
            'omsetKemarin',
            'profitHariIni',
            'jumlahTransaksi',
            'produkStokRendah',
            'transaksiTerbaru',
            'produkTerlaris'
        ));
    }
}
