# 📖 GUIDE DE VOCABULAIRE - CABAACABAA

## 🎯 POSITIONNEMENT DE LA PLATEFORME

**CabaaCabaa** n'est PAS seulement une plateforme de livraison de nourriture.

### C'est quoi exactement ?

**Une marketplace locale multi-vendeurs** qui regroupe:
- 🍕 Restaurants et cuisiniers
- 🛒 Boutiques et commerces
- 🏪 Épiceries et supérettes
- 👔 Services divers
- 📦 Avec options: Livraison, Emporter, Sur place

---

## 🔄 VOCABULAIRE À CHANGER

### ❌ À ÉVITER (trop restrictif)

| Ancien terme | Problème |
|--------------|----------|
| "Plateforme de livraison" | Trop restrictif, on fait aussi emporter/sur place |
| "Food delivery" | Limite à la nourriture seulement |
| "Livraison de nourriture" | Exclut les boutiques et autres services |
| "Restaurant" uniquement | On a aussi des boutiques, épiceries, etc. |

### ✅ À UTILISER (inclusif)

| Nouveau terme | Usage |
|---------------|-------|
| **Marketplace locale** | Description générale de la plateforme |
| **Plateforme multi-vendeurs** | Aspect technique |
| **Vendeur** | Terme générique pour tous (restaurants, boutiques, etc.) |
| **Commerce** | Alternative à "vendeur" |
| **Produit** | Au lieu de "plat" (plus générique) |
| **Article** | Alternative à "produit" |
| **Commande** | OK, reste universel |
| **Service** | Livraison, emporter, sur place |

---

## 📝 TEXTES À METTRE À JOUR

### 1. Meta Description (.env)

#### ❌ Ancien (trop restrictif)
```env
META_DESCRIPTION="Découvrez les meilleurs restaurants et épiceries près de chez vous. Livraison rapide et fiable."
```

#### ✅ Nouveau (inclusif)
```env
META_DESCRIPTION="Découvrez les meilleurs commerces près de chez vous : restaurants, boutiques, épiceries et plus. Livraison, emporter ou sur place."
```

### 2. Meta Keywords (.env)

#### ❌ Ancien
```env
META_KEYWORDS="food, livraison, restaurant, épicerie, repas, rapide"
```

#### ✅ Nouveau
```env
META_KEYWORDS="marketplace, commerce local, restaurant, boutique, épicerie, livraison, emporter, sur place, Togo, Bénin"
```

### 3. Meta Title (.env)

#### ❌ Ancien
```env
META_TITLE="CabaaCabaa - Votre plateforme de livraison préférée"
```

#### ✅ Nouveau (Options)
```env
# Option 1: Focus marketplace
META_TITLE="CabaaCabaa - Marketplace locale de commerces et services"

# Option 2: Focus proximité
META_TITLE="CabaaCabaa - Tous vos commerces locaux en un clic"

# Option 3: Focus diversité
META_TITLE="CabaaCabaa - Restaurants, boutiques et services près de chez vous"

# Option 4: Simple et efficace
META_TITLE="CabaaCabaa - Votre marketplace locale"
```

---

## 🎨 TEXTES D'INTERFACE À REVOIR

### Page d'accueil

#### ❌ Ancien
```
"Découvrez les meilleurs restaurants près de chez vous"
"Commandez vos plats préférés"
"Livraison rapide de nourriture"
```

#### ✅ Nouveau
```
"Découvrez tous les commerces près de chez vous"
"Commandez vos produits préférés"
"Livraison, emporter ou sur place - Vous choisissez !"
```

### Catégories de Vendeurs

Au lieu de seulement "Restaurant", avoir:
- 🍕 **Restaurants & Cuisines**
- 🛒 **Boutiques & Commerces**
- 🏪 **Épiceries & Supérettes**
- ☕ **Cafés & Pâtisseries**
- 🎁 **Services & Divers**

### Nomenclature Base de Données

| Table actuelle | Nom générique | Suggestion |
|----------------|---------------|------------|
| `plats` | ✅ Peut rester | Ou renommer en `produits` |
| `category_plats` | ⚠️ Trop spécifique | Renommer en `product_categories` |
| `vendeurs` | ✅ Parfait | Générique, garde tel quel |
| `vendor_categories` | ✅ Parfait | Permet de typer les vendeurs |

---

## 🔧 FICHIERS À MODIFIER

### 1. Fichier .env

```env
# ============================================
# META SEO
# ============================================
META_TITLE="CabaaCabaa - Votre marketplace locale"
META_DESCRIPTION="Découvrez les meilleurs commerces près de chez vous : restaurants, boutiques, épiceries et plus. Livraison, emporter ou sur place."
META_KEYWORDS="marketplace, commerce local, restaurant, boutique, épicerie, livraison, emporter, sur place, Togo, Bénin, Afrique"

# ============================================
# INFORMATIONS DU SITE
# ============================================
SITE_NAME=CabaaCabaa
SITE_TAGLINE="Votre marketplace locale"
SITE_DESCRIPTION="Plateforme multi-vendeurs regroupant restaurants, boutiques et commerces locaux"
```

### 2. Vues Blade à modifier

**Fichiers prioritaires**:
- `resources/views/home.blade.php` - Page d'accueil
- `resources/views/explore.blade.php` - Page exploration
- `resources/views/layouts/app.blade.php` - Layout principal
- `resources/views/welcome.blade.php` - Page de bienvenue

**Textes à chercher et remplacer**:
```
"livraison de nourriture" → "marketplace locale"
"plateforme de livraison" → "plateforme multi-vendeurs"
"restaurants et épiceries" → "commerces locaux"
"plats" → "produits" (contexte général)
"menu" → "catalogue" (pour les boutiques)
```

