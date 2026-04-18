@extends('layouts.admin')

@section('title', 'Suivi des Commandes')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-black text-gray-900 tracking-tight leading-none uppercase">Suivi Opérationnel</h1>
            <p class="text-gray-400 text-[8px] font-black uppercase tracking-[0.2em] mt-1.5 flex items-center gap-1.5 leading-none">
                <span class="w-1 h-1 rounded-full bg-red-500 animate-pulse"></span>
                Logistique en temps réel
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3 bg-white p-2 lg:px-4 lg:py-2 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex flex-col">
                <p class="text-[8px] font-black uppercase tracking-widest text-gray-400 mb-0.5">Panier Moyen</p>
                <p class="text-sm font-black text-gray-900 leading-none">{{ number_format($stats['avg_order'], 0, ',', ' ') }} <span class="text-[8px] text-gray-400 ml-0.5">F</span></p>
            </div>
            <div class="w-px h-5 bg-gray-100 hidden md:block"></div>
            <div class="flex flex-col">
                <p class="text-[8px] font-black uppercase tracking-widest text-gray-400 mb-0.5">Commissions</p>
                <p class="text-sm font-black text-blue-600 leading-none">{{ number_format($stats['total_fees'], 0, ',', ' ') }} <span class="text-[8px] text-blue-200 ml-0.5">F</span></p>
            </div>
            <div class="w-px h-5 bg-gray-100 hidden md:block"></div>
            <div class="flex flex-col pr-1">
                <p class="text-[8px] font-black uppercase tracking-widest text-gray-400 mb-0.5">Volume Total</p>
                <p class="text-lg font-black text-gray-900 tracking-tighter leading-none">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} <span class="text-[9px] text-gray-300 ml-0.5">F</span></p>
            </div>
            <button onclick="window.location.reload()" class="p-1.5 bg-gray-50 text-gray-400 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-all active:scale-95">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-7 gap-3">
        <a href="{{ route('admin.orders.index') }}" class="group bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:border-gray-900 hover:shadow-lg transition-all duration-300 relative overflow-hidden {{ !$status ? 'ring-4 ring-gray-900/10 border-gray-900' : '' }}">
            <div class="relative z-10">
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Toutes</p>
                <p class="text-xl font-black text-gray-900 group-hover:scale-105 transition-transform origin-left duration-300">{{ $stats['total'] }}</p>
            </div>
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'en_attente']) }}" class="group bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:border-orange-500 hover:shadow-lg transition-all duration-300 relative overflow-hidden {{ $status == 'en_attente' ? 'ring-4 ring-orange-500/10 border-orange-500' : '' }}">
            <div class="relative z-10">
                <p class="text-[9px] font-black text-orange-500 uppercase tracking-widest mb-2">Nouvelles</p>
                <p class="text-xl font-black text-gray-900 group-hover:scale-105 transition-transform origin-left">{{ $stats['en_attente']['count'] }}</p>
                <p class="text-[8px] font-bold text-gray-400 mt-0.5 italic">{{ number_format($stats['en_attente']['sum'], 0, ',', ' ') }} F</p>
            </div>
            @if($stats['en_attente']['count'] > 0)
                <div class="absolute top-3 right-3 w-1.5 h-1.5 bg-orange-500 rounded-full animate-ping"></div>
            @endif
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'en_preparation']) }}" class="group bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:border-blue-500 hover:shadow-lg transition-all duration-300 relative {{ $status == 'en_preparation' ? 'ring-4 ring-blue-500/10 border-blue-500' : '' }}">
            <div class="relative z-10">
                <p class="text-[9px] font-black text-blue-500 uppercase tracking-widest mb-2">Cuisine</p>
                <p class="text-xl font-black text-gray-900 group-hover:scale-105 transition-transform origin-left">{{ $stats['en_preparation'] }}</p>
            </div>
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'pret']) }}" class="group bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:border-indigo-500 hover:shadow-lg transition-all duration-300 relative {{ $status == 'pret' ? 'ring-4 ring-indigo-500/10 border-indigo-500' : '' }}">
            <div class="relative z-10">
                <p class="text-[9px] font-black text-indigo-500 uppercase tracking-widest mb-2">Départ</p>
                <p class="text-xl font-black text-gray-900 group-hover:scale-105 transition-transform origin-left">{{ $stats['pret'] }}</p>
            </div>
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'en_livraison']) }}" class="group bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:border-cyan-500 hover:shadow-lg transition-all duration-300 relative {{ $status == 'en_livraison' ? 'ring-4 ring-cyan-500/10 border-cyan-500' : '' }}">
            <div class="relative z-10">
                <p class="text-[9px] font-black text-cyan-500 uppercase tracking-widest mb-2">En route</p>
                <p class="text-xl font-black text-cyan-600 italic">Live</p>
            </div>
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'termine']) }}" class="group bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:border-green-500 hover:shadow-lg transition-all duration-300 relative {{ $status == 'termine' ? 'ring-4 ring-green-500/10 border-green-500' : '' }}">
            <div class="relative z-10">
                <p class="text-[9px] font-black text-green-500 uppercase tracking-widest mb-2">Livré</p>
                <div class="flex items-baseline gap-2">
                    <p class="text-xl font-black text-gray-900 group-hover:scale-105 transition-transform origin-left">{{ $stats['termine']['count'] }}</p>
                    <p class="text-[8px] font-bold text-green-600 italic">{{ number_format($stats['termine']['sum'], 0, ',', ' ') }} F</p>
                </div>
            </div>
        </a>

        <div class="bg-slate-900 p-4 rounded-2xl text-white shadow-xl shadow-slate-900/10 flex flex-col justify-center relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Efficacité</p>
                <div class="flex items-baseline gap-1">
                    <p class="text-xl font-black leading-none">{{ $stats['total'] > 0 ? round(($stats['termine']['count'] / $stats['total']) * 100) : 0 }}</p>
                    <span class="text-[10px] font-black text-red-500">%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-1.5 rounded-2xl border border-gray-100 shadow-sm">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center gap-1">
            @if($status) <input type="hidden" name="status" value="{{ $status }}"> @endif
            
            <div class="flex-1 px-3 py-1.5">
                <div class="relative group">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Recherche rapide..." 
                           class="w-full pl-8 pr-4 py-2 bg-transparent border-none focus:ring-0 text-xs font-bold text-gray-900 placeholder:text-gray-300 placeholder:uppercase">
                    <svg class="absolute left-0 top-2 w-4 h-4 text-gray-300 group-focus-within:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div class="h-6 w-px bg-gray-50 hidden lg:block mx-2"></div>

            <div class="flex items-center gap-2 px-3">
                <span class="text-[8px] font-black text-gray-300 uppercase tracking-widest whitespace-nowrap">Dates</span>
                <input type="date" name="date_start" value="{{ $date_start }}" 
                       class="px-2 py-1.5 bg-gray-50 border-none rounded-lg focus:bg-white focus:ring-2 focus:ring-red-500/10 transition-all text-[10px] font-bold text-gray-600">
                <span class="text-gray-200">-</span>
                <input type="date" name="date_end" value="{{ $date_end }}" 
                       class="px-2 py-1.5 bg-gray-50 border-none rounded-lg focus:bg-white focus:ring-2 focus:ring-red-500/10 transition-all text-[10px] font-bold text-gray-600">
            </div>

            <div class="flex items-center gap-1.5 ml-auto p-1.5">
                <button type="submit" class="px-5 py-2.5 bg-gray-900 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-black transition-all active:scale-95 shadow-lg shadow-gray-900/10">Appliquer</button>
                @if($search || $status || $date_start || $date_end)
                    <a href="{{ route('admin.orders.index') }}" class="p-2.5 bg-gray-50 text-gray-400 hover:text-red-600 rounded-xl transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="pl-4 pr-3 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">Référence</th>
                        <th class="px-3 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">Client / Logistique</th>
                        <th class="hidden xl:table-cell px-3 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">Partenaire</th>
                        <th class="px-3 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">Flux</th>
                        <th class="hidden lg:table-cell px-3 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">État</th>
                        <th class="pr-4 pl-3 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                    <tr class="group hover:bg-gray-50/80 transition-all duration-200 border-l-4 {{ $order->date_commande->isToday() ? 'border-red-500 bg-red-50/5' : 'border-transparent' }}">
                        <td class="pl-4 pr-3 py-3 whitespace-nowrap">
                            <span class="text-xs font-black text-gray-900 group-hover:text-red-600 transition-colors">#{{ $order->numero_commande }}</span>
                            <p class="text-[8px] text-gray-400 font-bold uppercase mt-0.5">{{ $order->date_commande->format('d/m H:i') }}</p>
                        </td>
                        <td class="px-3 py-3">
                            <div class="flex items-center gap-2.5">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($order->client->name ?? 'User') }}&background=FEE2E2&color=EF4444&bold=true" class="w-7 h-7 rounded-lg shrink-0 shadow-sm transition-transform group-hover:scale-105">
                                <div class="flex flex-col min-w-0">
                                    <p class="text-xs font-bold text-gray-900 truncate">{{ $order->client->name ?? 'Client' }}</p>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="text-[8px] font-black {{ $order->type_recuperation == 'livraison' ? 'text-cyan-600' : 'text-orange-600' }} uppercase leading-none">{{ $order->type_recuperation == 'livraison' ? 'Liv' : 'Emp' }}</span>
                                        <span class="text-[8px] text-gray-300 font-black">•</span>
                                        <p class="text-[8px] text-gray-400 font-bold uppercase leading-none">{{ $order->lignes->sum('quantite') }} Art.</p>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="hidden xl:table-cell px-3 py-3">
                            <p class="text-xs font-bold text-gray-800">{{ $order->vendeur->nom_commercial ?? 'Boutique' }}</p>
                            <p class="text-[8px] text-gray-400 font-bold uppercase mt-0.5">{{ $order->vendeur->telephone_commercial ?? 'N/A' }}</p>
                        </td>
                        <td class="px-3 py-3 whitespace-nowrap">
                            <p class="text-xs font-black text-gray-900">{{ number_format($order->montant_total, 0, ',', ' ') }} <span class="text-[8px] text-gray-400 ml-0.5">F</span></p>
                            <p class="text-[7px] text-gray-300 mt-0.5 font-bold uppercase">{{ $order->mode_paiement_prevu }}</p>
                        </td>
                        <td class="hidden lg:table-cell px-3 py-3">
                            @php
                                $statusMeta = [
                                    'en_attente' => ['class' => 'bg-orange-50 text-orange-600 border-orange-100', 'label' => 'Nouveau'],
                                    'en_preparation' => ['class' => 'bg-blue-50 text-blue-600 border-blue-100', 'label' => 'En cours'],
                                    'pret' => ['class' => 'bg-indigo-50 text-indigo-600 border-indigo-100', 'label' => 'Cuisine'],
                                    'en_livraison' => ['class' => 'bg-cyan-50 text-cyan-600 border-cyan-100', 'label' => 'Coursier'],
                                    'termine' => ['class' => 'bg-green-50 text-green-600 border-green-100', 'label' => 'Terminé'],
                                    'annule' => ['class' => 'bg-red-50 text-red-600 border-red-100', 'label' => 'Annulé'],
                                ];
                                $meta = $statusMeta[$order->statut] ?? ['class' => 'bg-gray-50 text-gray-600 border-gray-100', 'label' => $order->statut];
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 text-[8px] font-black uppercase tracking-widest rounded-lg border {{ $meta['class'] }}">
                                <span class="w-1 h-1 rounded-full bg-current shadow-sm"></span>
                                {{ $meta['label'] }}
                            </span>
                        </td>
                        <td class="pr-4 pl-3 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.orders.show', $order->id_commande) }}" class="w-8 h-8 bg-gray-50 text-gray-900 rounded-lg flex items-center justify-center hover:bg-gray-900 hover:text-white transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                @if($order->statut != 'termine' && $order->statut != 'annule')
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <div x-show="open" x-transition @click.away="open = false" class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-2xl border border-gray-100 p-4 z-50 text-left">
                                        <p class="text-[9px] font-black text-gray-900 uppercase tracking-widest mb-3">Interrompre ?</p>
                                        <form action="{{ route('admin.orders.status', $order->id_commande) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="statut" value="annule">
                                            <textarea name="raison_annulation" placeholder="Raison..." class="w-full h-16 p-2 text-[10px] bg-gray-50 border-none rounded-xl mb-3 font-bold" required></textarea>
                                            <button type="submit" class="w-full py-2 bg-red-600 text-white text-[9px] font-black uppercase tracking-widest rounded-xl">Confirmer</button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4 text-gray-200">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </div>
                                <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Aucune donnée opérationnelle</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-50 flex justify-center">
            {{ $orders->appends(request()->query())->links('vendor.pagination.premium') }}
        </div>
    </div>
</div>
@endsection
