@extends('layouts.admin')

@section('title', 'Gestion Vendeurs')

@section('content')
<div x-data="{ showAddModal: false, searchQuery: '{{ request('search') }}', statusFilter: '{{ request('status') }}' }" @keydown.escape="showAddModal = false" class="min-h-screen bg-gradient-to-br from-slate-50 via-purple-50 to-pink-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="space-y-2">
                <h1 class="text-3xl md:text-5xl font-bold bg-gradient-to-r from-purple-900 via-pink-900 to-rose-900 bg-clip-text text-transparent">
                    Gestion des Vendeurs
                </h1>
                <p class="text-slate-600 text-sm md:text-lg">Contrôlez et validez les partenaires de la plateforme</p>
            </div>
            
            <button @click="showAddModal = true" class="group relative overflow-hidden bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-4 rounded-2xl font-bold shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity"></div>
                <div class="relative flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nouveau Vendeur
                </div>
            </button>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Total -->
            <div class="group bg-white/80 backdrop-blur-lg rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-white/20">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-gradient-to-br from-slate-100 to-slate-200 rounded-2xl group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-slate-500 font-medium mb-2">Total Partenaires</p>
                <h3 class="text-4xl font-bold text-slate-900">{{ $stats['total'] }}</h3>
            </div>

            <!-- Active -->
            <div class="group bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative">
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-2xl">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-bold text-white">
                            Actifs
                        </span>
                    </div>
                    <p class="text-emerald-100 text-sm font-medium mb-2">Boutiques Actives</p>
                    <h3 class="text-4xl font-bold text-white">{{ $stats['active'] }}</h3>
                </div>
            </div>

            <!-- Pending -->
            <div class="group bg-white/80 backdrop-blur-lg rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-orange-200">
                @if($stats['pending'] > 0)
                    <div class="absolute top-4 right-4 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                    </div>
                @endif
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-gradient-to-br from-orange-100 to-amber-200 rounded-2xl group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-slate-500 font-medium mb-2">En Attente</p>
                <h3 class="text-4xl font-bold text-orange-600">{{ $stats['pending'] }}</h3>
            </div>

            <!-- Suspended -->
            <div class="group bg-white/80 backdrop-blur-lg rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-rose-200">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-gradient-to-br from-rose-100 to-pink-200 rounded-2xl group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-slate-500 font-medium mb-2">Comptes Suspendus</p>
                <h3 class="text-4xl font-bold text-rose-600">{{ $stats['suspended'] }}</h3>
            </div>

        </div>

        <!-- Filter Section -->
        <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-xl border border-white/20 p-6">
            <form action="{{ route('admin.vendors.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                
                <!-- Search Input -->
                <div class="flex-1 relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" 
                           name="search" 
                           x-model="searchQuery" 
                           placeholder="Rechercher par nom, boutique ou email..." 
                           class="w-full pl-12 pr-4 py-4 bg-slate-50 border-2 border-transparent rounded-2xl font-medium text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-purple-500 outline-none transition-all">
                </div>

                <!-- Status Filter -->
                <div class="w-full md:w-64">
                    <div x-data="{ 
                            open: false, 
                            selected: '{{ request('status') }}',
                            options: [
                                { val: '', label: 'Tous les statuts' },
                                { val: 'non_verifie', label: 'Incomplets' },
                                { val: 'en_cours', label: 'En attente' },
                                { val: 'verifie', label: 'Vérifiés' },
                                { val: 'suspendu', label: 'Suspendus' }
                            ],
                            get selectedLabel() {
                                const opt = this.options.find(o => o.val == this.selected);
                                return opt ? opt.label : 'Filtrer par statut';
                            }
                        }" class="relative w-full">
                        <input type="hidden" name="status" :value="selected">
                        <button type="button" @click="open = !open" 
                                class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-2xl font-medium text-slate-900 focus:bg-white focus:border-purple-500 outline-none transition-all flex items-center justify-between">
                            <span x-text="selectedLabel"></span>
                            <svg class="w-5 h-5 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 max-h-64 overflow-y-auto">
                            <template x-for="opt in options" :key="opt.val">
                                <button type="button" @click="selected = opt.val; open = false"
                                        class="w-full px-6 py-3 text-left hover:bg-purple-50 transition-colors flex items-center justify-between"
                                        :class="selected == opt.val ? 'bg-purple-50 text-purple-700' : 'text-slate-700'">
                                    <span class="font-bold" x-text="opt.label"></span>
                                    <svg x-show="selected == opt.val" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <button type="submit" class="px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-2xl font-bold shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5">
                    Filtrer
                </button>
                
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.vendors.index') }}" class="px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl font-medium hover:bg-slate-200 transition-all flex items-center justify-center">
                        Réinitialiser
                    </a>
                @endif
            </form>
        </div>

        <!-- Vendors Table -->
        <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-xl border border-white/20 overflow-hidden">
            @if($vendeurs->count())
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-slate-50 to-purple-50 border-b border-slate-200">
                                <th class="px-4 md:px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-600">Boutique</th>
                                <th class="hidden sm:table-cell px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-600">Type & Zone</th>
                                <th class="px-4 md:px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-600">Statut</th>
                                <th class="hidden lg:table-cell px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-600">État</th>
                                <th class="px-4 md:px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($vendeurs as $v)
                                <tr class="group hover:bg-purple-50/50 transition-colors">
                                    
                                    <!-- Boutique Info -->
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center text-white text-xl font-bold shadow-lg group-hover:scale-110 transition-transform">
                                                {{ substr($v->nom_commercial, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 group-hover:text-purple-700 transition-colors">
                                                    {{ $v->nom_commercial }}
                                                </div>
                                                <div class="text-sm text-slate-500 mt-1">
                                                    {{ $v->user->name ?? 'Sans utilisateur' }}
                                                </div>
                                                <div class="text-xs text-slate-400 mt-0.5">
                                                    {{ $v->user->email ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Type & Zone -->
                                    <td class="hidden sm:table-cell px-6 py-5">
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center px-3 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold">
                                                {{ $v->category ? $v->category->name : ($v->type_vendeur ?? 'Non défini') }}
                                            </span>
                                            <div class="text-xs text-slate-500 flex items-center gap-1 mt-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                </svg>
                                                {{ $v->zone->nom ?? 'Hors zone' }}
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-5">
                                        <div class="space-y-2">
                                            @php
                                                $statusConfig = [
                                                    'verifie' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'label' => 'Vérifié', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                                    'en_cours' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'label' => 'À Valider', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                                                    'non_verifie' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'Incomplet', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                                                    'suspendu' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-700', 'label' => 'Suspendu', 'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'],
                                                ];
                                                $conf = $statusConfig[$v->statut_verification] ?? $statusConfig['non_verifie'];
                                            @endphp
                                            
                                            <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl {{ $conf['bg'] }} {{ $conf['text'] }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $conf['icon'] }}"/>
                                                </svg>
                                                <span class="text-xs font-bold">{{ $conf['label'] }}</span>
                                            </div>
                                            
                                            @if($v->document_identite || $v->justificatif_domicile)
                                                <div class="flex gap-2 mt-2">
                                                    @if($v->document_identite)
                                                        <a href="{{ route('admin.vendors.show-doc', [$v->id_vendeur, 'identite']) }}" target="_blank" class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                            </svg>
                                                            ID
                                                        </a>
                                                    @endif
                                                    @if($v->justificatif_domicile)
                                                        <a href="{{ route('admin.vendors.show-doc', [$v->id_vendeur, 'domicile']) }}" target="_blank" class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                                            </svg>
                                                            Domicile
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Active Status -->
                                    <td class="hidden lg:table-cell px-6 py-5">
                                        @if($v->actif)
                                            <div class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-50 rounded-lg">
                                                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                                <span class="text-xs font-bold text-emerald-700">Actif</span>
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-2 px-3 py-2 bg-slate-50 rounded-lg">
                                                <div class="w-2 h-2 bg-slate-300 rounded-full"></div>
                                                <span class="text-xs font-bold text-slate-500">Inactif</span>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-5 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            
                                            <!-- View -->
                                            <a href="{{ route('admin.vendors.show', $v->id_vendeur) }}" class="p-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-900 hover:text-white transition-all" title="Voir Détails">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>

                                            <!-- Edit -->
                                            <a href="{{ route('admin.vendors.edit', $v->id_vendeur) }}" class="p-2.5 bg-blue-100 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all" title="Éditer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                                </svg>
                                            </a>

                                            <!-- More Actions -->
                                            <div x-data="{ open: false }" class="relative">
                                                <button @click="open = !open" class="p-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                                    </svg>
                                                </button>
                                                
                                                <div x-show="open" 
                                                     @click.away="open = false"
                                                     x-transition:enter="transition ease-out duration-100"
                                                     x-transition:enter-start="opacity-0 scale-95"
                                                     x-transition:enter-end="opacity-100 scale-100"
                                                     class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-20">
                                                    
                                                    @if($v->statut_verification === 'verifie')
                                                        <form action="{{ route('admin.vendors.unverify', $v->id_vendeur) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-colors">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                </svg>
                                                                Retirer Certification
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('admin.vendors.approve', $v->id_vendeur) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-emerald-600 hover:bg-emerald-50 transition-colors">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                </svg>
                                                                Certifier Boutique
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <div class="h-px bg-slate-100"></div>

                                                    @if($v->statut_verification === 'suspendu')
                                                        <form action="{{ route('admin.vendors.unsuspend', $v->id_vendeur) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-emerald-600 hover:bg-emerald-50 transition-colors">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                </svg>
                                                                Réactiver
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('admin.vendors.suspend', $v->id_vendeur) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-orange-600 hover:bg-orange-50 transition-colors">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/>
                                                                </svg>
                                                                Suspendre
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <div class="h-px bg-slate-100"></div>

                                                    <form action="{{ route('admin.vendors.destroy', $v->id_vendeur) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ce vendeur ?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-colors">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                            Supprimer
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-5 border-t border-slate-200 flex items-center justify-between bg-slate-50/50">
                    <p class="text-sm text-slate-600">
                        Affichage de <span class="font-bold">{{ $vendeurs->firstItem() }}</span> à <span class="font-bold">{{ $vendeurs->lastItem() }}</span> sur <span class="font-bold">{{ $vendeurs->total() }}</span> résultats
                    </p>
                    <div>
                        {{ $vendeurs->links() }}
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="py-24 text-center">
                    <div class="w-24 h-24 bg-gradient-to-br from-purple-100 to-pink-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-2">Aucun partenaire trouvé</h3>
                    <p class="text-slate-500 mb-8 max-w-md mx-auto">Essayez de modifier vos filtres ou lancez une nouvelle recherche.</p>
                    <a href="{{ route('admin.vendors.index') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-2xl font-bold shadow-xl hover:shadow-2xl transition-all hover:-translate-y-1">
                        Réinitialiser les filtres
                    </a>
                </div>
            @endif
        </div>

    </div>

    <!-- Add Vendor Modal -->
    <div x-show="showAddModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" 
         x-cloak>
        
        <div @click.away="showAddModal = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="relative bg-white rounded-3xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col">
            
            <!-- Modal Header -->
            <div class="px-8 py-6 bg-gradient-to-r from-purple-600 to-pink-600 text-white flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">Nouveau Partenaire</h2>
                    <p class="text-purple-100 text-sm mt-1">Création d'un accès établissement manuel</p>
                </div>
                <button @click="showAddModal = false" class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center hover:bg-white/30 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form action="{{ route('admin.vendors.store') }}" method="POST" class="p-8 space-y-6 overflow-y-auto">
                @csrf

                <!-- User Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700">Nom du Responsable</label>
                        <input type="text" 
                               name="name" 
                               required 
                               class="w-full px-4 py-3 bg-slate-50 border-2 border-transparent rounded-xl font-medium text-slate-900 focus:bg-white focus:border-purple-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700">Email Personnel</label>
                        <input type="email" 
                               name="email" 
                               required 
                               class="w-full px-4 py-3 bg-slate-50 border-2 border-transparent rounded-xl font-medium text-slate-900 focus:bg-white focus:border-purple-500 outline-none transition-all">
                    </div>
                </div>

                <!-- Store Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700">Nom de la Boutique</label>
                        <input type="text" 
                               name="nom_commercial" 
                               required 
                               class="w-full px-4 py-3 bg-slate-50 border-2 border-transparent rounded-xl font-medium text-slate-900 focus:bg-white focus:border-purple-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700">Catégorie</label>
                        <div x-data="{ 
                                open: false, 
                                selected: '',
                                options: [
                                    @foreach($vendorCategories as $cat)
                                        { id: '{{ $cat->id_category_vendeur }}', name: '{{ $cat->name }}' },
                                    @endforeach
                                ],
                                get selectedLabel() {
                                    const opt = this.options.find(o => o.id == this.selected);
                                    return opt ? opt.name : 'Sélectionner une catégorie...';
                                }
                            }" class="relative w-full">
                            <input type="hidden" name="id_category_vendeur" :value="selected">
                            <button type="button" @click="open = !open" 
                                    class="w-full px-4 py-3 bg-slate-50 border-2 border-transparent rounded-xl font-medium text-slate-900 focus:bg-white focus:border-purple-500 outline-none transition-all flex items-center justify-between">
                                <span x-text="selectedLabel" :class="!selected ? 'text-slate-400' : ''"></span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 max-h-48 overflow-y-auto">
                                <template x-for="opt in options" :key="opt.id">
                                    <button type="button" @click="selected = opt.id; open = false"
                                            class="w-full px-4 py-2.5 text-left hover:bg-purple-50 transition-colors flex items-center justify-between"
                                            :class="selected == opt.id ? 'bg-purple-50 text-purple-700' : 'text-slate-700'">
                                        <span class="font-bold text-sm" x-text="opt.name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <input type="hidden" name="type_vendeur" value="autre">
                    </div>
                </div>

                <!-- Contact & Zone -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700">Téléphone Commercial</label>
                        <input type="text" 
                               name="telephone_commercial" 
                               required 
                               class="w-full px-4 py-3 bg-slate-50 border-2 border-transparent rounded-xl font-medium text-slate-900 focus:bg-white focus:border-purple-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700">Zone de Couverture</label>
                        <div x-data="{ 
                                open: false, 
                                selected: '',
                                options: [
                                    { id: '', name: 'Toute zone / Indéfini' },
                                    @foreach($zones as $zone)
                                        { id: '{{ $zone->id_zone }}', name: '{{ $zone->nom ?? 'Zone ' . $zone->id_zone }}' },
                                    @endforeach
                                ],
                                get selectedLabel() {
                                    const opt = this.options.find(o => o.id == this.selected);
                                    return opt ? opt.name : 'Toute zone / Indéfini';
                                }
                            }" class="relative w-full">
                            <input type="hidden" name="id_zone" :value="selected">
                            <button type="button" @click="open = !open" 
                                    class="w-full px-4 py-3 bg-slate-50 border-2 border-transparent rounded-xl font-medium text-slate-900 focus:bg-white focus:border-purple-500 outline-none transition-all flex items-center justify-between">
                                <span x-text="selectedLabel"></span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 max-h-48 overflow-y-auto">
                                <template x-for="opt in options" :key="opt.id">
                                    <button type="button" @click="selected = opt.id; open = false"
                                            class="w-full px-4 py-2.5 text-left hover:bg-purple-50 transition-colors flex items-center justify-between"
                                            :class="selected == opt.id ? 'bg-purple-50 text-purple-700' : 'text-slate-700'">
                                        <span class="font-bold text-sm" x-text="opt.name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-slate-700">Adresse Physique Complète</label>
                    <input type="text" 
                           name="adresse_complete" 
                           required 
                           class="w-full px-4 py-3 bg-slate-50 border-2 border-transparent rounded-xl font-medium text-slate-900 focus:bg-white focus:border-purple-500 outline-none transition-all">
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-slate-700">Courte Description</label>
                    <textarea name="description" 
                              rows="3" 
                              class="w-full px-4 py-3 bg-slate-50 border-2 border-transparent rounded-xl font-medium text-slate-900 focus:bg-white focus:border-purple-500 outline-none transition-all resize-none"></textarea>
                </div>

                <!-- Modal Footer -->
                <div class="flex gap-4 pt-6 border-t border-slate-200">
                    <button type="submit" class="flex-1 px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-2xl font-bold shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5">
                        Créer le partenaire
                    </button>
                    <button type="button" 
                            @click="showAddModal = false" 
                            class="px-8 py-4 bg-slate-100 text-slate-600 rounded-2xl font-semibold hover:bg-slate-200 transition-all">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection