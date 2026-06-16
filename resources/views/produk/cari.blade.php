@extends('layouts.app')

@section('title', 'Cari Produk')
@section('page-title', 'Cari Produk')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Cari Produk</h1>
        <p class="text-sm text-slate-500 mt-1">Cari harga jual dan lokasi rak penyimpanan produk toko secara instan.</p>
    </div>

    <!-- Search Form Card -->
    <div class="card bg-white border border-slate-200 shadow-sm p-5 mb-6">
        <form action="{{ route('produk.search') }}" method="GET">
            <div class="relative">
                <!-- Magnifier Icon -->
                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z"></path>
                    </svg>
                </div>
                
                <!-- Large Input field -->
                <input type="text" name="q" value="{{ $q }}" autofocus 
                       placeholder="Ketik nama produk..." 
                       class="w-full pl-14 pr-12 h-16 text-2xl rounded-xl border border-slate-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all shadow-sm font-medium">
                
                <!-- Clear Button -->
                @if($q)
                    <a href="{{ route('produk.search') }}" 
                       class="absolute inset-y-0 right-0 pr-5 flex items-center text-slate-400 hover:text-slate-600 text-3xl font-bold cursor-pointer select-none">
                        &times;
                    </a>
                @endif

                <!-- Hidden Submit Button to support enter key submit -->
                <input type="submit" class="hidden">
            </div>
        </form>
        
        <!-- Results Subtext -->
        @if($q)
            <p class="text-xs text-slate-500 mt-2.5 font-medium">
                Menampilkan {{ $results->count() }} hasil untuk '{{ $q }}'
            </p>
        @endif
    </div>

    <!-- Search Results / States -->
    <div>
        @if($q !== '')
            <!-- Query submitted -->
            @if($results->isEmpty())
                <!-- Empty state -->
                <div class="py-20 text-center flex flex-col items-center justify-center">
                    <svg class="w-16 h-16 text-slate-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z"></path>
                    </svg>
                    <h3 class="font-bold text-slate-700 text-base">Produk '{{ $q }}' tidak ditemukan</h3>
                    <p class="text-xs text-slate-400 mt-1">Coba kata kunci lain atau periksa ejaan kata pencarian.</p>
                </div>
            @else
                <!-- Results Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($results as $product)
                        <div class="card bg-white border border-slate-200 rounded-xl p-5 hover:shadow-md transition-shadow cursor-default flex flex-col justify-between">
                            
                            <!-- Header Row: Category Badge + Stock status -->
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    @if(str_contains(strtolower($product->category->category_name), 'aksesori'))
                                        <span class="bg-blue-100 text-blue-700 rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider">
                                            {{ $product->category->category_name }}
                                        </span>
                                    @elseif(str_contains(strtolower($product->category->category_name), 'material'))
                                        <span class="badge-yellow text-[10px] font-bold py-0.5 px-2.5 rounded-full uppercase tracking-wider">
                                            {{ $product->category->category_name }}
                                        </span>
                                    @elseif(str_contains(strtolower($product->category->category_name), 'suku cadang'))
                                        <span class="badge-green text-[10px] font-bold py-0.5 px-2.5 rounded-full uppercase tracking-wider">
                                            {{ $product->category->category_name }}
                                        </span>
                                    @else
                                        <span class="badge-gray text-[10px] font-bold py-0.5 px-2.5 rounded-full uppercase tracking-wider">
                                            {{ $product->category->category_name }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div>
                                    @if($product->stock > 0)
                                        <span class="badge-green text-xs font-bold py-0.5 px-2.5 rounded-full">
                                            Stok: {{ $product->stock }}
                                        </span>
                                    @else
                                        <span class="badge-red text-xs font-bold py-0.5 px-2.5 rounded-full">
                                            Habis
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Product Name -->
                            <h3 class="font-bold text-slate-800 text-base mt-3 mb-4 leading-snug">
                                {{ $product->product_name }}
                            </h3>

                            <!-- Key Info Grid -->
                            <div class="grid grid-cols-2 gap-3 bg-slate-50 border border-slate-100/50 rounded-xl p-3">
                                <!-- Location code -->
                                <div class="flex flex-col justify-center">
                                    <span class="text-3xl font-black text-green-600 font-mono leading-none">
                                        {{ $product->rack?->rack_code ?? '-' }}
                                    </span>
                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-1.5">Kode Rak</span>
                                </div>
                                
                                <!-- Sell price -->
                                <div class="flex flex-col justify-center border-l border-slate-200 pl-3">
                                    <span class="text-lg font-black text-slate-900 leading-none">
                                        Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                                    </span>
                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-2.5">Harga Satuan</span>
                                </div>
                            </div>

                            <!-- Low stock amber notice -->
                            @if($product->stock <= $product->min_stock && $product->stock > 0)
                                <div class="mt-3 text-[10px] font-bold text-amber-600 flex items-center gap-1.5">
                                    <span>⚠️</span> <span>Stok hampir habis</span>
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <!-- Initial State (No search input yet) -->
            <div class="py-24 text-center flex flex-col items-center justify-center">
                <svg class="w-20 h-20 text-slate-200 mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z"></path>
                </svg>
                <h2 class="text-xl font-bold text-slate-400">Cari Produk</h2>
                <p class="text-sm text-slate-400 mt-1 max-w-sm leading-relaxed">Ketikkan nama produk untuk mencari harga dan lokasi rak.</p>
            </div>
        @endif
    </div>

</div>
@endsection
