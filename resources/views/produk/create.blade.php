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

    <!-- Form Card -->
    <div class="card bg-white shadow-sm border border-slate-200">
        <form action="{{ route('produk.store') }}" method="POST">
            @csrf
            
            @include('produk._form')
        </form>
    </div>

</div>
@endsection