### 3. Seeders à modifier

**`database/seeders/AppSettingSeeder.php`**:

```php
// Ligne 36 - Nom du site
['key' => 'site_name', 'value' => 'CabaaCabaa', 'label' => 'Nom du site', 'group' => 'general', 'type' => 'text'],

// Ligne 48-50 - Meta tags
['key' => 'meta_title', 'value' => 'CabaaCabaa - Votre marketplace locale', 'label' => 'Titre Meta (SEO)', 'group' => 'seo', 'type' => 'text'],
['key' => 'meta_description', 'value' => 'Découvrez les meilleurs commerces près de chez vous : restaurants, boutiques, épiceries et plus. Livraison, emporter ou sur place.', 'label' => 'Description Meta (SEO)', 'group' => 'seo', 'type' => 'textarea'],
['key' => 'meta_keywords', 'value' => 'marketplace, commerce local, restaurant, boutique, épicerie, livraison, emporter, sur place', 'label' => 'Mots-clés Meta', 'group' => 'seo', 'type' => 'text'],
```

---

## 🎯 CATÉGORIES DE VENDEURS SUGGÉRÉES

### Table `vendor_categories`

```php
// Exemples de catégories
[
    ['name' => 'Restaurant', 'icon' => '🍕', 'slug' => 'restaurant'],
    ['name' => 'Boutique', 'icon' => '🛒', 'slug' => 'boutique'],
    ['name' => 'Épicerie', 'icon' => '🏪', 'slug' => 'epicerie'],
    ['name' => 'Boulangerie', 'icon' => '🥖', 'slug' => 'boulangerie'],
    ['name' => 'Pâtisserie', 'icon' => '🍰', 'slug' => 'patisserie'],
    ['name' => 'Café', 'icon' => '☕', 'slug' => 'cafe'],
    ['name' => 'Fast Food', 'icon' => '🍔', 'slug' => 'fast-food'],
    ['name' => 'Supermarché', 'icon' => '🛍️', 'slug' => 'supermarche'],
    ['name' => 'Pharmacie', 'icon' => '💊', 'slug' => 'pharmacie'],
    ['name' => 'Autre', 'icon' => '🏬', 'slug' => 'autre'],
]
```

---

## 📊 EXEMPLES DE TEXTES PAR CONTEXTE

### Pour un Restaurant
```
"Découvrez notre menu"
"Commandez vos plats préférés"
"Livraison, emporter ou sur place"
```

### Pour une Boutique
```
"Découvrez notre catalogue"
"Commandez vos produits préférés"
"Livraison ou retrait en magasin"
```

### Pour une Épicerie
```
"Découvrez nos produits"
"Faites vos courses en ligne"
"Livraison à domicile ou retrait"
```

### Texte Générique (pour tous)
```
"Découvrez notre sélection"
"Commandez en ligne"
"Livraison, emporter ou sur place"
```

---

## 🔄 PLAN DE MIGRATION DES TEXTES

### Phase 1: Urgent (SEO & Branding)
- [ ] Mettre à jour `.env` (META_TITLE, META_DESCRIPTION, META_KEYWORDS)
- [ ] Mettre à jour `AppSettingSeeder.php`
- [ ] Mettre à jour page d'accueil (`home.blade.php`)

### Phase 2: Important (Interface)
- [ ] Mettre à jour `explore.blade.php`
- [ ] Mettre à jour `layouts/app.blade.php`
- [ ] Mettre à jour `welcome.blade.php`

### Phase 3: Progressif (Détails)
- [ ] Revoir tous les textes d'emails
- [ ] Revoir les messages de notification
- [ ] Revoir les textes d'aide/FAQ

---

## 💡 SUGGESTIONS DE SLOGANS

Pour la page d'accueil et le marketing:

1. **"Tous vos commerces locaux, une seule plateforme"**
2. **"Restaurants, boutiques et plus près de chez vous"**
3. **"Votre marketplace locale de confiance"**
4. **"Commandez local, recevez rapidement"**
5. **"Du restaurant à la boutique, tout en un clic"**

---

## 🎨 TONALITÉ & STYLE

### Valeurs à communiquer
- ✅ **Diversité** - Tous types de commerces
- ✅ **Proximité** - Local, près de chez vous
- ✅ **Flexibilité** - Livraison, emporter, sur place
- ✅ **Simplicité** - Facile à utiliser
- ✅ **Confiance** - Sécurisé et fiable

### Mots-clés à utiliser
- Marketplace
- Commerce local
- Proximité
- Diversité
- Flexibilité
- Choix
- Rapidité
- Confiance

---

## 📝 CHECKLIST DE MISE À JOUR

### Textes SEO
- [ ] META_TITLE
- [ ] META_DESCRIPTION
- [ ] META_KEYWORDS
- [ ] SITE_TAGLINE (nouveau)

### Interface Utilisateur
- [ ] Page d'accueil
- [ ] Page exploration
- [ ] Footer
- [ ] Header/Menu
- [ ] Page À propos

### Base de Données
- [ ] AppSettingSeeder
- [ ] VendorCategorySeeder (si existe)
- [ ] Textes d'emails

### Documentation
- [ ] README.md
- [ ] Guide utilisateur
- [ ] Guide vendeur

---

**Voulez-vous que je commence à mettre à jour ces textes maintenant ?**

Je peux:
1. ✅ Mettre à jour le fichier `.env`
2. ✅ Mettre à jour `AppSettingSeeder.php`
3. ✅ Créer un seeder pour les catégories de vendeurs
4. ✅ Mettre à jour les vues principales

Qu'en pensez-vous ? 😊
