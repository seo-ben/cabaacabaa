@extends('layouts.app')

@section('title', 'Trouvez votre repas en quelques clics')

@section('content')

<!-- Hero Section - Premium & Moderne -->
<section class="relative bg-gradient-to-b from-gray-50 via-white to-white dark:from-gray-950 dark:via-gray-900 dark:to-gray-950 pt-16 pb-24 overflow-hidden transition-colors duration-300">
    <!-- Decorative Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 -right-48 w-96 h-96 bg-red-100 dark:bg-red-900/20 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute top-1/3 -left-48 w-96 h-96 bg-orange-100 dark:bg-orange-900/20 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
    </div>

    <div class="relative max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14">
        <div class="text-center max-w-5xl mx-auto">
            <!-- Badge animé -->
            <div class="inline-flex items-center gap-3 px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full shadow-sm mb-8 hover:shadow-md transition-all">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                </span>
                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $vendorsCount ?? '150' }}+ vendeurs actifs maintenant</span>
            </div>

            <!-- Titre principal -->
            <h1 class="text-6xl md:text-7xl lg:text-8xl tracking-tight text-gray-900 dark:text-white mb-8 leading-tight">
                Votre repas,
                <br class="hidden sm:block">
                <span class="relative inline-block">
                    <span class="relative z-10 bg-gradient-to-r from-red-600 via-orange-500 to-red-600 bg-clip-text text-transparent">
                        en quelques clics
                    </span>
                    <svg class="absolute -bottom-2 left-0 w-full h-3 text-red-200 dark:text-red-900/50" viewBox="0 0 200 12" preserveAspectRatio="none">
                        <path d="M0,7 Q50,0 100,7 T200,7" fill="none" stroke="currentColor" stroke-width="3"/>
                    </svg>
                </span>
            </h1>
            
            <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400 mb-12 max-w-3xl mx-auto leading-relaxed">
                Les meilleurs vendeurs de Lomé à portée de main. Commandez maintenant, récupérez en 20 minutes.
            </p>

            <!-- Search Bar Ultra Moderne -->
            <div class="max-w-3xl mx-auto mb-12" x-data="{ location: '', focused: false }">
                <form action="{{-- route('explore') --}}" method="GET" class="relative">
                    <div class="relative group">
                        <!-- Glow effect -->
                        <div class="absolute -inset-1 bg-gradient-to-r from-red-600 to-orange-600 rounded-3xl opacity-0 group-hover:opacity-20 blur transition-opacity"></div>
                        
                        <div class="relative flex items-center bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 focus-within:border-red-500 dark:focus-within:border-red-500 focus-within:shadow-2xl">
                            <!-- Location Icon -->
                            <div class="pl-6 pr-3">
                                <svg class="w-6 h-6 text-gray-400 dark:text-gray-500 transition-colors" :class="focused && 'text-red-600 dark:text-red-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>

                            <!-- Input -->
                            <input type="text"
                                   name="location"
                                   x-model="location"
                                   @focus="focused = true"
                                   @blur="focused = false"
                                   placeholder="Entrez votre adresse ou quartier..."
                                   class="flex-1 py-6 bg-transparent border-0 focus:ring-0 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 text-lg">

                            <!-- Divider -->
                            <div class="h-8 w-px bg-gray-200 dark:bg-gray-700"></div>

                            <!-- Search Button -->
                            <button type="submit" class="m-2 px-8 py-4 bg-gradient-to-r from-red-600 to-orange-600 text-white rounded-xl hover:from-red-700 hover:to-orange-700 transition-all transform hover:scale-105 shadow-md hover:shadow-lg flex items-center gap-2 group">
                                <svg class="w-5 h-5 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <span class="hidden md:inline">Rechercher</span>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Quick Suggestions -->
                <div class="flex flex-wrap items-center justify-center gap-3 mt-6">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Populaire :</span>
                    <a href="{{-- route('explore', ['location' => 'tokoin']) --}}" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-red-300 dark:hover:border-red-900 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-full text-sm text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition-all">
                        📍 Tokoin
                    </a>
                    <a href="{{-- route('explore', ['location' => 'bè']) --}}" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-red-300 dark:hover:border-red-900 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-full text-sm text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition-all">
                        📍 Bè
                    </a>
                    <a href="{{-- route('explore', ['location' => 'nyekonakpoe']) --}}" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-red-300 dark:hover:border-red-900 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-full text-sm text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition-all">
                        📍 Nyekonakpoe
                    </a>
                </div>
            </div>

            <!-- Social Proof & Features -->
            <div class="flex flex-col md:flex-row items-center justify-center gap-6 md:gap-12">
                <!-- Rating -->
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1">
                        @for($i = 0; $i < 5; $i++)
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        @endfor
                    </div>
                    <div>
                        <div class="text-sm text-gray-900 dark:text-white">4.8/5</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">2,400+ avis</div>
                    </div>
                </div>

                <!-- Separator -->
                <div class="hidden md:block w-px h-12 bg-gray-200 dark:bg-gray-800"></div>

                <!-- Speed -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-900 dark:text-white">Rapide</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Prêt en 15-30 min</div>
                    </div>
                </div>

                <!-- Separator -->
                <div class="hidden md:block w-px h-12 bg-gray-200 dark:bg-gray-800"></div>

                <!-- Free -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-900 dark:text-white">Sans frais</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">0% commission</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Wave -->
    <div class="absolute bottom-0 left-0 right-0 h-24 pointer-events-none">
        <svg class="absolute bottom-0 w-full h-24 text-gray-50 dark:text-gray-900" viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path fill="currentColor" d="M0,32 C480,96 960,0 1440,32 L1440,120 L0,120 Z"/>
        </svg>
    </div>
