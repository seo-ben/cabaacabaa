@extends('layouts.admin')

@section('title', 'Gestion des Catégories de Vendeurs')

@section('content')
<div class="space-y-6" x-data="{ 
    openModal: false, 
    selectedCategory: null, 
    vendors: [],
    showVendors(category) {
        console.log('Opening modal for:', category);
        this.selectedCategory = category;
        this.vendors = category.vendeurs || [];
        this.openModal = true;
    }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight uppercase">Catégories de Boutiques</h1>
            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-1">Types de commerces (Supermarchés, Restaurants, etc.)</p>
        </div>
        <a href="{{ route('admin.vendor-categories.create') }}" class="px-5 py-3 bg-gray-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-xl hover:bg-black transition flex items-center gap-2 active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Nouvelle Catégorie
        </a>
    </div>

    @if(session('success'))
        <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-600 font-black text-[10px] uppercase tracking-widest flex items-center justify-between animate-fade-in shadow-sm">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- Table Layout -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Catégorie</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Description</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Boutiques</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Statut</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($categories as $category)
                        <tr class="group hover:bg-gray-50/50 transition-all">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-lg group-hover:scale-110 transition-transform text-gray-900 border border-gray-100 shadow-sm">
                                        @if(\Illuminate\Support\Str::startsWith($category->icon, ['M', 'm']))
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $category->icon }}"/>
                                            </svg>
                                        @elseif(\Illuminate\Support\Str::startsWith($category->icon, '<svg'))
                                            {!! $category->icon !!}
                                        @else
                                            {{ $category->icon ?: '🏪' }}
                                        @endif
                                    </div>
                                    <span class="text-[12px] font-black text-gray-900 uppercase tracking-tight">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                <p class="text-[10px] font-medium text-gray-500 line-clamp-1 max-w-xs">{{ $category->description ?: 'Aucune description' }}</p>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <button type="button" @click='showVendors(@json($category))' class="px-3 py-1 bg-orange-50 text-orange-600 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 hover:text-white transition-all border border-orange-100 active:scale-95">
                                    {{ $category->vendeurs_count }} Voir
                                </button>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <span class="px-2 py-1 {{ $category->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-50 text-gray-400' }} rounded-lg text-[8px] font-black uppercase tracking-widest border {{ $category->is_active ? 'border-emerald-100' : 'border-gray-100' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.vendor-categories.edit', $category->id_category_vendeur) }}" class="p-2 bg-gray-50 text-gray-400 rounded-lg hover:text-gray-900 hover:bg-gray-100 transition-all active:scale-90 border border-transparent hover:border-gray-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.vendor-categories.destroy', $category->id_category_vendeur) }}" method="POST" onsubmit="return confirm('Supprimer cette catégorie ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 bg-gray-50 text-gray-400 rounded-lg hover:text-red-600 hover:bg-red-50 transition-all active:scale-90 border border-transparent hover:border-red-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-20 text-center">
                                <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center mx-auto mb-4 text-gray-200">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <h3 class="text-sm font-black text-gray-900 uppercase tracking-tight">Aucune catégorie</h3>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Commencez par en créer une nouvelle</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Vendors - Simplified Structure -->
    <div x-show="openModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" 
         x-cloak
         x-transition.opacity>
        
        <div class="bg-white rounded-2xl w-full max-w-3xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden border border-gray-100"
             @click.away="openModal = false">
            
            <div class="bg-gray-50/50 px-8 py-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight" x-text="'Boutiques : ' + (selectedCategory ? selectedCategory.name : '')"></h3>
                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-1">Liste des établissements enregistrés dans cette catégorie</p>
                </div>
                <button @click="openModal = false" class="p-2 bg-white text-gray-400 border border-gray-100 rounded-xl hover:text-red-600 transition-all shadow-sm active:scale-90">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-8 overflow-y-auto flex-1">
                <template x-if="vendors && vendors.length > 0">
                    <div class="space-y-3">
                        <template x-for="vendor in vendors" :key="vendor.id_vendeur">
                            <div class="bg-white border border-gray-100 p-4 rounded-2xl flex items-center justify-between group hover:border-gray-900 transition-all shadow-sm">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center border border-gray-100 text-gray-300 font-black text-xs uppercase" x-text="vendor.nom_commercial ? vendor.nom_commercial.substring(0, 2) : '??'"></div>
                                    <div>
                                        <h4 class="text-sm font-black text-gray-900 uppercase tracking-tight" x-text="vendor.nom_commercial"></h4>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest" x-text="vendor.telephone_commercial || 'Pas de numéro'"></span>
                                            <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                                            <span :class="{
                                                'text-emerald-500': vendor.statut_verification === 'valide' || vendor.statut_verification === 'verifie',
                                                'text-orange-500': vendor.statut_verification === 'en_cours',
                                                'text-red-500': vendor.statut_verification === 'rejete' || vendor.statut_verification === 'suspendu'
                                            }" class="text-[8px] font-black uppercase tracking-widest" x-text="vendor.statut_verification"></span>
                                        </div>
                                    </div>
                                </div>
                                <a :href="'/admin/vendors/' + vendor.id_vendeur" class="p-3 bg-gray-900 text-white rounded-xl hover:bg-black transition-all shadow-lg active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </div>
                        </template>
                    </div>
                </template>
                <template x-if="!vendors || vendors.length === 0">
                    <div class="py-12 text-center text-gray-300 uppercase">
                        <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <p class="text-sm font-black text-gray-900">Aucune boutique</p>
                        <p class="text-[10px] font-bold mt-1 tracking-widest">Il n'y a pas encore d'établissements dans cette catégorie.</p>
                    </div>
                </template>
            </div>

            <div class="bg-gray-50 px-8 py-4 flex justify-end border-t border-gray-100">
                <button @click="openModal = false" class="px-6 py-3 bg-white border border-gray-200 text-gray-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
