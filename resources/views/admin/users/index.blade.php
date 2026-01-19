@extends('layouts.admin')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="container-fluid" x-data="{ 
    selectedUsers: [],
    selectAll: false,
    showCreateModal: {{ (session('showCreate') || $errors->any()) ? 'true' : 'false' }},
    showEditModal: false,
    showPasswordModal: false,
    showSuspendModal: false,
    editingUser: {},
    suspendingUser: {},

    toggleAll() {
        if (this.selectAll) {
            this.selectedUsers = Array.from(document.querySelectorAll('input[name=\'user_ids[]\']')).map(el => el.value);
        } else {
            this.selectedUsers = [];
        }
    },

    openEditModal(user) {
        this.editingUser = JSON.parse(JSON.stringify(user));
        this.showEditModal = true;
    },

    openPasswordModal(user) {
        this.editingUser = user;
        this.showPasswordModal = true;
    },

    openSuspendModal(user) {
        this.suspendingUser = user;
        this.showSuspendModal = true;
    }
}">
    <!-- Header Analysis -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Analyse de la Communauté</h1>
            <p class="text-gray-500 text-xs font-bold uppercase tracking-[0.2em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                Suivi des utilisateurs & sécurité
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-6">
            <div class="text-right">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Croissance</p>
                <div class="flex items-center gap-2 justify-end">
                    <span class="text-xs font-bold text-green-600">+12%</span>
                    <p class="text-lg font-black text-gray-900">{{ $statistics['total'] }}</p>
                </div>
            </div>
            <div class="w-px h-10 bg-gray-100 hidden md:block"></div>
            <div class="text-right">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Taux de Vérification</p>
                <p class="text-lg font-black text-blue-600">
                    {{ $statistics['total'] > 0 ? round((($statistics['total'] - $statistics['non_verifie']) / $statistics['total']) * 100) : 0 }}%
                </p>
            </div>
            <div class="flex items-center gap-3 ml-4">
                <a href="{{ route('admin.users.export', request()->all()) }}" class="p-3 bg-white border border-gray-100 rounded-2xl text-gray-600 hover:bg-gray-50 transition shadow-sm group">
                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </a>
                <button @click.stop="showCreateModal = true" class="px-6 py-3 bg-red-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-red-700 shadow-xl shadow-red-100 transition-all active:scale-95">
                    Nouveau membre
                </button>
            </div>
        </div>
    </div>
 
    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Total -->
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-[10px] font-black uppercase text-blue-500 bg-blue-50 px-2.5 py-1 rounded-lg">Base Totale</span>
            </div>
            <h3 class="text-3xl font-black text-gray-900 mb-1">{{ number_format($statistics['total'], 0, ',', ' ') }}</h3>
            <div class="flex items-center gap-2">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-tight">Membres inscrits</p>
            </div>
        </div>

        <!-- Role Split -->
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <span class="text-[10px] font-black uppercase text-purple-500 bg-purple-50 px-2.5 py-1 rounded-lg">Impact</span>
            </div>
            <div class="flex items-end gap-3 mb-1">
                <h3 class="text-3xl font-black text-gray-900">{{ $statistics['vendeurs'] }}</h3>
                <span class="text-sm font-bold text-gray-400 mb-1">Vendeurs</span>
            </div>
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-tight">& {{ $statistics['clients'] }} Clients</p>
        </div>

        <!-- Security Risk -->
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <span class="text-[10px] font-black uppercase text-orange-500 bg-orange-50 px-2.5 py-1 rounded-lg">Alerte Sécurité</span>
            </div>
            <h3 class="text-3xl font-black text-gray-900 mb-1">{{ $statistics['suspect'] + $statistics['verrouille'] }}</h3>
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-tight">Comptes à surveiller</p>
        </div>

        <!-- Status -->
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] font-black uppercase text-green-500 bg-green-50 px-2.5 py-1 rounded-lg">Santé Système</span>
            </div>
            <h3 class="text-3xl font-black text-gray-900 mb-1">{{ $statistics['actif'] }}</h3>
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-tight">Membres actifs</p>
        </div>
    </div>

    <!-- Bulk Actions Bar -->
    <div x-show="selectedUsers.length > 0" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="-translate-y-4 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         class="fixed top-24 left-1/2 -translate-x-1/2 z-40 w-full max-w-4xl px-4 pointer-events-none">
        <div class="bg-gray-900/95 backdrop-blur-xl p-4 rounded-3xl shadow-2xl shadow-red-500/10 border border-white/10 flex items-center justify-between pointer-events-auto">
            <div class="flex items-center gap-6 ml-4">
                <div class="flex flex-col">
                    <span class="text-[10px] font-black uppercase tracking-widest text-red-500">Sélection</span>
                    <span class="text-white font-black text-sm"><span x-text="selectedUsers.length"></span> utilisateurs</span>
                </div>
                <div class="h-8 w-px bg-white/10"></div>
                <form action="{{ route('admin.users.bulk-action') }}" method="POST" id="bulk-action-form" class="flex items-center gap-3">
                    @csrf
                    <template x-for="id in selectedUsers" :key="id">
                        <input type="hidden" name="user_ids[]" :value="id">
                    </template>
                    <select name="action" class="bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-xs font-bold text-white outline-none focus:ring-2 focus:ring-red-500 transition-all">
                        <option value="" class="bg-gray-900">Action groupée...</option>
                        <option value="activate" class="bg-gray-900">Activer les comptes</option>
                        <option value="suspend" class="bg-gray-900">Suspendre les comptes</option>
                        <option value="verify" class="bg-gray-900">Vérifier l'identité</option>
                        <option value="delete" class="bg-gray-900 text-red-400">Supprimer définitivement</option>
                    </select>
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-red-700 transition active:scale-95 shadow-lg shadow-red-600/20">
                        Appliquer
                    </button>
                </form>
            </div>
            <button @click="selectedUsers = []; selectAll = false" class="mr-4 p-2 text-gray-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    <div class="bg-white p-4 rounded-[2.5rem] border border-gray-100 shadow-sm mb-10">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1 relative group">
                <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher par nom, email, téléphone..." 
                       class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-transparent rounded-2xl text-sm font-bold text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-red-500 transition-all outline-none">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-red-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <select name="role" class="px-6 py-4 bg-gray-50 border border-transparent rounded-2xl text-[11px] font-black uppercase tracking-widest text-gray-600 outline-none focus:bg-white focus:ring-2 focus:ring-red-500 transition-all">
                    <option value="tous" {{ $role == 'tous' ? 'selected' : '' }}>Tous les rôles</option>
                    <option value="client" {{ $role == 'client' ? 'selected' : '' }}>Clients</option>
                    <option value="vendeur" {{ $role == 'vendeur' ? 'selected' : '' }}>Vendeurs</option>
                    <option value="admin" {{ $role == 'admin' ? 'selected' : '' }}>Administrateurs</option>
                </select>
                <select name="status" class="px-6 py-4 bg-gray-50 border border-transparent rounded-2xl text-[11px] font-black uppercase tracking-widest text-gray-600 outline-none focus:bg-white focus:ring-2 focus:ring-red-500 transition-all">
                    <option value="tous" {{ $status == 'tous' ? 'selected' : '' }}>Tous les statuts</option>
                    <option value="actif" {{ $status == 'actif' ? 'selected' : '' }}>Actifs</option>
                    <option value="inactif" {{ $status == 'inactif' ? 'selected' : '' }}>Inactifs</option>
                    <option value="suspendu" {{ $status == 'suspendu' ? 'selected' : '' }}>Suspendus</option>
                    <option value="supprime" {{ $status == 'supprime' ? 'selected' : '' }}>Supprimés</option>
                </select>
                <div class="flex items-center gap-3 px-4 bg-gray-50 rounded-2xl border border-transparent">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="verified" value="1" {{ request('verified') ? 'checked' : '' }} class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <span class="text-[10px] font-black uppercase text-gray-500 group-hover:text-gray-900 transition-colors">Vérifiés</span>
                    </label>
                    <div class="w-px h-4 bg-gray-200 mx-1"></div>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="suspicious" value="1" {{ request('suspicious') ? 'checked' : '' }} class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                        <span class="text-[10px] font-black uppercase text-gray-500 group-hover:text-gray-900 transition-colors">Suspects</span>
                    </label>
                    <div class="w-px h-4 bg-gray-200 mx-1"></div>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="locked" value="1" {{ request('locked') ? 'checked' : '' }} class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <span class="text-[10px] font-black uppercase text-gray-500 group-hover:text-gray-900 transition-colors">Verrouillés</span>
                    </label>
                </div>
                <button type="submit" class="px-8 py-4 bg-gray-900 text-white rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] hover:bg-black transition-all shadow-lg active:scale-95">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Main Table -->
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded-md border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer w-4 h-4">
                        </th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Utilisateur</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Profil & Engagement</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Niveau de Risque</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                    @php
                        $isToday = $user->date_creation && $user->date_creation->isToday();
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors group/row {{ $isToday ? 'bg-red-50/30' : '' }}">
                        <td class="px-8 py-4">
                            <input type="checkbox" name="user_ids[]" value="{{ $user->id_user }}" x-model="selectedUsers" class="rounded-md border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer w-4 h-4">
                        </td>
                        <td class="px-8 py-4">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->nom_complet) }}&background=random&color=fff&size=128" 
                                         class="w-12 h-12 rounded-[1rem] border-2 border-white shadow-sm ring-1 ring-gray-100" 
                                         alt="{{ $user->nom_complet }}">
                                    @if($user->isOnline())
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>
                                    @endif
                                </div>
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-sm font-black text-gray-900 leading-none">{{ $user->nom_complet }}</h4>
                                        @if($isToday)
                                            <span class="px-1.5 py-0.5 bg-red-100 text-red-600 text-[8px] font-black uppercase tracking-widest rounded">Nouveau</span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-500 mt-1">{{ $user->email }}</span>
                                    <span class="text-[10px] font-bold text-gray-400 mt-1">{{ $user->telephone }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2">
                                    @php
                                        $roleStyles = [
                                            'admin' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                            'vendeur' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'client' => 'bg-gray-50 text-gray-600 border-gray-100',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 text-[9px] font-black uppercase tracking-wider rounded-lg border {{ $roleStyles[$user->role] ?? 'bg-gray-50 text-gray-600' }}">
                                        {{ $user->role }}
                                    </span>
                                    @php
                                        $statusStyles = [
                                            'actif' => 'bg-green-50 text-green-600',
                                            'suspendu' => 'bg-orange-50 text-orange-600',
                                            'inactif' => 'bg-gray-50 text-gray-400',
                                            'supprime' => 'bg-red-50 text-red-600',
                                        ];
                                    @endphp
                                    <span class="text-[9px] font-black uppercase flex items-center gap-1.5 {{ $statusStyles[$user->status] ?? 'text-gray-400' }}">
                                        <span class="w-1 h-1 rounded-full bg-current"></span>
                                        {{ $user->status }}
                                    </span>
                                </div>
                                <p class="text-[10px] font-bold text-gray-400">
                                    {{ $user->commandes->count() }} commandes • Inscription {{ $user->date_creation ? $user->date_creation->diffForHumans() : 'N/A' }}
                                </p>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            <div class="space-y-2">
                                <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="h-full transition-all duration-500 {{ $user->risk_score > 5 ? 'bg-red-500' : 'bg-green-500' }}" 
                                         style="width: {{ min($user->risk_score * 10, 100) }}%"></div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] font-black uppercase tracking-widest {{ $user->risk_score > 5 ? 'text-red-600' : 'text-gray-400' }}">
                                        Score: {{ $user->risk_score }}/10
                                    </span>
                                    @if($user->is_verified)
                                        <div class="flex items-center gap-1 text-blue-600">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            <span class="text-[9px] font-black uppercase">Vérifié</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-4 ">
                            <div class="flex items-center justify-end gap-2 shrink-0 group-hover/row:opacity-100 transition-opacity">
                                <a href="{{ route('admin.users.show', $user->id_user) }}" class="p-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition active:scale-95" title="Détails">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <button @click.stop="openEditModal({{ json_encode($user) }})" class="p-2.5 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition active:scale-95" title="Modifier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="p-2.5 bg-gray-50 text-gray-400 rounded-xl hover:bg-gray-100 transition active:scale-95">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" 
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 p-2 z-50">
                                        @can('view_security')
                                        <a href="{{ route('admin.security.user', $user->id_user) }}" class="w-full flex items-center gap-3 px-4 py-2.5 text-[11px] font-black uppercase tracking-widest text-gray-700 hover:bg-gray-50 rounded-xl transition">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            Journal Sécurité
                                        </a>
                                        @endcan

                                        <button @click.stop="openPasswordModal({{ json_encode($user) }})" class="w-full flex items-center gap-3 px-4 py-2.5 text-[11px] font-black uppercase tracking-widest text-gray-700 hover:bg-gray-50 rounded-xl transition">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2V5a2 2 0 10-4 0v2m4 0h3a2 2 0 012 2v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2h3m4 0V5a2 2 0 012-2"/></svg>
                                            Changer MDP
                                        </button>

                                        @if($user->is_verified)
                                            <form action="{{ route('admin.users.unverify', $user->id_user) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-[11px] font-black uppercase tracking-widest text-red-500 hover:bg-red-50 rounded-xl transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Retirer Certification
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.users.verify', $user->id_user) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-[11px] font-black uppercase tracking-widest text-green-600 hover:bg-green-50 rounded-xl transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Certifier Compte
                                                </button>
                                            </form>
                                        @endif
                                        <button @click.stop="openSuspendModal({{ json_encode($user) }})" class="w-full flex items-center gap-3 px-4 py-2.5 text-[11px] font-black uppercase tracking-widest text-orange-600 hover:bg-orange-50 rounded-xl transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            Suspendre
                                        </button>
                                        <div class="h-px bg-gray-50 my-2 mx-2"></div>
                                        <form action="{{ route('admin.users.destroy', $user->id_user) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-[11px] font-black uppercase tracking-widest text-red-600 hover:bg-red-50 rounded-xl transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">
                            Aucun utilisateur trouvé
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Premium -->
        <div class="p-8 border-t border-gray-50 flex justify-center">
            {{ $users->appends(request()->query())->links('vendor.pagination.premium') }}
        </div>
    </div>

    <!-- CREATE MODAL -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <!-- Background overlay -->
            <div x-show="showCreateModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 @click="showCreateModal = false" 
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

            <!-- Modal panel -->
            <div x-show="showCreateModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.stop
                 class="relative inline-block align-middle bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full border border-gray-100 z-10">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-8 pt-8 pb-4">
                        <div class="flex justify-between items-center mb-8">
                            <div>
                                <h3 class="text-xl font-black text-gray-900 tracking-tight">Nouvel Utilisateur</h3>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Créez un compte manuellement</p>
                            </div>
                            <button type="button" @click="showCreateModal = false" class="p-2 text-gray-400 hover:text-gray-600 transition hover:bg-gray-50 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        @if($errors->any())
                            <div class="mb-6 p-4 bg-red-50 border-2 border-red-100 rounded-2xl">
                                <p class="text-[10px] font-black uppercase text-red-600 tracking-widest mb-2">Erreurs de validation :</p>
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li class="text-xs font-bold text-red-500">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Nom complet</label>
                                <input type="text" name="nom_complet" value="{{ old('nom_complet') }}" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none">
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Téléphone</label>
                                    <input type="text" name="telephone" value="{{ old('telephone') }}" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Mot de passe</label>
                                    <input type="password" name="password" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Confirmation</label>
                                    <input type="password" name="password_confirmation" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Rôle</label>
                                    <select name="role" class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none appearance-none">
                                        <option value="client">Client</option>
                                        <option value="vendeur">Vendeur</option>
                                        <option value="admin">Administrateur</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Statut initial</label>
                                    <select name="status" class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none appearance-none">
                                        <option value="actif">Actif</option>
                                        <option value="inactif">Inactif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-8 py-6 sm:flex sm:flex-row-reverse gap-4 mt-4">
                        <button type="submit" class="w-full sm:w-auto px-8 py-4 bg-red-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-red-700 shadow-lg shadow-red-100 transition active:scale-95 font-bold">Créer le compte</button>
                        <button type="button" @click="showCreateModal = false" class="mt-3 sm:mt-0 w-full sm:w-auto px-8 py-4 bg-white border border-gray-200 text-gray-600 rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-gray-50 transition font-bold">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <!-- Background overlay -->
            <div x-show="showEditModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 @click="showEditModal = false" 
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

            <!-- Modal panel -->
            <div x-show="showEditModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.stop
                 class="relative inline-block align-middle bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full border border-gray-100 z-10">
                <form :action="'{{ url('/admin/users') }}/' + editingUser.id_user" method="POST">
                    @csrf @method('PUT')
                    <div class="bg-white px-8 pt-8 pb-4">
                        <div class="flex justify-between items-center mb-8">
                            <div>
                                <h3 class="text-xl font-black text-gray-900 tracking-tight">Modifier le Profil</h3>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Édition de : <span x-text="editingUser.nom_complet" class="text-red-600"></span></p>
                            </div>
                            <button type="button" @click="showEditModal = false" class="p-2 text-gray-400 hover:text-gray-600 transition hover:bg-gray-50 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        @if($errors->any())
                            <div class="mb-6 p-4 bg-red-50 border-2 border-red-100 rounded-2xl">
                                <p class="text-[10px] font-black uppercase text-red-600 tracking-widest mb-2">Erreurs rencontrées :</p>
                                <ul class="list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li class="text-xs font-bold text-red-500">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Nom complet</label>
                                <input type="text" name="nom_complet" x-model="editingUser.nom_complet" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none">
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Email</label>
                                    <input type="email" name="email" x-model="editingUser.email" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Téléphone</label>
                                    <input type="text" name="telephone" x-model="editingUser.telephone" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Rôle</label>
                                    <select name="role" x-model="editingUser.role" class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none appearance-none">
                                        <option value="client">Client</option>
                                        <option value="vendeur">Vendeur</option>
                                        <option value="admin">Administrateur</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Statut</label>
                                    <select name="status" x-model="editingUser.status" class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none appearance-none">
                                        <option value="actif">Actif</option>
                                        <option value="inactif">Inactif</option>
                                        <option value="suspendu">Suspendu</option>
                                        <option value="en_attente_verification">En attente</option>
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Langue préférée</label>
                                <select name="langue_preferee" x-model="editingUser.langue_preferee" class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none appearance-none">
                                    <option value="fr">Français</option>
                                    <option value="en">Anglais</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-8 py-6 sm:flex sm:flex-row-reverse gap-4 mt-4">
                        <button type="submit" class="w-full sm:w-auto px-8 py-4 bg-red-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-red-700 shadow-lg shadow-red-100 transition active:scale-95 font-bold">Enregistrer</button>
                        <button type="button" @click="showEditModal = false" class="mt-3 sm:mt-0 w-full sm:w-auto px-8 py-4 bg-white border border-gray-200 text-gray-600 rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-gray-50 transition font-bold">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- PASSWORD MODAL -->
    <div x-show="showPasswordModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <!-- Background overlay -->
            <div x-show="showPasswordModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                 @click="showPasswordModal = false" 
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

            <!-- Modal panel -->
            <div x-show="showPasswordModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.stop
                 class="relative inline-block align-middle bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md sm:w-full border border-gray-100 z-10">
                <form :action="'{{ url('/admin/users') }}/' + editingUser.id_user + '/reset-password'" method="POST">
                    @csrf @method('PATCH')
                    <div class="bg-white px-8 pt-8 pb-4">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 7a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2V5a2 2 0 10-4 0v2m4 0h3a2 2 0 012 2v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2h3m4 0V5a2 2 0 012-2"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 tracking-tight">Réinitialiser</h3>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Sécurité du compte</p>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Nouveau mot de passe</label>
                                <input type="password" name="new_password" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Confirmer</label>
                                <input type="password" name="new_password_confirmation" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-8 py-6 sm:flex sm:flex-row-reverse gap-4 mt-4">
                        <button type="submit" class="w-full sm:w-auto px-8 py-4 bg-red-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-red-700 shadow-lg shadow-red-100 transition active:scale-95 font-bold">Confirmer</button>
                        <button type="button" @click="showPasswordModal = false" class="mt-3 sm:mt-0 w-full sm:w-auto px-8 py-4 bg-white border border-gray-200 text-gray-600 rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-gray-50 transition font-bold">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SUSPEND MODAL -->
    <div x-show="showSuspendModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <!-- Background overlay -->
            <div x-show="showSuspendModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                 @click="showSuspendModal = false" 
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

            <!-- Modal panel -->
            <div x-show="showSuspendModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.stop
                 class="relative inline-block align-middle bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md sm:w-full border border-gray-100 z-10">
                <form :action="'{{ url('/admin/users') }}/' + suspendingUser.id_user + '/suspend'" method="POST">
                    @csrf @method('PATCH')
                    <div class="bg-white px-8 pt-8 pb-4">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 tracking-tight">Suspendre</h3>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Action de sécurité</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 font-medium mb-6 px-1">Veuillez indiquer le motif de la suspension pour <strong x-text="suspendingUser.nom_complet" class="text-gray-900"></strong>.</p>
                        <textarea name="reason" required rows="4" class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-orange-500 transition-all outline-none placeholder-gray-400" placeholder="Ex: Comportement suspect, non-respect des CGU..."></textarea>
                    </div>
                    <div class="bg-gray-50 px-8 py-6 sm:flex sm:flex-row-reverse gap-4 mt-4">
                        <button type="submit" class="w-full sm:w-auto px-8 py-4 bg-orange-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-orange-700 shadow-lg shadow-orange-100 transition active:scale-95 font-bold">Confirmer</button>
                        <button type="button" @click="showSuspendModal = false" class="mt-3 sm:mt-0 w-full sm:w-auto px-8 py-4 bg-white border border-gray-200 text-gray-600 rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-gray-50 transition font-bold">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
