@extends('layouts.app')

@section('content')
<div class="bg-gray-50 dark:bg-gray-950 min-h-screen py-8 transition-colors duration-300" x-data="{ mobileFiltersOpen: false, loading: true }" x-init="setTimeout(() => loading = false, 800)">
    <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14">
        
        <!-- Page Header (Mobile) -->
        <div class="lg:hidden mb-8">
            <h1 class="text-3xl font-display font-black text-gray-900 dark:text-white tracking-tight">Nos Partenaires</h1>
            <p class="text-xs font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-1">{{ $vendeurs->total() }} boutiques disponibles</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Sidebar (Filters) -->
            <aside class="w-full lg:w-72 shrink-0">
                <!-- Mobile Filter Toggle -->
                <div class="lg:hidden mb-4">
                    <button @click="mobileFiltersOpen = !mobileFiltersOpen" 
                            class="w-full flex items-center justify-between px-6 py-4 bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 hover:border-orange-500 transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-orange-50 dark:bg-orange-900/20 rounded-lg flex items-center justify-center text-orange-600 dark:text-orange-400 group-hover:bg-orange-600 group-hover:text-white transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            </div>
                            <span class="text-[11px] font-black text-gray-900 text-center dark:text-white uppercase tracking-[0.2em]">Filtres & Zones</span>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="mobileFiltersOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>

                <div x-show="mobileFiltersOpen" x-transition.opacity 
                     class="fixed inset-0 bg-black/50 z-[60] lg:hidden" 
                     @click="mobileFiltersOpen = false" x-cloak></div>

                <div :class="mobileFiltersOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" 
                     class="fixed lg:sticky top-0 lg:top-28 left-0 bottom-0 w-80 lg:w-full bg-white dark:bg-gray-900 lg:bg-transparent z-[70] lg:z-auto lg:p-0 transition-transform duration-300 h-full lg:h-auto overflow-y-auto lg:overflow-visible shadow-2xl lg:shadow-none">
                    
                    <div class="lg:hidden mb-8 flex justify-between items-center">
                        <span class="text-xl font-display font-black dark:text-white">Filtrer les boutiques</span>
                        <button @click="mobileFiltersOpen = false" class="p-2 bg-gray-50 dark:bg-gray-800 rounded-xl">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="bg-white dark:bg-gray-900 rounded-2xl lg:shadow-sm lg:border lg:border-gray-100 dark:lg:border-gray-800 p-8 lg:p-7 space-y-10">
                        <form action="{{ route('explore') }}" method="GET" class="space-y-10">
                            
                            <!-- Search -->
                            <div>
                                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-6 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-600"></span>
                                    Recherche
                                </h3>
                                <div class="relative group">
                                    <input 
                                        type="text" 
                                        name="search" 
                                        value="{{ request('search') }}" 
                                        placeholder="Nom de l'établissement..." 
                                        class="w-full pl-5 pr-12 py-4 bg-gray-50 dark:bg-gray-800 border border-transparent rounded-xl text-[13px] font-bold text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-orange-500 transition-all outline-none"
                                    >
                                    <button type="submit" class="absolute right-2 top-2 bottom-2 w-11 bg-gray-900 dark:bg-gray-700 text-white rounded-xl flex items-center justify-center hover:bg-orange-600 transition-colors shadow-lg shadow-gray-200 dark:shadow-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Categories -->
                            <div>
                                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-6 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Catégories
                                </h3>
                                <div class="space-y-2 max-h-[300px] overflow-y-auto pr-2 custom-filter-scrollbar text-left">
                                    <a href="{{ route('explore', request()->except('category')) }}" 
                                       class="flex items-center justify-between px-5 py-3.5 rounded-xl text-[13px] font-black transition-all {{ !request('category') ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-xl shadow-gray-200 dark:shadow-none' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}">
                                        <span>Toutes les catégories</span>
                                        @if(!request('category'))
                                            <div class="w-1.5 h-1.5 rounded-full bg-orange-500"></div>
                                        @endif
                                    </a>
                                    @foreach($categories as $cat)
                                        <a href="{{ route('explore', array_merge(request()->query(), ['category' => $cat->id_categorie])) }}" 
                                           class="flex items-center justify-between px-5 py-3.5 rounded-xl text-[13px] font-black transition-all {{ request('category') == $cat->id_categorie ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-xl shadow-gray-200 dark:shadow-none' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}">
                                            <span>{{ $cat->nom_categorie }}</span>
                                            @if(request('category') == $cat->id_categorie)
                                                <div class="w-1.5 h-1.5 rounded-full bg-orange-500"></div>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Vendor Types (Genres de Boutique) -->
                            <div>
                                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-6 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Types de Boutique
                                </h3>
                                <div class="space-y-2 max-h-[300px] overflow-y-auto pr-2 custom-filter-scrollbar text-left">
                                    <a href="{{ route('explore', request()->except('type')) }}" 
                                       class="flex items-center justify-between px-5 py-3.5 rounded-xl text-[13px] font-black transition-all {{ !request('type') ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-xl shadow-gray-200 dark:shadow-none' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}">
                                        <span>Tous les genres</span>
                                        @if(!request('type'))
                                            <div class="w-1.5 h-1.5 rounded-full bg-orange-500"></div>
                                        @endif
                                    </a>
                                    @foreach($types as $type)
                                        <a href="{{ route('explore', array_merge(request()->query(), ['type' => $type->id_category_vendeur])) }}" 
                                           class="flex items-center justify-between px-5 py-3.5 rounded-xl text-[13px] font-black transition-all {{ request('type') == $type->id_category_vendeur ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-xl shadow-gray-200 dark:shadow-none' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}">
                                            <span>{{ $type->name }}</span>
                                            @if(request('type') == $type->id_category_vendeur)
                                                <div class="w-1.5 h-1.5 rounded-full bg-orange-500"></div>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Zones -->
                            <div>
                                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-6 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                    Zones
                                </h3>
                                <select name="zone" class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800 border border-transparent rounded-xl text-[13px] font-bold text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-orange-500 transition-all outline-none appearance-none">
                                    <option value="">Toutes les zones</option>
                                    @foreach($zones as $z)
                                        <option value="{{ $z->id_zone }}" {{ request('zone') == $z->id_zone ? 'selected' : '' }}>
                                            {{ $z->nom_zone ?: $z->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Apply Button -->
                            <div class="flex flex-col gap-3 pt-6">
                                <button type="submit" class="w-full py-5 bg-orange-600 text-white rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-orange-700 shadow-xl shadow-orange-100 dark:shadow-none transition-all active:scale-[0.98]">
                                    Filtrer les résultats
                                </button>
                                @if(request()->anyFilled(['search', 'category', 'zone']))
                                    <a href="{{ route('explore') }}" class="w-full py-5 text-center bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
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
                <div class="hidden lg:flex items-end justify-between pb-8 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h1 class="text-4xl font-display font-black text-gray-900 dark:text-white tracking-tight">Partenaires</h1>
                        <div class="flex items-center gap-3 mt-2">
                            <span class="px-3 py-1 bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 text-[10px] font-black uppercase tracking-[0.2em] rounded-full border border-orange-100/50 dark:border-orange-900/50">
                                {{ $vendeurs->total() }} boutiques actives
                            </span>
                            @if(request('search'))
                                <span class="text-xs font-bold text-gray-400 italic font-medium">résultats pour "{{ request('search') }}"</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($vendeurs->count())
                    <!-- Skeletons -->
                    <div x-show="loading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 py-10">
                        @for($i = 0; $i < 6; $i++)
                            <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-transparent shadow-sm flex flex-col h-full animate-pulse">
                                <div class="aspect-[16/10] bg-gray-200 dark:bg-gray-800 rounded-xl mb-6"></div>
                                <div class="px-2 space-y-4">
                                    <div class="h-6 bg-gray-200 dark:bg-gray-800 rounded w-3/4"></div>
                                    <div class="h-4 bg-gray-200 dark:bg-gray-800 rounded w-1/2"></div>
                                    <div class="h-20 bg-gray-200 dark:bg-gray-800 rounded"></div>
                                    <div class="flex gap-2">
                                        <div class="h-6 bg-gray-200 dark:bg-gray-800 rounded w-16"></div>
                                        <div class="h-6 bg-gray-200 dark:bg-gray-800 rounded w-16"></div>
                                    </div>
                                    <div class="h-12 bg-gray-200 dark:bg-gray-800 rounded-xl mt-4"></div>
                                </div>
                            </div>
                        @endfor
                    </div>

                    <!-- Vendors Grid -->
                    <div x-show="!loading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 py-10" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                        @foreach($vendeurs as $v)
                            <article class="group bg-white dark:bg-gray-900 rounded-2xl p-4 border border-transparent hover:border-orange-50 dark:hover:border-orange-900/30 transition-all duration-500 hover:shadow-2xl hover:shadow-orange-200/20 dark:hover:shadow-orange-900/10 flex flex-col h-full relative">
                                
                                <!-- Favorite Button -->
                                <button 
                                    @auth
                                        @php $isFav = auth()->user()->favoris()->where('id_vendeur', $v->id_vendeur)->exists(); @endphp
                                        @click="toggleFavorite({{ $v->id_vendeur }}, $event)"
                                        class="absolute top-8 right-8 z-20 w-10 h-10 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md rounded-xl flex items-center justify-center {{ $isFav ? 'text-red-600 fill-current' : 'text-gray-400' }} hover:text-red-600 transition-all shadow-lg active:scale-90"
                                    @else
                                        @click="window.location.href='{{ route('login') }}'"
                                        class="absolute top-8 right-8 z-20 w-10 h-10 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md rounded-xl flex items-center justify-center text-gray-400 hover:text-red-600 transition-all shadow-lg active:scale-90"
                                    @endauth
                                >
                                    <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                </button>

                                <!-- Image Section -->
                                <div class="relative aspect-[16/10] rounded-xl overflow-hidden mb-6 bg-gray-50 dark:bg-gray-800">
                                    @if($v->image_principale)
                                        <img src="{{ asset('storage/' . $v->image_principale) }}" 
                                             class="w-full h-full object-cover transform scale-100 group-hover:scale-110 transition-transform duration-700" 
                                             alt="{{ $v->nom_commercial }}"
                                             loading="lazy">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center">
                                            <span class="text-4xl font-black text-gray-300 dark:text-gray-600">{{ substr($v->nom_commercial, 0, 1) }}</span>
                                        </div>
                                    @endif

                                    <!-- Badges Overlay -->
                                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                                        @if($v->is_boosted)
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600/90 backdrop-blur-md rounded-lg text-white text-[9px] font-black uppercase tracking-widest shadow-xl">
                                            <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97a1 1 0 00.95.69h4.18c.969 0 1.371 1.24.588 1.81l-3.39 2.46a1 1 0 00-.364 1.118l1.286 3.97a1 1 0 01-1.54 1.118l-3.39-2.46a1 1 0 00-1.175 0l-3.39 2.46a1 1 0 01-1.54-1.118l1.286-3.97a1 1 0 00-.364-1.118L2.34 9.397c-.783-.57-.38-1.81.588-1.81h4.18a1 1 0 00.951-.69l1.286-3.97z"/></svg>
                                            Sponsorisé
                                        </div>
                                        @endif
                                        
                                        @if($v->statut_verification === 'verifie')
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-600/90 backdrop-blur-md rounded-lg text-white text-[9px] font-black uppercase tracking-widest shadow-xl">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            Certifié
                                        </div>
                                        @endif

                                        @if($v->coupons->count() > 0)
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600/90 backdrop-blur-md rounded-lg text-white text-[9px] font-black uppercase tracking-widest shadow-xl animate-pulse">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                            En Promo
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Info Content -->
                                <div class="px-2 flex-1 flex flex-col">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="space-y-1">
                                            <h3 class="text-xl font-black text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-all line-clamp-1 leading-tight">{{ $v->nom_commercial }}</h3>
                                            <div class="flex items-center gap-2">
                                                <div class="flex items-center gap-0.5">
                                                    @for($i = 0; $i < 5; $i++)
                                                        <svg class="w-3 h-3 {{ $i < round($v->note_moyenne) ? 'text-orange-500' : 'text-gray-200 dark:text-gray-700' }} fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                    @endfor
                                                </div>
                                                <span class="text-[10px] font-black text-gray-900 dark:text-white">{{ number_format($v->note_moyenne, 1) }}</span>
                                                <span class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest bg-gray-50 dark:bg-gray-800 px-2 py-0.5 rounded-md border border-gray-100 dark:border-gray-700">{{ $v->nombre_avis }} avis</span>
                                            </div>
                                        </div>
                                    </div>

                                    @if($v->description)
                                    <p class="text-[13px] font-medium text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed mb-6">
                                        {{ $v->description }}
                                    </p>
                                    @endif

                                    <!-- Metadata Tag Cloud -->
                                    <div class="flex flex-wrap gap-2 mb-6">
                                        @foreach($v->categories->take(2) as $cat)
                                            <span class="px-2.5 py-1.5 bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-[9px] font-black uppercase tracking-widest rounded-lg border border-gray-100 dark:border-gray-700 group-hover:bg-white dark:group-hover:bg-gray-700 group-hover:border-orange-100 dark:group-hover:border-orange-900/50 transition-all">
                                                {{ $cat->nom_categorie }}
                                            </span>
                                        @endforeach
                                        <span class="px-2.5 py-1.5 bg-gray-900 dark:bg-gray-800 text-white dark:text-gray-200 text-[9px] font-black uppercase tracking-widest rounded-lg shadow-sm">
                                            {{ $v->zone ? ($v->zone->nom_zone ?: $v->zone->nom) : 'Lomé' }}
                                        </span>
                                        @if(isset($v->distance))
                                        <span class="px-2.5 py-1.5 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 text-[9px] font-black uppercase tracking-widest rounded-lg border border-orange-200 dark:border-orange-800 animate-bounce-short">
                                            À {{ number_format($v->distance, 1) }} km
                                        </span>
                                        @endif
                                    </div>

                                    <!-- Action Button -->
                                    <div class="mt-auto pt-4 border-t border-gray-50 dark:border-gray-800">
                                        <a href="{{ route('vendor.show', ['id' => $v->id_vendeur, 'slug' => \Str::slug($v->nom_commercial)]) }}" 
                                           class="group/btn flex items-center justify-center gap-3 w-full py-4 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-orange-600 dark:hover:bg-orange-600 hover:text-white hover:shadow-xl hover:shadow-orange-100 dark:hover:shadow-none transition-all duration-300">
                                            Voir le catalogue
                                            <svg class="w-4 h-4 transform transition-transform group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-12 py-10">
                        {{ $vendeurs->appends(request()->query())->links('vendor.pagination.premium') }}
                    </div>

                @else
                    <!-- Empty State -->
                    <div x-show="!loading" class="bg-white dark:bg-gray-900 rounded-2xl p-24 text-center border border-gray-100 dark:border-gray-800 shadow-sm mt-10">
                        <div class="w-28 h-28 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-10 border border-gray-100 dark:border-gray-800">
                            <svg class="w-12 h-12 text-gray-200 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <h2 class="text-3xl font-display font-black text-gray-900 dark:text-white mb-4 tracking-tight">Aucune boutique trouvée</h2>
                        <p class="text-gray-400 dark:text-gray-500 font-bold max-w-sm mx-auto mb-12 uppercase text-[11px] tracking-[0.2em] leading-loose">Nous n'avons trouvé aucun partenaire correspondant à vos critères de recherche dans cette zone.</p>
                        <a href="{{ route('explore') }}" class="inline-flex px-12 py-5 bg-orange-600 text-white text-[10px] font-black uppercase tracking-[0.3em] rounded-xl hover:bg-orange-700 transition-all shadow-2xl shadow-orange-100 dark:shadow-none">
                            Réinitialiser les filtres
                        </a>
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>

<style>
    .custom-filter-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-filter-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-filter-scrollbar::-webkit-scrollbar-thumb { background: #f1f5f9; border-radius: 10px; }
    .dark .custom-filter-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; }
    .custom-filter-scrollbar::-webkit-scrollbar-thumb:hover { background: #e2e8f0; }
    .dark .custom-filter-scrollbar::-webkit-scrollbar-thumb:hover { background: #334155; }

    @keyframes bounce-short {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    .animate-bounce-short {
        animation: bounce-short 2s ease-in-out infinite;
    }
</style>
@endsection