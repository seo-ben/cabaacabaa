# 🔌 API & EXEMPLES D'UTILISATION - ESPACE VENDEUR

## 📋 TABLE DES MATIÈRES

1. [Routes Complètes](#1-routes-complètes)
2. [Exemples cURL](#2-exemples-curl)
3. [Exemples JavaScript/Fetch](#3-exemples-javascriptfetch)
4. [Codes de Réponse](#4-codes-de-réponse)
5. [Erreurs Courantes](#5-erreurs-courantes)

---

## 1. ROUTES COMPLÈTES

### 1.1 Dashboard
```http
GET /{vendor_slug}/dashboard
```

### 1.2 Produits (Plats)
```http
GET    /{vendor_slug}/plats
GET    /{vendor_slug}/plats/creer
POST   /{vendor_slug}/plats
GET    /{vendor_slug}/plats/{id}/modifier
PUT    /{vendor_slug}/plats/{id}
DELETE /{vendor_slug}/plats/{id}
```

### 1.3 Commandes
```http
GET   /{vendor_slug}/commandes
GET   /{vendor_slug}/commandes?status=en_attente
GET   /{vendor_slug}/commandes?search=CMD123
PATCH /{vendor_slug}/commandes/{id}/statut
```

### 1.4 Paramètres
```http
GET  /{vendor_slug}/parametres
POST /{vendor_slug}/parametres/profil
POST /{vendor_slug}/parametres/horaires
POST /{vendor_slug}/parametres/categories
POST /{vendor_slug}/parametres/toggle-status
```

### 1.5 Finances
```http
GET  /{vendor_slug}/payouts
POST /{vendor_slug}/payouts
```

### 1.6 Coupons
```http
GET    /{vendor_slug}/coupons
POST   /{vendor_slug}/coupons
PATCH  /{vendor_slug}/coupons/{coupon}/toggle
DELETE /{vendor_slug}/coupons/{coupon}
```

### 1.7 Équipe
```http
GET    /{vendor_slug}/team
GET    /{vendor_slug}/team/create
POST   /{vendor_slug}/team
DELETE /{vendor_slug}/team/{id}
```

### 1.8 Authentification Staff
```http
GET  /{vendor_slug}/staff-login
POST /{vendor_slug}/staff-login
```

### 1.9 Chat Commande (API)
```http
GET  /api/orders/{orderId}/messages
POST /api/orders/{orderId}/messages
GET  /api/orders/{orderId}/messages/unread
```

---

## 2. EXEMPLES cURL

### 2.1 Créer un Produit

```bash
curl -X POST "https://example.com/pizza-hut/plats" \
  -H "Content-Type: multipart/form-data" \
  -H "X-CSRF-TOKEN: {csrf_token}" \
  -H "Cookie: laravel_session={session}" \
  -F "nom_plat=Pizza Margherita" \
  -F "id_categorie=1" \
  -F "description=Pizza classique avec tomate et mozzarella" \
  -F "prix=5000" \
  -F "image=@/path/to/pizza.jpg"
```

**Réponse Succès:**
```
HTTP/1.1 302 Found
Location: /pizza-hut/plats
Set-Cookie: laravel_session=...

Session Flash: "Plat ajouté avec succès !"
```

---

### 2.2 Créer un Produit avec Variantes

```bash
curl -X POST "https://example.com/pizza-hut/plats" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "X-CSRF-TOKEN: {csrf_token}" \
  -d "nom_plat=Pizza Margherita" \
  -d "id_categorie=1" \
  -d "prix=5000" \
  -d "variants[0][groupe_nom]=Taille" \
  -d "variants[0][obligatoire]=1" \
  -d "variants[0][choix_multiple]=0" \
  -d "variants[0][min_choix]=1" \
  -d "variants[0][max_choix]=1" \
  -d "variants[0][options][0][nom]=Petite" \
  -d "variants[0][options][0][prix]=0" \
  -d "variants[0][options][1][nom]=Moyenne" \
  -d "variants[0][options][1][prix]=1000" \
  -d "variants[0][options][2][nom]=Grande" \
  -d "variants[0][options][2][prix]=2000"
```

---

### 2.3 Mettre à Jour Statut Commande

```bash
curl -X PATCH "https://example.com/pizza-hut/commandes/123/statut" \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {csrf_token}" \
  -H "Cookie: laravel_session={session}" \
  -d '{
    "statut": "en_preparation"
  }'
```

**Réponse Succès:**
```json
{
  "message": "Statut de la commande mis à jour !",
  "redirect": "/pizza-hut/commandes"
}
```

---

### 2.4 Créer Demande Payout

```bash
curl -X POST "https://example.com/pizza-hut/payouts" \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {csrf_token}" \
  -d '{
    "montant": 50000,
    "methode_paiement": "momo",
    "informations_paiement": "Numéro: +225 07 XX XX XX XX, Nom: Koffi Ablo"
  }'
```

**Réponse Succès:**
```
HTTP/1.1 302 Found
Session Flash: "Votre demande de paiement a été soumise."
```

**Réponse Erreur (Solde insuffisant):**
```
HTTP/1.1 302 Found
Session Flash Error: "Solde insuffisant pour cette demande."
```

---

### 2.5 Créer Coupon

```bash
curl -X POST "https://example.com/pizza-hut/coupons" \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {csrf_token}" \
  -d '{
    "code": "PROMO2026",
    "type": "percentage",
    "valeur": 15,
    "montant_minimal_achat": 10000,
    "limite_utilisation": 100,
    "expire_at": "2026-02-28"
  }'
```

**Réponse Succès:**
```
HTTP/1.1 302 Found
Session Flash: "Coupon créé avec succès !"
```

---

### 2.6 Ajouter Membre Staff

```bash
curl -X POST "https://example.com/pizza-hut/team" \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {csrf_token}" \
  -d '{
    "name": "Marie Kouassi",
    "email": "marie@example.com",
    "password": "SecurePass123!",
    "role_name": "Cuisinière",
    "permissions": ["manage_products", "view_orders"]
  }'
```

**Réponse Succès:**
```
HTTP/1.1 302 Found
Session Flash: "Membre ajouté avec succès. Lien de connexion : https://example.com/pizza-hut/staff-login?token=abc123..."
```

---

### 2.7 Connexion Staff

```bash
curl -X POST "https://example.com/pizza-hut/staff-login" \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {csrf_token}" \
  -d '{
    "email": "marie@example.com",
    "password": "SecurePass123!",
    "token": "abc123def456..."
  }'
```

**Réponse Succès:**
```
HTTP/1.1 302 Found
Location: /pizza-hut/dashboard
Set-Cookie: laravel_session=...
```

---

### 2.8 Envoyer Message Chat Commande

```bash
curl -X POST "https://example.com/api/orders/123/messages" \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {csrf_token}" \
  -d '{
    "message": "Votre commande est prête !"
  }'
```

**Réponse Succès:**
```json
{
  "success": true,
  "message": {
    "id": 456,
    "id_commande": 123,
    "id_user": 789,
    "message": "Votre commande est prête !",
    "is_read": false,
    "created_at": "2026-01-24T03:45:00.000000Z"
  }
}
```

---

### 2.9 Récupérer Messages Chat

```bash
curl -X GET "https://example.com/api/orders/123/messages" \
  -H "Cookie: laravel_session={session}"
```

**Réponse:**
```json
{
  "messages": [
    {
      "id": 1,
      "id_user": 789,
      "message": "Bonjour, ma commande arrive quand ?",
      "is_read": true,
      "created_at": "2026-01-24T03:30:00.000000Z",
      "user": {
        "name": "Client Dupont"
      }
    },
    {
      "id": 2,
      "id_user": 456,
      "message": "Votre commande est prête !",
      "is_read": false,
      "created_at": "2026-01-24T03:45:00.000000Z",
      "user": {
        "name": "Pizza Hut"
      }
    }
  ]
}
```

---

## 3. EXEMPLES JavaScript/Fetch

### 3.1 Mettre à Jour Statut Commande

```javascript
async function updateOrderStatus(vendorSlug, orderId, newStatus) {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  
  try {
    const response = await fetch(`/${vendorSlug}/commandes/${orderId}/statut`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        statut: newStatus
      })
    });
    
    if (response.ok) {
      const data = await response.json();
      console.log('Statut mis à jour:', data);
      // Recharger la page ou mettre à jour l'UI
      window.location.reload();
    } else {
      console.error('Erreur:', response.statusText);
    }
  } catch (error) {
    console.error('Erreur réseau:', error);
  }
}

// Utilisation
updateOrderStatus('pizza-hut', 123, 'en_preparation');
```

---

### 3.2 Créer Coupon

```javascript
async function createCoupon(vendorSlug, couponData) {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  
  try {
    const response = await fetch(`/${vendorSlug}/coupons`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify(couponData)
    });
    
    if (response.ok) {
      alert('Coupon créé avec succès !');
      window.location.reload();
    } else {
      const errors = await response.json();
      console.error('Erreurs de validation:', errors);
    }
  } catch (error) {
    console.error('Erreur:', error);
  }
}

// Utilisation
createCoupon('pizza-hut', {
  code: 'PROMO2026',
  type: 'percentage',
  valeur: 15,
  montant_minimal_achat: 10000,
  limite_utilisation: 100,
  expire_at: '2026-02-28'
});
```

---

### 3.3 Upload Image Produit

```javascript
async function uploadProductImage(vendorSlug, formData) {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  
  try {
    const response = await fetch(`/${vendorSlug}/plats`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: formData // FormData avec fichier
    });
    
    if (response.ok) {
      alert('Produit créé avec succès !');
      window.location.href = `/${vendorSlug}/plats`;
    } else {
      const errors = await response.json();
      console.error('Erreurs:', errors);
    }
  } catch (error) {
    console.error('Erreur:', error);
  }
}

// Utilisation avec formulaire
const form = document.getElementById('product-form');
form.addEventListener('submit', async (e) => {
  e.preventDefault();
  const formData = new FormData(form);
  await uploadProductImage('pizza-hut', formData);
});
```

---

### 3.4 Chat Temps Réel

```javascript
class OrderChat {
  constructor(orderId) {
    this.orderId = orderId;
    this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  }
  
  async loadMessages() {
    try {
      const response = await fetch(`/api/orders/${this.orderId}/messages`);
      const data = await response.json();
      return data.messages;
    } catch (error) {
      console.error('Erreur chargement messages:', error);
      return [];
    }
  }
  
  async sendMessage(message) {
    try {
      const response = await fetch(`/api/orders/${this.orderId}/messages`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': this.csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ message })
      });
      
      if (response.ok) {
        const data = await response.json();
        return data.message;
      }
    } catch (error) {
      console.error('Erreur envoi message:', error);
    }
  }
  
  async getUnreadCount() {
    try {
      const response = await fetch(`/api/orders/${this.orderId}/messages/unread`);
      const data = await response.json();
      return data.unread_count;
    } catch (error) {
      console.error('Erreur compteur:', error);
      return 0;
    }
  }
  
  // Polling toutes les 5 secondes
  startPolling(callback) {
    this.pollingInterval = setInterval(async () => {
      const messages = await this.loadMessages();
      callback(messages);
    }, 5000);
  }
  
  stopPolling() {
    if (this.pollingInterval) {
      clearInterval(this.pollingInterval);
    }
  }
}

// Utilisation
const chat = new OrderChat(123);

// Charger messages initiaux
chat.loadMessages().then(messages => {
  console.log('Messages:', messages);
});

// Envoyer message
chat.sendMessage('Votre commande est prête !');

// Polling temps réel
chat.startPolling((messages) => {
  // Mettre à jour l'UI avec nouveaux messages
  updateChatUI(messages);
});
```

---

### 3.5 Toggle Disponibilité Produit

```javascript
async function toggleProductAvailability(vendorSlug, productId, isAvailable) {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  
  try {
    const response = await fetch(`/${vendorSlug}/plats/${productId}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        disponible: isAvailable
      })
    });
    
    if (response.ok) {
      console.log('Disponibilité mise à jour');
    }
  } catch (error) {
    console.error('Erreur:', error);
  }
}

