@extends('layouts.app')

@section('title', 'Tambah Produk Baru')
@section('page-title', 'Tambah Produk Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Tambah Produk Baru</h1>
        <p class="text-sm text-slate-500 mt-1">Masukkan informasi produk secara detail untuk menyimpannya.</p>
    </div>

    <!-- Validation error summary -->
    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl">
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
    <div class="card bg-white shadow-sm border border-slate-200">
        <form action="{{ route('produk.store') }}" method="POST">
            @csrf
            
            @include('produk._form')
        </form>
    </div>

</div>
@endsection
