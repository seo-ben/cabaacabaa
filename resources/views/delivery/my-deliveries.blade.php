@extends('layouts.app')

@section('title', 'Mes Livraisons')

@section('content')
<div class="bg-gray-50 dark:bg-gray-950 py-16 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-16">
            <div>
                <h1 class="text-4xl font-display font-black text-gray-900 dark:text-white mb-4 tracking-tight">
                    Mes Livraisons
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400">
                    Gérez et suivez toutes les commandes qui vous sont assignées.
                </p>
            </div>
        </div>

        @if($deliveries->isEmpty())
            <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] p-16 text-center shadow-xl border border-gray-100 dark:border-gray-800">
                <div class="w-24 h-24 bg-red-50 dark:bg-red-900/20 rounded-3xl flex items-center justify-center mx-auto mb-8 text-red-600">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-4">Aucune livraison en cours</h3>
                <p class="text-gray-500 dark:text-gray-400">Dès qu'un vendeur vous assignera une commande, elle apparaîtra ici.</p>
                <a href="{{ route('delivery.index') }}" class="inline-block mt-8 text-xs font-black uppercase tracking-widest text-red-600 hover:text-red-700 transition-colors">
                    Voir les opportunités &rarr;
                </a>
            </div>
        @else
            <div class="space-y-6">
                @foreach($deliveries as $commande)
                    <div class="bg-white dark:bg-gray-900 rounded-4xl p-8 shadow-xl border border-gray-100 dark:border-gray-800 flex flex-col lg:flex-row gap-8 items-start lg:items-center">
                        
                        <div class="shrink-0">
                            <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center text-gray-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-3 mb-2">
                                <span class="text-xs font-black uppercase tracking-widest text-gray-400">#{{ $commande->numero_commande }}</span>
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest
                                    @if($commande->statut === 'livree') bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400
                                    @elseif($commande->statut === 'pret_pour_livraison') bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400
                                    @else bg-orange-50 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400 @endif">
                                    {{ $commande->statut }}
                                </span>
                            </div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-1">{{ $commande->vendeur->nom_commercial }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-500 font-medium mb-3">Prévu le {{ $commande->date_commande->format('d/m/Y à H:i') }}</p>
                            
                            <div class="flex flex-col gap-2 p-4 bg-orange-50/50 dark:bg-orange-900/10 rounded-2xl border border-orange-100/50 dark:border-orange-800/30">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span class="text-xs font-black text-gray-900 dark:text-white">{{ $commande->client->name ?? $commande->nom_complet_client }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400 italic">{{ $commande->adresse_livraison ?? 'Retrait boutique' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <span class="text-xs font-black text-orange-600">{{ $commande->telephone_client ?? $commande->client->telephone }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col lg:flex-row gap-4 items-stretch lg:items-center">
                            <a href="{{ route('orders.track', ['code' => $commande->numero_commande]) }}" 
                               class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-gray-100 dark:hover:bg-gray-700 transition-all text-center">
                                Suivre
                            </a>
                            @if($commande->statut !== 'livree')
                                <form action="{{ route('delivery.complete', $commande->id_commande) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full px-6 py-3 bg-green-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-green-700 transition-all shadow-lg shadow-green-200 dark:shadow-none">
                                        Terminé
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $deliveries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
