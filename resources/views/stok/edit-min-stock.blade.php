@extends('layouts.app')

@section('title', 'Atur Stok Minimum')
@section('page-title', 'Atur Stok Minimum')

@section('content')
<div class="max-w-lg mx-auto space-y-6">

    <!-- Header -->
    <div class="text-center lg:text-left">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Atur Stok Minimum</h1>
        <p class="text-sm text-slate-500 mt-1">Konfigurasikan tingkat stok minimum untuk peringatan sistem otomatis.</p>
    </div>

    <!-- Centered Card -->
    <div class="card bg-white shadow-sm border border-slate-200" x-data="{ minStock: {{ old('min_stock', $product->min_stock) ?? 0 }} }">
        
        <!-- Product Info Header -->
        <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-slate-900">{{ $product->product_name }}</h2>
                
                @if(str_contains(strtolower($product->category->category_name), 'aksesori'))
                    <span class="bg-blue-100 text-blue-700 rounded-full px-2.5 py-0.5 text-xs font-semibold">
                        {{ $product->category->category_name }}
                    </span>
                @elseif(str_contains(strtolower($product->category->category_name), 'material'))
                    <span class="badge-yellow text-xs font-semibold py-0.5 px-2.5">
                        {{ $product->category->category_name }}
                    </span>
                @elseif(str_contains(strtolower($product->category->category_name), 'suku cadang'))
                    <span class="badge-green text-xs font-semibold py-0.5 px-2.5">
                        {{ $product->category->category_name }}
                    </span>
                @else
                    <span class="badge-gray text-xs font-semibold py-0.5 px-2.5">
                        {{ $product->category->category_name }}
                    </span>
                @endif
            </div>

            <div class="mt-2.5 text-xs font-medium text-slate-500">
                Stok saat ini: 
                @if($product->stock <= $product->min_stock)
                    <span class="text-red-600 font-extrabold">{{ $product->stock }} unit</span>
                @else
                    <span class="text-green-600 font-extrabold">{{ $product->stock }} unit</span>
                @endif
            </div>
        </div>

        <!-- Update Form -->
        <form action="{{ route('stok.update-min', $product->product_id) }}" method="POST" class="space-y-5">
            @csrf

            <!-- Stok Minimum Baru -->
            <div>
                <label for="min_stock" class="form-label">Stok Minimum Baru</label>
                <input type="number" id="min_stock" name="min_stock" 
                       x-model.number="minStock"
                       value="{{ old('min_stock', $product->min_stock) }}" 
                       min="0" max="9999" required class="input-field" 
                       placeholder="Masukkan batas stok minimum">
                <p class="text-xs text-slate-400 mt-1.5">Sistem akan memberi alert saat stok &le; nilai ini.</p>
                
                @error('min_stock')
                    <p class="form-error mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Visual comparison indicator via Alpine.js -->
            <div class="p-4 border border-slate-100 rounded-xl bg-slate-50/50 space-y-2 text-xs">
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-medium">Stok saat ini:</span>
                    <span class="font-bold text-slate-800">{{ $product->stock }} unit</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-medium">Min stok baru:</span>
                    <span class="font-bold text-slate-800"><span x-text="minStock || 0"></span> unit</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                    <span class="text-slate-500 font-medium">Status alert:</span>
                    
                    <span x-show="{{ $product->stock }} <= minStock" class="text-amber-600 font-bold flex items-center gap-1">
                        Perlu Restock ⚠
                    </span>
                    <span x-show="{{ $product->stock }} > minStock" class="text-green-600 font-bold flex items-center gap-1">
                        Aman ✓
                    </span>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('stok.index') }}" class="btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn-primary cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
