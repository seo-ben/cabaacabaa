<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Vendeur;
use App\Models\CategoryPlat;
use App\Models\Plat;
use App\Models\Commande;
use App\Models\LigneCommande;

class GeneralSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Ensure basic Client and Admin are created (Fallback if not already in DatabaseSeeder)
        $client = User::firstOrCreate(
            ['email' => 'client2@example.com'],
            [
                'name' => 'Client User',
                'role' => 'client',
                'password' => Hash::make('password'),
            ]
        );

        $driver = User::firstOrCreate(
            ['email' => 'driver1@example.com'],
            [
                'name' => 'Livreur Rapide',
                'role' => 'client', // 'livreur' n'est pas dans l'enum de la migration
                'password' => Hash::make('password'),
            ]
        );

        // On crée l'entrée dans la table Driver liée
        \App\Models\Driver::firstOrCreate(
            ['user_id' => $driver->id_user],
            [
                'latitude' => 6.131900,
                'longitude' => 1.222700,
                'is_online' => true,
                'last_seen_at' => now(),
            ]
            
        );

        // 2. Create Categories
        $categoriesData = [
            ['nom_categorie' => 'Pizzas', 'description' => 'Délicieuses pizzas au feu de bois', 'actif' => true],
            ['nom_categorie' => 'Burgers', 'description' => 'Burgers gourmets avec frites', 'actif' => true],
            ['nom_categorie' => 'Sushi', 'description' => 'Assortiment de sushis frais', 'actif' => true],
            ['nom_categorie' => 'Boissons', 'description' => 'Boissons fraîches et jus', 'actif' => true],
            ['nom_categorie' => 'Desserts', 'description' => 'Pour les gourmands', 'actif' => true],
        ];

        $categories = [];
        foreach ($categoriesData as $catData) {
            $categories[$catData['nom_categorie']] = CategoryPlat::firstOrCreate(
                ['nom_categorie' => $catData['nom_categorie']],
                $catData
            );
        }

        // 3. Create Plats (Dishes) for existing vendors
        // Retrieve some vendors created in DatabaseSeeder
        $vendors = Vendeur::all();
        if ($vendors->isEmpty()) {
            $this->command->warn('Aucun vendeur trouvé. Veuillez exécuter DatabaseSeeder en premier.');
            return;
        }

        $plats = [];

        foreach ($vendors as $vendeur) {
            // Assign some food depending on vendor name
            if (str_contains(strtolower($vendeur->nom_commercial), 'pizza')) {
                $plats[] = Plat::create([
                    'id_vendeur' => $vendeur->id_vendeur,
                    'id_categorie' => $categories['Pizzas']->id_categorie,
                    'nom_plat' => 'Pizza Margherita',
                    'description' => 'Sauce tomate, mozzarella, basilic frais',
                    'prix' => 4500,
                    'devise' => 'XOF',
                    'disponible' => true,
                    'date_creation' => now(),
                ]);
                $plats[] = Plat::create([
                    'id_vendeur' => $vendeur->id_vendeur,
                    'id_categorie' => $categories['Pizzas']->id_categorie,
                    'nom_plat' => 'Pizza 4 Fromages',
                    'description' => 'Mozzarella, chèvre, emmental, roquefort',
                    'prix' => 6000,
                    'devise' => 'XOF',
                    'disponible' => true,
                    'date_creation' => now(),
                ]);
            } elseif (str_contains(strtolower($vendeur->nom_commercial), 'burger')) {
                $plats[] = Plat::create([
                    'id_vendeur' => $vendeur->id_vendeur,
                    'id_categorie' => $categories['Burgers']->id_categorie,
                    'nom_plat' => 'Classic Cheeseburger',
                    'description' => 'Steak haché, cheddar, salade, tomate, sauce secrète',
                    'prix' => 3500,
                    'devise' => 'XOF',
                    'disponible' => true,
                    'date_creation' => now(),
                ]);
            } elseif (str_contains(strtolower($vendeur->nom_commercial), 'sushi')) {
                $plats[] = Plat::create([
                    'id_vendeur' => $vendeur->id_vendeur,
                    'id_categorie' => $categories['Sushi']->id_categorie,
                    'nom_plat' => 'Plateau Maki Saumon (12 pcs)',
                    'description' => 'Maki au saumon frais et avocat',
                    'prix' => 8000,
                    'devise' => 'XOF',
                    'disponible' => true,
                    'date_creation' => now(),
                ]);
            }

            // Everyone sells drinks
            $plats[] = Plat::create([
                'id_vendeur' => $vendeur->id_vendeur,
                'id_categorie' => $categories['Boissons']->id_categorie,
                'nom_plat' => 'Coca-Cola (33cl)',
                'description' => 'Boisson gazeuse bien fraîche',
                'prix' => 1000,
                'devise' => 'XOF',
                'disponible' => true,
                'date_creation' => now(),
            ]);
        }

        // 4. Create some Commandes (Orders)
        if (count($plats) > 0) {
            $statuts = ['en_attente', 'en_preparation', 'prete', 'en_livraison', 'livree', 'annulee'];
            
            for ($i = 0; $i < 5; $i++) {
                // Pick a random plat
                $plat = $plats[array_rand($plats)];
                $quantite = rand(1, 3);
                $sous_total = $plat->prix * $quantite;
                $frais_service = 500;
                
                $commande = Commande::create([
                    'numero_commande' => 'CMD-' . strtoupper(Str::random(8)),
                    'id_client' => $client->id_user,
                    'id_vendeur' => $plat->id_vendeur,
                    'id_livreur' => rand(0, 1) ? $driver->id_user : null,
                    'statut' => $statuts[array_rand($statuts)],
                    'type_recuperation' => 'livraison',
                    'mode_paiement_prevu' => 'espece',
                    'paiement_effectue' => false,
                    'montant_plats' => $sous_total,
                    'frais_service' => $frais_service,
                    'montant_total' => $sous_total + $frais_service,
                    'date_commande' => now()->subDays(rand(0, 10))->subHours(rand(1, 10)),
                ]);

                LigneCommande::create([
                    'id_commande' => $commande->id_commande,
                    'id_plat' => $plat->id_plat,
                    'nom_plat_snapshot' => $plat->nom_plat,
                    'quantite' => $quantite,
                    'prix_unitaire' => $plat->prix,
                    'sous_total' => $sous_total,
                ]);
            }
        }

        $this->command->info('General system data seeded successfully!');
    }
}
