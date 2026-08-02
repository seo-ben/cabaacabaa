@extends('layouts.app')

@section('content')
<script src="{{ asset('js/product-options.js') }}"></script>

<div class="bg-white dark:bg-slate-950 min-h-screen transition-colors duration-300 pb-24" 
     x-data="productOptionsManager()">
    
    <!-- ================= MOBILE VIEW (RE-DESIGNED FROM IMAGE) ================= -->
    <main class="block lg:hidden font-sans bg-white dark:bg-slate-950 min-h-screen">

        <!-- 2. ORANGE SEARCH HERO -->
        <div class="bg-gradient-to-b from-[#EF5B2B] to-[#F1734A] rounded-b-2xl px-6 pt-4 pb-6 mb-8 border-t-4 border-white/10">
            <!-- Search Bar Area -->
            <form action="{{ route('explore.plats') }}" method="GET" id="searchForm" class="mb-4" onsubmit="event.preventDefault(); var inp = this.querySelector('input[name=search]'); if(inp) window.debounceAjaxPlatSearch(inp);">
                <div class="flex items-center gap-3">
                    <div class="flex-1 h-14 bg-white rounded-2xl flex items-center px-5 shadow-2xl shadow-black/10">
                        <svg class="w-5 h-5 text-orange-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Que recherchez-vous aujourd'hui ?" 
                               oninput="window.debounceAjaxPlatSearch(this)"
                               class="flex-1 bg-transparent border-none focus:ring-0 text-slate-900 font-bold placeholder-slate-400 text-xs outline-none">
                        
                        <button type="button" @click="mobileFiltersOpen = true" class="w-9 h-9 bg-orange-50 rounded-2xl flex items-center justify-center text-[#EF5B2B]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        </button>
                    </div>
                </div>
            </form>

            <!-- 3. CATEGORIES TABS -->
            <div class="flex overflow-x-auto gap-3 no-scrollbar pb-2">
                <a href="{{ route('explore.plats') }}" 
                   class="shrink-0 px-8 py-3 rounded-2xl text-[10px] font-black tracking-widest transition-all {{ !request('category') ? 'bg-[#1A1C2E] text-white shadow-xl translate-y-[-2px]' : 'bg-white text-slate-400' }}">
                    Tout
                </a>
                @foreach($categories as $cat)
                <a href="{{ route('explore.plats', array_merge(request()->query(), ['category' => $cat->id_categorie])) }}" 
                   class="shrink-0 px-8 py-3 rounded-2xl text-[10px] font-black tracking-widest transition-all {{ request('category') == $cat->id_categorie ? 'bg-[#1A1C2E] text-white shadow-xl translate-y-[-2px]' : 'bg-white text-slate-400' }}">
                    {{ Str::upper($cat->nom_categorie) }}
                </a>
                @endforeach
            </div>
        </div>

        <!-- 4. SECTION LABEL -->
        <section class="px-6 mb-8">
            <h2 class="text-[20px] leading-none font-black text-slate-900 dark:text-white  tracking-tighter mb-1.5">Spécial pour vous</h2>
            <span class="text-[9px] font-bold text-slate-400 tracking-widest" id="mobile-plats-count">{{ $plats->total() }} Résultats trouvés</span>
        </section>

        <!-- 5. THE GRID -->
        <section class="px-6" id="mobile-plats-list">
            <div class="grid grid-cols-2 gap-x-5 gap-y-10">
                @foreach($plats as $plat)
                <div class="flex flex-col">
                    <!-- Image Card -->
                    @php
                        $hasOptions = $plat->groupesVariantes->isNotEmpty();
                        $hasMedia = ($plat->medias && $plat->medias->count() > 0) || !empty($plat->video_url);
                        $canShowMedia = $plat->vendeur->canUseGallery(); // Subscription-gated
                        $opensModal = $hasOptions || ($hasMedia && $canShowMedia);
                    @endphp

                    <div class="relative aspect-square ">
                        <div class="w-full h-full rounded-2xl overflow-hidden bg-[#F4F5F7] dark:bg-slate-900 border border-black/[0.03] dark:border-white/[0.05] shadow-sm relative group cursor-pointer" 
                             @click="{{ $opensModal ? 'openModal(' . Js::from($plat) . ')' : 'addCart(' . $plat->id_plat . ')' }}">
                            <img src="{{ $plat->image_principale ? asset('storage/' . $plat->image_principale) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&fit=crop' }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            
                            <!-- Promo Badge -->
                            @if($plat->en_promotion)
                            <div class="absolute top-5 left-5">
                                <span class="px-2.5 py-1 bg-[#EF5B2B] text-white text-[9px] font-black rounded-lg shadow-lg">-20%</span>
                            </div>
                            @endif
                        </div>

                        <!-- Floating '+' Button (Conditional Action) -->
                        <button @click.stop="{{ $opensModal ? 'openModal(' . Js::from($plat) . ')' : 'addCart(' . $plat->id_plat . ')' }}"
                                class="absolute bottom-3 right-3 w-10 h-10 bg-[#EF5B2B] text-white rounded-full shadow-lg shadow-orange-500/30 flex items-center justify-center transform active:scale-90 transition-transform z-10 border-2 border-white dark:border-slate-900">
                            <svg class="w-5 h-5 stroke-[3.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>
                        </button>
                    </div>

                    <!-- Metadata (Matches layout & typography of the image) -->
                    <div class="px-1.5 space-y-0.5 mt-2">
                        <span class="text-[8px] font-black text-[#EF5B2B] uppercase tracking-widest">{{ $plat->categorie ? $plat->categorie->nom_categorie : 'Produit' }}</span>
                        <h4 class="text-[14px] font-black text-slate-900 dark:text-white leading-tight truncate">{{ $plat->nom_plat }}</h4>
                        
                        <div class="flex items-center gap-1 text-[10px] font-bold text-slate-400">
                            <svg class="w-3 h-3 text-slate-300 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                            <a href="{{ route('vendor.show', ['id' => $plat->vendeur->id_vendeur, 'slug' => \Str::slug($plat->vendeur->nom_commercial ?? 'boutique')]) }}" class="uppercase truncate tracking-wide whitespace-nowrap hover:text-[#EF5B2B] transition-colors cursor-pointer">{{ Str::limit($plat->vendeur->nom_commercial ?? 'Boutique', 15) }}</a>
                        </div>

                        <div class="flex items-baseline gap-2 pt-1">
                            <span class="text-[16px] font-black text-[#EF5B2B]">
                                {{ number_format($plat->en_promotion ? $plat->prix_promotion : $plat->prix, 0, ',', ' ') }} F
                            </span>
                            @if($plat->en_promotion)
                            <span class="text-[10px] font-medium text-slate-300 line-through">{{ number_format($plat->prix, 0, ',', ' ') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div id="mobile-pagination">
                @if($plats->hasPages())
                    <div class="mt-8">
                        {{ $plats->appends(request()->query())->links('vendor.pagination.premium') }}
                    </div>
                @endif
            </div>
        </section>
    </main>


    <!-- ================= DESKTOP VIEW ================= -->
    <div class="hidden lg:block max-w-[1920px] mx-auto px-10 py-12">

        <div class="grid grid-cols-4 gap-12" id="desktop-plats-list">
            @foreach($plats as $plat)
            <div class="flex flex-col group">
                <!-- Image Area -->
                <div class="relative aspect-square mb-6">
                    <div class="w-full h-full rounded-2xl overflow-hidden bg-white shadow-2xl border border-slate-50 dark:border-slate-800 transition-transform duration-700 group-hover:scale-[1.03] relative">
                        <img src="{{ $plat->image_principale ? asset('storage/' . $plat->image_principale) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&fit=crop' }}" 
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>

                    <!-- Promotion Badge -->
                    @if($plat->en_promotion)
                    <div class="absolute top-8 left-8">
                        <span class="px-3.5 py-1.5 bg-[#EF5B2B] text-white text-[11px] font-black rounded-xl shadow-xl shadow-orange-500/20">-20%</span>
                    </div>
                    @endif

                    <!-- Floating '+' Button (Desktop) -->
                    @php
                        $hasOptions = $plat->groupesVariantes->isNotEmpty();
                        $hasMedia = ($plat->medias && $plat->medias->count() > 0) || !empty($plat->video_url);
                        $canShowMedia = $plat->vendeur->canUseGallery(); 
                        $opensModal = $hasOptions || ($hasMedia && $canShowMedia);
                    @endphp
                    <button @click.stop="{{ $opensModal ? 'openModal(' . Js::from($plat) . ')' : 'addCart(' . $plat->id_plat . ')' }}"
                            class="absolute bottom-8 right-8 w-14 h-14 bg-[#EF5B2B] text-white rounded-2xl shadow-2xl flex items-center justify-center opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 hover:scale-110 active:scale-95 z-20">
                        <svg class="w-8 h-8 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>
                    </button>
                </div>

                <!-- Info Area -->
                <div class="px-4 space-y-2">
                    <span class="text-[11px] font-black text-[#EF5B2B] uppercase tracking-[0.2em]">{{ $plat->categorie ? $plat->categorie->nom_categorie : 'Produit' }}</span>
                    <h4 class="text-xl font-black text-slate-900 dark:text-white group-hover:text-[#EF5B2B] transition-colors leading-tight truncate">
                        {{ $plat->nom_plat }}
                    </h4>
                    
                    <div class="flex items-center gap-2 text-[12px] font-bold text-slate-400">
                        <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                        <a href="{{ route('vendor.show', ['id' => $plat->vendeur->id_vendeur, 'slug' => \Str::slug($plat->vendeur->nom_commercial ?? 'boutique')]) }}" class="uppercase tracking-widest hover:text-[#EF5B2B] transition-colors cursor-pointer relative z-30">{{ $plat->vendeur->nom_commercial }}</a>
                    </div>

                    <div class="flex items-baseline gap-3 pt-2">
                        <span class="text-2xl font-black text-[#EF5B2B]">
                            {{ number_format($plat->en_promotion ? $plat->prix_promotion : $plat->prix, 0, ',', ' ') }} F
                        </span>
                        @if($plat->en_promotion)
                        <span class="text-sm font-medium text-slate-300 line-through">{{ number_format($plat->prix, 0, ',', ' ') }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Product Options Modal (Persistence) -->
    <div x-show="modalOpen" class="fixed inset-0 z-[110] overflow-y-auto" role="dialog" aria-modal="true" x-cloak>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" @click="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <!-- Modal Panel -->
            <div x-show="modalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 class="relative inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full border border-white/5">
                
                <template x-if="selectedPlat">
                    <div class="flex flex-col max-h-[90vh]">
                        
                        <!-- Media Slider / Gallery -->
                        <div class="relative bg-slate-100 dark:bg-slate-950 aspect-square overflow-hidden">
                            <template x-for="(img, idx) in allImages" :key="idx">
                                <div x-show="currentImageIndex === idx" class="absolute inset-0 w-full h-full">
                                    <template x-if="img.type === 'image'">
                                        <img :src="img.url" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="img.type === 'video'">
                                        <div class="w-full h-full bg-black">
                                            <iframe :src="'https://www.youtube.com/embed/' + (img.url.includes('v=') ? img.url.split('v=')[1].split('&')[0] : img.url.split('/').pop()) + '?autoplay=1&mute=1'" 
                                                    class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            
                            <!-- Close Button -->
                            <button @click="closeModal()" class="absolute top-6 right-6 p-4 bg-white/20 backdrop-blur-xl rounded-full text-white border border-white/20 hover:bg-white/40 transition-all z-30 shadow-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>

                            <!-- Navigation Controls -->
                            <div x-show="allImages.length > 1" class="absolute inset-x-6 bottom-8 flex items-center justify-between z-20">
                                <button @click="prevImage()" class="p-4 bg-white/10 backdrop-blur-xl rounded-full text-white border border-white/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <div class="flex gap-2">
                                    <template x-for="(d, i) in allImages" :key="i">
                                        <div :class="currentImageIndex === i ? 'w-8 bg-white' : 'w-2 bg-white/40'" class="h-1.5 rounded-full transition-all duration-300"></div>
                                    </template>
                                </div>
                                <button @click="nextImage()" class="p-4 bg-white/10 backdrop-blur-xl rounded-full text-white border border-white/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Content Info -->
                        <div class="p-8 overflow-y-auto">
                            <div class="flex justify-between items-start mb-6">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[10px] font-black text-[#EF5B2B] uppercase tracking-[0.2em]" x-text="selectedPlat.categorie ? selectedPlat.categorie.nom_categorie : 'Produit'"></span>
                                        <span class="w-1.5 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full"></span>
                                        <a :href="'/vendor/' + selectedPlat.vendeur.id_vendeur + '-' + (selectedPlat.vendeur.nom_commercial ? selectedPlat.vendeur.nom_commercial.toLowerCase().replace(/[^a-z0-9]+/g, '-') : 'boutique')" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] hover:text-[#EF5B2B] transition-colors cursor-pointer" x-text="selectedPlat.vendeur.nom_commercial"></a>
                                    </div>
                                    <h3 class="text-3xl font-black text-slate-900 dark:text-white leading-tight" x-text="selectedPlat.nom_plat"></h3>
                                </div>
                                <div class="text-right">
                                    <div class="text-[28px] font-black text-slate-900 dark:text-white" x-text="formatPrice(calculateTotal()) + ' F'"></div>
                                </div>
                            </div>

                            <!-- Groups Selection -->
                            <div class="space-y-10 pb-24">
                                <template x-for="group in selectedPlat.groupes_variantes" :key="group.id_groupe">
                                    <div>
                                        <div class="flex items-center justify-between mb-6">
                                            <h4 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest" x-text="group.nom"></h4>
                                            <span x-show="group.obligatoire" class="px-3 py-1 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-[10px] font-black rounded-lg uppercase">Obligatoire</span>
                                        </div>
                                        <div class="grid grid-cols-1 gap-3">
                                            <template x-for="option in group.variantes" :key="option.id_variante">
                                                <div @click="group.choix_multiple ? toggleOption(group, option) : selectSingleOption(group, option)" 
                                                     class="group/item flex items-center justify-between p-5 rounded-2xl border-2 transition-all cursor-pointer"
                                                     :class="isSelected(group, option) ? 'border-[#EF5B2B] bg-orange-50/50 dark:bg-orange-900/10' : 'border-slate-50 dark:border-slate-800 hover:border-slate-200 dark:hover:border-slate-700'">
                                                    <div class="flex items-center gap-4">
                                                        <div class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all"
                                                             :class="isSelected(group, option) ? 'bg-[#EF5B2B] border-[#EF5B2B] text-white' : 'border-slate-200 dark:border-slate-700'">
                                                            <svg x-show="isSelected(group, option)" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                        </div>
                                                        <span class="font-bold text-slate-700 dark:text-slate-300" x-text="option.nom"></span>
                                                    </div>
                                                    <span x-show="option.prix_supplement > 0" class="text-xs font-black text-[#EF5B2B]" x-text="'+' + formatPrice(option.prix_supplement)"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Add to Cart Footer -->
                        <div class="absolute bottom-0 inset-x-0 p-8 pt-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-lg">
                            <button @click="confirmAddToCart()" 
                                    :disabled="!isValidSelection()"
                                    class="w-full py-6 bg-[#EF5B2B] text-white rounded-2xl text-xs font-black uppercase tracking-[0.3em] shadow-2xl shadow-orange-500/30 active:scale-95 transition-all disabled:opacity-50 disabled:grayscale">
                                Ajouter au panier
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<script>
window.platsAjaxDebounce = null;
window._currentAjaxController = null; // AbortController for cancelling stale requests

/**
 * Format a number with spaces as thousands separator (e.g. 1 500)
 */
window.formatPlatPrice = (n) => {
    return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
};

/**
 * Build a single mobile product card HTML from a JSON plat object
 */
window.buildMobileCard = (p) => {
    const imgSrc = p.image_principale || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&fit=crop';
    const displayPrice = p.en_promotion && p.prix_promotion ? p.prix_promotion : p.prix;
    const vendorUrl = p.vendeur ? `/vendor/${p.vendeur.id_vendeur}-${p.vendeur.slug}` : '#';
    const vendorName = p.vendeur ? (p.vendeur.nom_commercial || 'Boutique') : 'Boutique';
    const vendorNameShort = vendorName.length > 15 ? vendorName.substring(0, 15) + '…' : vendorName;
    const platJsonAttr = JSON.stringify(p.plat_json).replace(/"/g, '&quot;');

    // Action: open modal or add to cart
    const clickAction = p.opens_modal
        ? `openModal(${JSON.stringify(p.plat_json).replace(/'/g, "\\'")})`
        : `addCart(${p.id_plat})`;

    return `
    <div class="flex flex-col">
        <div class="relative aspect-square mb-4">
            <div class="w-full h-full rounded-2xl overflow-hidden bg-[#F4F5F7] dark:bg-slate-900 border border-black/[0.03] dark:border-white/[0.05] shadow-sm relative group cursor-pointer"
                 @click="${clickAction}">
                <img src="${imgSrc}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy">
                ${p.en_promotion ? `<div class="absolute top-5 left-5"><span class="px-2.5 py-1 bg-[#EF5B2B] text-white text-[9px] font-black rounded-lg shadow-lg">-20%</span></div>` : ''}
            </div>
            <button @click.stop="${clickAction}"
                    class="absolute bottom-3 right-3 w-10 h-10 bg-[#EF5B2B] text-white rounded-full shadow-lg shadow-orange-500/30 flex items-center justify-center transform active:scale-90 transition-transform z-10 border-2 border-white dark:border-slate-900">
                <svg class="w-5 h-5 stroke-[3.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>
            </button>
        </div>
        <div class="px-1.5 space-y-0.5 mt-2">
            <span class="text-[8px] font-black text-[#EF5B2B] uppercase tracking-widest">${p.categorie_nom}</span>
            <h4 class="text-[14px] font-black text-slate-900 dark:text-white leading-tight truncate">${p.nom_plat}</h4>
            <div class="flex items-center gap-1 text-[10px] font-bold text-slate-400">
                <svg class="w-3 h-3 text-slate-300 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                <a href="${vendorUrl}" class="uppercase truncate tracking-wide whitespace-nowrap hover:text-[#EF5B2B] transition-colors cursor-pointer">${vendorNameShort}</a>
            </div>
            <div class="flex items-baseline gap-2 pt-1">
                <span class="text-[16px] font-black text-[#EF5B2B]">${formatPlatPrice(displayPrice)} F</span>
                ${p.en_promotion ? `<span class="text-[10px] font-medium text-slate-300 line-through">${formatPlatPrice(p.prix)} F</span>` : ''}
            </div>
        </div>
    </div>`;
};

/**
 * Build a single desktop product card HTML from a JSON plat object
 */
window.buildDesktopCard = (p) => {
    const imgSrc = p.image_principale || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&fit=crop';
    const displayPrice = p.en_promotion && p.prix_promotion ? p.prix_promotion : p.prix;
    const vendorUrl = p.vendeur ? `/vendor/${p.vendeur.id_vendeur}-${p.vendeur.slug}` : '#';
    const vendorName = p.vendeur ? (p.vendeur.nom_commercial || 'Boutique') : 'Boutique';

    const clickAction = p.opens_modal
        ? `openModal(${JSON.stringify(p.plat_json).replace(/'/g, "\\'")})`
        : `addCart(${p.id_plat})`;

    return `
    <div class="flex flex-col group">
        <div class="relative aspect-square mb-6">
            <div class="w-full h-full rounded-2xl overflow-hidden bg-white shadow-2xl border border-slate-50 dark:border-slate-800 transition-transform duration-700 group-hover:scale-[1.03] relative">
                <img src="${imgSrc}" class="w-full h-full object-cover" loading="lazy">
                <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>
            ${p.en_promotion ? `<div class="absolute top-8 left-8"><span class="px-3.5 py-1.5 bg-[#EF5B2B] text-white text-[11px] font-black rounded-xl shadow-xl shadow-orange-500/20">-20%</span></div>` : ''}
            <button @click.stop="${clickAction}"
                    class="absolute bottom-8 right-8 w-14 h-14 bg-[#EF5B2B] text-white rounded-2xl shadow-2xl flex items-center justify-center opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 hover:scale-110 active:scale-95 z-20">
                <svg class="w-8 h-8 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>
            </button>
        </div>
        <div class="px-4 space-y-2">
            <span class="text-[11px] font-black text-[#EF5B2B] uppercase tracking-[0.2em]">${p.categorie_nom}</span>
            <h4 class="text-xl font-black text-slate-900 dark:text-white group-hover:text-[#EF5B2B] transition-colors leading-tight truncate">${p.nom_plat}</h4>
            <div class="flex items-center gap-2 text-[12px] font-bold text-slate-400">
                <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                <a href="${vendorUrl}" class="uppercase tracking-widest hover:text-[#EF5B2B] transition-colors cursor-pointer relative z-30">${vendorName}</a>
            </div>
            <div class="flex items-baseline gap-3 pt-2">
                <span class="text-2xl font-black text-[#EF5B2B]">${formatPlatPrice(displayPrice)} F</span>
                ${p.en_promotion ? `<span class="text-sm font-medium text-slate-300 line-through">${formatPlatPrice(p.prix)} F</span>` : ''}
            </div>
        </div>
    </div>`;
};

/**
 * Build pagination HTML from JSON metadata
 */
window.buildPaginationHtml = (meta) => {
    if (meta.last_page <= 1) return '';

    let html = '<div class="mt-8"><nav class="flex items-center justify-center gap-2">';

    // Previous button
    if (meta.prev_page_url) {
        html += `<a href="${meta.prev_page_url}" data-ajax-page class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-[#EF5B2B] hover:text-white hover:border-[#EF5B2B] transition-all">&laquo;</a>`;
    }

    // Page numbers
    for (let i = 1; i <= meta.last_page; i++) {
        const isActive = i === meta.current_page;
        if (isActive) {
            html += `<span class="px-4 py-2 bg-[#EF5B2B] text-white rounded-xl text-sm font-black shadow-lg shadow-orange-500/20">${i}</span>`;
        } else {
            // Show first, last, current ± 1, and use dots for the rest
            if (i === 1 || i === meta.last_page || Math.abs(i - meta.current_page) <= 1) {
                const pageUrl = new URL(window.location.href);
                pageUrl.searchParams.set('page', i);
                html += `<a href="${pageUrl}" data-ajax-page class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-[#EF5B2B] hover:text-white hover:border-[#EF5B2B] transition-all">${i}</a>`;
            } else if (Math.abs(i - meta.current_page) === 2) {
                html += `<span class="px-2 text-slate-400">…</span>`;
            }
        }
    }

    // Next button
    if (meta.next_page_url) {
        html += `<a href="${meta.next_page_url}" data-ajax-page class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-[#EF5B2B] hover:text-white hover:border-[#EF5B2B] transition-all">&raquo;</a>`;
    }

    html += '</nav></div>';
    return html;
};

/**
 * Core AJAX fetch — sends request to JSON endpoint, renders results.
 */
window.fetchPlatsJson = (url) => {
    // Cancel any previous in-flight request
    if (window._currentAjaxController) window._currentAjaxController.abort();
    window._currentAjaxController = new AbortController();

    // Loading indicator
    const mobileList = document.querySelector('#mobile-plats-list');
    const desktopList = document.querySelector('#desktop-plats-list');
    if (mobileList) mobileList.style.opacity = '0.5';
    if (desktopList) desktopList.style.opacity = '0.5';

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        signal: window._currentAjaxController.signal
    })
    .then(res => {
        if (!res.ok) throw new Error('Network error');
        return res.json();
    })
    .then(data => {
        // ── Update result count ──
        const mCount = document.querySelector('#mobile-plats-count');
        if (mCount) mCount.textContent = data.total + ' Résultats trouvés';

        // ── Render mobile cards ──
        if (mobileList) {
            const grid = mobileList.querySelector('.grid');
            if (grid) {
                grid.innerHTML = data.plats.length
                    ? data.plats.map(p => buildMobileCard(p)).join('')
                    : '<div class="col-span-2 text-center py-16"><p class="text-slate-400 font-bold text-sm">Aucun produit trouvé</p></div>';
            }
            mobileList.style.opacity = '1';
        }

        // ── Render desktop cards ──
        if (desktopList) {
            desktopList.innerHTML = data.plats.length
                ? data.plats.map(p => buildDesktopCard(p)).join('')
                : '<div class="col-span-4 text-center py-24"><p class="text-slate-400 font-bold text-lg">Aucun produit trouvé</p></div>';
            desktopList.style.opacity = '1';
        }

        // ── Render pagination ──
        const mobilePag = document.querySelector('#mobile-pagination');
        if (mobilePag) mobilePag.innerHTML = buildPaginationHtml(data);
    })
    .catch(err => {
        if (err.name === 'AbortError') return; // Cancelled — ignore
        console.error('AJAX plats error:', err);
        if (mobileList) mobileList.style.opacity = '1';
        if (desktopList) desktopList.style.opacity = '1';
    });
};

/**
 * Debounced search handler — called on every keystroke in the search input.
 */
window.debounceAjaxPlatSearch = (input) => {
    clearTimeout(window.platsAjaxDebounce);
    window.platsAjaxDebounce = setTimeout(() => {
        const searchVal = input.value.trim();
        const url = new URL(window.location.href);

        if (searchVal) {
            url.searchParams.set('search', searchVal);
        } else {
            url.searchParams.delete('search');
        }
        url.searchParams.delete('page'); // Reset to page 1 on new search

        window.history.replaceState({}, '', url);
        window.fetchPlatsJson(url.toString());
    }, 300);
};

/**
 * DOMContentLoaded — intercept category tabs, pagination links, and popstate.
 */
document.addEventListener('DOMContentLoaded', () => {

    // ── Intercept ALL clicks on category tabs and pagination links ──
    document.body.addEventListener('click', (e) => {
        // Pagination links (data-ajax-page)
        const pageLink = e.target.closest('[data-ajax-page]');
        if (pageLink) {
            e.preventDefault();
            window.history.pushState({}, '', pageLink.href);
            window.fetchPlatsJson(pageLink.href);
            return;
        }

        // Category tab links (only explore-plats links, NOT vendor links)
        const link = e.target.closest('a');
        if (link && link.href && link.href.includes('/produits') && !link.closest('[data-no-ajax]')) {
            // Exclude vendor links
            if (link.href.includes('/vendor/')) return;
            e.preventDefault();
            window.history.pushState({}, '', link.href);
            window.fetchPlatsJson(link.href);

            // Update active state on category tabs visually
            const allTabs = document.querySelectorAll('.flex.overflow-x-auto.gap-3 a');
            const clickedUrl = new URL(link.href);
            const clickedCategory = clickedUrl.searchParams.get('category');

            allTabs.forEach(tab => {
                const tabUrl = new URL(tab.href);
                const tabCategory = tabUrl.searchParams.get('category');
                const isActive = clickedCategory === tabCategory;

                tab.className = tab.className
                    .replace(/bg-\[#1A1C2E\] text-white shadow-xl translate-y-\[-2px\]/g, '')
                    .replace(/bg-white text-slate-400/g, '')
                    .trim();

                if (isActive) {
                    tab.classList.add('bg-[#1A1C2E]', 'text-white', 'shadow-xl', 'translate-y-[-2px]');
                } else {
                    tab.classList.add('bg-white', 'text-slate-400');
                }
            });
        }
    });

    // ── Handle browser back/forward ──
    window.addEventListener('popstate', () => {
        if (window.location.href.includes('/produits') || window.location.href.includes('explore-plats')) {
            window.fetchPlatsJson(window.location.href);
        }
    });

    // ── Prevent form submission (search form) ──
    const searchForms = document.querySelectorAll('#searchForm');
    searchForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const input = form.querySelector('input[name="search"]');
            if (input) window.debounceAjaxPlatSearch(input);
        });
    });
});
</script>
@endsection
