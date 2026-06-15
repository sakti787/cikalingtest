@extends('layouts.app')

@section('title', 'Input Transaksi')
@section('page-title', 'Input Transaksi')

@section('content')
<div x-data="{
    products: @json($products),
    filteredProducts: [],
    cart: [],
    search: '',
    selectedCategory: '',
    
    init() {
        this.filteredProducts = this.products;
    },
    
    filterProducts() {
        this.filteredProducts = this.products.filter(p => {
            const matchSearch = p.name.toLowerCase()
                .includes(this.search.toLowerCase());
            const matchCat = !this.selectedCategory || 
                p.category === this.selectedCategory;
            return matchSearch && matchCat;
        });
    },
    
    addToCart(product) {
        if (product.stock === 0) return;
        const existing = this.cart.find(i => i.id === product.id);
        if (existing) {
            if (existing.qty < product.stock) existing.qty++;
        } else {
            this.cart.push({
                id: product.id,
                name: product.name,
                price: product.price,
                buy_price: product.buy_price,
                stock: product.stock,
                category: product.category,
                rack_code: product.rack_code,
                qty: 1
            });
        }
    },
    
    removeFromCart(id) {
        this.cart = this.cart.filter(i => i.id !== id);
    },
    
    increaseQty(item) {
        const product = this.products.find(p => p.id === item.id);
        if (item.qty < product.stock) item.qty++;
    },
    
    decreaseQty(item) {
        if (item.qty > 1) item.qty--;
        else this.removeFromCart(item.id);
    },
    
    get totalItems() { 
        return this.cart.reduce((sum, item) => sum + item.qty, 0); 
    },
    get subtotal() { 
        return this.cart.reduce((s, i) => s + i.qty * i.price, 0);
    },
    get total() { 
        return this.subtotal; 
    },
    
    formatRp(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    },
    
    resetCart() {
        if (confirm('Batalkan transaksi ini?')) this.cart = [];
    },
    
    processPayment(printNota) {
        alert('Payment coming next prompt');
    }
}" class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-160px)]">

    <!-- LEFT PANEL: Product Grid & Search -->
    <div class="flex-1 flex flex-col min-w-0 h-full">
        
        <!-- Transaction Header Bar -->
        <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-4 shrink-0">
            <div class="flex items-center gap-3">
                <h3 class="text-lg font-bold text-slate-800">Transaksi Baru</h3>
                <span class="text-sm font-mono text-slate-400 bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded-md">
                    #----
                </span>
            </div>
            
            <div x-data="{ time: '' }" x-init="time = new Date().toLocaleString('id-ID', {weekday:'long',day:'numeric',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}); setInterval(() => { time = new Date().toLocaleString('id-ID', {weekday:'long',day:'numeric',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}) }, 1000)" class="text-sm text-slate-500 font-semibold">
                <span x-text="time"></span>
            </div>
        </div>

        <!-- Sticky Search Bar -->
        <div class="bg-slate-50/95 sticky top-0 pb-4 z-10 shrink-0">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <!-- Search icon -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z"></path>
                    </svg>
                </div>
                <input type="text" x-model="search" @input="filterProducts()" placeholder="Cari produk berdasarkan nama..." class="w-full pl-12 pr-4 h-14 text-lg rounded-xl border border-slate-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all shadow-sm">
            </div>
        </div>

        <!-- Category Filter Tabs -->
        <div class="flex gap-2 overflow-x-auto pb-3 mb-2 shrink-0 scrollbar-none">
            <button x-on:click="selectedCategory = ''; filterProducts()"
                :class="selectedCategory === '' ? 'bg-green-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                class="rounded-full px-5 py-1.5 text-sm font-semibold transition-all shrink-0 cursor-pointer">
                Semua
            </button>
            @foreach($categories as $category)
                <button x-on:click="selectedCategory = '{{ $category->category_name }}'; filterProducts()"
                    :class="selectedCategory === '{{ $category->category_name }}' ? 'bg-green-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="rounded-full px-5 py-1.5 text-sm font-semibold transition-all shrink-0 cursor-pointer">
                    {{ $category->category_name }}
                </button>
            @endforeach
        </div>

        <!-- Products Grid -->
        <div class="flex-1 overflow-y-auto pr-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 pb-6">
                <template x-for="product in filteredProducts" :key="product.id">
                    <div x-on:click="addToCart(product)"
                        :class="cart.find(i => i.id === product.id) ? 'border-green-500 bg-green-50/50 shadow-sm' : 'border-slate-200 bg-white'"
                        class="relative rounded-xl border-2 p-4 cursor-pointer hover:border-green-400 hover:shadow-md transition-all flex flex-col justify-between"
                        :style="product.stock === 0 ? 'opacity: 0.5; cursor: not-allowed;' : ''">
                        
                        <!-- Checkmark badge top-right -->
                        <div x-show="cart.find(i => i.id === product.id)" class="absolute top-2.5 right-2.5 text-green-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        
                        <!-- Habis badge -->
                        <span x-show="product.stock === 0" class="absolute top-2.5 right-2.5 bg-red-100 text-red-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
                            Habis
                        </span>

                        <div>
                            <!-- Category Badge -->
                            <span x-text="product.category" class="inline-block text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md uppercase tracking-wider mb-2"></span>
                            
                            <!-- Product Name -->
                            <h4 x-text="product.name" class="font-semibold text-slate-800 line-clamp-2 min-h-[40px] leading-snug"></h4>
                        </div>

                        <div class="mt-4">
                            <!-- Price -->
                            <div x-text="formatRp(product.price)" class="text-green-600 font-bold text-lg"></div>
                            
                            <!-- Footer Details: Stock + Rack -->
                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-50">
                                <div class="text-xs font-semibold" :class="product.stock > 10 ? 'text-slate-400' : (product.stock > 0 ? 'text-amber-500' : 'text-red-500')">
                                    <span x-text="'Stok: ' + product.stock"></span>
                                </div>
                                <div class="text-xs text-slate-400 flex items-center gap-1 font-medium">
                                    <span>📦</span> <span x-text="product.rack_code"></span>
                                </div>
                            </div>
                        </div>

                    </div>
                </template>
            </div>
            
            <!-- Empty state when no products found -->
            <div x-show="filteredProducts.length === 0" class="py-16 text-center">
                <svg class="w-16 h-16 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"></path>
                </svg>
                <p class="text-slate-500 font-medium">Tidak ada produk ditemukan</p>
                <p class="text-xs text-slate-400 mt-1">Coba kata kunci pencarian atau kategori lain</p>
            </div>
        </div>

    </div>

    <!-- RIGHT PANEL: Checkout Cart Summary -->
    <div class="w-80 flex-shrink-0 flex flex-col h-full bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Header -->
        <div class="bg-slate-900 text-white px-5 py-4 flex items-center justify-between shrink-0">
            <span class="font-bold text-sm tracking-wide uppercase">Keranjang Transaksi</span>
            <span x-text="totalItems" class="bg-green-500 text-white rounded-full text-xs font-bold px-2.5 py-0.5" x-show="totalItems > 0"></span>
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto max-h-[45vh] divide-y divide-slate-100">
            <template x-for="item in cart" :key="item.id">
                <div class="px-4 py-3 flex flex-col gap-2">
                    <div class="flex justify-between items-start gap-2">
                        <div class="min-w-0 flex-1">
                            <div x-text="item.name" class="font-medium text-sm text-slate-800 truncate" :title="item.name"></div>
                            <div x-text="item.category" class="text-[9px] uppercase font-bold text-slate-400 tracking-wider mt-0.5"></div>
                        </div>
                        <button @click="removeFromCart(item.id)" class="text-red-400 hover:text-red-600 text-lg font-bold px-1 transition-colors cursor-pointer shrink-0">
                            &times;
                        </button>
                    </div>
                    
                    <div class="flex justify-between items-center mt-1">
                        <!-- Qty adjustment controls -->
                        <div class="flex items-center gap-1.5">
                            <button @click="decreaseQty(item)" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-extrabold text-sm transition-all cursor-pointer select-none">
                                −
                            </button>
                            <span x-text="item.qty" class="font-semibold text-slate-800 text-sm w-7 text-center"></span>
                            <button @click="increaseQty(item)" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-extrabold text-sm transition-all cursor-pointer select-none">
                                ＋
                            </button>
                        </div>
                        
                        <!-- Subtotal -->
                        <div x-text="formatRp(item.price * item.qty)" class="font-semibold text-slate-900 text-sm"></div>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <div x-show="cart.length === 0" class="py-16 text-center flex flex-col items-center justify-center">
                <!-- Cart icon -->
                <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.116 60.116 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"></path>
                </svg>
                <span class="text-slate-400 text-sm font-semibold block">Keranjang kosong</span>
                <span class="text-slate-400 text-[11px] mt-0.5 block px-4">Klik produk di sebelah kiri untuk menambahkan</span>
            </div>
        </div>

        <!-- Summary section -->
        <div class="px-5 py-4 bg-slate-50 border-t border-slate-200 space-y-2 shrink-0">
            <div class="flex justify-between text-sm text-slate-500 font-medium">
                <span>Subtotal</span>
                <span x-text="formatRp(subtotal)"></span>
            </div>
            <div class="flex justify-between text-sm text-slate-500 font-medium">
                <span>Diskon</span>
                <span>Rp 0</span>
            </div>
            <div class="flex justify-between items-center text-slate-900 font-bold text-lg pt-2.5 border-t border-slate-200">
                <span>TOTAL</span>
                <span x-text="formatRp(total)"></span>
            </div>
        </div>

        <!-- Action checkout buttons -->
        <div class="px-5 pb-5 pt-2 bg-slate-50 space-y-2 shrink-0">
            <button x-on:click="processPayment(true)"
                x-bind:disabled="cart.length === 0"
                :class="cart.length === 0 ? 'opacity-50 cursor-not-allowed bg-green-400' : 'bg-green-600 hover:bg-green-700 active:bg-green-800'"
                class="btn-primary w-full justify-center min-h-[44px] cursor-pointer text-white font-semibold transition-all">
                <span>💳 Bayar & Cetak Nota</span>
            </button>
            
            <button x-on:click="processPayment(false)"
                x-bind:disabled="cart.length === 0"
                :class="cart.length === 0 ? 'opacity-50 cursor-not-allowed bg-slate-200 text-slate-400' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 active:bg-slate-300'"
                class="btn-secondary w-full justify-center min-h-[44px] cursor-pointer font-semibold transition-all border border-slate-200">
                <span>Bayar Tanpa Nota</span>
            </button>
            
            <button x-on:click="resetCart()"
                x-show="cart.length > 0"
                class="block w-full text-center text-red-500 hover:text-red-700 text-xs font-bold pt-2 transition-colors cursor-pointer select-none">
                Batalkan Transaksi
            </button>
        </div>

    </div>

</div>
@endsection
