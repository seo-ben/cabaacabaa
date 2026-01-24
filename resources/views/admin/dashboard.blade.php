@extends('layouts.admin')

@section('title', 'Tableau de Bord Admin')

@section('content')
<div class=" bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 sm:gap-6">
            <div class="space-y-1 sm:space-y-2">
                <h1 class="text-2xl sm:text-4xl md:text-5xl font-bold bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 bg-clip-text text-transparent">
                    Tableau de Bord
                </h1>
                <p class="text-slate-600 text-sm sm:text-lg">Bienvenue, Administrateur</p>
            </div>
            
            <div class="hidden md:flex items-center gap-4">
                <div class="bg-white/80 backdrop-blur-lg px-6 py-4 rounded-2xl shadow-lg border border-white/20">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-slate-500 font-medium">Aujourd'hui</p>
                            <p class="text-sm font-bold text-slate-900">{{ now()->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Revenue Card -->
            <div class="group relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-20 -mt-20 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-bold text-white">
                            +12.5%
                        </span>
                    </div>
                    <p class="text-emerald-100 text-sm font-medium mb-2">Volume d'affaires</p>
                    <h3 class="text-3xl font-bold text-white mb-1">
                        {{ number_format($totalRevenue, 0, ',', ' ') }}
                    </h3>
                    <p class="text-emerald-100 text-xs">{{ $currency }}</p>
                </div>
            </div>

            <!-- Vendors Card -->
            <a href="{{ route('admin.vendors.index') }}" class="group relative overflow-hidden bg-white rounded-3xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 border border-slate-200">
                <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-full -mr-20 -mt-20 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-50 rounded-xl group-hover:bg-blue-100 transition-colors">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-blue-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <p class="text-slate-500 text-sm font-medium mb-2">Partenaires</p>
                    <h3 class="text-3xl font-bold text-slate-900 mb-1">{{ $totalVendeurs }}</h3>
                    <p class="text-xs text-blue-600 font-medium group-hover:underline">Voir la liste</p>
                </div>
            </a>

            <!-- Pending Card -->
            <a href="{{ route('admin.vendors.index', ['status' => 'en_cours']) }}" class="group relative overflow-hidden bg-white rounded-3xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 border border-slate-200">
                @if($pendingVendeursCount > 0)
                    <div class="absolute top-4 right-4 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                    </div>
                @endif
                <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-orange-50 to-amber-50 rounded-full -mr-20 -mt-20 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-orange-50 rounded-xl group-hover:bg-orange-100 transition-colors">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-orange-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <p class="text-slate-500 text-sm font-medium mb-2">En attente</p>
                    <h3 class="text-3xl font-bold text-slate-900 mb-1">{{ $pendingVendeursCount }}</h3>
                    <p class="text-xs text-orange-600 font-medium group-hover:underline">Demandes à valider</p>
                </div>
            </a>

            <!-- Settings Card -->
            <a href="{{ route('admin.settings.index') }}" class="group relative overflow-hidden bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-white/10 rounded-xl backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-white group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <p class="text-slate-400 text-sm font-medium mb-2">Configuration</p>
                    <h3 class="text-3xl font-bold text-white mb-1">Système</h3>
                    <p class="text-xs text-slate-400 font-medium group-hover:text-white transition-colors">Paramètres API</p>
                </div>
            </a>

        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Recent Partners -->
            <div class="lg:col-span-1">
                <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-xl border border-white/20 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-slate-900">Nouveaux Partenaires</h3>
                        <a href="{{ route('admin.vendors.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline">
                            Voir tout
                        </a>
                    </div>
                    <div class="p-4 space-y-2 max-h-96 overflow-y-auto">
                        @foreach($vendeursRecent as $v)
                            <a href="{{ route('admin.vendors.show', $v->id_vendeur) }}" class="flex items-center gap-4 p-4 rounded-2xl hover:bg-blue-50 transition-all group">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg group-hover:scale-110 transition-transform">
                                    {{ substr($v->nom_commercial, 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-slate-900 truncate group-hover:text-blue-600 transition-colors">
                                        {{ $v->nom_commercial }}
                                    </p>
                                    <p class="text-xs text-slate-500 uppercase font-medium">
                                        {{ $v->type_vendeur }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-slate-400 font-medium">
                                        {{ optional($v->date_inscription)->format('d/m/Y') }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="lg:col-span-2">
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-slate-900 px-2">Accès Rapide</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- Finance -->
                        <a href="{{ route('admin.finance.index') }}" class="group bg-white/80 backdrop-blur-lg rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-white/20">
                            <div class="flex items-start gap-4">
                                <div class="p-4 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl group-hover:scale-110 transition-transform shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-slate-900 mb-1">Finance</h4>
                                    <p class="text-sm text-slate-600">Paiements & Retraits</p>
                                    <div class="mt-3 flex items-center text-green-600 text-sm font-medium group-hover:underline">
                                        Accéder
                                        <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <!-- Zones -->
                        <a href="{{ route('admin.zones.index') }}" class="group bg-white/80 backdrop-blur-lg rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-white/20">
                            <div class="flex items-start gap-4">
                                <div class="p-4 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl group-hover:scale-110 transition-transform shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-slate-900 mb-1">Zones</h4>
                                    <p class="text-sm text-slate-600">Gestion Géographique</p>
                                    <div class="mt-3 flex items-center text-blue-600 text-sm font-medium group-hover:underline">
                                        Accéder
                                        <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <!-- Users -->
                        <a href="{{ route('admin.users.index') }}" class="group bg-white/80 backdrop-blur-lg rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-white/20">
                            <div class="flex items-start gap-4">
                                <div class="p-4 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl group-hover:scale-110 transition-transform shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-slate-900 mb-1">Clients</h4>
                                    <p class="text-sm text-slate-600">Base Utilisateurs</p>
                                    <div class="mt-3 flex items-center text-purple-600 text-sm font-medium group-hover:underline">
                                        Accéder
                                        <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <!-- Orders -->
                        <a href="{{ route('admin.orders.index') }}" class="group bg-white/80 backdrop-blur-lg rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-white/20">
                            <div class="flex items-start gap-4">
                                <div class="p-4 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl group-hover:scale-110 transition-transform shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-slate-900 mb-1">Commandes</h4>
                                    <p class="text-sm text-slate-600">Suivi des Ventes</p>
                                    <div class="mt-3 flex items-center text-orange-600 text-sm font-medium group-hover:underline">
                                        Accéder
                                        <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>

                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection