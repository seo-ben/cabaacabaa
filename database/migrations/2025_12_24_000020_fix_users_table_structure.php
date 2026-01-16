<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixUsersTableStructure extends Migration
{
    public function up()
    {
        // If users table does not exist, create it with expected columns
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->bigIncrements('id_user');
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('remember_token', 100)->nullable();
                $table->string('role')->default('client');
                $table->string('statut_compte')->default('actif');
                $table->timestamps();
            });
            return;
        }

        // Otherwise, add missing columns safely
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'email')) {
                $table->string('email')->unique()->after('name');
            }
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'password')) {
                $table->string('password')->after('email_verified_at');
            }
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->string('remember_token', 100)->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('client')->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'statut_compte')) {
                $table->string('statut_compte')->default('actif')->after('role');
            }
            if (!Schema::hasColumn('users', 'created_at') || !Schema::hasColumn('users', 'updated_at')) {
                $table->timestamps();
            }
        });
    }

    public function down()
    {
        // Do not drop users table in rollback to avoid data loss. Only remove columns we added if present.
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
            // do not drop role/statut or timestamps to avoid breaking app expectations
        });
    }
}
