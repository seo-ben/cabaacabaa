<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vendeurs', function (Blueprint $table) {
            $table->string('slug', 255)->nullable()->unique()->after('nom_commercial');
        });

        // Generate slugs for existing vendors
        DB::statement("UPDATE vendeurs SET slug = LOWER(REPLACE(REPLACE(REPLACE(REPLACE(nom_commercial, ' ', '-'), 'é', 'e'), 'è', 'e'), 'à', 'a')) WHERE slug IS NULL");
    }

    public function down(): void
    {
        Schema::table('vendeurs', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
