<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vendeurs', function (Blueprint $table) {
            $table->unsignedBigInteger('id_category_vendeur')->nullable()->after('type_vendeur');
            $table->foreign('id_category_vendeur')->references('id_category_vendeur')->on('vendor_categories')->onDelete('set null');
            // Keep type_vendeur for now but make it nullable if it wasn't
        });
    }

    public function down(): void
    {
        Schema::table('vendeurs', function (Blueprint $table) {
            $table->dropForeign(['id_category_vendeur']);
            $table->dropColumn('id_category_vendeur');
        });
    }
};
