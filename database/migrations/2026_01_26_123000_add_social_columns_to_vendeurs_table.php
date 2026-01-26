<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vendeurs', function (Blueprint $table) {
            if (!Schema::hasColumn('vendeurs', 'facebook_url')) {
                $table->string('facebook_url', 255)->nullable()->after('image_principale');
            }
            if (!Schema::hasColumn('vendeurs', 'instagram_url')) {
                $table->string('instagram_url', 255)->nullable()->after('facebook_url');
            }
            if (!Schema::hasColumn('vendeurs', 'twitter_url')) {
                $table->string('twitter_url', 255)->nullable()->after('instagram_url');
            }
            if (!Schema::hasColumn('vendeurs', 'tiktok_url')) {
                $table->string('tiktok_url', 255)->nullable()->after('twitter_url');
            }
            if (!Schema::hasColumn('vendeurs', 'whatsapp_number')) {
                $table->string('whatsapp_number', 255)->nullable()->after('tiktok_url');
            }
            if (!Schema::hasColumn('vendeurs', 'website_url')) {
                $table->string('website_url', 255)->nullable()->after('whatsapp_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendeurs', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_url',
                'instagram_url',
                'twitter_url',
                'tiktok_url',
                'whatsapp_number',
                'website_url'
            ]);
        });
    }
};
