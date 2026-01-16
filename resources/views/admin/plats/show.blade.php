@extends('layouts.admin')

@section('title', 'Détails du Produit')

@section('content')
<div x-data="{ activeTab: 'details' }">
    <!-- Back Button & Actions -->
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.plats.index') }}" class="w-12 h-12 bg-white border border-gray-100 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-600 hover:border-red-100 transition shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ $plat->nom_plat }}</h1>
                <p class="text-gray-500 font-medium mt-1 text-sm uppercase tracking-widest">Publié par <span class="text-red-600">{{ $plat->vendeur->nom_commercial }}</span></p>
            </div>
        </div>
        <div class="flex gap-3">
            <form action="{{ route('admin.plats.toggle-availability', $plat->id_plat) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="px-8 py-4 {{ $plat->disponible ? 'bg-orange-600 shadow-orange-100' : 'bg-green-600 shadow-green-100' }} text-white rounded-[1.5rem] font-black shadow-xl hover:opacity-90 transition active:scale-95">
                    {{ $plat->disponible ? 'Désactiver le produit' : 'Activer le produit' }}
                </button>
            </form>
            <form action="{{ route('admin.plats.destroy', $plat->id_plat) }}" method="POST" onsubmit="return confirm('Supprimer ce plat définitivement ?')">
                @csrf @method('DELETE')
                <button type="submit" class="p-4 bg-white border border-gray-100 text-red-600 rounded-[1.5rem] font-black shadow-xl shadow-gray-100 hover:bg-red-50 hover:border-red-100 transition active:scale-95">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Sidebar Info -->
        <div class="lg:col-span-1 space-y-8">
            <!-- Product Image -->
            <div class="bg-white p-4 rounded-[3rem] border border-gray-100 shadow-sm">
                <div class="aspect-square rounded-[2.5rem] overflow-hidden bg-gray-50 border border-gray-50 relative group">
                    @if($plat->image_principale)
                        <img src="{{ asset('storage/' . $plat->image_principale) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-[10px] font-black uppercase tracking-widest mt-4">Aucune image</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stats Card -->
            <div class="bg-gray-900 p-8 rounded-[3rem] text-white shadow-2xl relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-32 h-32 bg-white/5 rounded-full blur-3xl"></div>
                <div class="relative z-10 space-y-6">
                    <div>
                        <p class="text-[10px] font-black uppercase text-gray-500 tracking-widest mb-1">Prix de vente</p>
                        <h4 class="text-3xl font-black {{ $plat->en_promotion ? 'text-orange-400' : 'text-white' }}">
                            {{ number_format($plat->en_promotion ? $plat->prix_promotion : $plat->prix, 0, ',', ' ') }} {{ $currency }}
                        </h4>
                        @if($plat->en_promotion)
                            <p class="text-xs font-bold text-gray-500 line-through mt-1">{{ number_format($plat->prix, 0, ',', ' ') }} {{ $currency }}</p>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-white/5 rounded-2xl border border-white/10">
                            <p class="text-[9px] font-black uppercase text-gray-500 tracking-widest mb-1">Commandes</p>
                            <p class="text-lg font-black">{{ $plat->nombre_commandes ?: 0 }}</p>
                        </div>
                        <div class="p-4 bg-white/5 rounded-2xl border border-white/10">
                            <p class="text-[9px] font-black uppercase text-gray-500 tracking-widest mb-1">Vues</p>
                            <p class="text-lg font-black">{{ $plat->nombre_vues ?: 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vendor Quick Info -->
            <div class="bg-white p-8 rounded-[3rem] border border-gray-100 shadow-sm">
                <h4 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6 border-b border-gray-50 pb-4">À propos du vendeur</h4>
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center text-red-600 text-xl font-black">
                        {{ substr($plat->vendeur->nom_commercial, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-black text-gray-900">{{ $plat->vendeur->nom_commercial }}</div>
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">{{ $plat->vendeur->type_vendeur }}</div>
                    </div>
                </div>
                <a href="{{ route('admin.vendors.show', $plat->id_vendeur) }}" class="block w-full py-4 bg-gray-50 text-gray-600 rounded-2xl text-center text-[10px] font-black uppercase tracking-widest hover:bg-gray-900 hover:text-white transition-all">Voir la boutique complète</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Tabs -->
            <div class="flex p-2 bg-white border border-gray-100 rounded-[2rem] shadow-sm">
                <button @click="activeTab = 'details'" :class="activeTab === 'details' ? 'bg-red-600 text-white shadow-lg shadow-red-100' : 'text-gray-400 hover:text-gray-600'" class="flex-1 py-4 px-6 rounded-[1.5rem] text-[11px] font-black uppercase tracking-widest transition-all">Informations</button>
                <button @click="activeTab = 'options'" :class="activeTab === 'options' ? 'bg-red-600 text-white shadow-lg shadow-red-100' : 'text-gray-400 hover:text-gray-600'" class="flex-1 py-4 px-6 rounded-[1.5rem] text-[11px] font-black uppercase tracking-widest transition-all">Options & Variantes</button>
            </div>

            <!-- Details Tab -->
            <div x-show="activeTab === 'details'" x-transition class="space-y-8">
                <div class="bg-white p-10 rounded-[3rem] border border-gray-100 shadow-sm">
                    <h3 class="text-xl font-black text-gray-900 tracking-tight mb-6">Description du produit</h3>
                    <div class="prose prose-sm text-gray-500 font-medium leading-relaxed max-w-none">
                        {{ $plat->description ?: 'Aucune description fournie pour ce produit.' }}
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-10 pt-10 border-t border-gray-50">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-gray-400 uppercase text-[10px]">Catégorie</span>
                                <span class="font-black text-gray-900">{{ $plat->categorie->nom_categorie ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-gray-400 uppercase text-[10px]">Temps prép. moy.</span>
                                <span class="font-black text-gray-900">{{ $plat->temps_preparation_min ? $plat->temps_preparation_min . ' mins' : 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-gray-400 uppercase text-[10px]">Gestion Stock</span>
                                <span class="font-black {{ $plat->stock_limite ? 'text-orange-600' : 'text-green-600' }}">{{ $plat->stock_limite ? 'Limité' : 'Illimité' }}</span>
                            </div>
                            @if($plat->stock_limite)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-bold text-gray-400 uppercase text-[10px]">Restant</span>
                                    <span class="font-black text-gray-900">{{ $plat->quantite_disponible ?: 0 }} unités</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($plat->images_supplementaires && count($plat->images_supplementaires) > 0)
                    <div class="bg-white p-10 rounded-[3rem] border border-gray-100 shadow-sm">
                        <h3 class="text-xl font-black text-gray-900 tracking-tight mb-6">Galerie Photos</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($plat->images_supplementaires as $image)
                                <div class="aspect-square rounded-2xl overflow-hidden bg-gray-50 border border-gray-50">
                                    <img src="{{ asset('storage/' . $image) }}" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Options Tab -->
            <div x-show="activeTab === 'options'" x-transition class="space-y-6">
                @forelse($plat->groupesVariantes as $groupe)
                    <div class="bg-white p-8 rounded-[3rem] border border-gray-100 shadow-sm">
                        <div class="flex justify-between items-center mb-6 border-b border-gray-50 pb-4">
                            <div>
                                <h4 class="text-lg font-black text-gray-900">{{ $groupe->nom }}</h4>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">
                                    {{ $groupe->obligatoire ? 'Obligatoire' : 'Facultatif' }} • {{ $groupe->max_choix ? 'Max: ' . $groupe->max_choix : 'Illimité' }}
                                </p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($groupe->variantes as $variante)
                                <div class="p-4 bg-gray-50 rounded-2xl flex justify-between items-center border border-transparent hover:border-red-100 transition-colors">
                                    <span class="font-bold text-gray-700">{{ $variante->nom }}</span>
                                    <span class="px-3 py-1 bg-white rounded-lg text-xs font-black text-gray-900 shadow-sm">+ {{ number_format($variante->prix_supplement, 0, ',', ' ') }} {{ $currency }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="py-20 text-center bg-white rounded-[3rem] border border-gray-100 shadow-sm">
                        <div class="w-20 h-20 bg-gray-50 rounded-[1.5rem] flex items-center justify-center mx-auto mb-6 text-gray-200">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h4 class="text-lg font-black text-gray-900">Aucune variante</h4>
                        <p class="text-gray-400 text-sm mt-1">Ce produit n'a pas d'options de personnalisation.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
