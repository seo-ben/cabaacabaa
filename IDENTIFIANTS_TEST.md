# 🔑 Identifiants des Comptes de Test (Seeders) - Cabaa Cabaa

Ce document répertorie tous les comptes utilisateurs créés automatiquement par les seeders dans la base de données (`DatabaseSeeder` & `ComprehensiveSystemSeeder`).

> **Mot de passe universel pour TOUS les comptes** : `password`

---

## 1. 🛡️ Super Administrateurs (Admin)

| Nom | Email | Mot de passe | Rôle | Description |
| :--- | :--- | :--- | :--- | :--- |
| **Super Admin CabaaCabaa** | `admin@cabaacabaa.com` | `password` | `super_admin` | Compte Admin complet du système (Toutes les permissions) |
| **Admin User** | `admin@example.com` | `password` | `super_admin` | Compte Admin secondaire de test |

---

## 2. 🏪 Comptes Vendeurs (Restaurants & Commerçants)

| Nom Commercial | Email du Gérant | Mot de passe | Type de Commerce | Zone / Emplacement |
| :--- | :--- | :--- | :--- | :--- |
| **Pizza King Lomé** | `pizza.king@cabaacabaa.com` | `password` | Pizzeria / Restaurant | Nyékonakpoè (Boosté) |
| **Saveurs du Togo** | `saveurs.togo@cabaacabaa.com` | `password` | Cuisine Africaine | Tokoin (Boosté) |
| **Burger House Tokoin** | `burger.house@cabaacabaa.com` | `password` | Fast Food | Tokoin |
| **Le Grilladin Nyékonakpoè** | `grilladin@cabaacabaa.com` | `password` | Grillades & Barbecue | Nyékonakpoè (Boosté) |
| **Sweet Delight Pâtisserie** | `sweet.delight@cabaacabaa.com` | `password` | Pâtisserie | Baguida |
| **Sushi Ocean** | `sushi@lome.com` | `password` | Restaurant Japonais | Bord de mer |
| **Super Marché du Coin** | `market@lome.com` | `password` | Épicerie / Courses | Centre Ville |

---

## 3. 🛵 Comptes Livreurs (Drivers)

| Nom du Livreur | Email | Mot de passe | Statut | Zone GPS |
| :--- | :--- | :--- | :--- | :--- |
| **Yao Livreur Express** | `yao.driver@gmail.com` | `password` | En ligne (`is_online = 1`) | Nyékonakpoè |
| **Edem Moto Course** | `edem.driver@gmail.com` | `password` | En ligne (`is_online = 1`) | Tokoin |
| **Kodjo Fast Delivery** | `kodjo.driver@gmail.com` | `password` | En ligne (`is_online = 1`) | Agoè-Nyivé |
| **Livreur Rapide** | `driver1@example.com` | `password` | En ligne (`is_online = 1`) | Centre Ville |

---

## 4. 👤 Comptes Clients (Utilisateurs Final)

| Nom & Prénom | Email | Mot de passe | Téléphone |
| :--- | :--- | :--- | :--- |
| **Koffi Mensah** | `koffi.mensah@gmail.com` | `password` | `+22890222222` |
| **Abla Lawson** | `abla.lawson@yahoo.fr` | `password` | `+22891333333` |
| **Jean-Pierre Dupont** | `jp.dupont@hotmail.com` | `password` | `+22892444444` |
| **Sena Amégadzie** | `sena.amegadzie@gmail.com` | `password` | `+22893555555` |
| **Client Test** | `client@example.com` | `password` | `+22890000001` |
| **Client 2 Test** | `client2@example.com` | `password` | `+22890000002` |

---

## 🚀 Commande pour réinitialiser et peupler la base de données

```bash
php artisan migrate:fresh --seed
```
