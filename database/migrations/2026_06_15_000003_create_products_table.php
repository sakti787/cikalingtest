<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('product_id');
            $table->string('product_name', 200);
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('rack_id')->nullable();
            $table->decimal('sell_price', 15, 2);
            $table->decimal('buy_price', 15, 2);
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('category_id')
                  ->references('category_id')->on('categories');
            $table->foreign('rack_id')
                  ->references('rack_id')->on('racks')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
