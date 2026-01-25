<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php
        // Variables are now shared globally via AppServiceProvider
        $finalLogo = $siteLogo ?? asset('assets/logo/logo-cabaa.png');
        $siteName = $siteName ?? config('app.name', 'FoodConnect');
        $finalFavicon = $siteFavicon ?? null;
    @endphp

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', \App\Models\AppSetting::get('meta_title', $siteName))</title>
    <meta name="description" content="{{ \App\Models\AppSetting::get('meta_description') }}">
    <meta name="keywords" content="{{ \App\Models\AppSetting::get('meta_keywords') }}">

    @if($finalFavicon)
        <link rel="icon" type="image/x-icon" href="{{ $finalFavicon }}">
    @endif

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">

    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
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
    @endif

    <style>
        [x-cloak] { display: none !important; }
        
        .glass-header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(243, 244, 246, 0.5);
        }

        .dark .glass-header {
            background: rgba(10, 10, 10, 0.8);
            border-bottom: 1px solid rgba(31, 41, 55, 0.5);
        }

        .dark body {
            background-color: #030712 !important;
            color: #f9fafb !important;
        }

        /* Custom Selection & Scrollbar */
        ::selection { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f9fafb; }
        ::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 20px; border: 2px solid #f9fafb; }
        ::-webkit-scrollbar-thumb:hover { background: #d1d5db; }

        .dark ::-webkit-scrollbar-track { background: #030712; }
        .dark ::-webkit-scrollbar-thumb { background: #1f2937; border: 2px solid #030712; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #374151; }
    </style>
    
    @php
        $dashboardRoute = route('dashboard');
        if(auth()->check()) {
            if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin') {
                $dashboardRoute = route('admin.dashboard');
            } elseif(auth()->user()->isVendor()) {
                $dashboardRoute = route('vendeur.dashboard');
            }
        }
    @endphp
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-200 font-sans transition-colors duration-300" 
      x-data="{ 
        darkMode: localStorage.getItem('darkMode') === 'true',
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },
        mobileMenuOpen: false,
        cartCount: {{ session('cart') ? count(session('cart')) : 0 }},
        showModal: false,
        modalTitle: '',
        modalMessage: '',
        modalType: 'success', // success, error, info
        notifications: [],
        unreadCount: 0,
        
        init() {
            window.showToast = (message, type = 'success', title = '') => {
                this.modalMessage = message;
                this.modalType = type;
                this.modalTitle = title || (type === 'success' ? 'Succès' : (type === 'error' ? 'Erreur' : 'Information'));
                this.showModal = true;
            };

            // Session Messages
            @if(session('success') || session('status'))
                setTimeout(() => window.showToast('{{ session('success') ?? session('status') }}', 'success'), 500);
            @endif
            @if(session('error'))
                setTimeout(() => window.showToast('{{ session('error') }}', 'error'), 500);
            @endif

            window.addEventListener('cart-updated', (e) => {
                this.cartCount = e.detail.count;
            });

            @auth
                this.fetchNotifications();
                setInterval(() => this.fetchNotifications(), 30000); // Poll every 30 seconds
            @endauth
        },

        fetchNotifications() {
            fetch('{{ route('notifications.unread') }}')
                .then(response => response.json())
                .then(data => {
                    this.notifications = data;
                    this.unreadCount = data.length;
                    
                    // Specific logic to notify user of NEW notifications if count increased
                    // (Optional for MVP)
                });
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
        },

        addCart(id) {
            fetch(`/panier/ajouter/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    this.cartCount = data.cart_count;
                    window.showToast(data.success, 'success');
                } else if(data.error) {
                    window.showToast(data.error, 'error');
                }
            });
        },
        toggleFavorite(vendorId, event = null) {
            fetch(`/favoris/toggle/${vendorId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.showToast(data.success, 'success');
                    
                    if(event && event.target) {
                        const btn = event.target.closest('button');
                        if(btn) {
                            const icon = btn.querySelector('svg');
                            if(data.action === 'added') {
                                icon.classList.remove('text-gray-400');
                                icon.classList.add('text-red-600', 'fill-current');
                            } else {
                                icon.classList.add('text-gray-400');
                                icon.classList.remove('text-red-600', 'fill-current');
                            }
                        }
                    }
                } else if(data.error) {
                    window.showToast(data.error, 'error');
                }
            });
        }
      }">

    <!-- Global Modal Alert -->
    <div x-show="showModal" 
         class="fixed inset-0 z-[100] overflow-y-auto" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" 
                 @click="showModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <!-- Modal Body -->
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative inline-block px-8 py-10 overflow-hidden text-center align-bottom transition-all transform bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-2xl sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-100 dark:border-gray-800">
                
                <div class="flex flex-col items-center">
                    <!-- Icon -->
                    <div :class="{
                        'bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400': modalType === 'success',
                        'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400': modalType === 'error',
                        'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400': modalType === 'info'
                    }" class="w-20 h-20 rounded-3xl flex items-center justify-center mb-8">
                        <template x-if="modalType === 'success'">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </template>
                        <template x-if="modalType === 'error'">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </template>
                        <template x-if="modalType === 'info'">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </template>
                    </div>

                    <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-4 tracking-tight" x-text="modalTitle"></h3>
                    <p class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest leading-loose mb-10" x-text="modalMessage"></p>

                    <button @click="showModal = false" 
                            class="w-full py-5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] hover:bg-black dark:hover:bg-gray-100 transition-all shadow-xl shadow-gray-200 dark:shadow-none">
                        D'accord
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Glassmorphic -->
    <header class="glass-header sticky top-0 z-50 transition-all duration-300">
        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14">
            <div class="flex justify-between items-center h-20">

                <!-- Logo -->
                <div class="flex items-center space-x-12">
                    <a href="/" class="flex items-center gap-2.5 group">
                        @if($finalLogo)
                            <img src="{{ $finalLogo }}" alt="{{ $siteName }}" class="h-10 object-contain transform group-hover:scale-105 transition-transform">
                        @else
                            <div class="w-10 h-10 bg-gradient-to-br from-red-600 to-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-red-200 transform group-hover:rotate-6 transition-transform">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        @endif
                        <span class="text-2xl font-display font-black tracking-tighter bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent group-hover:scale-105 transition-transform">
                            {{ $siteName }}
                        </span>
                    </a>

                    <!-- Navigation Desktop -->
                    <nav class="hidden lg:flex items-center gap-8">
                        <a href="/" class="relative text-[11px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-red-600 transition-colors group">
                            Accueil
                            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-red-600 transition-all group-hover:w-full {{ request()->is('/') ? 'w-full' : '' }}"></span>
                        </a>
                        <a href="{{ route('explore') }}" class="relative text-[11px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-red-600 transition-colors group">
                            Vendeurs
                            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-red-600 transition-all group-hover:w-full {{ request()->is('explore') ? 'w-full' : '' }}"></span>
                        </a>
                        <a href="{{ route('explore.plats') }}" class="relative text-[11px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-red-600 transition-colors group">
                            Produits
                            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-red-600 transition-all group-hover:w-full {{ request()->is('produits*') ? 'w-full' : '' }}"></span>
                        </a>
                        <a href="{{ route('orders.track') }}" class="relative text-[11px] font-black uppercase tracking-[0.2em] {{ request()->routeIs('orders.track') ? 'text-red-600' : 'text-gray-400' }} hover:text-red-600 transition-colors group">
                            Suivre commande
                            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-red-600 transition-all group-hover:w-full {{ request()->routeIs('orders.track') ? 'w-full' : '' }}"></span>
                        </a>

                    </nav>
                </div>

                <!-- Actions droite -->
                <div class="flex items-center gap-3 lg:gap-4">
                    <!-- Dark Mode Toggle -->
                    <button @click="toggleDarkMode()" class="p-2.5 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 rounded-2xl hover:bg-red-50 dark:hover:bg-red-950 transition-all border border-transparent hover:border-red-100">
                        <svg x-show="!darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M16.243 17.657l-.707-.707M6.343 6.336l-.707-.707ZM12 7a5 5 0 100 10 5 5 0 000-10z"/></svg>
                    </button>

                    @auth
                    <!-- Notifications Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open; if(open) markNotificationsRead()" 
                                class="relative p-2.5 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 rounded-2xl hover:bg-red-50 dark:hover:bg-red-950 transition-all group border border-transparent hover:border-red-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <template x-if="unreadCount > 0">
                                <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-red-600 border-2 border-white dark:border-gray-900 rounded-full animate-pulse shadow-sm shadow-red-200"></span>
                            </template>
                        </button>

                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             class="absolute right-0 mt-3 w-80 bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-gray-800 z-50 overflow-hidden"
                             x-cloak>
                            
                            <div class="p-6 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase tracking-widest text-gray-900 dark:text-white">Notifications</h3>
                                <span class="px-2.5 py-1 bg-red-50 dark:bg-red-900/40 text-red-600 dark:text-red-400 rounded-lg text-[10px] font-black" x-text="unreadCount + ' nouvelles'"></span>
                            </div>

                            <div class="max-h-96 overflow-y-auto custom-scrollbar">
                                <template x-for="notif in notifications" :key="notif.id_notification">
                                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors border-b border-gray-50 dark:border-gray-800 last:border-0 group cursor-pointer">
                                        <div class="flex gap-4">
                                            <div class="w-10 h-10 bg-orange-50 dark:bg-orange-900/20 rounded-xl flex items-center justify-center text-orange-600 dark:text-orange-400 shrink-0 group-hover:scale-110 transition-transform">
                                                <svg x-show="notif.type_notification === 'commande'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                                <svg x-show="notif.type_notification !== 'commande'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[11px] font-black text-gray-900 dark:text-white truncate mb-1" x-text="notif.titre"></p>
                                                <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold leading-relaxed line-clamp-2" x-text="notif.message"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="notifications.length === 0">
                                    <div class="p-10 text-center">
                                        <div class="w-12 h-12 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-200 dark:text-gray-700">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4a2 2 0 012-2m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                        </div>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Aucune nouvelle notification</p>
                                    </div>
                                </template>
                            </div>

                            <a href="#" class="block p-5 bg-gray-50 dark:bg-gray-800/80 text-center text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 hover:text-red-600 transition-colors">
                                Tout voir
                            </a>
                        </div>
                    </div>
                    @endauth

                    <!-- Panier Compact Premium (Desktop Only) -->
                    <a href="{{ route('cart.index') }}" class="hidden lg:flex relative p-2.5 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 rounded-2xl hover:bg-red-50 dark:hover:bg-red-950 transition-all group border border-transparent hover:border-red-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <template x-if="cartCount > 0">
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-600 text-white text-[10px] flex items-center justify-center rounded-lg font-black shadow-lg shadow-red-200" x-text="cartCount"></span>
                        </template>
                    </a>

                    @auth

                        <!-- Menu Utilisateur Floating (Desktop Only) -->
                        <div class="hidden lg:block relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 p-1.5 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl hover:border-red-200 dark:hover:border-red-900 transition-all shadow-sm">
                                <div class="relative">
                                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=ef4444&color=fff' }}"
                                         class="w-8 h-8 rounded-xl object-cover"
                                         alt="{{ auth()->user()->name }}">
                                    <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></div>
                                </div>
                                <span class="hidden md:block text-xs font-black uppercase tracking-widest text-gray-900 dark:text-white border-l border-gray-100 dark:border-gray-800 pl-3 ml-1">{{ Str::limit(auth()->user()->name, 12) }}</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown Refined -->
                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="absolute right-0 mt-3 w-64 bg-white dark:bg-gray-900 rounded-[2rem] shadow-2xl border border-gray-100 dark:border-gray-800 py-3 z-50 overflow-hidden"
                                 x-cloak>

                                <div class="px-6 py-4 border-b border-gray-50 dark:border-gray-800 flex items-center gap-3">
                                    <div class="w-10 h-10 bg-red-50 dark:bg-red-900/30 rounded-xl flex items-center justify-center text-red-600 dark:text-red-400 shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest truncate">{{ auth()->user()->email }}</p>
                                    </div>
                                </div>

                                <div class="p-2 space-y-1">
                                    <a href="{{ $dashboardRoute }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-black text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-800 rounded-xl transition-all">
                                        Tableau de bord
                                    </a>
                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all">
                                        Mon profil
                                    </a>
                                    <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all">
                                        Mes commandes
                                    </a>
                                    <a href="{{ route('orders.track') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-xl transition-all">
                                        Suivre une commande
                                    </a>
                                </div>

                                @if(auth()->user()->isVendor())
                                    <div class="p-2 border-t border-gray-50 dark:border-gray-800">
                                        <div class="px-4 py-2 mb-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Espace Vendeur</div>
                                        <a href="{{ vendor_route('vendeur.slug.orders.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-xl transition-all">
                                            Commandes reçues
                                        </a>
                                        <a href="{{ vendor_route('vendeur.slug.plats.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-xl transition-all">
                                            Ma boutique (Articles)
                                        </a>
                                    </div>
                                @endif

                                <div class="p-2 border-t border-gray-50 dark:border-gray-800">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full gap-3 px-4 py-2.5 text-sm font-black text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all">
                                            Se déconnecter
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="hidden lg:flex items-center gap-3">
                            <a href="{{ route('login') }}" class="text-[11px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition">Connexion</a>
                            <a href="{{ route('register') }}" class="px-6 py-3 bg-gray-900 dark:bg-white dark:text-gray-900 text-white text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-black dark:hover:bg-gray-100 transition shadow-lg">S'inscrire</a>
                        </div>
                    @endauth

                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="relative pb-24 lg:pb-0">


        <div class="w-full">
            @yield('content')
        </div>
    </main>

    <!-- Footer Premium -->
    <footer class="bg-white border-t border-gray-100 mt-32 relative overflow-hidden">
        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14 py-24">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-16">
                
                <!-- Brand Columns -->
                <div class="lg:col-span-2">
                    <a href="/" class="flex items-center gap-3 mb-8">
                        @if($finalLogo)
                            <img src="{{ $finalLogo }}" alt="{{ $siteName }}" class="h-10 object-contain">
                        @else
                            <div class="w-10 h-10 bg-gradient-to-br from-red-600 to-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-red-200">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                        @endif
                        <span class="text-2xl font-display font-black tracking-tighter bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent">{{ $siteName }}</span>
                    </a>
                    <p class="text-gray-500 font-medium leading-relaxed mb-10 max-w-sm italic">
                        La destination privilégiée pour découvrir les trésors culinaires de votre région. Savourez l'authenticité à chaque bouchée.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition-all"><svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                        <a href="#" class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 hover:bg-pink-50 hover:text-pink-600 transition-all"><svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.166.054 1.791.248 2.214.414.56.216.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.804-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0-2.163c-3.259 0-3.667.014-4.947.072-1.28.06-2.148.261-2.913.558-.789.306-1.459.717-2.126 1.384s-1.078 1.335-1.384 2.126c-.297.765-.499 1.636-.558 2.913-.06 1.28-.072 1.687-.072 4.947s.015 3.667.072 4.947c.06 1.278.262 2.148.558 2.913.306.789.717 1.459 1.384 2.126s1.335 1.078 2.126 1.384c.765.297 1.636.499 2.913.558 1.28.06 1.687.072 4.947.072s3.667-.015 4.947-.072c1.278-.06 2.148-.262 2.913-.558.789-.306 1.459-.717 2.126-1.384s1.078-1.335 1.384-2.126c.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.278-.262-2.148-.558-2.913-.306-.789-.717-1.459-1.384-2.126s-1.335-1.078-2.126-1.384c-.765-.297-1.636-.499-2.913-.558-1.28-.06-1.687-.072-4.947-.072z"/></svg></a>
                    </div>
                </div>

                <!-- Fast Links -->
                <div>
                    <h4 class="text-xs font-black uppercase tracking-widest text-gray-900 mb-8">Plateforme</h4>
                    <ul class="space-y-4">
                        <li><a href="/" class="text-[13px] font-bold text-gray-400 hover:text-red-600 transition-colors">Accueil</a></li>
                        <li><a href="{{ route('explore') }}" class="text-[13px] font-bold text-gray-400 hover:text-red-600 transition-colors">Explorer</a></li>
                        <li><a href="{{ route('vendors.map') }}" class="text-[13px] font-bold text-orange-600 hover:text-orange-700 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            Vendeurs les plus proches
                        </a></li>
                        @auth
                            @if(auth()->user()->canApplyAsVendor())
                                <li><a href="{{ route('vendor.apply') }}" class="text-[13px] font-bold text-red-500 hover:text-red-600 transition-colors">Devenir Vendeur</a></li>
                            @endif
                        @endauth
                    </ul>
                </div>

                <div>
                    <h4 class="text-xs font-black uppercase tracking-widest text-gray-900 mb-8">Aide & Plus</h4>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-[13px] font-bold text-gray-400 hover:text-red-600 transition-colors">Notre FAQ</a></li>
                        <li><a href="{{ route('terms') }}" class="text-[13px] font-bold text-gray-400 hover:text-red-600 transition-colors">Conditions d'Utilisation</a></li>
                        <li><a href="{{ route('privacy') }}" class="text-[13px] font-bold text-gray-400 hover:text-red-600 transition-colors">Politique de Confidentialité</a></li>
                    </ul>
                </div>

                <!-- Newsletter Column -->
                <div class="md:col-span-2 lg:col-span-1">
                    <h4 class="text-xs font-black uppercase tracking-widest text-gray-900 mb-8">Newsletter</h4>
                    <p class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-6">Savourez les offres exclusives</p>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="relative group">
                        @csrf
                        <input type="email" name="email" placeholder="Votre email" required
                               class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-[1.5rem] text-sm font-bold text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-red-500 transition-all outline-none">
                        <button type="submit" class="absolute right-2 top-2 bottom-2 px-4 bg-gray-900 text-white rounded-2xl hover:bg-black transition-colors font-black text-xs">OK</button>
                    </form>
                </div>

            </div>
            
            <div class="mt-24 pt-10 border-t border-gray-50">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-300 text-center">
                    &copy; {{ date('Y') }} {{ $siteName }}. Made with passion for food.
                </p>
            </div>
        </div>
    </footer>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bottom Navigation Bar (Mobile) -->
    <nav class="lg:hidden fixed bottom-6 left-4 right-4 z-50 bg-white/90 dark:bg-gray-900/90 backdrop-blur-xl border border-gray-200 dark:border-gray-800 rounded-2xl shadow-2xl flex justify-between items-center px-6 py-4">
        <a href="/" class="flex flex-col items-center gap-1 {{ request()->is('/') ? 'text-red-600 dark:text-red-500' : 'text-gray-400 dark:text-gray-500' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-[9px] font-black uppercase tracking-wider">Accueil</span>
        </a>

        <a href="{{ route('explore') }}" class="flex flex-col items-center gap-1 {{ request()->is('explore*') ? 'text-red-600 dark:text-red-500' : 'text-gray-400 dark:text-gray-500' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span class="text-[9px] font-black uppercase tracking-wider">Explorer</span>
        </a>

        <div class="relative -top-8">
            <a href="{{ route('cart.index') }}" class="flex flex-col items-center justify-center w-14 h-14 bg-red-600 text-white rounded-full shadow-lg shadow-red-200 transform active:scale-90 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <template x-if="cartCount > 0">
                    <span class="absolute top-0 right-0 w-4 h-4 bg-white text-red-600 text-[10px] font-black flex items-center justify-center rounded-full border-2 border-red-600" x-text="cartCount"></span>
                </template>
            </a>
        </div>

        <a href="{{ route('orders.index') }}" class="flex flex-col items-center gap-1 {{ request()->is('orders*') ? 'text-red-600 dark:text-red-500' : 'text-gray-400 dark:text-gray-500' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span class="text-[9px] font-black uppercase tracking-wider">Commandes</span>
        </a>

        @auth
            <a href="{{ $dashboardRoute }}" class="flex flex-col items-center gap-1 {{ request()->url() === $dashboardRoute ? 'text-red-600 dark:text-red-500' : 'text-gray-400 dark:text-gray-500' }}">
                <div class="relative">
                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=ef4444&color=fff' }}" 
                         class="w-6 h-6 rounded-lg object-cover {{ request()->url() === $dashboardRoute ? 'ring-2 ring-red-600' : '' }}">
                    <div class="absolute -bottom-0.5 -right-0.5 w-2 h-2 bg-green-500 border border-white dark:border-gray-900 rounded-full"></div>
                </div>
                <span class="text-[9px] font-black uppercase tracking-wider">Compte</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="flex flex-col items-center gap-1 {{ request()->is('login') ? 'text-red-600 dark:text-red-500' : 'text-gray-400 dark:text-gray-500' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-[9px] font-black uppercase tracking-wider">Connexion</span>
            </a>
        @endauth
    </nav>

    @yield('scripts')
    
    <!-- Google Maps API -->
    @if(env('GOOGLE_MAPS_API_KEY'))
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
    @endif
</body>
</html>
