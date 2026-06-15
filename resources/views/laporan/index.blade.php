@extends('layouts.app')

@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan Keuangan')

@section('content')
@php
    use Carbon\Carbon;
    $currentPeriod = Carbon::createFromFormat('Y-m', $bulan);
@endphp

<div class="space-y-6">

    <!-- Section Header Row -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Laporan Keuangan</h1>
            <p class="text-sm text-slate-500 mt-1">
                Periode: <strong class="text-slate-700">{{ $currentPeriod->translatedFormat('F Y') }}</strong>
            </p>
        </div>
        
        <!-- Period picker & PDF Export -->
        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" action="{{ route('laporan.index') }}" class="flex items-center gap-2">
                <input type="month" name="bulan" value="{{ $bulan }}" class="input-field max-w-[180px] h-[40px]">
                <button type="submit" class="btn-secondary h-[40px] px-4 py-1 text-sm font-semibold cursor-pointer">
                    Pilih Periode
                </button>
            </form>
            
            <a href="{{ route('laporan.export', ['bulan' => $bulan]) }}" class="btn-primary h-[40px] flex items-center gap-2 text-sm font-semibold">
                <!-- Download icon -->
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"></path>
                </svg>
                <span>Export PDF</span>
            </a>
        </div>
    </div>

    <!-- 3 Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        <!-- Card 1: Total Omset -->
        <div class="card flex flex-col justify-between p-6">
            <div>
                <span class="block text-sm font-semibold text-slate-400 uppercase tracking-wider">Total Omset</span>
                <span class="block text-2xl font-bold text-slate-900 mt-2">
                    Rp {{ number_format($totalOmset, 0, ',', '.') }}
                </span>
            </div>
            <div class="text-xs text-slate-500 mt-4 flex items-center gap-2">
                @if($omsetChange !== null)
                    @if($omsetChange >= 0)
                        <span class="inline-flex items-center gap-0.5 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                            ↑ {{ number_format($omsetChange, 1) }}%
                        </span>
                    @else
                        <span class="inline-flex items-center gap-0.5 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                            ↓ {{ number_format(abs($omsetChange), 1) }}%
                        </span>
                    @endif
                    <span class="text-slate-400">vs bulan lalu</span>
                @else
                    <span class="text-slate-400 font-medium">Tidak ada data bulan lalu</span>
                @endif
            </div>
        </div>

        <!-- Card 2: Total Profit -->
        <div class="card flex flex-col justify-between p-6">
            <div>
                <span class="block text-sm font-semibold text-slate-400 uppercase tracking-wider">Total Profit</span>
                <span class="block text-2xl font-bold text-slate-900 mt-2">
                    Rp {{ number_format($totalProfit, 0, ',', '.') }}
                </span>
            </div>
            <div class="text-xs text-slate-400 font-medium mt-4">
                <span>Margin rata-rata: <strong class="text-slate-600 font-semibold">{{ round($avgMargin, 1) }}%</strong></span>
            </div>
        </div>

        <!-- Card 3: Total Transaksi -->
        <div class="card flex flex-col justify-between p-6">
            <div>
                <span class="block text-sm font-semibold text-slate-400 uppercase tracking-wider">Total Transaksi</span>
                <span class="block text-2xl font-bold text-slate-900 mt-2">
                    {{ $totalTrx }}
                </span>
            </div>
            <div class="text-xs text-slate-400 font-medium mt-4">
                <span>transaksi bulan ini</span>
            </div>
        </div>

    </div>

    <!-- 2-Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        
        <!-- LEFT: Rincian Harian (col-span-3) -->
        <div class="card lg:col-span-3 flex flex-col bg-white">
            <div class="mb-5">
                <h3 class="text-lg font-bold text-slate-900">
                    Rincian Harian — {{ $currentPeriod->translatedFormat('F Y') }}
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Rincian omset, HPP, profit, dan margin per hari</p>
            </div>

            @if($harian->count() > 0)
                <div class="overflow-x-auto flex-1">
                    <table class="table-base">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th class="text-center">Transaksi</th>
                                <th>Omset</th>
                                <th>HPP</th>
                                <th>Profit</th>
                                <th class="text-center">Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($harian as $row)
                                <tr>
                                    <td class="font-medium text-slate-900">
                                        {{ Carbon::parse($row->tanggal)->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="text-center text-slate-600">
                                        {{ $row->jumlah_transaksi }}
                                    </td>
                                    <td class="font-semibold text-slate-800">
                                        Rp {{ number_format($row->omset, 0, ',', '.') }}
                                    </td>
                                    <td class="text-slate-500">
                                        Rp {{ number_format($row->hpp, 0, ',', '.') }}
                                    </td>
                                    <td class="font-semibold text-green-600">
                                        Rp {{ number_format($row->profit, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center font-medium text-slate-500">
                                        {{ $row->margin }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-100 font-bold border-t-2 border-slate-200">
                            <tr>
                                <td class="text-slate-900">TOTAL</td>
                                <td class="text-center text-slate-900">
                                    {{ $harian->sum('jumlah_transaksi') }}
                                </td>
                                <td class="text-slate-900">
                                    Rp {{ number_format($totalOmset, 0, ',', '.') }}
                                </td>
                                <td class="text-slate-500">
                                    Rp {{ number_format($harian->sum('hpp'), 0, ',', '.') }}
                                </td>
                                <td class="text-green-700">
                                    Rp {{ number_format($totalProfit, 0, ',', '.') }}
                                </td>
                                <td class="text-center text-slate-900">
                                    {{ $totalOmset > 0 ? round(($totalProfit / $totalOmset) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center py-16 text-center">
                    <svg class="w-16 h-16 text-slate-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"></path>
                    </svg>
                    <span class="text-slate-400 text-sm font-semibold">Belum ada transaksi bulan ini</span>
                </div>
            @endif
        </div>

        <!-- RIGHT: Profit per Kategori (col-span-2) -->
        <div class="card lg:col-span-2 flex flex-col justify-between bg-white">
            <div>
                <div class="mb-5">
                    <h3 class="text-lg font-bold text-slate-900">Profit per Kategori</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pembagian margin bersih per kategori barang</p>
                </div>

                @php
                    $maxProfit = max($profitKategori->pluck('total_profit')->toArray() ?: [1]);
                    $maxProfit = $maxProfit > 0 ? $maxProfit : 1;
                @endphp

                <div class="space-y-5">
                    @foreach($profitKategori as $category)
                        @php
                            $profitVal = $category->total_profit ?? 0;
                            $percentage = round(($profitVal / $maxProfit) * 100);
                        @endphp
                        <div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-medium text-slate-700">{{ $category->category_name }}</span>
                                <span class="font-semibold text-green-700">
                                    Rp {{ number_format($profitVal, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mt-1.5 border border-slate-200/50">
                                <div class="bg-green-500 h-full rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Total Box -->
            <div class="bg-green-50 border border-green-100 rounded-xl p-4 flex justify-between items-center mt-8">
                <div>
                    <span class="block text-xs font-bold text-green-800 uppercase tracking-wider">Total Profit Bulan Ini</span>
                    <span class="block text-lg font-extrabold text-green-900 mt-1">
                        Rp {{ number_format($totalProfit, 0, ',', '.') }}
                    </span>
                </div>
                <div class="text-2xl">
                    📈
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