</section>

<style>
@keyframes blob {
    0%, 100% { transform: translate(0, 0) scale(1); }
    25% { transform: translate(20px, -20px) scale(1.1); }
    50% { transform: translate(0, 20px) scale(0.9); }
    75% { transform: translate(-20px, -10px) scale(1.05); }
}

.animate-blob {
    animation: blob 8s ease-in-out infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}
</style>

<!-- Vendeurs Populaires -->
<section class="py-20 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14">
        <div class="flex items-end justify-between mb-12">
            <div>
                <h2 class="text-3xl text-gray-900 dark:text-white mb-2">Populaires près de vous</h2>
                <p class="text-gray-600 dark:text-gray-400">Les mieux notés du moment</p>
            </div>
            <a href="{{-- route('explore') --}}" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 flex items-center gap-2 group">
                <span>Voir tout</span>
                <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($popularVendors ?? [] as $vendor)
            <a href="{{-- route('vendor.show', $vendor->slug) --}}" class="group bg-white dark:bg-gray-800 rounded-3xl overflow-hidden hover:shadow-xl dark:hover:shadow-gray-900/50 transition-all duration-300">
                <!-- Image -->
                <div class="relative h-52 overflow-hidden bg-gray-100 dark:bg-gray-700">
                    <img src="{{ $vendor->cover_image ?? 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400' }}"
                         alt="{{ $vendor->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    
                    @if($vendor->is_open)
                        <span class="absolute top-4 right-4 px-3 py-1.5 bg-green-500 text-white text-xs rounded-full backdrop-blur-sm bg-opacity-90 shadow-lg">
                            Ouvert
                        </span>
                    @endif
                </div>

                <!-- Content -->
                <div class="p-5">
                    <h3 class="text-lg text-gray-900 dark:text-white mb-1 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                        {{ $vendor->name }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3 line-clamp-1">{{ $vendor->description }}</p>

                    <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-sm text-gray-900 dark:text-white">{{ number_format($vendor->rating, 1) }}</span>
                            <span class="text-sm text-gray-400 dark:text-gray-500">({{ $vendor->reviews_count }})</span>
                        </div>

                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $vendor->distance ?? '1.2' }} km</span>
                    </div>
                </div>
            </a>
            @empty
            <!-- Skeleton Loading -->
            @for($i = 0; $i < 4; $i++)
            <div class="bg-white dark:bg-gray-800 rounded-3xl overflow-hidden animate-pulse">
                <div class="h-52 bg-gray-200 dark:bg-gray-700"></div>
                <div class="p-5">
                    <div class="h-5 bg-gray-200 dark:bg-gray-700 rounded mb-2 w-3/4"></div>
                    <div class="h-4 bg-gray-100 dark:bg-gray-800 rounded mb-3"></div>
                    <div class="flex justify-between pt-3 border-t border-gray-100 dark:border-gray-700">
                        <div class="h-4 bg-gray-100 dark:bg-gray-800 rounded w-20"></div>
                        <div class="h-4 bg-gray-100 dark:bg-gray-800 rounded w-16"></div>
                    </div>
                </div>
            </div>
            @endfor
            @endforelse
        </div>
    </div>
