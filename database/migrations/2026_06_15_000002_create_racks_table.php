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
        Schema::create('racks', function (Blueprint $table) {
            $table->increments('rack_id');
            $table->string('rack_code', 10)->unique();
            $table->unsignedInteger('category_id');
            $table->integer('capacity');
            $table->text('description')->nullable();
            
            $table->foreign('category_id')
                  ->references('category_id')
                  ->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('racks');
    }
};
