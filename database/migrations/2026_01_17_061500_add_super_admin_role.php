<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('client', 'vendeur', 'admin', 'super_admin') DEFAULT 'client'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back
        // Warning: This will fail if there are 'super_admin' users.
        // Doing nothing or reverting blindly is tricky.
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('client', 'vendeur', 'admin') DEFAULT 'client'");
    }
};
