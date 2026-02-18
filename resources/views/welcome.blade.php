@extends('layouts.app')

@section('title', 'Bienvenue chez CabaaCabaa')

@section('content')

<!-- Mobile Header Section (Optional if not in layout) -->
{{-- Assumed handled by layout --}}

<!-- Main Container -->
<div class="bg-gray-50 dark:bg-gray-900 min-h-screen pb-24">

    <!-- 1. Promotions / Pubs (Carousel) -->
    @if($promotions->count() > 0)
    <div class="pt-4 px-4 mb-6">
        <div class="flex overflow-x-auto snap-x snap-mandatory gap-4 pb-4 no-scrollbar" style="scrollbar-width: none; -ms-overflow-style: none;">
            @foreach($promotions as $promo)
            <div class="snap-center shrink-0 w-[85%] md:w-[45%] lg:w-[30%] relative rounded-2xl overflow-hidden shadow-lg h-48 md:h-64">
                <a href="{{ $promo->vendeur ? route('vendor.show', $promo->vendeur->slug) : '#' }}" class="block w-full h-full relative">
                    <img src="{{ optional($promo->vendeur)->cover_image ?? 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80' }}"
                         alt="{{ optional($promo->vendeur)->name }}"
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex flex-col justify-end p-4">
                        <span class="text-white font-bold text-lg mb-1">{{ optional($promo->vendeur)->name }}</span>
                        @if($promo->description)
                        <span class="text-gray-200 text-sm line-clamp-2">{{ $promo->description }}</span>
                        @endif
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <!-- Fallback Banner if no promos -->
    <div class="pt-4 px-4 mb-6">
        <div class="relative rounded-2xl overflow-hidden shadow-lg h-48 bg-gradient-to-r from-green-600 to-green-500 flex items-center justify-center p-6 text-center">
            <div>
                <h2 class="text-white text-2xl font-bold mb-2">Bienvenue sur CabaaCabaa</h2>
                <p class="text-white/90">Tout ce dont vous avez besoin, livré chez vous.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- 2. Catégories (Grid of Circles) -->
    <div class="px-4 mb-8">
        <div class="grid grid-cols-4 gap-y-6 gap-x-2">
            @foreach($vendorCategories as $cat)
            <a href="{{ route('explore', ['category' => $cat->id_category_vendeur]) }}" class="flex flex-col items-center group">
                <div class="w-14 h-14 rounded-full bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300 border border-gray-100 dark:border-gray-700">
                    @if($cat->icon && str_contains($cat->icon, 'http'))
                        <img src="{{ $cat->icon }}" alt="{{ $cat->name }}" class="w-8 h-8 object-contain">
                    @elseif($cat->icon)
                        <span class="text-2xl">{{ $cat->icon }}</span>
                    @else
                        <span class="text-2xl">📦</span>
                    @endif
                </div>
                <span class="text-xs text-center text-gray-700 dark:text-gray-300 font-medium truncate w-full px-1">{{ $cat->name }}</span>
            </a>
            @endforeach
            <!-- "More" Item -->
            <a href="{{ route('explore') }}" class="flex flex-col items-center group">
                <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-2">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </div>
                <span class="text-xs text-center text-gray-700 dark:text-gray-300 font-medium">Tout voir</span>
            </a>
        </div>
    </div>

    <!-- 3. A tester absolument ! (Popular Vendors) -->
    <div class="px-4 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">A tester absolument !</h2>
            <a href="{{ route('explore') }}" class="text-red-600 text-sm font-medium">Voir tout</a>
        </div>
        
        <div class="flex overflow-x-auto snap-x snap-mandatory gap-4 pb-4 no-scrollbar" style="scrollbar-width: none; -ms-overflow-style: none;">
            @foreach($vendeurs as $vendor)
            <a href="{{ route('vendor.show', $vendor->slug) }}" class="snap-start shrink-0 w-64 bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700 block">
                <div class="h-32 bg-gray-200 relative">
                    <img src="{{ $vendor->cover_image ?? 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400' }}" alt="{{ $vendor->name }}" class="w-full h-full object-cover">
                    @if($vendor->is_open)
                        <span class="absolute top-2 right-2 px-2 py-0.5 bg-green-500 text-white text-[10px] uppercase font-bold rounded-sm">Ouvert</span>
                    @else
                        <span class="absolute top-2 right-2 px-2 py-0.5 bg-gray-500 text-white text-[10px] uppercase font-bold rounded-sm">Fermé</span>
                    @endif
                </div>
                <div class="p-3">
                    <h3 class="font-bold text-gray-900 dark:text-white truncate">{{ $vendor->name }}</h3>
                    <div class="flex items-center gap-1 text-xs text-gray-500 mt-1">
                        <span class="text-yellow-400">★</span>
                        <span>{{ number_format($vendor->note_moyenne ?? 0, 1) }} ({{ $vendor->avis_evaluations_count }})</span>
                        <span>•</span>
                        <span>{{ $vendor->categories->first()->name ?? 'Divers' }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-dashed border-gray-200 dark:border-gray-700">
                         <span class="text-xs text-gray-400">Livraison : 1000 F</span>
                         <span class="text-xs font-semibold text-gray-900 dark:text-white">30 - 40 min</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <!-- 4. Plein de Promos (Plats en promotion) -->
    @if(isset($plats) && $plats->count() > 0)
    <div class="px-4 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Plein de Promos 🎉</h2>
            <a href="{{ route('explore.plats') }}" class="text-red-600 text-sm font-medium">Voir tout</a>
        </div>
        
        <div class="flex overflow-x-auto snap-x snap-mandatory gap-4 pb-4 no-scrollbar" style="scrollbar-width: none; -ms-overflow-style: none;">
            @foreach($plats as $plat)
            <div class="snap-start shrink-0 w-64 bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700 relative">
                @if($plat->en_promotion)
                <div class="absolute top-2 left-2 bg-green-600 text-white text-xs font-bold px-2 py-1 rounded-md z-10">
                    -{{ $plat->pourcentage_promo ?? '15' }}%
                </div>
                @endif
                <div class="h-32 bg-gray-200 relative">
                    <img src="{{ $plat->image ?? 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400' }}" alt="{{ $plat->nom_plat }}" class="w-full h-full object-cover">
                    <button class="absolute bottom-2 right-2 bg-white p-1.5 rounded-full shadow hover:bg-gray-50 text-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </button>
                </div>
                <div class="p-3">
                    <h3 class="font-bold text-gray-900 dark:text-white truncate mb-1">{{ $plat->nom_plat }}</h3>
                    <div class="flex items-center gap-1 text-xs text-gray-500 mb-2">
                        <span>{{ number_format($plat->vendeur->note_moyenne ?? 0, 1) }}</span>
                        <span class="text-yellow-400">★</span>
                        <span>•</span>
                        <span>{{ $plat->vendeur->nom_commercial ?? 'Vendeur' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                         <div>
                            @if($plat->en_promotion)
                                <span class="text-sm font-bold text-gray-900 dark:text-white block">{{ number_format($plat->prix * (1 - ($plat->pourcentage_promo/100)), 0, ',', ' ') }} F</span>
                                <span class="text-xs text-gray-400 line-through">{{ number_format($plat->prix, 0, ',', ' ') }} F</span>
                            @else
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($plat->prix, 0, ',', ' ') }} F</span>
                            @endif
                         </div>
                         <div class="text-xs font-semibold bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                            25 - 35 min
                         </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- 5. Maison Équipée (Optional - Logic to fetch relevant category) -->
    <!-- Placeholder or generic section -->
    <div class="px-4 mb-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Maison Équipée 🏠</h2>
        <div class="grid grid-cols-2 gap-4">
            <!-- Items example -->
           @if(isset($plats) && $plats->count() > 0)
                @foreach($plats->take(2) as $item)
                <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="h-24 bg-gray-100 relative">
                        <img src="{{ $item->image ?? 'https://images.unsplash.com/photo-1556910103-1c02745a30bf?w=400' }}" class="w-full h-full object-cover">
                         <button class="absolute top-2 right-2 bg-white/80 p-1.5 rounded-full shadow hover:bg-white text-gray-700">
                             <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="p-3">
                        <h4 class="font-bold text-sm text-gray-900 dark:text-white truncate">{{ $item->nom_plat }}</h4>
                        <div class="flex items-center gap-1 text-[10px] text-gray-500 mt-1">
                            <span class="text-yellow-400">★</span> 3.8 (12) • {{ number_format($item->prix, 0, ',', ' ') }} F
                        </div>
                    </div>
                </div>
                @endforeach
           @endif
        </div>
    </div>

    <!-- Bottom Search Bar (Sticky or just at bottom of content) -->
    <div class="px-4 pb-4">
        <div class="relative">
            <input type="text" placeholder="Trouver un produit, un vendeur..." class="w-full py-3 pl-10 pr-4 bg-gray-200 dark:bg-gray-800 rounded-full text-sm border-none focus:ring-2 focus:ring-green-500">
            <svg class="w-5 h-5 text-gray-500 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </div>

</div>

<style>
/* Hide scrollbar for Chrome, Safari and Opera */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
/* Hide scrollbar for IE, Edge and Firefox */
.no-scrollbar {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
</style>

@endsection