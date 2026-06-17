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
        Schema::table('racks', function (Blueprint $table) {
            $table->unsignedInteger('category_id')->nullable()->change();
            $table->integer('row_position')->nullable();
            $table->integer('col_position')->nullable();
            $table->boolean('is_custom_box')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('racks', function (Blueprint $table) {
            $table->unsignedInteger('category_id')->nullable(false)->change();
            $table->dropColumn(['row_position', 'col_position', 'is_custom_box']);
        });
    }
};
