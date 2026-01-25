# 🔄 DIAGRAMMES & FLUX VENDEUR

## 1. ARCHITECTURE SYSTÈME VENDEUR

```
┌─────────────────────────────────────────────────────────────┐
│                    ESPACE VENDEUR                            │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌──────────────┐      ┌──────────────┐                    │
│  │ Propriétaire │      │    Staff     │                    │
│  │   (Owner)    │      │  (Employés)  │                    │
│  └──────┬───────┘      └──────┬───────┘                    │
│         │                     │                             │
│         │ /login              │ /{slug}/staff-login?token   │
│         │                     │                             │
│         └─────────┬───────────┘                             │
│                   ▼                                          │
│         ┌─────────────────────┐                             │
│         │  /{vendor_slug}/    │                             │
│         │     dashboard       │                             │
│         └─────────┬───────────┘                             │
│                   │                                          │
│         ┌─────────┴─────────┐                               │
│         │                   │                               │
│    ┌────▼────┐         ┌───▼────┐                          │
│    │ Produits│         │Commandes│                          │
│    │  /plats │         │/commandes│                         │
│    └────┬────┘         └───┬────┘                          │
│         │                  │                                │
│    ┌────▼────┐         ┌───▼────┐                          │
│    │Paramètres│        │Finances│                          │
│    │/parametres│       │/payouts│                          │
│    └────┬────┘         └───┬────┘                          │
│         │                  │                                │
│    ┌────▼────┐         ┌───▼────┐                          │
│    │ Coupons │         │ Équipe │                          │
│    │/coupons │         │ /team  │                          │
│    └─────────┘         └────────┘                          │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

---

## 2. FLUX CRÉATION PRODUIT

```
┌─────────────────────────────────────────────────────────────┐
│                  CRÉATION PRODUIT                            │
└─────────────────────────────────────────────────────────────┘

