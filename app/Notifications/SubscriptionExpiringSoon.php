<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiringSoon extends Notification
{
    use Queueable;

    protected $daysLeft;
    protected $vendeur;

    /**
     * Create a new notification instance.
     */
    public function __construct($vendeur, $daysLeft)
    {
        $this->vendeur = $vendeur;
        $this->daysLeft = $daysLeft;
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
        $message = ($this->daysLeft == 0) 
            ? "Votre abonnement expire AUJOURD'HUI !" 
            : "Votre abonnement expire dans {$this->daysLeft} jours.";

        return (new MailMessage)
                    ->subject('CabaaCabaa : Votre abonnement expire bientôt')
                    ->greeting("Bonjour {$this->vendeur->nom_commercial},")
                    ->line($message)
                    ->line('Renouvelez-le dès maintenant pour conserver vos avantages et votre visibilité sur la plateforme.')
                    ->action('Gérer mon abonnement', route('vendeur.slug.dashboard', ['vendor_slug' => $this->vendeur->slug]))
                    ->line('Merci de faire confiance à CabaaCabaa !');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'subscription_expiring',
            'title' => 'Abonnement bientôt expiré',
            'message' => "Votre abonnement expire dans {$this->daysLeft} jours. Pensez à le renouveler.",
            'vendor_slug' => $this->vendeur->slug,
        ];
    }
}
