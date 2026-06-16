@extends('layouts.app')

@section('title', 'Stok & Prediksi')
@section('page-title', 'Stok & Prediksi')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Stok & Prediksi</h1>
        <p class="text-sm text-slate-500 mt-1">Pantau stok minimum produk dan estimasi sisa hari persediaan berdasarkan rata-rata penjualan.</p>
    </div>

    <!-- Red banner for completely out of stock products -->
    @php
        $outOfStockNames = $alertProducts->where('stock', 0)->pluck('product_name')->join(', ');
    @endphp
    @if(!empty($outOfStockNames))
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3 shadow-sm">
            <span class="text-xl shrink-0">🚨</span>
            <div>
                <span class="font-bold text-red-800 block text-sm">PERINGATAN:</span>
                <span class="text-red-700 font-semibold text-xs mt-0.5 block leading-relaxed">
                    {{ $outOfStockNames }} sudah habis! Segera hubungi supplier untuk restock.
                </span>
            </div>
        </div>
    @endif

    <!-- Two Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- LEFT Column: Alert Stok Minimum Card -->
        <div class="card bg-white shadow-sm border border-slate-200 flex flex-col justify-between">
            <div class="pb-4 mb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Alert Stok Minimum</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $alertProducts->count() }} produk butuh perhatian</p>
                </div>
            </div>

            @if($alertProducts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table-base">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Stok</th>
                                <th class="text-center">Min Stok</th>
                                <th class="text-center">Status</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alertProducts as $p)
                                <tr>
                                    <td class="font-medium text-slate-900">
                                        {{ $p['product_name'] }}
                                        <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">
                                            {{ $p['category_name'] }}
                                        </div>
                                    </td>
                                    <td class="text-center font-extrabold text-sm">
                                        @if($p['status'] === 'habis')
                                            <span class="text-red-600">{{ $p['stock'] }}</span>
                                        @elseif($p['status'] === 'kritis')
                                            <span class="text-orange-600">{{ $p['stock'] }}</span>
                                        @else
                                            <span class="text-amber-600">{{ $p['stock'] }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center text-slate-500 font-semibold text-sm">
                                        {{ $p['min_stock'] }}
                                    </td>
                                    <td class="text-center">
                                        @if($p['status'] === 'habis')
                                            <span class="badge-red py-0.5 text-xs font-semibold rounded-full">Habis</span>
                                        @elseif($p['status'] === 'kritis')
                                            <span class="bg-orange-100 text-orange-700 px-2.5 py-0.5 rounded-full text-xs font-semibold inline-block">
                                                Kritis
                                            </span>
                                        @else
                                            <span class="badge-yellow py-0.5 text-xs font-semibold rounded-full">Rendah</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('produk.edit', $p['product_id']) }}" class="btn-secondary text-xs px-2.5 py-1.5 font-semibold inline-block cursor-pointer">
                                            Atur Restock
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-16 text-center">
                    <div class="text-green-500 text-5xl mb-3">✅</div>
                    <div class="text-slate-700 font-bold text-base">Semua stok dalam batas aman</div>
                    <p class="text-xs text-slate-400 mt-1">Tidak ada produk yang berada di bawah tingkat stok minimum.</p>
                </div>
            @endif
        </div>

        <!-- RIGHT Column: Prediksi Stok Habis Card -->
        <div class="card bg-white shadow-sm border border-slate-200 flex flex-col justify-between">
            <div class="pb-4 mb-4 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-900">Prediksi Stok Habis</h3>
                <p class="text-xs text-slate-400 mt-0.5">Estimasi persediaan berdasarkan rata-rata volume penjualan 7 hari terakhir</p>
            </div>

            @if($predictions->count() > 0)
                <div class="space-y-3">
                    @foreach($predictions as $p)
                        @php
                            $days = $p['hari_tersisa'];
                            $borderColor = match(true) {
                                $days <= 7 => 'border-red-200 bg-red-50/10',
                                $days <= 14 => 'border-amber-200 bg-amber-50/10',
                                default => 'border-slate-200 bg-slate-50/5',
                            };
                            $badgeColorClass = match(true) {
                                $days <= 7 => 'badge-red',
                                $days <= 14 => 'badge-yellow',
                                default => 'badge-green',
                            };
                        @endphp
                        <div class="rounded-xl border p-4 flex flex-col justify-between transition-all hover:shadow-sm {{ $borderColor }}">
                            <div class="flex items-start justify-between gap-3">
                                <span class="font-bold text-slate-800 text-sm leading-snug">{{ $p['product_name'] }}</span>
                                <span class="text-[9px] font-extrabold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md uppercase tracking-wider shrink-0">
                                    {{ $p['category_name'] }}
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100/50">
                                <div class="text-xs text-slate-400 font-medium">
                                    Avg terjual: <strong class="text-slate-600 font-semibold">{{ $p['avg_daily'] }}</strong>/hari
                                </div>
                                <div>
                                    <span class="rounded-full px-3 py-0.5 text-xs font-bold {{ $badgeColorClass }}">
                                        ~{{ $days }} hari
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-16 text-center text-slate-400">
                    <svg class="w-16 h-16 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                    </svg>
                    <div class="text-slate-500 font-semibold text-sm">Tidak ada produk yang diprediksi habis</div>
                    <p class="text-xs text-slate-400 mt-1">Persediaan semua produk aman untuk 30 hari ke depan.</p>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
