<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Rack;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Categories
        $aksesori = Category::create([
            'category_name' => 'Aksesori Rumah Tangga',
            'description' => 'Produk untuk kebutuhan rumah tangga',
        ]);
        
        $material = Category::create([
            'category_name' => 'Material Bangunan',
            'description' => 'Bahan bangunan dan konstruksi',
        ]);
        
        $sukuCadang = Category::create([
            'category_name' => 'Suku Cadang Motor',
            'description' => 'Onderdil dan aksesoris kendaraan bermotor',
        ]);

        // 2. Racks
        $rackA1 = Rack::create([
            'rack_code' => 'A1',
            'category_id' => $aksesori->category_id,
            'capacity' => 50,
            'description' => 'Rak A1 untuk Aksesori Rumah Tangga',
            'row_position' => 1,
            'col_position' => 1,
        ]);

        $rackA2 = Rack::create([
            'rack_code' => 'A2',
            'category_id' => $aksesori->category_id,
            'capacity' => 50,
            'description' => 'Rak A2 untuk Aksesori Rumah Tangga',
            'row_position' => 1,
            'col_position' => 3,
        ]);

        $rackB1 = Rack::create([
            'rack_code' => 'B1',
            'category_id' => $material->category_id,
            'capacity' => 80,
            'description' => 'Rak B1 untuk Material Bangunan',
            'row_position' => 2,
            'col_position' => 1,
        ]);

        $rackB2 = Rack::create([
            'rack_code' => 'B2',
            'category_id' => $material->category_id,
            'capacity' => 80,
            'description' => 'Rak B2 untuk Material Bangunan',
            'row_position' => 2,
            'col_position' => 3,
        ]);

        $rackC1 = Rack::create([
            'rack_code' => 'C1',
            'category_id' => $sukuCadang->category_id,
            'capacity' => 60,
            'description' => 'Rak C1 untuk Suku Cadang Motor',
            'row_position' => 3,
            'col_position' => 1,
        ]);

        // 3. Users
        User::create([
            'username' => 'ci_aling',
            'role' => 'pemilik',
            'password_hash' => bcrypt('pemilik123'),
        ]);

        User::create([
            'username' => 'kasir1',
            'role' => 'kasir',
            'password_hash' => bcrypt('kasir123'),
        ]);

        User::create([
            'username' => 'gudang1',
            'role' => 'gudang',
            'password_hash' => bcrypt('gudang123'),
        ]);

        // 4. Products
        Product::create([
            'product_name' => 'Semen Portland 50kg',
            'category_id' => $material->category_id,
            'rack_id' => $rackB1->rack_id,
            'sell_price' => 68000,
            'buy_price' => 55000,
            'stock' => 45,
            'min_stock' => 10,
            'is_active' => true,
        ]);

        Product::create([
            'product_name' => 'Oli Mesin SAE40 1L',
            'category_id' => $sukuCadang->category_id,
            'rack_id' => $rackC1->rack_id,
            'sell_price' => 32000,
            'buy_price' => 25000,
            'stock' => 8,
            'min_stock' => 15,
            'is_active' => true,
        ]);

        Product::create([
            'product_name' => 'Baut M8 x 50pcs',
            'category_id' => $material->category_id,
            'rack_id' => $rackB2->rack_id,
            'sell_price' => 15000,
            'buy_price' => 10000,
            'stock' => 120,
            'min_stock' => 20,
            'is_active' => true,
        ]);

        Product::create([
            'product_name' => 'Engsel Pintu 2 Inch',
            'category_id' => $aksesori->category_id,
            'rack_id' => $rackA1->rack_id,
            'sell_price' => 8500,
            'buy_price' => 6000,
            'stock' => 65,
            'min_stock' => 10,
            'is_active' => true,
        ]);

        Product::create([
            'product_name' => 'Kunci Ring 12mm',
            'category_id' => $sukuCadang->category_id,
            'rack_id' => $rackC1->rack_id,
            'sell_price' => 22000,
            'buy_price' => 16000,
            'stock' => 30,
            'min_stock' => 10,
            'is_active' => true,
        ]);

        Product::create([
            'product_name' => 'Kabel NYM 2.5mm/m',
            'category_id' => $aksesori->category_id,
            'rack_id' => $rackA2->rack_id,
            'sell_price' => 12000,
            'buy_price' => 8500,
            'stock' => 200,
            'min_stock' => 50,
            'is_active' => true,
        ]);
    }
}
