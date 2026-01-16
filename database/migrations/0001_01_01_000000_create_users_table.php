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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id_user');
            $table->string('name')->nullable();
            $table->string('nom_complet', 100)->nullable();
            $table->string('telephone', 20)->unique()->nullable();
            $table->string('email', 100)->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255)->nullable();
            $table->enum('role', ['client', 'vendeur', 'admin'])->default('client');
            $table->enum('statut_compte', ['actif', 'suspendu', 'en_attente', 'supprime'])->default('actif');
            $table->string('photo_profil', 255)->nullable();
            $table->string('langue_preferee', 5)->default('fr');
            $table->timestamp('date_creation')->useCurrent();
            $table->timestamp('date_derniere_connexion')->nullable();
            $table->string('derniere_ip', 45)->nullable();

            $table->index('role');
            $table->index('statut_compte');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedBigInteger('id_user')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }
};
