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
        Schema::create('stock_alerts', function (Blueprint $table) {
            $table->increments('alert_id');
            $table->unsignedInteger('product_id');
            $table->timestamp('alert_date')->useCurrent();
            $table->integer('current_stock');
            $table->integer('min_stock');
            $table->boolean('is_dismissed')->default(false);
            $table->timestamp('dismissed_at')->nullable();

            $table->foreign('product_id')
                  ->references('product_id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_alerts');
    }
};
