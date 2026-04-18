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
        Schema::table('vendeurs', function (Blueprint $table) {
            $table->integer('subscription_plan')->default(0)->after('boost_expires_at')->comment('0=Gratuit, 15000=Standard, 45000=Premium');
            $table->timestamp('trial_ends_at')->nullable()->after('subscription_plan');
            $table->boolean('trial_used')->default(false)->after('trial_ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendeurs', function (Blueprint $table) {
            $table->dropColumn(['subscription_plan', 'trial_ends_at', 'trial_used']);
        });
    }
};
