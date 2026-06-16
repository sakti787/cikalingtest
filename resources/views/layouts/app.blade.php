<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Toko Rukun Jaya')</title>
    <!-- Screen Zoom Preference Script -->
    <script>
        if (localStorage.getItem('screen-zoom') === 'enlarged') {
            document.documentElement.classList.add('zoom-enlarged');
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('priceInput', (config) => ({
                rawVal: config.initialValue || '',
                formattedVal: '',
                init() {
                    this.format();
                },
                format() {
                    let clean = String(this.rawVal).replace(/\D/g, '');
                    if (!clean) {
                        this.formattedVal = '';
                        this.rawVal = '';
                        return;
                    }
                    this.formattedVal = Number(clean).toLocaleString('id-ID');
                    this.rawVal = Number(clean);
                },
                onInput(val) {
                    let clean = val.replace(/\D/g, '');
                    this.rawVal = clean ? Number(clean) : '';
                    this.formattedVal = clean ? Number(clean).toLocaleString('id-ID') : '';
                }
            }));
        });
    </script>
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        html {
            font-size: 0.9375rem;
        }
        html.zoom-enlarged {
            font-size: 1.125rem;
        }
        /* Custom Transition for smooth collapse */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 200ms;
        }
        /* Initial styling to prevent FOUC (flash of uncollapsed layout) */
        body.sidebar-collapsed aside {
            width: 5rem !important; /* w-20 */
        }
        body.sidebar-collapsed .ml-64 {
            margin-left: 5rem !important;
        }
        body.sidebar-collapsed [x-show="sidebarOpen"] {
            display: none !important;
        }
    </style>
    @stack('scripts')
