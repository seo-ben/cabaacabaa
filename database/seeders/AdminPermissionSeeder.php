<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Permission;

class AdminPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Récupérer l'admin principal ou le créer s'il n'existe pas
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Supérieur',
                'role' => 'super_admin', // En faisant super_admin il a déjà accès à tout logiciellement, mais on attache quand même
                'password' => \Hash::make('password'),
            ]
        );

        // Au cas où le rôle était 'admin' basique, on force en super_admin
        $admin->role = 'super_admin';
        $admin->save();

        // 2. Récupérer TOUTES les permissions existantes de la base de données
        $allPermissions = Permission::all();

        // 3. Attacher les IDs de chaque permission à l'admin (sync va écraser et mettre tout propre sans doublon)
        $admin->permissions()->sync($allPermissions->pluck('id'));

        $this->command->info('✅ L\'utilisateur (admin@example.com) a maintenant le rôle super_admin ET possède explicitement toutes les permissions !');
    }
}