Vendeur
  │
  ├─► GET /{slug}/plats/creer
  │     │
  │     ├─► Vérification: A-t-il des spécialités ?
  │     │     │
  │     │     ├─► NON → Redirection /parametres
  │     │     │          (Message: Définir spécialités d'abord)
  │     │     │
  │     │     └─► OUI → Affichage formulaire
  │     │              (Catégories = spécialités seulement)
  │     │
  │     └─► Formulaire:
  │           - Nom plat
  │           - Catégorie (dropdown filtré)
  │           - Description
  │           - Prix
  │           - Image
  │           - Variantes (dynamique)
  │
  ├─► POST /{slug}/plats
  │     │
  │     ├─► Validation
  │     │     ├─► Nom requis
  │     │     ├─► Catégorie existe
  │     │     ├─► Catégorie ∈ spécialités vendeur ✓
  │     │     ├─► Prix > 0
  │     │     └─► Image < 2MB
  │     │
  │     ├─► Upload Image
  │     │     └─► ImageHelper::uploadAndConvert()
  │     │           └─► Conversion WebP
  │     │
  │     ├─► Création Plat
  │     │     └─► id_vendeur auto-assigné
  │     │
  │     ├─► Création Variantes (si présentes)
  │     │     │
  │     │     └─► Pour chaque groupe:
  │     │           ├─► Créer GroupeVariante
  │     │           └─► Créer Options (Variante)
  │     │
  │     └─► Redirection /{slug}/plats
  │           (Message: Plat ajouté avec succès)
  │
  └─► Résultat: Produit visible dans liste
```

---

## 3. FLUX GESTION COMMANDE

```
┌─────────────────────────────────────────────────────────────┐
│              WORKFLOW COMMANDE VENDEUR                       │
└─────────────────────────────────────────────────────────────┘

Client passe commande
  │
  ▼
┌─────────────────┐
│  en_attente     │ ← Nouvelle commande arrive
└────────┬────────┘
         │
         │ Vendeur: "Accepter"
         │ PATCH /{slug}/commandes/{id}/statut
         │ Body: { statut: "en_preparation" }
         │
         ▼
┌─────────────────┐
│ en_preparation  │ ← Automatisme: heure_preparation_debut = now()
└────────┬────────┘
         │
         │ Vendeur: "Marquer prêt"
         │ PATCH /{slug}/commandes/{id}/statut
         │ Body: { statut: "pret" }
         │
         ▼
┌─────────────────┐
│      pret       │ ← Automatisme: heure_prete = now()
└────────┬────────┘
         │
         │ Livreur/Vendeur: "Livraison confirmée"
         │ PATCH /{slug}/commandes/{id}/statut
         │ Body: { statut: "termine" }
         │
         ▼
┌─────────────────┐
│    termine      │ ← DÉCLENCHEMENT PAIEMENT
└────────┬────────┘
         │
         │ SI mode_paiement = "mobile_money":
         │
         ├─► 1. Calcul Commission
         │     commission = montant_plats × 10%
         │
         ├─► 2. Calcul Montant Vendeur
         │     montant_vendeur = montant_total - commission
         │
         ├─► 3. Crédit Wallet
         │     vendeur.wallet_balance += montant_vendeur
         │
         ├─► 4. Enregistrement Transaction
         │     TransactionFinanciere::create([
         │       type: 'credit_vente',
         │       montant: montant_vendeur,
         │       notes: 'Total: X - Com 10%: Y'
         │     ])
         │
         ├─► 5. Sauvegarde Commission
         │     commande.frais_service = commission
         │
         └─► 6. Event Notification
               event(OrderStatusChanged)
                 └─► Notification client temps réel
```

---

## 4. FLUX DEMANDE PAYOUT

```
┌─────────────────────────────────────────────────────────────┐
│                  DEMANDE DE PAIEMENT                         │
└─────────────────────────────────────────────────────────────┘

Vendeur
  │
  ├─► GET /{slug}/payouts
  │     │
  │     └─► Affichage:
  │           - Solde wallet actuel
  │           - Historique demandes
  │           - Formulaire nouvelle demande
  │
  ├─► POST /{slug}/payouts
  │     │
  │     ├─► Validation
  │     │     ├─► Montant >= 5000 XOF ✓
  │     │     ├─► Montant <= wallet_balance ✓
  │     │     ├─► Méthode ∈ [momo, flooz, banque, cheque] ✓
  │     │     └─► Informations paiement présentes ✓
  │     │
  │     ├─► Création PayoutRequest
  │     │     └─► statut: "en_attente"
  │     │
  │     ├─► Déduction Wallet IMMÉDIATE
  │     │     └─► wallet_balance -= montant
  │     │
  │     └─► Redirection avec message succès
  │
  └─► Admin traite la demande
        │
        ├─► Approuve
        │     └─► statut: "approuve" → "paye"
        │           └─► Paiement effectué
        │
        └─► Refuse
              └─► statut: "refuse"
                    └─► TODO: Remboursement wallet?
```

---

## 5. FLUX GESTION ÉQUIPE

```
┌─────────────────────────────────────────────────────────────┐
│              AJOUT MEMBRE STAFF                              │
└─────────────────────────────────────────────────────────────┘

Propriétaire
  │
  ├─► GET /{slug}/team/create
  │     └─► Formulaire:
  │           - Nom
  │           - Email
  │           - Mot de passe
  │           - Rôle personnalisé
  │           - Permissions (checkboxes)
  │
  ├─► POST /{slug}/team
  │     │
  │     ├─► Validation
  │     │     ├─► Email unique ✓
  │     │     ├─► Password >= 8 caractères ✓
  │     │     └─► Nom requis ✓
  │     │
  │     ├─► 1. Création User
  │     │     User::create([
  │     │       name, email,
  │     │       password: Hash::make(),
  │     │       role: 'client',
  │     │       email_verified_at: now(),
  │     │       status: 'actif'
  │     │     ])
  │     │
  │     ├─► 2. Génération Token
  │     │     token = bin2hex(random_bytes(32))
  │     │     └─► 64 caractères hexadécimaux
  │     │
  │     ├─► 3. Création VendorStaff
  │     │     VendorStaff::create([
  │     │       id_vendeur,
  │     │       id_user,
  │     │       role_name,
  │     │       permissions: [],
  │     │       access_token: token
  │     │     ])
  │     │
  │     ├─► 4. Génération URL
  │     │     url = "/{slug}/staff-login?token={token}"
  │     │
  │     └─► 5. Redirection avec URL
  │           (Message: Lien de connexion: {url})
  │
  └─► Membre reçoit URL et se connecte

┌─────────────────────────────────────────────────────────────┐
│              CONNEXION STAFF                                 │
└─────────────────────────────────────────────────────────────┘

Staff
  │
  ├─► GET /{slug}/staff-login?token=xxx
  │     └─► Formulaire:
  │           - Email (input)
  │           - Password (input)
  │           - Token (hidden, pré-rempli)
  │
  ├─► POST /{slug}/staff-login
  │     │
  │     ├─► 1. Recherche Staff
  │     │     VendorStaff::where([
  │     │       'access_token' => token,
  │     │       'id_vendeur' => vendor.id
  │     │     ])
  │     │
  │     ├─► 2. Vérification Email
  │     │     staff.user.email === email ✓
  │     │
  │     ├─► 3. Tentative Auth
  │     │     Auth::attempt([
  │     │       'email' => email,
  │     │       'password' => password
  │     │     ])
  │     │
  │     ├─► 4. Logging
  │     │     LoginAttempt::create([
  │     │       status: 'success' | 'failed',
  │     │       failure_reason: ...
  │     │     ])
  │     │
  │     └─► 5. Redirection
  │           └─► /{slug}/dashboard
  │
  └─► Accès complet dashboard vendeur
```

---

## 6. FLUX MISE À JOUR PARAMÈTRES

```
┌─────────────────────────────────────────────────────────────┐
│              PARAMÈTRES BOUTIQUE                             │
└─────────────────────────────────────────────────────────────┘

┌──────────────────┐
│ 1. PROFIL        │
├──────────────────┤
│ POST /parametres/profil
│   │
│   ├─► Validation
│   ├─► Upload image (si présente)
│   │     └─► ImageHelper::uploadAndConvert()
│   ├─► Update vendeur
│   └─► Message succès
└──────────────────┘

┌──────────────────┐
│ 2. HORAIRES      │
├──────────────────┤
│ POST /parametres/horaires
│   │
│   ├─► Validation format H:i
│   │
│   ├─► Pour chaque jour (0-6):
│   │     VendeurHoraire::updateOrCreate([
│   │       jour_semaine,
│   │       heure_ouverture,
│   │       heure_fermeture,
│   │       ferme
│   │     ])
│   │
│   ├─► Synchronisation JSON
│   │     vendeur.horaires_ouverture = [...]
│   │
│   └─► Message succès
└──────────────────┘

┌──────────────────┐
│ 3. SPÉCIALITÉS   │
├──────────────────┤
│ POST /parametres/categories
│   │
│   ├─► Validation catégories existantes
│   │
│   ├─► Si nouvelle spécialité:
│   │     CategoryPlat::firstOrCreate()
│   │
│   ├─► Synchronisation pivot
│   │     vendeur.categories().sync([...])
│   │
│   └─► Message succès
└──────────────────┘

┌──────────────────┐
│ 4. TOGGLE STATUS │
├──────────────────┤
│ POST /parametres/toggle-status
│   │
│   ├─► Toggle actif
│   │     vendeur.actif = !vendeur.actif
│   │
│   └─► Message: "Boutique ouverte/fermée"
└──────────────────┘
```

---

## 7. FLUX CRÉATION COUPON

```
┌─────────────────────────────────────────────────────────────┐
│                  CRÉATION COUPON                             │
└─────────────────────────────────────────────────────────────┘

Vendeur
  │
  ├─► GET /{slug}/coupons
  │     └─► Liste coupons existants
  │         + Formulaire création
  │
  ├─► POST /{slug}/coupons
  │     │
  │     ├─► Validation
  │     │     ├─► Code unique ✓
  │     │     ├─► Type ∈ [percentage, fixed] ✓
  │     │     ├─► Valeur > 0 ✓
  │     │     ├─► Montant minimal >= 0 ✓
  │     │     ├─► Limite >= 1 (optionnel) ✓
  │     │     └─► Expiration > today (optionnel) ✓
  │     │
  │     ├─► Création Coupon
  │     │     Coupon::create([
  │     │       id_vendeur,
  │     │       code: strtoupper(code),
  │     │       type,
  │     │       valeur,
  │     │       montant_minimal_achat,
  │     │       limite_utilisation,
  │     │       expire_at,
  │     │       actif: true
  │     │     ])
  │     │
  │     └─► Redirection avec succès
  │
  └─► Coupon disponible pour clients
```

---

## 8. SÉCURITÉ & MIDDLEWARES

```
┌─────────────────────────────────────────────────────────────┐
│              PIPELINE DE SÉCURITÉ                            │
└─────────────────────────────────────────────────────────────┘

Requête: GET /{vendor_slug}/dashboard
  │
  ├─► Middleware: auth
  │     │
  │     ├─► User authentifié ? ✓
  │     │     │
  │     │     ├─► OUI → Continue
  │     │     └─► NON → Redirect /login
  │     │
  │     └─► Continue
  │
  ├─► Middleware: IdentifyVendorBySlug
  │     │
  │     ├─► Recherche Vendeur
  │     │     Vendeur::where('slug', vendor_slug)
  │     │
  │     ├─► Vendeur trouvé ? ✓
  │     │     │
  │     │     ├─► OUI → Injection dans request
  │     │     │         $request->merge(['current_vendor' => $vendeur])
  │     │     │
  │     │     └─► NON → 404 Not Found
  │     │
  │     ├─► Vérification Propriétaire
  │     │     │
  │     │     ├─► User.vendeur.id === Vendeur.id ? ✓
  │     │     │     │
  │     │     │     ├─► OUI → Accès autorisé
  │     │     │     │
  │     │     │     └─► NON → Vérifier Staff
  │     │     │           │
  │     │     │           └─► VendorStaff existe ? ✓
  │     │     │                 │
  │     │     │                 ├─► OUI → Accès autorisé
  │     │     │                 └─► NON → 403 Forbidden
  │     │     │
  │     │     └─► Continue
  │     │
  │     └─► Continue
  │
  ├─► Contrôleur: VendorDashboardController@index
  │     │
  │     ├─► Récupération vendeur
  │     │     $vendeur = Auth::user()->vendeur
  │     │     OU
  │     │     $vendeur = $request->get('current_vendor')
  │     │
  │     ├─► Vérification finale
  │     │     if (!$vendeur) redirect()->home()
  │     │
  │     ├─► Calculs statistiques
  │     │
  │     └─► Retour vue
  │
  └─► Réponse: 200 OK
```

---

## 9. SYSTÈME DE COMMISSIONS

```
┌─────────────────────────────────────────────────────────────┐
│              CALCUL AUTOMATIQUE FINANCES                     │
└─────────────────────────────────────────────────────────────┘

Commande terminée (mobile_money)
  │
  ├─► Données commande:
  │     - montant_plats: 10,000 XOF
  │     - frais_livraison: 1,000 XOF
  │     - montant_total: 11,000 XOF
  │
  ├─► Calcul Commission (10% sur plats uniquement)
  │     commission = 10,000 × 0.10 = 1,000 XOF
  │
  ├─► Calcul Montant Vendeur
  │     montant_vendeur = 11,000 - 1,000 = 10,000 XOF
  │     (Vendeur récupère: plats - commission + livraison)
  │
  ├─► Crédit Wallet
  │     vendeur.wallet_balance += 10,000 XOF
  │
  ├─► Enregistrement Commission
  │     commande.frais_service = 1,000 XOF
  │
  └─► Transaction Financière
        TransactionFinanciere::create([
          id_commande,
          id_vendeur,
          type: 'credit_vente',
          montant: 10,000,
          devise: 'XOF',
          statut: 'succes',
          reference: 'WALLET-CMD123',
          notes: 'Crédit vente (Total: 11000 - Com 10%: 1000)'
        ])

Résultat:
  ✓ Plateforme: +1,000 XOF (commission)
  ✓ Vendeur: +10,000 XOF (wallet)
  ✓ Total: 11,000 XOF ✓
```

---

## 10. RELATIONS BASE DE DONNÉES

```
┌─────────────────────────────────────────────────────────────┐
│              SCHÉMA RELATIONNEL VENDEUR                      │
└─────────────────────────────────────────────────────────────┘

                    ┌──────────┐
                    │   User   │
                    └────┬─────┘
                         │ 1
                         │
                         │ 1
                    ┌────▼─────┐
          ┌─────────┤  Vendeur │─────────┐
          │         └────┬─────┘         │
          │              │               │
          │ N            │ N             │ N
    ┌─────▼─────┐  ┌────▼────┐    ┌─────▼─────┐
    │   Plat    │  │Commande │    │VendorStaff│
    └─────┬─────┘  └────┬────┘    └─────┬─────┘
          │             │               │
          │ N           │ N             │ 1
    ┌─────▼─────┐  ┌────▼────┐    ┌─────▼─────┐
    │  Groupe   │  │  Ligne  │    │   User    │
    │ Variante  │  │Commande │    │  (Staff)  │
    └─────┬─────┘  └─────────┘    └───────────┘
          │
          │ N
    ┌─────▼─────┐
    │ Variante  │
    │ (Option)  │
    └───────────┘

Autres relations:
  Vendeur 1─N VendeurHoraire
  Vendeur 1─N VendeurContact
  Vendeur N─N CategoryPlat (via vendeur_categories)
  Vendeur 1─N PayoutRequest
  Vendeur 1─N Coupon
  Vendeur 1─N TransactionFinanciere
  Vendeur 1─1 VendorCategory
  Vendeur 1─1 ZoneGeographique
```

---

**Document créé le:** 24 Janvier 2026  
**Version:** 1.0
