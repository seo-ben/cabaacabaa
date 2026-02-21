@php
    $statusLabels = [
        'en_attente' => 'Reçue',
        'en_preparation' => 'Préparation',
        'pret' => 'Prête',
        'termine' => 'Livrée',
        'annule' => 'Annulée'
    ];
    $label = $statusLabels[$statut] ?? $statut;
    $isSmall = $small ?? false;
@endphp

<span class="{{ $isSmall ? 'px-2 py-0.5 text-[8px]' : 'px-3 py-1.5 text-[9px]' }} rounded-full font-black uppercase tracking-widest border
    @if($statut == 'en_attente') bg-orange-50 text-orange-600 border-orange-100 dark:bg-orange-900/20 dark:border-orange-900/30
    @elseif($statut == 'en_preparation') bg-blue-50 text-blue-600 border-blue-100 dark:bg-blue-900/20 dark:border-blue-900/30
    @elseif($statut == 'pret') bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-900/20 dark:border-amber-900/30
    @elseif($statut == 'termine') bg-green-50 text-green-600 border-green-100 dark:bg-green-900/20 dark:border-green-900/30
    @else bg-gray-50 text-gray-400 border-gray-100 dark:bg-gray-800 dark:border-gray-700 @endif">
    {{ $label }}
</span>
