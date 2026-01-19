<x-mail::message>
# Félicitations {{ $vendeur->user->name }} !

{{ $customMessage }}

Votre boutique **{{ $vendeur->nom_commercial }}** est maintenant vérifiée et active sur la plateforme. Vous pouvez commencer à gérer vos produits et commandes dès maintenant.

<x-mail::button :url="route('vendeur.slug.dashboard', $vendeur->slug)">
Accéder au Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
