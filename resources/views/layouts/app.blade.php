<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Toko Rukun Jaya')</title>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('scripts')
</head>
<body class="bg-slate-50 text-slate-900 font-sans min-h-screen">

    <!-- Fixed Sidebar Left -->
    <aside class="fixed inset-y-0 left-0 w-64 h-screen bg-white border-r border-slate-200 flex flex-col z-20">
        <!-- Top Section: Logo Area -->
        <div class="p-6 border-b border-slate-200 flex flex-col gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center text-white font-bold text-lg tracking-wider shadow-sm shrink-0">
                    RJ
                </div>
                <span class="font-semibold text-slate-900 text-lg">Rukun Jaya</span>
            </div>
            <div>
                @php $role = auth()->user()->role @endphp
                @if($role === 'pemilik')
                    <span class="badge-green">Pemilik</span>
                @elseif($role === 'kasir')
                    <span class="badge-yellow">Kasir</span>
                @elseif($role === 'gudang')
                    <span class="badge-gray">Gudang</span>
                @endif
            </div>
        </div>

        <!-- Navigation Section -->
        <nav class="flex-1 overflow-y-auto p-4 space-y-6">
            @if($role === 'pemilik')
                <div>
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 px-3">MENU UTAMA</div>
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg bg-green-50 text-green-700 font-medium' : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors' }}">
                            <!-- grid SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"></path>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('transaksi.index') }}" class="{{ request()->routeIs('transaksi.*') ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg bg-green-50 text-green-700 font-medium' : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors' }}">
                            <!-- receipt SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"></path>
                            </svg>
                            <span>Transaksi</span>
                        </a>
                        <a href="{{ route('produk.index') }}" class="{{ (request()->routeIs('produk.*') && !request()->routeIs('produk.search')) ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg bg-green-50 text-green-700 font-medium' : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors' }}">
                            <!-- package SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"></path>
                            </svg>
                            <span>Produk</span>
                        </a>
                    </div>
                </div>

                <div>
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 px-3">LAPORAN</div>
                    <div class="space-y-1">
                        <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg bg-green-50 text-green-700 font-medium' : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors' }}">
                            <!-- chart-bar SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v5.625c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 0 1 3 18.75v-5.625ZM13.5 9.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v9.375c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 0 1 13.5 18.75V9.375ZM8.25 3.75c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v15c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125v-15Z"></path>
                            </svg>
                            <span>Laporan Keuangan</span>
                        </a>
                        <a href="{{ route('stok.index') }}" class="{{ request()->routeIs('stok.*') ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg bg-green-50 text-green-700 font-medium' : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors' }}">
                            <!-- cube SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5h-9m-9 0h9m0 0v9m0 0H3m9 0h9m0-9v9m-9-9l-9-5.25L12 3m0 0l9 4.25"></path>
                            </svg>
                            <span>Stok & Prediksi</span>
                        </a>
                    </div>
                </div>

                <div>
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 px-3">GUDANG</div>
                    <div class="space-y-1">
                        <a href="{{ route('rak.index') }}" class="{{ request()->routeIs('rak.index') ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg bg-green-50 text-green-700 font-medium' : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors' }}">
                            <!-- map SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503-3.485 2.197-1.099A2.25 2.25 0 0 0 21 10.682V4.912a2.249 2.249 0 0 0-2.835-2.148L15 3.75l-6-1.5-4.233 1.411A2.25 2.25 0 0 0 3 4.912v5.77c0 .866.497 1.66 1.282 2.052L9 15.75l6 1.5 5.233-1.744c.46-.154.767-.585.767-1.069V10.682Z"></path>
                            </svg>
                            <span>Peta Rak</span>
                        </a>
                    </div>
                </div>
            @elseif($role === 'kasir')
                <div>
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 px-3">KASIR</div>
                    <div class="space-y-1">
                        <a href="{{ route('transaksi.index') }}" class="{{ request()->routeIs('transaksi.*') ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg bg-green-50 text-green-700 font-medium' : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors' }}">
                            <!-- receipt SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"></path>
                            </svg>
                            <span>Input Transaksi</span>
                        </a>
                        <a href="{{ route('produk.search') }}" class="{{ request()->routeIs('produk.search') ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg bg-green-50 text-green-700 font-medium' : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors' }}">
                            <!-- package SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"></path>
                            </svg>
                            <span>Cari Produk</span>
                        </a>
                        <a href="{{ route('rak.index') }}" class="{{ request()->routeIs('rak.index') ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg bg-green-50 text-green-700 font-medium' : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors' }}">
                            <!-- map SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503-3.485 2.197-1.099A2.25 2.25 0 0 0 21 10.682V4.912a2.249 2.249 0 0 0-2.835-2.148L15 3.75l-6-1.5-4.233 1.411A2.25 2.25 0 0 0 3 4.912v5.77c0 .866.497 1.66 1.282 2.052L9 15.75l6 1.5 5.233-1.744c.46-.154.767-.585.767-1.069V10.682Z"></path>
                            </svg>
                            <span>Peta Rak</span>
                        </a>
                    </div>
                </div>
            @elseif($role === 'gudang')
                <div>
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 px-3">GUDANG</div>
                    <div class="space-y-1">
                        <a href="{{ route('barang.create') }}" class="{{ request()->routeIs('barang.*') ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg bg-green-50 text-green-700 font-medium' : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors' }}">
                            <!-- package SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"></path>
                            </svg>
                            <span>Input Barang Baru</span>
                        </a>
                        <a href="{{ route('rak.index') }}" class="{{ request()->routeIs('rak.index') ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg bg-green-50 text-green-700 font-medium' : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors' }}">
                            <!-- map SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503-3.485 2.197-1.099A2.25 2.25 0 0 0 21 10.682V4.912a2.249 2.249 0 0 0-2.835-2.148L15 3.75l-6-1.5-4.233 1.411A2.25 2.25 0 0 0 3 4.912v5.77c0 .866.497 1.66 1.282 2.052L9 15.75l6 1.5 5.233-1.744c.46-.154.767-.585.767-1.069V10.682Z"></path>
                            </svg>
                            <span>Peta Rak Digital</span>
                        </a>
                    </div>
                </div>
            @endif
        </nav>

        <!-- Bottom Section: User Info and Logout -->
        <div class="p-4 border-t border-slate-200 flex flex-col gap-4">
            <div class="flex items-center gap-3">
                <!-- Avatar circle -->
                <div class="w-10 h-10 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-lg shrink-0">
                    {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <span class="block font-medium text-slate-900 truncate">{{ auth()->user()->username }}</span>
                    <span class="block text-xs text-slate-500 capitalize">{{ auth()->user()->role }}</span>
                </div>
            </div>
            
            <!-- Logout Form -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium text-slate-500 hover:text-red-600 hover:bg-red-50/50 transition-colors cursor-pointer text-left">
                    <!-- logout SVG -->
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"></path>
                    </svg>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Right -->
    <div class="ml-64 min-h-screen flex flex-col">
        <!-- Top bar (sticky) -->
        <header class="sticky top-0 bg-white/80 backdrop-blur-sm border-b border-slate-200 z-10 px-8 py-4 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">@yield('page-title', 'Dashboard')</h2>
            </div>
            <!-- Live clock via Alpine.js -->
            <div x-data="{ time: '' }" x-init="time = new Date().toLocaleString('id-ID', {weekday:'long',day:'numeric',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}); setInterval(() => { time = new Date().toLocaleString('id-ID', {weekday:'long',day:'numeric',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}) }, 1000)" class="text-sm text-slate-500 font-medium">
                <span x-text="time"></span>
            </div>
        </header>

        <!-- Stock alert banner (if low stock exists) -->
        @php
            $lowStockCount = \App\Models\Product::where('is_active', true)
                ->whereColumn('stock', '<=', 'min_stock')
                ->count();
            $lowStockNames = \App\Models\Product::where('is_active', true)
                ->whereColumn('stock', '<=', 'min_stock')
                ->limit(3)
                ->pluck('product_name')
                ->join(', ');
        @endphp
        @if($lowStockCount > 0)
            <div class="bg-amber-50 border-b border-amber-200 px-8 py-3 flex items-center justify-between text-sm text-amber-800 shrink-0">
                <div class="flex items-center gap-2">
                    <!-- Warning icon -->
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"></path>
                    </svg>
                    <span>
                        <strong>{{ $lowStockCount }} produk</strong> memiliki stok mendekati minimum: {{ $lowStockNames }}{{ $lowStockCount > 3 ? '...' : '' }}
                    </span>
                </div>
                <a href="/stok" class="font-semibold text-amber-700 hover:text-amber-900 transition-colors">
                    Lihat Detail &rarr;
                </a>
            </div>
        @endif

        <!-- Page Content -->
        <main class="flex-1 p-8">
            <!-- Flash alert area -->
            @if(session('success'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 4000)" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-2 translate-x-2"
                     x-transition:enter-end="opacity-100 translate-y-0 translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 translate-x-0"
                     x-transition:leave-end="opacity-0 translate-y-2 translate-x-2"
                     class="fixed top-4 right-4 z-50 bg-white border-l-4 border-green-500 rounded-xl shadow-lg p-4 max-w-sm flex items-start gap-3"
                     x-cloak>
                    <div class="text-green-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <span class="font-semibold text-slate-900 block">Berhasil</span>
                        <span class="text-slate-600 text-sm">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 4000)" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-2 translate-x-2"
                     x-transition:enter-end="opacity-100 translate-y-0 translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 translate-x-0"
                     x-transition:leave-end="opacity-0 translate-y-2 translate-x-2"
                     class="fixed top-4 right-4 z-50 bg-white border-l-4 border-red-500 rounded-xl shadow-lg p-4 max-w-sm flex items-start gap-3"
                     x-cloak>
                    <div class="text-red-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <span class="font-semibold text-slate-900 block">Kesalahan</span>
                        <span class="text-slate-600 text-sm">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

            @if(session('warning'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 6000)" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-2 translate-x-2"
                     x-transition:enter-end="opacity-100 translate-y-0 translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 translate-x-0"
                     x-transition:leave-end="opacity-0 translate-y-2 translate-x-2"
                     class="fixed top-4 right-4 z-50 bg-white border-l-4 border-amber-500 rounded-xl shadow-lg p-4 max-w-sm flex items-start gap-3"
                     x-cloak>
                    <div class="text-amber-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <span class="font-semibold text-slate-900 block">Peringatan</span>
                        <span class="text-slate-600 text-sm">{{ session('warning') }}</span>
                    </div>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>
