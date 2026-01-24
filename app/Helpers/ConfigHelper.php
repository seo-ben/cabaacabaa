<?php

namespace App\Helpers;

use App\Models\AppSetting;

class ConfigHelper
{
    /**
     * Get a configuration value with priority: .env > database > default
     * 
     * @param string $key The setting key
     * @param mixed $default Default value if not found
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        // Map database keys to .env keys
        $envMap = [
            // Payment - Qosic
            'qosic_url' => 'QOSPAY_REQUEST_URL',
            'qosic_login' => 'QOSPAY_LOGIN',
            'qosic_password' => 'QOSPAY_PASSWORD',
            'qosic_client_id' => 'QOSPAY_CLIENT_ID',
            
            // Payment - CinetPay
            'cinetpay_site_id' => 'CINETPAY_SITE_ID',
            'cinetpay_api_key' => 'CINETPAY_API_KEY',
            'cinetpay_mode' => 'CINETPAY_MODE',
            
            // Payment - FedaPay
            'fedapay_api_key' => 'FEDAPAY_API_KEY',
            'fedapay_mode' => 'FEDAPAY_MODE',
            
            // Location
            'google_maps_api_key' => 'GOOGLE_MAPS_API_KEY',
            'mapbox_api_key' => 'MAPBOX_API_KEY',
            'default_delivery_fee' => 'DEFAULT_DELIVERY_FEE',
            
            // SEO & Analytics
            'seo_google_analytics' => 'GOOGLE_ANALYTICS_ID',
            'meta_title' => 'META_TITLE',
            'meta_description' => 'META_DESCRIPTION',
            'meta_keywords' => 'META_KEYWORDS',
            
            // Site Info
            'site_name' => 'SITE_NAME',
            'system_currency' => 'SYSTEM_CURRENCY',
            'contact_email' => 'SITE_CONTACT_EMAIL',
            'contact_phone' => 'SITE_CONTACT_PHONE',
            
            // Branding
            'site_logo_url' => 'SITE_LOGO_URL',
            'site_favicon_url' => 'SITE_FAVICON_URL',
        ];

        // Check if this key should come from .env
        if (isset($envMap[$key])) {
            $envValue = env($envMap[$key]);
            if ($envValue !== null && $envValue !== '') {
                return $envValue;
            }
        }

        // Fallback to database
        try {
            $setting = AppSetting::where('key', $key)->first();
            if ($setting && $setting->value !== null) {
                return $setting->value;
            }
        } catch (\Exception $e) {
            // If database is not available or table doesn't exist, continue to default
        }

        // Return default value
        return $default;
    }

    /**
     * Check if a key is sensitive (should not be displayed in admin)
     * 
     * @param string $key The setting key
     * @return bool
     */
    public static function isSensitive(string $key): bool
    {
        $sensitiveKeys = [
            // Payment credentials
            'qosic_login',
            'qosic_password',
            'qosic_client_id',
            'cinetpay_site_id',
            'cinetpay_api_key',
            'fedapay_api_key',
            
            // API Keys
            'google_maps_api_key',
            'mapbox_api_key',
            'seo_google_analytics',
        ];

        return in_array($key, $sensitiveKeys);
    }

    /**
     * Check if a key is configured via .env
     * 
     * @param string $key The setting key
     * @return bool
     */
    public static function isFromEnv(string $key): bool
    {
        $envMap = [
            'qosic_url' => 'QOSPAY_REQUEST_URL',
            'qosic_login' => 'QOSPAY_LOGIN',
            'qosic_password' => 'QOSPAY_PASSWORD',
            'qosic_client_id' => 'QOSPAY_CLIENT_ID',
            'cinetpay_site_id' => 'CINETPAY_SITE_ID',
            'cinetpay_api_key' => 'CINETPAY_API_KEY',
            'fedapay_api_key' => 'FEDAPAY_API_KEY',
            'google_maps_api_key' => 'GOOGLE_MAPS_API_KEY',
            'mapbox_api_key' => 'MAPBOX_API_KEY',
            'seo_google_analytics' => 'GOOGLE_ANALYTICS_ID',
        ];

        if (isset($envMap[$key])) {
            $envValue = env($envMap[$key]);
            return $envValue !== null && $envValue !== '';
        }

        return false;
    }

    /**
     * Get all settings grouped by group
     * 
     * @return \Illuminate\Support\Collection
     */
    public static function getAllGrouped()
    {
        try {
            return AppSetting::all()->groupBy('group');
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Set a setting value (only for non-sensitive keys)
     * 
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public static function set(string $key, $value): bool
    {
        // Don't allow setting sensitive keys via this method
        if (self::isSensitive($key)) {
            return false;
        }

        try {
            AppSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
