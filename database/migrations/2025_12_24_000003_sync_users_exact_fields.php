<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SyncUsersExactFields extends Migration
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
                $table->string('telephone', 20)->nullable()->unique()->after('nom_complet');
            }

            if (!Schema::hasColumn('users', 'password')) {
                $table->string('password', 255)->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('client');
            }

            if (!Schema::hasColumn('users', 'statut_compte')) {
                $table->string('statut_compte')->default('actif');
            }

            if (!Schema::hasColumn('users', 'photo_profil')) {
                $table->string('photo_profil', 255)->nullable();
            }

            if (!Schema::hasColumn('users', 'langue_preferee')) {
                $table->string('langue_preferee', 5)->default('fr');
            }

            if (!Schema::hasColumn('users', 'date_creation')) {
                $table->timestamp('date_creation')->useCurrent();
            }

            if (!Schema::hasColumn('users', 'date_derniere_connexion')) {
                $table->timestamp('date_derniere_connexion')->nullable();
            }

            if (!Schema::hasColumn('users', 'derniere_ip')) {
                $table->string('derniere_ip', 45)->nullable();
            }
        });

        // Migration des anciens mots de passe si existants
        if (
            Schema::hasColumn('users', 'mot_de_passe_hash') &&
            Schema::hasColumn('users', 'password')
        ) {
            $users = DB::table('users')
                ->select('id_user', 'password', 'mot_de_passe_hash')
                ->get();

            foreach ($users as $user) {
                if (
                    (empty($user->password) || is_null($user->password)) &&
                    !empty($user->mot_de_passe_hash)
                ) {
                    DB::table('users')
                        ->where('id_user', $user->id_user)
                        ->update([
                            'password' => $user->mot_de_passe_hash
                        ]);
                }
            }
        }
    }

    public function down()
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        // 🔥 SQLite : supprimer TOUS les index liés aux colonnes modifiées
        if (DB::getDriverName() === 'sqlite') {

            DB::statement('DROP INDEX IF EXISTS users_statut_compte_index');
            DB::statement('DROP INDEX IF EXISTS users_role_index');
            DB::statement('DROP INDEX IF EXISTS users_telephone_unique');
            DB::statement('DROP INDEX IF EXISTS users_telephone_index');
        }

        // Suppression sécurisée des vues
        try {
            DB::statement('DROP VIEW IF EXISTS vue_commandes_jour');
            DB::statement('DROP VIEW IF EXISTS vue_vendeurs_actifs');
        } catch (\Exception $e) {
            // ignoré volontairement
        }

        Schema::table('users', function (Blueprint $table) {

            if (Schema::hasColumn('users', 'derniere_ip')) {
                $table->dropColumn('derniere_ip');
            }

            if (Schema::hasColumn('users', 'date_derniere_connexion')) {
                $table->dropColumn('date_derniere_connexion');
            }

            if (Schema::hasColumn('users', 'date_creation')) {
                $table->dropColumn('date_creation');
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
                $table->dropColumn('telephone');
            }

            if (Schema::hasColumn('users', 'nom_complet')) {
                $table->dropColumn('nom_complet');
            }
        });
    }

}
