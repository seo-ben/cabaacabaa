<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToUsersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nom_complet')) {
                $table->string('nom_complet', 100)->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'telephone')) {
                $table->string('telephone', 20)->unique()->nullable()->after('nom_complet');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('client')->after('password');
            }
            if (!Schema::hasColumn('users', 'statut_compte')) {
                $table->string('statut_compte')->default('actif')->after('role');
            }
            if (!Schema::hasColumn('users', 'photo_profil')) {
                $table->string('photo_profil', 255)->nullable()->after('statut_compte');
            }
            if (!Schema::hasColumn('users', 'langue_preferee')) {
                $table->string('langue_preferee', 5)->default('fr')->after('photo_profil');
            }
            if (!Schema::hasColumn('users', 'date_derniere_connexion')) {
                $table->timestamp('date_derniere_connexion')->nullable()->after('langue_preferee');
            }
            if (!Schema::hasColumn('users', 'derniere_ip')) {
                $table->string('derniere_ip', 45)->nullable()->after('date_derniere_connexion');
            }

            // Indexes
            if (!Schema::hasColumn('users', 'telephone')) {
                $table->index('telephone');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->index('role');
            }
            if (!Schema::hasColumn('users', 'statut_compte')) {
                $table->index('statut_compte');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('users')) {
            return;
        }
        // Drop dependent views first to avoid SQLite "no such column" errors when removing columns
        try {
            \Illuminate\Support\Facades\DB::statement('DROP VIEW IF EXISTS vue_commandes_jour');
            \Illuminate\Support\Facades\DB::statement('DROP VIEW IF EXISTS vue_vendeurs_actifs');
        } catch (\Exception $e) {
            // ignore
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'derniere_ip')) {
                $table->dropColumn('derniere_ip');
            }
            if (Schema::hasColumn('users', 'date_derniere_connexion')) {
                $table->dropColumn('date_derniere_connexion');
            }
            if (Schema::hasColumn('users', 'langue_preferee')) {
                $table->dropColumn('langue_preferee');
            }
            if (Schema::hasColumn('users', 'photo_profil')) {
                $table->dropColumn('photo_profil');
            }
            if (Schema::hasColumn('users', 'statut_compte')) {
                $table->dropColumn('statut_compte');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'telephone')) {
                $table->dropUnique(['telephone']);
                $table->dropColumn('telephone');
            }
            if (Schema::hasColumn('users', 'nom_complet')) {
                $table->dropColumn('nom_complet');
            }
        });
    }
}
