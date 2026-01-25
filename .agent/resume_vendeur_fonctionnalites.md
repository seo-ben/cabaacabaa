# 🏪 RÉSUMÉ FONCTIONNALITÉS VENDEUR - Vue Rapide

## 📋 TABLE DES MATIÈRES

1. [Authentification](#1-authentification)
2. [Dashboard](#2-dashboard)
3. [Produits](#3-produits)
4. [Commandes](#4-commandes)
5. [Paramètres](#5-paramètres)
6. [Finances](#6-finances)
7. [Coupons](#7-coupons)
8. [Équipe](#8-équipe)

---

## 1. 🔐 AUTHENTIFICATION

### Propriétaire
- **Inscription:** `/vendeur/appliquer` → Vérification admin
- **Connexion:** `/login` → Redirection `/{slug}/dashboard`

### Staff (Employés)
- **Connexion:** `/{slug}/staff-login?token=xxx`
- **Sécurité:** Email + Password + Token unique
- **Permissions:** Système granulaire (JSON)

---

## 2. 📊 DASHBOARD

**Route:** `/{slug}/dashboard`

### Statistiques Affichées
```
📈 Ventes Totales        → Montant commandes terminées
📦 Commandes Actives     → En attente/préparation/prêt
🍕 Total Produits        → Nombre de plats
📋 5 Dernières Commandes → Avec infos client
💰 Solde Wallet          → Disponible pour payout
```

---

## 3. 🍕 PRODUITS (PLATS)

**Route:** `/{slug}/plats`

### Actions Disponibles
| Action | Route | Fonctionnalité |
|--------|-------|----------------|
| **Lister** | `GET /plats` | Tous les produits + filtres |
| **Créer** | `GET /plats/creer` | Formulaire création |
| **Sauvegarder** | `POST /plats` | Validation + Upload image |
| **Modifier** | `GET /plats/{id}/modifier` | Formulaire édition |
| **Mettre à jour** | `PUT /plats/{id}` | Sauvegarde modifications |
| **Supprimer** | `DELETE /plats/{id}` | Suppression produit |

### Champs Produit
```yaml
Obligatoires:
  - Nom du plat
  - Catégorie (limitée aux spécialités)
  - Prix

Optionnels:
  - Description
  - Image (auto-conversion WebP)
  - Promotion (prix réduit)
  - Disponibilité (toggle)
  - Variantes (tailles, options, suppléments)
```

### Système de Variantes
```
Groupe de Variantes
├─ Nom (ex: "Taille")
├─ Obligatoire (oui/non)
├─ Choix multiple (oui/non)
├─ Min/Max choix
└─ Options
   ├─ Option 1 (ex: "Petite", +0 XOF)
   ├─ Option 2 (ex: "Moyenne", +500 XOF)
   └─ Option 3 (ex: "Grande", +1000 XOF)
```

---

## 4. 📦 COMMANDES

**Route:** `/{slug}/commandes`

### Fonctionnalités
- ✅ Liste paginée (10/page)
- ✅ Filtrage par statut
- ✅ Recherche (numéro, nom client)
- ✅ Compteur messages non lus
- ✅ Statistiques par statut

### Statuts & Workflow
```mermaid
en_attente → en_preparation → pret → termine
                                  ↓
                              annule
```

| Statut | Action Vendeur | Automatisme |
|--------|----------------|-------------|
| `en_attente` | Accepter commande | - |
| `en_preparation` | Commencer préparation | ⏰ Timestamp début |
| `pret` | Marquer prêt | ⏰ Timestamp prêt |
| `termine` | Livraison confirmée | 💰 Paiement wallet |
| `annule` | Annuler | - |

### Paiement Automatique (statut "termine")
```
Si paiement = mobile_money:
  1. Commission = 10% × montant_plats
  2. Montant vendeur = Total - Commission
  3. Crédit wallet vendeur
  4. Enregistrement transaction
```

### Chat Commande
- **Messages** vendeur ↔ client
- **Temps réel** avec compteur non lus
- **API:** `/api/orders/{id}/messages`

---

## 5. ⚙️ PARAMÈTRES

**Route:** `/{slug}/parametres`

### 5.1 Profil Commercial
```yaml
Modifiable:
  - Nom commercial
  - Description
  - Adresse
  - Image principale
  - Réseaux sociaux (Facebook, Instagram, Twitter, TikTok)
  - WhatsApp
  - Site web

Non modifiable (après 1ère sélection):
  - Catégorie vendeur (Restaurant, Fast-Food, etc.)
```

### 5.2 Horaires d'Ouverture
```
Lundi    : 09:00 - 22:00  ✓ Ouvert
Mardi    : 09:00 - 22:00  ✓ Ouvert
Mercredi : 09:00 - 22:00  ✓ Ouvert
Jeudi    : 09:00 - 22:00  ✓ Ouvert
Vendredi : 09:00 - 23:00  ✓ Ouvert
Samedi   : 10:00 - 23:00  ✓ Ouvert
Dimanche : --:-- - --:--  ✗ Fermé
```

### 5.3 Spécialités (Catégories Menu)
- ✅ Sélection multiple
- ✅ Création nouvelle spécialité
- ⚠️ **Impact:** Limite les catégories de produits créables

### 5.4 Toggle Boutique
- **Ouvert** / **Fermé** manuel
- Indépendant des horaires

---

## 6. 💰 FINANCES

**Route:** `/{slug}/payouts`

### 6.1 Wallet (Portefeuille)
```
Solde actuel: wallet_balance (XOF)

Crédits automatiques:
  ✓ Commandes terminées (mobile_money)
  ✓ Montant = Total - Commission 10%
```

### 6.2 Demandes de Paiement
```yaml
Minimum: 5000 XOF

Méthodes:
  - Mobile Money (Momo)
  - Flooz
  - Virement bancaire
  - Chèque

Process:
  1. Demande créée → statut: en_attente
  2. Déduction immédiate du wallet
  3. Traitement admin requis
  4. Statuts: en_attente → approuve → paye
```

### 6.3 Historique Transactions
- Type: `credit_vente`
- Montant, Date, Référence
- Notes détaillées

---

## 7. 🎟️ COUPONS

**Route:** `/{slug}/coupons`

### Création Coupon
```yaml
Code: PROMO2026 (unique, auto uppercase)
Type: 
  - percentage (pourcentage)
  - fixed (montant fixe)
Valeur: 10 (10% ou 10 XOF selon type)
Montant minimal: 5000 XOF
Limite utilisation: 100 (optionnel)
Expiration: 2026-02-28 (optionnel)
Actif: ✓
```

### Actions
- ✅ Créer
- ✅ Activer/Désactiver
- ✅ Supprimer

---

## 8. 👥 ÉQUIPE (STAFF)

**Route:** `/{slug}/team`

### Ajout Membre
```yaml
1. Informations:
   - Nom
   - Email (unique)
   - Mot de passe
   - Rôle personnalisé (ex: "Cuisinier", "Livreur")
   - Permissions (array)

2. Système génère:
   - Compte utilisateur
   - Token unique (64 caractères)
   - URL connexion: /{slug}/staff-login?token=xxx

3. Membre se connecte avec:
   - Email + Password + Token
```

### Permissions (Exemples)
```json
[
  "manage_products",
  "view_orders",
  "update_order_status",
  "view_finances",
  "manage_coupons"
]
```

### Gestion
- ✅ Liste membres
- ✅ Ajouter membre
- ✅ Révoquer accès (supprimer)
- ⚠️ Impossible de se supprimer soi-même

---

## 🔒 SÉCURITÉ

### Contrôles
- ✅ Authentification requise
- ✅ Vérification propriétaire sur toutes actions
- ✅ Isolation complète données entre vendeurs
- ✅ Validation stricte formulaires
- ✅ Protection CSRF
- ✅ Upload sécurisé images (WebP)
- ✅ Logging tentatives connexion

### Middlewares
```php
auth                    → Authentification
EnsureUserIsVendeur     → Rôle vendeur
IdentifyVendorBySlug    → Injection vendeur via URL
```

---

## 🎯 ARCHITECTURE URL

### Format Slug-based
```
/{vendor_slug}/{action}

Exemples:
  /pizza-hut/dashboard
  /burger-king/plats
  /sushi-bar/commandes
  /cafe-paris/parametres
```

### Génération Automatique
```php
Slug = Str::slug(nom_commercial)

"Pizza Hut"     → pizza-hut
"Café de Paris" → cafe-de-paris
"Chez Maman"    → chez-maman
```

---

## 📊 MODÈLES PRINCIPAUX

### Vendeur
```php
Relations:
  - user (propriétaire)
  - plats (produits)
  - commandes
  - horaires
  - categories (spécialités)
  - payoutRequests
  - coupons
  - staff (équipe)

Champs clés:
  - nom_commercial, slug
  - wallet_balance
  - statut_verification
  - actif (ouvert/fermé)
```

### VendorStaff
```php
Champs:
  - id_vendeur
  - id_user
  - role_name
  - permissions (JSON)
  - access_token (unique)
```

### PayoutRequest
```php
Champs:
  - montant
  - methode_paiement
  - statut
  - informations_paiement
```

### Coupon
```php
Champs:
  - code (unique)
  - type (percentage/fixed)
  - valeur
  - montant_minimal_achat
  - limite_utilisation
  - expire_at
  - actif
```

---

## 📈 STATISTIQUES DISPONIBLES

### Dashboard
- 💰 Ventes totales
- 📦 Commandes actives
- 🍕 Total produits
- 📋 Dernières commandes

### Commandes
- 📊 Total par statut
- 🔍 Recherche/Filtres
- 💬 Messages non lus

### Finances
- 💵 Solde wallet
- 📜 Historique transactions
- 📤 Demandes payout

---

## ✅ CHECKLIST FONCTIONNALITÉS

### Gestion Boutique
- [x] Dashboard statistiques
- [x] Profil commercial
- [x] Horaires ouverture
- [x] Spécialités menu
- [x] Toggle ouvert/fermé

### Gestion Produits
- [x] CRUD complet
- [x] Variantes/Options
- [x] Upload images
- [x] Promotions
- [x] Disponibilité

### Gestion Commandes
- [x] Liste + filtres
- [x] Mise à jour statuts
- [x] Chat client
- [x] Notifications
- [x] Paiement automatique

### Finances
- [x] Wallet automatique
- [x] Demandes payout
- [x] Historique transactions
- [x] Calcul commissions

### Marketing
- [x] Coupons promotionnels
- [x] Codes réduction
- [x] Limites utilisation

### Équipe
- [x] Multi-utilisateurs
- [x] Permissions
- [x] Connexion sécurisée
- [x] Gestion accès

---

## 🚀 PRÊT POUR PRODUCTION

Le système vendeur est **100% fonctionnel** avec:
- ✅ Toutes fonctionnalités essentielles
- ✅ Sécurité robuste
- ✅ Architecture scalable
- ✅ Automatisations financières
- ✅ Gestion multi-utilisateurs

---

**Dernière mise à jour:** 24 Janvier 2026
