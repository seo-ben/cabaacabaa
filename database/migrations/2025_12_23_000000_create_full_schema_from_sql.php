<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class CreateFullSchemaFromSql extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Neutralized: migrations are now provided as individual Laravel migration files.
        // This migration intentionally does nothing to avoid executing raw MySQL SQL
        // which may be incompatible with the current connection (sqlite/mysql differences).
        return;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Nothing to undo here; individual migrations handle schema rollback.
        return;
    }
}
