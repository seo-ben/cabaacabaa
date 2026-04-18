<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoryPlat;

class CategoryPlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'nom_categorie' => 'Fast Food',
                'description'   => 'Burgers, frites, tacos et restauration rapide.',
                'icone'         => '🍔',
                'ordre_affichage' => 1,
                'actif'         => true,
            ],
            [
                'nom_categorie' => 'Africain',
                'description'   => 'Plats locaux et traditionnels.',
                'icone'         => '🍲',
                'ordre_affichage' => 2,
                'actif'         => true,
            ],
            [
                'nom_categorie' => 'Boissons',
                'description'   => 'Sodas, jus frais, et eaux.',
                'icone'         => '🥤',
                'ordre_affichage' => 3,
                'actif'         => true,
            ],
            [
                'nom_categorie' => 'Desserts',
                'description'   => 'Pâtisseries, glaces et gourmandises.',
                'icone'         => '🍰',
                'ordre_affichage' => 4,
                'actif'         => true,
            ],
            [
                'nom_categorie' => 'Pizza',
                'description'   => 'Pizzas artisanales et italiennes.',
                'icone'         => '🍕',
                'ordre_affichage' => 5,
                'actif'         => true,
            ],
        ];

        foreach ($categories as $cat) {
            CategoryPlat::updateOrCreate(
                ['nom_categorie' => $cat['nom_categorie']],
                $cat
            );
        }
    }
}