// Utilisation avec toggle switch
document.querySelectorAll('.product-toggle').forEach(toggle => {
  toggle.addEventListener('change', (e) => {
    const productId = e.target.dataset.productId;
    const isAvailable = e.target.checked;
    toggleProductAvailability('pizza-hut', productId, isAvailable);
  });
});
```

---

## 4. CODES DE RÉPONSE

### 4.1 Succès
| Code | Signification | Utilisation |
|------|---------------|-------------|
| `200 OK` | Succès | GET requests |
| `201 Created` | Ressource créée | POST création |
| `302 Found` | Redirection | Après POST/PUT/DELETE |

### 4.2 Erreurs Client
| Code | Signification | Cause |
|------|---------------|-------|
| `400 Bad Request` | Requête invalide | Données malformées |
| `401 Unauthorized` | Non authentifié | Session expirée |
| `403 Forbidden` | Accès refusé | Pas propriétaire |
| `404 Not Found` | Ressource introuvable | ID invalide |
| `422 Unprocessable Entity` | Validation échouée | Champs invalides |

### 4.3 Erreurs Serveur
| Code | Signification | Action |
|------|---------------|--------|
| `500 Internal Server Error` | Erreur serveur | Vérifier logs |
| `503 Service Unavailable` | Service indisponible | Réessayer plus tard |

---

## 5. ERREURS COURANTES

### 5.1 Validation Produit

**Erreur:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "id_categorie": [
      "Cette catégorie ne fait pas partie de vos spécialités enregistrées."
    ]
  }
}
```

