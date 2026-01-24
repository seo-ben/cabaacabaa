@extends('layouts.vendor')

@section('title', 'Tableau de bord')
@section('page_title', 'Tableau de bord')

@section('content')
<div class="space-y-10">
    
    @if(!$hasCategories)
    <!-- Critical Setup Warning -->
    <div class="bg-red-600 rounded-2xl p-8 text-white shadow-2xl shadow-red-200 relative overflow-hidden group">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full group-hover:scale-110 transition duration-700"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 class="text-xl font-black">Configuration Incomplète !</h3>
                    <p class="text-sm opacity-80 font-medium mt-1">Vous devez d'abord configurer votre **Boutique** dans les paramètres avant de pouvoir ajouter des articles.</p>
                </div>
            </div>
            <a href="{{ vendor_route('vendeur.slug.settings.index') }}" class="px-8 py-4 bg-white text-red-600 rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-gray-100 transition-all shadow-xl active:scale-95 whitespace-nowrap">
                Configurer ma boutique
            </a>
        </div>
    </div>
    @endif
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Revenue -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-50 dark:bg-red-900/20 rounded-full scale-0 group-hover:scale-100 transition duration-700"></div>
            <div class="relative z-10 space-y-4">
                <div class="w-14 h-14 bg-red-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-red-200 dark:shadow-red-900/20">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-[11px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Chiffre d'Affaire</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white leading-tight">{{ number_format($totalSales, 0) }} FCFA</h3>
                </div>
                <div class="flex items-center gap-2 text-[10px] font-black uppercase text-green-500">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                    +12% vs mois dernier
                </div>
            </div>
        </div>

        <!-- Active Orders -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-orange-50 dark:bg-orange-900/20 rounded-full scale-0 group-hover:scale-100 transition duration-700"></div>
            <div class="relative z-10 space-y-4">
                <div class="w-14 h-14 bg-orange-500 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-orange-200 dark:shadow-orange-900/20">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <div>
                    <p class="text-[11px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Commandes Actives</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white leading-tight">{{ $activeOrders }}</h3>
                </div>
                <div class="flex items-center gap-2 text-[10px] font-black uppercase text-orange-500">
                    <span class="w-1.5 h-1.5 bg-orange-500 rounded-full animate-pulse"></span>
                    En cours de traitement
                </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-gray-50 dark:bg-gray-800 rounded-full scale-0 group-hover:scale-100 transition duration-700"></div>
            <div class="relative z-10 space-y-4">
                <div class="w-14 h-14 bg-gray-900 dark:bg-gray-700 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-gray-200 dark:shadow-none">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </div>
                <div>
                    <p class="text-[11px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Articles en Boutique</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white leading-tight">{{ $totalPlats }}</h3>
                </div>
                <a href="{{ vendor_route('vendeur.slug.plats.index') }}" class="inline-flex items-center gap-2 text-[10px] font-black uppercase text-red-600 dark:text-red-400 hover:gap-3 transition-all">
                    Gérer mes articles
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent Orders Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        <!-- Left: Recent Orders -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-2xl p-10 border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h3 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white">Dernières Commandes</h3>
                    <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-1">Les 5 plus récentes</p>
                </div>
                <a href="{{ vendor_route('vendeur.slug.orders.index') }}" class="text-[11px] font-black uppercase tracking-widest text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors">Tout voir</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl">
                        <tr>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Réf</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Client</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Montant</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Statut</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @foreach($recentOrders as $order)
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-6 text-sm font-black text-gray-900 dark:text-white">#{{ $order->numero_commande }}</td>
                            <td class="px-6 py-6">
                                <p class="text-sm font-bold text-gray-900 dark:text-white leading-none">{{ $order->client->name ?? 'Anonyme' }}</p>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold mt-1 uppercase">{{ $order->date_commande->format('H:i') }}</p>
                            </td>
                            <td class="px-6 py-6 text-sm font-black text-gray-900 dark:text-white">{{ number_format($order->montant_total, 0) }} FCFA</td>
                            <td class="px-6 py-6">
                                @php
                                    $statusColors = [
                                        'en_attente' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400 border-yellow-100 dark:border-yellow-900/30',
                                        'en_preparation' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border-blue-100 dark:border-blue-900/30',
                                        'pret' => 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border-green-100 dark:border-green-900/30',
                                        'termine' => 'bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-100 dark:border-gray-700',
                                        'annule' => 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border-red-100 dark:border-red-900/30',
                                    ];
                                @endphp
                                @php
                                    $statusLabels = [
                                        'en_attente' => 'Reçue',
                                        'en_preparation' => 'Préparation',
                                        'pret' => 'Prête',
                                        'termine' => 'Livre',
                                        'annule' => 'Annulée'
                                    ];
                                @endphp
                                <span class="px-3 py-1.5 rounded-xl border {{ $statusColors[$order->statut] ?? 'bg-gray-50 dark:bg-gray-800' }} text-[9px] font-black uppercase tracking-widest">
                                    {{ $statusLabels[$order->statut] ?? $order->statut }}
                                </span>
                            </td>
                            <td class="px-6 py-6">
                                <button class="p-2.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 hover:border-red-100 dark:hover:border-red-900/30 transition-all shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($recentOrders->isEmpty())
                <div class="py-20 text-center">
                    <p class="text-sm font-bold text-gray-400 dark:text-gray-500 italic">Aucune commande enregistrée pour le moment.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Right: Tips & Quick Actions -->
        <div class="space-y-10">
            <!-- Boutique Status -->
            <div class="bg-gray-900 dark:bg-gray-900/50 rounded-2xl p-10 text-white shadow-2xl shadow-gray-200 dark:shadow-none border border-transparent dark:border-gray-800">
                <h3 class="text-sm font-black uppercase tracking-[0.2em] opacity-40 mb-6">État de la Boutique</h3>
                <div class="flex items-center gap-6 mb-8">
                    @php $isOpen = $vendeur->actif; @endphp
                    <div class="relative">
                        <div class="w-16 h-16 rounded-[1.5rem] {{ $isOpen ? 'bg-green-500' : 'bg-red-500' }} flex items-center justify-center shadow-lg {{ $isOpen ? 'shadow-green-500/20' : 'shadow-red-500/20' }}">
                            @if($isOpen)
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                        </div>
                        <div class="absolute -top-1 -right-1 w-5 h-5 {{ $isOpen ? 'bg-green-400' : 'bg-red-400' }} rounded-full animate-ping opacity-75"></div>
                    </div>
                    <div>
                        <p class="text-xl font-black">{{ $isOpen ? 'Boutique Ouverte' : 'Boutique Fermée' }}</p>
                        <p class="text-[10px] font-bold opacity-40 uppercase tracking-widest mt-1">Actuellement visible par les clients</p>
                    </div>
                </div>
                <a href="{{ vendor_route('vendeur.slug.settings.index') }}" class="w-full inline-block text-center py-4 bg-white/10 hover:bg-white/20 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all">
                    Modifier mes horaires
                </a>
            </div>

            <!-- Ad Card -->
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/10 dark:to-orange-950/20 rounded-2xl p-10 border border-orange-200 dark:border-orange-900/30">
                <p class="text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest mb-4">Conseil Pro</p>
                <h4 class="text-xl font-black text-gray-900 dark:text-white leading-tight mb-4">Boostez votre visibilité !</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed font-medium mb-8">Ajoutez des photos de haute qualité à vos articles pour augmenter vos ventes de <strong>30%</strong>.</p>
                <a href="{{ vendor_route('vendeur.slug.plats.index') }}" class="px-6 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-orange-950/5 dark:shadow-none hover:bg-orange-600 hover:text-white dark:hover:bg-orange-600 transition-all">
                    Mettre à jour mes articles
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
