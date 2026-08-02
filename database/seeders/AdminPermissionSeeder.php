<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Permission;

class AdminPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Emails des administrateurs principaux à accréditer
        $emails = [
            'admin@cabaacabaa.com',
            'admin@example.com',
        ];

        // 2. Récupérer TOUTES les permissions existantes de la base de données
        $allPermissions = Permission::all();

        foreach ($emails as $email) {
            $admin = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => 'Super Admin CabaaCabaa',
                    'role' => 'super_admin',
                    'password' => Hash::make('password'),
                ]
            );

            // S'assurer que le rôle est bien super_admin
            $admin->role = 'super_admin';
            $admin->save();

            // Attacher explicitement toutes les permissions si la relation existe
            if (method_exists($admin, 'permissions')) {
                $admin->permissions()->sync($allPermissions->pluck('id'));
            }
        }

        $this->command->info('✅ Les administrateurs (dont admin@cabaacabaa.com) ont le rôle super_admin ET possèdent toutes les permissions !');
    }
}

