<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

// On charge Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "<h1>Ajout des colonnes manquantes à la table 'vendeurs'</h1>";

try {
    if (!Schema::hasColumn('vendeurs', 'facebook_url')) {
        Schema::table('vendeurs', function (Blueprint $table) {
            $table->string('facebook_url', 255)->nullable()->after('image_principale');
            $table->string('instagram_url', 255)->nullable()->after('facebook_url');
            $table->string('twitter_url', 255)->nullable()->after('instagram_url');
            $table->string('tiktok_url', 255)->nullable()->after('twitter_url');
            $table->string('whatsapp_number', 255)->nullable()->after('tiktok_url');
            $table->string('website_url', 255)->nullable()->after('whatsapp_number');
        });
        echo "<p style='color:green'>✅ Les colonnes ont été ajoutées avec succès !</p>";
    } else {
        echo "<p style='color:orange'>⚠️ Les colonnes existent déjà.</p>";
    }
} catch (\Exception $e) {
    echo "<p style='color:red'>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>
