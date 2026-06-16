@extends('layouts.app')

@section('title', 'Input Barang Baru')
@section('page-title', 'Input Barang Baru')

@section('content')
<div x-data="inputBarangForm" class="space-y-6">

    <!-- Header -->
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Input Barang Baru</h1>
        <p class="text-sm text-slate-500 mt-1">Tambahkan produk baru ke dalam sistem pergudangan dan atur penempatan raknya.</p>
    </div>

    <!-- Validation error summary -->
    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl max-w-4xl mb-4">
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

    <!-- 2-Column Form Layout -->
    <form action="{{ route('barang.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl">
        @csrf

        <!-- Hidden inputs -->
        <input type="hidden" name="rack_id" x-bind:value="selectedRackId">

        <!-- LEFT CARD: Informasi Barang -->
        <div class="card bg-white border border-slate-200 shadow-sm p-6 space-y-5">
            <h3 class="font-bold text-slate-800 text-sm tracking-wide uppercase border-b border-slate-100 pb-3">Informasi Barang</h3>

            <!-- Nama Barang -->
            <div>
                <label for="product_name" class="form-label">Nama Barang</label>
                <input type="text" id="product_name" name="product_name" required 
                       class="input-field" value="{{ old('product_name') }}" 
                       placeholder="Contoh: Kunci Pas 12mm">
                @error('product_name')
                    <p class="form-error mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kategori -->
            <div>
                <label for="category_id" class="form-label">Kategori</label>
                <select id="category_id" name="category_id" x-model="selectedCategoryId" 
                        x-on:change="selectedRackId = ''; useRecommended = true" required 
                        class="input-field">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="form-error mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jumlah Stok Masuk -->
            <div>
                <label for="stock" class="form-label">Jumlah Stok Masuk</label>
                <input type="number" id="stock" name="stock" min="1" required 
                       class="input-field" value="{{ old('stock', 1) }}" 
                       placeholder="Jumlah unit masuk">
                @error('stock')
                    <p class="form-error mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Harga Beli -->
            <div>
                <label for="buy_price" class="form-label">Harga Beli</label>
                <div class="relative rounded-lg shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-slate-400 text-sm font-bold">Rp</span>
                    </div>
                    <input type="number" id="buy_price" name="buy_price" step="500" x-model.number="buyPrice" required 
                           class="input-field pl-9" placeholder="0">
                </div>
                @error('buy_price')
                    <p class="form-error mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Estimasi Harga Jual (Calculated) -->
            <div class="space-y-1.5 pt-3 border-t border-slate-100">
                <div class="p-3.5 bg-slate-50 border border-slate-100 rounded-xl flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Estimasi Harga Jual</span>
                        <span class="text-base font-extrabold text-slate-800 mt-0.5 block" x-text="formatRp(estimatedSellPrice)">Rp 0</span>
                    </div>
                    <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">
                        Margin 30%
                    </span>
                </div>
                <p class="text-[10px] text-slate-400 leading-normal">
                    Margin 30% dari harga beli (dibulatkan ke ratusan terdekat). Pemilik dapat menyesuaikan harga ini kembali setelah barang disimpan.
                </p>
            </div>
        </div>

        <!-- RIGHT CARD: Penempatan Rak -->
        <div class="card bg-white border border-slate-200 shadow-sm p-6 flex flex-col justify-between space-y-6">
            
            <div class="space-y-5">
                <h3 class="font-bold text-slate-800 text-sm tracking-wide uppercase border-b border-slate-100 pb-3">Penempatan Rak</h3>

                <!-- Section Rekomendasi Rak (Visible when category is selected) -->
                <div x-show="selectedCategoryId" class="space-y-3" x-cloak>
                    <label class="form-label block">Rekomendasi Rak</label>
                    <p class="text-xs text-slate-400 mt-0.5">Rak yang sesuai dengan kategori barang:</p>

                    <!-- Recommendations exist -->
                    <div class="space-y-2 max-h-[160px] overflow-y-auto pr-1" x-show="recommendedRacks.length > 0">
                        <template x-for="rack in recommendedRacks" :key="rack.id">
                            <div x-on:click="selectedRackId = rack.id; useRecommended = true"
                                 :class="selectedRackId == rack.id ? 'border-green-500 bg-green-50/50 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300'"
                                 class="rounded-xl border-2 p-3 cursor-pointer transition-all flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-slate-800 text-xs" x-text="rack.code"></span>
                                    <span class="text-[9px] text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md uppercase tracking-wider font-semibold ml-2" x-text="rack.category"></span>
                                </div>
                                <span class="text-xs font-semibold text-green-600" x-text="rack.available + ' slot tersedia'"></span>
                            </div>
                        </template>
                    </div>

                    <!-- No recommendations available -->
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-center gap-2" x-show="recommendedRacks.length === 0">
                        <span>⚠️</span>
                        <span>Tidak ada rak yang sesuai kategori ini dengan slot tersedia.</span>
                    </div>
                </div>

                <!-- Divider "ATAU PILIH MANUAL" -->
                <div x-show="selectedCategoryId" class="relative flex py-2 items-center" x-cloak>
                    <div class="flex-grow border-t border-slate-100"></div>
                    <span class="flex-shrink mx-3 text-[9px] text-slate-400 font-bold uppercase tracking-wider">ATAU PILIH MANUAL</span>
                    <div class="flex-grow border-t border-slate-100"></div>
                </div>

                <!-- Manual Select Dropdown -->
                <div x-show="selectedCategoryId" class="space-y-1" x-cloak>
                    <label for="manual_rack" class="form-label">Pilih Rak Manual</label>
                    <select id="manual_rack" x-model="selectedRackId" x-on:change="useRecommended = false" class="input-field">
                        <option value="">-- Tanpa Rak --</option>
                        <template x-for="rack in allRacks" :key="rack.id">
                            <option :value="rack.id" x-text="rack.code + ' — ' + rack.category + ' (' + rack.available + '/' + rack.capacity + ' slot)'"></option>
                        </template>
                    </select>
                </div>

                <!-- Category Mismatch Alert -->
                <div x-show="categoryMismatch" class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl space-y-1" x-cloak>
                    <div class="flex items-center gap-2 text-amber-800 font-bold text-xs">
                        <span>⚠️</span>
                        <span>Peringatan Mismatch Rak</span>
                    </div>
                    <p class="text-xs text-amber-700 leading-relaxed">
                        Rak <strong x-text="selectedRack?.code"></strong> adalah kategori <strong x-text="selectedRack?.category"></strong>, berbeda dengan kategori barang yang dipilih. Data tetap akan disimpan dengan catatan.
                    </p>
                </div>

                <!-- Default state message when no category selected -->
                <div x-show="!selectedCategoryId" class="py-10 text-center text-slate-400 text-xs">
                    Pilih kategori terlebih dahulu untuk melihat rekomendasi penempatan rak.
                </div>
            </div>

            <!-- Action buttons -->
            <div class="space-y-2 pt-4 border-t border-slate-100 shrink-0">
                <button type="submit" 
                        class="btn-primary w-full justify-center min-h-[44px] cursor-pointer"
                        x-bind:disabled="!selectedCategoryId || loading"
                        x-bind:class="loading ? 'opacity-75' : ''"
                        x-on:click="loading = true">
                    <span x-text="loading ? 'Menyimpan...' : 'Simpan Barang Baru'">Simpan Barang Baru</span>
                </button>
                <a href="{{ route('rak.index') }}" class="btn-secondary w-full justify-center min-h-[44px]">
                    Batal
                </a>
            </div>

        </div>

    </form>

</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('inputBarangForm', () => ({
            selectedCategoryId: null,
            selectedRackId: '',
            useRecommended: true,
            racks: @json($racks),
            buyPrice: 0,
            loading: false,
            
            get recommendedRacks() {
                if (!this.selectedCategoryId) return [];
                return this.racks.filter(r => 
                    r.category_id == this.selectedCategoryId && 
                    r.available > 0
                );
            },
            
            get allRacks() {
                return this.racks;
            },
            
            get selectedRack() {
                return this.racks.find(r => r.id == this.selectedRackId);
            },
            
            get categoryMismatch() {
                if (!this.selectedRack || !this.selectedCategoryId) return false;
                return this.selectedRack.category_id != this.selectedCategoryId;
            },
            
            get estimatedSellPrice() {
                return Math.ceil(this.buyPrice * 1.3 / 100) * 100;
            },
            
            formatRp(n) {
                return 'Rp ' + Number(n).toLocaleString('id-ID');
            }
        }));
    });
</script>
@endpush
@endsection