</section>

<!-- Comment ça marche -->
<section class="py-20 bg-white dark:bg-gray-950 transition-colors duration-300">
    <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14">
        <div class="text-center mb-16">
            <h2 class="text-3xl text-gray-900 dark:text-white mb-3">Simple et rapide</h2>
            <p class="text-gray-600 dark:text-gray-400">En 3 étapes seulement</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 max-w-5xl mx-auto">
            <!-- Step 1 -->
            <div class="text-center group">
                <div class="mx-auto w-16 h-16 bg-red-100 dark:bg-red-900/20 rounded-2xl flex items-center justify-center mb-6 transition-transform group-hover:scale-110">
                    <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg text-gray-900 dark:text-white mb-2">Recherchez</h3>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Trouvez les meilleurs vendeurs près de vous</p>
            </div>

            <!-- Step 2 -->
            <div class="text-center group">
                <div class="mx-auto w-16 h-16 bg-orange-100 dark:bg-orange-900/20 rounded-2xl flex items-center justify-center mb-6 transition-transform group-hover:scale-110">
                    <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg text-gray-900 dark:text-white mb-2">Commandez</h3>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Choisissez vos plats favoris en quelques clics</p>
            </div>

            <!-- Step 3 -->
            <div class="text-center group">
                <div class="mx-auto w-16 h-16 bg-green-100 dark:bg-green-900/20 rounded-2xl flex items-center justify-center mb-6 transition-transform group-hover:scale-110">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg text-gray-900 dark:text-white mb-2">Récupérez</h3>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Votre commande vous attend, prête à emporter</p>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="py-20 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14">
        <div class="text-center mb-12">
            <h2 class="text-3xl text-gray-900 dark:text-white mb-3">Explorez par catégorie</h2>
            <p class="text-gray-600 dark:text-gray-400">Qu'avez-vous envie de manger ?</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @php
            $categories = [
                ['name' => 'Plats locaux', 'icon' => '🍲', 'color' => 'from-red-500 to-orange-500'],
                ['name' => 'Fast-food', 'icon' => '🍔', 'color' => 'from-orange-500 to-yellow-500'],
                ['name' => 'Pizza', 'icon' => '🍕', 'color' => 'from-yellow-500 to-red-500'],
                ['name' => 'Boissons', 'icon' => '🥤', 'color' => 'from-blue-500 to-cyan-500'],
                ['name' => 'Desserts', 'icon' => '🍰', 'color' => 'from-pink-500 to-purple-500'],
                ['name' => 'Petit-déj', 'icon' => '🥐', 'color' => 'from-amber-500 to-orange-500'],
            ];
            @endphp

            @foreach($categories as $category)
            <a href="{{-- route('explore', ['category' => Str::slug($category['name'])]) --}}"
               class="group p-6 bg-white dark:bg-gray-800 hover:bg-gradient-to-br hover:{{ $category['color'] }} rounded-2xl transition-all duration-300 text-center hover:shadow-lg hover:-translate-y-1 border border-transparent dark:border-gray-700 hover:border-transparent">
                <div class="text-4xl mb-2 transform group-hover:scale-110 transition-transform">{{ $category['icon'] }}</div>
                <div class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-white transition-colors">{{ $category['name'] }}</div>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Vendeur CTA -->
