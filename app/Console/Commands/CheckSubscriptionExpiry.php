<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckSubscriptionExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier les abonnements expirants et envoyer des notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Vérification des abonnements...');

        // 1. Expire aujourd'hui ou déjà expiré
        $expired = \App\Models\Vendeur::where('subscription_expires_at', '<=', now())
            ->whereNotNull('subscription_expires_at')
            ->get();

        foreach ($expired as $vendor) {
            /** @var \App\Models\Vendeur $vendor */
            $freePlan = \App\Models\SubscriptionPlan::where('price', 0)->first();
            
            // Revert to free plan
            $vendor->update([
                'subscription_plan_id' => $freePlan->id,
                'subscription_expires_at' => null
            ]);

            $vendor->user->notify(new \App\Notifications\SubscriptionExpired($vendor));
            $this->warn("Abonnement expiré pour: {$vendor->nom_commercial}");
        }

        // 2. Expire dans 3 jours
        $in3Days = \App\Models\Vendeur::whereDate('subscription_expires_at', now()->addDays(3)->toDateString())->get();
        foreach ($in3Days as $vendor) {
            $vendor->user->notify(new \App\Notifications\SubscriptionExpiringSoon($vendor, 3));
        }

        // 3. Expire dans 1 jour
        $in1Day = \App\Models\Vendeur::whereDate('subscription_expires_at', now()->addDays(1)->toDateString())->get();
        foreach ($in1Day as $vendor) {
            $vendor->user->notify(new \App\Notifications\SubscriptionExpiringSoon($vendor, 1));
        }

        $this->info('Vérification terminée.');
    }
}
