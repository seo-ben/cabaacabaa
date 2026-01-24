<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Vendeur;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Run Config & Generic Seeders first
        $this->call([
            PermissionSeeder::class,
            AppSettingSeeder::class,
            VendorCategorySeeder::class,
            CountrySeeder::class,
        ]);

        // 2. Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        // 3. Create Client User
        $client = User::firstOrCreate(
            ['email' => 'client@example.com'],
            [
                'name' => 'Client User',
                'role' => 'client',
                'password' => Hash::make('password'),
            ]
        );

        // 4. Create Vendors with Locations (Lomé Area context)
        
        // Vendeur 1: Centre ville (Lomé)
        $userVendeur1 = User::firstOrCreate(
            ['email' => 'pizza@lome.com'],
            [
                'name' => 'Pizza King',
                'role' => 'vendeur',
                'password' => Hash::make('password'),
            ]
        );

        Vendeur::updateOrCreate(
            ['id_user' => $userVendeur1->id_user],
            [
                'nom_commercial' => 'Pizza King Lomé',
                'slug' => 'pizza-king-lome',
                'description' => 'Les meilleures pizzas au feu de bois de la capitale.',
                'type_vendeur' => 'restaurant', 
                'adresse_complete' => 'Boulevard Circulaire, Lomé',
                'latitude' => 6.131944,
                'longitude' => 1.222778, 
                'horaires_ouverture' => json_encode(['lundi' => '09:00-22:00']),
                'statut_verification' => 'verifie',
                'actif' => true,
                'is_boosted' => true, // BOOSTE (Payant)
                'boost_expires_at' => now()->addDays(30),
                'note_moyenne' => 4.5,
                'nombre_avis' => 120,
            ]
        );

        // Vendeur 2: Quartier Tokoin (Un peu plus au Nord)
        $userVendeur2 = User::firstOrCreate(
            ['email' => 'burger@lome.com'],
            [
                'name' => 'Burger House',
                'role' => 'vendeur',
                'password' => Hash::make('password'),
            ]
        );

        Vendeur::updateOrCreate(
            ['id_user' => $userVendeur2->id_user],
            [
                'nom_commercial' => 'Burger House Tokoin',
                'slug' => 'burger-house-tokoin',
                'description' => 'Burgers gourmets et frites maison.',
                'type_vendeur' => 'fast_food',
                'adresse_complete' => 'Tokoin, Lomé',
                'latitude' => 6.155000, 
                'longitude' => 1.215000, 
                'horaires_ouverture' => json_encode(['lundi' => '10:00-23:00']),
                'statut_verification' => 'verifie',
                'actif' => true,
                'note_moyenne' => 4.2,
                'nombre_avis' => 85,
            ]
        );

        // Vendeur 3: Près de la Plage (Sud)
        $userVendeur3 = User::firstOrCreate(
            ['email' => 'sushi@lome.com'],
            [
                'name' => 'Sushi Ocean',
                'role' => 'vendeur',
                'password' => Hash::make('password'),
            ]
        );

        Vendeur::updateOrCreate(
            ['id_user' => $userVendeur3->id_user],
            [
                'nom_commercial' => 'Sushi Ocean',
                'slug' => 'sushi-ocean',
                'description' => 'Sushi frais face à la mer.',
                'type_vendeur' => 'restaurant',
                'adresse_complete' => 'Route de la Plage, Lomé',
                'latitude' => 6.120000,
                'longitude' => 1.230000,
                'horaires_ouverture' => json_encode(['lundi' => '11:00-23:00']),
                'statut_verification' => 'verifie',
                'actif' => true,
                'note_moyenne' => 4.8,
                'nombre_avis' => 210,
            ]
        );

        // Vendeur 4: Épicerie (Pour tester la variété)
        $userVendeur4 = User::firstOrCreate(
            ['email' => 'market@lome.com'],
            [
                'name' => 'Super Marché Local',
                'role' => 'vendeur',
                'password' => Hash::make('password'),
            ]
        );

        Vendeur::updateOrCreate(
            ['id_user' => $userVendeur4->id_user],
            [
                'nom_commercial' => 'Super Marché du Coin',
                'slug' => 'super-marche-coin',
                'description' => 'Tous vos produits du quotidien.',
                'type_vendeur' => 'autre', // Mapped to Épicerie later
                'adresse_complete' => 'Quartier Administratif',
                'latitude' => 6.140000,
                'longitude' => 1.225000,
                'horaires_ouverture' => json_encode(['lundi' => '08:00-20:00']),
                'statut_verification' => 'verifie',
                'actif' => true,
                'note_moyenne' => 4.0,
                'nombre_avis' => 45,
            ]
        );
        
        // Link vendors to categories (optional, logic handled in controller mostly but good for consistency)
        // If you need direct relations, add them here.
    }
}
