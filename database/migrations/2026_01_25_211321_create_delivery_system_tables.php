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
        // Table for vendors seeking delivery drivers
        Schema::create('delivery_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_vendeur');
            $table->text('message')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();

            $table->foreign('id_vendeur')->references('id_vendeur')->on('vendeurs')->onDelete('cascade');
        });

        // Table for users applying to be delivery drivers
        Schema::create('delivery_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_delivery_request');
            $table->unsignedBigInteger('id_user');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('id_delivery_request')->references('id')->on('delivery_requests')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });

        // Add id_livreur to commandes to assign a driver to an order
        Schema::table('commandes', function (Blueprint $table) {
            $table->unsignedBigInteger('id_livreur')->nullable()->after('id_vendeur');
            $table->foreign('id_livreur')->references('id_user')->on('users')->onDelete('set null');
        });

        // Add fcm_token for push notifications
        Schema::table('users', function (Blueprint $table) {
            $table->string('fcm_token')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('fcm_token');
        });

        Schema::table('commandes', function (Blueprint $table) {
            $table->dropForeign(['id_livreur']);
            $table->dropColumn('id_livreur');
        });

        Schema::dropIfExists('delivery_applications');
        Schema::dropIfExists('delivery_requests');
    }
};
