# 🔄 GUIDE DE MIGRATION: Paramètres BDD → .env

## 📋 Vue d'ensemble

Ce guide explique comment migrer les paramètres sensibles de la table `app_settings` vers le fichier `.env` pour améliorer la sécurité du système.

---

## ⚠️ POURQUOI MIGRER ?

### Problèmes Actuels
1. **Sécurité**: Les clés API sont stockées en clair dans la base de données
2. **Exposition**: L'interface admin expose les clés API sensibles
3. **Versioning**: Les paramètres sensibles peuvent être versionnés par erreur
4. **Déploiement**: Difficile de gérer différents environnements (dev, staging, prod)

### Avantages de .env
1. ✅ **Sécurité renforcée**: Fichier exclu du versioning (.gitignore)
2. ✅ **Séparation des environnements**: Différentes valeurs par environnement
3. ✅ **Bonnes pratiques**: Standard Laravel et 12-factor app
4. ✅ **Protection**: Pas d'exposition via l'interface web

---

## 📝 ÉTAPE 1: Mettre à jour le fichier .env

### 1.1 Copier le template
```bash
cp .env.example .env
```

### 1.2 Ajouter les paramètres manquants

Ouvrez votre fichier `.env` et ajoutez/mettez à jour ces sections:

```env
# ============================================
# PAIEMENTS - QOSIC/QOSPAY
# ============================================
QOSPAY_REQUEST_URL=https://api.qosic.net/QosicBridge/user/requestpayment
QOSPAY_LOGIN=votre_login_qosic
QOSPAY_PASSWORD=votre_password_qosic
QOSPAY_CLIENT_ID=votre_client_id_qosic

# ============================================
# PAIEMENTS - CINETPAY
# ============================================
CINETPAY_SITE_ID=votre_site_id_cinetpay
CINETPAY_API_KEY=votre_api_key_cinetpay
CINETPAY_MODE=sandbox

# ============================================
# PAIEMENTS - FEDAPAY
# ============================================
FEDAPAY_API_KEY=votre_api_key_fedapay
FEDAPAY_MODE=sandbox

# ============================================
# CARTES & LOCALISATION
# ============================================
GOOGLE_MAPS_API_KEY=votre_google_maps_key
MAPBOX_API_KEY=votre_mapbox_key
DEFAULT_DELIVERY_FEE=500

# ============================================
# SEO & ANALYTICS
# ============================================
GOOGLE_ANALYTICS_ID=G-XXXXXXXXXX

# ============================================
# INFORMATIONS DU SITE
# ============================================
SITE_NAME=CabaaCabaa
SITE_CONTACT_EMAIL=contact@cabaacabaa.com
SITE_CONTACT_PHONE="+229 00 00 00 00"
SYSTEM_CURRENCY=XOF
```

---

## 🔧 ÉTAPE 2: Modifier le Code

### 2.1 Créer un Helper de Configuration

Créez `app/Helpers/ConfigHelper.php`:

```php
<?php

namespace App\Helpers;

use App\Models\AppSetting;

class ConfigHelper
{
    /**
     * Get a configuration value with priority: .env > database > default
     */
    public static function get(string $key, $default = null)
    {
        // Map database keys to .env keys
        $envMap = [
            'qosic_url' => 'QOSPAY_REQUEST_URL',
            'qosic_login' => 'QOSPAY_LOGIN',
            'qosic_password' => 'QOSPAY_PASSWORD',
            'qosic_client_id' => 'QOSPAY_CLIENT_ID',
            'cinetpay_site_id' => 'CINETPAY_SITE_ID',
            'cinetpay_api_key' => 'CINETPAY_API_KEY',
            'fedapay_api_key' => 'FEDAPAY_API_KEY',
            'fedapay_mode' => 'FEDAPAY_MODE',
            'google_maps_api_key' => 'GOOGLE_MAPS_API_KEY',
            'mapbox_api_key' => 'MAPBOX_API_KEY',
            'default_delivery_fee' => 'DEFAULT_DELIVERY_FEE',
            'seo_google_analytics' => 'GOOGLE_ANALYTICS_ID',
            'site_name' => 'SITE_NAME',
            'system_currency' => 'SYSTEM_CURRENCY',
            'contact_email' => 'SITE_CONTACT_EMAIL',
            'contact_phone' => 'SITE_CONTACT_PHONE',
        ];

        // Check if this key should come from .env
        if (isset($envMap[$key])) {
            $envValue = env($envMap[$key]);
            if ($envValue !== null) {
                return $envValue;
            }
        }

        // Fallback to database
        $setting = AppSetting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Check if a key is sensitive (should not be displayed in admin)
     */
    public static function isSensitive(string $key): bool
    {
        $sensitiveKeys = [
            'qosic_login',
            'qosic_password',
            'qosic_client_id',
            'cinetpay_site_id',
            'cinetpay_api_key',
            'fedapay_api_key',
            'google_maps_api_key',
            'mapbox_api_key',
            'seo_google_analytics',
        ];

        return in_array($key, $sensitiveKeys);
    }
}
```

