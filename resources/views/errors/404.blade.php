@extends(auth()->check() ? 'layouts.app' : 'layouts.guest_error')

@section('title', 'Halaman Tidak Ditemukan')
@section('page-title', 'Halaman Tidak Ditemukan')

@section('content')
<div class="flex flex-col items-center justify-center {{ auth()->check() ? 'py-20' : '' }}">
    <div class="text-8xl font-black text-slate-200 tracking-wider leading-none select-none">404</div>
    <h2 class="text-2xl font-bold text-slate-800 mt-5">Halaman Tidak Ditemukan</h2>
    <p class="text-slate-500 text-sm mt-2 max-w-sm mx-auto leading-relaxed">
        Halaman yang Anda cari tidak ditemukan atau telah dipindahkan.
    </p>
    
    <div class="mt-8 flex items-center justify-center gap-3 w-full">
        @if(auth()->check())
            <button onclick="history.back()" class="btn-secondary px-6 py-2.5 cursor-pointer font-semibold">
                Kembali
            </button>
            @php
                $role = auth()->user()->role;
                $homeRoute = match($role) {
                    'pemilik' => route('dashboard'),
                    'kasir' => route('transaksi.index'),
                    default => route('login'),
                };
            @endphp
            <a href="{{ $homeRoute }}" class="btn-primary px-6 py-2.5 cursor-pointer font-semibold inline-block">
                Menu Utama
            </a>
        @else
            <a href="{{ route('login') }}" class="btn-primary px-6 py-2.5 cursor-pointer font-semibold inline-block">
                Login
            </a>
        @endif
    </div>
</div>
@endsection
