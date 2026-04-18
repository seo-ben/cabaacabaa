<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plat;
use App\Models\Vendeur;
use App\Models\CategoryPlat;
use Illuminate\Support\Facades\DB;

class PlatSeeder extends Seeder
{
    /**
     * Seed 10 produits réalistes pour les vendeurs/boutiques existants.
     */
    public function run(): void
    {
        $vendeurs = Vendeur::all();

        if ($vendeurs->isEmpty()) {
            $this->command->warn('⚠️  Aucun vendeur trouvé. Créez d\'abord des vendeurs.');
            return;
        }

        // Récupérer les catégories existantes, ou null si aucune
        $categories = CategoryPlat::pluck('id_categorie')->toArray();
        $defaultCategory = !empty($categories) ? $categories[0] : null;

        $plats = [
            [
                'nom_plat' => 'Poulet Braisé Complet',
                'description' => 'Poulet braisé croustillant accompagné de frites dorées, salade fraîche et sauce piment maison. Un classique incontournable de la cuisine togolaise.',
                'prix' => 3500,
                'temps_preparation_min' => 25,
                'nombre_commandes' => rand(40, 150),
                'nombre_vues' => rand(200, 800),
            ],
            [
                'nom_plat' => 'Fufu avec Sauce Arachide',
                'description' => 'Fufu traditionnel servi avec une sauce arachide onctueuse, viande de bœuf tendre et légumes de saison. Portion généreuse.',
                'prix' => 2000,
                'temps_preparation_min' => 20,
                'nombre_commandes' => rand(60, 200),
                'nombre_vues' => rand(300, 900),
            ],
            [
                'nom_plat' => 'Riz Jollof Spécial',
                'description' => 'Riz jollof parfumé à la tomate, épices locales, accompagné de poulet grillé et banane plantain frite.',
                'prix' => 2500,
                'temps_preparation_min' => 30,
                'nombre_commandes' => rand(50, 180),
                'nombre_vues' => rand(250, 700),
            ],
            [
                'nom_plat' => 'Akoumé + Sauce Adémé',
                'description' => 'Pâte de maïs (Akoumé) servie avec sauce adémé aux épinards locaux, poisson fumé et crabes frais.',
                'prix' => 1800,
                'temps_preparation_min' => 15,
                'nombre_commandes' => rand(80, 250),
                'nombre_vues' => rand(400, 1000),
            ],
            [
                'nom_plat' => 'Poisson Grillé Tilapia',
                'description' => 'Tilapia entier grillé au feu de bois, mariné aux épices africaines, servi avec atchèkè et piment frais.',
                'prix' => 4000,
                'temps_preparation_min' => 35,
                'nombre_commandes' => rand(30, 120),
                'nombre_vues' => rand(180, 600),
            ],
            [
                'nom_plat' => 'Shawarma Jumbo',
                'description' => 'Shawarma XXL garni de viande de poulet marinée, crudités fraîches, frites et sauce cocktail maison.',
                'prix' => 3000,
                'temps_preparation_min' => 15,
                'nombre_commandes' => rand(100, 300),
                'nombre_vues' => rand(500, 1200),
            ],
            [
                'nom_plat' => 'Pizza Africaine 4 Saisons',
                'description' => 'Pizza artisanale avec sauce tomate maison, fromage mozzarella, poulet épicé, oignons caramélisés et poivrons grillés.',
                'prix' => 5000,
                'temps_preparation_min' => 25,
                'nombre_commandes' => rand(20, 90),
                'nombre_vues' => rand(150, 500),
            ],
            [
                'nom_plat' => 'Jus de Bissap Frais (1L)',
                'description' => 'Boisson naturelle à base de fleurs d\'hibiscus (bissap), sucre de canne et menthe fraîche. Rafraîchissant et vitaminé.',
                'prix' => 1000,
                'temps_preparation_min' => 5,
                'nombre_commandes' => rand(150, 400),
                'nombre_vues' => rand(600, 1500),
            ],
            [
                'nom_plat' => 'Brochettes de Bœuf (x5)',
                'description' => '5 brochettes de bœuf tendres grillées au charbon, assaisonnées aux épices tchakalaka, avec sauce piment et oignons.',
                'prix' => 2500,
                'temps_preparation_min' => 20,
                'nombre_commandes' => rand(70, 220),
                'nombre_vues' => rand(350, 900),
            ],
            [
                'nom_plat' => 'Gâteau Moelleux Chocolat',
                'description' => 'Gâteau fondant au chocolat noir premium, cœur coulant, nappage cacao et chantilly fraîche. Idéal pour les occasions.',
                'prix' => 3500,
                'temps_preparation_min' => 10,
                'nombre_commandes' => rand(25, 100),
                'nombre_vues' => rand(200, 600),
                'en_promotion' => true,
                'prix_promotion' => 2800,
            ],
        ];

        $count = 0;

        foreach ($plats as $index => $platData) {
            // Distribuer les plats entre les vendeurs existants (round-robin)
            $vendeur = $vendeurs[$index % $vendeurs->count()];

            // Assigner une catégorie aléatoire parmi celles existantes
            $catId = !empty($categories) ? $categories[array_rand($categories)] : null;

            Plat::create([
                'id_vendeur' => $vendeur->id_vendeur,
                'id_categorie' => $catId,
                'nom_plat' => $platData['nom_plat'],
                'description' => $platData['description'],
                'prix' => $platData['prix'],
                'devise' => 'XOF',
                'disponible' => true,
                'stock_limite' => false,
                'quantite_disponible' => null,
                'temps_preparation_min' => $platData['temps_preparation_min'],
                'image_principale' => null,
                'images_supplementaires' => null,
                'nombre_commandes' => $platData['nombre_commandes'],
                'nombre_vues' => $platData['nombre_vues'],
                'en_promotion' => $platData['en_promotion'] ?? false,
                'prix_promotion' => $platData['prix_promotion'] ?? null,
                'date_debut_promotion' => isset($platData['en_promotion']) ? now() : null,
                'date_fin_promotion' => isset($platData['en_promotion']) ? now()->addDays(30) : null,
            ]);

            $count++;
            $this->command->info("  ✅ {$platData['nom_plat']} → {$vendeur->nom_commercial}");
        }

        $this->command->info("🎉 {$count} produits créés avec succès pour {$vendeurs->count()} vendeur(s) !");
    }
}
