@extends('layouts.app')

@section('title', 'Input Transaksi')
@section('page-title', 'Input Transaksi')

@section('content')
    <div x-data="transactionCart" class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-160px)]">

        <!-- LEFT PANEL: Product Grid & Search -->
        <div class="flex-1 flex flex-col min-w-0 h-full">

            <!-- Transaction Header Bar -->
            <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-4 shrink-0">
                <div class="flex items-center gap-3">
                    <h3 class="text-lg font-bold text-slate-800">Transaksi Baru</h3>
                    <span
                        class="text-sm font-mono text-slate-400 bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded-md">
                        #----
                    </span>
                </div>

                <div x-data="{ time: '' }"
                    x-init="time = new Date().toLocaleString('id-ID', {weekday:'long',day:'numeric',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}); setInterval(() => { time = new Date().toLocaleString('id-ID', {weekday:'long',day:'numeric',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}) }, 1000)"
                    class="text-sm text-slate-500 font-semibold">
                    <span x-text="time"></span>
                </div>
            </div>

            <!-- Sticky Search Bar -->
            <div class="bg-slate-50/95 sticky top-0 pb-4 z-10 shrink-0">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <!-- Search icon -->
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z"></path>
                        </svg>
                    </div>
                    <input type="text" x-model="search" @input="filterProducts()"
                        placeholder="Cari produk berdasarkan nama..."
                        class="w-full pl-12 pr-4 h-14 text-lg rounded-xl border border-slate-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all shadow-sm">
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
                            <div x-show="cart.find(i => i.id === product.id)"
                                class="absolute top-2.5 right-2.5 text-green-600">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>

                            <!-- Habis badge -->
                            <span x-show="product.stock === 0"
                                class="absolute top-2.5 right-2.5 bg-red-100 text-red-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
                                Habis
                            </span>

                            <div>
                                <!-- Category Badge -->
                                <span x-text="product.category"
                                    class="inline-block text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md uppercase tracking-wider mb-2"></span>

                                <!-- Product Name -->
                                <h4 x-text="product.name"
                                    class="font-semibold text-slate-800 line-clamp-2 min-h-[40px] leading-snug"></h4>
                            </div>

                            <div class="mt-4">
                                <!-- Price -->
                                <div x-text="formatRp(product.price)" class="text-green-600 font-bold text-lg"></div>

                                <!-- Footer Details: Stock + Rack -->
                                <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-50">
                                    <div class="text-xs font-semibold"
                                        :class="product.stock > 10 ? 'text-slate-400' : (product.stock > 0 ? 'text-amber-500' : 'text-red-500')">
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
                    <svg class="w-16 h-16 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9">
                        </path>
                    </svg>
                    <p class="text-slate-500 font-medium">Tidak ada produk ditemukan</p>
                    <p class="text-xs text-slate-400 mt-1">Coba kata kunci pencarian atau kategori lain</p>
                </div>
            </div>

        </div>

        <!-- RIGHT PANEL: Checkout Cart Summary -->
        <div
            class="w-80 flex-shrink-0 flex flex-col h-full bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

            <!-- Header -->
            <div class="bg-slate-900 text-white px-5 py-4 flex items-center justify-between shrink-0">
                <span class="font-bold text-sm tracking-wide uppercase">Keranjang Transaksi</span>
                <span x-text="totalItems" class="bg-green-500 text-white rounded-full text-xs font-bold px-2.5 py-0.5"
                    x-show="totalItems > 0"></span>
            </div>

            <!-- Cart Items List -->
            <div class="flex-1 overflow-y-auto max-h-[45vh] divide-y divide-slate-100">
                <template x-for="item in cart" :key="item.id">
                    <div class="px-4 py-3 flex flex-col gap-2">
                        <div class="flex justify-between items-start gap-2">
                            <div class="min-w-0 flex-1">
                                <div x-text="item.name" class="font-medium text-sm text-slate-800 truncate"
                                    :title="item.name"></div>
                                <div x-text="item.category"
                                    class="text-[9px] uppercase font-bold text-slate-400 tracking-wider mt-0.5"></div>
                            </div>
                            <button @click="removeFromCart(item.id)"
                                class="text-red-400 hover:text-red-600 text-lg font-bold px-1 transition-colors cursor-pointer shrink-0">
                                &times;
                            </button>
                        </div>

                        <div class="flex justify-between items-center mt-1">
                            <!-- Qty adjustment controls -->
                            <div class="flex items-center gap-1.5">
                                <button @click="decreaseQty(item)"
                                    class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-extrabold text-sm transition-all cursor-pointer select-none">
                                    −
                                </button>
                                <span x-text="item.qty" class="font-semibold text-slate-800 text-sm w-7 text-center"></span>
                                <button @click="increaseQty(item)"
                                    class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-extrabold text-sm transition-all cursor-pointer select-none">
                                    ＋
                                </button>
                            </div>

                            <!-- Subtotal -->
                            <div x-text="formatRp(item.price * item.qty)" class="font-semibold text-slate-900 text-sm">
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Empty State -->
                <div x-show="cart.length === 0" class="py-16 text-center flex flex-col items-center justify-center">
                    <!-- Cart icon -->
                    <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.116 60.116 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z">
                        </path>
                    </svg>
                    <span class="text-slate-400 text-sm font-semibold block">Keranjang kosong</span>
                    <span class="text-slate-400 text-[11px] mt-0.5 block px-4">Klik produk di sebelah kiri untuk
                        menambahkan</span>
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
                <div
                    class="flex justify-between items-center text-slate-900 font-bold text-lg pt-2.5 border-t border-slate-200">
                    <span>TOTAL</span>
                    <span x-text="formatRp(total)"></span>
                </div>
            </div>

            <!-- Action checkout buttons -->
            <div class="px-5 pb-5 pt-2 bg-slate-50 space-y-2 shrink-0">
                <button x-on:click="openConfirmModal()" x-bind:disabled="cart.length === 0"
                    :class="cart.length === 0 ? 'opacity-50 cursor-not-allowed bg-green-400' : 'bg-green-600 hover:bg-green-700 active:bg-green-800 hover:shadow-md'"
                    class="btn-primary w-full justify-center min-h-[48px] cursor-pointer text-white font-bold transition-all shadow-sm">
                    <span>🔍 Bayar</span>
                </button>

                <button x-on:click="resetCart()" x-show="cart.length > 0 && !isProcessing"
                    class="block w-full text-center text-red-500 hover:text-red-700 text-xs font-bold pt-2 transition-colors cursor-pointer select-none">
                    Batalkan Transaksi
                </button>
            </div>

        </div>

        <!-- TOAST NOTIFICATIONS -->
        <div class="fixed bottom-6 right-6 z-[70] space-y-3">
            <!-- Success Toast -->
            <div x-show="showSuccess" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                class="bg-green-600 text-white rounded-xl shadow-lg px-5 py-4 flex items-center gap-3 max-w-sm border border-green-500"
                x-cloak>
                <span class="text-xl">✅</span>
                <div>
                    <p x-text="successMessage" class="text-sm font-bold"></p>
                </div>
            </div>

            <!-- Error Toast -->
            <div x-show="showError" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                class="bg-red-600 text-white rounded-xl shadow-lg px-5 py-4 flex items-center gap-3 max-w-sm border border-red-500"
                x-cloak>
                <span class="text-xl">⚠️</span>
                <div>
                    <p x-text="errorMessage" class="text-sm font-bold"></p>
                </div>
            </div>
        </div>

        <!-- MODAL CONFIRMATION (DOUBLE CHECK) -->
        <div x-show="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 md:p-10"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>

            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                @click="showConfirmModal = false"></div>

            <!-- Modal Body -->
            <div class="relative w-full max-w-4xl bg-white rounded-2xl shadow-2xl flex flex-col md:flex-row overflow-hidden max-h-[85vh] md:h-[600px]"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 scale-95">

                <!-- Left Panel: Verification Checklist -->
                <div class="flex-1 p-6 flex flex-col min-h-0 border-b md:border-b-0 md:border-r border-slate-200">
                    <div class="flex items-center justify-between mb-4 shrink-0">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-900">Periksa Ulang Barang</h3>
                            <p class="text-xs text-slate-500 font-medium">Konfirmasi item dengan pelanggan sebelum
                                pembayaran</p>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" @click="checkAllItems()"
                                class="text-xs font-bold text-green-700 hover:text-green-800 bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-lg transition-colors cursor-pointer select-none">
                                Centang Semua
                            </button>
                            <button type="button" @click="uncheckAllItems()"
                                class="text-xs font-bold text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg transition-colors cursor-pointer select-none">
                                Reset
                            </button>
                        </div>
                    </div>

                    <!-- Checklist Items Container -->
                    <div class="flex-1 overflow-y-auto divide-y divide-slate-100 pr-1 select-none">
                        <template x-for="item in cart" :key="item.id">
                            <div @click="toggleCheckItem(item.id)"
                                :class="checkedItemIds.includes(item.id) ? 'bg-green-50/70 border-green-200 text-green-950 shadow-sm' : 'hover:bg-slate-50 border-slate-100 text-slate-800'"
                                class="p-3.5 rounded-xl border transition-all duration-150 cursor-pointer flex items-center justify-between gap-4 mb-2">

                                <div class="flex items-center gap-3 min-w-0">
                                    <!-- Checkbox circle -->
                                    <div :class="checkedItemIds.includes(item.id) ? 'bg-green-600 border-green-600 text-white' : 'border-slate-300 bg-white text-transparent'"
                                        class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3.5"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <span
                                            :class="checkedItemIds.includes(item.id) ? 'line-through text-slate-400 font-normal' : 'font-bold'"
                                            class="text-sm block truncate" x-text="item.name"></span>
                                        <span class="text-xs text-slate-500 font-semibold"
                                            x-text="formatRp(item.price)"></span>
                                    </div>
                                </div>

                                <div class="text-right shrink-0">
                                    <span
                                        class="text-xs font-extrabold bg-slate-100 text-slate-700 px-2.5 py-0.5 rounded-md"
                                        x-text="item.qty + 'x'"></span>
                                    <span class="text-sm font-bold block mt-1"
                                        :class="checkedItemIds.includes(item.id) ? 'text-slate-400' : 'text-slate-900'"
                                        x-text="formatRp(item.price * item.qty)"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-4 pt-3 border-t border-slate-100 shrink-0">
                        <div class="flex justify-between items-center text-xs font-bold text-slate-500 mb-1.5">
                            <span>Verifikasi Barang:</span>
                            <span class="text-green-600"
                                x-text="checkedItemIds.length + ' / ' + cart.length + ' Item'"></span>
                        </div>
                        <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-green-500 h-full transition-all duration-300"
                                :style="'width: ' + (cart.length > 0 ? (checkedItemIds.length / cart.length * 100) : 0) + '%'">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Panel: Payment Inputs -->
                <div class="w-full md:w-96 p-6 bg-slate-50/50 flex flex-col min-h-0">
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-200 shrink-0">
                        <span class="font-extrabold text-xs text-slate-600 uppercase tracking-wider">Rincian
                            Pembayaran</span>
                        <button type="button" @click="showConfirmModal = false"
                            class="text-slate-400 hover:text-slate-600 text-2xl font-bold cursor-pointer transition-colors p-1">
                            &times;
                        </button>
                    </div>

                    <!-- Total Bill Box -->
                    <div class="bg-slate-900 text-white rounded-2xl p-5 mb-5 shadow-md shrink-0 space-y-1.5">
                        <div
                            class="flex justify-between items-center text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                            <span>Subtotal</span>
                            <span x-text="formatRp(subtotal)"></span>
                        </div>
                        <div class="flex justify-between items-center text-[10px] text-red-400 font-bold uppercase tracking-wider"
                            x-show="Number(discountAmount) > 0" x-cloak>
                            <span>Diskon</span>
                            <span x-text="'-' + formatRp(discountAmount)"></span>
                        </div>
                        <div class="border-t border-slate-800 pt-1.5 flex justify-between items-center">
                            <span class="text-xs text-slate-300 font-bold uppercase tracking-wider">TOTAL BAYAR</span>
                            <span class="text-3xl font-black tracking-tight text-white" x-text="formatRp(total)"></span>
                        </div>
                    </div>

                    <!-- Payment Inputs Container -->
                    <div class="space-y-4 flex-1 overflow-y-auto pr-1">

                        <!-- Metode Pembayaran Selector -->
                        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                            <label class="text-slate-600 text-[10px] font-bold uppercase tracking-wider block mb-2.5">Metode Pembayaran</label>
                            <div class="grid grid-cols-2 gap-2 bg-slate-100 p-1 rounded-xl">
                                <button type="button" @click="setPaymentMethod('cash')"
                                    :class="pembayaran === 'cash' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                    class="py-2.5 text-xs font-bold rounded-lg transition-all cursor-pointer select-none flex items-center justify-center gap-1.5">
                                    💵 Tunai / Cash
                                </button>
                                <button type="button" @click="setPaymentMethod('cashless')"
                                    :class="pembayaran === 'cashless' ? 'bg-green-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                    class="py-2.5 text-xs font-bold rounded-lg transition-all cursor-pointer select-none flex items-center justify-center gap-1.5">
                                    💳 Cashless / QRIS
                                </button>
                            </div>
                        </div>

                        <!-- Discount Input (Pelanggan Spesial) -->
                        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-2">
                            <div class="flex items-center justify-between">
                                <label for="discount_amount"
                                    class="text-slate-700 text-[10px] font-bold uppercase tracking-wider">Diskon Langsung
                                    (Pelanggan Spesial)</label>
                                <span
                                    class="text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-md">Nominal
                                    (Rp)</span>
                            </div>
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-extrabold text-sm">
                                    Rp
                                </div>
                                <input type="text" id="discount_amount" x-model="discountAmountFormatted"
                                    @input="let parsed = parseNumberInput($event.target.value); if (parsed > subtotal) { parsed = subtotal; }; discountAmountFormatted = formatNumberInput(parsed); discountAmount = parsed; if (pembayaran === 'cashless') { cashPaid = total; cashPaidFormatted = formatNumberInput(total); }"
                                    class="w-full pl-11 pr-4 h-10 text-base font-bold rounded-lg border border-slate-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all"
                                    placeholder="0">
                            </div>
                            <p class="text-[10px] text-slate-400">Nominal diskon langsung dalam Rupiah untuk pelanggan
                                setia.</p>
                        </div>

                        <!-- Cash Payment specific inputs -->
                        <div x-show="pembayaran === 'cash'" class="space-y-4" x-transition>
                            <div>
                                <label for="cash_paid"
                                    class="form-label text-slate-600 text-[10px] font-bold uppercase tracking-wider mb-2">UANG
                                    TUNAI DITERIMA</label>
                                <div class="relative">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 font-extrabold text-base">
                                        Rp
                                    </div>
                                    <input type="text" id="cash_paid" x-model="cashPaidFormatted"
                                        @input="cashPaidFormatted = formatNumberInput($event.target.value); cashPaid = parseNumberInput(cashPaidFormatted)"
                                        class="w-full pl-11 pr-4 h-12 text-lg font-black rounded-xl border border-slate-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all shadow-sm"
                                        placeholder="0">
                                </div>
                            </div>

                            <!-- Cash Suggestions -->
                            <div class="grid grid-cols-2 gap-2">
                                <template x-for="suggestion in cashSuggestions" :key="suggestion">
                                    <button type="button" @click="cashPaid = suggestion; cashPaidFormatted = formatNumberInput(suggestion)"
                                        :class="cashPaid === suggestion ? 'bg-green-600 text-white border-green-600 shadow-sm' : 'bg-white hover:bg-slate-100 text-slate-700 border-slate-200 hover:border-slate-300'"
                                        class="py-2.5 text-xs font-bold border rounded-xl shadow-sm transition-all cursor-pointer select-none">
                                        <span x-text="suggestion === total ? 'Uang Pas' : formatRp(suggestion)"></span>
                                    </button>
                                </template>
                            </div>

                            <!-- Return Change -->
                            <div class="p-4 rounded-xl border transition-all duration-200"
                                :class="cashPaid >= total ? 'bg-green-50 border-green-200 text-green-800 shadow-sm' : 'bg-slate-100 border-slate-200 text-slate-500'">
                                <span class="text-[9px] font-bold uppercase tracking-wider block mb-1">KEMBALIAN</span>
                                <span class="text-xl font-extrabold tracking-tight"
                                    x-text="cashPaid >= total ? formatRp(cashPaid - total) : 'Rp 0'"></span>
                            </div>
                        </div>

                        <!-- Cashless Notice Widget -->
                        <div x-show="pembayaran === 'cashless'" class="p-4 rounded-xl border border-green-200 bg-green-50/50 text-green-800 shadow-sm space-y-2.5" x-transition>
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-bold uppercase tracking-wider block text-green-700">Pembayaran Cashless</span>
                                <span class="bg-green-600 text-white text-[9px] font-bold uppercase px-2 py-0.5 rounded-md">QRIS / EDC</span>
                            </div>
                            <p class="text-xs font-semibold leading-relaxed">Silakan proses transaksi non-tunai melalui QRIS atau mesin EDC sebesar <span class="font-extrabold text-green-950 underline" x-text="formatRp(total)"></span>.</p>
                            <div class="flex items-center gap-2 pt-1">
                                <span class="relative flex h-2.5 w-2.5">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                                </span>
                                <span class="text-[10px] text-green-700 font-extrabold uppercase tracking-wider">Menunggu Pembayaran Non-Tunai</span>
                            </div>
                        </div>

                        <!-- Opsi Nota Toggle -->
                        <div
                            class="flex items-center justify-between py-3 px-4 bg-white rounded-xl border border-slate-200 shadow-sm">
                            <span class="text-xs font-bold text-slate-700">Cetak Nota Fisik</span>
                            <label class="relative inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" x-model="printNota" class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600">
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Checkout -->
                    <div class="pt-4 mt-4 border-t border-slate-200 shrink-0">
                        <!-- Indicator if not checked all -->
                        <div x-show="!isAllChecked()"
                            class="text-[11px] text-amber-700 font-bold bg-amber-50 rounded-xl p-3 border border-amber-200 text-center mb-3">
                            ⚠️ Harap periksa & centang semua barang untuk memastikan keakuratan.
                        </div>

                        <button type="button" @click="submitTransaction()"
                            x-bind:disabled="isProcessing || !isAllChecked() || (pembayaran === 'cash' && (cashPaid === '' || cashPaid < total))"
                            :class="(isProcessing || !isAllChecked() || (pembayaran === 'cash' && (cashPaid === '' || cashPaid < total))) ? 'opacity-50 cursor-not-allowed bg-green-400' : 'bg-green-600 hover:bg-green-700 active:bg-green-800 hover:shadow-lg'"
                            class="btn-primary w-full justify-center min-h-[48px] text-white text-base font-bold rounded-xl transition-all shadow-md cursor-pointer">
                            <span x-text="isProcessing ? 'Memproses...' : '✓ Selesaikan Transaksi'">✓ Selesaikan
                                Transaksi</span>
                        </button>

                        <button type="button" @click="showConfirmModal = false"
                            class="btn-secondary w-full justify-center min-h-[38px] text-xs font-semibold mt-2">
                            Kembali Ke Keranjang
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('transactionCart', () => ({
                    products: @json($products),
                    filteredProducts: [],
                    cart: [],
                    search: '',
                    selectedCategory: '',
                    isProcessing: false,
                    showSuccess: false,
                    showError: false,
                    successMessage: '',
                    errorMessage: '',
                    showConfirmModal: false,
                    checkedItemIds: [],
                    cashPaid: '',
                    cashPaidFormatted: '',
                    printNota: true,
                    discountAmount: 0,
                    discountAmountFormatted: '',
                    pembayaran: 'cash',

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
                        return Math.max(0, this.subtotal - Math.min(this.subtotal, Number(this.discountAmount) || 0));
                    },

                    formatRp(n) {
                        return 'Rp ' + Number(n).toLocaleString('id-ID');
                    },

                    formatNumberInput(value) {
                        let clean = String(value).replace(/\D/g, '');
                        if (!clean) return '';
                        return Number(clean).toLocaleString('id-ID');
                    },
                    parseNumberInput(formattedValue) {
                        let clean = String(formattedValue).replace(/\D/g, '');
                        return Number(clean) || 0;
                    },

                    resetCart() {
                        if (confirm('Batalkan transaksi ini?')) this.cart = [];
                    },

                    openConfirmModal() {
                        if (this.cart.length === 0) return;
                        this.checkedItemIds = [];
                        this.cashPaid = '';
                        this.cashPaidFormatted = '';
                        this.discountAmount = 0;
                        this.discountAmountFormatted = '';
                        this.pembayaran = 'cash';
                        this.showConfirmModal = true;
                    },

                    setPaymentMethod(method) {
                        this.pembayaran = method;
                        if (method === 'cashless') {
                            this.cashPaid = this.total;
                            this.cashPaidFormatted = this.formatNumberInput(this.total);
                        } else {
                            this.cashPaid = '';
                            this.cashPaidFormatted = '';
                        }
                    },

                    toggleCheckItem(id) {
                        if (this.checkedItemIds.includes(id)) {
                            this.checkedItemIds = this.checkedItemIds.filter(x => x !== id);
                        } else {
                            this.checkedItemIds.push(id);
                        }
                    },

                    checkAllItems() {
                        this.checkedItemIds = this.cart.map(i => i.id);
                    },

                    uncheckAllItems() {
                        this.checkedItemIds = [];
                    },

                    isAllChecked() {
                        return this.checkedItemIds.length === this.cart.length;
                    },

                    get cashSuggestions() {
                        const t = this.total;
                        if (t <= 0) return [];
                        const suggestions = [t];

                        const options = [10000, 20000, 50000, 100000, 200000];
                        options.forEach(opt => {
                            if (opt > t && !suggestions.includes(opt)) {
                                suggestions.push(opt);
                            }
                        });

                        const next10k = Math.ceil(t / 10000) * 10000;
                        if (next10k > t && !suggestions.includes(next10k)) {
                            suggestions.push(next10k);
                        }

                        const next50k = Math.ceil(t / 50000) * 50000;
                        if (next50k > t && !suggestions.includes(next50k)) {
                            suggestions.push(next50k);
                        }

                        const next100k = Math.ceil(t / 100000) * 100000;
                        if (next100k > t && !suggestions.includes(next100k)) {
                            suggestions.push(next100k);
                        }

                        return suggestions.filter(x => x >= t).sort((a, b) => a - b).slice(0, 4);
                    },

                    submitTransaction() {
                        if (this.pembayaran === 'cashless') {
                            this.cashPaid = this.total;
                        }
                        this.processPayment(this.printNota);
                    },

                    async processPayment(printNota) {
                        if (this.cart.length === 0) return;
                        this.isProcessing = true;

                        try {
                            const response = await fetch('/transaksi', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document
                                        .querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content'),
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    items: this.cart.map(i => ({
                                        product_id: i.id,
                                        quantity: i.qty,
                                        unit_price: i.price,
                                    })),
                                    printed_nota: printNota,
                                    is_special_price: (Number(this.discountAmount) || 0) > 0,
                                    discount: Number(this.discountAmount) || 0,
                                    pembayaran: this.pembayaran,
                                })
                            });

                            const data = await response.json();

                            if (data.success) {
                                if (printNota) {
                                    window.open('/transaksi/' + data.transaction_id + '/nota', '_blank');
                                }
                                this.cart = [];
                                this.showSuccess = true;
                                this.successMessage = 'Transaksi #' + data.transaction_id + ' berhasil!';
                                setTimeout(() => this.showSuccess = false, 4000);

                                // Close modal on success
                                this.showConfirmModal = false;

                                // Refresh product stocks
                                const res = await fetch('/transaksi?json=1');
                                if (res.ok) {
                                    const newProducts = await res.json();
                                    this.products = newProducts;
                                    this.filterProducts();
                                }
                            } else {
                                this.errorMessage = data.message;
                                this.showError = true;
                                setTimeout(() => this.showError = false, 5000);
                            }
                        } catch (e) {
                            this.errorMessage = 'Terjadi kesalahan koneksi.';
                            this.showError = true;
                            setTimeout(() => this.showError = false, 5000);
                        } finally {
                            this.isProcessing = false;
                        }
                    }
                }));
            });
        </script>
    @endpush
@endsection