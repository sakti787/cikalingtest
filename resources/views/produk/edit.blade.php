@extends('layouts.app')

@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Edit Produk</h1>
        <p class="text-sm text-slate-500 mt-1">Sesuaikan informasi produk di bawah ini.</p>
    </div>

    <!-- Validation error summary -->
    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl mb-6">
            <div class="flex items-center gap-2 font-bold text-sm mb-2">
                <span>⚠️</span>
                <span>Terdapat {{ $errors->count() }} kesalahan:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Card -->
    <div class="card bg-white shadow-sm border border-slate-200" x-data="{ loading: false }">
        <!-- Card Header with product details -->
        <div class="flex items-center gap-3 pb-5 mb-5 border-b border-slate-100">
            <h2 class="text-2xl font-bold text-slate-900">{{ $product->product_name }}</h2>
            
            @if(str_contains(strtolower($product->category->category_name), 'aksesori'))
                <span class="bg-blue-100 text-blue-700 rounded-full px-3 py-1.5 text-xs font-semibold inline-block">
                    {{ $product->category->category_name }}
                </span>
            @elseif(str_contains(strtolower($product->category->category_name), 'material'))
                <span class="badge-yellow text-xs font-semibold py-1 px-2.5">
                    {{ $product->category->category_name }}
                </span>
            @elseif(str_contains(strtolower($product->category->category_name), 'suku cadang'))
                <span class="badge-green text-xs font-semibold py-1 px-2.5">
                    {{ $product->category->category_name }}
                </span>
            @else
                <span class="badge-gray text-xs font-semibold py-1 px-2.5">
                    {{ $product->category->category_name }}
                </span>
            @endif
        </div>

        <form action="{{ route('produk.update', $product->product_id) }}" method="POST" @submit="loading = true">
            @csrf
            @method('PUT')
            
            @include('produk._form')
        </form>
    </div>

</div>
@endsection
