# 📊 ANALYSE COMPLÈTE DU SYSTÈME CABAACABAA

## 🎯 VUE D'ENSEMBLE

**CabaaCabaa** est une **plateforme de livraison de nourriture** (Food Delivery) développée avec **Laravel 12** (PHP 8.2+).

### Technologies Principales
- **Framework**: Laravel 12
- **Base de données**: MySQL (configurée actuellement pour MySQL, pas SQLite)
- **Frontend**: Blade Templates + Alpine.js + Vite
- **Paiements**: Qosic (QosPay), CinetPay, FedaPay
- **Cartes**: Google Maps API, Mapbox
- **Real-time**: Laravel Reverb (WebSockets)
- **Queue**: Database Queue
- **Cache**: Database Cache

---

## 🗂️ ARCHITECTURE DU SYSTÈME

### 1. **Modèles de Données (30 modèles)**

#### Utilisateurs & Authentification
- `User` - Utilisateurs (clients, vendeurs, admins)
- `Permission` - Permissions système
- `LoginAttempt` - Tentatives de connexion
- `VendorStaff` - Personnel des vendeurs

#### Vendeurs
- `Vendeur` - Restaurants/Épiceries
- `VendeurContact` - Contacts vendeurs
- `VendeurHoraire` - Horaires d'ouverture
- `VendorCategory` - Catégories de vendeurs

#### Catalogue
- `Plat` - Produits/Plats
- `CategoryPlat` - Catégories de plats
- `Section` - Sections de menu
- `Tag` - Tags pour plats
- `Media` - Médias (images)
- `GroupeVariante` - Groupes de variantes (ex: Taille)
- `Variante` - Variantes (ex: Petit, Moyen, Grand)

#### Commandes
- `Commande` - Commandes
- `LigneCommande` - Lignes de commande
- `OrderMessage` - Messages de chat commande

#### Évaluations & Favoris
- `AvisEvaluation` - Avis clients
- `FavorisClient` - Favoris des clients

#### Finance
- `TransactionFinanciere` - Transactions
- `Coupon` - Coupons de réduction
- `PayoutRequest` - Demandes de paiement vendeurs
- `AbonnementTarification` - Abonnements

#### Système
- `AppSetting` - **Paramètres système (BDD)**
- `Country` - Pays supportés
- `ZoneGeographique` - Zones de livraison
- `MiseEnAvant` - Mises en avant
- `Notification` - Notifications
- `LogActivite` - Logs d'activité

---

## ⚙️ SYSTÈME DE CONFIGURATION

### Configuration Actuelle (.env)

Le fichier `.env` contient actuellement:
```env
# Application
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:A4JSDjDlw2Vm9ega3sR7Gzj8/F7FsXp7SiYIiroKZXI=
APP_DEBUG=true
APP_URL=http://localhost
APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr

# Base de données MySQL (PAS SQLite!)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

# Sessions, Cache, Queue
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail
MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### ⚠️ PROBLÈME IDENTIFIÉ: Paramètres en Base de Données

**Le système utilise une table `app_settings` pour stocker des paramètres critiques qui DEVRAIENT être dans le `.env`!**

#### Paramètres actuellement en BDD (AppSetting)

**Groupe: Payment (Paiements)**
- `qosic_url` - URL API Qosic
- `qosic_login` - Login Qosic ⚠️ SENSIBLE
- `qosic_password` - Mot de passe Qosic ⚠️ SENSIBLE
- `qosic_client_id` - Client ID Qosic ⚠️ SENSIBLE
- `cinetpay_site_id` - Site ID CinetPay ⚠️ SENSIBLE
- `cinetpay_api_key` - API Key CinetPay ⚠️ SENSIBLE
- `fedapay_api_key` - API Key FedaPay ⚠️ SENSIBLE
- `fedapay_mode` - Mode FedaPay (sandbox/live)

**Groupe: Location (Localisation)**
- `google_maps_api_key` - Google Maps API Key ⚠️ SENSIBLE
- `mapbox_api_key` - Mapbox API Key ⚠️ SENSIBLE
- `default_delivery_fee` - Frais de livraison par défaut

**Groupe: General (Général)**
- `site_name` - Nom du site
- `system_currency` - Devise (XOF)
- `contact_email` - Email de contact
- `contact_phone` - Téléphone de contact

**Groupe: Branding (Image de marque)**
- `site_logo` - Logo du site (fichier)
- `site_logo_url` - Logo du site (URL)
- `site_favicon` - Favicon (fichier)
- `site_favicon_url` - Favicon (URL)

**Groupe: SEO**
- `meta_title` - Titre Meta
- `meta_description` - Description Meta
- `meta_keywords` - Mots-clés Meta
- `seo_google_analytics` - Google Analytics ID ⚠️ SENSIBLE

**Groupe: Email (Notifications)**
- `email_vendor_approved_subject` - Sujet email vendeur approuvé
- `email_vendor_approved_body` - Corps email vendeur approuvé

---

## 🚨 RECOMMANDATIONS CRITIQUES

### 1. **Migrer les Paramètres Sensibles vers .env**

Les **clés API et mots de passe NE DOIVENT PAS** être stockés en base de données pour des raisons de sécurité:

#### Variables à AJOUTER au .env:

```env
# ============================================
# PAIEMENTS - QOSIC/QOSPAY
# ============================================
QOSPAY_REQUEST_URL=https://api.qosic.net/QosicBridge/user/requestpayment
QOSPAY_LOGIN=
QOSPAY_PASSWORD=
QOSPAY_CLIENT_ID=

