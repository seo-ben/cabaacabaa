@extends('layouts.admin')

@section('title', 'Gestion Vendeurs')

@section('content')
<div x-data="{ showAddModal: false, searchQuery: '{{ request('search') }}', statusFilter: '{{ request('status') }}' }" @keydown.escape="showAddModal = false">
    
    <!-- Header with Stats -->
    <div class="mb-10 space-y-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Gestion des Vendeurs</h1>
                <p class="text-gray-500 font-medium mt-1 text-sm uppercase tracking-widest">Contrôlez et validez les partenaires de la plateforme</p>
            </div>
            <button @click="showAddModal = true" class="px-8 py-4 bg-red-600 text-white rounded-[1.5rem] font-black shadow-xl shadow-red-200 hover:bg-red-700 transition flex items-center gap-3 active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Nouveau Vendeur
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-gray-50 rounded-full scale-0 group-hover:scale-110 transition duration-700"></div>
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Total Partenaires</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ $stats['total'] }}</h3>
                </div>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-green-50 rounded-full scale-0 group-hover:scale-110 transition duration-700"></div>
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Boutiques Actives</p>
                    <h3 class="text-3xl font-black text-green-600">{{ $stats['active'] }}</h3>
                </div>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-50 rounded-full scale-0 group-hover:scale-110 transition duration-700"></div>
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">En Attente</p>
                    <h3 class="text-3xl font-black text-blue-600">{{ $stats['pending'] }}</h3>
                </div>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-red-50 rounded-full scale-0 group-hover:scale-110 transition duration-700"></div>
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Comptes Suspendus</p>
                    <h3 class="text-3xl font-black text-red-600">{{ $stats['suspended'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm mb-10">
        <form action="{{ route('admin.vendors.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <input type="text" name="search" x-model="searchQuery" placeholder="Rechercher par nom, boutique ou email..." 
                    class="w-full pl-12 pr-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all">
                <svg class="absolute left-4 top-4 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <div class="w-full md:w-64">
                <select name="status" x-model="statusFilter" class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all appearance-none cursor-pointer">
                    <option value="">Tous les statuts</option>
                    <option value="non_verifie">Incomplets</option>
                    <option value="en_cours">En attente (Nouvelle demande)</option>
                    <option value="verifie">Vérifiés</option>
                    <option value="suspendu">Suspendus</option>
                </select>
            </div>
            <button type="submit" class="px-10 py-4 bg-gray-900 text-white rounded-2xl font-black uppercase text-[11px] tracking-widest hover:bg-black transition-all shadow-lg active:scale-95">
                Filtrer la liste
            </button>
            @if(request()->anyFilled(['search', 'status']))
                <a href="{{ route('admin.vendors.index') }}" class="px-6 py-4 bg-gray-100 text-gray-600 rounded-2xl font-black uppercase text-[11px] tracking-widest hover:bg-gray-200 transition-all flex items-center justify-center">
                    Effacer
                </a>
            @endif
        </form>
    </div>

    <!-- Vendors Table -->
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        @if($vendeurs->count())
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-8 py-6 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">Boutique & Partenaire</th>
                            <th class="px-8 py-6 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">Type & Zone</th>
                            <th class="px-8 py-6 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">Vérification</th>
                            <th class="px-8 py-6 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">Actif</th>
                            <th class="px-8 py-6 text-right text-[10px] font-black uppercase tracking-widest text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($vendeurs as $v)
                            <tr class="group hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-red-600 text-lg font-black group-hover:scale-110 transition-transform">
                                            {{ substr($v->nom_commercial, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-black text-gray-900 group-hover:text-red-600 transition-colors">{{ $v->nom_commercial }}</div>
                                            <div class="text-[11px] font-bold text-gray-400 uppercase mt-0.5 tracking-tight">{{ $v->user->name ?? 'Sans utilisateur' }} • {{ $v->user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-gray-700 capitalize">{{ $v->category ? $v->category->name : ($v->type_vendeur ?? 'Non défini') }}</span>
                                        <span class="text-[10px] font-bold text-gray-400 mt-1">{{ $v->zone->nom ?? 'Hors zone' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col gap-2">
                                        @php
                                            $statusConfig = [
                                                'verifie' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'label' => 'Vérifié'],
                                                'en_cours' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'label' => 'À Valider'],
                                                'non_verifie' => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-600', 'label' => 'Incomplet'],
                                                'suspendu' => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'label' => 'Suspendu'],
                                            ];
                                            $conf = $statusConfig[$v->statut_verification] ?? $statusConfig['non_verifie'];
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl {{ $conf['bg'] }} {{ $conf['text'] }} text-[9px] font-black uppercase tracking-widest w-fit border border-current opacity-80">
                                            {{ $conf['label'] }}
                                        </span>
                                        
                                        @if($v->document_identite || $v->justificatif_domicile)
                                            <div class="flex gap-3">
                                                @if($v->document_identite)
                                                    <a href="{{ route('admin.vendors.show-doc', [$v->id_vendeur, 'identite']) }}" target="_blank" class="text-[9px] font-black text-blue-600 hover:underline uppercase tracking-tighter">📄 ID</a>
                                                @endif
                                                @if($v->justificatif_domicile)
                                                    <a href="{{ route('admin.vendors.show-doc', [$v->id_vendeur, 'domicile']) }}" target="_blank" class="text-[9px] font-black text-blue-600 hover:underline uppercase tracking-tighter">🏠 Justif</a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-2">
                                        @if($v->actif)
                                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                            <span class="text-[10px] font-black text-green-600 uppercase">Actif</span>
                                        @else
                                            <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                                            <span class="text-[10px] font-black text-gray-400 uppercase">Inactif</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.vendors.show', $v->id_vendeur) }}" class="p-2.5 bg-gray-50 text-gray-400 rounded-xl hover:bg-gray-900 hover:text-white transition-all border border-gray-100" title="Voir Détails">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>

                                        <a href="{{ route('admin.vendors.edit', $v->id_vendeur) }}" class="p-2.5 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all border border-blue-100" title="Éditer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                        </a>

                                        <div x-data="{ open: false }" class="relative">
                                            <button @click="open = !open" class="p-2.5 bg-gray-50 text-gray-400 rounded-xl hover:bg-gray-100 transition active:scale-95">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                            </button>
                                            <div x-show="open" @click.away="open = false" 
                                                 x-transition:enter="transition ease-out duration-100"
                                                 x-transition:enter-start="opacity-0 scale-95"
                                                 x-transition:enter-end="opacity-100 scale-100"
                                                 class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 p-2 z-20">
                                                
                                                @if($v->statut_verification === 'verifie')
                                                    <form action="{{ route('admin.vendors.unverify', $v->id_vendeur) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-[11px] font-black uppercase tracking-widest text-red-500 hover:bg-red-50 rounded-xl transition">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            Retirer Certification
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.vendors.approve', $v->id_vendeur) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-[11px] font-black uppercase tracking-widest text-green-600 hover:bg-green-50 rounded-xl transition">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            Certifier Boutique
                                                        </button>
                                                    </form>
                                                @endif

                                                <div class="h-px bg-gray-50 my-2 mx-2"></div>

                                                @if($v->statut_verification === 'suspendu')
                                                    <form action="{{ route('admin.vendors.unsuspend', $v->id_vendeur) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-[11px] font-black uppercase tracking-widest text-green-600 hover:bg-green-50 rounded-xl transition">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            Réactiver
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.vendors.suspend', $v->id_vendeur) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-[11px] font-black uppercase tracking-widest text-orange-600 hover:bg-orange-50 rounded-xl transition">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                            Suspendre
                                                        </button>
                                                    </form>
                                                @endif

                                                <div class="h-px bg-gray-50 my-2 mx-2"></div>

                                                <form action="{{ route('admin.vendors.destroy', $v->id_vendeur) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ce vendeur ?')">
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
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Enhanced Pagination -->
            <div class="px-10 py-8 border-t border-gray-50 flex items-center justify-between bg-gray-50/20">
                <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Affichage de {{ $vendeurs->firstItem() }}-{{ $vendeurs->lastItem() }} sur {{ $vendeurs->total() }} résultats</p>
                <div>
                    {{ $vendeurs->links() }}
                </div>
            </div>
        @else
            <div class="py-32 text-center">
                <div class="w-24 h-24 bg-gray-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8 text-gray-200">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Aucun partenaire trouvé</h3>
                <p class="text-gray-400 font-medium mt-2">Essayez de modifier vos filtres ou lancez une nouvelle recherche.</p>
                <a href="{{ route('admin.vendors.index') }}" class="mt-8 inline-block px-10 py-4 bg-red-600 text-white rounded-2xl font-black uppercase tracking-widest text-[11px] shadow-xl shadow-red-200 hover:bg-red-700 transition">Réinitialiser</a>
            </div>
        @endif
    </div>

    <!-- Modal Ajouter Vendeur -->
    <div x-show="showAddModal" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-cloak>
        
        <div @click.away="showAddModal = false" class="relative bg-white rounded-[3rem] shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">
            <div class="px-10 py-8 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">Nouveau Partenaire</h2>
                    <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest mt-1">Création d'un accès établissement manuel</p>
                </div>
                <button @click="showAddModal = false" class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 hover:bg-gray-100 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.vendors.store') }}" method="POST" class="p-10 space-y-8 overflow-y-auto">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Nom du Responsable</label>
                        <input type="text" name="name" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Email Personnel</label>
                        <input type="email" name="email" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Nom de la Boutique</label>
                        <input type="text" name="nom_commercial" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Type de Boutique / Catégorie</label>
                        <select name="id_category_vendeur" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all appearance-none cursor-pointer">
                            <option value="">Sélectionner une catégorie...</option>
                            @foreach($vendorCategories as $cat)
                                <option value="{{ $cat->id_category_vendeur }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="type_vendeur" value="autre">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Téléphone Commercial</label>
                        <input type="text" name="telephone_commercial" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Zone de Couverture</label>
                        <select name="id_zone" class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all appearance-none cursor-pointer">
                            <option value="">Toute zone / Indéfini</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id_zone }}">{{ $zone->nom ?? 'Zone ' . $zone->id_zone }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Adresse Physique Complète</label>
                    <input type="text" name="adresse_complete" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all">
                </div>

                <div class="space-y-2">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Courte Description</label>
                    <textarea name="description" rows="3" class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-[2rem] font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all resize-none"></textarea>
                </div>

                <div class="flex gap-4 pt-4 sticky bottom-0 bg-white border-t mt-4 pb-2">
                    <button type="submit" class="flex-1 px-10 py-5 bg-red-600 text-white rounded-[2rem] font-black uppercase text-[11px] tracking-widest shadow-xl shadow-red-200 hover:bg-black hover:shadow-none transition-all active:scale-95">
                        Valider la création
                    </button>
                    <button type="button" @click="showAddModal = false" class="px-10 py-5 bg-gray-100 text-gray-600 rounded-[2rem] font-black uppercase text-[11px] tracking-widest hover:bg-gray-200 transition-all">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
