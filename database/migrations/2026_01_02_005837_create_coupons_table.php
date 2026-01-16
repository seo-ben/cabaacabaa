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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id('id_coupon');
            $table->string('code')->unique();
            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('valeur', 10, 2);
            $table->decimal('montant_minimal_achat', 10, 2)->default(0);
            $table->integer('limite_utilisation')->nullable();
            $table->integer('nombre_utilisations')->default(0);
            $table->dateTime('expire_at')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        Schema::create('coupon_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_coupon');
            $table->unsignedBigInteger('id_user');
            $table->timestamp('used_at')->useCurrent();

            $table->foreign('id_coupon')->references('id_coupon')->on('coupons')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_user');
        Schema::dropIfExists('coupons');
    }
};
