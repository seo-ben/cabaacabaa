@extends('layouts.admin')

@section('title', 'Détails Vendeur')

@section('content')
<div class="space-y-4" x-data="{ 
    activeTab: '{{ request()->has('products_page') ? 'products' : (request()->has('orders_page') ? 'orders' : (request()->has('reviews_page') ? 'reviews' : 'overview')) }}' 
}">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.vendors.index') }}" class="w-8 h-8 bg-white border border-gray-100 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-600 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-black text-gray-900 tracking-tight leading-none uppercase">{{ $vendeur->nom_commercial }}</h1>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1.5 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    ID: #{{ $vendeur->id_vendeur }} • Inscrit le {{ optional($vendeur->date_inscription)->format('d/m/Y') }}
                </p>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.vendors.edit', $vendeur->id_vendeur) }}" class="px-3 py-2 bg-white border border-gray-100 rounded-xl text-[9px] font-black uppercase tracking-widest text-gray-600 hover:bg-gray-50 transition-all shadow-sm flex items-center gap-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                Modifier
            </a>
            @if($vendeur->statut_verification === 'en_cours')
                <form action="{{ route('admin.vendors.approve', $vendeur->id_vendeur) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-lg active:scale-95 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        Approuver
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm group">
            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Chiffre d'Affaires</p>
            <p class="text-lg font-black text-gray-900 leading-none">{{ number_format($totalRevenue, 0, ',', ' ') }} <span class="text-[10px]">{{ $currency }}</span></p>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm group">
            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Commandes</p>
            <p class="text-lg font-black text-blue-600 leading-none">{{ $vendeur->commandes_count }}</p>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm group">
            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Note Moyenne</p>
            <div class="flex items-center gap-1.5">
                <p class="text-lg font-black text-yellow-500 leading-none">{{ number_format($vendeur->note_moyenne, 1) }}</p>
                <svg class="w-3.5 h-3.5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            </div>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm group">
            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Portefeuille</p>
            <p class="text-lg font-black text-emerald-600 leading-none">{{ number_format($vendeur->wallet_balance, 0, ',', ' ') }} <span class="text-[10px]">{{ $currency }}</span></p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-4">
        <!-- Sidebar -->
        <div class="lg:w-64 space-y-4">
            <nav class="bg-white p-1.5 rounded-2xl border border-gray-100 shadow-sm space-y-1">
                <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'bg-gray-900 text-white shadow-lg' : 'text-gray-400 hover:bg-gray-50'" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-black uppercase text-[9px] tracking-widest transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profil
                </button>
                <button @click="activeTab = 'products'" :class="activeTab === 'products' ? 'bg-gray-900 text-white shadow-lg' : 'text-gray-400 hover:bg-gray-50'" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-black uppercase text-[9px] tracking-widest transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    Articles ({{ $vendeur->plats_count }})
                </button>
                <button @click="activeTab = 'orders'" :class="activeTab === 'orders' ? 'bg-gray-900 text-white shadow-lg' : 'text-gray-400 hover:bg-gray-50'" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-black uppercase text-[9px] tracking-widest transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Commandes
                </button>
                <button @click="activeTab = 'reviews'" :class="activeTab === 'reviews' ? 'bg-gray-900 text-white shadow-lg' : 'text-gray-400 hover:bg-gray-50'" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-black uppercase text-[9px] tracking-widest transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    Avis ({{ $vendeur->avis_evaluations_count }})
                </button>
            </nav>

            <div class="bg-gray-900 p-4 rounded-2xl text-white space-y-4">
                <p class="text-[8px] font-black uppercase tracking-widest text-gray-500">Documents Vérification</p>
                <div class="space-y-2">
                    @if($vendeur->document_identite)
                        <a href="{{ route('admin.vendors.show-doc', [$vendeur->id_vendeur, 'identite']) }}" target="_blank" class="flex items-center gap-3 p-2 bg-white/5 rounded-xl hover:bg-white/10 transition group text-left w-full">
                            <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center text-white group-hover:bg-red-500 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5L12 4l-2 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[9px] font-black uppercase tracking-tight">Identité</p>
                                <p class="text-[7px] text-emerald-500 font-bold uppercase tracking-widest">OK ✓</p>
                            </div>
                        </a>
                    @endif
                    @if($vendeur->justificatif_domicile)
                        <a href="{{ route('admin.vendors.show-doc', [$vendeur->id_vendeur, 'domicile']) }}" target="_blank" class="flex items-center gap-3 p-2 bg-white/5 rounded-xl hover:bg-white/10 transition group text-left w-full">
                            <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center text-white group-hover:bg-red-500 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[9px] font-black uppercase tracking-tight">Domicile</p>
                                <p class="text-[7px] text-emerald-500 font-bold uppercase tracking-widest">OK ✓</p>
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 space-y-4">
            <!-- Overview -->
            <div x-show="activeTab === 'overview'" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                        <p class="text-[9px] font-black text-gray-900 uppercase tracking-widest border-b border-gray-50 pb-2">Informations de Contact</p>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Email</p>
                                    <p class="text-[11px] font-bold text-gray-900 truncate">{{ $vendeur->user->email ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div>
                                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Mobile</p>
                                    <p class="text-[11px] font-bold text-gray-900">{{ $vendeur->telephone_commercial ?: 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                        <p class="text-[9px] font-black text-gray-900 uppercase tracking-widest border-b border-gray-50 pb-2">Profil Commercial</p>
                        <div class="space-y-4">
                            <div>
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-2">Catégories</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($vendeur->categories as $cat)
                                        <span class="px-2 py-1 bg-gray-50 border border-gray-100 rounded-lg text-[8px] font-black text-gray-600 uppercase tracking-widest">{{ $cat->icone }} {{ $cat->nom_categorie }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Adresse</p>
                                <p class="text-[10px] font-bold text-gray-700 leading-tight">{{ $vendeur->adresse_complete }}</p>
                                <p class="text-[8px] font-black text-red-500 uppercase tracking-widest mt-1">{{ $vendeur->zone->nom ?? 'Hors zone' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Hours -->
                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-4 border-b border-gray-50 pb-2">
                        <p class="text-[9px] font-black text-gray-900 uppercase tracking-widest">Planning d'Ouverture</p>
                        @php
                            $now = now(); $currentDay = $now->dayOfWeek; $currentHour = $now->format('H:i:s');
                            $todayHours = $vendeur->horaires->where('jour_semaine', $currentDay)->first();
                            if (!$todayHours && is_array($vendeur->horaires_ouverture)) { $todayHours = collect($vendeur->horaires_ouverture)->where('jour_semaine', $currentDay)->first(); }
                            $isOpen = ($todayHours && !$todayHours->ferme && $currentHour >= $todayHours->heure_ouverture && $currentHour <= $todayHours->heure_fermeture);
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-widest {{ $isOpen ? 'bg-emerald-50 text-emerald-500 border border-emerald-100' : 'bg-rose-50 text-rose-500 border border-rose-100' }}">
                            {{ $isOpen ? 'OUVERT' : 'FERMÉ' }}
                        </span>
                    </div>
                    <div class="grid grid-cols-7 gap-2">
                        @php $jours = [1 => 'LUN', 2 => 'MAR', 3 => 'MER', 4 => 'JEU', 5 => 'VEN', 6 => 'SAM', 0 => 'DIM']; @endphp
                        @foreach($jours as $id => $nom)
                            @php 
                                $dayData = $vendeur->horaires->where('jour_semaine', $id)->first();
                                if (!$dayData && is_array($vendeur->horaires_ouverture)) { $dayData = (object)collect($vendeur->horaires_ouverture)->where('jour_semaine', $id)->first(); }
                                $isNow = ($currentDay == $id);
                            @endphp
                            <div class="p-2.5 rounded-xl border {{ $isNow ? 'bg-gray-900 border-gray-900 shadow-lg' : 'bg-gray-50 border-gray-100' }} text-center transition-all">
                                <p class="text-[7px] font-black uppercase tracking-widest {{ $isNow ? 'text-gray-400' : 'text-gray-300' }} mb-1.5">{{ $nom }}</p>
                                @if($dayData && !$dayData->ferme)
                                    <p class="text-[9px] font-black {{ $isNow ? 'text-white' : 'text-gray-700' }} leading-none">{{ substr($dayData->heure_ouverture, 0, 5) }}</p>
                                    <div class="w-2 h-[1px] {{ $isNow ? 'bg-white/20' : 'bg-gray-200' }} mx-auto my-1"></div>
                                    <p class="text-[9px] font-black {{ $isNow ? 'text-white' : 'text-gray-700' }} leading-none">{{ substr($dayData->heure_fermeture, 0, 5) }}</p>
                                @else
                                    <p class="text-[7px] font-black text-gray-400 tracking-tighter mt-1">N/A</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Products -->
            <div x-show="activeTab === 'products'" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden text-[10px]" style="display: none;">
                <div class="px-4 py-3 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="font-black text-gray-900 uppercase tracking-widest leading-none">Catalogue Articles</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($plats as $plat)
                        <div class="px-4 py-2.5 flex items-center justify-between gap-4 group hover:bg-gray-50/50 transition-all">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg overflow-hidden bg-gray-100">
                                    @if($plat->image)
                                        <img src="{{ asset('storage/' . $plat->image) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-black text-gray-900 uppercase leading-none mb-1">{{ $plat->nom_plat }}</p>
                                    <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">{{ $plat->categorie->nom_categorie ?? 'Général' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-black text-gray-900">{{ number_format($plat->prix, 0, ',', ' ') }} F</p>
                                <span class="text-[7px] font-black uppercase tracking-widest {{ $plat->en_stock ? 'text-emerald-500' : 'text-rose-500' }}">
                                    {{ $plat->en_stock ? 'STOCK' : 'OUT' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-gray-300 font-bold uppercase text-[9px]">Vide</div>
                    @endforelse
                </div>
                @if($plats->hasPages())
                    <div class="px-4 py-2 bg-gray-50/50 border-t border-gray-100 text-[8px] font-bold">
                        {{ $plats->links() }}
                    </div>
                @endif
            </div>

            <!-- Orders -->
            <div x-show="activeTab === 'orders'" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden text-[10px]" style="display: none;">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">N° CMD</th>
                            <th class="px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Client</th>
                            <th class="px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Total</th>
                            <th class="px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Statut</th>
                            <th class="pr-4 pl-3 py-3 font-black text-gray-400 uppercase tracking-widest text-right leading-none">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentOrders as $order)
                            <tr class="group hover:bg-gray-50/50 transition-all">
                                <td class="px-4 py-2.5 font-black text-gray-900">#{{ $order->numero_commande }}</td>
                                <td class="px-3 py-2.5">
                                    <p class="font-black text-gray-700 uppercase leading-none mb-0.5 truncate max-w-[80px]">{{ $order->client->name ?? 'N/A' }}</p>
                                    <p class="text-[7px] text-gray-400 font-bold uppercase">{{ $order->client->phone ?? 'N/A' }}</p>
                                </td>
                                <td class="px-3 py-2.5 font-black text-gray-900">{{ number_format($order->montant_total, 0, ',', ' ') }} F</td>
                                <td class="px-3 py-2.5">
                                    @php
                                        $sc = [
                                            'en_attente' => 'text-orange-500',
                                            'en_preparation' => 'text-blue-500',
                                            'termine' => 'text-emerald-500',
                                            'annulee' => 'text-rose-500',
                                        ];
                                    @endphp
                                    <span class="font-black text-[8px] uppercase tracking-widest {{ $sc[$order->statut] ?? 'text-gray-400' }}">
                                        {{ str_replace('_', '', $order->statut) }}
                                    </span>
                                </td>
                                <td class="pr-4 pl-3 py-2.5 text-right font-bold text-gray-400 text-[8px] uppercase tracking-tighter">
                                    {{ $order->date_commande->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($recentOrders->hasPages())
                    <div class="px-4 py-2 bg-gray-50/50 border-t border-gray-100 text-[8px] font-bold">
                        {{ $recentOrders->links() }}
                    </div>
                @endif
            </div>

            <!-- Reviews -->
            <div x-show="activeTab === 'reviews'" class="grid grid-cols-2 gap-3" style="display: none;">
                @forelse($recentReviews as $review)
                    <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-gray-50 rounded-lg flex items-center justify-center text-[10px] font-black text-gray-400 uppercase">
                                    {{ substr($review->client->name ?? 'C', 0, 1) }}
                                </div>
                                <p class="text-[10px] font-black text-gray-900 uppercase truncate max-w-[100px]">{{ $review->client->name ?? 'Anonyme' }}</p>
                            </div>
                            <div class="flex gap-0.5 text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-2.5 h-2.5 {{ $i <= $review->note ? 'fill-current' : 'text-gray-100 fill-current' }}" viewBox="0 0 20 20"><path d="M10 1l2.6 6.3h6.4l-5.2 4 2 6.7-5.8-4.3-5.8 4.3 2-6.7-5.2-4h6.4z"/></svg>
                                @endfor
                            </div>
                        </div>
                        <p class="text-[9px] font-bold text-gray-500 leading-relaxed italic uppercase">"{{ $review->commentaire }}"</p>
                    </div>
                @empty
                    <div class="col-span-full py-8 text-center text-gray-300 font-bold uppercase text-[9px]">Aucun avis</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
