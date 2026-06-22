@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-8">
    
    <!-- Greeting & Header -->
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
            Selamat datang, {{ auth()->user()->username }}! 👋
        </h1>
        <p class="text-slate-500 mt-1 text-base">
            Berikut ringkasan operasional hari ini.
        </p>
    </div>

    <!-- ROW 1 — Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Omset Hari Ini -->
        <div class="card flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center shrink-0 shadow-sm">
                <!-- banknotes icon -->
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125H3.75c-.621 0-1.125-.504-1.125-1.125V5.625c0-.621.504-1.125 1.125-1.125Z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 10.5a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"></path>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <span class="block text-sm font-medium text-slate-500">Omset Hari Ini</span>
                <span class="block text-2xl font-bold text-slate-900 mt-1 truncate">
                    Rp {{ number_format($omsetHariIni, 0, ',', '.') }}
                </span>
                <div class="text-xs text-slate-400 mt-1">
                    @if($omsetKemarin > 0)
                        @php
                            $change = (($omsetHariIni - $omsetKemarin) / $omsetKemarin) * 100;
                        @endphp
                        @if($change >= 0)
                            <span class="text-green-600 font-semibold inline-flex items-center gap-0.5">
                                +{{ number_format($change, 1) }}% ↑
                            </span>
                        @else
                            <span class="text-red-600 font-semibold inline-flex items-center gap-0.5">
                                {{ number_format($change, 1) }}% ↓
                            </span>
                        @endif
                        vs kemarin
                    @else
                        <span>Hari pertama</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card 2: Profit Hari Ini -->
        <div class="card flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 shadow-sm">
                <!-- trending-up icon -->
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"></path>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <span class="block text-sm font-medium text-slate-500">Profit Hari Ini</span>
                <span class="block text-2xl font-bold text-slate-900 mt-1 truncate">
                    Rp {{ number_format($profitHariIni, 0, ',', '.') }}
                </span>
                <div class="text-xs text-slate-400 mt-1">
                    @php
                        $margin = $omsetHariIni > 0 ? ($profitHariIni / $omsetHariIni) * 100 : 0;
                    @endphp
                    <span>margin {{ number_format($margin, 1) }}%</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Jumlah Transaksi -->
        <div class="card flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 shadow-sm">
                <!-- shopping-cart icon -->
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.116 60.116 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"></path>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <span class="block text-sm font-medium text-slate-500">Jumlah Transaksi</span>
                <span class="block text-2xl font-bold text-slate-900 mt-1">
                    {{ $jumlahTransaksi }}
                </span>
                <div class="text-xs text-slate-400 mt-1">
                    <span>transaksi hari ini</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Stok Rendah -->
        <div class="card flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 shadow-sm {{ $produkStokRendah > 0 ? 'bg-red-50 text-red-600' : 'bg-slate-100 text-slate-500' }}">
                <!-- exclamation-triangle icon -->
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"></path>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <span class="block text-sm font-medium text-slate-500">Stok Rendah</span>
                <span class="block text-2xl font-bold mt-1 {{ $produkStokRendah > 0 ? 'text-red-600' : 'text-green-600' }}">
                    {{ $produkStokRendah }}
                </span>
                <div class="text-xs mt-1 flex items-center justify-between">
                    <span class="text-slate-400">produk perlu restock</span>
                    @if($produkStokRendah > 0)
                        <a href="/stok" class="text-red-600 font-semibold hover:underline">
                            Lihat &rarr;
                        </a>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <!-- ROW 2 — Two Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        
        <!-- LEFT: Transaksi Terbaru Card (col-span-3) -->
        <div class="card lg:col-span-3 flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Transaksi Terbaru</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Daftar transaksi kasir terupdate</p>
                </div>
                <a href="/transaksi" class="text-sm font-semibold text-green-600 hover:text-green-700 transition-colors">
                    Lihat Semua &rarr;
                </a>
            </div>

            @if($transaksiTerbaru->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table-base">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kasir</th>
                                <th>Total</th>
                                <th>Pembayaran</th>
                                <th>Waktu</th>
                                <th>Nota</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaksiTerbaru as $trx)
                                <tr>
                                    <td class="font-mono text-sm text-slate-500">
                                        #TRX{{ $trx->transaction_id }}
                                    </td>
                                    <td class="text-slate-700 font-medium">
                                        {{ $trx->kasir->username }}
                                    </td>
                                    <td class="font-semibold text-slate-900">
                                        Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        @if($trx->pembayaran === 'cashless')
                                            <span class="badge-blue">Cashless</span>
                                        @else
                                            <span class="badge-gray">Cash</span>
                                        @endif
                                    </td>
                                    <td class="text-slate-500 text-sm">
                                        {{ $trx->transaction_date->format('H:i') }}
                                    </td>
                                    <td>
                                        @if($trx->printed_nota)
                                            <span class="badge-green">Tercetak</span>
                                        @else
                                            <span class="badge-gray">Tanpa Nota</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center py-12 text-center">
                    <svg class="w-16 h-16 text-slate-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"></path>
                    </svg>
                    <span class="text-slate-400 text-sm font-medium">Belum ada transaksi hari ini</span>
                </div>
            @endif
        </div>

        <!-- RIGHT: Produk Terlaris Card (col-span-2) -->
        <div class="card lg:col-span-2 flex flex-col">
            <div class="mb-6">
                <h3 class="text-lg font-bold text-slate-900">Produk Terlaris</h3>
                <p class="text-xs text-slate-400 mt-0.5">Berdasarkan total terjual</p>
            </div>

            @if($produkTerlaris->count() > 0 && $produkTerlaris->first()->total_terjual > 0)
                <div class="space-y-4 flex-1">
                    @foreach($produkTerlaris as $index => $p)
                        <div class="flex items-center gap-3">
                            <!-- Rank Number -->
                            <div class="bg-slate-100 rounded-full w-7 h-7 flex items-center justify-center text-sm font-semibold text-slate-600 shrink-0">
                                {{ $index + 1 }}
                            </div>
                            
                            <!-- Product Details -->
                            <div class="flex-1 min-w-0">
                                <span class="block font-medium text-slate-900 truncate" title="{{ $p->product_name }}">
                                    {{ $p->product_name }}
                                </span>
                                <span class="block text-xs text-slate-500 mt-0.5">
                                    Stok: {{ $p->stock }}
                                </span>
                            </div>

                            <!-- Total Sold -->
                            <div class="text-right shrink-0">
                                <span class="text-sm font-semibold text-green-600 block">
                                    {{ $p->total_terjual ?? 0 }} terjual
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center py-12 text-center">
                    <svg class="w-16 h-16 text-slate-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"></path>
                    </svg>
                    <span class="text-slate-400 text-sm font-medium">Belum ada data penjualan</span>
                </div>
            @endif
        </div>

    </div>

    <!-- ROW 3 — Recent Activity -->
    <div class="card bg-white mt-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Aktivitas Terbaru</h3>
                <p class="text-xs text-slate-400 mt-0.5">Tindakan pemilik dan karyawan (stok, produk, transaksi, dll.)</p>
            </div>
            <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-lg">Realtime</span>
        </div>

        @if($recentActivities->count() > 0)
            <div class="relative pl-6 border-l-2 border-slate-100 space-y-6">
                @foreach($recentActivities as $activity)
                    <div class="relative">
                        <!-- Bullet point indicator -->
                        <span class="absolute -left-[31px] top-1.5 w-3 h-3 rounded-full border-2 border-white shadow-sm
                            @if($activity->activity_type === 'product_add') bg-green-500
                            @elseif($activity->activity_type === 'product_update') bg-blue-500
                            @elseif($activity->activity_type === 'product_deactivate') bg-red-500
                            @elseif($activity->activity_type === 'min_stock_update') bg-amber-500
                            @elseif($activity->activity_type === 'transaction') bg-emerald-500
                            @else bg-slate-500 @endif">
                        </span>
                        
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-2">
                            <div>
                                <span class="text-sm font-bold text-slate-800">
                                    {{ $activity->user->username }} 
                                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider px-1.5 py-0.5 rounded bg-slate-100 ml-1">
                                        {{ $activity->user->role }}
                                    </span>
                                </span>
                                <p class="text-slate-600 mt-1 text-sm leading-relaxed">
                                    {{ $activity->description }}
                                </p>
                            </div>
                            <span class="text-[11px] text-slate-400 shrink-0 font-medium font-mono" title="{{ $activity->created_at }}">
                                {{ $activity->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <!-- Clock / Activity icon -->
                <svg class="w-16 h-16 text-slate-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                </svg>
                <span class="text-slate-400 text-sm font-medium">Belum ada aktivitas tercatat</span>
            </div>
        @endif
    </div>

</div>
@endsection
