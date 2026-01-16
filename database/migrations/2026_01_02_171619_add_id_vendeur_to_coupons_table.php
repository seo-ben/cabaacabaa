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
        if (!Schema::hasColumn('coupons', 'id_vendeur')) {
            Schema::table('coupons', function (Blueprint $table) {
                $table->unsignedBigInteger('id_vendeur')->nullable()->after('id_coupon');
                $table->foreign('id_vendeur')->references('id_vendeur')->on('vendeurs')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropForeign(['id_vendeur']);
            $table->dropColumn('id_vendeur');
        });
    }
};