</head>
<body x-data="{ 
    sidebarOpen: localStorage.getItem('sidebar-open') !== 'false',
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
        localStorage.setItem('sidebar-open', this.sidebarOpen);
        if (this.sidebarOpen) {
            document.body.classList.remove('sidebar-collapsed');
            document.body.classList.add('sidebar-expanded');
        } else {
            document.body.classList.remove('sidebar-expanded');
            document.body.classList.add('sidebar-collapsed');
        }
    }
}" class="bg-slate-50 text-slate-900 font-sans min-h-screen">
    <script>
        if (localStorage.getItem('sidebar-open') === 'false') {
            document.body.classList.add('sidebar-collapsed');
        } else {
            document.body.classList.add('sidebar-expanded');
        }
    </script>

    <!-- Fixed Sidebar Left -->
    <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="fixed inset-y-0 left-0 h-screen bg-white border-r border-slate-200 flex flex-col z-20 transition-all duration-300">
        <!-- Top Section: Logo Area -->
        <div :class="sidebarOpen ? 'p-6' : 'p-4'" class="border-b border-slate-200 flex flex-col gap-3 transition-all duration-300">
            <div class="flex transition-all duration-300" :class="sidebarOpen ? 'flex-row items-center justify-between' : 'flex-col items-center gap-2'">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center text-white font-bold text-lg tracking-wider shadow-sm shrink-0">
                        RJ
                    </div>
                    <span x-show="sidebarOpen" class="font-semibold text-slate-900 text-lg whitespace-nowrap">Rukun Jaya</span>
                </div>
                <button @click="toggleSidebar()" class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors cursor-pointer focus:outline-none select-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
            <div x-show="sidebarOpen" class="transition-all duration-300">
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
        <nav :class="sidebarOpen ? 'p-4' : 'p-2'" class="flex-1 overflow-y-auto space-y-6 transition-all duration-300">
            @if($role === 'pemilik')
                <div>
                    <div x-show="sidebarOpen" class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 px-3">MENU UTAMA</div>
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}" title="Dashboard"
                           :class="sidebarOpen ? 'justify-start px-3' : 'justify-center px-0'"
                           class="flex items-center gap-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('dashboard') ? 'bg-green-50 text-green-700 font-medium' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <!-- grid SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"></path>
                            </svg>
                            <span x-show="sidebarOpen">Dashboard</span>
                        </a>
                        <a href="{{ route('transaksi.index') }}" title="Transaksi"
                           :class="sidebarOpen ? 'justify-start px-3' : 'justify-center px-0'"
                           class="flex items-center gap-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('transaksi.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <!-- receipt SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"></path>
                            </svg>
                            <span x-show="sidebarOpen">Transaksi</span>
                        </a>
                        <a href="{{ route('produk.index') }}" title="Produk"
                           :class="sidebarOpen ? 'justify-start px-3' : 'justify-center px-0'"
                           class="flex items-center gap-3 py-2.5 rounded-lg transition-all {{ (request()->routeIs('produk.*') && !request()->routeIs('produk.search')) ? 'bg-green-50 text-green-700 font-medium' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <!-- package SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"></path>
                            </svg>
                            <span x-show="sidebarOpen">Produk</span>
                        </a>
                    </div>
                </div>

                <div>
                    <div x-show="sidebarOpen" class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 px-3">LAPORAN</div>
                    <div class="space-y-1">
                        <a href="{{ route('laporan.index') }}" title="Laporan Keuangan"
                           :class="sidebarOpen ? 'justify-start px-3' : 'justify-center px-0'"
                           class="flex items-center gap-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('laporan.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <!-- chart-bar SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v5.625c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 0 1 3 18.75v-5.625ZM13.5 9.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v9.375c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 0 1 13.5 18.75V9.375ZM8.25 3.75c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v15c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125v-15Z"></path>
                            </svg>
                            <span x-show="sidebarOpen">Laporan Keuangan</span>
                        </a>
                        <a href="{{ route('stok.index') }}" title="Stok & Prediksi"
                           :class="sidebarOpen ? 'justify-start px-3' : 'justify-center px-0'"
                           class="flex items-center gap-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('stok.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <!-- cube SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5h-9m-9 0h9m0 0v9m0 0H3m9 0h9m0-9v9m-9-9l-9-5.25L12 3m0 0l9 4.25"></path>
                            </svg>
                            <span x-show="sidebarOpen">Stok & Prediksi</span>
                        </a>
                    </div>
                </div>

                <div>
                    <div x-show="sidebarOpen" class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 px-3">GUDANG</div>
                    <div class="space-y-1">
                        <a href="{{ route('rak.index') }}" title="Peta Rak"
                           :class="sidebarOpen ? 'justify-start px-3' : 'justify-center px-0'"
                           class="flex items-center gap-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('rak.index') ? 'bg-green-50 text-green-700 font-medium' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <!-- map SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503-3.485 2.197-1.099A2.25 2.25 0 0 0 21 10.682V4.912a2.249 2.249 0 0 0-2.835-2.148L15 3.75l-6-1.5-4.233 1.411A2.25 2.25 0 0 0 3 4.912v5.77c0 .866.497 1.66 1.282 2.052L9 15.75l6 1.5 5.233-1.744c.46-.154.767-.585.767-1.069V10.682Z"></path>
                            </svg>
                            <span x-show="sidebarOpen">Peta Rak</span>
                        </a>
                    </div>
                </div>
            @elseif($role === 'kasir')
                <div>
                    <div x-show="sidebarOpen" class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 px-3">KASIR</div>
                    <div class="space-y-1">
                        <a href="{{ route('transaksi.index') }}" title="Input Transaksi"
                           :class="sidebarOpen ? 'justify-start px-3' : 'justify-center px-0'"
                           class="flex items-center gap-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('transaksi.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <!-- receipt SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"></path>
                            </svg>
                            <span x-show="sidebarOpen">Input Transaksi</span>
                        </a>
                        <a href="{{ route('produk.search') }}" title="Cari Produk"
                           :class="sidebarOpen ? 'justify-start px-3' : 'justify-center px-0'"
                           class="flex items-center gap-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('produk.search') ? 'bg-green-50 text-green-700 font-medium' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <!-- package SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"></path>
                            </svg>
                            <span x-show="sidebarOpen">Cari Produk</span>
                        </a>
                        <a href="{{ route('rak.index') }}" title="Peta Rak"
                           :class="sidebarOpen ? 'justify-start px-3' : 'justify-center px-0'"
                           class="flex items-center gap-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('rak.index') ? 'bg-green-50 text-green-700 font-medium' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <!-- map SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503-3.485 2.197-1.099A2.25 2.25 0 0 0 21 10.682V4.912a2.249 2.249 0 0 0-2.835-2.148L15 3.75l-6-1.5-4.233 1.411A2.25 2.25 0 0 0 3 4.912v5.77c0 .866.497 1.66 1.282 2.052L9 15.75l6 1.5 5.233-1.744c.46-.154.767-.585.767-1.069V10.682Z"></path>
                            </svg>
                            <span x-show="sidebarOpen">Peta Rak</span>
                        </a>
                    </div>
                </div>
            @elseif($role === 'gudang')
                <div>
                    <div x-show="sidebarOpen" class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 px-3">GUDANG</div>
                    <div class="space-y-1">
                        <a href="{{ route('barang.create') }}" title="Input Barang Baru"
                           :class="sidebarOpen ? 'justify-start px-3' : 'justify-center px-0'"
                           class="flex items-center gap-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('barang.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <!-- package SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"></path>
                            </svg>
                            <span x-show="sidebarOpen">Input Barang Baru</span>
                        </a>
                        <a href="{{ route('rak.index') }}" title="Peta Rak Digital"
                           :class="sidebarOpen ? 'justify-start px-3' : 'justify-center px-0'"
                           class="flex items-center gap-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('rak.index') ? 'bg-green-50 text-green-700 font-medium' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <!-- map SVG -->
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503-3.485 2.197-1.099A2.25 2.25 0 0 0 21 10.682V4.912a2.249 2.249 0 0 0-2.835-2.148L15 3.75l-6-1.5-4.233 1.411A2.25 2.25 0 0 0 3 4.912v5.77c0 .866.497 1.66 1.282 2.052L9 15.75l6 1.5 5.233-1.744c.46-.154.767-.585.767-1.069V10.682Z"></path>
                            </svg>
                            <span x-show="sidebarOpen">Peta Rak Digital</span>
                        </a>
                    </div>
                </div>
            @endif
        </nav>

        <!-- Bottom Section: User Info and Logout -->
        <div :class="sidebarOpen ? 'p-4' : 'p-2'" class="border-t border-slate-200 flex flex-col gap-4 transition-all duration-300">
            <div class="flex transition-all duration-300" :class="sidebarOpen ? 'items-center gap-3' : 'flex-col items-center gap-1'">
                <!-- Avatar circle -->
                <div class="w-10 h-10 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-lg shrink-0" :title="auth()->user()->username">
                    {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                </div>
                <div x-show="sidebarOpen" class="flex-1 min-w-0 transition-all duration-300">
                    <span class="block font-medium text-slate-900 truncate">{{ auth()->user()->username }}</span>
                    <span class="block text-xs text-slate-500 capitalize">{{ auth()->user()->role }}</span>
                </div>
            </div>

            <!-- Screen Zoom Mode Selector -->
            <div x-data="{ 
                isEnlarged: localStorage.getItem('screen-zoom') === 'enlarged',
                toggleZoom() {
                    this.isEnlarged = !this.isEnlarged;
                    if (this.isEnlarged) {
                        localStorage.setItem('screen-zoom', 'enlarged');
                        document.documentElement.classList.add('zoom-enlarged');
                    } else {
                        localStorage.setItem('screen-zoom', 'normal');
                        document.documentElement.classList.remove('zoom-enlarged');
                    }
                }
            }" class="w-full">
                <button @click="toggleZoom()" 
                        :class="sidebarOpen ? 'justify-between px-3' : 'justify-center px-0'"
                        class="w-full flex items-center rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors cursor-pointer select-none py-2"
                        :title="isEnlarged ? 'Kembalikan Teks Normal' : 'Mode Lansia (Teks Besar)'">
                    <div class="flex items-center gap-2.5">
                        <!-- text sizing icon -->
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19L9.333 5M4 19h10.667M9.333 5L14.667 19M6.667 12h5.333M17 19v-4m0 0h4m-4 0V11" />
                        </svg>
                        <span x-show="sidebarOpen" x-text="isEnlarged ? 'Teks: Besar (Lansia)' : 'Teks: Normal'">Teks: Normal</span>
                    </div>
                    <span x-show="sidebarOpen" :class="isEnlarged ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500'" class="text-[10px] font-bold px-2 py-0.5 rounded-full">
                        <span x-text="isEnlarged ? 'Lansia' : 'Normal'">Normal</span>
                    </span>
                </button>
            </div>
            
            <!-- Logout Form -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" 
                        :class="sidebarOpen ? 'justify-start px-3' : 'justify-center px-0'"
                        class="w-full flex items-center gap-2.5 py-2 rounded-lg text-sm font-medium text-slate-500 hover:text-red-600 hover:bg-red-50/50 transition-colors cursor-pointer text-left"
                        title="Keluar">
                    <!-- logout SVG -->
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"></path>
                    </svg>
                    <span x-show="sidebarOpen">Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Right -->
    <div :class="sidebarOpen ? 'ml-64' : 'ml-20'" class="min-h-screen flex flex-col transition-all duration-300">
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
