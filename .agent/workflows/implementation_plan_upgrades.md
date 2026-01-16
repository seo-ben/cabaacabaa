# Plan d'implémentation - Améliorations Système Cabaa

Ce document détaille les étapes pour implémenter les suggestions retenues pour le système.

## 1. Fondation Database & Modèles (Back-end)
- [ ] **Système de Coupons**
    - Créer la migration `create_coupons_table` (code, type, valeur, date_expiration, usage_limit).
    - Créer le modèle `Coupon`.
    - Créer la table pivot `coupon_user` pour suivre l'utilisation.
- [ ] **Programme de Parrainage**
    - Ajouter `referral_code` et `referred_by` à la table `users`.
    - Ajouter `referral_balance` (ou utiliser un système de points) aux utilisateurs.
- [ ] **Wallet Vendeur (Portefeuille)**
    - Ajouter `wallet_balance` à la table `vendeurs`.
    - Créer la table `payout_requests` (demandes de retrait).
- [ ] **Géofencing Avancé**
    - Ajouter des champs de coordonnées (lat/long) aux vendeurs et zones si manquants.

## 2. Interface Utilisateur (UI/UX)
- [ ] **Skeleton Loaders**
    - Modifier `explore.blade.php` pour inclure des états de chargement Alpine.js ou CSS.
- [ ] **Mode Sombre (Dark Mode)**
    - Configurer Tailwind pour le mode sombre.
    - Ajouter un bouton toggle dans le header (layout app).
- [ ] **Optimisation Image (WebP)**
    - Configurer une intervention image ou un helper pour servir du WebP.

## 3. Logique Métier (Controllers)
- [ ] **Gestion des Coupons au Panier**
    - Créer `CouponController`.
    - Modifier `CartController` pour appliquer les réductions.
- [ ] **Logique de Parrainage à l'Inscription**
    - Modifier `AuthController` pour générer un code unique et vérifier le parrain.
- [ ] **Dashboard Retrait Vendeur**
    - Créer une vue pour que le vendeur demande ses gains.

## 4. Temps Réel & Notifications
- [ ] **Notifications Push/Real-time**
    - Configurer les bases pour les notifications instantanées (Database notifications + Broadcast).

---
*Note : Les suggestions 2 (Recherche Algolia) et 4 (Vérification IA) sont exclues selon la demande USER.*
