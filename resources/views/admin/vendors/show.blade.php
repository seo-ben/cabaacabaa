@extends('layouts.admin')

@section('title', 'Détails Vendeur')

@section('content')
<div class="space-y-10" x-data="{ 
    activeTab: '{{ request()->has('products_page') ? 'products' : (request()->has('orders_page') ? 'orders' : (request()->has('reviews_page') ? 'reviews' : 'overview')) }}' 
}">
    <!-- Header with Back Button and Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.vendors.index') }}" class="w-12 h-12 bg-white border border-gray-100 rounded-2xl flex items-center justify-center text-gray-400 hover:text-red-600 hover:border-red-100 transition-all shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ $vendeur->nom_commercial }}</h1>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">ID: #{{ $vendeur->id_vendeur }}</span>
                    <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                    <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Inscrit le {{ optional($vendeur->date_inscription)->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.vendors.edit', $vendeur->id_vendeur) }}" class="px-6 py-3 bg-white border border-gray-100 rounded-2xl text-[11px] font-black uppercase tracking-widest text-gray-600 hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                Modifier le profil
            </a>
            @if($vendeur->statut_verification === 'en_cours')
                <form action="{{ route('admin.vendors.approve', $vendeur->id_vendeur) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-green-700 transition shadow-lg shadow-green-100 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Approuver le vendeur
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-red-50 rounded-full scale-100 group-hover:scale-110 transition duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Chiffre d'affaires</p>
                <h3 class="text-3xl font-black text-gray-900">{{ number_format($totalRevenue, 0, ',', ' ') }} <span class="text-xs">{{ $currency }}</span></h3>
            </div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-50 rounded-full scale-100 group-hover:scale-110 transition duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Commandes totales</p>
                <h3 class="text-3xl font-black text-blue-600">{{ $vendeur->commandes_count }}</h3>
            </div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-yellow-50 rounded-full scale-100 group-hover:scale-110 transition duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Note moyenne</p>
                <div class="flex items-center gap-2">
                    <h3 class="text-3xl font-black text-yellow-500">{{ number_format($vendeur->note_moyenne, 1) }}</h3>
                    <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-green-50 rounded-full scale-100 group-hover:scale-110 transition duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Portefeuille actuel</p>
                <h3 class="text-3xl font-black text-green-600">{{ number_format($vendeur->wallet_balance, 0, ',', ' ') }} <span class="text-xs">{{ $currency }}</span></h3>
            </div>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <div class="flex flex-col lg:flex-row gap-10">
        <!-- Sidebar Navigation -->
        <div class="lg:w-80 space-y-4">
            <nav class="bg-white p-4 rounded-[2rem] border border-gray-100 shadow-sm space-y-1">
                <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'bg-red-50 text-red-600' : 'text-gray-500 hover:bg-gray-50'" class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Vue d'ensemble
                </button>
                <button @click="activeTab = 'products'" :class="activeTab === 'products' ? 'bg-red-50 text-red-600' : 'text-gray-500 hover:bg-gray-50'" class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    Produits ({{ $vendeur->plats_count }})
                </button>
                <button @click="activeTab = 'orders'" :class="activeTab === 'orders' ? 'bg-red-50 text-red-600' : 'text-gray-500 hover:bg-gray-50'" class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Commandes Récentes
                </button>
                <button @click="activeTab = 'reviews'" :class="activeTab === 'reviews' ? 'bg-red-50 text-red-600' : 'text-gray-500 hover:bg-gray-50'" class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    Avis clients ({{ $vendeur->avis_evaluations_count }})
                </button>
            </nav>

            <div class="bg-gray-900 p-8 rounded-[2.5rem] text-white space-y-6">
                <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-500">Documents de vérification</h4>
                <div class="space-y-4">
                    @if($vendeur->document_identite)
                        <a href="{{ route('admin.vendors.show-doc', [$vendeur->id_vendeur, 'identite']) }}" target="_blank" class="flex items-center gap-4 p-4 bg-white/5 rounded-2xl hover:bg-white/10 transition group text-left w-full">
                            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center text-white group-hover:bg-red-600 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5L12 4l-2 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-tight">Pièce d'identité</p>
                                <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest">Vérifié ✓</p>
                            </div>
                        </a>
                    @else
                        <div class="flex items-center gap-4 p-4 bg-white/5 rounded-2xl opacity-50">
                            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <p class="text-[11px] font-black uppercase tracking-tight text-gray-400">Non fourni</p>
                        </div>
                    @endif

                    @if($vendeur->justificatif_domicile)
                        <a href="{{ route('admin.vendors.show-doc', [$vendeur->id_vendeur, 'domicile']) }}" target="_blank" class="flex items-center gap-4 p-4 bg-white/5 rounded-2xl hover:bg-white/10 transition group text-left w-full">
                            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center text-white group-hover:bg-red-600 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-tight">Justificatif Domicile</p>
                                <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest">Vérifié ✓</p>
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 space-y-10">
            <!-- Overview Tab -->
            <div x-show="activeTab === 'overview'" class="space-y-10 animate-fade-in">
                <!-- Info Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-sm space-y-8">
                        <div>
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Contact & Support</h3>
                            <div class="space-y-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Email Responsable</p>
                                        <p class="text-sm font-bold text-gray-900">{{ $vendeur->user->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 5z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Téléphone Commercial</p>
                                        <p class="text-sm font-bold text-gray-900">{{ $vendeur->telephone_commercial ?: 'Non renseigné' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Localisation</p>
                                        <p class="text-sm font-bold text-gray-900">{{ $vendeur->adresse_complete }}</p>
                                        <p class="text-[11px] font-medium text-gray-400">{{ $vendeur->zone->nom ?? 'Hors zone' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-sm space-y-8">
                        <div>
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Profil Commercial</h3>
                            <div class="space-y-6">
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Spécialités</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($vendeur->categories as $cat)
                                            <span class="px-3 py-1.5 bg-red-50 text-red-600 rounded-xl text-[10px] font-black uppercase tracking-widest border border-red-100">{{ $cat->icone }} {{ $cat->nom_categorie }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Description</p>
                                    <p class="text-sm font-medium text-gray-600 leading-relaxed">{{ $vendeur->description ?: 'Aucune description disponible.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Hours Card -->
                <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden relative group">
                    <div class="absolute -right-16 -top-16 w-48 h-48 bg-gray-50 rounded-full scale-0 group-hover:scale-100 transition duration-700"></div>
                    
                    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8 mb-10">
                        <div>
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Horaires d'ouverture</h3>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Disponibilité hebdomadaire du partenaire</p>
                        </div>
                        
                        @php
                            $now = now();
                            $currentDay = $now->dayOfWeek; // 0 (Sun) to 6 (Sat)
                            $currentHour = $now->format('H:i:s');
                            
                            $todayHours = $vendeur->horaires->where('jour_semaine', $currentDay)->first();
                            
                            // Fallback if relations are empty but JSON exists
                            if (!$todayHours && is_array($vendeur->horaires_ouverture)) {
                                $todayHours = collect($vendeur->horaires_ouverture)->where('jour_semaine', $currentDay)->first();
                            }
                            
                            $isOpen = false;
                            if ($todayHours && !$todayHours->ferme) {
                                if ($currentHour >= $todayHours->heure_ouverture && $currentHour <= $todayHours->heure_fermeture) {
                                    $isOpen = true;
                                }
                            }
                        @endphp

                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl">
                            <div class="w-10 h-10 {{ $isOpen ? 'bg-green-500' : 'bg-red-500' }} rounded-xl flex items-center justify-center text-white shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">État actuel</p>
                                <p class="text-sm font-black {{ $isOpen ? 'text-green-600' : 'text-red-600' }} uppercase">{{ $isOpen ? 'Ouvert maintenant' : 'Actuellement fermé' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-10 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
                        @php
                            $jours = [
                                1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 
                                4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 
                                0 => 'Dimanche'
                            ];
                        @endphp
                        @foreach($jours as $id => $nom)
                            @php 
                                $dayData = $vendeur->horaires->where('jour_semaine', $id)->first();
                                if (!$dayData && is_array($vendeur->horaires_ouverture)) {
                                    $dayData = (object)collect($vendeur->horaires_ouverture)->where('jour_semaine', $id)->first();
                                }
                                $isCurrent = ($currentDay == $id);
                            @endphp
                            <div class="p-5 {{ $isCurrent ? 'bg-red-600 text-white shadow-xl shadow-red-100 scale-105' : 'bg-gray-50 text-gray-900' }} rounded-3xl text-center transition-all duration-500 relative overflow-hidden group/day">
                                @if($isCurrent)
                                    <div class="absolute top-2 right-2 w-1.5 h-1.5 bg-white rounded-full animate-ping"></div>
                                @endif
                                <p class="text-[10px] font-black {{ $isCurrent ? 'text-white/60' : 'text-gray-400' }} uppercase tracking-widest mb-2">{{ $nom }}</p>
                                @if($dayData && !$dayData->ferme)
                                    <p class="text-sm font-black">{{ substr($dayData->heure_ouverture, 0, 5) }}</p>
                                    <div class="w-4 h-[1px] {{ $isCurrent ? 'bg-white/30' : 'bg-gray-200' }} mx-auto my-1"></div>
                                    <p class="text-sm font-black">{{ substr($dayData->heure_fermeture, 0, 5) }}</p>
                                @else
                                    <p class="text-[10px] font-black uppercase tracking-widest mt-3 opacity-50">Fermé</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Products Tab -->
            <div x-show="activeTab === 'products'" class="space-y-6 animate-fade-in" style="display: none;">
                <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between">
                        <h3 class="text-lg font-black text-gray-900 tracking-tight">Catalogue de Produits</h3>
                        <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest group">{{ $vendeur->plats_count }} Articles</span>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @forelse($plats as $plat)
                            <div class="p-8 flex items-center justify-between gap-6 hover:bg-gray-50 transition">
                                <div class="flex items-center gap-6">
                                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center overflow-hidden border border-gray-50">
                                        @if($plat->image)
                                            <img src="{{ asset('storage/' . $plat->image) }}" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-gray-900">{{ $plat->nom_plat }}</p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">{{ $plat->categorie->nom_categorie ?? 'Général' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-black text-gray-900">{{ number_format($plat->prix, 0, ',', ' ') }} {{ $currency }}</p>
                                    <p class="text-[10px] font-black uppercase tracking-widest mt-1 {{ $plat->en_stock ? 'text-green-500' : 'text-red-500' }}">
                                        {{ $plat->en_stock ? 'En Stock' : 'Épuisé' }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="p-20 text-center">
                                <p class="text-gray-400 italic">Aucun produit dans le catalogue.</p>
                            </div>
                        @endforelse
                    </div>
                    </div>
                    @if($plats->hasPages())
                        <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-50">
                            {{ $plats->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Orders Tab -->
            <div x-show="activeTab === 'orders'" class="space-y-6 animate-fade-in" style="display: none;">
                <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between">
                        <h3 class="text-lg font-black text-gray-900 tracking-tight">Flux de Commandes Récentes</h3>
                        <a href="{{ route('admin.orders.index', ['vendeur' => $vendeur->id_vendeur]) }}" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline">Voir tout l'historique</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">N° COMMANDE</th>
                                    <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Client</th>
                                    <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Montant</th>
                                    <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Statut</th>
                                    <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($recentOrders as $order)
                                    <tr class="hover:bg-gray-50/30 transition">
                                        <td class="px-8 py-6">
                                            <span class="text-xs font-black text-gray-900">{{ $order->numero_commande }}</span>
                                        </td>
                                        <td class="px-8 py-6">
                                            <p class="text-xs font-bold text-gray-900">{{ $order->client->name ?? 'Client Inconnu' }}</p>
                                            <p class="text-[10px] text-gray-400 font-medium uppercase tracking-tight">{{ $order->client->phone ?? 'N/A' }}</p>
                                        </td>
                                        <td class="px-8 py-6">
                                            <p class="text-sm font-black text-gray-900">{{ number_format($order->montant_total, 0, ',', ' ') }} {{ $currency }}</p>
                                        </td>
                                        <td class="px-8 py-6">
                                            @php
                                                $statutColors = [
                                                    'en_attente' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                                    'validee' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                    'en_preparation' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                                    'prete' => 'bg-purple-50 text-purple-600 border-purple-100',
                                                    'en_livraison' => 'bg-orange-50 text-orange-600 border-orange-100',
                                                    'termine' => 'bg-green-50 text-green-600 border-green-100',
                                                    'annulee' => 'bg-red-50 text-red-600 border-red-100',
                                                ];
                                            @endphp
                                            <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border {{ $statutColors[$order->statut] ?? 'bg-gray-50 text-gray-400 border-gray-100' }}">
                                                {{ str_replace('_', ' ', $order->statut) }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ $order->date_commande->diffForHumans() }}</p>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    </div>
                    @if($recentOrders->hasPages())
                        <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-50">
                            {{ $recentOrders->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reviews Tab -->
            <div x-show="activeTab === 'reviews'" class="space-y-6 animate-fade-in" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($recentReviews as $review)
                        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-red-600 font-black text-sm">
                                        {{ substr($review->client->name ?? 'C', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-gray-900">{{ $review->client->name ?? 'Anonyme' }}</p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $review->date_publication->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-1 text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-3 h-3 {{ $i <= $review->note ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-sm font-medium text-gray-600 leading-relaxed">"{{ $review->commentaire }}"</p>
                        </div>
                    @empty
                        <div class="col-span-full py-20 bg-white rounded-[2.5rem] border-2 border-dashed border-gray-100 text-center">
                            <p class="text-gray-400 font-medium italic">Aucun avis client pour le moment.</p>
                        </div>
                    @endforelse
                </div>
                @if($recentReviews->hasPages())
                    <div class="mt-8">
                        {{ $recentReviews->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