### 2.2 Enregistrer le Helper

Ajoutez dans `composer.json`:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/"
    },
    "files": [
        "app/Helpers/VendorHelper.php",
        "app/Helpers/ConfigHelper.php"
    ]
},
```

Puis exécutez:
```bash
composer dump-autoload
```

### 2.3 Modifier OrderController.php

Remplacez dans `app/Http/Controllers/OrderController.php`:

```php
use App\Helpers\ConfigHelper;

public function __construct()
{
    $this->qosic_url = ConfigHelper::get('qosic_url', 'https://api.qosic.net/QosicBridge/user/requestpayment');
    $this->qosic_login = ConfigHelper::get('qosic_login');
    $this->qosic_password = ConfigHelper::get('qosic_password');
    $this->qosic_clientid = ConfigHelper::get('qosic_client_id');
}

// Ligne 109
$minFee = (int) ConfigHelper::get('default_delivery_fee', 500);
```

### 2.4 Modifier AppServiceProvider.php

Dans `app/Providers/AppServiceProvider.php`:

```php
use App\Helpers\ConfigHelper;

public function boot(): void
{
    if (Schema::hasTable('app_settings')) {
        view()->share('currency', ConfigHelper::get('system_currency', 'XOF'));
        view()->share('siteName', ConfigHelper::get('site_name', 'CabaaCabaa'));
        
        $logoPath = ConfigHelper::get('site_logo');
        view()->share('siteLogo', $logoPath ? asset('storage/' . $logoPath) : null);
        
        $faviconPath = ConfigHelper::get('site_favicon');
        view()->share('siteFavicon', $faviconPath ? asset('storage/' . $faviconPath) : null);
    }
}
```

---

## 🎨 ÉTAPE 3: Modifier l'Interface Admin

### 3.1 Masquer les Paramètres Sensibles

Modifiez `resources/views/admin/settings/index.blade.php`:

```blade
@foreach($items as $setting)
    @php
        $isSensitive = \App\Helpers\ConfigHelper::isSensitive($setting->key);
        $isFromEnv = env(strtoupper(str_replace('_', '', $setting->key))) !== null;
    @endphp
    
    <div class="space-y-3 {{ in_array($setting->type, ['textarea', 'image']) ? 'md:col-span-2' : '' }}">
        <label for="{{ $setting->key }}" class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] ml-1">
            {{ $setting->label }}
            @if($isFromEnv)
                <span class="ml-2 px-2 py-1 bg-green-100 text-green-700 text-[9px] rounded-full">Configuré via .env</span>
            @endif
        </label>
        
        <div class="relative">
            @if($isSensitive && $isFromEnv)
                {{-- Champ masqué si configuré via .env --}}
                <input type="text" disabled value="••••••••••••••••" 
                    class="w-full px-6 py-4 bg-gray-100 border-2 border-gray-200 rounded-2xl font-bold text-gray-500 cursor-not-allowed">
                <p class="text-xs text-gray-500 mt-2">
                    ⚠️ Ce paramètre est configuré dans le fichier .env pour des raisons de sécurité.
                </p>
            @elseif($setting->type === 'password')
                <input type="password" name="{{ $setting->key }}" id="{{ $setting->key }}" 
                    value="{{ $setting->value }}"
                    placeholder="Laisser vide pour ne pas modifier"
                    class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all shadow-sm">
            @else
                {{-- Code existant pour les autres types --}}
            @endif
        </div>
    </div>
@endforeach
```

### 3.2 Modifier le SettingController

Dans `app/Http/Controllers/Admin/SettingController.php`:

```php
use App\Helpers\ConfigHelper;

public function update(Request $request)
{
    $data = $request->except(['_token', '_method']);

    foreach ($data as $key => $value) {
        $setting = AppSetting::where('key', $key)->first();
        if (!$setting) continue;

        // Ne pas mettre à jour si le paramètre vient de .env
        if (ConfigHelper::isSensitive($key)) {
            // Vérifier si configuré dans .env
            $envKey = strtoupper(str_replace('_', '', $key));
            if (env($envKey) !== null) {
                continue; // Skip, géré par .env
            }
        }

        if ($setting->type === 'image') {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $path = \App\Helpers\ImageHelper::uploadAndConvert($file, 'settings');
                $setting->update(['value' => $path]);
            }
        } else {
            $setting->update(['value' => $value]);
        }
    }

    return redirect()->back()->with('success', 'Paramètres mis à jour avec succès.');
}
```

---

## 🗑️ ÉTAPE 4: Nettoyer la Base de Données (Optionnel)

### Option A: Supprimer les Paramètres Sensibles

```php
// Créer une migration: php artisan make:migration remove_sensitive_settings_from_database