**Solution:** Sélectionner une catégorie parmi les spécialités du vendeur.

---

### 5.2 Payout Solde Insuffisant

**Erreur:**
```
Session Flash Error: "Solde insuffisant pour cette demande."
```

**Solution:** Vérifier `wallet_balance` avant de soumettre.

---

### 5.3 Token Staff Invalide

**Erreur:**
```json
{
  "errors": {
    "token": ["Lien d'accès invalide ou expiré."]
  }
}
```

**Solution:** Utiliser le lien fourni lors de la création du membre.

---

### 5.4 CSRF Token Manquant

**Erreur:**
```
419 Page Expired
```

**Solution:** Inclure le token CSRF dans toutes les requêtes POST/PUT/PATCH/DELETE.

```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

```javascript
headers: {
  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
}
```

---

### 5.5 Catégorie Vendeur Déjà Définie

**Erreur:**
```json
{
  "errors": {
    "id_category_vendeur": [
      "La catégorie vendeur ne peut pas être modifiée après sélection."
    ]
  }
}
```

**Solution:** La catégorie vendeur (Restaurant, Fast-Food, etc.) est définitive.

---

### 5.6 Email Staff Déjà Utilisé

**Erreur:**
```json
{
  "errors": {
    "email": [
      "The email has already been taken."
    ]
  }
}
```

**Solution:** Utiliser un email unique pour chaque membre.

---

## 6. BONNES PRATIQUES

### 6.1 Gestion des Erreurs

```javascript
async function apiCall(url, options) {
  try {
    const response = await fetch(url, options);
    
    // Vérifier le statut
    if (!response.ok) {
      if (response.status === 422) {
        // Erreurs de validation
        const errors = await response.json();
        displayValidationErrors(errors.errors);
      } else if (response.status === 401) {
        // Non authentifié
        window.location.href = '/login';
      } else if (response.status === 403) {
        // Accès refusé
        alert('Vous n\'avez pas les permissions nécessaires.');
      } else {
        // Autre erreur
        alert('Une erreur est survenue.');
      }
      return null;
    }
    
    return await response.json();
  } catch (error) {
    console.error('Erreur réseau:', error);
    alert('Erreur de connexion au serveur.');
    return null;
  }
}
```

---

### 6.2 Debouncing Recherche

```javascript
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Utilisation pour recherche commandes
const searchInput = document.getElementById('search-orders');
const debouncedSearch = debounce(async (query) => {
  const url = `/pizza-hut/commandes?search=${encodeURIComponent(query)}`;
  window.location.href = url;
}, 500);

