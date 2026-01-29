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
        
        <!-- Alibaba-style Header -->
        <div class="mb-10">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
                <div class="flex-1">
                    <nav class="flex mb-3 md:mb-4 text-[9px] md:text-[10px] font-black uppercase tracking-widest text-gray-400">
                        <a href="/" class="hover:text-red-600 transition-colors">Accueil</a>
                        <span class="mx-1 md:mx-2">/</span>
                        <span class="text-gray-900 dark:text-gray-200">Produits</span>
                    </nav>
                    <h1 class="hidden md:block text-3xl lg:text-4xl font-display font-black text-gray-900 dark:text-white tracking-tight">Marché Global des Produits</h1>
                    <p class="hidden md:block text-sm font-bold text-gray-400 dark:text-gray-500 mt-2">Découvrez plus de {{ $plats->total() }} articles sélectionnés par nos experts.</p>
                </div>

                <!-- Functional Sort Actions -->
                <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar -mx-2 px-2 md:mx-0 md:px-0">
                    <div class="flex items-center bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border border-gray-100 dark:border-gray-700 rounded-[1.2rem] p-1 shadow-sm shrink-0">
                        @php $currentSort = request('sort', 'recommended'); @endphp
                        
                        <a href="{{ route('explore.plats', array_merge(request()->query(), ['sort' => 'recommended'])) }}" 
                           class="px-5 py-2.5 rounded-[0.9rem] text-[9px] font-black uppercase tracking-[0.15em] transition-all duration-300 {{ $currentSort === 'recommended' ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-lg shadow-gray-200 dark:shadow-none' : 'text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                            Recommandés
                        </a>
                        
                        <a href="{{ route('explore.plats', array_merge(request()->query(), ['sort' => 'newest'])) }}" 
                           class="px-5 py-2.5 rounded-[0.9rem] text-[9px] font-black uppercase tracking-[0.15em] transition-all duration-300 {{ $currentSort === 'newest' ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-lg shadow-gray-200 dark:shadow-none' : 'text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                            Nouveautés
                        </a>
                        
                        <a href="{{ route('explore.plats', array_merge(request()->query(), ['sort' => 'top_sales'])) }}" 
                           class="px-5 py-2.5 rounded-[0.9rem] text-[9px] font-black uppercase tracking-[0.15em] transition-all duration-300 {{ $currentSort === 'top_sales' ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-lg shadow-gray-200 dark:shadow-none' : 'text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                            Top Ventes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-10">
            
            <!-- Professional Sidebar -->
            <aside class="w-full lg:w-72 shrink-0">
                <!-- Mobile Toggle -->
                <div class="lg:hidden mb-6">
                    <button @click="mobileFiltersOpen = !mobileFiltersOpen" 
                            class="w-full flex items-center justify-between px-6 py-5 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            <span class="text-sm font-black uppercase tracking-widest">Filtres Avancés</span>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" :class="mobileFiltersOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>

                <div x-show="mobileFiltersOpen" x-transition.opacity 
                     class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[60] lg:hidden" 
                     @click="mobileFiltersOpen = false" x-cloak></div>

                <div :class="mobileFiltersOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" 
                     class="fixed lg:sticky top-0 lg:top-28 left-0 bottom-0 w-80 lg:w-full bg-white dark:bg-gray-800 lg:bg-transparent z-[70] lg:z-auto transition-transform duration-500 h-full lg:h-auto overflow-y-auto lg:overflow-visible p-8 lg:p-0 border-r lg:border-r-0 border-gray-100 dark:border-gray-700">
                    
                    <div class="lg:hidden mb-10 flex justify-between items-center">
                        <span class="text-2xl font-display font-black">Filtres</span>
                        <button @click="mobileFiltersOpen = false" class="p-2.5 bg-gray-50 dark:bg-gray-700 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="space-y-10">
                        <form action="{{ route('explore.plats') }}" method="GET" class="space-y-10 border-b border-gray-100 dark:border-gray-800 pb-10">
                            <!-- Search -->
                            <div class="space-y-4">
                                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Mot-clé</label>
                                <div class="relative">
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ex: Pizza, Sushi..." 
                                           class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-900 border border-transparent dark:border-gray-700 rounded-2xl text-[13px] font-bold focus:ring-2 focus:ring-red-600 transition-all outline-none">
                                    <button type="submit" class="absolute right-2 top-2 bottom-2 w-10 bg-gray-900 dark:bg-gray-700 text-white rounded-xl flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Budget -->
                            <div class="space-y-4">
                                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Budget (FCFA)</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" 
                                           class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-red-600 outline-none">
                                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" 
                                           class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-red-600 outline-none">
                                </div>
                            </div>

                            <button type="submit" class="w-full py-4 bg-red-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-red-500/20 hover:bg-red-700 transition-all">Cibler la recherche</button>
                        </form>

                        <!-- Categories List (Vertical) -->
                        <div class="space-y-4">
                            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-900 dark:text-white">Sélection par Secteur</h3>
                            <div class="space-y-1">
                                <a href="{{ route('explore.plats', request()->except('category')) }}" 
                                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all {{ !request('category') ? 'bg-red-50 dark:bg-red-900/20 text-red-600' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ !request('category') ? 'bg-red-600' : 'bg-transparent' }}"></span>
                                    Tous les articles
                                </a>
                                @foreach($categories as $cat)
                                    <a href="{{ route('explore.plats', array_merge(request()->query(), ['category' => $cat->id_categorie])) }}" 
                                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all {{ request('category') == $cat->id_categorie ? 'bg-red-50 dark:bg-red-900/20 text-red-600' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ request('category') == $cat->id_categorie ? 'bg-red-600' : 'bg-transparent' }}"></span>
                                        {{ $cat->nom_categorie }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Alibaba Main Grid -->
            <main class="flex-1">
                @if($plats->count())
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 lg:gap-6">
                        @foreach($plats as $plat)
                            <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 hover:border-red-500 dark:hover:border-red-500 transition-all duration-300 flex flex-col overflow-hidden hover:shadow-2xl hover:shadow-black/5">
                                
                                <!-- Product Image Container -->
                                <div class="relative aspect-square overflow-hidden bg-gray-50 dark:bg-gray-700">
                                    @if($plat->image_principale)
                                        <img src="{{ asset('storage/' . $plat->image_principale) }}" 
                                             class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500" 
                                             alt="{{ $plat->nom_plat }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-200">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif

                                    <!-- Quick Badges -->
                                    <div class="absolute top-3 left-3 flex flex-wrap gap-2">
                                        @if($plat->en_promotion)
                                            <span class="px-2.5 py-1 bg-red-600 text-white text-[9px] font-black uppercase tracking-wider rounded-lg shadow-lg shadow-red-500/30">Promo</span>
                                        @endif
                                        @if($plat->vendeur->is_boosted)
                                            <span class="px-2.5 py-1 bg-orange-500 text-white text-[9px] font-black uppercase tracking-wider rounded-lg shadow-lg shadow-orange-500/30 text-center">Top Qualité</span>
                                        @endif
                                    </div>

                                    <!-- Favorite Button Overlay -->
                                    <button @auth @click="toggleFavorite({{ $plat->vendeur->id_vendeur }}, $event)" @else @click="window.location.href='{{ route('login') }}'" @endauth
                                            class="absolute top-3 right-3 w-9 h-9 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md rounded-xl flex items-center justify-center text-gray-400 hover:text-red-600 transition-all opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 duration-300">
                                        <svg class="w-5 h-5 {{ auth()->check() && auth()->user()->favoris()->where('id_vendeur', $plat->vendeur->id_vendeur)->exists() ? 'text-red-500 fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    </button>
                                </div>

                                <!-- Product Info Area -->
                                <div class="p-3 sm:p-4 flex-1 flex flex-col space-y-3">
                                    <h3 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white line-clamp-2 leading-snug group-hover:text-red-600 transition-colors h-8 sm:h-10">
                                        {{ $plat->nom_plat }}
                                    </h3>

                                    <!-- Pricing Block -->
                                    <div class="flex flex-col">
                                        <div class="flex items-baseline gap-1.5 sm:gap-2">
                                            @if($plat->en_promotion)
                                                <span class="text-base sm:text-lg font-black text-red-600">{{ number_format($plat->prix_promotion, 0) }} FCFA</span>
                                                <span class="text-[9px] sm:text-[10px] font-bold text-gray-300 line-through">{{ number_format($plat->prix, 0) }}</span>
                                            @else
                                                <span class="text-base sm:text-lg font-black text-gray-900 dark:text-white">{{ number_format($plat->prix, 0) }} FCFA</span>
                                            @endif
                                        </div>
                                        <span class="text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Par Unité</span>
                                    </div>

                                    <!-- Status / Social Proof -->
                                    <div class="flex items-center gap-2 pt-1 border-t border-gray-50 dark:border-gray-700/50">
                                        <div class="flex items-center gap-1 text-orange-400">
                                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            <span class="text-[10px] sm:text-[11px] font-black text-gray-900 dark:text-white">{{ number_format($plat->vendeur->note_moyenne, 1) }}</span>
                                        </div>
                                        <span class="text-[9px] sm:text-[10px] font-bold text-gray-400 flex-1 truncate">{{ $plat->nombre_commandes ?? rand(10, 100) }} vendus</span>
                                    </div>

                                    <!-- Vendor Footer -->
                                    <div class="mt-auto pt-3 border-t border-gray-50 dark:border-gray-700/50 flex items-center justify-between gap-2">
                                        <a href="{{ route('vendor.show', ['id' => $plat->vendeur->id_vendeur, 'slug' => \Str::slug($plat->vendeur->nom_commercial)]) }}" 
                                           class="flex items-center gap-1.5 sm:gap-2 group/v overflow-hidden flex-1 min-w-0">
                                           <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-gray-50 dark:bg-gray-700 flex items-center justify-center border border-gray-100 dark:border-gray-600 shrink-0">
                                               @if($plat->vendeur->logo)
                                                   <img src="{{ asset('storage/' . $plat->vendeur->logo) }}" class="w-full h-full object-cover">
                                               @else
                                                   <span class="text-[9px] sm:text-[10px] font-black text-gray-400">{{ substr($plat->vendeur->nom_commercial, 0, 1) }}</span>
                                               @endif
                                           </div>
                                           <div class="flex flex-col min-w-0">
                                               <span class="text-[9px] sm:text-[10px] font-black text-gray-900 dark:text-white uppercase truncate group-hover/v:text-red-600 transition-colors">{{ $plat->vendeur->nom_commercial }}</span>
                                               <div class="flex items-center gap-1">
                                                   <svg class="w-2 h-2 sm:w-2.5 sm:h-2.5 text-blue-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                                                   <span class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase tracking-tighter truncate">Vérifié</span>
                                               </div>
                                           </div>
                                        </a>

                                        <div class="shrink-0">
                                            @if($plat->is_available)
                                                <button @click="@if($plat->groupesVariantes->isNotEmpty()) openModal({{ Js::from($plat) }}) @else addCart({{ $plat->id_plat }}) @endif"
                                                        class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl flex items-center justify-center hover:bg-black dark:hover:bg-gray-100 transition-all shadow-lg active:scale-90">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                </button>
                                            @else
                                                <span class="text-[7px] sm:text-[8px] font-black text-red-600 border border-red-600 px-1.5 py-0.5 sm:px-2 sm:py-1 rounded-lg uppercase">Épuisé</span>
                                            @endif
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
                    <!-- Professional Empty State -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl py-12 px-6 sm:p-20 text-center border border-gray-100 dark:border-gray-700 shadow-xl shadow-black/5 transition-all duration-300">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 bg-red-50 dark:bg-red-900/30 rounded-[1.5rem] sm:rounded-[2rem] flex items-center justify-center mx-auto mb-6 sm:mb-8">
                            <svg class="w-10 h-10 sm:w-12 sm:h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-display font-black text-gray-900 dark:text-white mb-4 tracking-tight">Aucune correspondance directe</h2>
                        <p class="text-gray-400 dark:text-gray-500 max-w-sm mx-auto mb-10 text-[11px] sm:text-sm font-bold uppercase tracking-widest leading-loose">
                            Nous n'avons trouvé aucun article pour votre recherche actuelle. Essayez d'ajuster les filtres ou d'utiliser d'autres mots-clés.
                        </p>
                        <a href="{{ route('explore.plats') }}" class="inline-flex px-8 sm:px-12 py-4 sm:py-5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[9px] sm:text-[10px] font-black uppercase tracking-[0.3em] rounded-2xl hover:bg-black transition-all shadow-xl shadow-gray-200 dark:shadow-none">
                            Voir tout le catalogue
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
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 z-10 sticky top-0">
                            <div class="flex justify-between items-start gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <template x-if="selectedPlat.vendeur">
                                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest truncate" x-text="selectedPlat.vendeur.nom_commercial"></span>
                                        </template>
                                        <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                        <span class="text-[9px] font-black text-red-600 uppercase tracking-widest" x-text="selectedPlat.categorie ? selectedPlat.categorie.nom_categorie : 'Produit'"></span>
                                    </div>
                                    <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white leading-tight truncate" x-text="selectedPlat.nom_plat" :title="selectedPlat.nom_plat"></h3>
                                    <p class="text-sm font-black text-red-600 dark:text-red-400 mt-0.5">
                                        <span x-text="formatPrice(calculateTotal())"></span> FCFA
                                    </p>
                                </div>
                                <button @click="closeModal()" class="p-2 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors shrink-0">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Scrollable Content -->
                        <div class="flex-1 overflow-y-auto p-6 space-y-8">
                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed font-medium" x-text="selectedPlat.description"></p>

                            <!-- Variant Groups -->
                            <template x-for="group in selectedPlat.groupes_variantes" :key="group.id_groupe">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-widest" x-text="group.nom"></h4>
                                        <div class="flex gap-2">
                                            <span x-show="group.obligatoire" class="px-2 py-0.5 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-[8px] font-black uppercase rounded">Obligatoire</span>
                                            <span x-show="group.choix_multiple" class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-[8px] font-black uppercase rounded">
                                                Max <span x-text="group.max_choix"></span>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <template x-for="option in group.variantes" :key="option.id_variante">
                                            <div class="flex flex-col gap-2 p-3 sm:p-4 rounded-xl border-2 transition-all"
                                                 :class="isSelected(group, option) ? 'border-red-600 bg-red-50/50 dark:bg-red-900/10' : 'border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800'">
                                                 
                                                 <div class="flex items-center justify-between">
                                                     <label class="flex items-center gap-3 cursor-pointer flex-1 min-w-0">
                                                         <!-- Checkbox for multiple -->
                                                         <input x-show="group.choix_multiple" 
                                                                type="checkbox" 
                                                                :name="'group_' + group.id_groupe" 
                                                                :value="option.id_variante"
                                                                @change="toggleOption(group, option)"
                                                                :checked="isSelected(group, option)"
                                                                class="w-4 h-4 text-red-600 rounded focus:ring-red-500 bg-gray-100 border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                                                         
                                                         <!-- Radio for single -->
                                                         <input x-show="!group.choix_multiple" 
                                                                type="radio" 
                                                                :name="'group_' + group.id_groupe" 
                                                                :value="option.id_variante"
                                                                @change="selectSingleOption(group, option)"
                                                                :checked="isSelected(group, option)"
                                                                class="w-4 h-4 text-red-600 focus:ring-red-500 bg-gray-100 border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                                                         
                                                         <span class="text-xs font-bold text-gray-900 dark:text-white truncate" x-text="option.nom"></span>
                                                     </label>
                                                     
                                                     <span x-show="option.prix_supplement > 0" class="text-[10px] font-black text-gray-400 dark:text-gray-500 shrink-0">
                                                         +<span x-text="formatPrice(option.prix_supplement)"></span>
                                                     </span>
                                                 </div>

                                                 <!-- Quantity Controls for this option -->
                                                 <template x-if="isSelected(group, option)">
                                                     <div class="flex items-center justify-between mt-2 pt-2 border-t border-red-100 dark:border-red-900/30">
                                                         <p class="text-[8px] font-black text-red-600 dark:text-red-400 uppercase tracking-[0.1em]">Quantité</p>
                                                         <div class="flex items-center gap-3 bg-white dark:bg-gray-800 px-2 py-1 rounded-lg border border-red-100 dark:border-red-900/30">
                                                             <button @click="decrementOption(group, option)" class="text-gray-400 hover:text-red-600 transition-colors">
                                                                 <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                                             </button>
                                                             <span class="text-[11px] font-black text-gray-900 dark:text-white w-4 text-center" x-text="getOptionQuantity(group, option)"></span>
                                                             <button @click="incrementOption(group, option)" class="text-gray-400 hover:text-red-600 transition-colors">
                                                                 <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
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
                        <div class="p-5 bg-white dark:bg-gray-800 border-t border-gray-50 dark:border-gray-700 sticky bottom-0 z-10 w-full">
                            <button @click="confirmAddToCart()" 
                                    :disabled="!isValidSelection()"
                                    class="w-full py-3.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-black dark:hover:bg-gray-200 shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed">
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
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    
    .custom-filter-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-filter-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-filter-scrollbar::-webkit-scrollbar-thumb { background: #f1f5f9; border-radius: 10px; }
    .custom-filter-scrollbar::-webkit-scrollbar-thumb:hover { background: #e2e8f0; }
    
    .dark .custom-filter-scrollbar::-webkit-scrollbar-thumb { background: #374151; }
    .dark .custom-filter-scrollbar::-webkit-scrollbar-thumb:hover { background: #4b5563; }
</style>
@endsection
