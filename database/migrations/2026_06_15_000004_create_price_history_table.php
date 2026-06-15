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
        Schema::create('price_history', function (Blueprint $table) {
            $table->increments('price_id');
            $table->unsignedInteger('product_id');
            $table->decimal('old_price', 15, 2);
            $table->decimal('new_price', 15, 2);
            $table->unsignedInteger('changed_by');
            $table->timestamp('changed_at')->useCurrent();

            $table->foreign('product_id')
                  ->references('product_id')->on('products');
            $table->foreign('changed_by')
                  ->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_history');
    }
};
