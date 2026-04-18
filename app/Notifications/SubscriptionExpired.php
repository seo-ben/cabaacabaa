<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpired extends Notification
{
    use Queueable;

    protected $vendeur;

    /**
     * Create a new notification instance.
     */
    public function __construct($vendeur)
    {
        $this->vendeur = $vendeur;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('CabaaCabaa : Votre abonnement a expiré')
                    ->greeting("Bonjour {$this->vendeur->nom_commercial},")
                    ->line("Votre abonnement est arrivé à son terme.")
                    ->line('Vos avantages premium ont été suspendus et votre boutique est repassée en mode gratuit.')
                    ->action('Renouveler mon abonnement', route('vendeur.slug.dashboard', ['vendor_slug' => $this->vendeur->slug]))
                    ->line('Renouvelez dès aujourd\'hui pour retrouver toutes vos fonctionnalités.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'subscription_expired',
            'title' => 'Abonnement expiré',
            'message' => "Votre abonnement a expiré. Vous êtes repassé en plan gratuit.",
            'vendor_slug' => $this->vendeur->slug,
        ];
    }
}
