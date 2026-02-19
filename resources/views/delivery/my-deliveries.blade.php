@extends('layouts.app')

@section('title', 'Mes Livraisons')

@section('content')

{{-- ==================== MOBILE VIEW (< lg) ==================== --}}
<div class="block lg:hidden bg-gray-50 dark:bg-slate-950 min-h-screen pb-28">

    {{-- Mobile Header --}}
    <div class="relative bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 overflow-hidden" style="min-height:200px;">
        {{-- Decorative blobs --}}
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-orange-500/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-red-500/10 rounded-full blur-2xl"></div>

        {{-- Header content --}}
        <div class="relative z-10 px-5 pt-12 pb-8">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tableau de bord</p>
                    <h1 class="text-2xl font-black text-white tracking-tight">Mes Livraisons</h1>
                    <p class="text-xs text-gray-400 font-medium mt-1">Commandes assignées à vous</p>
                </div>
                {{-- Live badge --}}
                <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-xl">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                    <span class="text-[9px] font-black text-green-400 uppercase tracking-widest">Live</span>
                </div>
            </div>

            {{-- Stats row --}}
            <div class="grid grid-cols-3 gap-3 mt-2">
                @php
                    $total = $deliveries->total();
                    $enCours = $deliveries->where('statut', '!=', 'livree')->count();
                    $terminees = $deliveries->where('statut', 'livree')->count();
                @endphp
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-3 text-center border border-white/10">
                    <div class="text-xl font-black text-white">{{ $total }}</div>
                    <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-0.5">Total</div>
                </div>
                <div class="bg-orange-500/20 rounded-2xl p-3 text-center border border-orange-500/30">
                    <div class="text-xl font-black text-orange-400">{{ $enCours }}</div>
                    <div class="text-[9px] font-black text-orange-400 uppercase tracking-widest mt-0.5">En cours</div>
                </div>
                <div class="bg-green-500/20 rounded-2xl p-3 text-center border border-green-500/30">
                    <div class="text-xl font-black text-green-400">{{ $terminees }}</div>
                    <div class="text-[9px] font-black text-green-400 uppercase tracking-widest mt-0.5">Terminées</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Content area --}}
    <div class="px-4 -mt-2 space-y-3">

        @if($deliveries->isEmpty())
            {{-- Empty state --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-10 text-center border border-gray-100 dark:border-slate-800 shadow-sm mt-4">
                <div class="w-16 h-16 bg-orange-50 dark:bg-orange-900/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <h3 class="text-lg font-black text-gray-900 dark:text-white mb-2">Aucune livraison assignée</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium leading-relaxed">Dès qu'un vendeur vous assignera une commande, elle apparaîtra ici.</p>
                <a href="{{ route('delivery.index') }}"
                   class="inline-flex items-center gap-2 mt-5 px-5 py-3 bg-gradient-to-r from-red-600 to-orange-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-red-500/25 active:scale-95 transition-all">
                    Voir les opportunités
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        @else
            @foreach($deliveries as $commande)
            {{-- Delivery Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">

                {{-- Card Header --}}
                <div class="flex items-center justify-between px-4 pt-4 pb-3 border-b border-gray-50 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        {{-- Status dot --}}
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center
                            @if($commande->statut === 'livree') bg-green-50 dark:bg-green-900/20
                            @elseif($commande->statut === 'pret_pour_livraison') bg-blue-50 dark:bg-blue-900/20
                            @else bg-orange-50 dark:bg-orange-900/20 @endif">
                            @if($commande->statut === 'livree')
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @elseif($commande->statut === 'pret_pour_livraison')
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            @else
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">#{{ $commande->numero_commande }}</p>
                            <span class="text-[10px] font-black
                                @if($commande->statut === 'livree') text-green-600 dark:text-green-400
                                @elseif($commande->statut === 'pret_pour_livraison') text-blue-600 dark:text-blue-400
                                @else text-orange-600 dark:text-orange-400 @endif">
                                {{ Str::title(str_replace('_', ' ', $commande->statut)) }}
                            </span>
                        </div>
                    </div>
                    <p class="text-[9px] font-bold text-gray-400">{{ $commande->date_commande->format('d/m · H:i') }}</p>
                </div>

                {{-- Card Body --}}
                <div class="px-4 py-3">
                    {{-- Vendor --}}
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-red-50 to-orange-50 dark:from-slate-700 dark:to-slate-600 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Vendeur</p>
                            <p class="text-sm font-black text-gray-900 dark:text-white leading-tight">{{ $commande->vendeur->nom_commercial }}</p>
                        </div>
                    </div>

                    {{-- Client info --}}
                    <div class="bg-orange-50/60 dark:bg-orange-900/10 rounded-2xl p-3 space-y-2 border border-orange-100/50 dark:border-orange-800/20">
                        {{-- Client name --}}
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <span class="text-xs font-black text-gray-900 dark:text-white truncate">{{ $commande->client->name ?? $commande->nom_complet_client }}</span>
                        </div>
                        {{-- Address --}}
                        <div class="flex items-start gap-2">
                            <div class="w-5 h-5 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400 italic leading-tight">{{ $commande->adresse_livraison ?? 'Retrait boutique' }}</span>
                        </div>
                        {{-- Phone (tappable) --}}
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            @php $phone = $commande->telephone_client ?? ($commande->client->telephone ?? null); @endphp
                            @if($phone)
                                <a href="tel:{{ $phone }}" class="text-xs font-black text-orange-600 dark:text-orange-400 active:text-orange-800 transition-colors">
                                    {{ $phone }}
                                </a>
                            @else
                                <span class="text-xs font-bold text-gray-400 italic">Non renseigné</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Card Actions --}}
                <div class="px-4 pb-4 flex gap-2">
                    {{-- Track --}}
                    <a href="{{ route('orders.track', ['code' => $commande->numero_commande]) }}"
                       class="flex-1 py-3 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest text-center border border-gray-100 dark:border-slate-700 active:scale-95 transition-all flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        Suivre
                    </a>
                    {{-- Complete --}}
                    @if($commande->statut !== 'livree')
                        <form action="{{ route('delivery.complete', $commande->id_commande) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit"
                                    class="w-full py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-green-500/25 active:scale-95 transition-all flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Terminé
                            </button>
                        </form>
                    @else
                        <div class="flex-1 py-3 bg-green-50 dark:bg-green-900/20 rounded-2xl text-[10px] font-black uppercase tracking-widest text-center text-green-600 dark:text-green-400 border border-green-100 dark:border-green-900/30 flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Livrée
                        </div>
                    @endif
                </div>
            </div>
            @endforeach

            {{-- Pagination --}}
            <div class="pt-2">
                {{ $deliveries->links() }}
            </div>
        @endif
    </div>
</div>
{{-- ==================== END MOBILE VIEW ==================== --}}


{{-- ==================== DESKTOP VIEW (>= lg) ==================== --}}
<div class="hidden lg:block bg-gray-50 dark:bg-gray-950 py-16 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-16">
            <div>
                <h1 class="text-4xl font-display font-black text-gray-900 dark:text-white mb-4 tracking-tight">Mes Livraisons</h1>
                <p class="text-lg text-gray-600 dark:text-gray-400">Gérez et suivez toutes les commandes qui vous sont assignées.</p>
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
                    <div class="bg-white dark:bg-gray-900 rounded-[2rem] p-8 shadow-xl border border-gray-100 dark:border-gray-800 flex flex-col lg:flex-row gap-8 items-start lg:items-center">
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
                                <div class="flex items-center gap-2"><svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg><span class="text-xs font-black text-gray-900 dark:text-white">{{ $commande->client->name ?? $commande->nom_complet_client }}</span></div>
                                <div class="flex items-center gap-2"><svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg><span class="text-xs font-medium text-gray-600 dark:text-gray-400 italic">{{ $commande->adresse_livraison ?? 'Retrait boutique' }}</span></div>
                                <div class="flex items-center gap-2"><svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg><span class="text-xs font-black text-orange-600">{{ $commande->telephone_client ?? $commande->client->telephone }}</span></div>
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
            <div class="mt-12">{{ $deliveries->links() }}</div>
        @endif
    </div>
</div>
{{-- ==================== END DESKTOP VIEW ==================== --}}

@endsection
