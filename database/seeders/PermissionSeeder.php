<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['key' => 'view_dashboard', 'name' => 'Voir le Tableau de bord', 'group' => 'Dashboard'],

            // Vendors
            ['key' => 'view_vendors', 'name' => 'Voir les Vendeurs', 'group' => 'Vendeurs'],
            ['key' => 'manage_vendors', 'name' => 'Gérer les Vendeurs (Créer/Modifier/Supprimer)', 'group' => 'Vendeurs'],
            ['key' => 'approve_vendors', 'name' => 'Approuver/Suspendre les Vendeurs', 'group' => 'Vendeurs'],
            ['key' => 'view_vendor_categories', 'name' => 'Gérer les Types de Boutiques', 'group' => 'Vendeurs'],

            // Orders
            ['key' => 'view_orders', 'name' => 'Voir les Commandes', 'group' => 'Commandes'],
            ['key' => 'manage_orders', 'name' => 'Gérer les Commandes (Modifier statut)', 'group' => 'Commandes'],

            // Users
            ['key' => 'view_users', 'name' => 'Voir les Utilisateurs', 'group' => 'Utilisateurs'],
            ['key' => 'manage_users', 'name' => 'Gérer les Utilisateurs', 'group' => 'Utilisateurs'],
            ['key' => 'manage_admins', 'name' => 'Gérer les Administrateurs (Permissions)', 'group' => 'Utilisateurs'],

            // Products & Catalog
            ['key' => 'manage_products', 'name' => 'Gérer les Produits (Global)', 'group' => 'Catalogue'],
            ['key' => 'manage_categories', 'name' => 'Gérer les Catégories Produits', 'group' => 'Catalogue'],

            // Locations
            ['key' => 'manage_zones', 'name' => 'Gérer les Zones et Localisation', 'group' => 'Localisation'],

            // Finance
            ['key' => 'view_finance', 'name' => 'Voir les Finances', 'group' => 'Finance'],

            // Settings
            ['key' => 'manage_settings', 'name' => 'Gérer les Paramètres Système', 'group' => 'Paramètres'],

            // Security
            ['key' => 'view_security', 'name' => 'Accéder au Journal de Sécurité', 'group' => 'Sécurité'],
        ];

        foreach ($permissions as $permission) {
            \App\Models\Permission::firstOrCreate(
                ['key' => $permission['key']],
                ['name' => $permission['name'], 'group' => $permission['group']]
            );
        }
    }
}
