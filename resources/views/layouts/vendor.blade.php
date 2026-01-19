<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php
        $siteName = \App\Models\AppSetting::get('site_name', config('app.name', 'FoodConnect'));
        $siteLogo = \App\Models\AppSetting::get('site_logo');
        $siteLogoUrl = \App\Models\AppSetting::get('site_logo_url');
        $siteFavicon = \App\Models\AppSetting::get('site_favicon');
        $siteFaviconUrl = \App\Models\AppSetting::get('site_favicon_url');
        
        $finalLogo = $siteLogo ? asset('storage/' . $siteLogo) : ($siteLogoUrl ?: null);
        $finalFavicon = $siteFavicon ? asset('storage/' . $siteFavicon) : ($siteFaviconUrl ?: null);
    @endphp

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Vendeur Dashboard') - {{ $siteName }}</title>
    <meta name="description" content="{{ \App\Models\AppSetting::get('meta_description') }}">
    <meta name="keywords" content="{{ \App\Models\AppSetting::get('meta_keywords') }}">

    @if($finalFavicon)
        <link rel="icon" type="image/x-icon" href="{{ $finalFavicon }}">
    @endif

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- AlpineJS -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Vite Assets (Echo, etc) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        .sidebar-active {
            background: linear-gradient(to right, rgba(239, 68, 68, 0.1), transparent);
            border-left: 4px solid #ef4444;
            color: #ef4444 !important;
        }
        .dark .sidebar-active {
            background: linear-gradient(to right, rgba(239, 68, 68, 0.2), transparent);
        }

        /* Real-time Order Alert Animation */
        @keyframes alert-pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 20px rgba(239, 68, 68, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        .new-order-alert {
            animation: alert-pulse 2s infinite;
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-950 font-sans text-gray-900 dark:text-gray-100 transition-colors duration-300" 
      x-data="{ 
          sidebarOpen: true,
          darkMode: localStorage.getItem('darkMode') === 'true',
          newOrder: null,
          toggleDarkMode() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('darkMode', this.darkMode);
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          },
          init() {
              if (window.Echo) {
                  window.Echo.private('vendeur.{{ Auth::user()->vendeur->id_vendeur }}')
                      .listen('.order.placed', (e) => {
                          this.newOrder = e.order;
                          this.playNotificationSound();
                          // Optionnel: Recharger la liste si on est sur la page des commandes
                          if (window.location.pathname.includes('/vendeur/commandes')) {
                              // On pourrait faire un refresh partiel ou total
                              // window.location.reload(); 
                          }
                      });
              }
          },
          playNotificationSound() {
              const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
              audio.play().catch(e => console.log('Audio autoplay blocked'));
          }
      }">

    <!-- New Order Popup Alert -->
    <template x-if="newOrder">
        <div class="fixed top-8 left-1/2 -translate-x-1/2 z-[100] w-full max-w-md px-4">
            <div class="bg-gray-900 text-white p-6 rounded-[2.5rem] shadow-2xl new-order-alert flex items-center gap-6 border border-white/10">
                <div class="w-16 h-16 bg-red-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-8 h-8 text-white animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <div class="flex-1">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-red-500 mb-1">Nouvelle Commande !</p>
                    <h4 class="text-lg font-black tracking-tight" x-text="'#' + newOrder.numero"></h4>
                    <p class="text-xs font-bold text-gray-400 mt-1" x-text="newOrder.client"></p>
                </div>
                <div class="flex flex-col gap-2">
                    <a href="{{ vendor_route('vendeur.slug.orders.index') }}" class="px-4 py-2 bg-white text-gray-900 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-100 transition-colors">Voir</a>
                    <button @click="newOrder = null" class="text-[10px] font-black uppercase tracking-widest text-gray-500 hover:text-white transition-colors">Fermer</button>
                </div>
            </div>
        </div>
    </template>

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 transition-transform duration-300 transform lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        
        <div class="flex flex-col h-full">
            <!-- Brand -->
            <div class="px-8 py-10 flex items-center gap-3">
                @if($finalLogo)
                    <img src="{{ $finalLogo }}" alt="{{ $siteName }}" class="w-10 h-10 object-contain">
                @else
                    <div class="w-10 h-10 bg-gradient-to-br from-red-600 to-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-red-200 dark:shadow-red-900/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                @endif
                <span class="text-2xl font-display font-black tracking-tighter bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent truncate">{{ $siteName }}</span>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 space-y-2">
                <p class="px-4 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-4">Général</p>
                
                <a href="{{ vendor_route('vendeur.slug.dashboard') }}" 
                   class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-gray-500 dark:text-gray-400 transition-all hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white {{ request()->routeIs('vendeur.slug.dashboard') || request()->routeIs('vendeur.dashboard') ? 'sidebar-active' : '' }}">
                   <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                   Tableau de bord
                </a>

                <a href="{{ vendor_route('vendeur.slug.orders.index') }}" 
                   class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-gray-500 dark:text-gray-400 transition-all hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white {{ request()->routeIs('vendeur.slug.orders.*') || request()->routeIs('vendeur.orders.*') ? 'sidebar-active' : '' }}">
                   <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                   Commandes
                   @if(isset($activeOrders) && $activeOrders > 0)
                    <span class="ml-auto px-2 py-0.5 bg-red-600 text-white text-[10px] rounded-lg">{{ $activeOrders }}</span>
                   @endif
                </a>

                <a href="{{ vendor_route('vendeur.slug.coupons.index') }}" 
                   class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-gray-500 dark:text-gray-400 transition-all hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white {{ request()->routeIs('vendeur.slug.coupons.*') || request()->routeIs('vendeur.coupons.*') ? 'sidebar-active' : '' }}">
                   <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                   Coupons & Promos
                </a>

                <a href="{{ vendor_route('vendeur.slug.payouts.index') }}" 
                   class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-gray-500 dark:text-gray-400 transition-all hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white {{ request()->routeIs('vendeur.slug.payouts.*') || request()->routeIs('vendeur.payouts.*') ? 'sidebar-active' : '' }}">
                   <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                   Portefeuille
                </a>

                <a href="{{ vendor_route('vendeur.slug.plats.index') }}" 
                   class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-gray-500 dark:text-gray-400 transition-all hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white {{ request()->routeIs('vendeur.slug.plats.*') || request()->routeIs('vendeur.plats.*') ? 'sidebar-active' : '' }}">
                   <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                   Produits & Menu
                </a>

                <p class="px-4 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mt-10 mb-4">Configuration</p>

                <a href="{{ vendor_route('vendeur.slug.settings.index') }}" 
                   class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-gray-500 dark:text-gray-400 transition-all hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white {{ request()->routeIs('vendeur.slug.settings.*') || request()->routeIs('vendeur.settings.*') ? 'sidebar-active' : '' }}">
                   <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                   Boutique
                </a>

                @php
                    $currentVendor = request()->get('current_vendor') ?? (Auth::user()->vendeur ?? null);
                    $isOwner = $currentVendor && Auth::id() === $currentVendor->id_user;
                @endphp

                @if($isOwner || Auth::user()->role === 'super_admin')
                <a href="{{ vendor_route('vendeur.slug.team.index') }}" 
                   class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-gray-500 dark:text-gray-400 transition-all hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white {{ request()->routeIs('vendeur.slug.team.*') ? 'sidebar-active' : '' }}">
                   <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                   Équipe
                </a>
                @endif
            </nav>

            <!-- User Footer -->
            <div class="p-6 border-t border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-4 p-4 rounded-3xl bg-gray-50 dark:bg-gray-800/50">
                    <div class="w-10 h-10 rounded-2xl bg-white dark:bg-gray-700 flex items-center justify-center font-black text-xs text-red-600 shadow-sm border border-gray-100 dark:border-gray-600">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-sm font-black text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">Vendeur Partenaire</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest text-gray-400 hover:text-red-600 transition-colors">
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Header & Content wrapper -->
    <div class="lg:pl-72 transition-all duration-300" :class="sidebarOpen ? 'lg:pl-72' : 'lg:pl-0'">
        
        <!-- Header -->
        <header class="sticky top-0 z-40 bg-white/80 dark:bg-gray-950/80 backdrop-blur-md border-b border-gray-100 dark:border-gray-800 transition-colors duration-300">
            <div class="px-8 flex items-center justify-between h-20">
                <div class="flex items-center gap-6">
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2.5 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 rounded-2xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h2 class="text-xl font-black tracking-tight text-gray-900 dark:text-white">@yield('page_title', 'Dashboard')</h2>
                </div>
                
                <div class="flex items-center gap-4">
                    <!-- Dark Mode Toggle -->
                    <button @click="toggleDarkMode()" class="p-2.5 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 rounded-2xl hover:bg-red-50 dark:hover:bg-red-950 transition-all border border-transparent hover:border-red-100 dark:hover:border-red-900/30">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M16.243 17.657l-.707-.707M6.343 6.343l-.707-.707ZM12 7a5 5 0 100 10 5 5 0 000-10z"/></svg>
                    </button>

                    <a href="{{ route('vendor.show', [Auth::user()->vendeur->id_vendeur, \Str::slug(Auth::user()->vendeur->nom_commercial ?? 'boutique')]) }}" target="_blank" class="px-6 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-red-600 dark:hover:bg-red-600 dark:hover:text-white transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Ma Boutique
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="p-8">
            <!-- Messages Flush/Alerts -->
            @if(session('success'))
                <div class="mb-8 p-4 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-900/30 rounded-[2rem] flex items-center gap-4 text-green-700 dark:text-green-400 transition-colors">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-2xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-sm font-bold">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-8 p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-900/30 rounded-[2rem] flex items-center gap-4 text-red-700 dark:text-red-400 transition-colors">
                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900/50 rounded-2xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-sm font-bold">{{ session('error') }}</p>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>

