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
        Schema::table('users', function (Blueprint $table) {
            // Statut et suspension
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['actif', 'inactif', 'suspendu', 'en_attente_verification'])->default('en_attente_verification')->after('statut_compte');
            }

            if (!Schema::hasColumn('users', 'suspended_at')) {
                $table->timestamp('suspended_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('users', 'suspension_reason')) {
                $table->text('suspension_reason')->nullable()->after('suspended_at');
            }

            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes()->after('suspension_reason');
            }

            // Sécurité et authentification
            if (!Schema::hasColumn('users', 'login_attempts')) {
                $table->integer('login_attempts')->default(0)->after('deleted_at');
            }

            if (!Schema::hasColumn('users', 'locked_until')) {
                $table->timestamp('locked_until')->nullable()->after('login_attempts');
            }

            if (!Schema::hasColumn('users', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('locked_until');
            }

            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('is_verified');
            }

            // Détection d'abus
            if (!Schema::hasColumn('users', 'risk_score')) {
                $table->integer('risk_score')->default(0)->after('email_verified_at');
            }

            if (!Schema::hasColumn('users', 'suspicious_flags')) {
                $table->json('suspicious_flags')->nullable()->after('risk_score');
            }

            if (!Schema::hasColumn('users', 'last_suspicious_activity')) {
                $table->timestamp('last_suspicious_activity')->nullable()->after('suspicious_flags');
            }

            // Historique
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable()->after('derniere_ip');
            }

            if (!Schema::hasColumn('users', 'failed_logins')) {
                $table->json('failed_logins')->nullable()->after('last_login_ip');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'status', 'suspended_at', 'suspension_reason', 'deleted_at',
                'login_attempts', 'locked_until', 'is_verified', 'email_verified_at',
                'risk_score', 'suspicious_flags', 'last_suspicious_activity',
                'last_login_ip', 'failed_logins'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
