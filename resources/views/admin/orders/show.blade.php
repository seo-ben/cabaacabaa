@extends('layouts.admin')

@section('title', 'Détails Commande #' . $order->numero_commande)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-gray-400">
        <a href="{{ route('admin.orders.index') }}" class="hover:text-red-600 transition">Commandes</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-900">#{{ $order->numero_commande }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Left Column: Details -->
        <div class="flex-1 space-y-6">
            <!-- Order Header Card -->
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 bg-red-50 dark:bg-red-900/10 rounded-3xl flex items-center justify-center text-red-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Commande #{{ $order->numero_commande }}</h2>
                            <p class="text-gray-500 text-sm font-medium">Passée le {{ $order->date_commande->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                    <div>
                        @php
                            $statusClasses = [
                                'en_attente' => 'bg-orange-50 text-orange-600 border-orange-100',
                                'en_preparation' => 'bg-blue-50 text-blue-600 border-blue-100',
                                'pret' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                'termine' => 'bg-green-50 text-green-600 border-green-100',
                                'annule' => 'bg-red-50 text-red-600 border-red-100',
                            ];
                        @endphp
                        <span class="px-6 py-2.5 rounded-2xl border text-xs font-black uppercase tracking-[0.2em] {{ $statusClasses[$order->statut] ?? 'bg-gray-50' }}">
                            {{ strtoupper(str_replace('_', ' ', $order->statut)) }}
                        </span>
                    </div>
                </div>

                <!-- Timeline / Process -->
                <div class="mt-12 grid grid-cols-1 md:grid-cols-4 gap-4 relative">
                    <div class="absolute top-6 left-0 w-full h-px bg-gray-100 hidden md:block"></div>
                    
                    <div class="relative z-10 text-center md:text-left">
                        <div class="w-12 h-12 rounded-full mx-auto md:mx-0 mb-3 flex items-center justify-center {{ $order->date_commande ? 'bg-red-600 text-white shadow-glow' : 'bg-gray-100 text-gray-400' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-900">Passée</p>
                        <p class="text-[9px] text-gray-400 mt-1">{{ $order->date_commande->format('H:i') }}</p>
                    </div>

                    <div class="relative z-10 text-center md:text-left">
                        <div class="w-12 h-12 rounded-full mx-auto md:mx-0 mb-3 flex items-center justify-center {{ $order->heure_preparation_debut ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-400' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-900">En cuisine</p>
                        <p class="text-[9px] text-gray-400 mt-1">{{ $order->heure_preparation_debut ? $order->heure_preparation_debut->format('H:i') : '--:--' }}</p>
                    </div>

                    <div class="relative z-10 text-center md:text-left">
                        <div class="w-12 h-12 rounded-full mx-auto md:mx-0 mb-3 flex items-center justify-center {{ $order->heure_prete ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-400' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-900">Prête</p>
                        <p class="text-[9px] text-gray-400 mt-1">{{ $order->heure_prete ? $order->heure_prete->format('H:i') : '--:--' }}</p>
                    </div>

                    <div class="relative z-10 text-center md:text-left">
                        <div class="w-12 h-12 rounded-full mx-auto md:mx-0 mb-3 flex items-center justify-center {{ $order->statut == 'termine' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-400' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-900">Livrée</p>
                        <p class="text-[9px] text-gray-400 mt-1">{{ $order->heure_recuperation_effective ? $order->heure_recuperation_effective->format('H:i') : '--:--' }}</p>
                    </div>
                </div>
            </div>

            <!-- Items Card -->
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between">
                    <h3 class="text-lg font-black text-gray-900 tracking-tight">Articles commandés</h3>
                    <span class="px-4 py-1.5 bg-gray-50 rounded-xl text-[10px] font-black text-gray-500 uppercase tracking-widest">
                        {{ $order->lignes->count() }} Article(s)
                    </span>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($order->lignes as $ligne)
                    <div class="p-8 flex items-center gap-6">
                        <img src="{{ $ligne->plat->image_principale ? asset('storage/' . $ligne->plat->image_principale) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=200&fit=crop' }}" 
                             class="w-20 h-20 rounded-3xl object-cover shadow-sm">
                        <div class="flex-1">
                            <h4 class="text-base font-black text-gray-900 leading-tight mb-1">{{ $ligne->plat->nom_plat }}</h4>
                            <p class="text-gray-400 text-xs font-medium">{{ $ligne->quantite }}x à {{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} F</p>
                            @if($ligne->options)
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @php $options = is_array($ligne->options) ? $ligne->options : json_decode($ligne->options, true); @endphp
                                    @foreach($options as $group => $sel)
                                        @foreach($sel as $varName => $price)
                                            <span class="px-2 py-0.5 bg-gray-50 border border-gray-100 rounded-md text-[9px] font-bold text-gray-500 uppercase tracking-tighter">{{ $varName }}</span>
                                        @endforeach
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-black text-gray-900">{{ number_format($ligne->prix_total, 0, ',', ' ') }} F</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                <!-- Totals -->
                <div class="bg-gray-50/50 p-8 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="font-medium text-gray-500">Sous-total plats</span>
                        <span class="font-black text-gray-900">{{ number_format($order->montant_plats, 0, ',', ' ') }} F</span>
                    </div>
                    @if($order->frais_service > 0)
                    <div class="flex justify-between text-sm">
                        <span class="font-medium text-gray-500">Frais de service & livraison</span>
                        <span class="font-black text-gray-900">{{ number_format($order->frais_service, 0, ',', ' ') }} F</span>
                    </div>
                    @endif
                    <div class="pt-4 border-t border-gray-200 flex justify-between items-center">
                        <span class="text-lg font-black text-gray-900 uppercase tracking-tighter">Total à régler</span>
                        <span class="text-2xl font-black text-red-600 tracking-tight">{{ number_format($order->montant_total, 0, ',', ' ') }} F</span>
                    </div>
                </div>
            </div>

            @if($order->instructions_speciales)
            <div class="p-8 bg-amber-50 rounded-[2rem] border border-amber-100">
                <p class="text-[10px] font-black uppercase text-amber-600 tracking-widest mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Notes spéciales
                </p>
                <p class="text-amber-900 text-sm font-medium leading-relaxed italic">{{ $order->instructions_speciales }}</p>
            </div>
            @endif
        </div>

        <!-- Right Column: Actors & Control -->
        <div class="w-full lg:w-96 space-y-6">
            <!-- Client Card -->
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-8">
                <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-6">Informations Client</h4>
                <div class="flex items-center gap-4 mb-6">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($order->client->name ?? 'User') }}&background=random" class="w-14 h-14 rounded-2xl shadow-sm">
                    <div>
                        <p class="text-base font-black text-gray-900">{{ $order->client->name ?? 'Client Inconnu' }}</p>
                        <p class="text-xs text-gray-500">{{ $order->client->email ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <p class="text-sm font-bold text-gray-700">{{ $order->client->phone ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Restaurant Card -->
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-8">
                <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-6">Restaurant</h4>
                <div class="flex items-center gap-4 mb-6">
                    @if($order->vendeur)
                        <img src="{{ $order->vendeur->image_principale ? asset('storage/' . $order->vendeur->image_principale) : 'https://ui-avatars.com/api/?name='.urlencode($order->vendeur->nom_commercial) }}" class="w-14 h-14 rounded-2xl shadow-sm object-cover">
                        <div>
                            <p class="text-base font-black text-gray-900">{{ $order->vendeur->nom_commercial }}</p>
                            <p class="text-xs text-slate-500">{{ $order->vendeur->zone ? $order->vendeur->zone->nom : 'Lomé' }}</p>
                        </div>
                    @else
                        <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center text-gray-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <p class="text-base font-black text-gray-900">Boutique Inconnue</p>
                            <p class="text-xs text-slate-500">N/A</p>
                        </div>
                    @endif
                </div>
                @if($order->vendeur)
                <a href="{{ route('admin.vendors.edit', $order->id_vendeur) }}" class="w-full py-3 bg-gray-50 text-gray-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-100 transition flex items-center justify-center gap-2">
                    Gérer le partenaire
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
                @endif
            </div>

            <!-- Admin Actions Card -->
            <div class="bg-slate-900 rounded-[2.5rem] shadow-xl p-8 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-red-600/10 rounded-full blur-2xl"></div>
                
                <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-8 relative z-10">Actions Administrateur</h4>
                
                <form action="{{ route('admin.orders.status', $order->id_commande) }}" method="POST" class="space-y-6 relative z-10">
                    @csrf @method('PATCH')
                    
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-3">Changer le statut de force</label>
                        <select name="statut" class="w-full bg-slate-800 border-none rounded-xl text-sm font-bold px-4 py-3 focus:ring-2 focus:ring-red-500">
                            <option value="en_attente" {{ $order->statut == 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="en_preparation" {{ $order->statut == 'en_preparation' ? 'selected' : '' }}>En préparation</option>
                            <option value="pret" {{ $order->statut == 'pret' ? 'selected' : '' }}>Prêt pour retrait</option>
                            <option value="termine" {{ $order->statut == 'termine' ? 'selected' : '' }}>Terminé / Livré</option>
                            <option value="annule" {{ $order->statut == 'annule' ? 'selected' : '' }}>Annuler la commande</option>
                        </select>
                    </div>

                    <div x-show="$el.closest('form').querySelector('select').value == 'annule'" class="mt-4">
                        <textarea name="raison_annulation" placeholder="Raison de l'annulation forcée..." class="w-full bg-slate-800 border-none rounded-xl text-xs font-medium px-4 py-3 focus:ring-2 focus:ring-red-500 mb-4">{{ $order->raison_annulation }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-4 bg-red-600 text-white rounded-xl text-[10px] font-black uppercase tracking-[0.2em] shadow-2xl shadow-red-600/20 hover:scale-105 active:scale-95 transition-all">
                        Appliquer le changement
                    </button>
                </form>

                @if($order->statut == 'termine')
                <div class="mt-8 pt-8 border-t border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-green-900/30 flex items-center justify-center text-green-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Paiement</p>
                            <p class="text-sm font-bold text-white">{{ $order->paiement_effectue ? 'Confirmé' : 'En attente' }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            @if($order->avis)
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-8">
                <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-6">Évaluation Client</h4>
                <div class="flex items-center gap-1 text-yellow-500 mb-4">
                    @for($i=0; $i<$order->avis->note; $i++) <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 1l2.6 6.3h6.4l-5.2 4 2 6.7-5.8-4.3-5.8 4.3 2-6.7-5.2-4h6.4z"/></svg> @endfor
                </div>
                <p class="text-sm text-gray-600 italic font-medium leading-relaxed">"{{ $order->avis->commentaire }}"</p>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .shadow-glow { filter: drop-shadow(0 0 8px rgba(239, 68, 68, 0.4)); }
</style>
@endsection
