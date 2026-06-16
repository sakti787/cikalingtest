@extends('layouts.app')

@section('title', 'Peta Rak Digital')
@section('page-title', 'Peta Rak Digital')

@section('content')
<div x-data="rackMap" class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Peta Rak Digital</h1>
            <p class="text-sm text-slate-500 mt-1">Representasi visual tata letak rak barang toko berdasarkan kategori.</p>
        </div>
        <div class="text-xs text-slate-400 font-semibold bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-lg self-start sm:self-center">
            Terakhir Diperbarui: {{ now()->translatedFormat('d M Y H:i') }}
        </div>
    </div>

    <!-- 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        <!-- LEFT: Rack Map layout (col-span-3) -->
        <div class="card lg:col-span-3 bg-white border border-slate-200 shadow-sm p-0 overflow-hidden">
            <!-- Header Filter Section -->
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-bold text-slate-800 text-sm tracking-wide uppercase">Layout Toko</h3>
                
                <!-- Category Filter Pills -->
                <div class="flex gap-2 flex-wrap mt-3">
                    <button x-on:click="filterCat = ''" 
                        :class="filterCat === '' ? 'bg-slate-800 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                        class="rounded-full px-4 py-1 text-xs font-bold transition-all cursor-pointer">
                        Semua
                    </button>
                    @foreach($categories as $category)
                        <button x-on:click="filterCat = '{{ $category->category_name }}'" 
                            :class="filterCat === '{{ $category->category_name }}' ? 'bg-slate-800 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                            class="rounded-full px-4 py-1 text-xs font-bold transition-all cursor-pointer">
                            {{ $category->category_name }}
                        </button>
                    @endforeach
                    <button x-on:click="filterCat = 'kosong'" 
                        :class="filterCat === 'kosong' ? 'bg-slate-800 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                        class="rounded-full px-4 py-1 text-xs font-bold transition-all cursor-pointer">
                        Kosong
                    </button>
                </div>
            </div>

            <!-- Rack Grid Areas -->
            <div class="p-6 space-y-6">
                <template x-for="area in ['A', 'B', 'C']" :key="area">
                    <div x-show="filteredRacks.some(r => r.code.startsWith(area))" class="space-y-2">
                        <!-- Area Name -->
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                            Area <span x-text="area"></span>
                        </div>
                        
                        <!-- Grid list of racks in the area -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                            <template x-for="rack in filteredRacks.filter(r => r.code.startsWith(area))" :key="rack.id">
                                <div x-on:click="selectRack(rack)"
                                    :class="[
                                        selectedRack?.id === rack.id ? 'ring-2 ring-green-600 ring-offset-2' : '',
                                        categoryColor(rack.category) === 'blue' ? 'bg-blue-50/50 border-blue-200 text-blue-900 hover:border-blue-400' : '',
                                        categoryColor(rack.category) === 'amber' ? 'bg-amber-50/50 border-amber-200 text-amber-900 hover:border-amber-400' : '',
                                        categoryColor(rack.category) === 'green' ? 'bg-green-50/50 border-green-200 text-green-900 hover:border-green-400' : '',
                                        categoryColor(rack.category) === 'slate' ? 'bg-slate-50/50 border-slate-200 text-slate-700 hover:border-slate-300' : ''
                                    ]"
                                    class="rounded-xl border-2 p-3 cursor-pointer transition-all hover:shadow-md flex flex-col justify-between min-h-[110px]"
                                    :style="rack.count === 0 ? 'background-color: #f8fafc; border-color: #e2e8f0; color: #64748b;' : ''">
                                    
                                    <div>
                                        <!-- Rack Code -->
                                        <div class="text-lg font-bold" x-text="rack.code"></div>
                                        <!-- Category Label -->
                                        <div class="text-[9px] uppercase tracking-wider font-semibold opacity-70 truncate mt-0.5" x-text="rack.count > 0 ? rack.category : 'Kosong'"></div>
                                    </div>

                                    <div class="mt-3">
                                        <!-- Capacity counter -->
                                        <div class="text-xs font-bold text-slate-700" x-text="rack.count + ' / ' + rack.capacity + ' item'"></div>
                                        <!-- Capacity level bar -->
                                        <div class="w-full bg-slate-200 rounded-full h-1.5 mt-1.5 overflow-hidden">
                                            <div :class="[
                                                categoryColor(rack.category) === 'blue' ? 'bg-blue-600' : '',
                                                categoryColor(rack.category) === 'amber' ? 'bg-amber-500' : '',
                                                categoryColor(rack.category) === 'green' ? 'bg-green-600' : '',
                                                categoryColor(rack.category) === 'slate' ? 'bg-slate-400' : ''
                                            ]"
                                                 :style="'width: ' + (rack.count / rack.capacity * 100) + '%'"
                                                 class="h-1.5 rounded-full transition-all duration-300"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Static Entrance + Cashier Gate -->
                <div class="bg-slate-900 text-white text-center text-xs py-3 rounded-xl mt-6 font-bold tracking-wider uppercase shadow-sm">
                    🚪 Pintu Masuk / Area Kasir Utama
                </div>
            </div>
        </div>

        <!-- RIGHT: Detail Panel (col-span-2) -->
        <div class="lg:col-span-2 h-full flex flex-col">
            
            <!-- Default Placeholder -->
            <div x-show="!selectedRack" class="card bg-white border border-slate-200 shadow-sm py-20 text-center flex flex-col items-center justify-center h-full">
                <!-- Map icon -->
                <svg class="w-16 h-16 text-slate-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503-3.485 2.197-1.099A2.25 2.25 0 0 0 21 10.682V4.912a2.249 2.249 0 0 0-2.835-2.148L15 3.75l-6-1.5-4.233 1.411A2.25 2.25 0 0 0 3 4.912v5.77c0 .866.497 1.66 1.282 2.052L9 15.75l6 1.5 5.233-1.744c.46-.154.767-.585.767-1.069V10.682Z"></path>
                </svg>
                <h3 class="text-slate-500 font-semibold text-sm">Pilih Rak</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-[200px] mx-auto leading-relaxed">Klik salah satu rak di tata letak toko untuk melihat detail produk yang tersimpan.</p>
            </div>

            <!-- Detail Card -->
            <div x-show="selectedRack" class="card bg-white border border-slate-200 shadow-sm p-6 space-y-6 h-full flex flex-col justify-between" x-cloak>
                <div>
                    <!-- Header Info -->
                    <div class="pb-4 border-b border-slate-100">
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="text-xl font-bold text-slate-900">Detail Rak <span class="font-mono text-green-600" x-text="selectedRack?.code"></span></h2>
                            
                            <!-- Category Badge -->
                            <span x-show="selectedRack?.count > 0" 
                                  :class="[
                                      categoryColor(selectedRack?.category) === 'blue' ? 'bg-blue-100 text-blue-700' : '',
                                      categoryColor(selectedRack?.category) === 'amber' ? 'bg-amber-100 text-amber-700' : '',
                                      categoryColor(selectedRack?.category) === 'green' ? 'bg-green-100 text-green-700' : ''
                                  ]"
                                  class="rounded-full px-2.5 py-0.5 text-xs font-semibold inline-block" 
                                  x-text="selectedRack?.category">
                            </span>
                            <span x-show="selectedRack?.count === 0" class="bg-slate-100 text-slate-600 rounded-full px-2.5 py-0.5 text-xs font-semibold inline-block">
                                Kosong
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-2 font-medium">
                            Kapasitas Terisi: <strong class="text-slate-700"><span x-text="selectedRack?.count"></span> / <span x-text="selectedRack?.capacity"></span></strong> item
                        </p>
                    </div>

                    <!-- Products Table -->
                    <div class="mt-5">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Daftar Produk Di Rak</h4>
                        
                        <div class="overflow-x-auto">
                            <table class="table-base border border-slate-100 rounded-lg overflow-hidden">
                                <thead>
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th class="text-right">Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="product in selectedRack?.products" :key="product.id">
                                        <tr>
                                            <td class="font-medium text-slate-800 text-xs" x-text="product.name"></td>
                                            <td class="text-right font-bold text-xs" :class="product.stock > 0 ? 'text-slate-900' : 'text-red-500'" x-text="product.stock"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            
                            <!-- Empty rack state -->
                            <template x-if="selectedRack?.count === 0">
                                <div class="text-center py-10 border border-dashed border-slate-200 rounded-lg">
                                    <span class="text-xs text-slate-400 block">Rak ini kosong</span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Recommendation box -->
                <div class="mt-6 pt-5 border-t border-slate-100 space-y-3 shrink-0">
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Rekomendasi Penempatan</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Sistem merekomendasikan penempatan barang baru di rak ini jika memiliki kategori yang sesuai:</p>
                    <div class="flex flex-wrap gap-2">
                        <span :class="[
                            categoryColor(selectedRack?.category) === 'blue' ? 'bg-blue-50 text-blue-700 border-blue-200' : '',
                            categoryColor(selectedRack?.category) === 'amber' ? 'bg-amber-50 text-amber-700 border-amber-200' : '',
                            categoryColor(selectedRack?.category) === 'green' ? 'bg-green-50 text-green-700 border-green-200' : '',
                            categoryColor(selectedRack?.category) === 'slate' ? 'bg-slate-50 text-slate-600 border-slate-200' : ''
                        ]" class="border text-[10px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider" x-text="selectedRack?.count > 0 ? selectedRack?.category : 'Semua Kategori (Kosong)'"></span>
                    </div>
                    
                    <a href="{{ route('barang.create') }}" class="btn-primary text-xs px-3 py-2.5 mt-2 w-full text-center inline-block cursor-pointer font-semibold shadow-sm">
                        Input Barang Baru
                    </a>
                </div>

            </div>

        </div>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('rackMap', () => ({
            racks: @json($racks),
            selectedRack: null,
            filterCat: '',
            
            get filteredRacks() {
                if (!this.filterCat) return this.racks;
                if (this.filterCat === 'kosong') {
                    return this.racks.filter(r => r.count === 0);
                }
                return this.racks.filter(r => r.category === this.filterCat);
            },
            
            selectRack(rack) {
                this.selectedRack = rack;
            },
            
            categoryColor(cat) {
                const colors = {
                    'Aksesori Rumah Tangga': 'blue',
                    'Material Bangunan':     'amber',
                    'Suku Cadang Motor':     'green',
                };
                return colors[cat] || 'slate';
            }
        }));
    });
</script>
@endpush
@endsection
