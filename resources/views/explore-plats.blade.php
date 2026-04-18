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
            <form action="{{ route('explore.plats') }}" method="GET" id="searchForm" class="mb-4" onsubmit="event.preventDefault(); window.ajaxPlatFilter(this)">
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
            <h2 class="text-[20px] leading-none font-black text-slate-900 dark:text-white uppercase tracking-tighter mb-1.5">Spécial pour vous</h2>
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest" id="mobile-plats-count">{{ $plats->total() }} Résultats trouvés</span>
        </section>

        <!-- 5. THE GRID -->
        <section class="px-6 pb-24" id="mobile-plats-list">
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

                    <div class="relative aspect-square mb-4">
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

window.fetchPlatsResults = (url) => {
    document.body.style.cursor = 'wait';
    
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const mCount = document.querySelector('#mobile-plats-count');
            const newMCount = doc.querySelector('#mobile-plats-count');
            if (mCount && newMCount) mCount.innerHTML = newMCount.innerHTML;

            const mList = document.querySelector('#mobile-plats-list');
            const newMList = doc.querySelector('#mobile-plats-list');
            if (mList && newMList) mList.innerHTML = newMList.innerHTML;

            const dList = document.querySelector('#desktop-plats-list');
            const newDList = doc.querySelector('#desktop-plats-list');
            if (dList && newDList) dList.innerHTML = newDList.innerHTML;

            const mPag = document.querySelector('#mobile-pagination');
            const newMPag = doc.querySelector('#mobile-pagination');
            if (mPag && newMPag) mPag.innerHTML = newMPag.innerHTML;
            
            document.body.style.cursor = 'default';
        })
        .catch(() => document.body.style.cursor = 'default');
};

window.debounceAjaxPlatSearch = (input) => {
    clearTimeout(window.platsAjaxDebounce);
    window.platsAjaxDebounce = setTimeout(() => {
        const form = input.closest('form');
        const url = new URL(form.action);
        
        const currentParams = new URLSearchParams(window.location.search);
        const params = new URLSearchParams(new FormData(form));
        if(currentParams.has('category') && !params.has('category')) params.set('category', currentParams.get('category'));
        
        url.search = params.toString();
        window.history.pushState({}, '', url);
        window.fetchPlatsResults(url);
    }, 300);
};

document.addEventListener('DOMContentLoaded', () => {
    document.body.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (link && link.href.includes('explore-plats') && !link.href.includes('vendor.show')) {
            e.preventDefault();
            window.history.pushState({}, '', link.href);
            window.fetchPlatsResults(link.href);
            
            fetch(link.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                   const parser = new DOMParser();
                   const doc = parser.parseFromString(html, 'text/html');
                   
                   const categoryTabs = document.querySelector('.flex.overflow-x-auto.gap-3');
                   const newCategoryTabs = doc.querySelector('.flex.overflow-x-auto.gap-3');
                   if(categoryTabs && newCategoryTabs) categoryTabs.innerHTML = newCategoryTabs.innerHTML;
                });
        }
    });

    window.addEventListener('popstate', () => {
        if(window.location.href.includes('explore-plats')) {
            window.fetchPlatsResults(window.location.href);
        }
    });
});
</script>
@endsection
