@extends('layouts.app')

@section('content')
<script src="{{ asset('js/product-options.js') }}"></script>
<div class="bg-gray-50 dark:bg-gray-900 min-h-screen py-8 transition-colors duration-300" 
     x-data="productOptionsManager()"
     x-init="
        @if(request('edit_cart'))
            @php
                $editingPlatId = (int) request('plat_id');
                $cartKey = request('edit_cart');
                $cartItem = session('cart')[$cartKey] ?? null;
                $p = App\Models\Plat::with(['groupesVariantes.variantes'])->find($editingPlatId);
                
                $initialSelections = [];
                if($p && $cartItem && isset($cartItem['options'])) {
                    foreach($cartItem['options'] as $g) {
                        $vg = $p->groupesVariantes->filter(function($gp) use ($g) { 
                            return $gp->nom == $g['groupe']; 
                        })->first();
                        
                        if($vg) {
                            if($vg->choix_multiple) {
                                $initialSelections[$vg->id_groupe] = array_column($g['variantes'], 'id');
                            } else {
                                $initialSelections[$vg->id_groupe] = $g['variantes'][0]['id'] ?? null;
                            }
                        }
                    }
                }
            @endphp
            @if($p)
                $nextTick(() => {
                    openModal({{ Js::from($p) }}, '{{ $cartKey }}', {{ Js::from($initialSelections) }});
                });
            @endif
        @endif
     ">
    <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14">
        
        <!-- Page Header (Mobile) -->
        <div class="lg:hidden mb-8">
            <h1 class="text-3xl font-display font-black text-gray-900 dark:text-white tracking-tight">Tous nos Produits</h1>
            <p class="text-xs font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-1">{{ $plats->total() }} produits disponibles</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Sidebar (Filters) -->
            <aside class="w-full lg:w-72 shrink-0">
                <!-- Mobile Filter Toggle -->
                <div class="lg:hidden mb-4">
                    <button @click="mobileFiltersOpen = !mobileFiltersOpen" 
                            class="w-full flex items-center justify-between px-6 py-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:border-red-500 transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-red-50 dark:bg-red-900/30 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400 group-hover:bg-red-600 group-hover:text-white transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            </div>
                            <span class="text-[11px] font-black text-gray-900 text-center dark:text-white uppercase tracking-[0.2em]">Filtres & Recherche</span>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transform transition-transform" :class="mobileFiltersOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>

                <div x-show="mobileFiltersOpen" x-transition.opacity 
                     class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[60] lg:hidden" 
                     @click="mobileFiltersOpen = false" x-cloak></div>

                <div :class="mobileFiltersOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" 
                     class="fixed lg:sticky top-0 lg:top-28 left-0 bottom-0 w-80 lg:w-full bg-white dark:bg-gray-800 lg:bg-transparent z-[70] lg:z-auto  lg:p-0 transition-transform duration-300 h-full lg:h-auto overflow-y-auto lg:overflow-visible border-r lg:border-r-0 border-gray-100 dark:border-gray-700">
                    
                    <div class="lg:hidden mb-8 flex justify-between items-center">
                        <span class="text-xl font-display font-black text-gray-900 dark:text-white">Filtres</span>
                        <button @click="mobileFiltersOpen = false" class="p-2 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <svg class="w-6 h-6 text-gray-400 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl lg:shadow-sm lg:border lg:border-gray-100 dark:lg:border-gray-700 p-8 lg:p-7 space-y-10">
                        <form action="{{ route('explore.plats') }}" method="GET" class="space-y-10">
                            
                            <!-- Search -->
                            <div>
                                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 dark:text-gray-500 mb-6 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-600 dark:bg-red-500"></span>
                                    Recherche
                                </h3>
                                <div class="relative group">
                                    <input 
                                        type="text" 
                                        name="search" 
                                        value="{{ request('search') }}" 
                                        placeholder="Que recherchez-vous ?" 
                                        class="w-full pl-5 pr-12 py-4 bg-gray-50 dark:bg-gray-900 border border-transparent dark:border-gray-700 rounded-xl text-[13px] font-bold text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:bg-white dark:focus:bg-gray-900 focus:ring-2 focus:ring-red-500 dark:focus:ring-red-500 transition-all outline-none"
                                    >
                                    <button type="submit" class="absolute right-2 top-2 bottom-2 w-11 bg-gray-900 dark:bg-gray-700 text-white rounded-xl flex items-center justify-center hover:bg-red-600 dark:hover:bg-red-600 transition-colors shadow-lg shadow-gray-200 dark:shadow-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Categories -->
                            <div>
                                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 dark:text-gray-500 mb-6 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                    Catégories
                                </h3>
                                <div class="space-y-2 max-h-[400px] overflow-y-auto pr-2 custom-filter-scrollbar">
                                    <a href="{{ route('explore.plats', request()->except('category')) }}" 
                                       class="flex items-center justify-between px-5 py-3.5 rounded-xl text-[13px] font-black transition-all {{ !request('category') ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-xl shadow-gray-200 dark:shadow-gray-900/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                                        <span>Toutes les catégories</span>
                                        @if(!request('category'))
                                            <div class="w-1.5 h-1.5 rounded-full bg-red-500"></div>
                                        @endif
                                    </a>
                                    @foreach($categories as $cat)
                                        <a href="{{ route('explore.plats', array_merge(request()->query(), ['category' => $cat->id_categorie])) }}" 
                                           class="flex items-center justify-between px-5 py-3.5 rounded-xl text-[13px] font-black transition-all {{ request('category') == $cat->id_categorie ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-xl shadow-gray-200 dark:shadow-gray-900/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                                            <span>{{ $cat->nom_categorie }}</span>
                                            @if(request('category') == $cat->id_categorie)
                                                <div class="w-1.5 h-1.5 rounded-full bg-red-500"></div>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Budget -->
                            <div>
                                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 dark:text-gray-500 mb-6 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Gamme de prix
                                </h3>
                                <div class="grid grid-cols-2 gap-4 items-center">
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-300 dark:text-gray-600">MIN</span>
                                        <input 
                                            type="number" 
                                            name="min_price" 
                                            value="{{ request('min_price') }}" 
                                            class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-900 border border-transparent dark:border-gray-700 rounded-xl text-[11px] font-black text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-900 focus:ring-2 focus:ring-red-500 transition-all outline-none"
                                        >
                                    </div>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-300 dark:text-gray-600">MAX</span>
                                        <input 
                                            type="number" 
                                            name="max_price" 
                                            value="{{ request('max_price') }}" 
                                            class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-900 border border-transparent dark:border-gray-700 rounded-xl text-[11px] font-black text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-900 focus:ring-2 focus:ring-red-500 transition-all outline-none"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="flex flex-col gap-3 pt-6">
                                <button type="submit" class="w-full py-5 bg-red-600 text-white rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-red-700 shadow-xl shadow-red-100 dark:shadow-none transition-all">
                                    Appliquer les filtres
                                </button>
                                @if(request()->anyFilled(['search', 'category', 'min_price', 'max_price']))
                                    <a href="{{ route('explore.plats') }}" class="w-full py-5 text-center bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                                        Réinitialiser
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1">
                <!-- Title & Count (Desktop) -->
                <div class="hidden lg:flex items-end justify-between pb-6 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h1 class="text-4xl font-display font-black text-gray-900 dark:text-white tracking-tight">Tous nos Produits</h1>
                        <div class="flex items-center gap-3 mt-2">
                            <span class="px-3 py-1 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-[10px] font-black uppercase tracking-[0.2em] rounded-full">
                                {{ $plats->total() }} produits trouvés
                            </span>
                            @if(request('search'))
                                <span class="text-xs font-bold text-gray-400 dark:text-gray-500 italic">pour "{{ request('search') }}"</span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Sorting Dropdown -->
                    <div class="relative inline-block text-left" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-4 px-6 py-3 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-[11px] font-black uppercase tracking-widest hover:border-red-200 dark:hover:border-red-800 transition-all shadow-sm">
                            <span class="text-gray-400 dark:text-gray-500">Trier par:</span>
                            <span class="text-gray-900 dark:text-white">Populaires</span>
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                </div>

                @if($plats->count())
                    <!-- Products Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8 mt-8">
                        @foreach($plats as $plat)
                            <div class="group bg-white dark:bg-gray-800 rounded-2xl p-5 border border-transparent dark:border-gray-700/50 hover:border-red-50 dark:hover:border-red-900/30 transition-all duration-500 hover:shadow-2xl hover:shadow-red-200/20 dark:hover:shadow-black/40 flex flex-col h-full">
                                
                                <!-- Image & Quick Actions -->
                                <div class="relative aspect-[4/5] rounded-xl overflow-hidden mb-6 bg-gray-50 dark:bg-gray-700">
                                    @if($plat->image_principale)
                                        <img src="{{ asset('storage/' . $plat->image_principale) }}" 
                                             class="w-full h-full object-cover transform scale-100 group-hover:scale-110 transition-transform duration-700" 
                                             alt="{{ $plat->nom_plat }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-100 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif

                                    <!-- Floating Badges -->
                                    <div class="absolute top-5 left-5 right-5 flex justify-between items-start pointer-events-none">
                                        <div class="flex flex-col gap-2">
                                            @if($plat->en_promotion)
                                                <div class="px-4 py-2 bg-red-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-full shadow-xl">
                                                    Offre Spéciale
                                                </div>
                                            @endif
                                            <div class="px-4 py-2 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md text-gray-900 dark:text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-full shadow-xl">
                                                {{ $plat->categorie->nom_categorie ?? 'Produit' }}
                                            </div>
                                            @if(!$plat->is_available)
                                                <div class="px-4 py-2 bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-full shadow-xl">
                                                    ÉPUISÉ
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <button 
                                            @auth
                                                @php $isFav = auth()->user()->favoris()->where('id_vendeur', $plat->vendeur->id_vendeur)->exists(); @endphp
                                                @click="toggleFavorite({{ $plat->vendeur->id_vendeur }}, $event)"
                                                class="w-10 h-10 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md rounded-xl flex items-center justify-center {{ $isFav ? 'text-red-600 fill-current' : 'text-gray-400 dark:text-gray-500' }} hover:text-red-600 transition-colors shadow-lg pointer-events-auto"
                                            @else
                                                @click="window.location.href='{{ route('login') }}'"
                                                class="w-10 h-10 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md rounded-xl flex items-center justify-center text-gray-400 dark:text-gray-500 hover:text-red-600 transition-colors shadow-lg pointer-events-auto"
                                            @endauth
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                        </button>
                                    </div>

                                    <!-- Hover Action: Add to Cart -->
                                    <div class="absolute inset-x-0 bottom-0 p-6 translate-y-0 lg:translate-y-full lg:group-hover:translate-y-0 transition-transform duration-500">
                                        {{-- Debug: {{ $plat->groupesVariantes->count() }} variants --}}
                                        @if($plat->groupesVariantes->isNotEmpty())
                                            <button @click="openModal({{ Js::from([
                                                'id_plat' => $plat->id_plat,
                                                'nom_plat' => $plat->nom_plat,
                                                'description' => $plat->description,
                                                'prix' => $plat->prix,
                                                'prix_promotion' => $plat->prix_promotion,
                                                'en_promotion' => $plat->en_promotion,
                                                'groupes_variantes' => $plat->groupesVariantes->map(fn($g) => [
                                                    'id_groupe' => $g->id_groupe,
                                                    'nom' => $g->nom,
                                                    'obligatoire' => $g->obligatoire,
                                                    'choix_multiple' => $g->choix_multiple,
                                                    'min_choix' => $g->min_choix,
                                                    'max_choix' => $g->max_choix,
                                                    'variantes' => $g->variantes->map(fn($v) => [
                                                        'id_variante' => $v->id_variante,
                                                        'nom' => $v->nom,
                                                        'prix_supplement' => $v->prix_supplement
                                                    ])
                                                ])
                                            ]) }})"
                                                    {{ !$plat->is_available ? 'disabled' : '' }}
                                                    class="w-full py-4 {{ $plat->is_available ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900' : 'bg-gray-300 text-white cursor-not-allowed' }} rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-black dark:hover:bg-gray-100 shadow-2xl transition-all">
                                                {{ $plat->is_available ? 'Choisir les options' : 'Stock Épuisé' }}
                                            </button>
                                        @else
                                            <button @click="addCart({{ $plat->id_plat }})"
                                                    {{ !$plat->is_available ? 'disabled' : '' }}
                                                    class="w-full py-4 {{ $plat->is_available ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900' : 'bg-gray-300 text-white cursor-not-allowed' }} rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-black dark:hover:bg-gray-100 shadow-2xl transition-all">
                                                {{ $plat->is_available ? 'Ajouter au panier' : 'Stock Épuisé' }}
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Details -->
                                <div class="px-2 flex-1 flex flex-col">
                                    <div class="flex justify-between items-start gap-4 mb-3">
                                        <div>
                                            <h3 class="text-xl font-black text-gray-900 dark:text-white leading-tight group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">{{ $plat->nom_plat }}</h3>
                                            <div class="flex items-center gap-2 mt-1.5">
                                                <div class="flex items-center">
                                                    @for($i = 0; $i < 5; $i++)
                                                        <svg class="w-3 h-3 {{ $i < round($plat->vendeur->note_moyenne) ? 'text-orange-400' : 'text-gray-200 dark:text-gray-700' }} fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                    @endfor
                                                </div>
                                                <span class="text-[10px] font-black text-gray-900 dark:text-white">{{ number_format($plat->vendeur->note_moyenne, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end shrink-0">
                                            @if($plat->en_promotion)
                                                <span class="text-[10px] font-black text-gray-300 dark:text-gray-600 line-through tracking-widest">{{ number_format($plat->prix, 0) }}</span>
                                                <span class="text-xl font-black text-red-600 dark:text-red-400 tracking-tighter">{{ number_format($plat->prix_promotion, 0) }} <span class="text-[10px] uppercase font-black">FCFA</span></span>
                                            @else
                                                <span class="text-xl font-black text-gray-900 dark:text-white tracking-tighter">{{ number_format($plat->prix, 0) }} <span class="text-[10px] uppercase font-black text-gray-400 dark:text-gray-500">FCFA</span></span>
                                            @endif
                                        </div>
                                    </div>

                                    <p class="text-sm font-bold text-gray-400 dark:text-gray-500 line-clamp-2 mb-8 leading-relaxed">
                                        {{ $plat->description ?: 'Qualité garantie pour ce produit sélectionné avec passion.' }}
                                    </p>

                                    <!-- Footer Card -->
                                    <div class="mt-auto pt-6 border-t border-gray-50 dark:border-gray-700/50 flex items-center justify-between">
                                        <a href="{{ route('vendor.show', ['id' => $plat->vendeur->id_vendeur, 'slug' => \Str::slug($plat->vendeur->nom_commercial)]) }}" 
                                           class="flex items-center gap-3 group/v">
                                            <div class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-700 flex items-center justify-center border border-gray-100 dark:border-gray-600 group-hover/v:border-red-100 dark:group-hover/v:border-red-900 group-hover/v:bg-red-50 dark:group-hover/v:bg-red-900/20 transition-all overflow-hidden">
                                                @if($plat->vendeur->logo)
                                                    <img src="{{ asset('storage/' . $plat->vendeur->logo) }}" class="w-full h-full object-cover">
                                                @else
                                                    <span class="text-xs font-black text-gray-400 dark:text-gray-500 group-hover/v:text-red-600 dark:group-hover/v:text-red-400">{{ substr($plat->vendeur->nom_commercial, 0, 1) }}</span>
                                                @endif
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest group-hover/v:text-red-600 dark:group-hover/v:text-red-400 transition-colors">{{ Str::limit($plat->vendeur->nom_commercial, 15) }}</span>
                                                <span class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tight">Vendeur vérifié</span>
                                            </div>
                                        </a>

                                        <div class="flex gap-1 items-center">
                                            <div class="w-1.5 h-1.5 rounded-full {{ $plat->is_available ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></div>
                                            <span class="text-[9px] font-black {{ $plat->is_available ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} uppercase tracking-widest">
                                                {{ $plat->is_available ? 'Disponible' : 'Vendu' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-20">
                        {{ $plats->appends(request()->query())->links('vendor.pagination.premium') }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl py-12 px-6 sm:p-24 text-center border border-gray-100 dark:border-gray-700 shadow-sm transition-all duration-300">
                        <div class="w-20 h-20 sm:w-28 sm:h-28 bg-gray-50 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-8 sm:mb-10 border border-gray-100 dark:border-gray-600">
                            <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-200 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-display font-black text-gray-900 dark:text-white mb-4 tracking-tight">Oups ! Rien trouvé</h2>
                        <p class="text-gray-400 dark:text-gray-500 font-bold max-w-sm mx-auto mb-12 uppercase text-[11px] tracking-[0.2em] leading-loose">Nous n'avons trouvé aucun produit correspondant à vos critères. Essayez peut-être une autre catégorie ou un mot-clé différent.</p>
                        <a href="{{ route('explore.plats') }}" class="inline-flex px-12 py-5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[10px] font-black uppercase tracking-[0.3em] rounded-xl hover:bg-black dark:hover:bg-gray-100 transition-all shadow-2xl shadow-gray-200 dark:shadow-none">
                            Réinitialiser la recherche
                        </a>
                    </div>
                @endif
            </main>
        </div>
    </div>

    <!-- Product Options Modal -->
    <div x-show="modalOpen" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-gray-900/75 dark:bg-black/80 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-[2.5rem] text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100 dark:border-gray-700">
                
                <template x-if="selectedPlat">
                    <div class="flex flex-col max-h-[85vh]">
                        <!-- Modal Header -->
                        <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 z-10 flex justify-between items-center sticky top-0">
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white" x-text="selectedPlat.nom_plat"></h3>
                                <p class="text-sm font-bold text-red-600 dark:text-red-400 mt-1">
                                    <span x-text="formatPrice(calculateTotal())"></span> FCFA
                                </p>
                            </div>
                            <button @click="closeModal()" class="p-2 bg-gray-50 dark:bg-gray-700 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Scrollable Content -->
                        <div class="flex-1 overflow-y-auto p-8 space-y-8">
                            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed font-medium" x-text="selectedPlat.description"></p>

                            <!-- Variant Groups -->
                            <template x-for="group in selectedPlat.groupes_variantes" :key="group.id_groupe">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest" x-text="group.nom"></h4>
                                        <div class="flex gap-2">
                                            <span x-show="group.obligatoire" class="px-2 py-0.5 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-[9px] font-black uppercase rounded">Obligatoire</span>
                                            <span x-show="group.choix_multiple" class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-[9px] font-black uppercase rounded">
                                                Max <span x-text="group.max_choix"></span>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <template x-for="option in group.variantes" :key="option.id_variante">
                                            <div class="flex flex-col gap-2 p-4 rounded-xl border-2 transition-all"
                                                 :class="isSelected(group, option) ? 'border-red-600 bg-red-50/50 dark:bg-red-900/10' : 'border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800'">
                                                 
                                                 <div class="flex items-center justify-between">
                                                     <label class="flex items-center gap-3 cursor-pointer flex-1">
                                                         <!-- Checkbox for multiple -->
                                                         <input x-show="group.choix_multiple" 
                                                                type="checkbox" 
                                                                :name="'group_' + group.id_groupe" 
                                                                :value="option.id_variante"
                                                                @change="toggleOption(group, option)"
                                                                :checked="isSelected(group, option)"
                                                                class="w-5 h-5 text-red-600 rounded focus:ring-red-500 bg-gray-100 border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                                                         
                                                         <!-- Radio for single -->
                                                         <input x-show="!group.choix_multiple" 
                                                                type="radio" 
                                                                :name="'group_' + group.id_groupe" 
                                                                :value="option.id_variante"
                                                                @change="selectSingleOption(group, option)"
                                                                :checked="isSelected(group, option)"
                                                                class="w-5 h-5 text-red-600 focus:ring-red-500 bg-gray-100 border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                                                         
                                                         <span class="text-sm font-bold text-gray-900 dark:text-white" x-text="option.nom"></span>
                                                     </label>
                                                     
                                                     <span x-show="option.prix_supplement > 0" class="text-xs font-black text-gray-500 dark:text-gray-400">
                                                         +<span x-text="formatPrice(option.prix_supplement)"></span>
                                                     </span>
                                                 </div>

                                                 <!-- Quantity Controls for this option -->
                                                 <template x-if="isSelected(group, option)">
                                                     <div class="flex items-center justify-between mt-2 pt-2 border-t border-red-100 dark:border-red-900/30">
                                                         <p class="text-[9px] font-black text-red-600 dark:text-red-400 uppercase tracking-widest">Quantité pour cette option</p>
                                                         <div class="flex items-center gap-3 bg-white dark:bg-gray-800 px-3 py-1.5 rounded-xl border border-red-100 dark:border-red-900/30">
                                                             <button @click="decrementOption(group, option)" class="text-gray-400 hover:text-red-600 transition-colors">
                                                                 <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                                             </button>
                                                             <span class="text-xs font-black text-gray-900 dark:text-white w-4 text-center" x-text="getOptionQuantity(group, option)"></span>
                                                             <button @click="incrementOption(group, option)" class="text-gray-400 hover:text-red-600 transition-colors">
                                                                 <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                             </button>
                                                         </div>
                                                     </div>
                                                 </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Sticky Footer -->
                        <div class="p-6 bg-white dark:bg-gray-800 border-t border-gray-50 dark:border-gray-700 sticky bottom-0 z-10 w-full">
                            <button @click="confirmAddToCart()" 
                                    :disabled="!isValidSelection()"
                                    class="w-full py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-black dark:hover:bg-gray-200 shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                Ajouter à la commande
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-filter-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-filter-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-filter-scrollbar::-webkit-scrollbar-thumb { background: #f1f5f9; border-radius: 10px; }
    .custom-filter-scrollbar::-webkit-scrollbar-thumb:hover { background: #e2e8f0; }
    
    .dark .custom-filter-scrollbar::-webkit-scrollbar-thumb { background: #374151; }
    .dark .custom-filter-scrollbar::-webkit-scrollbar-thumb:hover { background: #4b5563; }
</style>
@endsection
