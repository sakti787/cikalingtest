<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Delete all users with role 'gudang'
        DB::table('users')->where('role', 'gudang')->delete();

        // 2. Change the column enum on MySQL
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('pemilik', 'kasir') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('pemilik', 'kasir', 'gudang') NOT NULL");
    }
};
