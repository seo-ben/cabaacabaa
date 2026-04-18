@extends('layouts.admin')

@section('title', 'Profil Utilisateur - ' . $user->nom_complet)

@section('content')
<div class="space-y-4" x-data="{ activeTab: 'stats', showEditModal: false }">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <nav class="flex text-[8px] font-black uppercase tracking-widest text-gray-400 mb-2">
                <a href="{{ route('admin.users.index') }}" class="hover:text-red-600 transition">Utilisateurs</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900">Détails Profil</span>
            </nav>
            <h1 class="text-xl font-black text-gray-900 tracking-tight leading-none uppercase flex items-center gap-3">
                {{ $user->nom_complet }}
                <span class="px-2.5 py-1 text-[8px] font-black uppercase tracking-widest rounded-lg border {{ $user->status === 'actif' ? 'bg-emerald-50 text-emerald-500 border-emerald-100' : 'bg-rose-50 text-rose-500 border-rose-100' }}">
                    {{ $user->status }}
                </span>
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <button @click="showEditModal = true" class="px-3 py-2 bg-white border border-gray-100 rounded-xl text-[9px] font-black uppercase tracking-widest text-gray-600 hover:bg-gray-50 transition-all shadow-sm flex items-center gap-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                Modifier
            </button>
            @if($user->status === 'actif')
                <form action="{{ route('admin.users.suspend', $user->id_user) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="reason" value="Suspension administrative">
                    <button type="submit" class="px-3 py-2 bg-rose-50 text-rose-500 border border-rose-100 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-rose-100 transition shadow-sm flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        Suspendre
                    </button>
                </form>
            @endif
        </div>
    </div>
 
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Left Column -->
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="h-20 bg-gray-900 relative flex items-center justify-center overflow-hidden">
                    <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
                </div>
                <div class="px-5 pb-6 text-center">
                    <div class="relative -mt-10 mb-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->nom_complet) }}&background=fff&color=000&size=150&bold=true" 
                             class="w-20 h-20 rounded-2xl border-4 border-white shadow-xl mx-auto" alt="{{ $user->nom_complet }}">
                        @if($user->isOnline())
                            <span class="absolute bottom-1 right-1/2 translate-x-8 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></span>
                        @endif
                    </div>
                    
                    <h2 class="text-lg font-black text-gray-900 leading-tight mb-1 uppercase">{{ $user->nom_complet }}</h2>
                    <span class="px-2 py-0.5 bg-gray-50 text-gray-400 rounded text-[7px] font-black uppercase tracking-widest">{{ $user->role }}</span>
                    
                    <div class="mt-4 space-y-2 text-left">
                        <div class="p-3 bg-gray-50/50 rounded-xl flex items-center gap-3 border border-transparent">
                            <div class="w-8 h-8 bg-white rounded-lg shadow-inner flex items-center justify-center text-gray-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[7px] font-black text-gray-300 uppercase tracking-widest leading-none mb-1">Email</p>
                                <p class="text-[10px] font-bold text-gray-700 truncate uppercase">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="p-3 bg-gray-50/50 rounded-xl flex items-center gap-3 border border-transparent">
                            <div class="w-8 h-8 bg-white rounded-lg shadow-inner flex items-center justify-center text-gray-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[7px] font-black text-gray-300 uppercase tracking-widest leading-none mb-1">Mobile</p>
                                <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest">{{ $user->telephone ?: 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
 
            <!-- Security Scoring -->
            <div class="bg-gray-900 rounded-2xl p-4 text-white shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-red-500/10 rounded-full -mr-8 -mt-8 blur-3xl"></div>
                
                <p class="text-[8px] font-black uppercase tracking-widest text-gray-500 mb-4">Risk Analytical Engine</p>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-end mb-1.5">
                            <span class="text-[7px] font-black text-gray-400 uppercase tracking-widest">Platform Trust Score</span>
                            <span class="text-sm font-black {{ ($user->risk_score ?? 0) > 5 ? 'text-rose-500' : 'text-emerald-500' }} tracking-tighter">{{ $user->risk_score ?? 0 }}/10</span>
                        </div>
                        <div class="h-1.5 bg-gray-800 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ ($user->risk_score ?? 0) > 5 ? 'bg-rose-500' : 'bg-emerald-500' }} transition-all duration-1000" style="width: {{ ($user->risk_score ?? 0) * 10 }}%"></div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        @forelse($suspiciousFlags as $flag)
                            <div class="flex items-center gap-2 px-2.5 py-1.5 bg-gray-800/50 rounded-lg border border-gray-700/50">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                <span class="text-[7px] font-black uppercase tracking-widest text-gray-300">{{ $flag }}</span>
                            </div>
                        @empty
                            <p class="text-[7px] text-center text-gray-600 font-bold uppercase tracking-widest py-2">Profil certifié • Risque négligeable</p>
                        @endforelse
                    </div>

                    @if($user->is_verified)
                        <div class="px-3 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center gap-3">
                            <div class="w-6 h-6 bg-emerald-500 rounded-lg flex items-center justify-center text-white">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <p class="text-[7px] font-black text-emerald-500 uppercase tracking-widest">Compte Vérifié</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
 
        <!-- Right Column -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Tabs -->
            <div class="bg-gray-50 border border-gray-100 p-1 rounded-2xl inline-flex">
                <button @click="activeTab = 'stats'" :class="activeTab === 'stats' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="px-4 py-2 rounded-xl font-black text-[9px] uppercase tracking-widest transition-all">Vue d'ensemble</button>
                <button @click="activeTab = 'auth'" :class="activeTab === 'auth' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="px-4 py-2 rounded-xl font-black text-[9px] uppercase tracking-widest transition-all ml-1">Sécurité</button>
                <button @click="activeTab = 'activity'" :class="activeTab === 'activity' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="px-4 py-2 rounded-xl font-black text-[9px] uppercase tracking-widest transition-all ml-1">Transactions</button>
            </div>
 
            <!-- Overview -->
            <div x-show="activeTab === 'stats'" class="space-y-4 animate-fade-in">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-white p-3 rounded-2xl border border-gray-100 shadow-sm text-center">
                        <p class="text-[7px] font-black text-gray-300 uppercase tracking-widest mb-1">Volume Cmds</p>
                        <p class="text-lg font-black text-gray-900 leading-none">{{ number_format($stats['commandes_total']) }}</p>
                    </div>
                    <div class="bg-white p-3 rounded-2xl border border-gray-100 shadow-sm text-center">
                        <p class="text-[7px] font-black text-gray-300 uppercase tracking-widest mb-1">Livraisons</p>
                        <p class="text-lg font-black text-emerald-500 leading-none">{{ $stats['commandes_total'] > 0 ? round(($stats['commandes_completes'] / $stats['commandes_total']) * 100) : 0 }}%</p>
                    </div>
                    <div class="bg-white p-3 rounded-2xl border border-gray-100 shadow-sm text-center">
                        <p class="text-[7px] font-black text-gray-300 uppercase tracking-widest mb-1">Dépenses</p>
                        <p class="text-lg font-black text-rose-500 leading-none">{{ number_format($stats['montant_total'], 0, ',', ' ') }} F</p>
                    </div>
                    <div class="bg-white p-3 rounded-2xl border border-gray-100 shadow-sm text-center">
                        <p class="text-[7px] font-black text-gray-300 uppercase tracking-widest mb-1">Engagement ★</p>
                        <p class="text-lg font-black text-yellow-500 leading-none">{{ number_format($stats['note_moyenne'], 1) }}</p>
                    </div>
                </div>
 
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 overflow-hidden">
                    <p class="text-[9px] font-black text-gray-900 uppercase tracking-widest border-b border-gray-50 pb-2 mb-4">Flux d'Activité Récent</p>
                    <div class="space-y-4">
                        @forelse($user->commandes()->latest('date_commande')->take(3)->get() as $cmd)
                            <div class="flex gap-3 relative">
                                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center shrink-0 border border-gray-100">
                                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-[10px] font-black text-gray-900 leading-none uppercase">Cmd #{{ $cmd->numero_commande }}</p>
                                        <span class="text-[7px] font-black text-gray-300">{{ $cmd->date_commande->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-[9px] text-gray-400 mt-1 uppercase font-bold truncate">Achat de {{ number_format($cmd->montant_total) }} F chez {{ $cmd->vendeur->nom_commercial ?? 'Boutique' }}</p>
                                </div>
                                <span class="px-2 py-0.5 bg-gray-50 text-gray-400 border border-gray-100 rounded text-[7px] font-black uppercase h-fit">{{ $cmd->statut }}</span>
                            </div>
                        @empty
                            <p class="text-[8px] text-center text-gray-300 font-bold uppercase tracking-widest py-4">Inactif</p>
                        @endforelse
                    </div>
                </div>
            </div>
 
            <!-- Security Details -->
            <div x-show="activeTab === 'auth'" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden text-[10px]" style="display: none;">
                <div class="px-4 py-3 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="font-black text-gray-900 uppercase tracking-widest leading-none">Historique d'Audition</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="pl-4 pr-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Timestamp</th>
                                <th class="px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Origine IP</th>
                                <th class="px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Dispositif</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($loginHistory as $login)
                                <tr class="hover:bg-gray-50/50 transition-all">
                                    <td class="pl-4 pr-3 py-2.5">
                                        <p class="font-black text-gray-900 leading-none">{{ \Carbon\Carbon::parse($login['date'])->format('d/m/Y') }}</p>
                                        <p class="text-[7px] text-gray-300 font-bold mt-0.5">{{ \Carbon\Carbon::parse($login['date'])->format('H:i') }}</p>
                                    </td>
                                    <td class="px-3 py-2.5 font-bold font-mono text-gray-600">{{ $login['ip'] }}</td>
                                    <td class="px-3 py-2.5 text-[7px] text-gray-300 font-bold uppercase truncate max-w-[150px]">{{ $login['user_agent'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="p-8 text-center text-gray-300 font-bold uppercase text-[9px]">Vierge</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
 
            <!-- Detailed Orders -->
            <div x-show="activeTab === 'activity'" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden text-[10px]" style="display: none;">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="pl-4 pr-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Référence</th>
                            <th class="px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Point de Vente</th>
                            <th class="px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Net</th>
                            <th class="pr-4 pl-3 py-3 font-black text-gray-400 uppercase tracking-widest text-right leading-none">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($user->commandes()->latest('date_commande')->get() as $cmd)
                            <tr class="hover:bg-gray-50/50 transition-all">
                                <td class="pl-4 pr-3 py-2.5 font-black text-gray-900">#{{ $cmd->numero_commande }}</td>
                                <td class="px-3 py-2.5 font-bold text-gray-600 uppercase tracking-tighter truncate max-w-[120px]">{{ $cmd->vendeur->nom_commercial ?? 'N/A' }}</td>
                                <td class="px-3 py-2.5 font-black text-gray-900">{{ number_format($cmd->montant_total) }} F</td>
                                <td class="pr-4 pl-3 py-2.5 text-right">
                                    <a href="{{ route('admin.orders.show', $cmd->id_commande) }}" class="p-1.5 bg-gray-50 text-gray-400 hover:text-gray-900 rounded-lg transition-all inline-block">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-8 text-center text-gray-300 font-bold uppercase text-[9px]">Zéro transaction</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
        <div @click.away="showEditModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col scale-95" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
            <div class="px-5 py-4 bg-gray-900 text-white flex items-center justify-between">
                <h2 class="text-xs font-black uppercase tracking-widest">Modifier Profil</h2>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.users.update', $user->id_user) }}" method="POST" class="p-6 space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5 col-span-2">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">Nom complet</label>
                        <input type="text" name="nom_complet" value="{{ $user->nom_complet }}" required class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-red-500/10 transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" required class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-red-500/10 transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">Mobile</label>
                        <input type="text" name="telephone" value="{{ $user->telephone }}" class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-red-500/10 transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">Rôle</label>
                        <select name="role" class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-[10px] font-black uppercase text-gray-900 focus:bg-white focus:ring-2 focus:ring-red-500/10 outline-none">
                            <option value="client" {{ $user->role === 'client' ? 'selected' : '' }}>Client</option>
                            <option value="vendeur" {{ $user->role === 'vendeur' ? 'selected' : '' }}>Vendeur</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">Statut</label>
                        <select name="status" class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-[10px] font-black uppercase text-gray-900 focus:bg-white focus:ring-2 focus:ring-red-500/10 outline-none">
                            <option value="actif" {{ $user->status === 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="suspendu" {{ $user->status === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 py-3 bg-gray-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all shadow-lg active:scale-95">Mettre à jour</button>
                    <button type="button" @click="showEditModal = false" class="px-6 py-3 bg-gray-100 text-gray-400 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-200 transition-all">Retour</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
