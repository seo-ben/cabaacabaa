@extends('layouts.app')

@section('content')
<script src="{{ asset('js/product-options.js') }}"></script>
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 transition-colors duration-300" 
     x-data="productOptionsManager()">
    
    <!-- Closed State Modal -->
    @if(!$vendeur->actif)
    <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-2xl text-center space-y-6 animate-in fade-in zoom-in duration-300">
            <div class="w-20 h-20 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center mx-auto">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Actuellement Fermé</h2>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Ce vendeur a terminé son service pour le moment. Revenez pendant les heures d'ouverture.
                </p>
            </div>
            <a href="{{ route('explore') }}" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 dark:bg-slate-700 text-white rounded-xl font-semibold hover:bg-slate-800 dark:hover:bg-slate-600 transition-all">
                Découvrir d'autres vendeurs
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
    @endif

    <!-- Hero Section -->
    <div class="relative bg-slate-900 overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0">
            @if($vendeur->image_principale)
                <img src="{{ asset('storage/' . $vendeur->image_principale) }}" 
                     class="w-full h-full object-cover opacity-40" 
                     alt="{{ $vendeur->nom_commercial }}">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/80 to-slate-900/40"></div>
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-slate-800 to-slate-900"></div>
            @endif
        </div>

        <!-- Hero Content -->
        <div class="relative max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14 py-16">
            <div class="flex flex-col lg:flex-row items-start lg:items-end gap-8">
                <!-- Logo/Avatar -->
                <div class="flex-shrink-0">
                    <div class="w-32 h-32 lg:w-40 lg:h-40 rounded-xl overflow-hidden bg-white dark:bg-slate-800 shadow-2xl border-4 border-white/20">
                        @if($vendeur->image_principale)
                            <img src="{{ asset('storage/' . $vendeur->image_principale) }}" 
                                 class="w-full h-full object-cover" 
                                 alt="{{ $vendeur->nom_commercial }}">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                                <span class="text-5xl font-bold text-white">{{ substr($vendeur->nom_commercial, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Vendor Info -->
                <div class="flex-1 text-white">
                    <!-- Categories & Badges -->
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        @foreach($vendeur->categories as $cat)
                            <span class="px-3 py-1 bg-white/10 backdrop-blur-sm rounded-lg text-xs font-semibold border border-white/20">
                                {{ $cat->nom_categorie }}
                            </span>
                        @endforeach
                        @if($vendeur->statut_verification === 'verifie')
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-500 rounded-lg text-xs font-semibold">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Vérifié
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 {{ $isOpen ? 'bg-green-500/20 border-green-500/30' : 'bg-red-500/20 border-red-500/30' }} backdrop-blur-sm rounded-lg text-xs font-semibold border">
                            <span class="w-2 h-2 rounded-full {{ $isOpen ? 'bg-green-400' : 'bg-red-400' }} animate-pulse"></span>
                            {{ $isOpen ? 'Ouvert' : 'Fermé' }}
                        </span>
                    </div>

                    <!-- Name -->
                    <h1 class="text-4xl lg:text-5xl font-bold mb-4">{{ $vendeur->nom_commercial }}</h1>

                    <!-- Rating & Location -->
                    <div class="flex flex-wrap items-center gap-6 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="flex gap-1">
                                @for($i=1;$i<=5;$i++)
                                    <svg class="w-5 h-5 {{ $i <= floor($ratingMoyen) ? 'text-yellow-400 fill-current' : 'text-slate-600' }}" 
                                         viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="font-semibold">{{ number_format($ratingMoyen, 1) }}</span>
                            <span class="text-slate-300">({{ $avisCount }} avis)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ $vendeur->zone ? ($vendeur->zone->nom ?? $vendeur->zone->nom_zone) : 'Lomé' }}</span>
                        </div>
                    </div>
                </div>

                <!-- CTA Button -->
                <div class="flex-shrink-0 hidden lg:block">
                    <button class="px-8 py-4 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Commander maintenant
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="sticky top-0 z-40 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14">
            <nav class="flex gap-8 overflow-x-auto scrollbar-hide py-4">
                <a href="#menu" class="text-sm font-semibold text-orange-600 border-b-2 border-orange-600 pb-4 whitespace-nowrap">
                    Catalogue
                </a>
                <a href="#about" class="text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white pb-4 whitespace-nowrap transition-colors">
                    À propos
                </a>
                <a href="#gallery" class="text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white pb-4 whitespace-nowrap transition-colors">
                    Galerie
                </a>
                <a href="#reviews" class="text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white pb-4 whitespace-nowrap transition-colors">
                    Avis ({{ $avisCount }})
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14 py-12">
        
        <!-- Promotions Section -->
        @if($vendeur->coupons->count() > 0)
        <div class="mb-12">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Promotions en cours</h2>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">Utilisez ces codes lors de votre commande</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($vendeur->coupons as $coupon)
                <div class="group relative bg-white dark:bg-slate-800 p-6 rounded-[2rem] border-2 border-dashed border-red-200 dark:border-red-900/50 hover:border-red-500 transition-all overflow-hidden">
                    <!-- Background Decoration -->
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-50 dark:bg-red-900/20 rounded-full blur-2xl group-hover:bg-red-100 transition-colors"></div>
                    
                    <div class="relative flex items-center justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-baseline gap-1 mb-1">
                                <span class="text-3xl font-black text-red-600">
                                    {{ $coupon->type === 'pourcentage' ? $coupon->valeur . '%' : number_format($coupon->valeur, 0, ',', ' ') . ' F' }}
                                </span>
                                <span class="text-xs font-black text-slate-400 uppercase tracking-widest">OFFERT</span>
                            </div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-4">
                                Dès {{ number_format($coupon->montant_minimal, 0, ',', ' ') }} F d'achat
                            </p>
                            
                            <div class="flex items-center gap-3">
                                <div class="px-4 py-2 bg-slate-100 dark:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-600">
                                    <code class="text-sm font-black text-slate-900 dark:text-white tracking-widest uppercase">{{ $coupon->code }}</code>
                                </div>
                                <button onclick="copyToClipboard('{{ $coupon->code }}')" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Copier le code">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Menu & Content -->
            <div class="lg:col-span-2 space-y-12">
                
                <!-- Products Section -->
                <section id="menu" class="scroll-mt-20">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Nos Articles</h2>
                        <p class="text-slate-600 dark:text-slate-400">{{ $vendeur->plats->count() }} articles disponibles</p>
                    </div>

                    @if($vendeur->plats->count() > 0)
                        @foreach($vendeur->plats->groupBy('id_categorie') as $catId => $plats)
                            @php $category = $vendeur->categories->firstWhere('id_categorie', $catId); @endphp
                            <div class="mb-12">
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6 pb-3 border-b border-slate-200 dark:border-slate-800">
                                    {{ $category->nom_categorie ?? 'Autres' }}
                                </h3>
                                
                                <div class="space-y-6">
                                    @foreach($plats as $plat)
                                        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-lg transition-all group">
                                            <div class="flex flex-col sm:flex-row gap-4 p-5">
                                                <!-- Image -->
                                                <div class="relative w-full sm:w-40 h-40 flex-shrink-0 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-700">
                                                    @if($plat->image_principale)
                                                        <img src="{{ asset('storage/' . $plat->image_principale) }}" 
                                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" 
                                                             alt="{{ $plat->nom_plat }}">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center">
                                                            <svg class="w-12 h-12 text-slate-300 dark:text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    @if($plat->en_promotion)
                                                        <div class="absolute top-2 left-2 px-2 py-1 bg-red-600 text-white text-xs font-bold rounded-lg">
                                                            -{{ round((($plat->prix - $plat->prix_promotion)/$plat->prix)*100) }}%
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Content -->
                                                <div class="flex-1 flex flex-col justify-between min-w-0">
                                                    <div>
                                                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-1 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                                                            {{ $plat->nom_plat }}
                                                        </h4>
                                                        @if($plat->description)
                                                            <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 mb-3">
                                                                {{ $plat->description }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="flex items-center justify-between gap-4">
                                                        <div class="flex items-baseline gap-2">
                                                            @if($plat->en_promotion)
                                                                <span class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                                                                    {{ number_format($plat->prix_promotion, 0) }} FCFA
                                                                </span>
                                                                <span class="text-sm text-slate-400 dark:text-slate-500 line-through">
                                                                    {{ number_format($plat->prix, 0) }} FCFA
                                                                </span>
                                                            @else
                                                                <span class="text-2xl font-bold text-slate-900 dark:text-white">
                                                                    {{ number_format($plat->prix, 0) }} FCFA
                                                                </span>
                                                            @endif
                                                        </div>
                                                        
                                                        @php
                                                            $hasOptions = $plat->groupesVariantes->isNotEmpty();
                                                        @endphp
                                                        <button type="button" 
                                                                @if($hasOptions)
                                                                    @click="openModal({{ Js::from([
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
                                                                @else
                                                                    @click="addCart({{ $plat->id_plat }})"
                                                                @endif
                                                                class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-semibold transition-all flex items-center gap-2 shadow-sm">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                            </svg>
                                                            {{ $hasOptions ? 'Choisir' : 'Ajouter' }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="py-20 bg-slate-50 dark:bg-slate-800/50 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-700 text-center">
                            <div class="w-16 h-16 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-sm border border-slate-100 dark:border-slate-700">
                                <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Catalogue en préparation</h3>
                            <p class="text-slate-600 dark:text-slate-400">Revenez bientôt pour découvrir nos articles !</p>
                        </div>
                    @endif
                </section>

                <!-- Gallery Section -->
                @if(!empty($vendeur->images_galerie) && count($vendeur->images_galerie) > 0)
                <section id="gallery" class="scroll-mt-20">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Galerie Photo</h2>
                        <p class="text-slate-600 dark:text-slate-400">Découvrez notre établissement</p>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($vendeur->images_galerie as $index => $img)
                            <div class="relative aspect-square rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 group cursor-pointer {{ $index === 0 ? 'md:col-span-2 md:row-span-2' : '' }}">
                                <img src="{{ asset('storage/' . $img) }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                                     alt="Galerie {{ $index + 1 }}">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300"></div>
                            </div>
                        @endforeach
                    </div>
                </section>
                @endif

                <!-- About Section -->
                <section id="about" class="scroll-mt-20">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">À Propos</h2>
                    </div>
                    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-8 space-y-8">
                        @if($vendeur->description)
                            <p class="text-slate-700 dark:text-slate-300 leading-relaxed text-lg">
                                {{ $vendeur->description }}
                            </p>
                        @else
                            <p class="text-slate-700 dark:text-slate-300 leading-relaxed text-lg">
                                Bienvenue chez {{ $vendeur->nom_commercial }}. Nous nous engageons à vous offrir le meilleur service avec des articles sélectionnés et un savoir-faire authentique.
                            </p>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-6 border-t border-slate-100 dark:border-slate-700">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-orange-50 dark:bg-orange-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white mb-1">Qualité garantie</h4>
                                    <p class="text-sm text-slate-600 dark:text-slate-400">Articles sélectionnés avec soin</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white mb-1">Livraison rapide</h4>
                                    <p class="text-sm text-slate-600 dark:text-slate-400">Service express disponible</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Reviews Section -->
                <section id="reviews" class="scroll-mt-20">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Avis Clients</h2>
                            <p class="text-slate-600 dark:text-slate-400">Ce que nos clients disent de nous</p>
                        </div>
                        <button class="px-4 py-2 border-2 border-slate-200 dark:border-slate-700 hover:border-orange-500 dark:hover:border-orange-500 hover:text-orange-600 dark:hover:text-orange-500 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-300 transition-all">
                            Laisser un avis
                        </button>
                    </div>

                    <div class="space-y-6">
                        @foreach($avis as $a)
                            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 space-y-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-600 rounded-xl flex items-center justify-center font-bold text-slate-600 dark:text-slate-300">
                                            {{ substr($a->client->name ?? 'A', 0, 1) }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 dark:text-white">{{ $a->client->name ?? 'Client' }}</h4>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $a->date_publication->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 px-3 py-1 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                        <svg class="w-4 h-4 text-yellow-500 fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="text-sm font-bold text-yellow-700 dark:text-yellow-500">{{ $a->note }}</span>
                                    </div>
                                </div>
                                <p class="text-slate-700 dark:text-slate-300 leading-relaxed">{{ $a->commentaire }}</p>
                            </div>
                        @endforeach
                        
                        @if($avis->isEmpty())
                            <div class="py-16 text-center">
                                <p class="text-slate-500 dark:text-slate-400">Aucun avis pour le moment. Soyez le premier à donner votre avis !</p>
                            </div>
                        @endif
                    </div>
                </section>
            </div>

            <!-- Right Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-6">
                    
                    <!-- Contact Card -->
                    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Informations</h3>
                        
                        <div class="space-y-6">
                            <!-- Address -->
                            <div class="flex gap-4">
                                <div class="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Adresse</p>
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                                        {{ $vendeur->adresse_complete ?: ($vendeur->zone ? $vendeur->zone->nom : 'Lomé') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="flex gap-4">
                                <div class="w-10 h-10 bg-orange-50 dark:bg-orange-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Téléphone</p>
                                    @php
                                        $vTel = $vendeur->contacts->where('telephone_principal', '!=', null)->first()?->telephone_principal ?? $vendeur->telephone_commercial;
                                    @endphp
                                    <a href="tel:{{ $vTel }}" class="text-sm font-bold text-slate-900 dark:text-white hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                                        {{ $vTel ?: 'Non renseigné' }}
                                    </a>
                                </div>
                            </div>

                            <!-- Social Links -->
                            @if($vendeur->facebook_url || $vendeur->instagram_url || $vendeur->whatsapp_number || $vendeur->website_url)
                            <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-3">Suivez-nous</p>
                                <div class="flex flex-wrap gap-2">
                                    @if($vendeur->facebook_url)
                                        <a href="{{ $vendeur->facebook_url }}" target="_blank" 
                                           class="w-10 h-10 bg-slate-100 dark:bg-slate-700 hover:bg-blue-600 rounded-xl flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-white transition-all">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if($vendeur->instagram_url)
                                        <a href="{{ $vendeur->instagram_url }}" target="_blank" 
                                           class="w-10 h-10 bg-slate-100 dark:bg-slate-700 hover:bg-pink-600 rounded-xl flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-white transition-all">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.791-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.209-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if($vendeur->whatsapp_number)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $vendeur->whatsapp_number) }}" target="_blank" 
                                           class="w-10 h-10 bg-slate-100 dark:bg-slate-700 hover:bg-green-600 rounded-xl flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-white transition-all">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if($vendeur->website_url)
                                        <a href="{{ $vendeur->website_url }}" target="_blank" 
                                           class="w-10 h-10 bg-slate-100 dark:bg-slate-700 hover:bg-slate-900 rounded-xl flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-white transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Opening Hours Card -->
                    @if($vendeur->horaires->count() > 0)
                    <div class="bg-slate-900 dark:bg-black text-white rounded-xl p-6 shadow-lg">
                        <div class="flex items-center gap-2 mb-6">
                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="text-lg font-bold">Heures d'ouverture</h3>
                        </div>
                        <div class="space-y-3">
                            @php $jours = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche']; @endphp
                            @foreach($vendeur->horaires->sortBy('jour_semaine') as $h)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-slate-300 font-medium">{{ $jours[$h->jour_semaine-1] ?? $jours[0] }}</span>
                                    <span class="font-semibold {{ $h->ferme ? 'text-red-400' : 'text-white' }}">
                                        @if($h->ferme) 
                                            Fermé 
                                        @else 
                                            {{ substr($h->heure_ouverture,0,5) }} - {{ substr($h->heure_fermeture,0,5) }}
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 space-y-3">
                        <button class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-700 dark:text-white rounded-xl font-semibold transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                            Partager
                        </button>
                        <button class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700 hover:bg-red-50 dark:hover:bg-red-900/20 text-slate-700 dark:text-white hover:text-red-600 dark:hover:text-red-400 rounded-xl font-semibold transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            Ajouter aux favoris
                        </button>
                    </div>

                    <!-- Security Badge -->
                    <div class="bg-gradient-to-br from-blue-600 to-blue-700 text-white rounded-xl p-6 shadow-lg">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold mb-1">Paiement sécurisé</h4>
                                <p class="text-sm text-blue-100 leading-relaxed">
                                    Transactions protégées. Paiement à la livraison ou Mobile Money.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .scroll-mt-20 {
        scroll-margin-top: 5rem;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

    <!-- Product Options Modal -->
    <div x-show="modalOpen" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;" x-cloak>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-[2.5rem] text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-100 dark:border-slate-700">
                
                <template x-if="selectedPlat">
                    <div class="flex flex-col max-h-[85vh]">
                        <!-- Modal Header -->
                        <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 z-10 flex justify-between items-center sticky top-0">
                            <div>
                                <h3 class="text-xl font-black text-slate-900 dark:text-white" x-text="selectedPlat.nom_plat"></h3>
                                <p class="text-sm font-bold text-orange-600 dark:text-orange-400 mt-1">
                                    <span x-text="formatPrice(calculateTotal())"></span> FCFA
                                </p>
                            </div>
                            <button @click="closeModal()" class="p-2 bg-slate-50 dark:bg-slate-700 rounded-full hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Scrollable Content -->
                        <div class="flex-1 overflow-y-auto p-8 space-y-8">
                            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed font-medium" x-text="selectedPlat.description"></p>

                            <!-- Variant Groups -->
                            <template x-for="group in selectedPlat.groupes_variantes" :key="group.id_groupe">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest" x-text="group.nom"></h4>
                                        <div class="flex gap-2">
                                            <span x-show="group.obligatoire" class="px-2 py-0.5 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-[9px] font-black uppercase rounded">Obligatoire</span>
                                            <span x-show="group.choix_multiple" class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-[9px] font-black uppercase rounded">
                                                Max <span x-text="group.max_choix"></span>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <template x-for="option in group.variantes" :key="option.id_variante">
                                            <div class="flex flex-col gap-2 p-4 rounded-xl border-2 transition-all"
                                                 :class="isSelected(group, option) ? 'border-orange-600 bg-orange-50/50 dark:bg-orange-900/10' : 'border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800'">
                                                 
                                                 <div class="flex items-center justify-between">
                                                     <label class="flex items-center gap-3 cursor-pointer flex-1">
                                                         <!-- Checkbox for multiple -->
                                                         <input x-show="group.choix_multiple" 
                                                                type="checkbox" 
                                                                :name="'group_' + group.id_groupe" 
                                                                :value="option.id_variante"
                                                                @change="toggleOption(group, option)"
                                                                :checked="isSelected(group, option)"
                                                                class="w-5 h-5 text-orange-600 rounded focus:ring-orange-500 bg-slate-100 border-slate-300 dark:bg-slate-700 dark:border-slate-600">
                                                         
                                                         <!-- Radio for single -->
                                                         <input x-show="!group.choix_multiple" 
                                                                type="radio" 
                                                                :name="'group_' + group.id_groupe" 
                                                                :value="option.id_variante"
                                                                @change="selectSingleOption(group, option)"
                                                                :checked="isSelected(group, option)"
                                                                class="w-5 h-5 text-orange-600 focus:ring-orange-500 bg-slate-100 border-slate-300 dark:bg-slate-700 dark:border-slate-600">
                                                         
                                                         <span class="text-sm font-bold text-slate-900 dark:text-white" x-text="option.nom"></span>
                                                     </label>
                                                     
                                                     <span x-show="option.prix_supplement > 0" class="text-xs font-black text-slate-500 dark:text-slate-400">
                                                         +<span x-text="formatPrice(option.prix_supplement)"></span>
                                                     </span>
                                                 </div>

                                                 <!-- Quantity Controls for this option -->
                                                 <template x-if="isSelected(group, option)">
                                                     <div class="flex items-center justify-between mt-2 pt-2 border-t border-orange-100 dark:border-orange-900/30">
                                                         <p class="text-[9px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest">Quantité</p>
                                                         <div class="flex items-center gap-3 bg-white dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-orange-100 dark:border-orange-900/30">
                                                             <button @click="decrementOption(group, option)" class="text-slate-400 hover:text-orange-600 transition-colors">
                                                                 <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                                             </button>
                                                             <span class="text-xs font-black text-slate-900 dark:text-white w-4 text-center" x-text="getOptionQuantity(group, option)"></span>
                                                             <button @click="incrementOption(group, option)" class="text-slate-400 hover:text-orange-600 transition-colors">
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
                        <div class="p-6 bg-white dark:bg-slate-800 border-t border-slate-50 dark:border-slate-700 sticky bottom-0 z-10 w-full">
                            <button @click="confirmAddToCart()" 
                                    :disabled="!isValidSelection()"
                                    class="w-full py-4 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-black dark:hover:bg-slate-200 shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                Ajouter à la commande
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Code promo copié : ' + text);
        });
    }
</script>
@endsection