public function up()
{
    $sensitiveKeys = [
        'qosic_login',
        'qosic_password',
        'qosic_client_id',
        'cinetpay_site_id',
        'cinetpay_api_key',
        'fedapay_api_key',
        'google_maps_api_key',
        'mapbox_api_key',
        'seo_google_analytics',
    ];

    DB::table('app_settings')
        ->whereIn('key', $sensitiveKeys)
        ->delete();
}
```

### Option B: Marquer comme "Géré par .env"

```php
public function up()
{
    $sensitiveKeys = [
        'qosic_login',
        'qosic_password',
        // ... autres clés
    ];

    DB::table('app_settings')
        ->whereIn('key', $sensitiveKeys)
        ->update([
            'value' => null,
            'label' => DB::raw("CONCAT(label, ' (Configuré via .env)')"),
        ]);
}
```

---

## 🧪 ÉTAPE 5: Tester la Migration

### 5.1 Vérifier les Paiements

```bash
# Tester une commande avec paiement mobile money
# Vérifier que les credentials Qosic sont bien lus depuis .env
```

### 5.2 Vérifier l'Interface Admin

1. Accéder à `/admin/settings`
2. Vérifier que les champs sensibles affichent "Configuré via .env"
3. Vérifier que les autres champs fonctionnent normalement

### 5.3 Vérifier les Logs

```bash
# Vérifier qu'il n'y a pas d'erreurs
tail -f storage/logs/laravel.log
```

---

## 📚 ÉTAPE 6: Documentation

### 6.1 Créer un Guide de Déploiement

Créez `docs/DEPLOYMENT.md`:

```markdown
# Guide de Déploiement CabaaCabaa

## Configuration Requise

### 1. Variables d'Environnement Obligatoires

Copiez `.env.example` vers `.env` et configurez:

#### Paiements
- `QOSPAY_LOGIN`: Login Qosic
- `QOSPAY_PASSWORD`: Mot de passe Qosic
- `QOSPAY_CLIENT_ID`: Client ID Qosic

#### Cartes
- `GOOGLE_MAPS_API_KEY`: Clé API Google Maps

### 2. Installation

```bash
composer install
npm install
php artisan key:generate
php artisan migrate
php artisan db:seed
npm run build
```
```

---

## ✅ CHECKLIST DE MIGRATION

- [ ] Copier `.env.example` vers `.env`
- [ ] Ajouter toutes les clés API dans `.env`
- [ ] Créer `ConfigHelper.php`
- [ ] Enregistrer le helper dans `composer.json`
- [ ] Exécuter `composer dump-autoload`
- [ ] Modifier `OrderController.php`
- [ ] Modifier `AppServiceProvider.php`
- [ ] Modifier la vue `admin/settings/index.blade.php`
- [ ] Modifier `SettingController.php`
- [ ] Tester les paiements
- [ ] Tester l'interface admin
- [ ] (Optionnel) Nettoyer la BDD
- [ ] Documenter le déploiement
- [ ] Vérifier que `.env` est dans `.gitignore`

---

## 🔒 SÉCURITÉ POST-MIGRATION

### Vérifications
1. ✅ Le fichier `.env` est dans `.gitignore`
2. ✅ Les clés API ne sont plus visibles dans l'admin
3. ✅ Les valeurs sensibles ne sont plus en BDD
4. ✅ Chaque environnement a son propre `.env`

### Bonnes Pratiques
- Ne jamais commiter le fichier `.env`
- Utiliser des clés différentes pour dev/staging/prod
- Changer régulièrement les clés API
- Utiliser des secrets managers en production (AWS Secrets Manager, etc.)

---

## 🆘 DÉPANNAGE

### Problème: "Class ConfigHelper not found"
```bash
composer dump-autoload
```

### Problème: Les paiements ne fonctionnent plus
Vérifier que les variables `.env` sont bien définies:
```bash
php artisan tinker
>>> env('QOSPAY_LOGIN')
```

### Problème: L'admin affiche toujours les clés
Vider le cache:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

**Date**: 21 janvier 2026
**Version**: 1.0
**Auteur**: Équipe CabaaCabaa
