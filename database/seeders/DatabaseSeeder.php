<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create core users: admin, vendeur, client
        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        $vendeur = \App\Models\User::factory()->create([
            'name' => 'Vendeur User',
            'email' => 'vendeur@example.com',
            'role' => 'vendeur',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        // Create a minimal vendeur profile so this user can login and see vendor link
        \App\Models\Vendeur::create([
            'id_user' => $vendeur->id_user,
            'id_zone' => null,
            'nom_commercial' => 'Vendeur Demo',
            'description' => 'Profil généré par seeder',
            'type_vendeur' => 'restaurant',
            'adresse_complete' => 'Adresse de démonstration',
            'latitude' => 0.0,
            'longitude' => 0.0,
            'horaires_ouverture' => json_encode([]),
            'telephone_commercial' => null,
            'statut_verification' => 'non_verifie',
            'note_moyenne' => 0.00,
            'nombre_avis' => 0,
            'image_principale' => null,
            'images_galerie' => json_encode([]),
            'actif' => true,
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Client User',
            'email' => 'client@example.com',
            'role' => 'client',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
    }
}