# ============================================
# PAIEMENTS - CINETPAY
# ============================================
CINETPAY_SITE_ID=
CINETPAY_API_KEY=
CINETPAY_MODE=sandbox

# ============================================
# PAIEMENTS - FEDAPAY
# ============================================
FEDAPAY_API_KEY=
FEDAPAY_MODE=sandbox

# ============================================
# CARTES & LOCALISATION
# ============================================
GOOGLE_MAPS_API_KEY=
MAPBOX_API_KEY=
DEFAULT_DELIVERY_FEE=500

# ============================================
# SEO & ANALYTICS
# ============================================
GOOGLE_ANALYTICS_ID=

# ============================================
# INFORMATIONS SITE
# ============================================
SITE_NAME=CabaaCabaa
SITE_CONTACT_EMAIL=contact@cabaacabaa.com
SITE_CONTACT_PHONE="+229 00 00 00 00"
SYSTEM_CURRENCY=XOF

# ============================================
# BRANDING (URLs externes si hébergées ailleurs)
# ============================================
SITE_LOGO_URL=
SITE_FAVICON_URL=

# ============================================
# META SEO
# ============================================
META_TITLE="CabaaCabaa - Votre plateforme de livraison préférée"
META_DESCRIPTION="Découvrez les meilleurs restaurants et épiceries près de chez vous. Livraison rapide et fiable."
META_KEYWORDS="food, livraison, restaurant, épicerie, repas, rapide"

