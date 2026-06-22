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
        <div class="flex flex-wrap items-center gap-3 self-start sm:self-center">
            @if(auth()->user()->role === 'pemilik')
                <button x-on:click="openModal()" class="bg-green-600 hover:bg-green-700 text-white flex items-center gap-2 cursor-pointer text-xs font-bold py-2.5 px-4 rounded-xl shadow-sm hover:scale-[1.02] transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                    </svg>
                    Edit Tata Letak
                </button>
            @endif
            <div class="text-xs text-slate-400 font-semibold bg-slate-100 border border-slate-200 px-3 py-2.5 rounded-xl">
                Terakhir Diperbarui: {{ now()->translatedFormat('d M Y H:i') }}
            </div>
        </div>
    </div>

    <!-- 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        <!-- LEFT: Rack Map layout (col-span-3) -->
        <div class="card lg:col-span-3 bg-white border border-slate-200 shadow-sm p-0 overflow-hidden">
            <!-- Header Filter Section -->
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-bold text-slate-800 text-sm tracking-wide uppercase">Layout Toko (Grid 6x6)</h3>
                
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
                <!-- 6x6 Interactive Grid Map -->
                <div class="grid grid-cols-6 gap-3 aspect-square max-w-full md:max-w-[480px] mx-auto p-4 bg-slate-50 border border-slate-200 rounded-2xl shadow-inner">
                    <template x-for="cell in gridCells" :key="cell.row + '-' + cell.col">
                        <!-- Grid Cell -->
                        <div 
                            x-on:click="cell.item ? selectRack(cell.item) : null"
                            :class="[
                                cell.item ? 'cursor-pointer hover:shadow-md hover:scale-[1.02] border' : 'bg-slate-100/50 border border-dashed border-slate-200/80',
                                cell.item && selectedRack?.id === cell.item.id ? 'ring-2 ring-green-600 ring-offset-2 z-10' : '',
                                cell.item ? getItemColorClass(cell.item) : ''
                            ]"
                            class="rounded-xl flex flex-col justify-between p-2 aspect-square transition-all text-center select-none overflow-hidden relative"
                            :style="cell.item && isFilteredOut(cell.item) ? 'opacity: 0.2; filter: grayscale(80%); pointer-events: none;' : ''"
                        >
                            <!-- Content of cell if occupied -->
                            <template x-if="cell.item">
                                <div class="h-full flex flex-col justify-between">
                                    <div>
                                        <!-- Code -->
                                        <div class="text-xs sm:text-sm font-extrabold tracking-tight truncate leading-tight mt-0.5" x-text="cell.item.code"></div>
                                        <!-- Sublabel -->
                                        <div x-show="!cell.item.is_box" class="text-[7px] uppercase tracking-wider font-bold opacity-60 truncate mt-0.5" x-text="cell.item.count > 0 ? cell.item.category : 'Kosong'"></div>
                                        <div x-show="cell.item.is_box" class="text-[7px] uppercase tracking-widest font-black text-indigo-550 mt-0.5">Area</div>
                                    </div>
                                    
                                    <!-- If rack: Capacity indicators -->
                                    <template x-if="!cell.item.is_box">
                                        <div class="mt-1">
                                            <div class="text-[9px] font-black text-slate-700" x-text="cell.item.count + '/' + cell.item.capacity"></div>
                                            <!-- Small Progress bar -->
                                            <div class="w-full bg-slate-200/80 rounded-full h-1 overflow-hidden mt-0.5">
                                                <div :class="getProgressBarClass(cell.item)"
                                                     :style="'width: ' + Math.min(100, (cell.item.count / cell.item.capacity * 100)) + '%'"
                                                     class="h-1 rounded-full transition-all duration-300"></div>
                                             </div>
                                        </div>
                                    </template>
                                    <!-- If box: Icon -->
                                    <template x-if="cell.item.is_box">
                                        <div class="text-sm mt-1">
                                            🏢
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <!-- Coordinate if empty -->
                            <template x-if="!cell.item">
                                <div class="m-auto text-[8px] font-semibold text-slate-350" x-text="cell.row + ',' + cell.col"></div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Legend -->
                <div class="flex flex-wrap gap-x-4 gap-y-2 justify-center items-center pt-4 border-t border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded bg-blue-100 border border-blue-300"></span>
                        Aksesori Rumah Tangga
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded bg-amber-100 border border-amber-300"></span>
                        Material Bangunan
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded bg-green-100 border border-green-300"></span>
                        Suku Cadang Motor
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded bg-indigo-50 border border-indigo-200 border-dashed"></span>
                        Kotak Kustom (Area)
                    </div>
                </div>

                <!-- Static Entrance Info -->
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
                <h3 class="text-slate-500 font-semibold text-sm">Pilih Rak / Area</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-[200px] mx-auto leading-relaxed">Klik salah satu kotak pada grid layout toko untuk melihat detailnya.</p>
            </div>

            <!-- Detail Card (If Rack) -->
            <div x-show="selectedRack && !selectedRack.is_box" class="card bg-white border border-slate-200 shadow-sm p-6 space-y-6 h-full flex flex-col justify-between" x-cloak>
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

            <!-- Detail Card (If Custom Box/Area) -->
            <div x-show="selectedRack && selectedRack.is_box" class="card bg-white border border-slate-200 shadow-sm p-6 space-y-6 h-full flex flex-col justify-between" x-cloak>
                <div>
                    <!-- Header Info -->
                    <div class="pb-4 border-b border-slate-100">
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="text-xl font-bold text-slate-900">Area <span class="font-mono text-indigo-650" x-text="selectedRack?.code"></span></h2>
                            <span class="bg-indigo-100 text-indigo-705 rounded-full px-2.5 py-0.5 text-xs font-semibold inline-block">
                                Kotak Kustom
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-2 font-medium">
                            Area ini bukan merupakan rak penyimpanan produk, melainkan area/fasilitas toko.
                        </p>
                    </div>

                    <div class="mt-8 py-12 text-center border border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                        <div class="text-4xl mb-3">🏢</div>
                        <p class="text-xs font-bold text-slate-650 uppercase tracking-wider" x-text="selectedRack?.code"></p>
                        <p class="text-[10px] text-slate-400 mt-1">Koordinat: Baris <span x-text="selectedRack?.row"></span>, Kolom <span x-text="selectedRack?.col"></span></p>
                    </div>
                </div>

                <div class="mt-6 pt-5 border-t border-slate-100">
                    <p class="text-[11px] text-slate-400 leading-relaxed text-center">Gunakan menu "Edit Tata Letak" untuk memindahkan, mengubah nama, atau menghapus area ini.</p>
                </div>
            </div>

        </div>

    </div>

    <!-- EDIT LAYOUT POPUP MODAL (with animation) -->
    <div x-show="showEditModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         x-cloak>
         
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal()"></div>

        <!-- Modal Wrapper -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="showEditModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-4xl w-full overflow-hidden flex flex-col max-h-[90vh] z-10">
                 
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Pengaturan Tata Letak Toko</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Atur rak dan area toko Anda dalam Grid 6x6. Seret & letakkan untuk memindahkan.</p>
                    </div>
                    <button type="button" @click="closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors cursor-pointer focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body (2 Columns Split) -->
                <div class="p-6 grid grid-cols-1 md:grid-cols-5 gap-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                    
                    <!-- LEFT COLUMN: Grid Editor (3/5) -->
                    <div class="md:col-span-3 space-y-4">
                        <div class="grid grid-cols-6 gap-2 aspect-square max-w-[400px] mx-auto p-3 bg-slate-100 border border-slate-200 rounded-2xl shadow-inner">
                            <template x-for="cell in editGridCells" :key="cell.row + '-' + cell.col">
                                <div 
                                    x-on:click="selectEditCell(cell.row, cell.col, cell.item)"
                                    @dragover.prevent="dragOver($event)"
                                    @drop="drop($event, cell.row, cell.col)"
                                    :class="[
                                        cell.item ? 'cursor-grab active:cursor-grabbing hover:scale-[1.02] hover:shadow-sm' : 'bg-white border-dashed border-slate-200 hover:bg-slate-50 hover:border-slate-300',
                                        selectedEditCell?.row === cell.row && selectedEditCell?.col === cell.col ? 'ring-2 ring-green-600 ring-offset-1 z-10' : '',
                                        cell.item ? getItemColorClass(cell.item) : ''
                                    ]"
                                    class="rounded-xl border aspect-square flex flex-col justify-center items-center text-center p-1 relative select-none transition-all"
                                    :draggable="cell.item ? 'true' : 'false'"
                                    @dragstart="cell.item ? dragStart($event, cell.item) : null"
                                    @dragend="dragEnd($event)"
                                >
                                    <!-- Cell Content -->
                                    <template x-if="cell.item">
                                        <div class="flex flex-col justify-center items-center w-full h-full">
                                            <span class="text-[10px] font-black tracking-tight truncate max-w-full" x-text="cell.item.code"></span>
                                            <span class="text-[6.5px] font-black uppercase tracking-wider opacity-60 leading-none mt-0.5 truncate max-w-full" x-text="cell.item.is_box ? 'Area' : 'Rak'"></span>
                                        </div>
                                    </template>
                                    <!-- Coordinate if empty -->
                                    <template x-if="!cell.item">
                                        <span class="text-[7.5px] font-semibold text-slate-300" x-text="cell.row + ',' + cell.col"></span>
                                    </template>
                                </div>
                            </template>
                        </div>
                        <div class="text-[10px] text-center text-slate-400 font-bold uppercase tracking-wider">
                            💡 Tips: Seret item dari satu kotak ke kotak lain untuk menukar atau memindahkan posisi.
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: Editor Control Form (2/5) -->
                    <div class="md:col-span-2">
                        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 h-full flex flex-col justify-between min-h-[300px]">
                            <div x-show="selectedEditCell">
                                <!-- Header coordinate of selected cell -->
                                <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
                                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">
                                        Baris <span x-text="selectedEditCell?.row"></span>, Kolom <span x-text="selectedEditCell?.col"></span>
                                    </span>
                                    <span x-show="selectedEditCell?.item" 
                                          :class="selectedEditCell?.item?.is_box ? 'bg-indigo-100 text-indigo-700' : 'bg-green-100 text-green-700'" 
                                          class="text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider" 
                                          x-text="selectedEditCell?.item?.is_box ? 'Kotak Kustom' : 'Rak Barang'"></span>
                                </div>

                                <!-- FORM IF EMPTY -->
                                <div x-show="!selectedEditCell?.item" class="text-center py-8">
                                    <div class="text-3xl mb-2">➕</div>
                                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wide">Tambahkan Item Baru</h4>
                                    <p class="text-[11px] text-slate-500 mt-1 mb-4 leading-relaxed">Pilih jenis item yang ingin ditempatkan di koordinat ini.</p>
                                    
                                    <div class="grid grid-cols-2 gap-3">
                                        <button type="button" @click="addRack(selectedEditCell.row, selectedEditCell.col)"
                                                class="bg-white border border-slate-200 rounded-xl p-3 hover:bg-slate-100 hover:border-slate-350 transition-all text-center flex flex-col items-center justify-center cursor-pointer group">
                                            <span class="text-2xl mb-1 group-hover:scale-110 transition-transform">📦</span>
                                            <span class="text-[10px] font-bold text-slate-700">Tambah Rak</span>
                                        </button>
                                        <button type="button" @click="addBox(selectedEditCell.row, selectedEditCell.col)"
                                                class="bg-white border border-slate-200 rounded-xl p-3 hover:bg-slate-100 hover:border-slate-350 transition-all text-center flex flex-col items-center justify-center cursor-pointer group">
                                            <span class="text-2xl mb-1 group-hover:scale-110 transition-transform">🏢</span>
                                            <span class="text-[10px] font-bold text-slate-700">Tambah Kotak</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- FORM IF ITEM EXISTS -->
                                <div x-show="selectedEditCell?.item" class="space-y-4">
                                    <!-- Title/Code Input -->
                                    <div>
                                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1" x-text="selectedEditCell?.item?.is_box ? 'Nama Kotak / Area' : 'Kode Rak'"></label>
                                        <input type="text" x-model="selectedEditCell.item.code" 
                                               class="w-full text-xs rounded-xl border border-slate-200 p-2 focus:outline-none focus:ring-2 focus:ring-green-600/30 focus:border-green-650 bg-white font-medium" 
                                               placeholder="Contoh: Rak A1 atau Area Kasir">
                                    </div>

                                    <!-- Category dropdown (only for racks) -->
                                    <div x-show="selectedEditCell?.item && !selectedEditCell?.item?.is_box">
                                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Kategori Barang</label>
                                        <select x-model.number="selectedEditCell.item.category_id" 
                                                @change="syncCategoryName(selectedEditCell.item)"
                                                class="w-full text-xs rounded-xl border border-slate-200 p-2 focus:outline-none focus:ring-2 focus:ring-green-600/30 focus:border-green-650 bg-white font-medium">
                                            <template x-for="cat in categories" :key="cat.category_id">
                                                <option :value="cat.category_id" x-text="cat.category_name" :selected="cat.category_id === selectedEditCell.item.category_id"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <!-- Capacity input (only for racks) -->
                                    <div x-show="selectedEditCell?.item && !selectedEditCell?.item?.is_box">
                                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Kapasitas Maksimal</label>
                                        <input type="number" x-model.number="selectedEditCell.item.capacity" 
                                               class="w-full text-xs rounded-xl border border-slate-200 p-2 focus:outline-none focus:ring-2 focus:ring-green-600/30 focus:border-green-650 bg-white font-medium" 
                                               min="1">
                                    </div>

                                    <!-- Alert if has products -->
                                    <div x-show="selectedEditCell?.item && !selectedEditCell?.item?.is_box && selectedEditCell?.item?.count > 0" 
                                         class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-[10px] text-amber-800 leading-relaxed font-medium">
                                        ⚠️ Rak ini memiliki <strong><span x-text="selectedEditCell?.item?.count"></span> produk</strong> di dalamnya. Jika rak dihapus, produk akan dilepaskan dari rak.
                                    </div>

                                    <!-- Delete button -->
                                    <div class="pt-2">
                                        <button type="button" @click="removeCellItem()"
                                                class="w-full bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-xl py-2.5 text-xs font-bold transition-colors cursor-pointer flex items-center justify-center gap-1.5">
                                            <!-- Trash icon -->
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                                            </svg>
                                            Hapus dari Layout
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div x-show="!selectedEditCell" class="text-center py-16 text-slate-400">
                            <svg class="w-10 h-10 mx-auto text-slate-350 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.042 9.152c.582.448 1.148.89 1.676 1.345m-7.663-.346c.582-.448 1.148-.89 1.676-1.345m-7.663.346c.582.448 1.148.89 1.676 1.345M6.667 15h10.667M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z"></path>
                            </svg>
                            <p class="text-[11px] font-semibold leading-relaxed max-w-[200px] mx-auto">Pilih kotak pada grid untuk mengedit detail, menambah rak/kotak baru, atau seret item.</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-slate-150 flex items-center justify-end gap-3 bg-slate-50/50">
                <button type="button" @click="closeModal()" :disabled="isSaving"
                        class="bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 rounded-xl px-4 py-2.5 text-xs font-bold transition-all cursor-pointer focus:outline-none">
                    Batal
                </button>
                <button type="button" @click="saveLayout()" :disabled="isSaving"
                        class="bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white rounded-xl px-4 py-2.5 text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer focus:outline-none shadow-sm">
                    <span x-show="isSaving" class="inline-block w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    Simpan Perubahan
                </button>
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
            categories: @json($categories),
            selectedRack: null,
            filterCat: '',
            
            // Modal states
            showEditModal: false,
            editRacks: [],
            selectedEditCell: null,
            draggedItem: null,
            isSaving: false,
            
            init() {
                // Ensure all racks have row and col positions, place unpositioned ones in empty spots
                let grid = Array.from({length: 6}, () => Array(6).fill(null));
                this.racks.forEach(r => {
                    if (r.row && r.col) {
                        grid[r.row - 1][r.col - 1] = r;
                    }
                });
                
                this.racks.forEach(r => {
                    if (!r.row || !r.col) {
                        let placed = false;
                        for (let i = 0; i < 6 && !placed; i++) {
                            for (let j = 0; j < 6 && !placed; j++) {
                                if (!grid[i][j]) {
                                    r.row = i + 1;
                                    r.col = j + 1;
                                    grid[i][j] = r;
                                    placed = true;
                                }
                            }
                        }
                    }
                });
            },
            
            get gridCells() {
                let cells = [];
                for (let r = 1; r <= 6; r++) {
                    for (let c = 1; c <= 6; c++) {
                        let item = this.racks.find(rack => rack.row === r && rack.col === c);
                        cells.push({ row: r, col: c, item: item });
                    }
                }
                return cells;
            },
            
            get editGridCells() {
                let cells = [];
                for (let r = 1; r <= 6; r++) {
                    for (let c = 1; c <= 6; c++) {
                        let item = this.editRacks.find(rack => rack.row === r && rack.col === c);
                        cells.push({ row: r, col: c, item: item });
                    }
                }
                return cells;
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
            },
            
            isFilteredOut(item) {
                if (!this.filterCat) return false;
                if (item.is_box) return true;
                if (this.filterCat === 'kosong') {
                    return item.count > 0;
                }
                return item.category !== this.filterCat;
            },
            
            getItemColorClass(item) {
                if (item.is_box) {
                    return 'bg-indigo-50/50 border-indigo-200 border-dashed text-indigo-900 hover:border-indigo-400 hover:bg-indigo-50';
                }
                
                const colors = {
                    'Aksesori Rumah Tangga': 'bg-blue-50/50 border-blue-200 text-blue-900 hover:border-blue-400',
                    'Material Bangunan':     'bg-amber-50/50 border-amber-200 text-amber-900 hover:border-amber-400',
                    'Suku Cadang Motor':     'bg-green-50/50 border-green-200 text-green-900 hover:border-green-400',
                };
                return colors[item.category] || 'bg-slate-50/50 border-slate-200 text-slate-700 hover:border-slate-300';
            },
            
            getProgressBarClass(item) {
                if (item.is_box) return '';
                const colors = {
                    'Aksesori Rumah Tangga': 'bg-blue-600',
                    'Material Bangunan':     'bg-amber-500',
                    'Suku Cadang Motor':     'bg-green-600',
                };
                return colors[item.category] || 'bg-slate-400';
            },
            
            // Modal functions
            openModal() {
                this.editRacks = JSON.parse(JSON.stringify(this.racks));
                this.selectedEditCell = null;
                this.showEditModal = true;
            },
            
            closeModal() {
                if (this.isSaving) return;
                this.showEditModal = false;
            },
            
            // Drag and drop handlers
            dragStart(e, item) {
                this.draggedItem = item;
                e.dataTransfer.effectAllowed = 'move';
            },
            
            dragEnd(e) {
                this.draggedItem = null;
            },
            
            dragOver(e) {
                e.preventDefault();
            },
            
            drop(e, row, col) {
                if (!this.draggedItem) return;
                
                let targetItem = this.editRacks.find(r => r.row === row && r.col === col);
                
                if (targetItem) {
                    // Swap coordinates
                    const tempRow = this.draggedItem.row;
                    const tempCol = this.draggedItem.col;
                    
                    this.draggedItem.row = row;
                    this.draggedItem.col = col;
                    
                    targetItem.row = tempRow;
                    targetItem.col = tempCol;
                } else {
                    // Move
                    this.draggedItem.row = row;
                    this.draggedItem.col = col;
                }
                
                // Select dropped cell
                this.selectedEditCell = { row: row, col: col, item: this.draggedItem };
                this.draggedItem = null;
            },
            
            selectEditCell(row, col, item) {
                this.selectedEditCell = { row, col, item };
            },
            
            addRack(row, col) {
                if (this.editRacks.find(r => r.row === row && r.col === col)) return;
                
                let newRack = {
                    id: null,
                    code: 'R' + row + '-' + col,
                    category: this.categories[0]?.category_name ?? 'Kosong',
                    category_id: this.categories[0]?.category_id ?? null,
                    capacity: 50,
                    count: 0,
                    row: row,
                    col: col,
                    is_box: false,
                    products: []
                };
                
                this.editRacks.push(newRack);
                this.selectedEditCell = { row, col, item: newRack };
            },
            
            addBox(row, col) {
                if (this.editRacks.find(r => r.row === row && r.col === col)) return;
                
                let newBox = {
                    id: null,
                    code: 'KOTAK',
                    category: 'Kotak Kustom',
                    category_id: null,
                    capacity: 0,
                    count: 0,
                    row: row,
                    col: col,
                    is_box: true,
                    products: []
                };
                
                this.editRacks.push(newBox);
                this.selectedEditCell = { row, col, item: newBox };
            },
            
            removeCellItem() {
                if (!this.selectedEditCell || !this.selectedEditCell.item) return;
                
                if (!this.selectedEditCell.item.is_box && this.selectedEditCell.item.count > 0) {
                    if (!confirm('Peringatan: Rak ini masih berisi ' + this.selectedEditCell.item.count + ' produk. Menghapus rak ini akan melepaskan produk tersebut dari rak (id rak diset null). Apakah Anda yakin?')) {
                        return;
                    }
                }
                
                this.editRacks = this.editRacks.filter(r => r !== this.selectedEditCell.item);
                this.selectedEditCell.item = null;
            },
            
            syncCategoryName(item) {
                let cat = this.categories.find(c => c.category_id === parseInt(item.category_id));
                if (cat) {
                    item.category = cat.category_name;
                }
            },
            
            saveLayout() {
                // Validation for empty titles
                for (let r of this.editRacks) {
                    if (!r.code || r.code.trim() === '') {
                        alert('Semua rak atau kotak kustom harus memiliki nama/kode!');
                        return;
                    }
                }
                
                // Validation for duplicate codes
                let codes = this.editRacks.map(r => r.code.trim().toUpperCase());
                let duplicates = codes.filter((item, index) => codes.indexOf(item) !== index);
                if (duplicates.length > 0) {
                    alert('Nama / Kode "' + duplicates[0] + '" digunakan lebih dari sekali. Setiap item harus memiliki nama/kode yang unik!');
                    return;
                }
                
                this.isSaving = true;
                
                let payload = this.editRacks.map(r => ({
                    id: r.id,
                    code: r.code,
                    category_id: r.is_box ? null : r.category_id,
                    capacity: r.is_box ? 0 : r.capacity,
                    row: r.row,
                    col: r.col,
                    is_box: r.is_box
                }));
                
                fetch('{{ route("rak.save-layout") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ layout: payload })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Gagal menyimpan tata letak.');
                        this.isSaving = false;
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan koneksi.');
                    this.isSaving = false;
                });
            }
        }));
    });
</script>
@endpush
@endsection
