<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Vendeur;
use App\Models\VendorCategory;
use App\Models\CategoryPlat;
use App\Models\Plat;
use App\Models\GroupeVariante;
use App\Models\Variante;
use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\AvisEvaluation;
use App\Models\Driver;
use App\Models\ZoneGeographique;
use App\Models\Coupon;
use App\Models\SubscriptionPlan;
use App\Models\MiseEnAvant;
use App\Models\AppSetting;
use App\Models\TransactionFinanciere;
use App\Models\PayoutRequest;
use App\Models\FavorisClient;
use App\Models\LogActivite;

class ComprehensiveSystemSeeder extends Seeder
{
    /**
     * Run the comprehensive system database seeder for CabaaCabaa.
     */
    public function run(): void
    {
        $this->command->info('🚀 Demarrage du Seeder Systeme Complet CabaaCabaa (Environnement Lome / Togo)...');

        // Disable Foreign Key Checks during seed (SQLite & MySQL support)
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        // 1. SYSTEM SETTINGS (APP_SETTINGS)
        $this->seedAppSettings();

        // 2. SUBSCRIPTION PLANS
        $plans = $this->seedSubscriptionPlans();

        // 3. GEOGRAPHICAL ZONES (Lomé & Environs)
        $zones = $this->seedGeographicalZones();

        // 4. VENDOR CATEGORIES & DISH CATEGORIES
        $vendorCategories = $this->seedVendorCategories();
        $dishCategories = $this->seedDishCategories();

        // 5. CORE USERS & ROLES
        $admin = $this->seedAdminUsers();
        $clients = $this->seedClientUsers();
        $drivers = $this->seedDriverUsers();

        // 6. VENDORS & VENDOR USERS
        $vendors = $this->seedVendors($zones, $vendorCategories, $plans);

        // 7. DISHES & VARIANTS (OPTIONS)
        $plats = $this->seedDishesAndVariants($vendors, $dishCategories);

        // 8. PROMOTIONS & BANNER PROMOS (MISE EN AVANT)
        $this->seedPromotions($vendors, $zones);

        // 9. COUPONS / VOUCHERS
        $coupons = $this->seedCoupons($vendors);

        // 10. REALISTIC ORDERS & ORDER ITEMS
        $commandes = $this->seedOrders($clients, $vendors, $drivers, $plats);

        // 11. REVIEWS & RATINGS (AVIS & EVALUATIONS)
        $this->seedReviews($commandes);

        // 12. CLIENT FAVORITES
        $this->seedFavorites($clients, $vendors);

        // 13. FINANCIAL TRANSACTIONS & PAYOUT REQUESTS
        $this->seedFinancials($vendors);

        // 14. SYSTEM ACTIVITY LOGS
        $this->seedLogs($admin, $clients);

        // Re-enable Foreign Key Checks
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->command->info('✅ Seeder Systeme Complet CabaaCabaa execute avec succes !');
    }

