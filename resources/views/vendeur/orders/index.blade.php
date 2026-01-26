@extends('layouts.vendor')

@section('title', 'Gestion des Commandes')
@section('page_title', 'Gestion des Commandes')

@section('content')
<div class="space-y-8 animate-in fade-in duration-700">
    
    <!-- Dashboard Stats Summary -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col items-center justify-center text-center">
            <span class="text-2xl font-black text-gray-900">{{ $stats['total'] }}</span>
            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">Toutes</span>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col items-center justify-center text-center border-l-4 border-l-orange-500">
            <span class="text-2xl font-black text-orange-600">{{ $stats['en_attente'] }}</span>
            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">Reçue</span>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col items-center justify-center text-center border-l-4 border-l-blue-500">
            <span class="text-2xl font-black text-blue-600">{{ $stats['en_preparation'] }}</span>
            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">Préparation</span>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col items-center justify-center text-center border-l-4 border-l-orange-400">
            <span class="text-2xl font-black text-orange-400">{{ $stats['pret'] }}</span>
            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">Prête</span>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col items-center justify-center text-center border-l-4 border-l-green-500">
            <span class="text-2xl font-black text-green-600">{{ $stats['termine'] }}</span>
            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">Livre</span>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white p-4 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
        <!-- Status Pills -->
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar max-w-full">
            <a href="{{ vendor_route('vendeur.slug.orders.index') }}" 
               class="px-5 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all {{ !$status ? 'bg-gray-900 text-white shadow-lg' : 'bg-gray-50 text-gray-400 hover:bg-gray-100' }}">
               Toutes
            </a>
            @foreach(['en_attente' => 'Reçue', 'en_preparation' => 'Préparation', 'pret' => 'Prête', 'termine' => 'Livre', 'annule' => 'Annulée'] as $key => $label)
                <a href="{{ vendor_route('vendeur.slug.orders.index', ['status' => $key]) }}" 
                   class="px-5 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all 
                   {{ $status === $key ? 'bg-orange-600 text-white shadow-lg' : 'bg-gray-50 text-gray-400 hover:bg-gray-100' }}">
                   {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- Search Form -->
        <form action="{{ vendor_route('vendeur.slug.orders.index') }}" method="GET" class="relative w-full md:w-80">
            @if($status) <input type="hidden" name="status" value="{{ $status }}"> @endif
            <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher #CMD ou Client..." 
                   class="w-full pl-12 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-[11px] font-bold text-gray-900 focus:ring-2 focus:ring-orange-500 transition-all">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-50">
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Commande</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Client</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Articles</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Total</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Statut</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                    <tr class="group hover:bg-gray-50/50 transition-all">
                        <td class="px-8 py-6">
                            <span class="text-xs font-black text-gray-900 block">#{{ $order->numero_commande }}</span>
                            <span class="text-[9px] font-bold text-gray-400 uppercase mt-1">{{ $order->date_commande->format('H:i d/m') }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center text-[10px] font-black text-gray-400 uppercase">
                                    {{ substr($order->client->name ?? $order->nom_complet_client ?? 'A', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-xs font-black text-gray-900 leading-none">{{ $order->client->name ?? $order->nom_complet_client ?? 'Anonyme' }}</p>
                                    <p class="text-[9px] font-bold text-gray-400 mt-1 uppercase">{{ $order->telephone_client ?? $order->client->telephone ?? '---' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-wrap gap-1 max-w-[200px]">
                                @foreach($order->lignes as $ligne)
                                    <span class="px-2 py-1 bg-gray-50 text-[9px] font-bold text-gray-600 rounded-md border border-gray-100">
                                        {{ $ligne->quantite }}x {{ Str::limit($ligne->nom_plat_snapshot, 15) }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-sm font-black text-gray-900">{{ number_format($order->montant_total, 0) }} FCFA</p>
                            <span class="text-[9px] font-black uppercase text-orange-600">{{ $order->mode_paiement_prevu }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border
                                @if($order->statut == 'en_attente') bg-orange-50 text-orange-600 border-orange-100
                                @elseif($order->statut == 'en_preparation') bg-blue-50 text-blue-600 border-blue-100
                                @elseif($order->statut == 'pret') bg-amber-50 text-amber-600 border-amber-100
                                @elseif($order->statut == 'termine') bg-green-50 text-green-600 border-green-100
                                @else bg-gray-50 text-gray-400 border-gray-100 @endif">
                                @php
                                    $statusLabels = [
                                        'en_attente' => 'Reçue',
                                        'en_preparation' => 'Préparation',
                                        'pret' => 'Prête',
                                        'termine' => 'Livre',
                                        'annule' => 'Annulée'
                                    ];
                                @endphp
                                {{ $statusLabels[$order->statut] ?? $order->statut }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="inline-flex gap-2">
                                <!-- Chat Button -->
                                <button @click="$dispatch('open-chat-modal', { orderId: {{ $order->id_commande }} })" 
                                        class="p-2 bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-600 hover:text-white transition-all border border-orange-100 relative" 
                                        title="Discuter avec le client">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    @if($order->unread_messages_count > 0)
                                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-600 text-white text-[8px] font-black rounded-full flex items-center justify-center animate-pulse">
                                            {{ $order->unread_messages_count }}
                                        </span>
                                    @endif
                                </button>

                                <!-- View Details Button -->
                                <button @click="$dispatch('open-order-modal', { order: {{ Js::from([
                                    'numero' => $order->numero_commande,
                                    'date' => $order->date_commande->format('d/m/Y H:i'),
                                    'client' => $order->client->name ?? $order->nom_complet_client ?? 'Anonyme',
                                    'telephone' => $order->telephone_client ?? $order->client->telephone ?? '---',
                                    'adresse' => $order->adresse_livraison ?? 'Retrait sur place',
                                    'total' => $order->montant_total,
                                    'paiement' => $order->mode_paiement_prevu,
                                    'statut' => $order->statut,
                                    'lignes' => $order->lignes->map(fn($l) => [
                                        'nom' => $l->nom_plat_snapshot,
                                        'quantite' => $l->quantite,
                                        'prix' => $l->prix_unitaire,
                                        'options' => $l->options // Already cast to array
                                    ])
                                ]) }} })" 
                                        class="p-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-900 hover:text-white transition-all border border-gray-200" 
                                        title="Voir détails">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                
                                <form action="{{ vendor_route('vendeur.slug.orders.status', $order->id_commande) }}" method="POST" class="inline-flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    
                                    @if($order->statut === 'en_attente')
                                        <button type="submit" name="statut" value="en_preparation" class="p-2 bg-gray-900 text-white rounded-lg hover:bg-blue-600 transition-all shadow-sm" title="Préparation">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    @elseif($order->statut === 'en_preparation')
                                        <button type="submit" name="statut" value="pret" class="p-2 bg-blue-600 text-white rounded-lg hover:bg-orange-500 transition-all shadow-sm" title="Prête">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        </button>
                                    @elseif($order->statut === 'pret')
                                        <button type="submit" name="statut" value="termine" class="p-2 bg-orange-500 text-white rounded-lg hover:bg-green-600 transition-all shadow-sm" title="Livre">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                                        </button>
                                    @endif

                                    @if(!in_array($order->statut, ['termine', 'annule']))
                                        <button type="submit" name="statut" value="annule" onclick="return confirm('Annuler cette commande ?')" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all border border-red-100" title="Annuler">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    @endif
                                </form>

                                <!-- Assign Driver Button -->
                                @if($order->type_recuperation === 'livraison' && $order->statut !== 'termine' && $order->statut !== 'annule')
                                    <button @click="$dispatch('open-assign-modal', { orderId: {{ $order->id_commande }}, currentDriver: {{ $order->id_livreur ?? 'null' }} })"
                                            class="p-2 {{ $order->id_livreur ? 'bg-green-50 text-green-600 border-green-100' : 'bg-indigo-50 text-indigo-600 border-indigo-100' }} rounded-lg hover:{{ $order->id_livreur ? 'bg-green-600' : 'bg-indigo-600' }} hover:text-white transition-all border"
                                            title="{{ $order->id_livreur ? 'Changer de livreur' : 'Assigner un livreur' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-32 text-center flex-col items-center justify-center space-y-4">
                            <div class="w-20 h-20 bg-gray-50 mx-auto rounded-3xl flex items-center justify-center text-gray-200">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <p class="text-sm font-black text-gray-400 italic">Aucune commande trouvée.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Enhanced Pagination -->
    <div class="flex justify-between items-center bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
            Affichage de {{ $orders->firstItem() }}-{{ $orders->lastItem() }} sur {{ $orders->total() }} commandes
        </p>
        <div>
            {{ $orders->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div x-data="{ 
    open: false, 
    orderData: null,
    init() {
        this.$watch('open', value => {
            document.body.style.overflow = value ? 'hidden' : 'auto';
        });
    }
}" 
     @open-order-modal.window="orderData = $event.detail.order; open = true"
     x-show="open" 
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;">
    
    <!-- Overlay -->
    <div x-show="open" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm" 
         @click="open = false"></div>
    
    <!-- Modal Panel -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="relative bg-white rounded-[2.5rem] shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
            
            <template x-if="orderData">
                <div class="flex flex-col max-h-[90vh]">
                    <!-- Header -->
                    <div class="p-8 border-b border-gray-100 bg-gray-50 flex justify-between items-center sticky top-0 z-10">
                        <div>
                            <h2 class="text-2xl font-black text-gray-900" x-text="'Commande #' + orderData.numero"></h2>
                            <p class="text-xs font-bold text-gray-400 uppercase mt-1" x-text="orderData.date"></p>
                        </div>
                        <button @click="open = false" class="p-2 bg-white rounded-full hover:bg-gray-100 transition-all">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto p-8 space-y-8">
                        <!-- Client Info -->
                        <div class="bg-gray-50 rounded-2xl p-6 space-y-4">
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Informations Client</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Nom</p>
                                    <p class="text-sm font-bold text-gray-900" x-text="orderData.client"></p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Téléphone</p>
                                    <p class="text-sm font-bold text-gray-900" x-text="orderData.telephone"></p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Adresse</p>
                                    <p class="text-sm font-bold text-gray-900" x-text="orderData.adresse"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div>
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-4">Articles Commandés</h3>
                            <div class="space-y-3">
                                <template x-for="(ligne, index) in orderData.lignes" :key="index">
                                    <div class="bg-white border border-gray-100 rounded-xl p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex-1">
                                                <p class="text-sm font-black text-gray-900" x-text="ligne.quantite + 'x ' + ligne.nom"></p>
                                                <p class="text-xs font-bold text-gray-500 mt-1" x-text="new Intl.NumberFormat('fr-FR').format(ligne.prix) + ' FCFA'"></p>
                                            </div>
                                            <p class="text-sm font-black text-orange-600" x-text="new Intl.NumberFormat('fr-FR').format(ligne.prix * ligne.quantite) + ' FCFA'"></p>
                                        </div>
                                        
                                        <!-- Options -->
                                        <template x-if="ligne.options && ligne.options.length > 0">
                                            <div class="mt-3 pt-3 border-t border-gray-50 space-y-1">
                                                <template x-for="(optGroup, gIndex) in ligne.options" :key="gIndex">
                                                    <div class="flex items-center gap-2 text-xs">
                                                        <span class="font-bold text-gray-500" x-text="optGroup.groupe + ':'"></span>
                                                        <div class="flex flex-wrap gap-1">
                                                            <template x-for="(variant, vIndex) in optGroup.variantes" :key="vIndex">
                                                                <span class="px-2 py-0.5 bg-gray-50 text-gray-700 rounded text-[10px] font-semibold">
                                                                    <span x-text="variant.nom"></span>
                                                                    <template x-if="variant.quantite > 1">
                                                                        <span class="text-gray-400" x-text="' x' + variant.quantite"></span>
                                                                    </template>
                                                                    <span x-show="variant.prix > 0" class="text-orange-600" x-text="' +' + new Intl.NumberFormat('fr-FR').format(variant.prix * (variant.quantite || 1))"></span>
                                                                </span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Payment Info -->
                        <div class="bg-gray-900 text-white rounded-2xl p-6 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-black uppercase tracking-widest">Total</span>
                                <span class="text-2xl font-black" x-text="new Intl.NumberFormat('fr-FR').format(orderData.total) + ' FCFA'"></span>
                            </div>
                            <div class="flex justify-between items-center pt-4 border-t border-white/10">
                                <span class="text-xs font-bold text-white/60 uppercase">Mode de paiement</span>
                                <span class="text-xs font-black uppercase" x-text="orderData.paiement"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<!-- Chat Modal -->
<div x-data="{ 
    open: false, 
    orderId: null,
    init() {
        this.$watch('open', value => {
            document.body.style.overflow = value ? 'hidden' : 'auto';
        });
    }
}" 
     @open-chat-modal.window="orderId = $event.detail.orderId; open = true"
     x-show="open" 
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;">
    
    <!-- Overlay -->
    <div x-show="open" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm" 
         @click="open = false; orderId = null"></div>
    
    <!-- Modal Panel -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="relative bg-white rounded-[2.5rem] shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
            
            <template x-if="orderId">
                <div class="flex flex-col h-[80vh]">
                    <!-- Header -->
                    <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-black text-gray-900">Discussion avec le client</h2>
                            <p class="text-xs font-bold text-gray-400 uppercase mt-1">Commande <span x-text="'#' + orderId"></span></p>
                        </div>
                        <button @click="open = false; orderId = null" class="p-2 bg-white rounded-full hover:bg-gray-100 transition-all">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

            <div x-show="orderId" class="flex-1 overflow-hidden">
                <template x-if="orderId">
                    @include('partials.order-chat', ['orderId' => 'orderId'])
                </template>
            </div>
                </div>
            </template>
        </div>
    </div>
</div>


<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
</div>

<!-- Assign Driver Modal -->
<div x-data="{ 
    open: false, 
    orderId: null,
    currentDriver: null,
    init() {
        this.$watch('open', value => {
            document.body.style.overflow = value ? 'hidden' : 'auto';
        });
    }
}" 
     @open-assign-modal.window="orderId = $event.detail.orderId; currentDriver = $event.detail.currentDriver; open = true"
     x-show="open" 
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;">
    
    <!-- Overlay -->
    <div x-show="open" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm" 
         @click="open = false; orderId = null"></div>
    
    <!-- Modal Panel -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="relative bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden">
            
            <div class="flex flex-col">
                <!-- Header -->
                <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-black text-gray-900">Assigner un livreur</h2>
                        <p class="text-xs font-bold text-gray-400 uppercase mt-1">Commande <span x-text="'#' + orderId"></span></p>
                    </div>
                    <button @click="open = false; orderId = null" class="p-2 bg-white rounded-full hover:bg-gray-100 transition-all">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-8">
                    @if($acceptedDrivers->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-sm text-gray-500 mb-4">Vous n'avez aucun livreur accepté pour le moment.</p>
                            <a href="{{ vendor_route('vendeur.slug.delivery.index') }}" class="text-xs font-black text-orange-600 uppercase tracking-widest hover:underline">Gérer mes livreurs &rarr;</a>
                        </div>
                    @else
                        <form :action="'{{ route('vendeur.slug.delivery.assign', ['vendor_slug' => $vendeur->slug, 'id' => ':id']) }}'.replace(':id', orderId)" method="POST" class="space-y-6">
                            @csrf
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3 ml-1">Choisir un partenaire livreur</label>
                                <div class="grid grid-cols-1 gap-3 overflow-y-auto max-h-60 custom-scrollbar">
                                    @foreach($acceptedDrivers as $driver)
                                        <label class="relative flex items-center gap-4 p-4 border-2 rounded-2xl cursor-pointer transition-all hover:bg-gray-50"
                                               :class="currentDriver == {{ $driver->id_user }} ? 'border-orange-500 bg-orange-50/30' : 'border-gray-100'">
                                            <input type="radio" name="id_livreur" value="{{ $driver->id_user }}" class="hidden" x-model="currentDriver">
                                            <img src="{{ $driver->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($driver->user->name) }}" class="w-10 h-10 rounded-xl object-cover">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-black text-gray-900 truncate">{{ $driver->user->name }}</p>
                                                <p class="text-[10px] text-gray-400 font-bold">{{ $driver->user->telephone }}</p>
                                            </div>
                                            <div x-show="currentDriver == {{ $driver->id_user }}" class="text-orange-600">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit" class="w-full py-4 bg-gray-900 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-black transition-all shadow-xl">
                                Valider l'assignation
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
