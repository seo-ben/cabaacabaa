@extends('layouts.admin')

@section('title', 'Gestion Vendeurs')

@section('content')
<div x-data="{ 
    showAddModal: false, 
    showQuickView: false,
    selectedVendor: null,
    searchQuery: '{{ request('search') }}', 
    statusFilter: '{{ request('status') }}',
    openQuickView(vendor) {
        console.log('Quick view for:', vendor);
        this.selectedVendor = vendor;
        this.showQuickView = true;
    }
}" @keydown.escape="showAddModal = false; showQuickView = false" class="space-y-4">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight leading-none uppercase">Partenaires & Boutiques</h1>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1.5 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span>
                Certification & Logistique
            </p>
        </div>
        
        <button @click="showAddModal = true" class="flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all active:scale-95 shadow-lg shadow-gray-900/10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Nouveau Vendeur
        </button>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white p-3 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Total</p>
            <p class="text-xl font-black text-gray-900 leading-none">{{ $stats['total'] }}</p>
        </div>

        <div class="bg-white p-3 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden">
            <p class="text-[8px] font-black text-emerald-500 uppercase tracking-widest mb-1">Actifs</p>
            <p class="text-xl font-black text-gray-900 leading-none">{{ $stats['active'] }}</p>
            <div class="absolute top-2 right-2 w-1 h-1 bg-emerald-500 rounded-full animate-ping"></div>
        </div>

        <div class="bg-white p-3 rounded-2xl border border-gray-100 shadow-sm relative">
            <p class="text-[8px] font-black text-orange-500 uppercase tracking-widest mb-1">En Attente</p>
            <p class="text-xl font-black text-gray-900 leading-none">{{ $stats['pending'] }}</p>
            @if($stats['pending'] > 0)
                <span class="absolute top-2 right-2 flex h-1.5 w-1.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-orange-500"></span>
                </span>
            @endif
        </div>

        <div class="bg-white p-3 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-[8px] font-black text-rose-500 uppercase tracking-widest mb-1">Suspendus</p>
            <p class="text-xl font-black text-gray-900 leading-none">{{ $stats['suspended'] }}</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-1 rounded-2xl border border-gray-100 shadow-sm">
        <form action="{{ route('admin.vendors.index') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center gap-1">
            <div class="flex-1 px-3 py-1">
                <div class="relative group">
                    <input type="text" name="search" x-model="searchQuery" placeholder="Recherche partenaire..." 
                           class="w-full pl-8 pr-4 py-2 bg-transparent border-none focus:ring-0 text-xs font-bold text-gray-900 placeholder:text-gray-300 placeholder:uppercase">
                    <svg class="absolute left-0 top-2 w-4 h-4 text-gray-300 group-focus-within:text-purple-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div class="h-6 w-px bg-gray-50 hidden lg:block mx-1"></div>

            <select name="status" x-model="statusFilter" class="px-3 py-2 bg-gray-50 border-none rounded-xl text-[10px] font-bold text-gray-600 focus:ring-0 mx-2">
                <option value="">Tous les statuts</option>
                <option value="non_verifie">Incomplets</option>
                <option value="en_cours">En attente</option>
                <option value="verifie">Vérifiés</option>
                <option value="suspendu">Suspendus</option>
            </select>

            <div class="flex items-center gap-1 p-1">
                <button type="submit" class="px-5 py-2.5 bg-gray-900 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-black transition-all shadow-lg active:scale-95">Filtrer</button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.vendors.index') }}" class="p-2.5 bg-gray-50 text-gray-400 hover:text-red-600 rounded-xl transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden text-[10px]">
        @if($vendeurs->count())
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="pl-4 pr-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Boutique</th>
                        <th class="hidden sm:table-cell px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Logistique</th>
                        <th class="px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Statut</th>
                        <th class="hidden lg:table-cell px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none text-center">État</th>
                        <th class="pr-4 pl-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($vendeurs as $v)
                    <tr class="group hover:bg-gray-50/50 transition-all duration-200">
                        <td class="pl-4 pr-3 py-1.5 whitespace-nowrap">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 font-black text-[10px] group-hover:bg-gray-900 group-hover:text-white transition-all shadow-inner">
                                    {{ substr($v->nom_commercial, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-black text-gray-900 truncate">{{ $v->nom_commercial }}</p>
                                    <p class="text-[7px] text-gray-400 font-bold uppercase truncate leading-none">{{ $v->user->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="hidden sm:table-cell px-3 py-1.5">
                            <span class="inline-flex px-1.5 py-0.5 rounded-md bg-blue-50 text-blue-600 font-black text-[7px] uppercase tracking-tighter">
                                {{ $v->category ? $v->category->name : ($v->type_vendeur ?? 'AUTRE') }}
                            </span>
                            <p class="text-[6px] text-gray-300 font-bold uppercase mt-0.5 italic">{{ $v->zone->nom ?? 'Hors zone' }}</p>
                        </td>
                        <td class="px-3 py-1.5">
                            @php
                                $statusMap = [
                                    'verifie' => ['text-emerald-500', 'CERTIFIÉ'],
                                    'en_cours' => ['text-orange-500', 'EN ATTENTE'],
                                    'non_verifie' => ['text-amber-500', 'INCOMPLET'],
                                    'suspendu' => ['text-rose-500', 'SUSPENDU'],
                                ];
                                $st = $statusMap[$v->statut_verification] ?? ['text-gray-400', 'INCONNU'];
                            @endphp
                            <span class="font-black {{ $st[0] }} tracking-tighter text-[9px]">{{ $st[1] }}</span>
                        </td>
                        <td class="hidden lg:table-cell px-3 py-1.5 text-center">
                            <span class="w-1.5 h-1.5 inline-block rounded-full {{ $v->actif ? 'bg-emerald-500' : 'bg-gray-200' }}"></span>
                        </td>
                        <td class="pr-4 pl-3 py-1.5 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button" @click='openQuickView(@json($v))' class="p-2 bg-gray-50 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all" title="Aperçu rapide">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <a href="{{ route('admin.vendors.show', $v->id_vendeur) }}" class="p-2 bg-gray-50 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-all" title="Dossier complet">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </a>
                                <a href="{{ route('admin.vendors.edit', $v->id_vendeur) }}" class="p-2 bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="px-4 py-2 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between text-[7px] font-bold text-gray-400 uppercase tracking-widest">
                <span>{{ $vendeurs->firstItem() }}-{{ $vendeurs->lastItem() }} sur {{ $vendeurs->total() }}</span>
                <div>{{ $vendeurs->links() }}</div>
            </div>
        @else
            <div class="py-12 text-center">
                <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest italic">Aucun partenaire trouvé</p>
                <a href="{{ route('admin.vendors.index') }}" class="inline-block mt-4 text-[8px] font-black text-purple-500 uppercase underline">Réinitialiser</a>
            </div>
        @endif
    </div>
</div>

<!-- Quick View Modal - Robust Structure -->
<div x-show="showQuickView" 
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" 
     x-cloak
     x-transition.opacity>
    
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-gray-100 flex flex-col"
         @click.away="showQuickView = false">
        
        <div class="p-6 bg-gray-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center font-black text-xl" x-text="selectedVendor ? selectedVendor.nom_commercial.substring(0, 1) : '?'"></div>
                <div>
                    <h2 class="text-sm font-black uppercase tracking-widest" x-text="selectedVendor ? selectedVendor.nom_commercial : ''"></h2>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5" x-text="selectedVendor && selectedVendor.category ? selectedVendor.category.name : 'Vendeur'"></p>
                </div>
            </div>
            <button @click="showQuickView = false" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-8 space-y-6">
            <!-- Info Grid -->
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Responsable</p>
                    <p class="text-xs font-bold text-gray-900" x-text="selectedVendor && selectedVendor.user ? selectedVendor.user.name : 'N/A'"></p>
                </div>
                <div>
                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Contact</p>
                    <p class="text-xs font-bold text-gray-900" x-text="selectedVendor ? selectedVendor.telephone_commercial : 'N/A'"></p>
                </div>
                <div>
                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Zone</p>
                    <p class="text-xs font-bold text-gray-900" x-text="selectedVendor && selectedVendor.zone ? selectedVendor.zone.nom : 'Hors zone'"></p>
                </div>
                <div>
                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Statut Vérif.</p>
                    <div class="flex items-center gap-2">
                         <span class="w-2 h-2 rounded-full" :class="{
                            'bg-emerald-500': selectedVendor && (selectedVendor.statut_verification === 'verifie' || selectedVendor.statut_verification === 'valide'),
                            'bg-orange-500': selectedVendor && selectedVendor.statut_verification === 'en_cours',
                            'bg-rose-500': selectedVendor && (selectedVendor.statut_verification === 'suspendu' || selectedVendor.statut_verification === 'rejete')
                         }"></span>
                         <span class="text-xs font-bold uppercase" x-text="selectedVendor ? (selectedVendor.statut_verification ? selectedVendor.statut_verification.replace('_', ' ') : 'INCONNU') : ''"></span>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-50 flex gap-3">
                <a :href="'/admin/vendors/' + (selectedVendor ? selectedVendor.id_vendeur : '')" class="flex-1 py-3 bg-gray-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all text-center shadow-lg active:scale-95 leading-none flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Dossier
                </a>
                <button @click="showQuickView = false" class="px-6 py-3 bg-gray-100 text-gray-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-200 transition-all active:scale-95">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Registration Modal -->
<div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4" x-cloak x-transition.opacity>
    <div @click.away="showAddModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-hidden flex flex-col border border-gray-100">
        <div class="px-6 py-5 bg-gray-900 text-white flex items-center justify-between">
            <h2 class="text-sm font-black uppercase tracking-widest">Nouveau Partenaire</h2>
            <button @click="showAddModal = false" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.vendors.store') }}" method="POST" class="p-8 space-y-4 overflow-y-auto">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Nom Responsable</label>
                    <input type="text" name="name" required class="w-full px-4 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-purple-500/10 transition-all">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Email Personnel</label>
                    <input type="email" name="email" required class="w-full px-4 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-purple-500/10 transition-all">
                </div>
            </div>
            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Nom Commercial (Boutique)</label>
                <input type="text" name="nom_commercial" required class="w-full px-4 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-purple-500/10 transition-all">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Catégorie</label>
                    <select name="id_category_vendeur" required class="w-full px-4 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-purple-500/10 transition-all">
                        @foreach($vendorCategories as $cat)
                            <option value="{{ $cat->id_category_vendeur }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Zone</label>
                    <select name="id_zone" class="w-full px-4 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-purple-500/10 transition-all">
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id_zone }}">{{ $zone->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-6">
                <button type="submit" class="flex-1 py-3 bg-gray-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all shadow-lg active:scale-95">Valider l'inscription</button>
                <button type="button" @click="showAddModal = false" class="px-6 py-3 bg-gray-100 text-gray-400 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-200 transition-all">Annuler</button>
            </div>
        </form>
    </div>
</div>
@endsection