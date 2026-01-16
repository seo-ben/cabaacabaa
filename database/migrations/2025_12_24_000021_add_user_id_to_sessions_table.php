<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToSessionsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('sessions')) {
            return;
        }

        Schema::table('sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('sessions', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('last_activity')->index();
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('sessions')) {
            return;
        }
        Schema::table('sessions', function (Blueprint $table) {
            if (Schema::hasColumn('sessions', 'user_id')) {
                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
}