searchInput.addEventListener('input', (e) => {
  debouncedSearch(e.target.value);
});
```

---

### 6.3 Confirmation Suppression

```javascript
function confirmDelete(vendorSlug, itemType, itemId) {
  if (confirm(`Êtes-vous sûr de vouloir supprimer cet élément ?`)) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    fetch(`/${vendorSlug}/${itemType}/${itemId}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      }
    })
    .then(response => {
      if (response.ok) {
        window.location.reload();
      }
    });
  }
}

// Utilisation
// <button onclick="confirmDelete('pizza-hut', 'plats', 123)">Supprimer</button>
```

---

### 6.4 Upload avec Prévisualisation

```javascript
function previewImage(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    
    reader.onload = function(e) {
      const preview = document.getElementById('image-preview');
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    
    reader.readAsDataURL(input.files[0]);
  }
}

// HTML
// <input type="file" name="image" onchange="previewImage(this)">
// <img id="image-preview" style="display:none; max-width: 200px;">
```

---

## 7. WEBHOOKS & ÉVÉNEMENTS

### 7.1 Événements Laravel Disponibles

```php
// Événement déclenché lors du changement de statut
event(new OrderStatusChanged($order));
```

**Écouter l'événement (JavaScript):**

```javascript
// Avec Laravel Echo (WebSocket)
Echo.channel('orders')
  .listen('OrderStatusChanged', (e) => {
    console.log('Commande mise à jour:', e.order);
    // Mettre à jour l'UI en temps réel
    updateOrderUI(e.order);
  });
```

---

## 8. LIMITES & QUOTAS

### 8.1 Limites Upload
- **Image produit:** Max 2 MB
- **Format:** JPG, PNG, WebP
- **Conversion:** Automatique en WebP

### 8.2 Limites Texte
- **Nom produit:** Max 100 caractères
- **Description:** Illimité
- **Code coupon:** Max 20 caractères
- **Informations payout:** Max 500 caractères

### 8.3 Limites Financières
- **Payout minimum:** 5,000 XOF
- **Commission:** 10% sur montant_plats

---

## 9. SÉCURITÉ API

### 9.1 CSRF Protection

**Toutes les requêtes POST/PUT/PATCH/DELETE doivent inclure:**

```javascript
headers: {
  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
}
```

### 9.2 Rate Limiting

Laravel applique automatiquement un rate limiting:
- **Web routes:** 60 requêtes/minute
- **API routes:** 60 requêtes/minute

**Dépassement:**
```
HTTP/1.1 429 Too Many Requests
Retry-After: 60
```

---

## 10. TESTING

### 10.1 Test Création Produit

```bash
# Test avec données valides
curl -X POST "http://localhost/pizza-hut/plats" \
  -H "X-CSRF-TOKEN: test-token" \
  -F "nom_plat=Test Pizza" \
  -F "id_categorie=1" \
  -F "prix=5000"

# Test avec catégorie invalide
curl -X POST "http://localhost/pizza-hut/plats" \
  -F "nom_plat=Test Pizza" \
  -F "id_categorie=999" \
  -F "prix=5000"
# Attendu: 422 Unprocessable Entity
```

### 10.2 Test Payout

```bash
# Test avec solde suffisant
curl -X POST "http://localhost/pizza-hut/payouts" \
  -H "Content-Type: application/json" \
  -d '{
    "montant": 10000,
    "methode_paiement": "momo",
    "informations_paiement": "Test"
  }'

# Test avec montant trop faible
curl -X POST "http://localhost/pizza-hut/payouts" \
  -d '{"montant": 1000, ...}'
# Attendu: 422 (montant minimum 5000)
```

---

**Document créé le:** 24 Janvier 2026  
**Version:** 1.0  
**Auteur:** Documentation API Vendeur
