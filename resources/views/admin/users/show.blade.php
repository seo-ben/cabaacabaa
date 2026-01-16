@extends('layouts.admin')

@section('title', 'Profil Utilisateur - ' . $user->nom_complet)

@section('content')
<div class="max-w-7xl mx-auto space-y-8" x-data="{ activeTab: 'stats', showEditModal: false }">
    <!-- Header/Breadcrumb -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2">
                <a href="{{ route('admin.users.index') }}" class="hover:text-red-600 transition">Utilisateurs</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900 font-medium">Détails du profil</span>
            </nav>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight flex items-center gap-3">
                {{ $user->nom_complet }}
                <span @class([
                    'px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-full border shadow-sm',
                    'bg-green-50 text-green-700 border-green-100' => $user->status === 'actif',
                    'bg-red-50 text-red-700 border-red-100' => $user->status === 'suspendu' || $user->status === 'supprime',
                    'bg-gray-50 text-gray-700 border-gray-100' => $user->status === 'inactif',
                ])>
                    {{ $user->status }}
                </span>
            </h1>
        </div>
        <div class="flex items-center gap-3">
            <button @click="showEditModal = true" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Modifier
            </button>
            @if($user->status === 'actif')
                <form action="{{ route('admin.users.suspend', $user->id_user) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="reason" value="Suspension administrative">
                    <button type="submit" class="px-4 py-2 bg-orange-50 text-orange-600 border border-orange-100 rounded-xl font-bold hover:bg-orange-100 transition shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        Suspendre
                    </button>
                </form>
            @elseif($user->status === 'suspendu')
                <form action="{{ route('admin.users.unsuspend', $user->id_user) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-green-50 text-green-600 border border-green-100 rounded-xl font-bold hover:bg-green-100 transition shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Réactiver
                    </button>
                </form>
            @endif
        </div>
    </div>
 
    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: User Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl overflow-hidden">
                <div class="h-32 bg-gray-900 flex items-center justify-center overflow-hidden">
                    <div class="w-full h-full opacity-30 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
                </div>
                <div class="px-8 pb-8 text-center">
                    <div class="relative -mt-16 mb-6">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->nom_complet) }}&background=fff&color=000&size=200" 
                             class="w-32 h-32 rounded-[2rem] border-8 border-white shadow-2xl mx-auto ring-1 ring-gray-100" 
                             alt="{{ $user->nom_complet }}">
                        @if($user->isOnline())
                            <span class="absolute bottom-2 right-1/2 translate-x-12 w-6 h-6 bg-green-500 border-4 border-white rounded-full"></span>
                        @endif
                    </div>
                    
                    <h2 class="text-2xl font-black text-gray-900 leading-tight mb-1">{{ $user->nom_complet }}</h2>
                    <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-lg text-[10px] font-black uppercase tracking-widest">{{ $user->role }}</span>
                    
                    <div class="mt-8 space-y-4 text-left">
                        <div class="p-4 bg-gray-50 rounded-2xl flex items-center gap-4 border border-transparent hover:border-gray-200 transition-colors">
                            <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Email</p>
                                <p class="text-sm font-black text-gray-900">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-2xl flex items-center gap-4 border border-transparent hover:border-gray-200 transition-colors">
                            <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Téléphone</p>
                                <p class="text-sm font-black text-gray-900">{{ $user->telephone ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
 
            <!-- Security Check Card -->
            <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-500/10 rounded-full -mr-16 -mt-16 blur-3xl"></div>
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-500">Sécurité & Scoring</h3>
                    <div class="w-10 h-10 bg-gray-800 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                    </div>
                </div>
                
                <div class="space-y-8">
                    <div>
                        <div class="flex justify-between items-end mb-3">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Risk Score Platform</span>
                            <span class="text-3xl font-black {{ $user->risk_score > 5 ? 'text-red-500' : 'text-green-500' }}">{{ $user->risk_score ?? 0 }}<span class="text-sm text-gray-600">/10</span></span>
                        </div>
                        <div class="h-3 bg-gray-800 rounded-full overflow-hidden p-0.5">
                            <div class="h-full rounded-full bg-gradient-to-r from-green-500 via-yellow-500 to-red-500 transition-all duration-1000" style="width: {{ ($user->risk_score ?? 0) * 10 }}%"></div>
                        </div>
                    </div>
 
                    <div class="space-y-4">
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest block">Signaux Détectés</span>
                        @forelse($suspiciousFlags as $flag)
                            <div class="group flex items-center justify-between p-4 bg-gray-800/50 rounded-2xl border border-gray-800 hover:border-gray-700 transition-all">
                                <div class="flex items-center gap-3">
                                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                                    <span class="text-[11px] font-black uppercase tracking-wider text-gray-200">{{ $flag }}</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </div>
                        @empty
                            <p class="text-xs text-center text-gray-600 italic py-4">Profil sain • Aucune anomalie</p>
                        @endforelse
                    </div>
 
                    @if($user->is_verified)
                        <div class="p-4 bg-blue-600/10 border border-blue-600/20 rounded-2xl flex items-center gap-4 group">
                            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/20 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-blue-500 uppercase tracking-widest">Identité Confirmée</p>
                                <p class="text-[11px] font-bold text-blue-400">Vérifié le {{ $user->email_verified_at ? $user->email_verified_at->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
 
        <!-- Right Column: Stats & Activity -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Tabs System -->
            <div class="bg-white p-2 rounded-3xl border border-gray-100 shadow-sm inline-flex mb-4">
                <button @click="activeTab = 'stats'" :class="activeTab === 'stats' ? 'bg-gray-900 text-white shadow-xl' : 'text-gray-500 hover:text-gray-900'" class="px-8 py-3 rounded-2xl font-black text-[11px] uppercase tracking-widest transition-all">Vue d'ensemble</button>
                <button @click="activeTab = 'auth'" :class="activeTab === 'auth' ? 'bg-gray-900 text-white shadow-xl' : 'text-gray-500 hover:text-gray-900'" class="px-8 py-3 rounded-2xl font-black text-[11px] uppercase tracking-widest transition-all ml-1">Sécurité</button>
                <button @click="activeTab = 'activity'" :class="activeTab === 'activity' ? 'bg-gray-900 text-white shadow-xl' : 'text-gray-500 hover:text-gray-900'" class="px-8 py-3 rounded-2xl font-black text-[11px] uppercase tracking-widest transition-all ml-1">Commandes</button>
            </div>
 
            <!-- Tab: Stats -->
            <div x-show="activeTab === 'stats'" x-transition class="space-y-8">
                <!-- Stats Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm text-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Volume Global</p>
                        <p class="text-3xl font-black text-gray-900">{{ number_format($stats['commandes_total']) }}</p>
                        <p class="text-[9px] font-bold text-gray-400 mt-1 uppercase">Commandes</p>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm text-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Taux Succès</p>
                        <p class="text-3xl font-black text-green-600">
                            {{ $stats['commandes_total'] > 0 ? round(($stats['commandes_completes'] / $stats['commandes_total']) * 100) : 0 }}%
                        </p>
                        <p class="text-[9px] font-bold text-gray-400 mt-1 uppercase">Livraison</p>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm text-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Dépenses</p>
                        <p class="text-3xl font-black text-red-600">{{ number_format($stats['montant_total'], 0, ',', ' ') }} {{ $currency }}</p>
                        <p class="text-[9px] font-bold text-gray-400 mt-1 uppercase">Total Cash</p>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm text-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Satisfaction</p>
                        <p class="text-3xl font-black text-yellow-500">{{ number_format($stats['note_moyenne'], 1) }}</p>
                        <p class="text-[9px] font-bold text-gray-400 mt-1 uppercase">★ Moyenne</p>
                    </div>
                </div>
 
                <!-- Activity Feed -->
                <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl p-8">
                    <h3 class="text-lg font-black text-gray-900 mb-8 flex items-center justify-between">
                        Dernières Interactions
                        <span class="w-12 h-1 bg-gray-100 rounded-full"></span>
                    </h3>
                    <div class="space-y-8">
                        @forelse($user->commandes()->latest('date_commande')->take(3)->get() as $cmd)
                            <div class="flex gap-6 relative group">
                                <div class="w-px h-full bg-gray-100 absolute left-6 top-12"></div>
                                <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center shrink-0 border border-gray-100 group-hover:bg-red-50 group-hover:border-red-100 transition-colors">
                                    <svg class="w-6 h-6 text-gray-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-3 mb-1">
                                        <h4 class="font-black text-gray-900 uppercase text-xs tracking-wider">Commande #{{ $cmd->numero_commande }}</h4>
                                        <span class="text-[10px] font-black text-gray-400">{{ $cmd->date_commande->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-500 font-medium">Validation d'un paiement de {{ number_format($cmd->montant_total) }} {{ $currency }} via {{ $cmd->vendeur->nom_vendeur }}</p>
                                    <div class="mt-3">
                                        <span class="px-2.5 py-1 bg-green-50 text-green-600 text-[9px] font-black uppercase tracking-widest rounded-lg border border-green-100">{{ $cmd->statut }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <p class="text-gray-400 text-sm font-bold uppercase tracking-widest">Aucun historique d'interaction</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
 
            <!-- Tab: Auth/Security Details -->
            <div x-show="activeTab === 'auth'" x-transition class="space-y-6">
                <!-- Login History Table -->
                <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-2xl overflow-hidden">
                    <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                        <h3 class="font-black text-gray-900 uppercase text-sm tracking-widest">Logs de Sécurité</h3>
                        <span class="text-[10px] font-black text-red-600 px-3 py-1 bg-red-50 rounded-full uppercase tracking-tighter">{{ count($loginHistory) }} Sessions Actives</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Horodatage</th>
                                    <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Emplacement IP</th>
                                    <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Empreinte Appareil</th>
                                    <th class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Audit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($loginHistory as $login)
                                    <tr class="hover:bg-gray-50/30 transition-colors">
                                        <td class="px-8 py-5">
                                            <p class="text-xs font-black text-gray-900">{{ \Carbon\Carbon::parse($login['date'])->format('d M Y') }}</p>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase">{{ \Carbon\Carbon::parse($login['date'])->format('H:i:s') }}</p>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="text-xs font-black text-gray-700 font-mono">{{ $login['ip'] }}</span>
                                        </td>
                                        <td class="px-8 py-5 text-[10px] text-gray-400 font-bold uppercase tracking-tight max-w-xs truncate">
                                            {{ $login['user_agent'] }}
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-widest rounded-lg border border-emerald-100">Success</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-8 py-16 text-center text-gray-400 uppercase tracking-widest text-[10px] font-black">Aucun log détecté</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
 
            <!-- Tab: Activity/Orders -->
            <div x-show="activeTab === 'activity'" x-transition class="space-y-6">
                <!-- Detailed Orders Table -->
                <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-2xl overflow-hidden">
                    <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                        <h3 class="font-black text-gray-900 uppercase text-sm tracking-widest">Portefeuille Commandes</h3>
                        <div class="flex items-center gap-4">
                            <a href="{{ route('admin.orders.index', ['client_id' => $user->id_user]) }}" class="px-4 py-2 bg-gray-50 text-gray-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-100 transition">Voir toutes</a>
                            <a href="{{ route('admin.users.export', ['user_id' => $user->id_user]) }}" class="px-4 py-2 bg-red-50 text-red-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-100 transition">Exporter</a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Commande</th>
                                    <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Vendeur</th>
                                    <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Montant</th>
                                    <th class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($user->commandes()->latest('date_commande')->get() as $cmd)
                                    <tr class="hover:bg-gray-50/30 transition-colors">
                                        <td class="px-8 py-5 text-xs font-black text-gray-900">#{{ $cmd->numero_commande }}</td>
                                        <td class="px-8 py-5 text-xs font-bold text-gray-600">{{ $cmd->vendeur->nom_vendeur }}</td>
                                        <td class="px-8 py-5 text-xs font-black text-gray-900">{{ number_format($cmd->montant_total) }} {{ $currency }}</td>
                                        <td class="px-8 py-5 text-right">
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-gray-100 text-gray-600">{{ $cmd->statut }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-8 py-16 text-center text-gray-400 uppercase tracking-widest text-[10px] font-black">Aucune commande</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- EDIT MODAL -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <!-- Background overlay -->
            <div x-show="showEditModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                 @click="showEditModal = false" 
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

            <!-- Modal panel -->
            <div x-show="showEditModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.stop
                 class="relative inline-block align-middle bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full border border-gray-100 z-10">
                <form action="{{ route('admin.users.update', $user->id_user) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="bg-white px-8 pt-8 pb-4">
                        <div class="flex justify-between items-center mb-8">
                            <div>
                                <h3 class="text-xl font-black text-gray-900 tracking-tight">Modifier le Profil</h3>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Mise à jour des informations</p>
                            </div>
                            <button type="button" @click="showEditModal = false" class="p-2 text-gray-400 hover:text-gray-600 transition hover:bg-gray-50 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Nom complet</label>
                                <input type="text" name="nom_complet" value="{{ $user->nom_complet }}" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none">
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Email</label>
                                    <input type="email" name="email" value="{{ $user->email }}" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Téléphone</label>
                                    <input type="text" name="telephone" value="{{ $user->telephone }}" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Rôle</label>
                                    <select name="role" class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none appearance-none">
                                        <option value="client" {{ $user->role === 'client' ? 'selected' : '' }}>Client</option>
                                        <option value="vendeur" {{ $user->role === 'vendeur' ? 'selected' : '' }}>Vendeur</option>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrateur</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Statut</label>
                                    <select name="status" class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none appearance-none">
                                        <option value="actif" {{ $user->status === 'actif' ? 'selected' : '' }}>Actif</option>
                                        <option value="inactif" {{ $user->status === 'inactif' ? 'selected' : '' }}>Inactif</option>
                                        <option value="suspendu" {{ $user->status === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                                        <option value="en_attente_verification" {{ $user->status === 'en_attente_verification' ? 'selected' : '' }}>En attente</option>
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Langue préférée</label>
                                <select name="langue_preferee" class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 transition-all outline-none appearance-none">
                                    <option value="fr" {{ $user->langue_preferee === 'fr' ? 'selected' : '' }}>Français</option>
                                    <option value="en" {{ $user->langue_preferee === 'en' ? 'selected' : '' }}>Anglais</option>
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
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
