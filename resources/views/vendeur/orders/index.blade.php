@extends('layouts.vendor')

@section('title', 'Gestion des Commandes')
@section('page_title', 'Gestion des Commandes')

@section('content')
<div class="space-y-6 sm:space-y-8 animate-in fade-in duration-700">
    
    {{-- ── Statistics Summary ── --}}
    {{-- ── Filters, Stats & Search Bar ── --}}
    <div class="bg-white dark:bg-gray-900 p-2 sm:p-3 rounded-[2rem] sm:rounded-full border border-gray-100 dark:border-gray-800 shadow-sm flex flex-col lg:flex-row items-center justify-between gap-4">
        {{-- Status Pills with Counts --}}
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar w-full lg:w-auto px-1">
            <a href="{{ vendor_route('vendeur.slug.orders.index') }}" 
               class="shrink-0 px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2 {{ !$status ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-lg' : 'bg-gray-50 dark:bg-gray-800 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
               <span>Toutes</span>
               <span class="px-1.5 py-0.5 rounded-md {{ !$status ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500' }}">{{ $stats['total'] }}</span>
            </a>
            
            @php
                $statusConfig = [
                    'en_attente' => ['label' => 'Nouvelles', 'color' => 'orange', 'count' => $stats['en_attente']],
                    'en_preparation' => ['label' => 'En Cours', 'color' => 'blue', 'count' => $stats['en_preparation']],
                    'pret' => ['label' => 'Prêtes', 'color' => 'amber', 'count' => $stats['pret']],
                    'termine' => ['label' => 'Livrées', 'color' => 'green', 'count' => $stats['termine']],
                ];
            @endphp

            @foreach($statusConfig as $key => $cfg)
                <a href="{{ vendor_route('vendeur.slug.orders.index', ['status' => $key]) }}" 
                   class="shrink-0 px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2 
                   {{ $status === $key ? 'bg-'.$cfg['color'].'-600 text-white shadow-lg' : 'bg-gray-50 dark:bg-gray-800 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                   <span>{{ $cfg['label'] }}</span>
                   <span class="px-1.5 py-0.5 rounded-md {{ $status === $key ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500' }}">{{ $cfg['count'] }}</span>
                </a>
            @endforeach
        </div>

        {{-- Search Form --}}
        <form action="{{ vendor_route('vendeur.slug.orders.index') }}" method="GET" class="relative w-full lg:w-64">
            @if($status) <input type="hidden" name="status" value="{{ $status }}"> @endif
            <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher..." 
                   class="w-full pl-9 pr-4 py-2.5 bg-gray-50 dark:bg-gray-800 border-none rounded-full text-[10px] font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 transition-all">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </form>
    </div>

    {{-- ── Desktop Table View ── --}}
    <div class="hidden lg:block bg-white dark:bg-gray-900 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-gray-800/30 border-b border-gray-50 dark:border-gray-800">
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Commande</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Client</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Articles</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Total</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Statut</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($orders as $order)
                    <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition-all">
                        <td class="px-8 py-6">
                            <span class="text-xs font-black text-gray-900 dark:text-white block">#{{ $order->numero_commande }}</span>
                            <span class="text-[9px] font-bold text-gray-400 uppercase mt-1">{{ $order->date_commande->format('H:i d/m') }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center text-[10px] font-black text-gray-400 uppercase">
                                    {{ substr($order->client->name ?? $order->nom_complet_client ?? 'A', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-xs font-black text-gray-900 dark:text-white leading-none">{{ $order->client->name ?? $order->nom_complet_client ?? 'Anonyme' }}</p>
                                    <p class="text-[9px] font-bold text-gray-400 mt-1 uppercase">{{ $order->telephone_client ?? $order->client->telephone ?? '---' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-wrap gap-1 max-w-[200px]">
                                @foreach($order->lignes as $ligne)
                                    <span class="px-2 py-1 bg-gray-50 dark:bg-gray-800 text-[9px] font-bold text-gray-600 dark:text-gray-400 rounded-md border border-gray-100 dark:border-gray-700">
                                        {{ $ligne->quantite }}x {{ Str::limit($ligne->nom_plat_snapshot, 15) }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-sm font-black text-gray-900 dark:text-white">{{ number_format($order->montant_total, 0) }} FCFA</p>
                            <span class="text-[9px] font-black uppercase text-orange-600 dark:text-orange-500">{{ $order->mode_paiement_prevu }}</span>
                        </td>
                        <td class="px-8 py-6">
                            @include('partials.order-status-badge', ['statut' => $order->statut])
                        </td>
                        <td class="px-8 py-6 text-right">
                            @include('partials.order-actions', ['order' => $order])
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="6" class="px-8 py-20 text-center text-gray-400 text-xs font-bold italic">Aucune commande.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Mobile/Tablet Card View ── --}}
    <div class="lg:hidden space-y-4">
        @forelse($orders as $order)
            <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm p-4 sm:p-5 flex flex-col gap-4">
                {{-- Header Card --}}
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center text-xs font-black text-gray-400">
                            {{ substr($order->client->name ?? $order->nom_complet_client ?? 'A', 0, 1) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-black text-gray-900 dark:text-white">#{{ $order->numero_commande }}</span>
                                @include('partials.order-status-badge', ['statut' => $order->statut, 'small' => true])
                            </div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase mt-0.5">{{ $order->client->name ?? $order->nom_complet_client ?? 'Anonyme' }} • {{ $order->date_commande->format('H:i') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-gray-900 dark:text-white">{{ number_format($order->montant_total, 0) }}</p>
                        <p class="text-[8px] font-black text-orange-600 dark:text-orange-500 uppercase tracking-tighter">FCFA</p>
                    </div>
                </div>

                {{-- Articles --}}
                <div class="flex flex-wrap gap-1.5 py-3 border-y border-gray-50 dark:border-gray-800">
                    @foreach($order->lignes as $ligne)
                        <span class="px-2 py-1 bg-gray-50 dark:bg-gray-800 text-[9px] font-bold text-gray-600 dark:text-gray-400 rounded-lg">
                            {{ $ligne->quantite }}x {{ Str::limit($ligne->nom_plat_snapshot, 20) }}
                        </span>
                    @endforeach
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        @if($order->telephone_client ?? $order->client->telephone)
                            <a href="tel:{{ $order->telephone_client ?? $order->client->telephone }}" class="w-9 h-9 flex items-center justify-center bg-green-50 dark:bg-green-900/20 text-green-600 rounded-xl">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 005.456 5.456l.773-1.548a1 1 0 011.06-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
                            </a>
                        @endif
                        <button @click="$dispatch('open-chat-modal', { orderId: {{ $order->id_commande }} })"
                                class="relative w-9 h-9 flex items-center justify-center bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            @if($order->unread_messages_count > 0)
                                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-600 text-white text-[8px] font-black rounded-full flex items-center justify-center">{{ $order->unread_messages_count }}</span>
                            @endif
                        </button>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <button @click="$dispatch('open-order-modal', { order: {{ Js::from(['numero' => $order->numero_commande, 'date' => $order->date_commande->format('d/m/Y H:i'), 'client' => $order->client->name ?? $order->nom_complet_client ?? 'Anonyme', 'total' => $order->montant_total, 'statut' => $order->statut, 'lignes' => $order->lignes]) }} })"
                                class="px-4 py-2 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-[10px] font-black uppercase rounded-xl tracking-widest transition-all active:scale-95">
                            Détails
                        </button>
                        
                        <form action="{{ vendor_route('vendeur.slug.orders.status', $order->id_commande) }}" method="POST" class="flex gap-1.5 focus-within:outline-none">
                            @csrf @method('PATCH')
                            @if($order->statut === 'en_attente')
                                <button type="submit" name="statut" value="en_preparation" class="px-4 py-2 bg-blue-600 text-white text-[10px] font-black uppercase rounded-xl tracking-widest shadow-lg shadow-blue-500/20 active:scale-95">Accepter</button>
                            @elseif($order->statut === 'en_preparation')
                                <button type="submit" name="statut" value="pret" class="px-4 py-2 bg-amber-500 text-white text-[10px] font-black uppercase rounded-xl tracking-widest shadow-lg shadow-amber-500/20 active:scale-95">Prêt</button>
                            @elseif($order->statut === 'pret')
                                <button type="submit" name="statut" value="termine" class="px-4 py-2 bg-green-600 text-white text-[10px] font-black uppercase rounded-xl tracking-widest shadow-lg shadow-green-500/20 active:scale-95">Livré</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="py-20 text-center text-gray-400 font-bold italic text-xs">Aucune commande pour le moment.</div>
        @endforelse
    </div>

    {{-- ── Pagination ── --}}
    <div class="flex justify-between items-center bg-white dark:bg-gray-900 p-4 sm:p-6 rounded-[2rem] border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="hidden sm:block text-[9px] font-black text-gray-400 uppercase tracking-widest">
            Affichage {{ $orders->firstItem() }}-{{ $orders->lastItem() }} sur {{ $orders->total() }}
        </p>
        <div class="w-full sm:w-auto overflow-hidden">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    </div>
</div>

{{-- Modals remain the same but could be improved too if needed --}}
@include('vendeur.orders.modals')

@endsection
