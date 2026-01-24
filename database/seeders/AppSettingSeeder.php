<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Qosic
            ['key' => 'qosic_url', 'value' => 'https://api.qosic.net/QosicBridge/user/requestpayment', 'label' => 'Qosic API URL', 'group' => 'payment', 'type' => 'text'],
            ['key' => 'qosic_login', 'value' => null, 'label' => 'Qosic Login', 'group' => 'payment', 'type' => 'text'],
            ['key' => 'qosic_password', 'value' => null, 'label' => 'Qosic Password', 'group' => 'payment', 'type' => 'password'],
            ['key' => 'qosic_client_id', 'value' => null, 'label' => 'Qosic Client ID', 'group' => 'payment', 'type' => 'text'],

            // CinetPay
            ['key' => 'cinetpay_site_id', 'value' => null, 'label' => 'CinetPay Site ID', 'group' => 'payment', 'type' => 'text'],
            ['key' => 'cinetpay_api_key', 'value' => null, 'label' => 'CinetPay API Key', 'group' => 'payment', 'type' => 'password'],

            // FedaPay
            ['key' => 'fedapay_api_key', 'value' => null, 'label' => 'FedaPay API Key', 'group' => 'payment', 'type' => 'password'],
            ['key' => 'fedapay_mode', 'value' => 'sandbox', 'label' => 'FedaPay Mode (sandbox/live)', 'group' => 'payment', 'type' => 'text'],

            // Location
            ['key' => 'google_maps_api_key', 'value' => null, 'label' => 'Google Maps API Key', 'group' => 'location', 'type' => 'text'],
            ['key' => 'mapbox_api_key', 'value' => null, 'label' => 'Mapbox API Key', 'group' => 'location', 'type' => 'text'],
            ['key' => 'default_delivery_fee', 'value' => '500', 'label' => 'Frais de livraison par défaut', 'group' => 'location', 'type' => 'number'],

            // General
            ['key' => 'site_name', 'value' => 'CabaaCabaa', 'label' => 'Nom du site', 'group' => 'general', 'type' => 'text'],
            ['key' => 'system_currency', 'value' => 'XOF', 'label' => 'Devise du système (ex: XOF, €, $)', 'group' => 'general', 'type' => 'text'],
            ['key' => 'contact_email', 'value' => 'contact@cabaacabaa.com', 'label' => 'Email de contact', 'group' => 'general', 'type' => 'text'],
            ['key' => 'contact_phone', 'value' => '+229 00 00 00 00', 'label' => 'Téléphone de contact', 'group' => 'general', 'type' => 'text'],

            // Branding
            ['key' => 'site_logo', 'value' => null, 'label' => 'Logo du site (Fichier)', 'group' => 'branding', 'type' => 'image'],
            ['key' => 'site_logo_url', 'value' => null, 'label' => 'Logo du site (URL)', 'group' => 'branding', 'type' => 'text'],
            ['key' => 'site_favicon', 'value' => null, 'label' => 'Favicon du site (Fichier)', 'group' => 'branding', 'type' => 'image'],
            ['key' => 'site_favicon_url', 'value' => null, 'label' => 'Favicon du site (URL)', 'group' => 'branding', 'type' => 'text'],

            // SEO
            ['key' => 'meta_title', 'value' => 'CabaaCabaa - Votre marketplace locale', 'label' => 'Titre Meta (SEO)', 'group' => 'seo', 'type' => 'text'],
            ['key' => 'meta_description', 'value' => 'Découvrez les meilleurs commerces près de chez vous : restaurants, boutiques, épiceries et plus. Livraison, emporter ou sur place.', 'label' => 'Description Meta (SEO)', 'group' => 'seo', 'type' => 'textarea'],
            ['key' => 'meta_keywords', 'value' => 'marketplace, commerce local, restaurant, boutique, épicerie, livraison, emporter, sur place', 'label' => 'Mots-clés Meta', 'group' => 'seo', 'type' => 'text'],
            ['key' => 'seo_google_analytics', 'value' => null, 'label' => 'Google Analytics ID', 'group' => 'seo', 'type' => 'text'],

            // Email Notifications
            ['key' => 'email_vendor_approved_subject', 'value' => 'Votre boutique est maintenant active ! 🚀', 'label' => 'Sujet Email: Vendeur Approuvé', 'group' => 'email', 'type' => 'text'],
            ['key' => 'email_vendor_approved_body', 'value' => "Félicitations ! Votre compte vendeur a été validé par notre équipe. Vous pouvez maintenant configurer votre menu, ajouter des photos et commencer à recevoir des commandes. Connectez-vous à votre tableau de bord pour finaliser votre configuration.", 'label' => 'Contenu Email: Vendeur Approuvé', 'group' => 'email', 'type' => 'textarea'],
        ];

        foreach ($settings as $setting) {
            $existing = \App\Models\AppSetting::where('key', $setting['key'])->first();

            if ($existing) {
                // Update metadata but keep the existing value (to preserve user changes like logos)
                $existing->update([
                    'label' => $setting['label'],
                    'group' => $setting['group'],
                    'type' => $setting['type'],
                ]);
            } else {
                // Create new setting with default value
                \App\Models\AppSetting::create($setting);
            }
        }
    }
}
