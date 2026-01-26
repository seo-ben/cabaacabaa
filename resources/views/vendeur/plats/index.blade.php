@extends('layouts.vendor')

@section('title', 'Mes Articles')
@section('page_title', 'Mes Articles')

@section('content')
<div class="space-y-10">
    <!-- Header avec stats simples -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Mes Articles</h1>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Total de {{ $plats->count() }} articles actifs</p>
        </div>
        <a href="{{ vendor_route('vendeur.slug.plats.create') }}" class="px-8 py-4 bg-red-600 text-white rounded-[1.5rem] font-black shadow-xl shadow-red-600/20 hover:bg-red-700 transition flex items-center gap-3 active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            AJOUTER UN ARTICLE
        </a>
    </div>

    @if($plats->isEmpty())
        <div class="bg-white rounded-[3rem] p-24 text-center border-2 border-dashed border-gray-100 italic">
            <div class="w-24 h-24 bg-gray-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8 text-gray-200">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <h3 class="text-2xl font-black text-gray-900 mb-2">Votre boutique est vide</h3>
            <p class="text-gray-400 font-medium">Commencez par ajouter votre premier article pour être visible par les clients.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @foreach($plats as $plat)
                <div class="group bg-white rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-gray-200/50 transition-all duration-500 overflow-hidden">
                    <!-- Image Wrapper -->
                    <div class="relative aspect-video overflow-hidden">
                        <img src="{{ $plat->image_principale ? asset('storage/' . $plat->image_principale) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&q=80' }}" 
                             alt="{{ $plat->nom_plat }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        
                        <!-- Badges Overlay -->
                        <div class="absolute top-4 left-4 flex flex-wrap gap-2">
                            <span class="px-4 py-1.5 bg-white/95 backdrop-blur-md rounded-xl text-[9px] font-black uppercase text-red-600 shadow-xl shadow-black/5">
                                {{ $plat->categorie->nom_categorie ?? 'Sans catégorie' }}
                            </span>
                            @if($plat->en_promotion)
                                <span class="px-4 py-1.5 bg-red-600 text-white rounded-xl text-[9px] font-black uppercase shadow-xl shadow-red-600/20">
                                    PROMO
                                </span>
                            @endif
                        </div>

                        <!-- Availability Toggle -->
                        <div class="absolute top-4 right-4 group/toggle">
                            <form action="{{ vendor_route('vendeur.slug.plats.toggle-availability', $plat->id_plat) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="p-2 {{ $plat->is_available ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-400' }} rounded-xl shadow-xl flex items-center gap-2 border-2 border-white transition-all hover:scale-105"
                                        title="{{ $plat->is_available ? 'Marquer comme épuisé' : 'Réactiver l\'article' }}">
                                    <div class="w-2 h-2 rounded-full bg-white {{ $plat->is_available ? 'animate-pulse' : '' }}"></div>
                                    <span class="text-[9px] font-black uppercase tracking-widest">{{ $plat->is_available ? 'En Stock' : 'Épuisé' }}</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-8">
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <h3 class="text-xl font-black text-gray-900 leading-tight group-hover:text-red-600 transition-colors">{{ $plat->nom_plat }}</h3>
                            <div class="text-right">
                                <p class="text-xl font-black text-gray-900">{{ number_format($plat->prix, 0) }}</p>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-tighter">FCFA</p>
                            </div>
                        </div>

                        <p class="text-gray-500 text-sm font-medium line-clamp-2 leading-relaxed mb-8">
                            {{ $plat->description ?: 'Qualité garantie pour cet article sélectionné avec passion.' }}
                        </p>
                        
                        <!-- Actions -->
                        <div class="flex items-center gap-3 pt-8 border-t border-gray-50">
                            <a href="{{ vendor_route('vendeur.slug.plats.edit', $plat->id_plat) }}" class="flex-1 py-3.5 bg-gray-50 text-gray-900 rounded-2xl text-[11px] font-black underline decoration-red-600/30 underline-offset-4 uppercase tracking-widest hover:bg-gray-900 hover:text-white transition-all text-center">
                                Modifier
                            </a>
                            <form action="{{ vendor_route('vendeur.slug.plats.destroy', $plat->id_plat) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce plat définitivement ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-12 h-12 flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-50 rounded-2xl transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
