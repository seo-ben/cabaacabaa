@extends('layouts.admin')

@section('title', 'Suivi des Commandes')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Suivi Opérationnel</h1>
            <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mt-1">Analyse financière & logistique</p>
        </div>
        <div class="flex flex-wrap items-center gap-6">
            <div class="text-right">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Panier Moyen</p>
                <p class="text-lg font-black text-gray-900">{{ number_format($stats['avg_order'], 0, ',', ' ') }} F</p>
            </div>
            <div class="w-px h-10 bg-gray-100 hidden md:block"></div>
            <div class="text-right">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Commissions (Frais)</p>
                <p class="text-lg font-black text-blue-600">{{ number_format($stats['total_fees'], 0, ',', ' ') }} F</p>
            </div>
            <div class="w-px h-10 bg-gray-100 hidden md:block"></div>
            <div class="text-right">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Volume d'affaires</p>
                <p class="text-2xl font-black text-gray-900">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} F</p>
            </div>
            <button onclick="window.location.reload()" class="p-3 bg-white border border-gray-100 rounded-2xl hover:bg-gray-50 transition shadow-sm active:scale-95">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <a href="{{ route('admin.orders.index') }}" class="bg-white p-5 rounded-[2rem] border border-gray-100 shadow-sm hover:border-black transition flex flex-col justify-between {{ !$status ? 'ring-4 ring-gray-900/10 border-gray-900' : '' }}">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Toutes</p>
            <p class="text-2xl font-black text-gray-900">{{ $stats['total'] }}</p>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'en_attente']) }}" class="bg-white p-5 rounded-[2rem] border border-gray-100 shadow-sm hover:border-orange-500 transition flex flex-col justify-between {{ $status == 'en_attente' ? 'ring-4 ring-orange-500/10 border-orange-500' : '' }}">
            <p class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-3">En attente</p>
            <div class="space-y-1">
                <p class="text-2xl font-black text-gray-900">{{ $stats['en_attente']['count'] }}</p>
                <p class="text-[10px] font-bold text-gray-400 italic">{{ number_format($stats['en_attente']['sum'], 0, ',', ' ') }} F</p>
            </div>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'termine']) }}" class="bg-white p-5 rounded-[2rem] border border-gray-100 shadow-sm hover:border-green-500 transition flex flex-col justify-between {{ $status == 'termine' ? 'ring-4 ring-green-500/10 border-green-500' : '' }}">
            <p class="text-[10px] font-black text-green-500 uppercase tracking-widest mb-3">Validées</p>
            <div class="space-y-1">
                <p class="text-2xl font-black text-gray-900">{{ $stats['termine']['count'] }}</p>
                <p class="text-[10px] font-bold text-green-600 italic">{{ number_format($stats['termine']['sum'], 0, ',', ' ') }} F</p>
            </div>
        </a>
        <div class="bg-white p-5 rounded-[2rem] border border-gray-100 shadow-sm space-y-4 col-span-1">
            <div>
                <p class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-1">Préparation</p>
                <p class="text-xl font-black text-gray-900">{{ $stats['en_preparation'] }}</p>
            </div>
            <div class="pt-2 border-t border-gray-50">
                <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-1">Prêtes</p>
                <p class="text-xl font-black text-gray-900">{{ $stats['pret'] }}</p>
            </div>
        </div>
        <div class="bg-white p-5 rounded-[2rem] border border-gray-100 shadow-sm col-span-1 flex flex-col justify-center">
            <p class="text-[10px] font-black text-red-500 uppercase tracking-widest mb-1">Annulées</p>
            <p class="text-2xl font-black text-gray-900">{{ $stats['annule'] }}</p>
        </div>
        <div class="bg-slate-900 p-5 rounded-[2rem] text-white flex flex-col justify-center">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Ratio succès</p>
            <p class="text-2xl font-black">{{ $stats['total'] > 0 ? round(($stats['termine']['count'] / $stats['total']) * 100) : 0 }}%</p>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-[2.5rem] border border-gray-100 shadow-sm">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-col lg:flex-row gap-6">
            @if($status) <input type="hidden" name="status" value="{{ $status }}"> @endif
            
            <!-- Recherche textuelle -->
            <div class="flex-1">
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Recherche multicritères</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="N° Commande, Client ou Établissement..." 
                           class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 transition-all text-sm font-bold">
                    <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- Plage de dates -->
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-1 md:w-44">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Du</label>
                    <input type="date" name="date_start" value="{{ $date_start }}" 
                           class="w-full px-4 py-3 bg-gray-50 border border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 transition-all text-sm font-bold text-gray-600">
                </div>
                <div class="flex-1 md:w-44">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Au</label>
                    <input type="date" name="date_end" value="{{ $date_end }}" 
                           class="w-full px-4 py-3 bg-gray-50 border border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 transition-all text-sm font-bold text-gray-600">
                </div>
            </div>

            <div class="flex items-end gap-3">
                <button type="submit" class="px-8 py-3.5 bg-gray-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-gray-900/10 active:scale-95">Appliquer</button>
                @if($search || $status || $date_start || $date_end)
                    <a href="{{ route('admin.orders.index') }}" class="px-4 py-3.5 text-gray-500 hover:text-red-600 font-bold text-xs uppercase tracking-widest flex items-center">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">N° COMMANDE</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">DETAILS</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">ÉTABLISSEMENT</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">MONTANT</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">STATUT</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50/50 transition border-l-4 {{ $order->date_commande->isToday() ? 'border-red-500 bg-red-50/10' : 'border-transparent' }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-black text-gray-900">#{{ $order->numero_commande }}</span>
                                @if($order->date_commande->isToday())
                                    <span class="px-2 py-0.5 bg-red-600 text-white text-[8px] font-black uppercase tracking-tighter rounded-full">Aujourd'hui</span>
                                @endif
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold">{{ $order->date_commande->format('d M, H:i') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($order->client->name ?? 'User') }}&background=random" class="w-8 h-8 rounded-full">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $order->client->name ?? 'Client Inconnu' }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <p class="text-[10px] text-gray-500">{{ $order->type_recuperation == 'livraison' ? 'Livraison' : 'Emporter' }}</p>
                                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-tighter">{{ $order->lignes->sum('quantite') }} Articles</p>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-gray-900">{{ $order->vendeur->nom_commercial ?? 'Boutique Inconnue' }}</p>
                            <p class="text-[10px] text-gray-500">{{ $order->vendeur->telephone_commercial ?? 'N/A' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-black text-gray-900">{{ number_format($order->montant_total, 0, ',', ' ') }} F</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $order->mode_paiement_prevu }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusClasses = [
                                    'en_attente' => 'bg-orange-50 text-orange-600 border-orange-100',
                                    'en_preparation' => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'pret' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                    'termine' => 'bg-green-50 text-green-600 border-green-100',
                                    'annule' => 'bg-red-50 text-red-600 border-red-100',
                                ];
                                $statusLabels = [
                                    'en_attente' => 'En attente',
                                    'en_preparation' => 'Préparation',
                                    'pret' => 'Prêt',
                                    'termine' => 'Livré',
                                    'annule' => 'Annulé',
                                ];
                            @endphp
                            <span class="px-3 py-1 text-[10px] font-black uppercase tracking-wider rounded-full border {{ $statusClasses[$order->statut] ?? 'bg-gray-50 text-gray-600' }}">
                                {{ $statusLabels[$order->statut] ?? $order->statut }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.orders.show', $order->id_commande) }}" class="p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                @if($order->statut != 'termine' && $order->statut != 'annule')
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 p-4 z-50">
                                        <h4 class="text-sm font-bold text-gray-900 mb-2">Annuler la commande ?</h4>
                                        <form action="{{ route('admin.orders.status', $order->id_commande) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="statut" value="annule">
                                            <textarea name="raison_annulation" placeholder="Raison de l'annulation..." class="w-full p-2 text-xs bg-gray-50 border border-gray-200 rounded-lg mb-3" required></textarea>
                                            <div class="flex gap-2">
                                                <button type="submit" class="flex-1 py-2 bg-red-600 text-white text-[10px] font-black uppercase tracking-widest rounded-lg">Confirmer</button>
                                                <button type="button" @click="open = false" class="flex-1 py-2 bg-gray-100 text-gray-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Non</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">Aucune commande trouvée</h3>
                                <p class="text-gray-500 text-sm">Nous n'avons trouvé aucune commande correspondant à vos critères.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-8 border-t border-gray-50 flex justify-center">
            {{ $orders->appends(request()->query())->links('vendor.pagination.premium') }}
        </div>
    </div>
</div>
@endsection
