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
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code')->nullable()->unique();
            $table->unsignedBigInteger('referred_by')->nullable();
            $table->decimal('referral_balance', 10, 2)->default(0);
            $table->foreign('referred_by')->references('id_user')->on('users')->onDelete('set null');
        });

        Schema::table('vendeurs', function (Blueprint $table) {
            $table->decimal('wallet_balance', 10, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn(['referral_code', 'referred_by', 'referral_balance']);
        });

        Schema::table('vendeurs', function (Blueprint $table) {
            $table->dropColumn(['wallet_balance']);
        });
    }
};