<section class="py-20 bg-gradient-to-br from-gray-900 to-gray-800 dark:from-black dark:to-gray-900 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-block p-3 bg-white/10 backdrop-blur-sm rounded-2xl mb-6 shadow-xl">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        
        <h2 class="text-4xl mb-4 font-bold">Vous êtes vendeur ?</h2>
        <p class="text-xl text-gray-300 mb-8 leading-relaxed">
            Rejoignez notre plateforme et développez votre activité
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{-- route('vendor.apply') --}}" class="px-8 py-4 bg-white text-gray-900 rounded-xl hover:bg-gray-100 transition-colors font-bold shadow-lg">
                Devenir vendeur
            </a>
            <a href="{{-- route('how-it-works') --}}" class="px-8 py-4 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl hover:bg-white/20 transition-colors font-bold">
                En savoir plus
            </a>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-20 bg-white dark:bg-gray-950 transition-colors duration-300">
    <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14">
        <div class="text-center mb-12">
            <h2 class="text-3xl text-gray-900 dark:text-white mb-3">Ils nous font confiance</h2>
            <p class="text-gray-600 dark:text-gray-400">L'avis de nos utilisateurs</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @for($i = 0; $i < 3; $i++)
            <div class="bg-gray-50 dark:bg-gray-900 p-8 rounded-2xl border border-transparent dark:border-gray-800">
                <div class="flex items-center gap-1 mb-4">
                    @for($j = 0; $j < 5; $j++)
                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endfor
                </div>
                <p class="text-gray-700 dark:text-gray-300 mb-6 leading-relaxed italic">
                    "Application très pratique ! Je gagne du temps pendant ma pause déjeuner. Les vendeurs sont pros et la nourriture toujours bonne."
                </p>
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name=Kofi+Mensah&background=ef4444&color=fff" class="w-10 h-10 rounded-full" alt="Client">
                    <div>
                        <div class="text-sm text-gray-900 dark:text-white font-bold">Kofi Mensah</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Client depuis 6 mois</div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="py-20 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl text-gray-900 dark:text-white mb-3">Questions fréquentes</h2>
            <p class="text-gray-600 dark:text-gray-400">Tout ce que vous devez savoir</p>
        </div>

        <div class="space-y-3" x-data="{ open: null }">
            @php
            $faqs = [
                [
                    'question' => 'Comment passer une commande ?',
                    'answer' => 'Recherchez un vendeur près de vous, parcourez son menu, ajoutez les plats à votre panier, puis validez votre commande. Vous recevrez une confirmation avec l\'heure de retrait.'
                ],
                [
                    'question' => 'Comment fonctionne le paiement ?',
                    'answer' => 'Vous payez directement chez le vendeur lors de la récupération de votre commande, soit en espèces, soit via mobile money.'
                ],
                [
                    'question' => 'Y a-t-il des frais de service ?',
                    'answer' => 'Non, notre plateforme est gratuite pour les clients. Vous payez uniquement le prix des plats commandés.'
                ],
                [
                    'question' => 'Puis-je annuler ma commande ?',
                    'answer' => 'Oui, vous pouvez annuler gratuitement jusqu\'à 15 minutes après avoir passé commande.'
                ]
            ];
            @endphp

            @foreach($faqs as $index => $faq)
            <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 transition-colors">
                <button @click="open = open === {{ $index }} ? null : {{ $index }}"
                        class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <span class="text-gray-900 dark:text-white font-medium">{{ $faq['question'] }}</span>
                    <svg :class="open === {{ $index }} ? 'rotate-180' : ''"
                         class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-transform"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === {{ $index }}"
                     x-collapse
                     class="px-6 pb-4 text-gray-600 dark:text-gray-300 leading-relaxed border-t border-gray-100 dark:border-gray-700/50 pt-4">
                    {{ $faq['answer'] }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Final CTA -->
<section class="py-20 bg-red-600 dark:bg-red-700 transition-colors">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl text-white mb-4 font-bold">
            Prêt à commander ?
        </h2>
        <p class="text-xl text-red-100 mb-8">
            Rejoignez des milliers d'utilisateurs satisfaits
        </p>
        <a href="{{-- route('explore') --}}"
           class="inline-flex items-center gap-2 px-8 py-4 bg-white text-red-600 dark:text-red-700 rounded-xl hover:bg-gray-100 transition-colors text-lg font-bold shadow-lg">
            Découvrir les vendeurs
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </a>
    </div>
</section>

@endsection