@extends('layouts.admin')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div x-data="{ 
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
}" class="space-y-4">
    <!-- Header Analysis -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight leading-none uppercase">Analyse Communauté</h1>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1.5 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Suivi des utilisateurs & sécurité
            </p>
        </div>
        <div class="flex items-center gap-3">
             <div class="hidden md:flex flex-col items-end">
                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Croissance</p>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black text-emerald-600">+12%</span>
                    <p class="text-sm font-black text-gray-900">{{ $statistics['total'] }}</p>
                </div>
            </div>
            <div class="w-px h-6 bg-gray-100 hidden md:block mx-2"></div>
            <button @click.stop="showCreateModal = true" class="px-4 py-2.5 bg-gray-900 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-black transition-all active:scale-95 shadow-lg shadow-gray-900/10">
                Nouveau membre
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden group">
            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Membres</p>
            <p class="text-xl font-black text-gray-900 leading-none">{{ number_format($statistics['total'], 0, ',', ' ') }}</p>
            <div class="absolute -right-2 -bottom-2 w-10 h-10 bg-blue-50 rounded-full group-hover:scale-150 transition-transform"></div>
        </div>

        <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-[8px] font-black text-purple-500 uppercase tracking-widest mb-1.5">Vendeurs</p>
            <p class="text-xl font-black text-gray-900 leading-none">{{ $statistics['vendeurs'] }}</p>
        </div>

        <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-[8px] font-black text-orange-500 uppercase tracking-widest mb-1.5">Sécurité</p>
            <p class="text-xl font-black text-gray-900 leading-none">{{ $statistics['suspect'] + $statistics['verrouille'] }}</p>
        </div>

        <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-[8px] font-black text-emerald-500 uppercase tracking-widest mb-1.5">Santé Système</p>
            <p class="text-xl font-black text-gray-900 leading-none">{{ $statistics['actif'] }}</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-1.5 rounded-2xl border border-gray-100 shadow-sm">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center gap-1">
            <div class="flex-1 px-3 py-1">
                <div class="relative group">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Recherche utilisateur..." 
                           class="w-full pl-8 pr-4 py-2 bg-transparent border-none focus:ring-0 text-xs font-bold text-gray-900 placeholder:text-gray-300 placeholder:uppercase">
                    <svg class="absolute left-0 top-2 w-4 h-4 text-gray-300 group-focus-within:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div class="h-6 w-px bg-gray-50 hidden lg:block mx-1"></div>

            <div class="flex items-center gap-1.5 px-3">
                <select name="role" class="bg-gray-50 border-none rounded-xl text-[10px] font-bold text-gray-600 focus:ring-0">
                    <option value="tous">Tous les rôles</option>
                    <option value="client" {{ $role == 'client' ? 'selected' : '' }}>Clients</option>
                    <option value="vendeur" {{ $role == 'vendeur' ? 'selected' : '' }}>Vendeurs</option>
                    <option value="admin" {{ $role == 'admin' ? 'selected' : '' }}>Admins</option>
                </select>
                <select name="status" class="bg-gray-50 border-none rounded-xl text-[10px] font-bold text-gray-600 focus:ring-0">
                    <option value="tous">Tous les statuts</option>
                    <option value="actif" {{ $status == 'actif' ? 'selected' : '' }}>Actifs</option>
                    <option value="suspendu" {{ $status == 'suspendu' ? 'selected' : '' }}>Suspendus</option>
                </select>
            </div>

            <div class="flex items-center gap-1.5 p-1.5 ml-auto">
                <button type="submit" class="px-5 py-2.5 bg-gray-900 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-black transition-all shadow-lg">Appliquer</button>
                @if($search || $role != 'tous' || $status != 'tous')
                    <a href="{{ route('admin.users.index') }}" class="p-2.5 bg-gray-50 text-gray-400 hover:text-red-600 rounded-xl transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden text-[10px]">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="pl-4 pr-3 py-3 w-10">
                        <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded border-gray-300 text-red-600 focus:ring-red-500 w-3.5 h-3.5">
                    </th>
                    <th class="px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Utilisateur</th>
                    <th class="px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Profil & Engagement</th>
                    <th class="hidden lg:table-cell px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Risque</th>
                    <th class="pr-4 pl-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                <tr class="group hover:bg-gray-50/50 transition-all duration-200">
                    <td class="pl-4 pr-3 py-2.5">
                        <input type="checkbox" name="user_ids[]" value="{{ $user->id_user }}" x-model="selectedUsers" class="rounded border-gray-300 text-red-600 focus:ring-red-500 w-3.5 h-3.5 text-[10px]">
                    </td>
                    <td class="px-3 py-2.5">
                        <div class="flex items-center gap-2.5">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->nom_complet) }}&background=F1F5F9&color=64748B&bold=true" 
                                 class="w-8 h-8 rounded-lg shadow-inner">
                            <div class="min-w-0">
                                <p class="font-black text-gray-900 truncate">{{ $user->nom_complet }}</p>
                                <p class="text-[8px] text-gray-400 font-bold uppercase truncate">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-2.5">
                        <div class="flex items-center gap-2">
                             <span class="px-1.5 py-0.5 text-[8px] font-black uppercase tracking-widest rounded bg-gray-100 text-gray-600 border border-gray-200">
                                {{ $user->role }}
                            </span>
                            <span class="text-[8px] font-black uppercase tracking-tighter {{ $user->status == 'actif' ? 'text-emerald-500' : 'text-gray-400' }}">
                                {{ $user->status }}
                            </span>
                        </div>
                        <p class="text-[7px] text-gray-300 font-bold uppercase mt-1">{{ $user->commandes->count() }} commandes • {{ $user->date_creation ? $user->date_creation->diffForHumans() : 'N/A' }}</p>
                    </td>
                    <td class="hidden lg:table-cell px-3 py-2.5">
                        <div class="w-16 bg-gray-100 h-1 rounded-full overflow-hidden mb-1">
                            <div class="h-full {{ $user->risk_score > 5 ? 'bg-red-500' : 'bg-green-500' }}" style="width: {{ $user->risk_score * 10 }}%"></div>
                        </div>
                        <span class="text-[7px] font-black uppercase tracking-widest text-gray-400">Score: {{ $user->risk_score }}</span>
                    </td>
                    <td class="pr-4 pl-3 py-2.5 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ route('admin.users.show', $user->id_user) }}" class="p-2 bg-gray-50 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <button @click.stop="openEditModal({{ json_encode($user) }})" class="p-2 bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                             <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="p-2 bg-gray-50 text-gray-400 rounded-lg hover:bg-gray-100 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border border-gray-100 p-1.5 z-50 overflow-hidden">
                                     <button @click.stop="openPasswordModal({{ json_encode($user) }})" class="w-full flex items-center gap-2.5 px-3 py-2 text-[9px] font-black uppercase text-gray-700 hover:bg-gray-50 rounded-lg transition">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2V5a2 2 0 10-4 0v2m4 0h3a2 2 0 012 2v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2h3m4 0V5a2 2 0 012-2"/></svg>
                                        Changer MDP
                                    </button>
                                    <div class="h-px bg-gray-50 my-1"></div>
                                    <form action="{{ route('admin.users.destroy', $user->id_user) }}" method="POST" onsubmit="return confirm('Confirmer ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 text-[9px] font-black uppercase text-red-500 hover:bg-red-50 rounded-lg transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
                    <td colspan="5" class="px-5 py-12 text-center text-gray-300 font-bold uppercase text-[10px]">Aucun membre trouvé</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-4 py-3 bg-gray-50/50 border-t border-gray-100 flex justify-center">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Simplified Create Modal -->
<div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4">
    <div @click.away="showCreateModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-hidden flex flex-col">
        <div class="px-5 py-4 bg-gray-900 text-white flex items-center justify-between">
            <h2 class="text-xs font-black uppercase tracking-widest">Nouveau membre</h2>
            <button @click="showCreateModal = false" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-4 overflow-y-auto">
            @csrf
            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Nom complet</label>
                <input type="text" name="nom_complet" required class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-red-500/10 transition-all">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Email</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-red-500/10 transition-all">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Rôle</label>
                    <select name="role" class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-red-500/10 transition-all">
                        <option value="client">Client</option>
                        <option value="vendeur">Vendeur</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
             <div class="space-y-1.5">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Mot de passe</label>
                <input type="password" name="password" required class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-red-500/10 transition-all">
            </div>
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 py-3 bg-gray-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all shadow-lg">Créer compte</button>
                <button type="button" @click="showCreateModal = false" class="px-6 py-3 bg-gray-100 text-gray-400 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-200 transition-all">Annuler</button>
            </div>
        </form>
    </div>
</div>
@endsection
