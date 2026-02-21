@extends('layouts.vendor')

@section('title', 'Catalogue')
@section('page_title', 'Mon Catalogue')

@section('content')
<div class="space-y-6 sm:space-y-10">
    
    {{-- ── Header & Action ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">Catalogue Articles</h1>
            <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-1">
                {{ $plats->count() }} article{{ $plats->count() > 1 ? 's' : '' }} au total
            </p>
        </div>
        <a href="{{ vendor_route('vendeur.slug.plats.create') }}" 
           class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-red-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-red-600/20 hover:bg-red-700 transition-all active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
            Ajouter un produit
        </a>
    </div>

    @if($plats->isEmpty())
        {{-- ── Empty State ── --}}
        <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] p-12 sm:p-24 text-center border-2 border-dashed border-gray-100 dark:border-gray-800">
            <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-3xl flex items-center justify-center mx-auto mb-6 text-gray-200 dark:text-gray-700">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Votre catalogue est vide</h3>
            <p class="text-gray-400 dark:text-gray-500 text-sm max-w-xs mx-auto">Commencez à ajouter vos produits pour les rendre visibles à vos clients.</p>
            <a href="{{ vendor_route('vendeur.slug.plats.create') }}" class="inline-block mt-6 text-red-600 font-black text-[10px] uppercase tracking-widest hover:underline">Créer mon premier article &rarr;</a>
        </div>
    @else
        {{-- ── List of Products ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @foreach($plats as $plat)
                <div class="group bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col">
                    
                    {{-- Media --}}
                    <div class="relative aspect-[4/3] sm:aspect-video overflow-hidden bg-gray-100 dark:bg-gray-800">
                        <img src="{{ $plat->image_principale ? asset('storage/' . $plat->image_principale) : 'https://images.unsplash.com/photo-1546241072-48010ad28c2c?w=600&q=80' }}" 
                             alt="{{ $plat->nom_plat }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        
                        {{-- Overlay Badges --}}
                        <div class="absolute top-3 left-3 flex flex-col gap-2">
                            <span class="px-3 py-1 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md rounded-lg text-[8px] font-black uppercase text-gray-900 dark:text-white shadow-sm border border-black/5">
                                {{ $plat->categorie->nom_categorie ?? 'DIVERS' }}
                            </span>
                            @if($plat->en_promotion)
                                <span class="px-3 py-1 bg-red-600 text-white rounded-lg text-[8px] font-black uppercase shadow-lg shadow-red-600/30">
                                    PROMO
                                </span>
                            @endif
                        </div>

                        {{-- Quick Availability Toggle --}}
                        <div class="absolute top-3 right-3">
                            <form action="{{ vendor_route('vendeur.slug.plats.toggle-availability', $plat->id_plat) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border-2 border-white dark:border-gray-900 shadow-xl transition-all active:scale-95
                                        {{ $plat->disponible ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-400' }}">
                                    <div class="w-1.5 h-1.5 rounded-full bg-white {{ $plat->disponible ? 'animate-pulse' : '' }}"></div>
                                    <span class="text-[8px] font-black uppercase tracking-tighter">{{ $plat->disponible ? 'En Stock' : 'Épuisé' }}</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <h3 class="text-base font-black text-gray-900 dark:text-white leading-tight line-clamp-2">{{ $plat->nom_plat }}</h3>
                            <div class="text-right shrink-0">
                                <p class="text-base font-black text-red-600 dark:text-red-500">{{ number_format($plat->prix, 0) }}</p>
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-tighter">FCFA</p>
                                @if($plat->prix_promotion)
                                    <p class="text-[9px] font-bold text-gray-300 line-through">{{ number_format($plat->prix_promotion, 0) }}</p>
                                @endif
                            </div>
                        </div>

                        <p class="text-gray-500 dark:text-gray-400 text-[11px] font-medium line-clamp-2 leading-relaxed mb-6">
                            {{ $plat->description ?: 'Aucune description disponible pour cet article.' }}
                        </p>
                        
                        {{-- Actions --}}
                        <div class="mt-auto pt-4 border-t border-gray-50 dark:border-gray-800 flex items-center gap-2">
                            <a href="{{ vendor_route('vendeur.slug.plats.edit', $plat->id_plat) }}" 
                               class="flex-1 py-3 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-900 dark:hover:bg-red-600 hover:text-white transition-all text-center">
                                Modifier
                            </a>
                            <form action="{{ vendor_route('vendeur.slug.plats.destroy', $plat->id_plat) }}" method="POST" 
                                  onsubmit="return confirm('Confirmer la suppression de cet article ?')" class="shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-10 h-10 flex items-center justify-center text-gray-300 dark:text-gray-600 hover:text-red-600 dark:hover:text-red-500 bg-gray-50 dark:bg-gray-800 rounded-xl transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
