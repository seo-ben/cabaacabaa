@extends('layouts.admin')

@section('title', 'Tableau de Bord Admin')

@section('content')
<div class="space-y-10">
    <!-- Welcome Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Bonjour, l'Administrateur 👋</h1>
            <p class="text-gray-500 font-medium mt-1 text-sm uppercase tracking-widest">Voici l'état actuel de votre plateforme aujourd'hui</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="px-6 py-3 bg-white border border-gray-100 rounded-2xl shadow-sm">
                <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest block">Date</span>
                <span class="text-sm font-bold text-gray-900">{{ now()->translatedFormat('d F Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Revenue -->
        <div class="bg-gray-900 p-8 rounded-[2.5rem] text-white relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full scale-100 group-hover:scale-150 transition duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Volume d'affaires</p>
                <h3 class="text-3xl font-black">{{ number_format($totalRevenue, 0, ',', ' ') }} <span class="text-xs">{{ $currency }}</span></h3>
                <div class="mt-4 flex items-center gap-2 text-green-400">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/></svg>
                    <span class="text-[10px] font-black uppercase tracking-widest">En croissance</span>
                </div>
            </div>
        </div>

        <!-- Total Vendors -->
        <a href="{{ route('admin.vendors.index') }}" class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden group hover:border-red-500 transition-all">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-red-50 rounded-full scale-100 group-hover:scale-150 transition duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Partenaires</p>
                <h3 class="text-3xl font-black text-gray-900">{{ $totalVendeurs }}</h3>
                <div class="mt-4 text-red-600 flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase tracking-widest">Gérer la liste</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </div>
            </div>
        </a>

        <!-- Pending Approvals -->
        <a href="{{ route('admin.vendors.index', ['status' => 'en_cours']) }}" class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden group hover:border-blue-500 transition-all">
            @if($pendingVendeursCount > 0)
                <div class="absolute top-6 right-6 w-3 h-3 bg-blue-500 rounded-full animate-ping"></div>
            @endif
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-50 rounded-full scale-100 group-hover:scale-150 transition duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Attentes de validation</p>
                <h3 class="text-3xl font-black text-blue-600">{{ $pendingVendeursCount }}</h3>
                <div class="mt-4 text-blue-600 flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase tracking-widest">Voir les demandes</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </a>

        <!-- Configuration -->
        <a href="{{ route('admin.settings.index') }}" class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden group hover:border-gray-900 transition-all">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-gray-50 rounded-full scale-100 group-hover:scale-150 transition duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Système</p>
                <h3 class="text-3xl font-black text-gray-900">Config</h3>
                <div class="mt-4 text-gray-900 flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase tracking-widest">Paramètres API</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Recent Vendors List -->
        <div class="lg:col-span-1 space-y-6">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Nouveaux Partenaires</h3>
                <a href="{{ route('admin.vendors.index') }}" class="text-[10px] font-black text-red-600 uppercase tracking-widest hover:underline">Voir tout</a>
            </div>
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden p-4 space-y-2">
                @foreach($vendeursRecent as $v)
                    <a href="{{ route('admin.vendors.show', $v->id_vendeur) }}" class="flex items-center justify-between p-4 bg-gray-50/50 hover:bg-red-50 rounded-2xl transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-white border border-gray-100 rounded-xl flex items-center justify-center text-red-600 font-black text-sm group-hover:border-red-200">
                                {{ substr($v->nom_commercial, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-black text-gray-900 group-hover:text-red-600 transition-colors">{{ $v->nom_commercial }}</p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">{{ $v->type_vendeur }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ optional($v->date_inscription)->format('d/m/Y') }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- System Status & Quick Links -->
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Raccourcis & État</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Finance Card -->
                <a href="{{ route('admin.finance.index') }}" class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-6 group hover:border-green-500 transition-all">
                    <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-black text-gray-900 group-hover:text-green-600 transition-colors">Finance</h4>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Paiements & Retraits</p>
                    </div>
                </a>

                <!-- Zones Card -->
                <a href="{{ route('admin.zones.index') }}" class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-6 group hover:border-blue-500 transition-all">
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-black text-gray-900 group-hover:text-blue-600 transition-colors">Zones</h4>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Gestion Géographique</p>
                    </div>
                </a>

                <!-- Users Card -->
                <a href="{{ route('admin.users.index') }}" class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-6 group hover:border-yellow-500 transition-all">
                    <div class="w-14 h-14 bg-yellow-50 rounded-2xl flex items-center justify-center text-yellow-600 group-hover:bg-yellow-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-black text-gray-900 group-hover:text-yellow-600 transition-colors">Clients</h4>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Base Utilisateurs</p>
                    </div>
                </a>

                <!-- Orders Card -->
                <a href="{{ route('admin.orders.index') }}" class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-6 group hover:border-purple-500 transition-all">
                    <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-black text-gray-900 group-hover:text-purple-600 transition-colors">Commandes</h4>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Suivi des Ventes</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
