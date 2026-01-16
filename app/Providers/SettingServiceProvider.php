<?php

namespace App\Providers;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Skip if running in console (migrations, etc.) or if table doesn't exist
        if ($this->app->runningInConsole() || !Schema::hasTable('app_settings')) {
            return;
        }

        // Load all settings
        $settings = AppSetting::all();

        foreach ($settings as $setting) {
            // Mapping settings to config
            switch ($setting->key) {
                case 'site_name':
                    Config::set('app.name', $setting->value);
                    break;
                case 'contact_email':
                    Config::set('mail.from.address', $setting->value);
                    break;
                // Add more mappings here if needed
            }
        }
    }
}
