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
        
        $currentVendor = $vendeur ?? request()->get('current_vendor') ?? (Auth::user()->vendeur ?? null);
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
    @yield('extra_head')
</head>
<body class="bg-gray-50 dark:bg-gray-950 font-sans text-gray-900 dark:text-gray-100 transition-colors duration-300" 
    x-data="{ 
          sidebarOpen: window.innerWidth >= 1024,
          mobileMenuOpen: false,
          darkMode: localStorage.getItem('darkMode') === 'true',
          newOrder: null,
          notifications: [],
          unreadCount: 0,
          notificationPermission: Notification.permission,
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
                  window.Echo.private('vendeur.{{ $currentVendor->id_vendeur ?? 0 }}')
                      .listen('.order.placed', (e) => {
                          this.newOrder = e.order;
                          this.playNotificationSound();
                      });
              }

              this.fetchNotifications();
              setInterval(() => this.fetchNotifications(), 30000);
          },
          fetchNotifications() {
              fetch('{{ route('notifications.unread') }}')
                  .then(response => response.json())
                  .then(data => {
                      if (data.length > this.unreadCount) {
                          this.playNotificationSound();
                      }
                      this.notifications = data;
                      this.unreadCount = data.length;
                  });
          },
          requestNotificationPermission() {
              Notification.requestPermission().then(permission => {
                  this.notificationPermission = permission;
                  if (permission === 'granted') {
                    this.playNotificationSound();
                  }
              });
          },
          playNotificationSound() {
              const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
              audio.play().catch(e => console.log('Audio autoplay blocked'));
          },
          markNotificationsRead() {
              if (this.unreadCount === 0) return;
              
              fetch('{{ route('notifications.mark-read') }}', {
                  method: 'POST',
                  headers: {
                      'X-CSRF-TOKEN': '{{ csrf_token() }}',
                      'Accept': 'application/json'
                  }
              }).then(() => {
                  this.notifications = [];
                  this.unreadCount = 0;
              });
          }
      }">

    <!-- New Order Popup Alert -->
    <template x-if="newOrder">
        <div class="fixed top-8 left-1/2 -translate-x-1/2 z-100 w-full max-w-md px-4">
            <div class="bg-gray-900 text-white p-6 rounded-4xl shadow-2xl new-order-alert flex items-center gap-6 border border-white/10">
                <div class="w-16 h-16 bg-red-600 rounded-2xl flex items-center justify-center shrink-0">
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

    <!-- Mobile Backdrop -->
    <div x-show="sidebarOpen && window.innerWidth < 1024" 
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-gray-900/60 backdrop-blur-sm lg:hidden transition-opacity"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;"></div>

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-50 bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 transition-all duration-300 transform"
           :class="[
               sidebarOpen ? 'w-72 translate-x-0' : (window.innerWidth >= 1024 ? 'w-20 translate-x-0' : 'w-72 -translate-x-full')
           ]">
        
        <div class="flex flex-col h-full">
            <!-- Brand -->
            <div class="px-6 py-10 flex items-center gap-4">
                @if(Auth::user()->vendeur && Auth::user()->vendeur->image_principale)
                    <img src="{{ asset('storage/' . Auth::user()->vendeur->image_principale) }}" 
                         alt="{{ Auth::user()->vendeur->nom_commercial }}" 
                         class="w-12 h-12 rounded-2xl object-cover shadow-xl shadow-red-200/50 dark:shadow-red-900/20 border-2 border-white dark:border-gray-800">
                @else
                    <div class="w-12 h-12 bg-linear-to-br from-red-600 to-orange-500 rounded-2xl flex items-center justify-center shadow-xl shadow-red-200/50 dark:shadow-red-900/20">
                        <span class="text-xl font-black text-white uppercase">{{ mb_substr(Auth::user()->vendeur->nom_commercial ?? Auth::user()->name, 0, 1) }}</span>
                    </div>
                @endif
                <div class="overflow-hidden">
                    <h1 class="text-sm font-black text-gray-900 dark:text-white truncate leading-tight">{{ Auth::user()->vendeur->nom_commercial ?? 'Ma Boutique' }}</h1>
                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest truncate">Espace Vendeur</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 space-y-2 overflow-y-auto no-scrollbar">
                <p x-show="sidebarOpen" class="px-4 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-4 transition-opacity duration-300">Général</p>
                
                <a href="{{ vendor_route('vendeur.slug.dashboard') }}" 
                   class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-gray-500 dark:text-gray-400 transition-all hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white {{ request()->routeIs('vendeur.slug.dashboard') || request()->routeIs('vendeur.dashboard') ? 'sidebar-active' : '' }}"
                   :class="!sidebarOpen ? 'justify-center px-0' : ''"
                   title="Tableau de bord">
                   <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                   <span x-show="sidebarOpen" class="truncate transition-opacity duration-300">Tableau de bord</span>
                </a>

                <a href="{{ vendor_route('vendeur.slug.orders.index') }}" 
                   class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-gray-500 dark:text-gray-400 transition-all hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white {{ request()->routeIs('vendeur.slug.orders.*') || request()->routeIs('vendeur.orders.*') ? 'sidebar-active' : '' }}"
                   :class="!sidebarOpen ? 'justify-center px-0' : ''"
                   title="Commandes">
                   <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                   <span x-show="sidebarOpen" class="truncate transition-opacity duration-300">Commandes</span>
                   <div x-show="sidebarOpen" class="ml-auto flex items-center gap-1">
                       @if(isset($unreadChatMessagesCount) && $unreadChatMessagesCount > 0)
                        <span class="px-2 py-0.5 bg-orange-600 text-white text-[10px] rounded-lg animate-pulse" title="Nouveaux messages chat">
                            {{ $unreadChatMessagesCount }}
                        </span>
                       @endif
                       @if(isset($activeOrders) && $activeOrders > 0)
                        <span class="px-2 py-0.5 bg-red-600 text-white text-[10px] rounded-lg">{{ $activeOrders }}</span>
                       @endif
                   </div>
                </a>

                <a href="{{ vendor_route('vendeur.slug.coupons.index') }}" 
                   class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-gray-500 dark:text-gray-400 transition-all hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white {{ request()->routeIs('vendeur.slug.coupons.*') || request()->routeIs('vendeur.coupons.*') ? 'sidebar-active' : '' }}"
                   :class="!sidebarOpen ? 'justify-center px-0' : ''"
                   title="Coupons & Promos">
                   <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                   <span x-show="sidebarOpen" class="truncate transition-opacity duration-300">Coupons & Promos</span>
                </a>

                <a href="{{ vendor_route('vendeur.slug.payouts.index') }}" 
                   class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-gray-500 dark:text-gray-400 transition-all hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white {{ request()->routeIs('vendeur.slug.payouts.*') || request()->routeIs('vendeur.payouts.*') ? 'sidebar-active' : '' }}"
                   :class="!sidebarOpen ? 'justify-center px-0' : ''"
                   title="Portefeuille">
                   <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                   <span x-show="sidebarOpen" class="truncate transition-opacity duration-300">Portefeuille</span>
                </a>

                <a href="{{ vendor_route('vendeur.slug.plats.index') }}" 
                   class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-gray-500 dark:text-gray-400 transition-all hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white {{ request()->routeIs('vendeur.slug.plats.*') || request()->routeIs('vendeur.plats.*') ? 'sidebar-active' : '' }}"
                   :class="!sidebarOpen ? 'justify-center px-0' : ''"
                   title="Produits & Menu">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    <span x-show="sidebarOpen" class="truncate transition-opacity duration-300">Produits & Menu</span>
                 </a>

                 <a href="{{ vendor_route('vendeur.slug.delivery.index') }}" 
                   class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-gray-500 dark:text-gray-400 transition-all hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white {{ request()->routeIs('vendeur.slug.delivery.*') ? 'sidebar-active' : '' }}"
                   :class="!sidebarOpen ? 'justify-center px-0' : ''"
                   title="Livreurs">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-show="sidebarOpen" class="truncate transition-opacity duration-300">Livreurs</span>
                 </a>

                <p x-show="sidebarOpen" class="px-4 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mt-10 mb-4 transition-opacity duration-300">Configuration</p>

                <a href="{{ vendor_route('vendeur.slug.settings.index') }}" 
                   class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-gray-500 dark:text-gray-400 transition-all hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white {{ request()->routeIs('vendeur.slug.settings.*') || request()->routeIs('vendeur.settings.*') ? 'sidebar-active' : '' }}"
                   :class="!sidebarOpen ? 'justify-center px-0' : ''"
                   title="Boutique">
                   <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                   <span x-show="sidebarOpen" class="truncate transition-opacity duration-300">Boutique</span>
                </a>

                @php
                    $currentVendor = request()->get('current_vendor') ?? (Auth::user()->vendeur ?? null);
                    $isOwner = $currentVendor && Auth::id() === $currentVendor->id_user;
                @endphp

                @if($isOwner || Auth::user()->role === 'super_admin')
                <a href="{{ vendor_route('vendeur.slug.team.index') }}" 
                   class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-gray-500 dark:text-gray-400 transition-all hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white {{ request()->routeIs('vendeur.slug.team.*') ? 'sidebar-active' : '' }}"
                   :class="!sidebarOpen ? 'justify-center px-0' : ''"
                   title="Équipe">
                   <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                   <span x-show="sidebarOpen" class="truncate transition-opacity duration-300">Équipe</span>
                </a>
                @endif
            </nav>

            <!-- User Footer -->
            <div class="p-6 border-t border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-4 p-4 rounded-3xl bg-gray-50 dark:bg-gray-800/50" :class="!sidebarOpen ? 'justify-center p-2' : ''">
                    <div class="w-10 h-10 shrink-0 rounded-2xl bg-white dark:bg-gray-700 flex items-center justify-center font-black text-xs text-red-600 shadow-sm border border-gray-100 dark:border-gray-600">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div x-show="sidebarOpen" class="flex-1 overflow-hidden transition-opacity duration-300">
                        <p class="text-sm font-black text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">Collaborateur</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest text-gray-400 hover:text-red-600 transition-all" :class="!sidebarOpen ? 'justify-center px-0' : ''">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span x-show="sidebarOpen" class="transition-opacity duration-300">Déconnexion</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Mobile Bottom Nav vars -->
    @php
        $mobileNav = [
            'dashboard' => ['route' => vendor_route('vendeur.slug.dashboard'), 'label' => 'Dashboard', 'check' => request()->routeIs('vendeur.slug.dashboard')],
            'orders'    => ['route' => vendor_route('vendeur.slug.orders.index'), 'label' => 'Commandes', 'check' => request()->routeIs('vendeur.slug.orders.*')],
            'produits'  => ['route' => vendor_route('vendeur.slug.plats.index'), 'label' => 'Produits', 'check' => request()->routeIs('vendeur.slug.plats.*')],
            'wallet'    => ['route' => vendor_route('vendeur.slug.payouts.index'), 'label' => 'Wallet', 'check' => request()->routeIs('vendeur.slug.payouts.*')],
            'settings'  => ['route' => vendor_route('vendeur.slug.settings.index'), 'label' => 'Boutique', 'check' => request()->routeIs('vendeur.slug.settings.*')],
        ];
    @endphp

    <!-- Header & Content wrapper -->
    <div class="transition-all duration-300" :class="sidebarOpen ? 'lg:pl-72' : (window.innerWidth >= 1024 ? 'lg:pl-20' : 'lg:pl-0')">
        
        <!-- Header -->
        <header class="sticky top-0 z-40 bg-white/80 dark:bg-gray-950/80 backdrop-blur-md border-b border-gray-100 dark:border-gray-800 transition-colors duration-300">
            <div class="px-4 lg:px-8 flex items-center justify-between h-16 lg:h-20">
                <div class="flex items-center gap-3 lg:gap-6">
                    <!-- Sidebar toggle (desktop only) -->
                    <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:flex p-2.5 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 rounded-2xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors shadow-sm active:scale-95">
                        <svg class="w-6 h-6 transition-transform duration-300" :class="!sidebarOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                        </svg>
                    </button>
                    <!-- Mobile: boutique avatar + name -->
                    <div class="flex lg:hidden items-center gap-3">
                        @if($currentVendor && $currentVendor->image_principale)
                            <img src="{{ asset('storage/' . $currentVendor->image_principale) }}"
                                 class="w-9 h-9 rounded-xl object-cover border-2 border-red-100"
                                 alt="">                        
                        @else
                            <div class="w-9 h-9 bg-linear-to-br from-red-600 to-orange-500 rounded-xl flex items-center justify-center shadow-md">
                                <span class="text-sm font-black text-white uppercase">{{ mb_substr($currentVendor->nom_commercial ?? Auth::user()->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm font-black text-gray-900 dark:text-white leading-none truncate max-w-[140px]">{{ $currentVendor->nom_commercial ?? 'Ma Boutique' }}</p>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Espace {{ $currentVendor && Auth::id() === $currentVendor->id_user ? 'Vendeur' : 'Collaborateur' }}</p>
                        </div>
                    </div>
                    <!-- Desktop title -->
                    <h2 class="hidden lg:block text-xl font-black tracking-tight text-gray-900 dark:text-white">@yield('page_title', 'Dashboard')</h2>
                </div>
                
                <div class="flex items-center gap-2 sm:gap-4">
                    <!-- Notifications Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open; if(open) markNotificationsRead()" 
                                class="relative p-2.5 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 rounded-2xl hover:bg-red-50 dark:hover:bg-red-950 transition-all border border-transparent hover:border-red-100 dark:hover:border-red-900/30">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <template x-if="unreadCount > 0">
                                <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-red-600 border-2 border-white dark:border-gray-900 rounded-full"></span>
                            </template>
                        </button>

                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             class="absolute right-0 mt-4 w-80 bg-white dark:bg-gray-900 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-800 z-50 overflow-hidden"
                             x-cloak>
                            
                            <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/30">
                                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Notifications</h3>
                                <span class="px-2 py-0.5 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg text-[10px] font-black" x-text="unreadCount + ' nouvelles'"></span>
                            </div>

                            <div class="max-h-96 overflow-y-auto no-scrollbar">
                                <template x-if="notifications.length === 0">
                                    <div class="p-10 text-center">
                                        <div class="w-12 h-12 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-300">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                        </div>
                                        <p class="text-xs font-bold text-gray-400">Aucune notification</p>
                                    </div>
                                </template>

                                <template x-for="notif in notifications" :key="notif.id">
                                    <div class="p-4 border-b border-gray-50 dark:border-gray-800/50 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors cursor-pointer group">
                                        <div class="flex gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-black text-gray-900 dark:text-white" x-text="notif.titre"></p>
                                                <p class="text-[11px] text-gray-500 dark:text-gray-400 line-clamp-2 mt-1 font-medium" x-text="notif.message"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Access -->
                    @if(isset($unreadChatMessagesCount) && $unreadChatMessagesCount > 0)
                    <a href="{{ vendor_route('vendeur.slug.orders.index') }}" 
                       title="Messages non lus"
                       class="p-2.5 bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 rounded-2xl border border-orange-100 dark:border-orange-900/30 relative animate-pulse">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-600 text-white text-[9px] font-black rounded-full flex items-center justify-center">
                            {{ $unreadChatMessagesCount }}
                        </span>
                    </a>
                    @endif

                     <!-- Dark Mode Toggle -->
                    <button @click="toggleDarkMode()" class="p-2.5 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 rounded-2xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-all border border-transparent">
                        <svg x-show="!darkMode" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="darkMode" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M16.243 17.657l-.707-.707M6.343 6.343l-.707-.707ZM12 7a5 5 0 100 10 5 5 0 000-10z"/></svg>
                    </button>

                    <!-- Permission button discreet -->
                    <template x-if="notificationPermission !== 'granted'">
                        <button @click="requestNotificationPermission()" class="hidden sm:flex p-2 text-orange-400 hover:text-orange-500 transition-colors" title="Activer les alertes sonores">
                            <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                        </button>
                    </template>

                    <a href="{{ route('vendor.show', [Auth::user()->vendeur->id_vendeur, \Str::slug(Auth::user()->vendeur->nom_commercial ?? 'boutique')]) }}" target="_blank" class="px-4 sm:px-6 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl text-[10px] sm:text-[11px] font-black uppercase tracking-widest hover:bg-red-600 dark:hover:bg-red-600 dark:hover:text-white transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span class="hidden xs:inline">Ma Boutique</span>
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="p-4 lg:p-8 pb-24 lg:pb-8">
            <!-- Messages Flush/Alerts -->
            @if(session('success'))
                <div class="mb-8 p-4 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-900/30 rounded-4xl flex items-center gap-4 text-green-700 dark:text-green-400 transition-colors">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-2xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-sm font-bold">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-8 p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-900/30 rounded-4xl flex items-center gap-4 text-red-700 dark:text-red-400 transition-colors">
                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900/50 rounded-2xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-sm font-bold">{{ session('error') }}</p>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- ============================================ -->
    <!-- MOBILE BOTTOM NAVIGATION — Vendeur App Bar  -->
    <!-- ============================================ -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50">
        <div class="bg-white/95 dark:bg-gray-950/95 backdrop-blur-2xl border-t border-gray-100/60 dark:border-gray-800/60 shadow-2xl shadow-black/10">
            <div class="flex justify-around items-center px-1 pt-2 pb-3" style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom))">

                {{-- Dashboard --}}
                <a href="{{ vendor_route('vendeur.slug.dashboard') }}"
                   class="flex flex-col items-center justify-center flex-1 py-1 group relative transition-all duration-200 active:scale-90">
                    @if(request()->routeIs('vendeur.slug.dashboard') || request()->routeIs('vendeur.dashboard'))
                        <span class="absolute top-0 left-1/2 -translate-x-1/2 w-6 h-1 bg-red-600 rounded-full"></span>
                    @endif
                    <div class="relative w-9 h-9 flex items-center justify-center rounded-2xl transition-all duration-200 {{ request()->routeIs('vendeur.slug.dashboard') ? 'bg-red-600 shadow-lg shadow-red-500/30' : 'bg-transparent group-active:bg-gray-100 dark:group-active:bg-gray-800' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('vendeur.slug.dashboard') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('vendeur.slug.dashboard') ? '2.5' : '1.8' }}" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </div>
                    <span class="mt-0.5 text-[8px] font-bold tracking-wide {{ request()->routeIs('vendeur.slug.dashboard') ? 'text-red-600 dark:text-red-500' : 'text-gray-400 dark:text-gray-500' }}">Dashboard</span>
                </a>

                {{-- Commandes --}}
                <a href="{{ vendor_route('vendeur.slug.orders.index') }}"
                   class="flex flex-col items-center justify-center flex-1 py-1 group relative transition-all duration-200 active:scale-90">
                    @if(request()->routeIs('vendeur.slug.orders.*'))
                        <span class="absolute top-0 left-1/2 -translate-x-1/2 w-6 h-1 bg-orange-500 rounded-full"></span>
                    @endif
                    <div class="relative w-9 h-9 flex items-center justify-center rounded-2xl transition-all duration-200 {{ request()->routeIs('vendeur.slug.orders.*') ? 'bg-orange-500 shadow-lg shadow-orange-500/30' : 'bg-transparent group-active:bg-gray-100 dark:group-active:bg-gray-800' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('vendeur.slug.orders.*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('vendeur.slug.orders.*') ? '2.5' : '1.8' }}" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        @if(isset($activeOrders) && $activeOrders > 0)
                            <span class="absolute -top-1 -right-1 min-w-[16px] h-4 px-1 bg-red-600 text-white text-[8px] font-black rounded-full flex items-center justify-center animate-pulse shadow-sm">
                                {{ $activeOrders > 9 ? '9+' : $activeOrders }}
                            </span>
                        @endif
                    </div>
                    <span class="mt-0.5 text-[8px] font-bold tracking-wide {{ request()->routeIs('vendeur.slug.orders.*') ? 'text-orange-500' : 'text-gray-400 dark:text-gray-500' }}">Commandes</span>
                </a>

                {{-- Produits (Central FAB elevated) --}}
                <div class="relative flex flex-col items-center justify-center flex-1 -mt-4">
                    <a href="{{ vendor_route('vendeur.slug.plats.index') }}"
                       class="relative flex items-center justify-center w-14 h-14 rounded-2xl shadow-xl active:scale-90 transition-all duration-200 border-[3px] border-white dark:border-gray-950
                       {{ request()->routeIs('vendeur.slug.plats.*') || request()->routeIs('vendeur.plats.*') ? 'bg-red-600 shadow-red-600/40' : 'bg-linear-to-br from-red-500 to-orange-500 shadow-red-500/30' }}">
                        @if(request()->routeIs('vendeur.slug.plats.*') || request()->routeIs('vendeur.plats.*'))
                            <span class="absolute inset-0 rounded-2xl animate-ping bg-red-500 opacity-20"></span>
                        @endif
                        {{-- Fork & knife icon (food/menu) --}}
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 002-2V2"/>
                            <path d="M7 2v20"/>
                            <path d="M21 15V2v0a5 5 0 00-5 5v6c0 1.1.9 2 2 2h3zm0 0v7"/>
                        </svg>
                    </a>
                    <span class="mt-1 text-[9px] font-black tracking-wide {{ request()->routeIs('vendeur.slug.plats.*') || request()->routeIs('vendeur.plats.*') ? 'text-red-600 dark:text-red-400' : 'text-gray-400 dark:text-gray-500' }}">Menu</span>
                </div>

                {{-- Wallet --}}
                <a href="{{ vendor_route('vendeur.slug.payouts.index') }}"
                   class="flex flex-col items-center justify-center flex-1 py-1 group relative transition-all duration-200 active:scale-90">
                    @if(request()->routeIs('vendeur.slug.payouts.*'))
                        <span class="absolute top-0 left-1/2 -translate-x-1/2 w-6 h-1 bg-green-500 rounded-full"></span>
                    @endif
                    <div class="w-9 h-9 flex items-center justify-center rounded-2xl transition-all duration-200 {{ request()->routeIs('vendeur.slug.payouts.*') ? 'bg-green-500 shadow-lg shadow-green-500/30' : 'bg-transparent group-active:bg-gray-100 dark:group-active:bg-gray-800' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('vendeur.slug.payouts.*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('vendeur.slug.payouts.*') ? '2.5' : '1.8' }}" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <span class="mt-0.5 text-[8px] font-bold tracking-wide {{ request()->routeIs('vendeur.slug.payouts.*') ? 'text-green-500' : 'text-gray-400 dark:text-gray-500' }}">Wallet</span>
                </a>

                {{-- Boutique/Settings --}}
                <a href="{{ vendor_route('vendeur.slug.settings.index') }}"
                   class="flex flex-col items-center justify-center flex-1 py-1 group relative transition-all duration-200 active:scale-90">
                    @if(request()->routeIs('vendeur.slug.settings.*') || request()->routeIs('vendeur.slug.team.*') || request()->routeIs('vendeur.slug.coupons.*'))
                        <span class="absolute top-0 left-1/2 -translate-x-1/2 w-6 h-1 bg-indigo-500 rounded-full"></span>
                    @endif
                    <div class="w-9 h-9 flex items-center justify-center rounded-2xl transition-all duration-200 {{ request()->routeIs('vendeur.slug.settings.*') || request()->routeIs('vendeur.slug.team.*') || request()->routeIs('vendeur.slug.coupons.*') ? 'bg-indigo-500 shadow-lg shadow-indigo-500/30' : 'bg-transparent group-active:bg-gray-100 dark:group-active:bg-gray-800' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('vendeur.slug.settings.*') || request()->routeIs('vendeur.slug.team.*') || request()->routeIs('vendeur.slug.coupons.*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span class="mt-0.5 text-[8px] font-bold tracking-wide {{ request()->routeIs('vendeur.slug.settings.*') || request()->routeIs('vendeur.slug.team.*') || request()->routeIs('vendeur.slug.coupons.*') ? 'text-indigo-500' : 'text-gray-400 dark:text-gray-500' }}">Boutique</span>
                </a>

            </div>
        </div>
    </nav>
    <!-- /MOBILE BOTTOM NAVIGATION -->

    @yield('scripts')
</body>
</html>

