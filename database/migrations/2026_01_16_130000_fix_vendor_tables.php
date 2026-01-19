<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Change icon to TEXT to support long SVG strings
        // Using raw SQL to avoid dependency requirement
        DB::statement("ALTER TABLE vendor_categories MODIFY COLUMN icon TEXT NULL");

        // 2. Make type_vendeur nullable in vendeurs table
        DB::statement("ALTER TABLE vendeurs MODIFY COLUMN type_vendeur ENUM('restaurant','cantine','fast_food','vendeur_independant','patisserie','autre') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert icon to string (might truncate data if not careful, but this is down method)
        DB::statement("ALTER TABLE vendor_categories MODIFY COLUMN icon VARCHAR(255) NULL");

        // Revert type_vendeur to NOT NULL (this might fail if there are nulls)
        // We generally can't easily revert this safely without data loss or default values if nulls exist.
        // But for completeness:
        // DB::statement("ALTER TABLE vendeurs MODIFY COLUMN type_vendeur ENUM('restaurant','cantine','fast_food','vendeur_independant','patisserie','autre') NOT NULL");
    }
};