    /**
     * 1. Seed System Settings
     */
    private function seedAppSettings(): void
    {
        $settings = [
            ['key' => 'app_name', 'value' => 'CabaaCabaa', 'label' => 'Nom de l application', 'group' => 'general', 'type' => 'text'],
            ['key' => 'currency', 'value' => 'XOF', 'label' => 'Devise principale', 'group' => 'general', 'type' => 'text'],
            ['key' => 'commission_rate', 'value' => '10.5', 'label' => 'Taux de commission (%)', 'group' => 'financial', 'type' => 'number'],
            ['key' => 'delivery_fee_base', 'value' => '500', 'label' => 'Frais de livraison de base', 'group' => 'location', 'type' => 'number'],
            ['key' => 'delivery_fee_per_km', 'value' => '150', 'label' => 'Frais par km', 'group' => 'location', 'type' => 'number'],
            ['key' => 'support_phone', 'value' => '+228 90 00 00 00', 'label' => 'Téléphone support', 'group' => 'general', 'type' => 'text'],
            ['key' => 'support_email', 'value' => 'support@cabaacabaa.com', 'label' => 'Email support', 'group' => 'general', 'type' => 'text'],
            ['key' => 'enable_tmoney', 'value' => 'true', 'label' => 'Activer TMoney', 'group' => 'payment', 'type' => 'text'],
            ['key' => 'enable_flooz', 'value' => 'true', 'label' => 'Activer Flooz', 'group' => 'payment', 'type' => 'text'],
            ['key' => 'enable_card', 'value' => 'true', 'label' => 'Activer Carte Bancaire', 'group' => 'payment', 'type' => 'text'],
            ['key' => 'enable_cash_on_delivery', 'value' => 'true', 'label' => 'Activer Paiement à la livraison', 'group' => 'payment', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            AppSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * 2. Seed Subscription Plans
     */
    private function seedSubscriptionPlans(): array
    {
        $plansData = [
            [
                'name' => 'Plan Gratuit',
                'price' => 0,
                'product_limit' => 10,
                'staff_limit' => 1,
                'coupon_limit' => 1,
                'has_gallery' => false,
                'has_socials' => true,
                'can_recruit_drivers' => false,
            ],
            [
                'name' => 'Plan Pro Resto',
                'price' => 15000,
                'product_limit' => 50,
                'staff_limit' => 5,
                'coupon_limit' => 10,
                'has_gallery' => true,
                'has_socials' => true,
                'can_recruit_drivers' => true,
            ],
            [
                'name' => 'Plan Enterprise Fleet',
                'price' => 45000,
                'product_limit' => 200,
                'staff_limit' => 20,
                'coupon_limit' => 50,
                'has_gallery' => true,
                'has_socials' => true,
                'can_recruit_drivers' => true,
            ]
        ];

        $plans = [];
        foreach ($plansData as $planData) {
            $plans[] = SubscriptionPlan::updateOrCreate(
                ['name' => $planData['name']],
                $planData
            );
        }
        return $plans;
    }

    /**
     * 3. Seed Geographical Zones (Lomé, Togo)
     */
    private function seedGeographicalZones(): array
    {
        $zonesData = [
            [
                'nom' => 'Lomé Centre / Nyékonakpoè',
                'nom_zone' => 'Nyékonakpoè - Centre Ville',
                'description' => 'Zone administrative et commerciale du centre de Lomé',
                'ville' => 'Lomé',
                'quartier' => 'Nyékonakpoè',
                'code_postal' => '00228',
                'latitude' => 6.131944,
                'latitude_centre' => 6.131944,
                'longitude' => 1.222778,
                'longitude_centre' => 1.222778,
                'rayon_km' => 8.0,
                'population_estimee' => 150000,
                'actif' => true,
            ],
            [
                'nom' => 'Tokoin / Doumasséssé',
                'nom_zone' => 'Tokoin Hopital - Doumasséssé',
                'description' => 'Secteur universitaire et résidentiel animé',
                'ville' => 'Lomé',
                'quartier' => 'Tokoin',
                'code_postal' => '00228',
                'latitude' => 6.155000,
                'latitude_centre' => 6.155000,
                'longitude' => 1.215000,
                'longitude_centre' => 1.215000,
                'rayon_km' => 7.0,
                'population_estimee' => 200000,
                'actif' => true,
            ],
            [
                'nom' => 'Agoè / Cacaveli',
                'nom_zone' => 'Agoè Minamadou - Cacaveli',
                'description' => 'Grand Lomé Nord, zone résidentielle en pleine expansion',
                'ville' => 'Lomé',
                'quartier' => 'Agoè-Nyivé',
                'code_postal' => '00228',
                'latitude' => 6.210000,
                'latitude_centre' => 6.210000,
                'longitude' => 1.200000,
                'longitude_centre' => 1.200000,
                'rayon_km' => 12.0,
                'population_estimee' => 350000,
                'actif' => true,
            ],
            [
                'nom' => 'Baguida / Plage',
                'nom_zone' => 'Baguida Littoral',
                'description' => 'Zone touristique, hotels et restaurants en bord de mer',
                'ville' => 'Lomé',
                'quartier' => 'Baguida',
                'code_postal' => '00228',
                'latitude' => 6.140000,
                'latitude_centre' => 6.140000,
                'longitude' => 1.320000,
                'longitude_centre' => 1.320000,
                'rayon_km' => 10.0,
                'population_estimee' => 90000,
                'actif' => true,
            ]
        ];

        $zones = [];
        foreach ($zonesData as $zoneData) {
            $zones[] = ZoneGeographique::updateOrCreate(
                ['nom' => $zoneData['nom']],
                $zoneData
            );
        }
        return $zones;
    }

    /**
     * 4. Seed Vendor Categories & Dish Categories
     */
    private function seedVendorCategories(): array
    {
        $vCats = [
            ['name' => 'Restaurant', 'icon' => 'fa-utensils', 'description' => 'Cuisine locale et internationale'],
            ['name' => 'Fast Food', 'icon' => 'fa-hamburger', 'description' => 'Burgers, frites, tacos et snacks rapides'],
            ['name' => 'Pizzerias', 'icon' => 'fa-pizza-slice', 'description' => 'Pizzas au four à bois et spécialités italiennes'],
            ['name' => 'Boulangerie / Pâtisserie', 'icon' => 'fa-ice-cream', 'description' => 'Gâteaux, boulangeries et douceurs sucrées'],
            ['name' => 'Épicerie', 'icon' => 'fa-shopping-basket', 'description' => 'Produits frais et courses du quotidien'],
        ];

        $categories = [];
        foreach ($vCats as $vCat) {
            $categories[$vCat['name']] = VendorCategory::updateOrCreate(
                ['slug' => Str::slug($vCat['name'])],
                [
                    'name' => $vCat['name'],
                    'description' => $vCat['description'],
                    'icon' => $vCat['icon'],
                    'is_active' => true
                ]
            );
        }
        return $categories;
    }

    private function seedDishCategories(): array
    {
        $dCats = [
            ['nom_categorie' => 'Pizzas', 'description' => 'Pizzas chaudes et croustillantes', 'actif' => true],
            ['nom_categorie' => 'Burgers & Tacos', 'description' => 'Burgers généreux et tacos garni', 'actif' => true],
            ['nom_categorie' => 'Cuisine Africaine & Togolaise', 'description' => 'Thiéboudienne, Fufu, Ayimolou et sauce Graine', 'actif' => true],
            ['nom_categorie' => 'Grillades & Barbecue', 'description' => 'Poulet braisé, brochettes et poisson grillé', 'actif' => true],
            ['nom_categorie' => 'Boissons & Jus Frais', 'description' => 'Sodas, jus naturels et boissons bien fraîches', 'actif' => true],
            ['nom_categorie' => 'Desserts & Pâtisseries', 'description' => 'Tartes, glaces et gâteaux gourmands', 'actif' => true],
        ];

        $categories = [];
        foreach ($dCats as $dCat) {
            $categories[$dCat['nom_categorie']] = CategoryPlat::updateOrCreate(
                ['nom_categorie' => $dCat['nom_categorie']],
                $dCat
            );
        }
        return $categories;
    }

    /**
     * 5. Seed Core Users & Roles (Admin, Clients, Drivers)
     */
    private function seedAdminUsers(): User
    {
        return User::updateOrCreate(
            ['email' => 'admin@cabaacabaa.com'],
            [
                'name' => 'Super Admin CabaaCabaa',
                'role' => 'admin',
                'telephone' => '+22890111111',
                'password' => Hash::make('password'),
            ]
        );
    }

    private function seedClientUsers(): array
    {
        $clientsData = [
            ['name' => 'Koffi Mensah', 'email' => 'koffi.mensah@gmail.com', 'telephone' => '+22890222222'],
            ['name' => 'Abla Lawson', 'email' => 'abla.lawson@yahoo.fr', 'telephone' => '+22891333333'],
            ['name' => 'Jean-Pierre Dupont', 'email' => 'jp.dupont@hotmail.com', 'telephone' => '+22892444444'],
            ['name' => 'Sena Amégadzie', 'email' => 'sena.amegadzie@gmail.com', 'telephone' => '+22893555555'],
        ];

        $clients = [];
        foreach ($clientsData as $cData) {
            $clients[] = User::updateOrCreate(
                ['email' => $cData['email']],
                array_merge($cData, [
                    'role' => 'client',
                    'password' => Hash::make('password'),
                ])
            );
        }
        return $clients;
    }

    private function seedDriverUsers(): array
    {
        $driversData = [
            ['name' => 'Yao Livreur Express', 'email' => 'yao.driver@gmail.com', 'lat' => 6.132000, 'lng' => 1.222800],
            ['name' => 'Edem Moto Course', 'email' => 'edem.driver@gmail.com', 'lat' => 6.156000, 'lng' => 1.216000],
            ['name' => 'Kodjo Fast Delivery', 'email' => 'kodjo.driver@gmail.com', 'lat' => 6.211000, 'lng' => 1.201000],
        ];

        $drivers = [];
        foreach ($driversData as $dData) {
            $user = User::updateOrCreate(
                ['email' => $dData['email']],
                [
                    'name' => $dData['name'],
                    'role' => 'client',
                    'telephone' => '+22899' . rand(100000, 999999),
                    'password' => Hash::make('password'),
                ]
            );

            Driver::updateOrCreate(
                ['user_id' => $user->id_user],
                [
                    'latitude' => $dData['lat'],
                    'longitude' => $dData['lng'],
                    'is_online' => true,
                    'last_seen_at' => now(),
                ]
            );

            $drivers[] = $user;
        }
        return $drivers;
    }

    /**
     * 6. Seed Vendors
     */
    private function seedVendors(array $zones, array $vCategories, array $plans): array
    {
        $vendorsSetup = [
            [
                'email' => 'pizza.king@cabaacabaa.com',
                'name' => 'Gérant Pizza King',
                'nom_commercial' => 'Pizza King Lomé',
                'description' => 'Les meilleures pizzas au feu de bois de la capitale.',
                'type_vendeur' => 'restaurant',
                'adresse_complete' => 'Boulevard Circulaire, Nyékonakpoè, Lomé',
                'latitude' => 6.131944,
                'longitude' => 1.222778,
                'category' => $vCategories['Pizzerias']->id_category_vendeur ?? null,
                'zone' => $zones[0]->id_zone,
                'plan' => $plans[1]->id,
                'note' => 4.7,
                'boosted' => true
            ],
            [
                'email' => 'saveurs.togo@cabaacabaa.com',
                'name' => 'Chef Akossiwa',
                'nom_commercial' => 'Saveurs du Togo',
                'description' => 'Spécialités africaines et togolaises cuisinées avec amour.',
                'type_vendeur' => 'restaurant',
                'adresse_complete' => 'Avenue de la Chance, Tokoin, Lomé',
                'latitude' => 6.155000,
                'longitude' => 1.215000,
                'category' => $vCategories['Restaurant']->id_category_vendeur ?? null,
                'zone' => $zones[1]->id_zone,
                'plan' => $plans[1]->id,
                'note' => 4.9,
                'boosted' => true
            ],
            [
                'email' => 'burger.house@cabaacabaa.com',
                'name' => 'Gérant Burger House',
                'nom_commercial' => 'Burger House Tokoin',
                'description' => 'Burgers artisanaux, frites croustillantes et milkshakes.',
                'type_vendeur' => 'fast_food',
                'adresse_complete' => 'Carrefour Bodjona, Tokoin, Lomé',
                'latitude' => 6.158000,
                'longitude' => 1.218000,
                'category' => $vCategories['Fast Food']->id_category_vendeur ?? null,
                'zone' => $zones[1]->id_zone,
                'plan' => $plans[0]->id,
                'note' => 4.5,
                'boosted' => false
            ],
            [
                'email' => 'grilladin@cabaacabaa.com',
                'name' => 'Maître Grilleur Kossi',
                'nom_commercial' => 'Le Grilladin Nyékonakpoè',
                'description' => 'Poulets braisés au charbon de bois, poisson grillé et frites d ananas.',
                'type_vendeur' => 'restaurant',
                'adresse_complete' => 'Rue des Etoiles, Nyékonakpoè, Lomé',
                'latitude' => 6.134000,
                'longitude' => 1.224000,
                'category' => $vCategories['Restaurant']->id_category_vendeur ?? null,
                'zone' => $zones[0]->id_zone,
                'plan' => $plans[2]->id,
                'note' => 4.8,
                'boosted' => true
            ],
            [
                'email' => 'sweet.delight@cabaacabaa.com',
                'name' => 'Chef Pâtissière Marie',
                'nom_commercial' => 'Sweet Delight Pâtisserie',
                'description' => 'Gâteaux d anniversaire, viennoiseries et desserts raffinés.',
                'type_vendeur' => 'patisserie',
                'adresse_complete' => 'Zone Résidentielle, Baguida, Lomé',
                'latitude' => 6.142000,
                'longitude' => 1.322000,
                'category' => $vCategories['Boulangerie / Pâtisserie']->id_category_vendeur ?? null,
                'zone' => $zones[3]->id_zone,
                'plan' => $plans[0]->id,
                'note' => 4.6,
                'boosted' => false
            ],
        ];

        $vendors = [];
        foreach ($vendorsSetup as $vSetup) {
            $user = User::updateOrCreate(
                ['email' => $vSetup['email']],
                [
                    'name' => $vSetup['name'],
                    'role' => 'vendeur',
                    'telephone' => '+22898' . rand(100000, 999999),
                    'password' => Hash::make('password'),
                ]
            );

            $vendor = Vendeur::updateOrCreate(
                ['id_user' => $user->id_user],
                [
                    'nom_commercial' => $vSetup['nom_commercial'],
                    'slug' => Str::slug($vSetup['nom_commercial']),
                    'description' => $vSetup['description'],
                    'type_vendeur' => $vSetup['type_vendeur'],
                    'adresse_complete' => $vSetup['adresse_complete'],
                    'latitude' => $vSetup['latitude'],
                    'longitude' => $vSetup['longitude'],
                    'id_category_vendeur' => $vSetup['category'],
                    'id_zone' => $vSetup['zone'],
                    'subscription_plan_id' => $vSetup['plan'],
                    'horaires_ouverture' => json_encode(['lundi' => '09:00-23:00', 'mardi' => '09:00-23:00', 'dimanche' => '10:00-22:00']),
                    'statut_verification' => 'verifie',
                    'actif' => true,
                    'is_boosted' => $vSetup['boosted'],
                    'boost_expires_at' => $vSetup['boosted'] ? now()->addDays(30) : null,
                    'note_moyenne' => $vSetup['note'],
                    'nombre_avis' => rand(25, 180),
                    'wallet_balance' => rand(50000, 450000),
                    'delivery_rate_per_km' => 200,
                ]
            );

            $vendors[] = $vendor;
        }
        return $vendors;
    }

    /**
     * 7. Seed Dishes & Options / Variants
     */
    private function seedDishesAndVariants(array $vendors, array $dCategories): array
    {
        $allPlats = [];

        foreach ($vendors as $vendor) {
            $nom = strtolower($vendor->nom_commercial);

            if (str_contains($nom, 'pizza')) {
                // Pizza Dishes
                $p1 = Plat::create([
                    'id_vendeur' => $vendor->id_vendeur,
                    'id_categorie' => $dCategories['Pizzas']->id_categorie,
                    'nom_plat' => 'Pizza Margherita Supreme',
                    'description' => 'Sauce tomate fraîche, double mozzarella, huile d olive extra vierge et basilic',
                    'prix' => 5000,
                    'devise' => 'XOF',
                    'disponible' => true,
                    'date_creation' => now(),
                ]);

                $p2 = Plat::create([
                    'id_vendeur' => $vendor->id_vendeur,
                    'id_categorie' => $dCategories['Pizzas']->id_categorie,
                    'nom_plat' => 'Pizza 4 Fromages Gourmande',
                    'description' => 'Mozzarella, Gorgonzola, chèvre frais, parmesan AOP',
                    'prix' => 6500,
                    'devise' => 'XOF',
                    'disponible' => true,
                    'date_creation' => now(),
                ]);

                // Create Variants Group
                $g1 = GroupeVariante::create([
                    'id_plat' => $p1->id_plat,
                    'nom' => 'Taille de la Pizza',
                    'obligatoire' => true,
                    'choix_multiple' => false,
                    'min_choix' => 1,
                    'max_choix' => 1
                ]);

                Variante::create(['id_groupe' => $g1->id_groupe, 'nom' => 'Moyenne (30cm)', 'prix_supplement' => 0]);
                Variante::create(['id_groupe' => $g1->id_groupe, 'nom' => 'Grande Familiale (40cm)', 'prix_supplement' => 2500]);

                $allPlats[] = $p1;
                $allPlats[] = $p2;

            } elseif (str_contains($nom, 'saveurs') || str_contains($nom, 'grilladin')) {
                // Local Dishes
                $p1 = Plat::create([
                    'id_vendeur' => $vendor->id_vendeur,
                    'id_categorie' => $dCategories['Cuisine Africaine & Togolaise']->id_categorie,
                    'nom_plat' => 'Poulet Braise au Charbon + Frites d Yame',
                    'description' => 'Demi-poulet mariné aux épices locales, braisé à la perfection et servi avec piment vert',
                    'prix' => 4500,
                    'devise' => 'XOF',
                    'disponible' => true,
                    'date_creation' => now(),
                ]);

                $p2 = Plat::create([
                    'id_vendeur' => $vendor->id_vendeur,
                    'id_categorie' => $dCategories['Cuisine Africaine & Togolaise']->id_categorie,
                    'nom_plat' => 'Thieboudienne au Poisson Frais',
                    'description' => 'Riz gras sénégalais au mérou frais, légumes mijotés (carotte, chou, manioc)',
                    'prix' => 3500,
                    'devise' => 'XOF',
                    'disponible' => true,
                    'date_creation' => now(),
                ]);

                $allPlats[] = $p1;
                $allPlats[] = $p2;

            } elseif (str_contains($nom, 'burger')) {
                // Burger Dishes
                $p1 = Plat::create([
                    'id_vendeur' => $vendor->id_vendeur,
                    'id_categorie' => $dCategories['Burgers & Tacos']->id_categorie,
                    'nom_plat' => 'Double Bacon Cheeseburger XL',
                    'description' => 'Deux steaks hachés 150g, bacon croustillant, cheddar fondu, oignons caramélisés',
                    'prix' => 4200,
                    'devise' => 'XOF',
                    'disponible' => true,
                    'date_creation' => now(),
                ]);

                $allPlats[] = $p1;
            } else {
                // Bakery / Desserts
                $p1 = Plat::create([
                    'id_vendeur' => $vendor->id_vendeur,
                    'id_categorie' => $dCategories['Desserts & Pâtisseries']->id_categorie,
                    'nom_plat' => 'Tartelette Citron Meringuée',
                    'description' => 'Pâte sablée pur beurre, crème citron acidulée et meringue italienne dorée',
                    'prix' => 2000,
                    'devise' => 'XOF',
                    'disponible' => true,
                    'date_creation' => now(),
                ]);

                $allPlats[] = $p1;
            }

            // Universal Drink for all vendors
            $drink = Plat::create([
                'id_vendeur' => $vendor->id_vendeur,
                'id_categorie' => $dCategories['Boissons & Jus Frais']->id_categorie,
                'nom_plat' => 'Jus de Bissap Maison (50cl)',
                'description' => 'Jus naturel d hibiscus parfumé à la menthe fraîche et fleur d oranger',
                'prix' => 1000,
                'devise' => 'XOF',
                'disponible' => true,
                'date_creation' => now(),
            ]);

            $allPlats[] = $drink;
        }

        return $allPlats;
    }

    /**
     * 8. Seed Promotions & Banners
     */
    private function seedPromotions(array $vendors, array $zones): void
    {
        foreach ($vendors as $index => $vendor) {
            if ($vendor->is_boosted) {
                MiseEnAvant::create([
                    'id_vendeur' => $vendor->id_vendeur,
                    'type_promotion' => 'banniere_hero',
                    'priorite' => $index + 1,
                    'date_debut' => now()->subDays(5),
                    'date_fin' => now()->addDays(25),
                    'id_zone' => $zones[0]->id_zone,
                    'actif' => true,
                    'description' => 'Offre spéciale du mois chez ' . $vendor->nom_commercial,
                    'date_creation' => now()
                ]);
            }
        }
    }

    /**
     * 9. Seed Coupons
     */
    private function seedCoupons(array $vendors): array
    {
        $coupons = [];
        foreach ($vendors as $vendor) {
            $coupons[] = Coupon::create([
                'id_vendeur' => $vendor->id_vendeur,
                'code' => 'PROMO' . rand(10, 50),
                'type' => 'pourcentage',
                'valeur' => 15.00,
                'montant_minimal_achat' => 3000,
                'limite_utilisation' => 100,
                'nombre_utilisations' => 12,
                'expire_at' => now()->addDays(60),
                'actif' => true
            ]);
        }
        return $coupons;
    }

    /**
     * 10. Seed Realistic Orders & Line Items
     */
    private function seedOrders(array $clients, array $vendors, array $drivers, array $plats): array
    {
        $statuts = ['en_attente', 'en_preparation', 'prete', 'en_livraison', 'livree', 'annulee'];
        $modesPaiement = ['tmoney', 'flooz', 'carte_bancaire', 'espece'];
        $commandes = [];

        for ($i = 1; $i <= 15; $i++) {
            $client = $clients[array_rand($clients)];
            $vendor = $vendors[array_rand($vendors)];
            $driver = $drivers[array_rand($drivers)];
            $statut = $statuts[array_rand($statuts)];
            $modePaiement = $modesPaiement[array_rand($modesPaiement)];

            // Pick plats for this vendor
            $vendorPlats = array_filter($plats, fn($p) => $p->id_vendeur === $vendor->id_vendeur);
            if (empty($vendorPlats)) {
                $vendorPlats = $plats;
            }

            $selectedPlat = $vendorPlats[array_rand($vendorPlats)];
            $quantite = rand(1, 3);
            $montantPlats = $selectedPlat->prix * $quantite;
            $fraisService = 500;
            $montantTotal = $montantPlats + $fraisService;

            $commande = Commande::create([
                'numero_commande' => 'CAB-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'id_client' => $client->id_user,
                'id_vendeur' => $vendor->id_vendeur,
                'id_livreur' => in_array($statut, ['en_livraison', 'livree']) ? $driver->id_user : null,
                'statut' => $statut,
                'type_recuperation' => 'livraison',
                'mode_paiement_prevu' => $modePaiement,
                'paiement_effectue' => in_array($statut, ['livree', 'en_livraison', 'prete']),
                'montant_plats' => $montantPlats,
                'frais_service' => $fraisService,
                'montant_total' => $montantTotal,
                'date_commande' => now()->subHours(rand(1, 48)),
                'heure_preparation_debut' => now()->subMinutes(50),
                'heure_prete' => now()->subMinutes(30),
                'heure_recuperation_effective' => $statut === 'livree' ? now()->subMinutes(10) : null,
                'instructions_speciales' => 'Appeler à l arrivee au portail.',
            ]);

            LigneCommande::create([
                'id_commande' => $commande->id_commande,
                'id_plat' => $selectedPlat->id_plat,
                'nom_plat_historique' => $selectedPlat->nom_plat,
                'prix_unitaire_historique' => $selectedPlat->prix,
                'quantite' => $quantite,
                'prix_total_ligne' => $montantPlats,
            ]);

            $commandes[] = $commande;
        }

        return $commandes;
    }

    /**
     * 11. Seed Reviews & Ratings
     */
    private function seedReviews(array $commandes): void
    {
        $commentaires = [
            'Livraison super rapide et repas chaud à l arrivée ! Je recommande vivement.',
            'Plat succulent, exactement ce que j avais commandé. Merci au chef !',
            'Service de qualité, livreur courtois et souriant.',
            'Très satisfait de la quantité et du goût. CabaaCabaa est au top !'
        ];

        foreach ($commandes as $cmd) {
            if ($cmd->statut === 'livree') {
                AvisEvaluation::create([
                    'id_client' => $cmd->id_client,
                    'id_vendeur' => $cmd->id_vendeur,
                    'id_commande' => $cmd->id_commande,
                    'note' => rand(4, 5),
                    'commentaire' => $commentaires[array_rand($commentaires)],
                    'note_qualite' => rand(4, 5),
                    'note_rapidite' => rand(4, 5),
                    'note_rapport_qualite_prix' => rand(4, 5),
                    'statut_avis' => 'publie',
                    'date_publication' => now()->subHours(2),
                    'reponse_vendeur' => 'Merci beaucoup pour votre confiance et bon appétit !',
                    'date_reponse' => now()->subHour()
                ]);
            }
        }
    }

    /**
     * 12. Seed Favorites
     */
    private function seedFavorites(array $clients, array $vendors): void
    {
        foreach ($clients as $client) {
            $favVendor = $vendors[array_rand($vendors)];
            FavorisClient::firstOrCreate([
                'id_client' => $client->id_user,
                'id_vendeur' => $favVendor->id_vendeur,
            ], [
                'date_ajout' => now()
            ]);
        }
    }

    /**
     * 13. Seed Financial Transactions & Payouts
     */
    private function seedFinancials(array $vendors): void
    {
        foreach ($vendors as $vendor) {
            // Financial Credit Transaction
            TransactionFinanciere::create([
                'id_vendeur' => $vendor->id_vendeur,
                'type' => 'credit',
                'montant' => rand(25000, 100000),
                'description' => 'Encaissement ventes commandes CabaaCabaa',
                'statut' => 'complete',
                'reference' => 'TX-' . strtoupper(Str::random(10)),
                'date_transaction' => now()->subDays(rand(1, 5))
            ]);

            // Payout Request
            PayoutRequest::create([
                'id_vendeur' => $vendor->id_vendeur,
                'montant' => rand(15000, 50000),
                'mode_paiement' => 'TMoney',
                'numero_compte' => '+22890' . rand(100000, 999999),
                'statut' => 'approuve',
                'created_at' => now()->subDays(rand(1, 3))
            ]);
        }
    }

    /**
     * 14. Seed Activity Logs
     */
    private function seedLogs(User $admin, array $clients): void
    {
        LogActivite::create([
            'id_user' => $admin->id_user,
            'action' => 'login_admin',
            'description' => 'Connexion réussie au tableau de bord d administration',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'created_at' => now()
        ]);
    }
}
