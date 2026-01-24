@extends('layouts.app')

@section('title', 'Accueil - ' . config('app.name'))

@section('content')

<main class="bg-white dark:bg-slate-950 font-sans selection:bg-red-500 selection:text-white">

    <!-- 1. Refined Hero Section -->
    <section class="relative pt-16 pb-20 lg:pt-24 lg:pb-32 overflow-hidden">
        <!-- Subtle Ambient Background -->
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-red-500/5 rounded-full blur-[120px]"></div>
        
        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                
                <!-- Left Content -->
                <div data-aos="fade-up">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-red-50 dark:bg-red-900/20 rounded-full mb-6 border border-red-100 dark:border-red-900/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                        <span class="text-[10px] font-bold text-red-600 dark:text-red-400 uppercase tracking-wider">N°1 de la marketplace locale</span>
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-slate-900 dark:text-white leading-tight mb-6">
                        Tous vos commerces, <br/>
                        <span class="text-red-600">à portée de clic</span>.
                    </h1>
                    
                    <p class="text-base md:text-lg text-slate-600 dark:text-slate-400 mb-8 max-w-lg leading-relaxed">
                        Restaurants, boutiques, épiceries... Commandez tout ce dont vous avez besoin, livré chez vous ou à emporter.
                    </p>

                    <!-- Search Box - More Compact -->
                    <form action="{{ route('explore.plats') }}" method="GET" class="relative max-w-lg flex items-center bg-gray-50 dark:bg-slate-900 rounded-2xl p-1.5 border border-gray-200 dark:border-slate-800 shadow-sm focus-within:ring-2 focus-within:ring-red-500/20 transition-all">
                        <div class="flex-1 flex items-center px-4 gap-3">
                            <button type="button" onclick="detectLocation()" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Autour de moi">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </button>
                            <input type="text" name="search" placeholder="Quel produit recherchez-vous ?" 
                                   class="w-full py-3 bg-transparent border-none focus:ring-0 focus:outline-none text-slate-900 dark:text-white font-medium placeholder-gray-400 text-sm">
                            <input type="hidden" name="lat" id="search_lat" value="{{ request('lat') }}">
                            <input type="hidden" name="lng" id="search_lng" value="{{ request('lng') }}">
                        </div>
                        <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 transition-colors shadow-md">
                            Rechercher
                        </button>
                    </form>

                    <!-- Trust Stats - Balanced -->
                    <div class="mt-10 flex items-center gap-8">
                        <div>
                            <p class="text-xl font-bold text-slate-900 dark:text-white">{{ number_format($stats['total_commandes']) }}+</p>
                            <p class="text-[11px] text-gray-500 uppercase tracking-wide font-semibold">Commandes</p>
                        </div>
                        <div class="w-px h-8 bg-gray-200 dark:bg-slate-800"></div>
                        <div>
                            <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $stats['total_vendeurs'] }}+</p>
                            <p class="text-[11px] text-gray-500 uppercase tracking-wide font-semibold">Établissements</p>
                        </div>
                        <div class="w-px h-8 bg-gray-200 dark:bg-slate-800"></div>
                        <div class="flex items-center gap-2">
                            <div class="flex -space-x-2">
                                @for($i=1; $i<=3; $i++)
                                    <img src="https://i.pravatar.cc/100?u={{$i}}" class="w-8 h-8 rounded-full border-2 border-white dark:border-slate-950">
                                @endfor
                            </div>
                            <p class="text-[11px] text-gray-500 font-semibold tracking-wide uppercase">4.9/5 Avis</p>
                        </div>
                    </div>
                </div>

                <!-- Right: Clean Visual -->
                <div class="relative hidden lg:block" data-aos="fade-left">
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12">
                            <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&h=500&fit=crop" class="rounded-3xl shadow-xl w-full object-cover h-[400px]" alt="Hero Food">
                        </div>
                        <div class="col-span-6">
                            <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl shadow-lg border border-gray-100 dark:border-slate-800 flex items-center gap-4 -mt-12 relative z-20">
                                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-xl flex items-center justify-center text-green-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">Livraison Rapide</p>
                                    <p class="text-[11px] text-gray-500">Moyenne 25 min</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Categories - Balanced Pill Layout -->
    <section class="py-8 bg-gray-50 dark:bg-slate-900/50">
        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14">
            <div class="flex items-center gap-4 overflow-x-auto scrollbar-hide py-2">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap mr-2">Catégories :</span>
                @foreach($categories as $category)
                <a href="{{ route('explore', ['category' => $category->id_categorie]) }}" 
                   class="bg-white dark:bg-slate-800 px-5 py-2.5 rounded-xl border border-gray-200 dark:border-slate-700 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:border-red-500 hover:text-red-600 transition-all whitespace-nowrap shadow-sm active:scale-95">
                    {{ $category->nom_categorie }}
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 3. Featured Restaurants - Clean Grid -->
    <section class="py-20 px-4">
        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14">
            <div class="flex items-end justify-between mb-12">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Les meilleurs établissements</h2>
                    <p class="text-sm text-gray-500">Sélectionnés pour leur qualité et leur fiabilité.</p>
                </div>
                <a href="{{ route('explore') }}" class="text-red-600 text-sm font-bold flex items-center gap-1 hover:gap-2 transition-all">
                    Voir tout <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($vendeurs as $vendor)
                <div class="group bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-all overflow-hidden flex flex-col">
                    <div class="relative h-40 overflow-hidden">
                        <img src="{{ $vendor->image_principale ? asset('storage/' . $vendor->image_principale) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&fit=crop' }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-4 left-4 bg-white/95 backdrop-blur-sm px-2.5 py-1 rounded-lg shadow-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-yellow-500 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span class="text-xs font-bold text-slate-800">{{ number_format($vendor->note_moyenne, 1) }}</span>
                        </div>
                        @if($vendor->is_boosted)
                        <div class="absolute top-4 right-4 bg-red-600 text-white px-2.5 py-1 rounded-lg shadow-sm flex items-center gap-1.5">
                            <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97a1 1 0 00.95.69h4.18c.969 0 1.371 1.24.588 1.81l-3.39 2.46a1 1 0 00-.364 1.118l1.286 3.97a1 1 0 01-1.54 1.118l-3.39-2.46a1 1 0 00-1.175 0l-3.39 2.46a1 1 0 01-1.54-1.118l1.286-3.97a1 1 0 00-.364-1.118L2.34 9.397c-.783-.57-.38-1.81.588-1.81h4.18a1 1 0 00.951-.69l1.286-3.97z"/></svg>
                            <span class="text-[9px] font-bold uppercase tracking-wider">Mis en avant</span>
                        </div>
                        @endif
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-center gap-2 mb-2">
                            @foreach($vendor->categories->take(1) as $vcat)
                            <span class="text-[9px] text-red-600 font-bold uppercase tracking-tight">{{ $vcat->nom_categorie }}</span>
                            @endforeach
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2 line-clamp-1">{{ $vendor->nom_commercial }}</h3>
                        <p class="text-[11px] text-gray-500 line-clamp-2 mb-4 flex-1">{{ $vendor->description ?: 'Cliquez pour voir tout le catalogue.' }}</p>
                        
                        <div class="flex items-center justify-between pt-4 border-t border-gray-50 dark:border-slate-800">
                            <div class="flex items-center gap-2 text-gray-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span class="text-[10px] font-medium">{{ $vendor->zone ? $vendor->zone->nom : 'Lomé' }} @if(isset($vendor->distance)) ({{ number_format($vendor->distance, 1) }} km) @endif</span>
                            </div>
                            <a href="{{ route('vendor.show', ['id' => $vendor->id_vendeur, 'slug' => \Str::slug($vendor->nom_commercial)]) }}" 
                               class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-3 py-1.5 rounded-lg text-[10px] font-bold hover:bg-red-600 transition-colors">
                                Voir
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($vendeurs->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $vendeurs->appends(request()->query())->links('vendor.pagination.premium') }}
            </div>
            @endif
        </div>
    </section>

    <!-- 4. Trending Dishes - Simple List -->
    <section class="py-20 bg-gray-50 dark:bg-slate-900/50 px-4">
        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-10 text-center">Les articles les plus commandés</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
                @foreach($plats as $plat)
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-3 shadow-sm hover:shadow-md transition-all group">
                    <div class="aspect-square rounded-xl overflow-hidden mb-3 relative">
                        <img src="{{ $plat->image_principale ? asset('storage/' . $plat->image_principale) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&fit=crop' }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <button onclick="addCart({{ $plat->id_plat }})" class="absolute bottom-2 right-2 w-8 h-8 bg-red-600 text-white rounded-lg flex items-center justify-center shadow-lg hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate mb-1">{{ $plat->nom_plat }}</h4>
                    <p class="text-[10px] text-gray-500 mb-2 truncate">{{ $plat->vendeur->nom_commercial }}</p>
                    <p class="text-sm font-black text-red-600">{{ number_format($plat->prix, 0, ',', ' ') }} <span class="text-[10px] font-normal">F</span></p>
                </div>
                @endforeach
            </div>

            @if($plats->hasPages())
            <div class="mt-12 flex justify-center scale-75">
                {{ $plats->appends(request()->query())->links('vendor.pagination.premium') }}
            </div>
            @endif
        </div>
    </section>

    <!-- NEW: 4.5. How It Works -->
    <section class="py-24 px-4 bg-white dark:bg-slate-950">
        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-4">Comment ça marche ?</h2>
                <p class="text-sm text-gray-500 max-w-lg mx-auto">Commandez vos articles préférés en quelques clics seulement.</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-12">
                <!-- Step 1 -->
                <div class="text-center group">
                    <div class="w-20 h-20 bg-red-50 dark:bg-red-900/20 rounded-3xl flex items-center justify-center text-red-600 mx-auto mb-6 transition-transform group-hover:rotate-6">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-3">Recherche Facile</h3>
                    <p class="text-sm text-gray-500 leading-relaxed px-4">Trouvez les meilleures boutiques et articles près de chez vous en un instant.</p>
                </div>
                <!-- Step 2 -->
                <div class="text-center group">
                    <div class="w-20 h-20 bg-orange-50 dark:bg-orange-900/20 rounded-3xl flex items-center justify-center text-orange-600 mx-auto mb-6 transition-transform group-hover:-rotate-6">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-3">Commande Rapide</h3>
                    <p class="text-sm text-gray-500 leading-relaxed px-4">Sélectionnez vos options d'achat et validez votre panier en toute simplicité.</p>
                </div>
                <!-- Step 3 -->
                <div class="text-center group">
                    <div class="w-20 h-20 bg-green-50 dark:bg-green-900/20 rounded-3xl flex items-center justify-center text-green-600 mx-auto mb-6 transition-transform group-hover:scale-110">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-3">Livraison Express</h3>
                    <p class="text-sm text-gray-500 leading-relaxed px-4">Suivez votre commande en temps réel jusqu'à votre porte.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- NEW: 4.7. Features / Why Us -->
    <section class="py-24 px-4 bg-gray-50 dark:bg-slate-900/50">
        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1526367790999-0150786486a9?w=800&fit=crop" class="rounded-[2.5rem] shadow-2xl relative z-10" alt="Delivery Guy">
                    <div class="absolute -top-6 -left-6 w-32 h-32 bg-red-600 rounded-full mix-blend-multiply filter blur-2xl opacity-20"></div>
                </div>
                
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-8 leading-tight">Pourquoi choisir <span class="text-red-600">{{ config('app.name') }}</span> ?</h2>
                    <div class="space-y-8">
                        <div class="flex gap-5">
                            <div class="shrink-0 w-12 h-12 bg-white dark:bg-slate-800 rounded-xl shadow-sm flex items-center justify-center text-red-600 border border-gray-100 dark:border-slate-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Qualité Garantie</h4>
                                <p class="text-sm text-gray-500">Nous travaillons uniquement avec les établissements les mieux notés de votre région.</p>
                            </div>
                        </div>
                        <div class="flex gap-5">
                            <div class="shrink-0 w-12 h-12 bg-white dark:bg-slate-800 rounded-xl shadow-sm flex items-center justify-center text-red-600 border border-gray-100 dark:border-slate-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Prix Justes</h4>
                                <p class="text-sm text-gray-500">Bénéficiez des meilleurs tarifs et profitez régulièrement de promotions exclusives.</p>
                            </div>
                        </div>
                        <div class="flex gap-5">
                            <div class="shrink-0 w-12 h-12 bg-white dark:bg-slate-800 rounded-xl shadow-sm flex items-center justify-center text-red-600 border border-gray-100 dark:border-slate-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Support Dédié</h4>
                                <p class="text-sm text-gray-500">Notre équipe est à votre écoute 7j/7 pour vous garantir une expérience parfaite.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NEW: 4.9. Simple Testimonials -->
    <section class="py-24 px-4 bg-white dark:bg-slate-950">
        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-12 text-center">Ce que disent nos clients</h2>
            <div class="grid md:grid-cols-3 gap-8">
                @for($i=1; $i<=3; $i++)
                <div class="p-8 bg-gray-50 dark:bg-slate-900/50 rounded-3xl border border-gray-100 dark:border-slate-800">
                    <div class="flex items-center gap-1 text-yellow-500 mb-4">
                        @for($j=0; $j<5; $j++) <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 1L13 7L19 8L15 12L16 18L10 15L4 18L5 12L1 8L7 7L10 1Z"/></svg> @endfor
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 italic mb-6 leading-relaxed">
                        @if($i==1) "Le service est incroyablement rapide ! Les plats arrivent encore fumants et toujours bien présentés." @elseif($i==2) "J'ai découvert des restaurants locaux que je ne connaissais même pas. Une vraie pépite pour les papilles." @else "L'application est très intuitive et le paiement mobile est un vrai plus au quotidien." @endif
                    </p>
                    <div class="flex items-center gap-3">
                        <img src="https://i.pravatar.cc/100?u={{$i+10}}" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <p class="text-xs font-bold text-slate-900 dark:text-white">@if($i==1) Sarah M. @elseif($i==2) Koffi A. @else Marc T. @endif</p>
                            <p class="text-[10px] text-gray-500">Client fidèle</p>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </section>

    <!-- 5. Minimalist CTA -->
    <section class="py-24 px-4 bg-white dark:bg-slate-950">
        <div class="max-w-4xl mx-auto text-center px-8 py-16 bg-slate-900 dark:bg-white rounded-[2.5rem] relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-3xl font-bold text-white dark:text-slate-900 mb-6">Vous êtes un professionnel ?</h2>
                <p class="text-slate-400 dark:text-slate-500 mb-10 text-sm font-medium">Rejoignez la plateforme et boostez votre visibilité auprès des clients de votre région.</p>
                <div class="flex flex-wrap justify-center gap-4">
                    @auth
                        @if(auth()->user()->canApplyAsVendor())
                            <a href="{{ route('vendor.apply') }}" class="bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-8 py-3.5 rounded-xl font-bold text-sm hover:bg-red-600 hover:text-white transition-all shadow-lg">Devenir Partenaire</a>
                        @endif
                    @endauth
                    <a href="{{ route('explore') }}" class="bg-transparent text-white dark:text-slate-500 border border-white/20 dark:border-slate-800 px-8 py-3.5 rounded-xl font-bold text-sm hover:bg-white/5 transition-all">Découvrir les articles</a>
                </div>
            </div>
        </div>
    </section>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Vérifier si on a déjà une localisation dans l'URL
        const urlParams = new URLSearchParams(window.location.search);
        const hasLocation = urlParams.has('lat') && urlParams.has('lng');

        // Si pas de localisation dans l'URL mais présente en stockage
        if (!hasLocation) {
            const storedLat = localStorage.getItem('user_lat');
            const storedLng = localStorage.getItem('user_lng');

            if (storedLat && storedLng) {
                const latInput = document.getElementById('search_lat');
                const lngInput = document.getElementById('search_lng');
                if(latInput) latInput.value = storedLat;
                if(lngInput) lngInput.value = storedLng;
            }
        }
    });

    function detectLocation() {
        if (!navigator.geolocation) {
            window.showToast("La géolocalisation n'est pas supportée par votre navigateur.", 'error');
            return;
        }

        const btn = document.querySelector('button[onclick="detectLocation()"]');
        const icon = btn.innerHTML;
        btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                // Sauvegarder
                localStorage.setItem('user_lat', lat);
                localStorage.setItem('user_lng', lng);
                
                // Mettre à jour les inputs
                const latInput = document.getElementById('search_lat');
                const lngInput = document.getElementById('search_lng');
                if(latInput) latInput.value = lat;
                if(lngInput) lngInput.value = lng;

                // Rediriger vers la page des produits avec la localisation
                const searchUrl = new URL(window.location.origin + '/produits');
                searchUrl.searchParams.set('lat', lat);
                searchUrl.searchParams.set('lng', lng);
                window.location.href = searchUrl.toString();
            },
            (error) => {
                btn.innerHTML = icon;
                console.error("Erreur géolocalisation:", error);
                window.showToast("Impossible de vous localiser. Veuillez vérifier vos paramètres.", 'error');
            }
        );
    }
    
    function addCart(id) {
        fetch(`/panier/ajouter/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Dispatch event for AlpineJS to update cart count
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
                window.showToast(data.success, 'success');
            } else {
                window.showToast(data.error || 'Erreur lors de l\'ajout', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            window.showToast('Une erreur est survenue', 'error');
        });
    }
</script>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

@endsection