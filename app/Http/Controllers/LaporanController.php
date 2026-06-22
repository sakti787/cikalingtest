<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Helper to compute all reporting metrics for a given month.
     */
    private function getLaporanData(string $bulan): array
    {
        $start = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();
        $prevStart = $start->copy()->subMonth()->startOfMonth();
        $prevEnd   = $start->copy()->subMonth()->endOfMonth();
        
        // QUERY 1 — daily breakdown
        $harian = Transaction::whereBetween('transaction_date', [$start, $end])
            ->select(
                DB::raw('DATE(transaction_date) as tanggal'),
                DB::raw('COUNT(*) as jumlah_transaksi'),
                DB::raw('SUM(total_amount) as omset')
            )
            ->groupBy('tanggal')
            ->orderByRaw('tanggal')
            ->get();
            
        foreach ($harian as $row) {
            $hpp = TransactionItem::whereHas('transaction', fn($q) => 
                $q->whereDate('transaction_date', $row->tanggal))
                ->join('products', fn($join) => $join->on('transaction_items.product_id', '=', 'products.product_id'))
                ->sum(DB::raw('transaction_items.quantity * products.buy_price'));
                
            $row->hpp = (float) $hpp;
            $row->profit = (float) $row->omset - $hpp;
            $row->margin = $row->omset > 0 
                ? round($row->profit / $row->omset * 100, 1) : 0;
        }

        // QUERY 2 — summary
        $totalOmset   = $harian->sum(fn($row) => $row->omset);
        $totalProfit  = $harian->sum(fn($row) => $row->profit);
        $totalTrx     = Transaction::whereBetween('transaction_date', [$start, $end])->count();
        $avgMargin    = $harian->avg(fn($row) => $row->margin) ?? 0;
        
        $prevOmset    = Transaction::whereBetween('transaction_date', [$prevStart, $prevEnd])
            ->sum('total_amount');
            
        $omsetChange  = $prevOmset > 0 
            ? (($totalOmset - $prevOmset) / $prevOmset * 100) : null;

        // QUERY 3 — profit by category
        $profitKategori = Category::withSum([
            'products as total_profit' => function($q) use ($start, $end) {
                $q->join('transaction_items', 'products.product_id', '=', 'transaction_items.product_id')
                  ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.transaction_id')
                  ->whereBetween('transactions.transaction_date', [$start, $end]);
            }
        ], DB::raw('(transaction_items.unit_price - products.buy_price) * transaction_items.quantity'))->get();

        return compact(
            'harian', 
            'totalOmset', 
            'totalProfit', 
            'totalTrx', 
            'avgMargin', 
            'prevOmset', 
            'omsetChange', 
            'profitKategori'
        );
    }

    /**
     * Display the monthly financial report dashboard.
     */
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        $data  = $this->getLaporanData($bulan);
        
        return view('laporan.index', $data + ['bulan' => $bulan]);
    }

    /**
     * Export the financial report as a PDF document.
     */
    public function exportPdf(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        $data  = $this->getLaporanData($bulan);
        $bulanLabel = Carbon::createFromFormat('Y-m', $bulan)
            ->translatedFormat('F Y');
        $printedBy = auth()->user()->username;
        $printedAt = now()->format('d/m/Y H:i');
        
        $pdf = Pdf::loadView('laporan.pdf', 
            $data + compact('bulanLabel', 'printedBy', 'printedAt'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download(
            'Laporan_' . str_replace('-', '_', $bulan) . '_TokRukunJaya.pdf'
        );
    }
}
