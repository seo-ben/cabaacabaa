<?php
// Script utilitaire pour créer le lien symbolique storage
// À placer dans le dossier public/ sur le serveur

$target = __DIR__ . '/../storage/app/public';
$link = __DIR__ . '/storage';

echo "<h1>Configuration du Storage</h1>";

// 1. Vérification de la structure
if (!file_exists($target)) {
    echo "<p style='color:red'>❌ Erreur critique : Le dossier cible n'existe pas : $target</p>";
    echo "<p>Assurez-vous que vous avez bien envoyé tout le dossier 'storage' de votre projet Laravel.</p>";
    die();
}

// 2. Vérification de l'existant
if (file_exists($link)) {
    echo "<p>⚠️ Un élément 'storage' existe déjà dans le dossier public.</p>";
    
    if (is_link($link)) {
        echo "<p style='color:green'>✅ C'est déjà un lien symbolique.</p>";
        echo "<p>Il pointe vers : " . readlink($link) . "</p>";
        echo "<p>Si les images ne s'affichent pas, le chemin est peut-être incorrect ou il s'agit d'un problème de permissions.</p>";
    } elseif (is_dir($link)) {
        echo "<p style='color:orange'>⚠️ C'est un dossier physique, pas un lien !</p>";
        echo "<p><strong>Action requise :</strong> Supprimez le dossier 'public/storage' via votre client FTP/Gestionnaire de fichiers (assurez-vous qu'il est vide ou sauvegardez-le avant), puis rechargez cette page.</p>";
    }
} else {
    // 3. Création du lien
    try {
        // On tente avec le chemin relatif qui est souvent plus fiable
        if (symlink('../storage/app/public', $link)) {
            echo "<p style='color:green'>✅ Lien symbolique créé avec succès !</p>";
        } else {
            throw new Exception("Échec avec chemin relatif");
        }
    } catch (Exception $e) {
        // Fallback chemin absolu
        try {
            if (symlink($target, $link)) {
                echo "<p style='color:green'>✅ Lien symbolique créé avec succès (chemin absolu) !</p>";
            } else {
                echo "<p style='color:red'>❌ Impossible de créer le lien symbolique.</p>";
                echo "<p>Erreur : " . $e->getMessage() . "</p>";
                echo "<p>La fonction symlink() est peut-être désactivée par votre hébergeur (LWS). Contactez le support ou créez le lien via le terminal si vous avez un accès SSH.</p>";
            }
        } catch (Exception $e2) {
             echo "<p style='color:red'>❌ Erreur fatale : " . $e2->getMessage() . "</p>";
        }
    }
}

echo "<hr>";
echo "<h3>Vérification des Permissions (Dossier parent)</h3>";
if (is_writable(dirname($target))) {
    echo "<p style='color:green'>✅ Le dossier storage/app est accessible en écriture.</p>";
} else {
    echo "<p style='color:red'>❌ Le dossier storage/app n'est PAS accessible en écriture.</p>";
    echo "<p>Veuillez régler les permissions (CHMOD 775 ou 777) sur le dossier <code>storage</code> et ses sous-dossiers via votre client FTP.</p>";
}
?>
