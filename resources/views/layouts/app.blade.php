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
        notificationPermission: Notification.permission,
        
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
                    if (data.length > this.unreadCount) {
                        this.playNotificationSound();
                        if (Notification.permission === 'granted') {
                            new window.Notification(data[0].titre, {
                                body: data[0].message,
                                icon: '{{ asset('assets/logo/logo-cabaa.png') }}'
                            });
                        }
                    }
                    this.notifications = data;
                    this.unreadCount = data.length;
                });
        },

        playNotificationSound() {
            const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
            audio.play().catch(e => console.log('Autoplay blocked or audio error'));
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
                    window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
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
        },

        requestNotificationPermission() {
            Notification.requestPermission().then(permission => {
                this.notificationPermission = permission;
                if (permission === 'granted') {
                    window.showToast('Notifications activées !', 'success');
                    this.playNotificationSound();
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

                    <!-- Notification Permission -->
                    <template x-if="notificationPermission !== 'granted'">
                        <button @click="requestNotificationPermission()" class="p-2.5 bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 rounded-2xl hover:bg-orange-100 dark:hover:bg-orange-900/50 transition-all border border-transparent animate-pulse" title="Activer les notifications sonores">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        </button>
                    </template>

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
                                    @if(auth()->user()->isDriver())
                                    <a href="{{ route('delivery.my-deliveries') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-xl transition-all">
                                        Espace Livreur
                                    </a>
                                    @endif
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
    <footer class="bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 mt-20 lg:mt-32 relative overflow-hidden transition-colors duration-300">
        <!-- Subtle Glow Background -->
        <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-[500px] h-[500px] bg-red-500/5 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/2 w-[500px] h-[500px] bg-orange-500/5 blur-[120px] rounded-full pointer-events-none"></div>

        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14 pt-12 pb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-12 lg:gap-16">
                
                <!-- Brand Columns -->
                <div class="sm:col-span-2 lg:col-span-2 space-y-8">
                    <a href="/" class="flex items-center gap-3 group">
                        @if($finalLogo)
                            <img src="{{ $finalLogo }}" alt="{{ $siteName }}" class="h-12 object-contain transform group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-12 h-12 bg-gradient-to-br from-red-600 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg shadow-red-200 transform group-hover:rotate-6 transition-transform duration-500">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                        @endif
                        <span class="text-3xl font-display font-black tracking-tighter bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent transform group-hover:scale-105 transition-transform duration-500 leading-none">
                            {{ $siteName }}
                        </span>
                    </a>
                    
                    <p class="text-gray-500 dark:text-gray-400 font-medium leading-relaxed max-w-sm text-sm lg:text-base">
                        La destination privilégiée pour découvrir les trésors culinaires de votre région. Savourez l'authenticité et la qualité à chaque bouchée, livrés directement chez vous.
                    </p>

                    <div class="flex gap-4">
                        @php
                            $socials = [
                                [
                                    'name' => 'Facebook',
                                    'icon' => 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z', 
                                    'color' => 'blue',
                                    'url' => '#'
                                ],
                                [
                                    'name' => 'X',
                                    'icon' => 'M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z', 
                                    'color' => 'slate',
                                    'url' => '#'
                                ],
                                [
                                    'name' => 'Instagram',
                                    'icon' => 'M12 2.163c3.204 0 3.584.012 4.85.07 1.166.054 1.791.248 2.214.414.56.216.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.804-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0-2.163c-3.259 0-3.667.014-4.947.072-1.28.06-2.148.261-2.913.558-.789.306-1.459.717-2.126 1.384s-1.078 1.335-1.384 2.126c-.297.765-.499 1.636-.558 2.913-.06 1.28-.072 1.687-.072 4.947s.015 3.667.072 4.947c.06 1.278.262 2.148.558 2.913.306.789.717 1.459 1.384 2.126s1.335 1.078 2.126 1.384c.765.297 1.636.499 2.913.558 1.28.06 1.687.072 4.947.072s3.667-.015 4.947-.072c1.278-.06 2.148-.262 2.913-.558.789-.306 1.459-.717 2.126-1.384s1.078-1.335 1.384-2.126c.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.278-.262-2.148-.558-2.913-.306-.789-.717-1.459-1.384-2.126s-1.335-1.078-2.126-1.384c-.765-.297-1.636-.499-2.913-.558-1.28-.06-1.687-.072-4.947-.072zM12 7c2.761 0 5 2.239 5 5s-2.239 5-5 5-5-2.239-5-5 2.239-5 5-5zm0 2a3 3 0 100 6 3 3 0 000-6zm6.25-4.75a1 1 0 110 2 1 1 0 010-2z', 
                                    'color' => 'pink',
                                    'url' => '#'
                                ],
                                [
                                    'name' => 'TikTok',
                                    'icon' => 'M12 2a1 1 0 0 0-1 1v12.5a1.5 1.5 0 1 1-3-1.5c.01 0 .02 0 .03 0V11a4.5 4.5 0 1 0 5.47 5.47V7a7.5 7.5 0 0 0 5.5 2.5v-3a4.5 4.5 0 0 1-4-2V2h-2z', 
                                    'color' => 'gray',
                                    'url' => '#'
                                ],
                                [
                                    'name' => 'WhatsApp',
                                    'icon' => 'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z', 
                                    'color' => 'green',
                                    'url' => '#'
                                ],
                            ];
                        @endphp
                        @foreach($socials as $social)
                            <a href="{{ $social['url'] }}" 
                               title="{{ $social['name'] }}"
                               class="w-12 h-12 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-{{ $social['color'] }}-50 dark:hover:bg-{{ $social['color'] }}-900/20 hover:text-{{ $social['color'] }}-600 dark:hover:text-{{ $social['color'] }}-400 transition-all duration-300 transform hover:-translate-y-1 shadow-sm">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="{{ $social['icon'] }}"/></svg>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Footer Nav Sections -->
                <div>
                    <h4 class="text-xs font-black uppercase tracking-[0.2em] text-gray-900 dark:text-white mb-8 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                        Plateforme
                    </h4>
                    <ul class="space-y-4">
                        <li>
                            <a href="/" class="group flex items-center gap-2 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-all">
                                <span class="w-0 group-hover:w-2 h-0.5 bg-red-600 transition-all duration-300"></span>
                                Accueil
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('explore.plats') }}" class="group flex items-center gap-2 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-all">
                                <span class="w-0 group-hover:w-2 h-0.5 bg-red-600 transition-all duration-300"></span>
                                Produits
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('vendors.map') }}" class="group flex items-center gap-2 text-sm font-bold text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 transition-all">
                                <span class="w-0 group-hover:w-2 h-0.5 bg-orange-600 transition-all duration-300"></span>
                                Autour de moi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('delivery.index') }}" class="group flex items-center gap-2 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-all">
                                <span class="w-0 group-hover:w-2 h-0.5 bg-red-600 transition-all duration-300"></span>
                                Devenir Livreur
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('vendor.apply') }}" class="group flex items-center gap-2 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-all">
                                <span class="w-0 group-hover:w-2 h-0.5 bg-red-600 transition-all duration-300"></span>
                                Devenir Partenaire 
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-xs font-black uppercase tracking-[0.2em] text-gray-900 dark:text-white mb-8 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                        Aide & Plus
                    </h4>
                    <ul class="space-y-4">
                        <li>
                            <a href="#" class="group flex items-center gap-2 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-all">
                                <span class="w-0 group-hover:w-2 h-0.5 bg-red-600 transition-all duration-300"></span>
                                Nos FAQ
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('terms') }}" class="group flex items-center gap-2 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-all">
                                <span class="w-0 group-hover:w-2 h-0.5 bg-red-600 transition-all duration-300"></span>
                                Conditions d'Utilisation
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('privacy') }}" class="group flex items-center gap-2 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-all">
                                <span class="w-0 group-hover:w-2 h-0.5 bg-red-600 transition-all duration-300"></span>
                                Confidentialité
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Newsletter Column (Normal styling) -->
                <div class="sm:col-span-2 lg:col-span-1">
                    <div class="space-y-6">
                        <h4 class="text-xs font-black uppercase tracking-[0.2em] text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            Newsletter
                        </h4>
                        <div class="space-y-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 leading-relaxed">
                                Savourez nos offres exclusives chaque semaine directement dans votre boîte mail.
                            </p>
                            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-3">
                                @csrf
                                <div class="relative group">
                                    <input type="email" name="email" placeholder="Votre email" required
                                           class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl text-sm font-bold text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-red-500 transition-all outline-none">
                                </div>
                                <button type="submit" class="w-full py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl hover:bg-black dark:hover:bg-gray-200 transition-all font-black text-[10px] uppercase tracking-[0.2em] shadow-lg active:scale-[0.98]">
                                    Souscrire
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="mt-20 pt-10 border-t border-gray-50 dark:border-gray-800 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-300 dark:text-gray-600 text-center md:text-left order-2 md:order-1">
                    &copy; {{ date('Y') }} {{ $siteName }}. Tous droits réservés.
                </p>
                <div class="flex gap-8 order-1 md:order-2">
                    <span class="text-[10px] font-black text-gray-300 dark:text-gray-600 uppercase tracking-widest">Cotonou</span>
                    <span class="text-[10px] font-black text-gray-300 dark:text-gray-600 uppercase tracking-widest">Abidjan</span>
                    <span class="text-[10px] font-black text-gray-300 dark:text-gray-600 uppercase tracking-widest">Lomel</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bottom Navigation Bar (Mobile) - App-like Design -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50" x-data="{ activeTab: '{{ request()->is('/') ? 'home' : (request()->is('produits*') ? 'produits' : (request()->is('explore*') ? 'explore' : (request()->routeIs('orders.track') ? 'track' : (request()->is('orders*') || request()->routeIs('orders.*') ? 'orders' : 'account')))) }}' }">
        <!-- Safe area spacer for iOS -->
        <div class="h-safe-area-inset-bottom bg-white/95 dark:bg-gray-950/95"></div>
        
        <!-- Main Navigation Container -->
        <div class="bg-white/95 dark:bg-gray-950/95 backdrop-blur-2xl border-t border-gray-100/50 dark:border-gray-800/50 shadow-2xl shadow-black/10">
            <div class="relative flex justify-between items-end px-2 pt-2 pb-2">
                
                <!-- Home -->
                <a href="/" class="relative flex-1 flex flex-col items-center justify-center py-1.5 group transition-all duration-300">
                    <div class="relative">
                        <div class="{{ request()->is('/') ? 'bg-red-500' : 'bg-transparent' }} absolute -inset-1.5 rounded-xl transition-all duration-300 {{ request()->is('/') ? 'opacity-20' : 'opacity-0 group-active:opacity-10 group-active:bg-red-500' }}"></div>
                        <svg class="relative w-5 h-5 transition-all duration-300 {{ request()->is('/') ? 'text-red-600 dark:text-red-500 scale-110' : 'text-gray-400 dark:text-gray-500' }}" fill="{{ request()->is('/') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->is('/') ? '0' : '1.5' }}" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <span class="mt-0.5 text-[8px] font-bold transition-colors duration-300 {{ request()->is('/') ? 'text-red-600 dark:text-red-500' : 'text-gray-400 dark:text-gray-500' }}">Accueil</span>
                    @if(request()->is('/'))
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-1 h-1 bg-red-600 rounded-full"></div>
                    @endif
                </a>

                <!-- Produit/Search -->
                <a href="{{ route('explore.plats') }}" class="relative flex-1 flex flex-col items-center justify-center py-1.5 group transition-all duration-300">
                    <div class="relative">
                        <div class="{{ request()->is('produits*') ? 'bg-red-500' : 'bg-transparent' }} absolute -inset-1.5 rounded-xl transition-all duration-300 {{ request()->is('produits*') ? 'opacity-20' : 'opacity-0 group-active:opacity-10 group-active:bg-red-500' }}"></div>
                        <svg class="relative w-5 h-5 transition-all duration-300 {{ request()->is('produits*') ? 'text-red-600 dark:text-red-500 scale-110' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->is('produits*') ? '2.5' : '1.5' }}" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <span class="mt-0.5 text-[8px] font-bold transition-colors duration-300 {{ request()->is('produits*') ? 'text-red-600 dark:text-red-500' : 'text-gray-400 dark:text-gray-500' }}">Produits</span>
                    @if(request()->is('produits*'))
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-1 h-1 bg-red-600 rounded-full"></div>
                    @endif
                </a>

                <!-- Cart Button (Central - Elevated) -->
                <div class="relative -mt-6 -translate-y-4 z-10 flex-1 flex justify-center">
                    <a href="{{ route('cart.index') }}" class="relative flex items-center justify-center w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 text-white rounded-2xl shadow-xl shadow-red-500/30 transform active:scale-90 transition-all duration-300 group">
                        <!-- Animated ring on hover -->
                        <div class="absolute inset-0 rounded-2xl border-2 border-white/20 group-active:border-white/40 transition-colors"></div>
                        <!-- Cart Icon -->
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <!-- Cart Count Badge -->
                        <template x-if="cartCount > 0">
                            <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-white text-red-600 text-[10px] font-black flex items-center justify-center rounded-full shadow-lg border-2 border-red-500" x-text="cartCount"></span>
                        </template>
                        <!-- Pulse effect when cart has items -->
                        <template x-if="cartCount > 0">
                            <div class="absolute inset-0 rounded-2xl bg-red-400 animate-ping opacity-20"></div>
                        </template>
                    </a>
                    <span class="absolute -bottom-8 block text-center mt-1 text-[8px] font-bold text-gray-400 dark:text-gray-500">Panier</span>
                </div>

                <!-- Track Order (Suivi) -->
                <a href="{{ route('orders.track') }}" class="relative flex-1 flex flex-col items-center justify-center py-1.5 group transition-all duration-300">
                    <div class="relative">
                        <div class="{{ request()->routeIs('orders.track') ? 'bg-orange-500' : 'bg-transparent' }} absolute -inset-1.5 rounded-xl transition-all duration-300 {{ request()->routeIs('orders.track') ? 'opacity-20' : 'opacity-0 group-active:opacity-10 group-active:bg-orange-500' }}"></div>
                        <svg class="relative w-5 h-5 transition-all duration-300 {{ request()->routeIs('orders.track') ? 'text-orange-600 dark:text-orange-500 scale-110' : 'text-gray-400 dark:text-gray-500' }}" fill="{{ request()->routeIs('orders.track') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('orders.track') ? '0' : '1.5' }}" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('orders.track') ? '0' : '1.5' }}" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span class="mt-0.5 text-[8px] font-bold transition-colors duration-300 {{ request()->routeIs('orders.track') ? 'text-orange-600 dark:text-orange-500' : 'text-gray-400 dark:text-gray-500' }}">Suivi</span>
                    @if(request()->routeIs('orders.track'))
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-1 h-1 bg-orange-600 rounded-full"></div>
                    @endif
                </a>

                <!-- Profile/Account with Dropdown -->
                @auth
                <div class="relative flex-1 flex justify-center" x-data="{ accountMenu: false }">
                    <!-- Account Button -->
                    <button @click="accountMenu = !accountMenu" class="relative flex flex-col items-center justify-center w-full py-1.5 group transition-all duration-300">
                        <div class="relative">
                            <div class="{{ request()->url() === $dashboardRoute ? 'bg-red-500' : 'bg-transparent' }} absolute -inset-1.5 rounded-xl transition-all duration-300 {{ request()->url() === $dashboardRoute ? 'opacity-20' : 'opacity-0 group-active:opacity-10 group-active:bg-red-500' }}"></div>
                            <div class="relative">
                                <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=ef4444&color=fff&bold=true&size=64' }}" 
                                     class="w-6 h-6 rounded-lg object-cover ring-2 transition-all duration-300 {{ request()->url() === $dashboardRoute ? 'ring-red-500 scale-110' : 'ring-transparent' }}"
                                     alt="{{ auth()->user()->name }}">
                                <!-- Online indicator -->
                                <div class="absolute -bottom-0.5 -right-0.5 w-2 h-2 bg-green-500 border-2 border-white dark:border-gray-950 rounded-full"></div>
                            </div>
                        </div>
                        <span class="mt-0.5 text-[8px] font-bold transition-colors duration-300 {{ request()->url() === $dashboardRoute ? 'text-red-600 dark:text-red-500' : 'text-gray-400 dark:text-gray-500' }}">Compte</span>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="accountMenu" 
                         @click.away="accountMenu = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                         class="absolute bottom-full right-0 mb-3 w-56 bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 overflow-hidden z-[60]"
                         x-cloak>
                        
                        <!-- User Info Header -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                            <div class="flex items-center gap-3">
                                <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=ef4444&color=fff&bold=true&size=64' }}" 
                                     class="w-10 h-10 rounded-xl object-cover"
                                     alt="{{ auth()->user()->name }}">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-[10px] text-gray-400 truncate">{{ auth()->user()->email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Items -->
                        <div class="p-2">
                            <!-- Dashboard -->
                            <a href="{{ $dashboardRoute }}" @click="accountMenu = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <div class="w-8 h-8 bg-red-50 dark:bg-red-900/20 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                </div>
                                <span class="text-sm font-semibold">Tableau de bord</span>
                            </a>

                            <!-- My Orders -->
                            <a href="{{ route('orders.index') }}" @click="accountMenu = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <div class="w-8 h-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                </div>
                                <span class="text-sm font-semibold">Mes commandes</span>
                            </a>

                            <!-- Profile -->
                            <a href="{{ route('profile.edit') }}" @click="accountMenu = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <div class="w-8 h-8 bg-purple-50 dark:bg-purple-900/20 rounded-lg flex items-center justify-center text-purple-600 dark:text-purple-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <span class="text-sm font-semibold">Mon profil</span>
                            </a>

                            <!-- Favorites -->
                            <a href="{{ route('favoris.index') }}" @click="accountMenu = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <div class="w-8 h-8 bg-pink-50 dark:bg-pink-900/20 rounded-lg flex items-center justify-center text-pink-600 dark:text-pink-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                </div>
                                <span class="text-sm font-semibold">Mes favoris</span>
                            </a>
                        </div>

                        <!-- Vendor Section -->
                        @if(auth()->user()->isVendor())
                        <div class="p-2 border-t border-gray-100 dark:border-gray-800">
                            <div class="px-3 py-1.5 text-[9px] font-black uppercase tracking-widest text-gray-400">Espace Vendeur</div>
                            <a href="{{ route('vendeur.dashboard') }}" @click="accountMenu = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                                <div class="w-8 h-8 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <span class="text-sm font-semibold">Ma boutique</span>
                            </a>
                            <a href="{{ vendor_route('vendeur.slug.orders.index') }}" @click="accountMenu = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                                <div class="w-8 h-8 bg-green-50 dark:bg-green-900/20 rounded-lg flex items-center justify-center text-green-600 dark:text-green-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                </div>
                                <span class="text-sm font-semibold">Commandes reçues</span>
                            </a>
                        </div>
                        @endif

                        <!-- Admin Section -->
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
                        <div class="p-2 border-t border-gray-100 dark:border-gray-800">
                            <div class="px-3 py-1.5 text-[9px] font-black uppercase tracking-widest text-gray-400">Administration</div>
                            <a href="{{ route('admin.dashboard') }}" @click="accountMenu = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <div class="w-8 h-8 bg-red-50 dark:bg-red-900/20 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <span class="text-sm font-semibold">Panel Admin</span>
                            </a>
                        </div>
                        @endif

                        <!-- Delivery Section -->
                        @if(auth()->user()->isDriver())
                        <div class="p-2 border-t border-gray-100 dark:border-gray-800">
                            <a href="{{ route('delivery.my-deliveries') }}" @click="accountMenu = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 transition-colors">
                                <div class="w-8 h-8 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg flex items-center justify-center text-cyan-600 dark:text-cyan-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                </div>
                                <span class="text-sm font-semibold">Espace Livreur</span>
                            </a>
                        </div>
                        @endif

                        <!-- Logout -->
                        <div class="p-2 border-t border-gray-100 dark:border-gray-800">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" @click="accountMenu = false" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <div class="w-8 h-8 bg-red-50 dark:bg-red-900/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    </div>
                                    <span class="text-sm font-bold">Déconnexion</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                <a href="{{ route('login') }}" class="relative flex-1 flex flex-col items-center justify-center py-1.5 group transition-all duration-300">
                    <div class="relative">
                        <div class="{{ request()->is('login') ? 'bg-red-500' : 'bg-transparent' }} absolute -inset-1.5 rounded-xl transition-all duration-300 {{ request()->is('login') ? 'opacity-20' : 'opacity-0 group-active:opacity-10 group-active:bg-red-500' }}"></div>
                        <svg class="relative w-5 h-5 transition-all duration-300 {{ request()->is('login') ? 'text-red-600 dark:text-red-500 scale-110' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <span class="mt-0.5 text-[8px] font-bold transition-colors duration-300 {{ request()->is('login') ? 'text-red-600 dark:text-red-500' : 'text-gray-400 dark:text-gray-500' }}">Connexion</span>
                    @if(request()->is('login'))
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-1 h-1 bg-red-600 rounded-full"></div>
                    @endif
                </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Mobile Bottom Safe Area Padding -->
    <div class="lg:hidden h-24"></div>

    <style>
        /* iOS Safe Area Support */
        @supports (padding-bottom: env(safe-area-inset-bottom)) {
            .h-safe-area-inset-bottom {
                height: env(safe-area-inset-bottom);
            }
        }
        
        /* Subtle bounce animation for cart badge */
        @keyframes bounce-subtle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-2px); }
        }
        .animate-bounce-subtle {
            animation: bounce-subtle 2s infinite;
        }
        
        /* Hide scrollbar when mobile nav is present */
        @media (max-width: 1023px) {
            body {
                padding-bottom: env(safe-area-inset-bottom, 0);
            }
        }
    </style>

    @yield('scripts')
    
    <!-- Google Maps API -->
    @if(env('GOOGLE_MAPS_API_KEY'))
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
    @endif
</body>
</html>