# ============================================
# EMAIL TEMPLATES (optionnel, peut rester en BDD)
# ============================================
EMAIL_VENDOR_APPROVED_SUBJECT="Votre boutique est maintenant active ! 🚀"
```

### 2. **Paramètres à GARDER en Base de Données**

Ces paramètres peuvent rester dans `app_settings` car ils sont modifiables par l'admin:
- Logos/Favicons (fichiers uploadés)
- Textes d'emails personnalisables
- Frais de livraison par défaut (modifiable)
- Nom du site (si changement fréquent)
- Informations de contact (si changement fréquent)

### 3. **Modifier le Code pour Utiliser .env en Priorité**

Le code actuel dans `OrderController.php` fait déjà un fallback vers `.env`:

```php
$this->qosic_url = \App\Models\AppSetting::get('qosic_url', env('QOSPAY_REQUEST_URL', 'https://...'));
$this->qosic_login = \App\Models\AppSetting::get('qosic_login', env('QOSPAY_LOGIN'));
```

**Mais il faut INVERSER la priorité**: `.env` d'abord, BDD en fallback.

---

## 📁 STRUCTURE DES CONTRÔLEURS

### Admin Controllers (12 contrôleurs)
- `AdminController` - Dashboard admin
- `SettingController` - **Gestion des paramètres système**
- `VendorController` - Gestion vendeurs
- `UserController` - Gestion utilisateurs
- `CategoryController` - Catégories de plats
- `VendorCategoryController` - Catégories de vendeurs
- `PlatController` - Gestion produits
- `OrderController` - Gestion commandes
- `ZoneController` - Zones géographiques
- `FinanceController` - Finance & payouts
- `SecurityController` - Sécurité & logs
- `CountryController` - Pays supportés
- `AdminUserController` - Gestion admins

### Vendor Controllers (8 contrôleurs)
- `VendorDashboardController` - Dashboard vendeur
- `PlatController` - Gestion produits vendeur
- `OrderController` - Commandes vendeur
- `VendorSettingsController` - Paramètres vendeur
- `PayoutController` - Demandes de paiement
- `CouponController` - Coupons vendeur
- `TeamController` - Gestion équipe
- `StaffAuthController` - Auth personnel

### Public Controllers (14 contrôleurs)
- `HomeController` - Page d'accueil & exploration
- `WelcomeController` - Page de bienvenue
- `AuthController` - Authentification
- `CartController` - Panier
- `OrderController` - **Commandes & paiements**
- `CouponController` - Application coupons
- `FavoriteController` - Favoris
- `NotificationController` - Notifications
- `ProfileController` - Profil utilisateur
- `ReviewController` - Avis
- `OrderChatController` - Chat commandes
- `NewsletterController` - Newsletter

---

## 🔐 SYSTÈME D'AUTHENTIFICATION

### Rôles Utilisateurs
- `client` - Client standard
- `vendeur` - Propriétaire de restaurant
- `admin` - Administrateur
- `super_admin` - Super administrateur

### Middlewares
- `EnsureUserIsAdmin` - Accès admin
- `EnsureUserIsVendeur` - Accès vendeur
- `IdentifyVendorBySlug` - Identification vendeur par slug

---

## 💳 SYSTÈME DE PAIEMENT

### Providers Supportés
1. **Qosic (QosPay)** - Mobile Money Togo
2. **CinetPay** - Paiements Afrique de l'Ouest
3. **FedaPay** - Paiements mobile money

### Flux de Paiement
1. Client passe commande
2. Si paiement mobile money → Appel API Qosic
3. Push notification sur téléphone client
4. Callback webhook pour confirmation
5. Mise à jour statut commande

---

## 🗺️ SYSTÈME DE LIVRAISON

### Calcul des Frais
- **Formule Haversine** pour distance
- Frais de base: 300 FCFA
- Tarif par km: 150 FCFA
- Frais minimum: 500 FCFA (configurable)
- Distance max: 25 km

### Zones Géographiques
- Gestion des zones de couverture
- Détection automatique de localisation
- Vérification de couverture par adresse

---

## 📊 FONCTIONNALITÉS PRINCIPALES

### Pour les Clients
- ✅ Exploration restaurants/épiceries
- ✅ Panier avec variantes
- ✅ Commande (emporter/sur place/livraison)
- ✅ Paiement mobile money
- ✅ Suivi de commande en temps réel
- ✅ Chat avec vendeur
- ✅ Avis et évaluations
- ✅ Favoris
- ✅ Coupons de réduction
- ✅ Historique commandes

### Pour les Vendeurs
- ✅ Dashboard avec statistiques
- ✅ Gestion produits/plats
- ✅ Gestion variantes (tailles, options)
- ✅ Gestion commandes
- ✅ Gestion horaires
- ✅ Gestion coupons
- ✅ Demandes de paiement (wallet)
- ✅ Gestion équipe (staff)
- ✅ Chat avec clients

### Pour les Admins
- ✅ Dashboard global
- ✅ Gestion vendeurs (approbation)
- ✅ Gestion utilisateurs
- ✅ Gestion catégories
- ✅ Gestion zones géographiques
- ✅ Gestion commandes
- ✅ Finance & transactions
- ✅ **Paramètres système** (AppSettings)
- ✅ Sécurité & logs
- ✅ Gestion pays

---

## 🎨 INTERFACE ADMIN - PARAMÈTRES

L'interface admin (`/admin/settings`) permet de configurer:
- **General**: Nom site, devise, contacts
- **Branding**: Logos, favicons
- **Payment**: Clés API paiements
- **Location**: Clés API cartes, frais livraison
- **SEO**: Meta tags, Google Analytics
- **Email**: Templates emails

**⚠️ PROBLÈME**: Les clés API sont modifiables via l'interface web, ce qui est un risque de sécurité!

---

## 📝 RÉSUMÉ DES ACTIONS NÉCESSAIRES

### 1. ✅ Créer un fichier .env.example complet
Avec toutes les variables nécessaires documentées

### 2. ✅ Ajouter les variables manquantes au .env
Toutes les clés API et paramètres sensibles

### 3. ⚠️ Modifier le code pour prioriser .env
Inverser la logique: `.env` d'abord, BDD en fallback

### 4. ⚠️ Supprimer les paramètres sensibles de la BDD
Ou les masquer dans l'interface admin

### 5. ⚠️ Mettre à jour le Seeder
Ne pas créer de paramètres sensibles par défaut

### 6. ⚠️ Documenter la configuration
Guide de déploiement avec toutes les variables

---

## 🔒 SÉCURITÉ

### Bonnes Pratiques Actuelles
✅ Authentification Laravel
✅ CSRF Protection
✅ Middleware de rôles
✅ Logs d'activité
✅ Tentatives de connexion trackées

### À Améliorer
⚠️ Clés API en base de données
⚠️ Pas de chiffrement des paramètres sensibles
⚠️ Interface admin expose les clés API

---

## 📦 DÉPENDANCES PRINCIPALES

```json
{
  "cinetpay/cinetpay-php": "^1.9",
  "fedapay/fedapay-php": "^0.4.7",
  "laravel/framework": "^12.0",
  "laravel/reverb": "^1.0",
  "pusher/pusher-php-server": "^7.2"
}
```

---

## 🌐 ROUTES PRINCIPALES

- `/` - Accueil
- `/explore` - Explorer restaurants
- `/vendor/{id}-{slug}` - Page vendeur
- `/panier` - Panier
- `/checkout` - Commande
- `/mes-commandes` - Historique
- `/admin/*` - Administration
- `/{vendor_slug}/*` - Dashboard vendeur

---

**Date d'analyse**: 21 janvier 2026
**Version Laravel**: 12.0
**Base de données**: MySQL
**Environnement**: Local (Development)
