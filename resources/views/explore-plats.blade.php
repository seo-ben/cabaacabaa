@extends('layouts.app')

@section('content')
<script src="{{ asset('js/product-options.js') }}"></script>

<div class="bg-white dark:bg-gray-950 min-h-screen transition-colors duration-300" 
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
    

    <!-- ================= MOBILE VIEW (< lg) ================= -->
    <main class="block lg:hidden bg-gray-50 dark:bg-slate-950 min-h-screen font-sans">
        <!-- 1. Sticky Search & Categories Bar -->
        <div class="sticky top-0 z-30 bg-gray-50/80 dark:bg-slate-950/80 backdrop-blur-xl border-b border-gray-100 dark:border-slate-800/50 pb-2">
            <!-- Search & Location Bar -->
            <section class="pt-6 px-4 mb-4">
                <div class="flex flex-col gap-4">
                    <h1 class="text-3xl font-display font-black text-gray-900 dark:text-white tracking-tight">Nos Produits</h1>
                    
                    <form action="{{ route('explore.plats') }}" method="GET" class="relative flex items-center bg-white dark:bg-slate-900/50 rounded-2xl p-1.5 border border-gray-100 dark:border-slate-800 shadow-sm focus-within:ring-2 focus-within:ring-red-500/20 transition-all">
                        <div class="flex-1 flex items-center px-3 gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Pizza, Burger..." 
                                   class="w-full py-2 bg-transparent border-none focus:ring-0 focus:outline-none text-slate-900 dark:text-white font-medium placeholder-gray-400 text-sm">
                        </div>
                    </form>
                </div>
            </section>

            <!-- 2. Horizontal Categories -->
            <section class="px-3">
                <div class="flex overflow-x-auto snap-x snap-mandatory px-4 pb-2 no-scrollbar">
                    <a href="{{ route('explore.plats', request()->except('category')) }}" 
                       class="snap-start shrink-0 px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ !request('category') ? 'bg-red-600 text-white shadow-lg shadow-red-500/20' : 'bg-white/50 dark:bg-slate-900/50 text-gray-400 border border-gray-100 dark:border-slate-800' }}">
                        Tout
                    </a>
                    @foreach($categories as $cat)
                    <a href="{{ route('explore.plats', array_merge(request()->query(), ['category' => $cat->id_categorie])) }}" 
                       class="snap-start shrink-0 px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ request('category') == $cat->id_categorie ? 'bg-red-600 text-white shadow-lg shadow-red-500/20' : 'bg-white/50 dark:bg-slate-900/50 text-gray-400 border border-gray-100 dark:border-slate-800' }}">
                        {{ $cat->nom_categorie }}
                    </a>
                    @endforeach
                </div>
            </section>
        </div>

        <!-- 3. Stats Bar -->
        <section class="px-4 mb-8">
            <div class="flex items-center justify-between text-[10px] font-black text-gray-400 uppercase tracking-widest">
                <span>{{ $plats->total() }} Articles trouvés</span>
                <button @click="mobileFiltersOpen = true" class="flex items-center gap-1.5 text-red-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    Filtres
                </button>
            </div>
        </section>

        <!-- 4. Grid -->
        <section class="px-4 pb-12">
            <div class="grid grid-cols-2 gap-4">
                @foreach($plats as $plat)
                <div class="bg-white dark:bg-slate-900 rounded-3xl overflow-hidden shadow-sm border border-gray-50 dark:border-slate-800 flex flex-col">
                    <div class="relative aspect-square">
                        <img src="{{ $plat->image_principale ? asset('storage/' . $plat->image_principale) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&fit=crop' }}" class="w-full h-full object-cover">
                        @if($plat->en_promotion)
                            <span class="absolute top-2 left-2 bg-red-600 text-white text-[8px] font-black px-1.5 py-0.5 rounded-lg shadow-lg">PROMO</span>
                        @endif
                        <button @click="@if($plat->groupesVariantes->isNotEmpty()) openModal({{ Js::from($plat) }}) @else addCart({{ $plat->id_plat }}) @endif" 
                                class="absolute bottom-2 right-2 w-10 h-10 bg-white dark:bg-slate-800 text-red-600 rounded-xl shadow-lg flex items-center justify-center active:scale-90 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                    <div class="p-3 flex-1 flex flex-col">
                        <span class="text-[8px] font-black text-red-600 uppercase tracking-tighter mb-1">{{ $plat->categorie->nom_categorie ?? 'Article' }}</span>
                        <h4 class="font-bold text-xs text-slate-900 dark:text-white line-clamp-1 mb-1">{{ $plat->nom_plat }}</h4>
                        <p class="text-[9px] text-gray-400 line-clamp-1 mb-2">Par {{ $plat->vendeur->nom_commercial }}</p>
                        <div class="mt-auto flex items-center justify-between">
                            <span class="text-xs font-black text-slate-900 dark:text-white">{{ number_format($plat->en_promotion ? $plat->prix_promotion : $plat->prix, 0, ',', ' ') }} F</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination Mobile -->
            @if($plats->hasPages())
                <div class="mt-8 flex justify-center scale-90">
                    {{ $plats->appends(request()->query())->links('vendor.pagination.premium') }}
                </div>
            @endif
        </section>
    </main>

    <!-- ================= DESKTOP VIEW (>= lg) ================= -->
    <div class="hidden lg:block max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14 py-12">
        <div class="flex flex-col lg:flex-row gap-12">
            
            <!-- Elegant Sticky Sidebar (Desktop) -->
            <aside class="w-72 shrink-0">
                <div class="sticky top-28 space-y-10">
                    
                    <!-- Search Form -->
                    <form action="{{ route('explore.plats') }}" method="GET" class="space-y-6">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Rechercher</label>
                            <div class="relative group">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Pizza, Burger..." 
                                       class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-red-500 transition-all outline-none">
                                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-gray-400 group-hover:text-red-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Budget (FCFA)</label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" 
                                       class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl text-xs font-bold outline-none focus:ring-1 focus:ring-red-500">
                                <span class="text-gray-300">-</span>
                                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" 
                                       class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl text-xs font-bold outline-none focus:ring-1 focus:ring-red-500">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-black dark:hover:bg-gray-200 transition-all shadow-lg active:scale-95">
                            Filtrer le Catalogue
                        </button>
                    </form>

                    <!-- Categories -->
                    <div class="space-y-4">
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-900 dark:text-white flex items-center gap-2">
                             <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                             Catégories
                        </h3>
                        <div class="space-y-1">
                            <a href="{{ route('explore.plats', request()->except('category')) }}" 
                               class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition-all {{ !request('category') ? 'bg-red-50 dark:bg-red-900/20 text-red-600' : 'text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                <span>Tout voir</span>
                                <span class="w-5 h-5 bg-gray-50 dark:bg-gray-700/50 rounded-lg flex items-center justify-center text-[8px] font-black opacity-0 group-hover:opacity-100 transition-all">
                                    →
                                </span>
                            </a>
                            @foreach($categories as $cat)
                                <a href="{{ route('explore.plats', array_merge(request()->query(), ['category' => $cat->id_categorie])) }}" 
                                   class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition-all {{ request('category') == $cat->id_categorie ? 'bg-red-50 dark:bg-red-900/20 text-red-600' : 'text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                    <span>{{ $cat->nom_categorie }}</span>
                                    @if(request('category') == $cat->id_categorie)
                                        <div class="w-2 h-2 rounded-full bg-red-600 shadow-sm shadow-red-200"></div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Product Grid (Desktop) -->
            <main class="flex-1">
                @if($plats->count())
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6 sm:gap-8">
                        @foreach($plats as $plat)
                            <div class="group bg-white dark:bg-gray-900 rounded-[2rem] border border-gray-100 dark:border-gray-800 hover:border-red-200 dark:hover:border-red-900/50 transition-all duration-500 flex flex-col overflow-hidden hover:shadow-2xl hover:shadow-red-500/5">
                                
                                <!-- Visual Image Container -->
                                <div class="relative aspect-[4/5] overflow-hidden bg-gray-50 dark:bg-gray-800">
                                    @if($plat->image_principale)
                                        <img src="{{ asset('storage/' . $plat->image_principale) }}" 
                                             class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700" 
                                             alt="{{ $plat->nom_plat }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-200 dark:text-gray-700">
                                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif

                                    <!-- Status Badges Top Left -->
                                    <div class="absolute top-4 left-4 flex flex-col gap-2 z-10">
                                        @if($plat->en_promotion)
                                            <span class="px-3 py-1.5 bg-red-600 text-white text-[9px] font-black uppercase tracking-widest rounded-full shadow-lg shadow-red-500/30">Offre Spéciale</span>
                                        @endif
                                    </div>

                                    <!-- Quick Options Wrapper -->
                                    <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                                    <!-- Bottom Gradient Overlay -->
                                    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/20 to-transparent pointer-events-none"></div>
                                </div>

                                <!-- Information Area -->
                                <div class="p-5 sm:p-6 flex-1 flex flex-col">
                                    
                                    <!-- Vendor Category Mini Info -->
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-[9px] font-black uppercase tracking-[0.2em] text-red-600">{{ $plat->categorie ? $plat->categorie->nom_categorie : 'Produit' }}</span>
                                        <div class="flex items-center gap-1">
                                            <svg class="w-2.5 h-2.5 text-yellow-500 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            <span class="text-[10px] font-black text-gray-900 dark:text-gray-300">{{ number_format($plat->vendeur->note_moyenne, 1) }}</span>
                                        </div>
                                    </div>

                                    <h3 class="text-sm sm:text-base font-black text-gray-900 dark:text-white leading-snug group-hover:text-red-600 transition-colors h-10 sm:h-12 line-clamp-2 mb-4">
                                        {{ $plat->nom_plat }}
                                    </h3>

                                    <!-- Pricing Block -->
                                    <div class="flex items-end justify-between gap-4 mt-auto">
                                        <div class="flex flex-col">
                                            @if($plat->en_promotion)
                                                <div class="flex items-center gap-2">
                                                    <span class="text-lg font-black text-red-600">{{ number_format($plat->prix_promotion, 0, ',', ' ') }} FCFA</span>
                                                    <span class="text-[10px] font-bold text-gray-300 line-through">{{ number_format($plat->prix, 0, ',', ' ') }}</span>
                                                </div>
                                            @else
                                                <span class="text-lg font-black text-gray-900 dark:text-white">{{ number_format($plat->prix, 0, ',', ' ') }} FCFA</span>
                                            @endif
                                            <span class="text-[8px] font-black uppercase tracking-widest text-gray-400 mt-0.5">Disponibilité immédiate</span>
                                        </div>

                                        <!-- Add Button -->
                                        @if($plat->is_available)
                                            <button @click="@if($plat->groupesVariantes->isNotEmpty()) openModal({{ Js::from($plat) }}) @else addCart({{ $plat->id_plat }}) @endif"
                                                    class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl flex items-center justify-center hover:bg-black dark:hover:bg-gray-100 transition-all shadow-xl active:scale-90">
                                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                            </button>
                                        @else
                                            <span class="text-[9px] font-black text-gray-400 border border-gray-100 dark:border-gray-800 px-3 py-2 rounded-xl uppercase">Épuisé</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Vendor Mini Link -->
                                    <div class="mt-6 pt-4 border-t border-gray-50 dark:border-gray-800 flex items-center gap-3">
                                        <div class="w-6 h-6 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center border border-gray-100 dark:border-gray-700 shrink-0">
                                            @if($plat->vendeur->logo)
                                                <img src="{{ asset('storage/' . $plat->vendeur->logo) }}" class="w-full h-full object-cover rounded-lg">
                                            @else
                                                <span class="text-[8px] font-black text-gray-400 uppercase">{{ substr($plat->vendeur->nom_commercial, 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('vendor.show', ['id' => $plat->vendeur->id_vendeur, 'slug' => \Str::slug($plat->vendeur->nom_commercial)]) }}" 
                                           class="text-[9px] font-black text-gray-400 uppercase tracking-widest hover:text-red-600 transition-colors truncate">
                                            {{ $plat->vendeur->nom_commercial }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Enhanced Pagination -->
                    <div class="mt-20">
                        {{ $plats->appends(request()->query())->links('vendor.pagination.premium') }}
                    </div>
                @else
                    <!-- Clean Empty State -->
                    <div class="text-center py-32 bg-gray-50 dark:bg-gray-900/50 rounded-3xl border border-dashed border-gray-200 dark:border-gray-800">
                        <div class="w-20 h-20 bg-white dark:bg-gray-800 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-sm">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <h2 class="text-2xl font-display font-black text-gray-900 dark:text-white mb-4">Aucun résultat trouvé</h2>
                        <p class="text-sm font-medium text-gray-400 max-w-sm mx-auto mb-10">
                            Nous n'avons trouvé aucun article correspondant à vos critères. Essayez d'ajuster vos filtres.
                        </p>
                        <a href="{{ route('explore.plats') }}" class="inline-flex px-10 py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-black transition-all">
                            Réinitialiser la recherche
                        </a>
                    </div>
                @endif
            </main>
        </div>
    </div>

    <!-- Mobile Filters Slide-over -->
    <div x-show="mobileFiltersOpen" 
         class="fixed inset-0 z-[100] lg:hidden" 
         x-transition:enter="transition ease-in-out duration-500"
         x-cloak>
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="mobileFiltersOpen = false"></div>
        <div class="absolute right-0 top-0 bottom-0 w-80 bg-white dark:bg-gray-900 p-8 shadow-2xl overflow-y-auto">
            <div class="flex justify-between items-center mb-10">
                <span class="text-xl font-display font-black text-gray-900 dark:text-white uppercase tracking-tight">Filtres</span>
                <button @click="mobileFiltersOpen = false" class="p-3 bg-gray-50 dark:bg-gray-800 rounded-2xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form action="{{ route('explore.plats') }}" method="GET" class="space-y-10">
                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Mot-clé</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Pizza..." 
                           class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl text-sm font-bold">
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Budget Max</label>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="FCFA" 
                           class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl text-sm font-bold">
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Secteur</label>
                    <div class="grid gap-2">
                        @foreach($categories as $cat)
                            <button type="button" 
                                    @click="window.location.href='{{ route('explore.plats', array_merge(request()->query(), ['category' => $cat->id_categorie])) }}'"
                                    class="px-5 py-4 rounded-2xl text-xs font-black uppercase tracking-widest text-left transition-all {{ request('category') == $cat->id_categorie ? 'bg-red-600 text-white shadow-lg' : 'bg-gray-50 dark:bg-gray-800 text-gray-400' }}">
                                {{ $cat->nom_categorie }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="w-full py-5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-3xl text-[10px] font-black uppercase tracking-[0.3em] shadow-2xl">
                    Afficher les résultats
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Options with Improved Glassmorphism -->
    <div x-show="modalOpen" class="fixed inset-0 z-[110] overflow-y-auto" role="dialog" aria-modal="true" x-cloak>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-md transition-opacity" @click="closeModal()"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="modalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 class="relative inline-block align-bottom bg-white dark:bg-gray-900 rounded-[3rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full border border-gray-100 dark:border-gray-800">
                
                <template x-if="selectedPlat">
                    <div class="flex flex-col max-h-[90vh]">
                        <!-- Elegant Header -->
                        <div class="px-8 py-6 border-b border-gray-50 dark:border-gray-800 flex justify-between items-center bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl sticky top-0 z-20">
                            <div class="flex-1 min-w-0 pr-8">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[9px] font-black text-red-600 uppercase tracking-widest" x-text="selectedPlat.categorie ? selectedPlat.categorie.nom_categorie : 'Produit'"></span>
                                    <template x-if="selectedPlat.vendeur">
                                        <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest" x-text="'• ' + selectedPlat.vendeur.nom_commercial"></span>
                                    </template>
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white truncate" x-text="selectedPlat.nom_plat"></h3>
                                <p class="text-sm font-black text-red-600 mt-0.5">
                                    <span x-text="formatPrice(calculateTotal())"></span> FCFA
                                </p>
                            </div>
                            <button @click="closeModal()" class="p-3 bg-gray-50 dark:bg-gray-800 rounded-2xl hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <svg class="w-6 h-6 text-gray-400 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Content with Refined Inputs -->
                        <div class="flex-1 overflow-y-auto p-8 space-y-10 custom-scrollbar">
                            <!-- Variant Groups -->
                            <template x-for="group in selectedPlat.groupes_variantes" :key="group.id_groupe">
                                <div class="space-y-5">
                                    <div class="flex items-baseline justify-between">
                                        <h4 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest" x-text="group.nom"></h4>
                                        <div class="flex gap-2">
                                            <template x-if="group.obligatoire">
                                                <span class="px-2 py-0.5 bg-red-600 text-white text-[8px] font-black uppercase rounded-lg">REQUIS</span>
                                            </template>
                                            <template x-if="group.choix_multiple">
                                                <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-800 text-gray-500 text-[8px] font-black uppercase rounded-lg">MAX <span x-text="group.max_choix"></span></span>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="grid gap-3">
                                        <template x-for="option in group.variantes" :key="option.id_variante">
                                            <div class="relative group cursor-pointer" @click="group.choix_multiple ? toggleOption(group, option) : selectSingleOption(group, option)">
                                                <div class="p-4 rounded-3xl border-2 transition-all flex items-center justify-between"
                                                     :class="isSelected(group, option) ? 'border-red-600 bg-red-50/50 dark:bg-red-900/10' : 'border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 hover:border-gray-200 dark:hover:border-gray-700'">
                                                     
                                                     <div class="flex items-center gap-4 flex-1 min-w-0">
                                                        <div class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all"
                                                             :class="isSelected(group, option) ? 'bg-red-600 border-red-600' : 'border-gray-200 dark:border-gray-700'">
                                                            <svg x-show="isSelected(group, option)" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                        </div>
                                                        <div class="flex flex-col">
                                                            <span class="text-sm font-bold text-gray-900 dark:text-white" x-text="option.nom"></span>
                                                            <template x-if="option.prix_supplement > 0">
                                                                <span class="text-[10px] font-black text-red-600 mt-0.5">+<span x-text="formatPrice(option.prix_supplement)"></span> FCFA</span>
                                                            </template>
                                                        </div>
                                                     </div>

                                                     <!-- Option Quantity for Multiple Choice with Multi-Quantity -->
                                                     <template x-if="isSelected(group, option)">
                                                        <div class="flex items-center gap-3 bg-white dark:bg-gray-900 px-3 py-2 rounded-xl border border-red-100 dark:border-red-900/30" @click.stop>
                                                            <button @click="decrementOption(group, option)" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-red-600 transition-colors">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
                                                            </button>
                                                            <span class="text-sm font-black text-gray-900 dark:text-white min-w-[20px] text-center" x-text="getOptionQuantity(group, option)"></span>
                                                            <button @click="incrementOption(group, option)" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-red-600 transition-colors">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                                            </button>
                                                        </div>
                                                     </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Modal Action Footer -->
                        <div class="px-8 py-6 bg-white dark:bg-gray-900 border-t border-gray-50 dark:border-gray-800 sticky bottom-0 z-20">
                            <button @click="confirmAddToCart()" 
                                    :disabled="!isValidSelection()"
                                    class="w-full py-5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-3xl text-[11px] font-black uppercase tracking-[0.3em] hover:bg-black dark:hover:bg-gray-100 shadow-2xl transition-all disabled:opacity-30 disabled:cursor-not-allowed">
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
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #f3f4f6; border-radius: 10px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #1f2937; }
</style>
@endsection
