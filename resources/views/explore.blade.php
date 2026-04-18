@extends('layouts.app')

@section('content')
<div class="bg-gray-50 dark:bg-gray-950 min-h-screen py-4 sm:py-8 transition-colors duration-300" x-data="{ mobileFiltersOpen: false, loading: true }" x-init="setTimeout(() => loading = false, 800)">
    <div class="max-w-[1920px] mx-auto px-4 sm:px-10 lg:px-14">
        
    <!-- ================= MOBILE VIEW (< lg) ================= -->
    <main class="block lg:hidden bg-gray-50 dark:bg-slate-950 min-h-screen font-sans">
        <!-- 1. Sticky Header Section (Compacted) -->
        <div class="sticky top-20 z-30 bg-gray-50 dark:bg-slate-950 border-b border-gray-200 dark:border-slate-800 pb-2 pt-2">
            <!-- Search Section -->
            <section class="px-4 mb-3">
                <form action="{{ route('explore') }}" method="GET" id="mobileSearchForm" onsubmit="event.preventDefault(); window.ajaxShopFilter(this)" class="relative flex items-center bg-white dark:bg-slate-900/80 rounded-2xl p-1 border border-gray-200 dark:border-slate-800 shadow-sm transition-all focus-within:border-orange-500/50">
                    <!-- Preserve existing filters when searching -->
                    @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    @if(request('zone')) <input type="hidden" name="zone" value="{{ request('zone') }}"> @endif
                    
                    <div class="flex-1 flex items-center px-3 gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ex: Pizza, Boutique bio..." 
                               oninput="window.debounceAjaxShopSearch(this)"
                               class="w-full py-2 bg-transparent border-none focus:ring-0 focus:outline-none text-slate-900 dark:text-white font-medium placeholder-gray-400 text-sm">
                    </div>
                </form>
            </section>

            <!-- 2. Horizontal Type Filters -->
            <section class="mb-1">
                <div class="flex overflow-x-auto gap-2 px-4 pb-2 no-scrollbar snap-x">
                    <a href="{{ route('explore', request()->except(['type', 'page'])) }}" 
                       class="snap-start shrink-0 px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all {{ !request('type') ? 'bg-orange-600 text-white' : 'bg-white dark:bg-slate-900/50 text-gray-400 border border-gray-200 dark:border-slate-800' }}">
                        Tous
                    </a>
                    @foreach($types as $type)
                    <a href="{{ route('explore', array_merge(request()->query(), ['type' => $type->id_category_vendeur, 'page' => 1])) }}" 
                       class="snap-start shrink-0 px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all {{ request('type') == $type->id_category_vendeur ? 'bg-orange-600 text-white' : 'bg-white dark:bg-slate-900/50 text-gray-400 border border-gray-200 dark:border-slate-800' }}">
                        {{ $type->name }}
                    </a>
                    @endforeach
                </div>
            </section>
        </div>

        <!-- 3. Categories Horizontal Grid (More space-efficient) -->
        <section class="mt-4 mb-6">
            <div class="px-4 mb-3 flex items-center justify-between">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400">Spécialités</h3>
                @if(request('category'))
                <a href="{{ route('explore', request()->except(['category', 'page'])) }}" class="text-[9px] font-bold text-orange-600 dark:text-orange-400 uppercase tracking-tighter">Effacer</a>
                @endif
            </div>
            <div class="flex overflow-x-auto gap-4 px-4 pb-4 no-scrollbar snap-x">
                @foreach($categories as $cat)
                <a href="{{ route('explore', array_merge(request()->query(), ['category' => $cat->id_categorie, 'page' => 1])) }}" 
                   class="snap-start shrink-0 flex flex-col items-center gap-2 group">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-all border {{ request('category') == $cat->id_categorie ? 'bg-orange-600 text-white shadow-lg border-orange-500' : 'bg-white dark:bg-slate-900 border-gray-100 dark:border-slate-800 text-gray-400 group-hover:border-orange-200' }}">
                        <span class="text-lg font-bold">{{ substr($cat->nom_categorie, 0, 1) }}</span>
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-tighter truncate w-14 text-center {{ request('category') == $cat->id_categorie ? 'text-orange-600' : 'text-gray-400' }}">{{ $cat->nom_categorie }}</span>
                </a>
                @endforeach
            </div>
        </section>

        <!-- 4. Quick Filters & Stats bar -->
        <section class="px-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="bg-orange-50 dark:bg-orange-950/30 px-3 py-2 rounded-2xl border border-orange-100 dark:border-orange-900/30">
                    <span class="text-[10px] font-black text-orange-600 uppercase tracking-widest" id="mobile-results-count">{{ $vendeurs->total() }} Résultats</span>
                </div>
                <!-- Zone Filter Toggle (Simplified) -->
                <button @click="mobileFiltersOpen = true" class="flex-1 flex items-center justify-between px-4 py-2 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl text-[10px] font-black uppercase text-gray-500">
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>{{ request('zone') ? ($zones->firstWhere('id_zone', request('zone'))->nom_zone ?? 'Zone sélectionnée') : 'Toutes les zones' }}</span>
                    </div>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                </button>
            </div>
        </section>

        <!-- 5. Vendors List (Compact Cards) -->
        <section class="px-4 pb-24 space-y-4" id="mobile-vendors-list">
            @forelse($vendeurs as $v)
            <article class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-sm border border-gray-100 dark:border-slate-800 relative group">
                <a href="{{ route('vendor.show', ['id' => $v->id_vendeur, 'slug' => \Str::slug($v->nom_commercial)]) }}" class="flex">
                    <div class="w-32 h-32 shrink-0 relative">
                        <img src="{{ $v->image_principale ? asset('storage/' . $v->image_principale) : 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=400' }}" class="w-full h-full object-cover">
                        @if($v->is_boosted)
                        <div class="absolute top-2 left-2 px-1.5 py-0.5 bg-orange-600 text-white text-[7px] font-black rounded uppercase tracking-widest shadow-lg">Ad</div>
                        @endif
                    </div>
                    <div class="p-4 flex flex-col justify-between flex-1 min-w-0">
                        <div>
                            <div class="flex justify-between items-start mb-1">
                                <h2 class="text-sm font-black text-slate-900 dark:text-white truncate pr-2">{{ $v->nom_commercial }}</h2>
                                <div class="flex items-center gap-1 shrink-0">
                                    <svg class="w-2.5 h-2.5 text-orange-500 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span class="text-[10px] font-black text-slate-900 dark:text-white">{{ number_format($v->note_moyenne, 1) }}</span>
                                </div>
                            </div>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest truncate mb-2">{{ $v->zone ? ($v->zone->nom_zone ?: $v->zone->nom) : 'Lomé' }}</p>
                            
                            <div class="flex flex-wrap gap-1">
                                @foreach($v->categories->take(2) as $cat)
                                    <span class="text-[7px] font-black uppercase tracking-tight px-2 py-0.5 bg-gray-50 dark:bg-slate-800 text-gray-500 rounded-md border border-gray-100 dark:border-slate-700">{{ $cat->nom_categorie }}</span>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="mt-2 flex items-center justify-between">
                            @if(isset($v->distance))
                                <span class="text-[8px] font-black text-orange-600 bg-orange-50 dark:bg-orange-950/40 px-2 py-0.5 rounded-md border border-orange-100 dark:border-orange-900/30">{{ number_format($v->distance, 1) }} km</span>
                            @else
                                <span></span>
                            @endif
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-2 py-1 rounded-md border border-gray-50 dark:border-slate-800">Voir</span>
                        </div>
                    </div>
                </a>
            </article>
            @empty
            <div class="py-20 text-center bg-white dark:bg-slate-900 rounded-2xl border border-dashed border-gray-200 dark:border-slate-800">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Aucun résultat trouvé</p>
            </div>
            @endforelse
        </section>

        <!-- Pagination Mobile -->
        <div id="mobile-pagination">
            @if($vendeurs->hasPages())
                <div class="px-4 pb-24 flex justify-center transform scale-75 origin-center">
                    {{ $vendeurs->appends(request()->query())->links('vendor.pagination.premium') }}
                </div>
            @endif
        </div>
    </main>

    <!-- ================= DESKTOP VIEW (>= lg) ================= -->
    <div class="hidden lg:block">
        
        <!-- Compact Header -->
        <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-100 dark:border-gray-800">
            <div>
                <h1 class="text-3xl font-display font-black text-gray-900 dark:text-white tracking-tight">Nos Partenaires</h1>
                <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em] mt-2 flex items-center gap-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-600"></span>
                    <span id="desktop-results-count">{{ $vendeurs->total() }}</span> établissements disponibles
                </p>
            </div>
            
            <div class="flex items-center gap-3">
               <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest pr-4 border-r border-gray-100 dark:border-gray-800">Lomé, Togo</span>
               <div class="flex bg-gray-100 dark:bg-gray-900 p-1 rounded-2xl border border-gray-200 dark:border-gray-800">
                    <button class="px-5 py-2.5 bg-white dark:bg-gray-800 text-orange-600 rounded-xl text-[10px] font-black shadow-sm flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        Grille
                    </button>
                </div>
            </div>
        </div>

        <div class="flex gap-10">
            
            <!-- Sidebar (Consolidated Filters) -->
            <aside class="w-72 shrink-0">
                <div class="sticky top-28 space-y-6">
                    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
                        <div class="p-6">
                            <form action="{{ route('explore') }}" method="GET" id="desktopSearchForm" onsubmit="event.preventDefault(); window.ajaxShopFilter(this)" class="space-y-8">
                                @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                                @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                                
                                <div>
                                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-4 flex items-center gap-2">
                                        <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Zone Géographique
                                    </h3>
                                    <select name="zone" onchange="window.ajaxShopFilter(this.form)" class="w-full px-4 py-3.5 bg-gray-50 dark:bg-gray-800 border border-transparent rounded-xl text-[13px] font-bold text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-orange-500 transition-all outline-none appearance-none">
                                        <option value="">Toutes les zones</option>
                                        @foreach($zones as $z)
                                            <option value="{{ $z->id_zone }}" {{ request('zone') == $z->id_zone ? 'selected' : '' }}>
                                                {{ $z->nom_zone ?: $z->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-4 flex items-center gap-2">
                                        <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        Rechercher
                                    </h3>
                                    <div class="relative">
                                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, produit..." 
                                               oninput="window.debounceAjaxShopSearch(this)"
                                               class="w-full pl-4 pr-10 py-3.5 bg-gray-50 dark:bg-gray-800 border border-transparent rounded-xl text-[13px] font-bold text-gray-900 dark:text-white placeholder-gray-400 focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-orange-500 transition-all outline-none">
                                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <div class="border-t border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 p-6 space-y-6">
                            <div>
                                <h3 class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400 mb-4">Par Type</h3>
                                <div class="space-y-1.5">
                                    <a href="{{ route('explore', request()->except(['type', 'page'])) }}" 
                                       class="flex items-center justify-between px-4 py-3 rounded-xl text-[11px] font-black transition-all {{ !request('type') ? 'bg-white dark:bg-gray-800 text-orange-600 shadow-sm border border-gray-100 dark:border-gray-700' : 'text-gray-500 hover:text-orange-600' }}">
                                        Toutes les boutiques
                                        @if(!request('type')) <div class="w-1 h-1 rounded-full bg-orange-500"></div> @endif
                                    </a>
                                    @foreach($types as $type)
                                    <a href="{{ route('explore', array_merge(request()->query(), ['type' => $type->id_category_vendeur, 'page' => 1])) }}" 
                                       class="flex items-center justify-between px-4 py-3 rounded-xl text-[11px] font-black transition-all {{ request('type') == $type->id_category_vendeur ? 'bg-white dark:bg-gray-800 text-orange-600 shadow-sm border border-gray-100 dark:border-gray-700' : 'text-gray-500 hover:text-orange-600' }}">
                                        {{ $type->name }}
                                        @if(request('type') == $type->id_category_vendeur) <div class="w-1 h-1 rounded-full bg-orange-500"></div> @endif
                                    </a>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <h3 class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400 mb-4">Spécialités</h3>
                                <div class="max-h-60 overflow-y-auto pr-2 custom-filter-scrollbar space-y-1.5">
                                    <a href="{{ route('explore', request()->except(['category', 'page'])) }}" 
                                       class="flex items-center justify-between px-4 py-3 rounded-xl text-[11px] font-black transition-all {{ !request('category') ? 'bg-white dark:bg-gray-800 text-orange-600 shadow-sm border border-gray-100 dark:border-gray-700' : 'text-gray-500 hover:text-orange-600' }}">
                                        Toutes les spécialités
                                        @if(!request('category')) <div class="w-1 h-1 rounded-full bg-orange-500"></div> @endif
                                    </a>
                                    @foreach($categories as $cat)
                                    <a href="{{ route('explore', array_merge(request()->query(), ['category' => $cat->id_categorie, 'page' => 1])) }}" 
                                       class="flex items-center justify-between px-4 py-3 rounded-xl text-[11px] font-black transition-all {{ request('category') == $cat->id_categorie ? 'bg-white dark:bg-gray-800 text-orange-600 shadow-sm border border-gray-100 dark:border-gray-700' : 'text-gray-500 hover:text-orange-600' }}">
                                        {{ $cat->nom_categorie }}
                                        @if(request('category') == $cat->id_categorie) <div class="w-1 h-1 rounded-full bg-orange-500"></div> @endif
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="p-6 bg-white dark:bg-gray-900">
                             <a href="{{ route('explore') }}" class="flex items-center justify-center w-full py-4 text-center bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                Effacer tous les filtres
                            </a>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1" id="desktop-vendors-list">
                @if($vendeurs->count())
                    <div x-show="!loading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                        @foreach($vendeurs as $v)
                            <article class="group relative flex flex-col bg-white dark:bg-gray-900 rounded-2xl p-3 border border-gray-100 dark:border-gray-800 hover:border-orange-50 dark:hover:border-orange-900/30 transition-all duration-500 hover:shadow-2xl hover:shadow-orange-200/10 dark:hover:shadow-none h-full">
                                <a href="{{ route('vendor.show', ['id' => $v->id_vendeur, 'slug' => \Str::slug($v->nom_commercial)]) }}" class="block">
                                    <div class="relative aspect-[4/3] rounded-xl overflow-hidden mb-4 bg-gray-50 dark:bg-gray-800">
                                        @if($v->image_principale)
                                            <img src="{{ asset('storage/' . $v->image_principale) }}" class="w-full h-full object-cover transform scale-100 group-hover:scale-110 transition-transform duration-700">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-4xl font-black text-gray-200 dark:text-gray-700 bg-gray-50 dark:bg-gray-800">{{ substr($v->nom_commercial, 0, 1) }}</div>
                                        @endif
                                    </div>
                                    <div class="px-2">
                                        <div class="flex justify-between items-start mb-1">
                                            <h3 class="text-[15px] font-black text-gray-900 dark:text-white line-clamp-1 leading-tight group-hover:text-orange-600 transition-colors">{{ $v->nom_commercial }}</h3>
                                            <div class="flex items-center gap-1 bg-orange-50 dark:bg-orange-900/20 px-1.5 py-0.5 rounded-lg shrink-0">
                                                <svg class="w-2.5 h-2.5 text-orange-600 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                <span class="text-[10px] font-black text-orange-600">{{ number_format($v->note_moyenne, 1) }}</span>
                                            </div>
                                        </div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">{{ $v->zone ? ($v->zone->nom_zone ?: $v->zone->nom) : 'Lomé' }}</p>
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>
                    <!-- Pagination Desktop -->
                    <div class="mt-16 py-8 border-t border-gray-100 dark:border-gray-800">
                        {{ $vendeurs->appends(request()->query())->links('vendor.pagination.premium') }}
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-900 rounded-2xl p-24 text-center border border-gray-100 dark:border-gray-800 shadow-sm mt-10">
                        <h2 class="text-3xl font-display font-black text-gray-900 dark:text-white mb-4 tracking-tight">Oups ! Rien trouvé.</h2>
                        <p class="text-gray-400 dark:text-gray-500 font-bold max-w-sm mx-auto mb-10 uppercase text-[10px] tracking-widest">Nous n'avons trouvé aucun partenaire correspondant à vos critères.</p>
                        <a href="{{ route('explore') }}" class="inline-flex px-10 py-4 bg-orange-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl shadow-xl shadow-orange-100 dark:shadow-none">Toutes les boutiques</a>
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>

<!-- Zone Filter Mobile Modal -->
<div x-show="mobileFiltersOpen" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;"
     x-data="{ zoneSearch: '' }"
     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="mobileFiltersOpen = false"></div>
    <div class="relative min-h-screen flex items-end justify-center">
        <div class="bg-white dark:bg-slate-900 w-full rounded-t-2xl shadow-2xl overflow-hidden"
             x-transition:enter="transition ease-out duration-400 transform" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-300 transform" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
            
            <!-- Header -->
            <div class="px-5 pt-4 pb-3 flex items-center justify-between border-b border-gray-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Zone de livraison</h3>
                        <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">{{ $zones->count() }} zones disponibles</p>
                    </div>
                </div>
                <button @click="mobileFiltersOpen = false" class="p-2 bg-gray-100 dark:bg-slate-800 rounded-xl text-gray-400 hover:text-gray-600 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Search Input -->
            <div class="px-5 py-3">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-model="zoneSearch" placeholder="Rechercher une zone..." 
                           class="w-full pl-9 pr-4 py-2.5 bg-gray-50 dark:bg-slate-800 border-none rounded-xl text-[11px] font-bold text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-orange-500/30 transition-all">
                </div>
            </div>

            <!-- Zones List -->
            <div class="px-5 pb-4 max-h-[55vh] overflow-y-auto space-y-1.5 custom-filter-scrollbar">
                <!-- Option: Toutes les zones -->
                <button @click="window.location.href='{{ route('explore', request()->except(['zone', 'page'])) }}'" 
                        x-show="!zoneSearch || 'toutes les zones'.includes(zoneSearch.toLowerCase())"
                        class="w-full px-4 py-3 rounded-xl flex items-center justify-between transition-all active:scale-[0.98] {{ !request('zone') ? 'bg-orange-600 text-white shadow-lg shadow-orange-600/20' : 'bg-gray-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                    <div class="flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center {{ !request('zone') ? 'bg-white/20' : 'bg-orange-100 dark:bg-orange-900/30' }}">
                            <svg class="w-3 h-3 {{ !request('zone') ? 'text-white' : 'text-orange-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest">Toutes les zones</span>
                    </div>
                    @if(!request('zone'))
                    <div class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
                    @endif
                </button>

                @foreach($zones as $z)
                <button @click="window.location.href='{{ route('explore', array_merge(request()->query(), ['zone' => $z->id_zone, 'page' => 1])) }}'" 
                        x-show="!zoneSearch || '{{ strtolower($z->nom_zone ?: $z->nom) }}'.includes(zoneSearch.toLowerCase()) || '{{ strtolower($z->ville ?? '') }}'.includes(zoneSearch.toLowerCase())"
                        class="w-full px-4 py-3 rounded-xl flex items-center justify-between transition-all active:scale-[0.98] {{ request('zone') == $z->id_zone ? 'bg-orange-600 text-white shadow-lg shadow-orange-600/20' : 'bg-gray-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 {{ request('zone') == $z->id_zone ? 'bg-white/20' : 'bg-orange-100 dark:bg-orange-900/30' }}">
                            <svg class="w-3 h-3 {{ request('zone') == $z->id_zone ? 'text-white' : 'text-orange-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div class="text-left min-w-0">
                            <span class="text-[10px] font-black uppercase tracking-widest block truncate">{{ $z->nom_zone ?: $z->nom }}</span>
                            @if($z->ville)
                            <span class="text-[8px] font-bold uppercase tracking-widest {{ request('zone') == $z->id_zone ? 'text-orange-200' : 'text-gray-400' }} block truncate">{{ $z->ville }} {{ $z->rayon_km ? '• ' . number_format($z->rayon_km, 0) . ' km' : '' }}</span>
                            @endif
                        </div>
                    </div>
                    @if(request('zone') == $z->id_zone)
                    <div class="w-1.5 h-1.5 rounded-full bg-white animate-pulse shrink-0"></div>
                    @endif
                </button>
                @endforeach

                <!-- No result message -->
                <div x-show="zoneSearch && document.querySelectorAll('[x-show*=zoneSearch]:not([style*=none])').length <= 1" class="py-6 text-center">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Aucune zone trouvée</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-5 pt-2 pb-6 border-t border-gray-100 dark:border-slate-800">
                <button @click="mobileFiltersOpen = false" class="w-full py-3.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-slate-800 dark:hover:bg-gray-100 transition-all active:scale-[0.98]">Fermer</button>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-filter-scrollbar::-webkit-scrollbar { width: 3px; }
    .custom-filter-scrollbar::-webkit-scrollbar-thumb { background: #f1f5f9; border-radius: 10px; }
    .dark .custom-filter-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; }
    .no-scrollbar::-webkit-scrollbar { display<script>
window.shopsAjaxDebounce = null;

window.fetchShopResults = (url) => {
    document.body.style.cursor = 'wait';
    
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const mCount = document.querySelector('#mobile-results-count');
            const newMCount = doc.querySelector('#mobile-results-count');
            if (mCount && newMCount) mCount.innerHTML = newMCount.innerHTML;

            const dCount = document.querySelector('#desktop-results-count');
            const newDCount = doc.querySelector('#desktop-results-count');
            if (dCount && newDCount) dCount.innerHTML = newDCount.innerHTML;

            const mobileList = document.querySelector('#mobile-vendors-list');
            const newMobileList = doc.querySelector('#mobile-vendors-list');
            if (mobileList && newMobileList) mobileList.innerHTML = newMobileList.innerHTML;

            const desktopList = document.querySelector('#desktop-vendors-list');
            const newDesktopList = doc.querySelector('#desktop-vendors-list');
            if (desktopList && newDesktopList) desktopList.innerHTML = newDesktopList.innerHTML;

            const mobilePag = document.querySelector('#mobile-pagination');
            const newMobilePag = doc.querySelector('#mobile-pagination');
            if (mobilePag && newMobilePag) mobilePag.innerHTML = newMobilePag.innerHTML;

            document.body.style.cursor = 'default';
        })
        .catch(() => document.body.style.cursor = 'default');
};

window.ajaxShopFilter = (form) => {
    const url = new URL(form.action);
    const currentParams = new URLSearchParams(window.location.search);
    const params = new URLSearchParams(new FormData(form));
    
    // Merge URL existing params
    if(currentParams.has('category') && !params.has('category')) params.set('category', currentParams.get('category'));
    if(currentParams.has('type') && !params.has('type')) params.set('type', currentParams.get('type'));
    if(currentParams.has('zone') && !params.has('zone')) params.set('zone', currentParams.get('zone'));
    
    url.search = params.toString();
    window.history.pushState({}, '', url);
    window.fetchShopResults(url);
};

window.debounceAjaxShopSearch = (input) => {
    clearTimeout(window.shopsAjaxDebounce);
    window.shopsAjaxDebounce = setTimeout(() => {
        window.ajaxShopFilter(input.closest('form'));
    }, 300);
};

document.addEventListener('DOMContentLoaded', () => {

    document.body.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (link && link.href.includes('explore') && !link.href.includes('vendor.show')) {
            e.preventDefault();
            window.history.pushState({}, '', link.href);
            window.fetchShopResults(link.href);
            
            fetch(link.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                   const parser = new DOMParser();
                   const doc = parser.parseFromString(html, 'text/html');
                   
                   const mFilters = document.querySelector('.sticky.top-20');
                   const newMFilters = doc.querySelector('.sticky.top-20');
                   if(mFilters && newMFilters) mFilters.innerHTML = newMFilters.innerHTML;
                   
                   const mCats = document.querySelector('section.mt-4.mb-6');
                   const newMCats = doc.querySelector('section.mt-4.mb-6');
                   if(mCats && newMCats) mCats.innerHTML = newMCats.innerHTML;

                   const dSidebar = document.querySelector('aside.w-72');
                   const newDSidebar = doc.querySelector('aside.w-72');
                   if(dSidebar && newDSidebar) dSidebar.innerHTML = newDSidebar.innerHTML;
                });
        }
    });

    window.addEventListener('popstate', () => {
        if(window.location.href.includes('explore')) {
            window.fetchShopResults(window.location.href);
        }
    });
});
</script>
@endsection
