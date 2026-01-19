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
        // Add vendor staff table
        Schema::create('vendor_staff', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_vendeur');
            $table->unsignedBigInteger('id_user');
            $table->string('role_name')->default('staff'); // e.g. Manager, Staff
            $table->json('permissions')->nullable(); // Array of permissions for this shop
            $table->string('access_token', 64)->unique()->nullable(); // Unique login token
            $table->timestamps();

            $table->foreign('id_vendeur')->references('id_vendeur')->on('vendeurs')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');

            $table->unique(['id_vendeur', 'id_user']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_staff');
    }
};
