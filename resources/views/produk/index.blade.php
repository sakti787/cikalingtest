@extends('layouts.app')

@section('title', 'Kelola Produk')
@section('page-title', 'Kelola Produk')

@section('content')
<div class="space-y-6" x-data="{ 
    showDeleteModal: false, 
    deleteRoute: '', 
    productName: '',
    confirmDelete(route, name) {
        this.deleteRoute = route;
        this.productName = name;
        this.showDeleteModal = true;
    }
}">

    <!-- Section Header Row -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Kelola Produk</h1>
            <p class="text-sm text-slate-500 mt-1">
                {{ $products->total() }} produk terdaftar
            </p>
        </div>
        <div>
            <a href="{{ route('produk.create') }}" class="btn-primary">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                </svg>
                <span>Tambah Produk</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar Form -->
    <form method="GET" action="{{ route('produk.index') }}" class="card grid grid-cols-1 md:grid-cols-12 gap-4 items-end bg-white">
        
        <!-- Search Input -->
        <div class="md:col-span-4">
            <label for="search" class="form-label">Cari Produk</label>
            <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Nama produk..." class="input-field">
        </div>
        
        <!-- Category Selector -->
        <div class="md:col-span-3">
            <label for="category_id" class="form-label">Kategori</label>
            <select id="category_id" name="category_id" class="input-field">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->category_id }}" {{ request('category_id') == $cat->category_id ? 'selected' : '' }}>
                        {{ $cat->category_name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <!-- Status Selector -->
        <div class="md:col-span-3">
            <label for="is_active" class="form-label">Status</label>
            <select id="is_active" name="is_active" class="input-field">
                <option value="">Semua Status</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>
        
        <!-- Actions Buttons -->
        <div class="md:col-span-2 flex gap-2">
            <button type="submit" class="btn-primary flex-1 justify-center min-h-[44px] cursor-pointer">
                Filter
            </button>
            <a href="{{ route('produk.index') }}" class="btn-secondary flex-1 justify-center min-h-[44px] items-center">
                Reset
            </a>
        </div>

    </form>

    <!-- Products Table -->
    <div class="card p-0 overflow-hidden bg-white border border-slate-200 shadow-sm">
        @if($products->count() > 0)
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Kode Rak</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <!-- Product Name -->
                                <td class="font-medium text-slate-900">
                                    {{ $product->product_name }}
                                </td>

                                <!-- Category Badge -->
                                <td>
                                    @if(str_contains(strtolower($product->category->category_name), 'aksesori'))
                                        <span class="bg-blue-100 text-blue-700 rounded-full px-3 py-1 text-sm font-medium inline-block">
                                            {{ $product->category->category_name }}
                                        </span>
                                    @elseif(str_contains(strtolower($product->category->category_name), 'material'))
                                        <span class="badge-yellow">
                                            {{ $product->category->category_name }}
                                        </span>
                                    @elseif(str_contains(strtolower($product->category->category_name), 'suku cadang'))
                                        <span class="badge-green">
                                            {{ $product->category->category_name }}
                                        </span>
                                    @else
                                        <span class="badge-gray">
                                            {{ $product->category->category_name }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Rack Code -->
                                <td>
                                    @if($product->rack)
                                        <span class="font-mono text-sm bg-slate-100 px-2.5 py-1 rounded text-slate-600 border border-slate-200">
                                            {{ $product->rack->rack_code }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-medium">-</span>
                                    @endif
                                </td>

                                <!-- Buy Price -->
                                <td class="text-slate-500">
                                    Rp {{ number_format($product->buy_price, 0, ',', '.') }}
                                </td>

                                <!-- Sell Price -->
                                <td class="font-semibold text-slate-900">
                                    Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                                </td>

                                <!-- Stock levels -->
                                <td>
                                    @if($product->stock <= $product->min_stock)
                                        <span class="text-red-600 font-bold">
                                            {{ $product->stock }}
                                        </span>
                                        <span class="text-xs text-red-500 font-normal block mt-0.5">
                                            (min: {{ $product->min_stock }})
                                        </span>
                                    @else
                                        <span class="text-slate-900">
                                            {{ $product->stock }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Status Badge -->
                                <td>
                                    @if($product->is_active && $product->stock > $product->min_stock)
                                        <span class="badge-green">Aktif</span>
                                    @elseif($product->is_active && $product->stock <= $product->min_stock)
                                        <span class="badge-yellow">Stok Rendah</span>
                                    @else
                                        <span class="badge-gray">Nonaktif</span>
                                    @endif
                                </td>

                                 <!-- Action Buttons -->
                                 <td class="text-right">
                                     <div class="inline-flex items-center gap-2">
                                         <!-- Edit button -->
                                         <a href="{{ route('produk.edit', $product->product_id) }}" class="btn-secondary px-3 py-1 text-sm min-h-[32px] items-center">
                                             Edit
                                         </a>

                                         <!-- Status Toggle Button -->
                                         @if($product->is_active)
                                             <form action="{{ route('produk.deactivate', $product->product_id) }}" method="POST" class="inline" x-data>
                                                 @csrf
                                                 <button type="submit" x-on:click.prevent="if(confirm('Apakah Anda yakin ingin menonaktifkan produk ini?')) $el.closest('form').submit()" class="btn-secondary px-3 py-1 text-sm min-h-[32px] cursor-pointer items-center">
                                                     Nonaktifkan
                                                 </button>
                                             </form>
                                         @else
                                             <form action="{{ route('produk.activate', $product->product_id) }}" method="POST" class="inline" x-data>
                                                 @csrf
                                                 <button type="submit" x-on:click.prevent="if(confirm('Apakah Anda yakin ingin mengaktifkan kembali produk ini?')) $el.closest('form').submit()" class="bg-green-600 hover:bg-green-700 text-white rounded-lg px-3 py-1 text-sm min-h-[32px] cursor-pointer inline-flex items-center transition-colors font-medium">
                                                     Aktifkan
                                                 </button>
                                             </form>
                                         @endif

                                         <!-- Delete Button -->
                                         <button type="button" @click="confirmDelete('{{ route('produk.destroy', $product->product_id) }}', '{{ addslashes($product->product_name) }}')" class="btn-danger px-3 py-1 text-sm min-h-[32px] cursor-pointer items-center">
                                             Hapus
                                         </button>
                                     </div>
                                 </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Table Footer & Pagination -->
            <div class="px-6 py-4 flex flex-col md:flex-row items-center justify-between border-t border-slate-100 bg-slate-50/50 gap-4">
                <div class="text-sm text-slate-500 font-medium">
                    Menampilkan {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk
                </div>
                <div>
                    {{ $products->links() }}
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="py-16 text-center">
                <!-- Large package icon -->
                <svg class="w-20 h-20 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"></path>
                </svg>
                <h3 class="text-lg font-bold text-slate-800">Tidak ada produk ditemukan</h3>
                
                @if(request('search') || request('category_id') || request('is_active') !== null)
                    <p class="text-slate-500 mt-1 text-sm">Coba kata kunci atau filter pencarian lain</p>
                    <a href="{{ route('produk.index') }}" class="text-green-600 font-semibold hover:underline mt-4 inline-block text-sm">
                        Bersihkan Filter
                    </a>
                @else
                    <p class="text-slate-500 mt-1 text-sm">Mulai dengan menambahkan produk baru Anda</p>
                    <a href="{{ route('produk.create') }}" class="text-green-600 font-semibold hover:underline mt-4 inline-block text-sm">
                        Tambah Produk Pertama
                    </a>
                @endif
            </div>
        @endif
    </div>

    <!-- Beautiful Delete Confirmation Modal -->
    <div x-show="showDeleteModal" 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="display: none;">
        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-6" @click.away="showDeleteModal = false">
            
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                    <!-- warning SVG -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"></path>
                    </svg>
                </div>
                <div class="space-y-1.5">
                    <h3 class="text-lg font-bold text-slate-900">Hapus Produk Permanen?</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Apakah Anda yakin ingin menghapus produk <span class="font-bold text-slate-950" x-text="productName"></span> secara permanen dari sistem? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" @click="showDeleteModal = false" class="btn-secondary min-h-[38px] px-4 py-2 text-sm cursor-pointer">
                    Batal
                </button>
                <form :action="deleteRoute" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger bg-red-600 hover:bg-red-700 text-white min-h-[38px] px-4 py-2 text-sm cursor-pointer rounded-lg transition-colors font-medium">
                        Ya, Hapus Produk
                    </button>
                </form>
            </div>
            
        </div>
    </div>

</div>
@endsection
