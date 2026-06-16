<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    
    <!-- Col 1 -->
    <div class="space-y-5">
        <!-- Nama Produk -->
        <div>
            <label for="product_name" class="form-label">Nama Produk</label>
            <input type="text" id="product_name" name="product_name" value="{{ old('product_name', $product->product_name ?? '') }}" required class="input-field" placeholder="Masukkan nama produk">
            @error('product_name')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <!-- Kategori -->
        <div>
            <label for="category_id" class="form-label">Kategori</label>
            <select id="category_id" name="category_id" required class="input-field">
                <option value="" disabled {{ !isset($product) ? 'selected' : '' }}>-- Pilih Kategori --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->category_id }}" {{ old('category_id', $product->category_id ?? '') == $category->category_id ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <!-- Kode Rak -->
        <div>
            <label for="rack_id" class="form-label">Kode Rak</label>
            <select id="rack_id" name="rack_id" class="input-field">
                <option value="">-- Tanpa Rak --</option>
                @foreach($racks as $rack)
                    <option value="{{ $rack->rack_id }}" {{ old('rack_id', $product->rack_id ?? '') == $rack->rack_id ? 'selected' : '' }}>
                        {{ $rack->rack_code }} — {{ $rack->category->category_name }}
                    </option>
                @endforeach
            </select>
            @error('rack_id')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Col 2 -->
    <div class="space-y-5">
        <!-- Harga Beli -->
        <div>
            <label for="buy_price" class="form-label">Harga Beli</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-medium">
                    Rp
                </div>
                <input type="number" step="500" min="0" id="buy_price" name="buy_price" value="{{ old('buy_price', isset($product) ? intval($product->buy_price) : '') }}" required class="input-field pl-12" placeholder="0">
            </div>
            @error('buy_price')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <!-- Harga Jual -->
        <div>
            <label for="sell_price" class="form-label">Harga Jual</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-medium">
                    Rp
                </div>
                <input type="number" step="500" min="0" id="sell_price" name="sell_price" value="{{ old('sell_price', isset($product) ? intval($product->sell_price) : '') }}" required class="input-field pl-12" placeholder="0">
            </div>
            @if(isset($product))
                @php
                    $lastChange = $product->priceHistories()->orderBy('changed_at', 'desc')->first();
                @endphp
                @if($lastChange)
                    <p class="text-xs text-slate-400 mt-1.5">
                        Terakhir diubah: {{ $lastChange->changed_at->format('d M Y H:i') }}
                    </p>
                @endif
            @endif
            @error('sell_price')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <!-- Stok & Stok Minimum Grid -->
        <div class="grid grid-cols-2 gap-4">
            <!-- Stok -->
            <div>
                <label for="stock" class="form-label">Stok</label>
                <input type="number" min="0" id="stock" name="stock" value="{{ old('stock', $product->stock ?? '') }}" required class="input-field" placeholder="0">
                @error('stock')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Stok Minimum -->
            <div>
                <label for="min_stock" class="form-label">Stok Minimum</label>
                <input type="number" min="0" id="min_stock" name="min_stock" value="{{ old('min_stock', $product->min_stock ?? '') }}" required class="input-field" placeholder="0">
                @error('min_stock')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Actions Row -->
    <div class="md:col-span-2 flex justify-end gap-3 pt-6 border-t border-slate-100">
        <a href="{{ isset($product) ? 'javascript:history.back()' : route('produk.index') }}" class="btn-secondary">
            Batal
        </a>
        <button type="submit" 
                x-data="{ loading: false }"
                x-on:click="loading = true"
                x-bind:disabled="loading"
                x-bind:class="loading ? 'opacity-75' : ''"
                class="btn-primary cursor-pointer">
            <span x-text="loading ? 'Menyimpan...' : 'Simpan Produk'">Simpan Produk</span>
        </button>
    </div>

</div>
