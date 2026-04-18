<?php
$baseDir = __DIR__ . '/app/Http/Controllers';
$apiDir = __DIR__ . '/app/Http/Controllers/Api';

function convertToApi($src, $dest) {
    if (is_dir($src)) {
        if (!is_dir($dest)) mkdir($dest, 0755, true);
        $files = scandir($src);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') continue;
            // Ne pas dupliquer le dossier Api lui-même ou les controllers Auth par défaut
            if ($src === __DIR__ . '/app/Http/Controllers' && $file === 'Api') continue;
            if ($src === __DIR__ . '/app/Http/Controllers' && $file === 'Auth') continue; 
            
            convertToApi("$src/$file", "$dest/$file");
        }
    } else {
        if (pathinfo($src, PATHINFO_EXTENSION) !== 'php') return;
        
        // On ne va pas écraser les 4 contrôleurs API qu'on a fait de nos mains précédemment si on veut les garder,
        // mais pour ce script on va juste laisser ceux qu'on a déjà fait
        if (file_exists($dest) && in_array(basename($dest), ['AuthController.php', 'ClientController.php', 'DriverController.php', 'VendorController.php'])) {
            return;
        }

        $content = file_get_contents($src);
        
        // 1. Adapter le Namespace pour y inclure \Api
        $content = preg_replace('/namespace\s+App\\\\Http\\\\Controllers\b/', 'namespace App\Http\Controllers\Api', $content);
        $content = preg_replace('/namespace\s+App\\\\Http\\\\Controllers\\\\([a-zA-Z0-9_\\\\]+)/', 'namespace App\Http\Controllers\Api\\\$1', $content);
        
        // 2. Ajouter l'import vers le Controller parent si besoin
        if (strpos($content, 'use App\Http\Controllers\Controller;') === false && basename($dest) !== 'Controller.php') {
             $content = preg_replace('/namespace\s+([a-zA-Z0-9_\\\\]+);/', "namespace $1;\n\nuse App\Http\Controllers\Controller;", $content);
        }
        
        // 3. Mini patch: transformer naïvement les `return view(...)` ou `return redirect(...)` en renvoi de JSON.
        // C'est basique mais ça évite à l'appli de crasher avec des vues manquantes !
        $content = preg_replace('/return\s+view\([^\;]+\);/', 'return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);', $content);
        $content = preg_replace('/return\s+redirect\([^\;]+\)(\-\>([^\;]+))?;/', 'return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);', $content);
        $content = preg_replace('/return\s+back\([^\;]*\)(\-\>([^\;]+))?;/', 'return response()->json(["info" => "Action terminée (Ancien back).", "status" => "success"]);', $content);

        file_put_contents($dest, $content);
        echo "Fichier généré : " . str_replace(__DIR__, '', $dest) . "\n";
    }
}

convertToApi($baseDir, $apiDir);
echo "Terminé !\n";
