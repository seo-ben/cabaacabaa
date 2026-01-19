<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php
        // Variables are now shared globally via AppServiceProvider to ensure persistence across sessions and views.
        // $siteLogo, $siteName, $siteFavicon etc. are available.
        $finalLogo = $siteLogo ?? asset('assets/logo/logo-cabaa.png');
        $finalFavicon = $siteFavicon ?? null;
    @endphp

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Meta Tags Dynamiques -->
    <title>@yield('title', 'Admin') - {{ \App\Models\AppSetting::get('meta_title', $siteName) }}</title>
    <meta name="description" content="{{ \App\Models\AppSetting::get('meta_description') }}">
    <meta name="keywords" content="{{ \App\Models\AppSetting::get('meta_keywords') }}">

    @if($finalFavicon)
        <link rel="icon" type="image/x-icon" href="{{ $finalFavicon }}">
    @endif



    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <!-- Alpine.js Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Alpine.js Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom Scrollbar for Premium Feel */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f9fafb; }
        ::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex" 
         x-data="{ 
            sidebarOpen: true, 
            mobileMenuOpen: false,
            notifications: [],
            unreadCount: 0,
            
            init() {
                this.fetchNotifications();
                setInterval(() => this.fetchNotifications(), 30000); // Poll every 30 seconds
            },

            fetchNotifications() {
                fetch('{{ route('notifications.unread') }}')
                    .then(response => response.json())
                    .then(data => {
                        this.notifications = data;
                        this.unreadCount = data.length;
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
            }
         }">

        <!-- Sidebar Desktop -->
        <aside
            :class="sidebarOpen ? 'w-64' : 'w-20'"
            class="bg-white border-r border-gray-200 hidden lg:flex flex-col transition-all duration-300 fixed h-screen z-30"
        >
            <!-- Logo & Toggle -->
            <div class="h-16 flex items-center justify-between px-4 border-b border-gray-200">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3" x-show="sidebarOpen">
                    @if($finalLogo)
                        <img src="{{ $finalLogo }}" alt="{{ $siteName }}" class="w-10 h-10 object-contain">
                    @else
                        <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-orange-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    @endif
                    <span class="text-xl font-bold bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent truncate">
                        {{ $siteName }}
                    </span>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4 px-2">
                
                @can('view_dashboard')
                <!-- Tableau de bord -->
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-600 transition group {{ request()->routeIs('admin.dashboard') ? 'bg-red-50 text-red-600' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span x-show="sidebarOpen" class="font-medium">Tableau de bord</span>
                </a>
                @endcan

                @can('view_vendors')
                <!-- Gestion Vendeurs -->
                <div class="mt-4" x-data="{ open: {{ request()->routeIs('admin.vendors*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 mb-1 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('admin.vendors*') ? 'bg-red-50 text-red-600' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span x-show="sidebarOpen" class="font-medium">Vendeurs</span>
                        </div>
                        <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse class="ml-8 space-y-1">
                        <a href="{{ route('admin.vendors.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-red-600 rounded-lg hover:bg-red-50 transition {{ request()->routeIs('admin.vendors.index') ? 'text-red-600 bg-red-50' : '' }}">Liste vendeurs</a>
                        <a href="{{ route('admin.vendors.index') }}?status=pending" class="block px-3 py-2 text-sm text-gray-600 hover:text-red-600 rounded-lg hover:bg-red-50 transition">En attente</a>
                        <a href="{{ route('admin.vendors.index') }}?status=active" class="block px-3 py-2 text-sm text-gray-600 hover:text-red-600 rounded-lg hover:bg-red-50 transition">Actifs</a>
                        <a href="{{ route('admin.vendors.index') }}?status=suspended" class="block px-3 py-2 text-sm text-gray-600 hover:text-red-600 rounded-lg hover:bg-red-50 transition">Suspendus</a>
                    </div>
                </div>
                @endcan

                @can('view_orders')
                <!-- Gestion Commandes -->
                <div class="mt-1" x-data="{ open: {{ request()->routeIs('admin.orders*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 mb-1 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span x-show="sidebarOpen" class="font-medium">Commandes</span>
                        </div>
                        <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse class="ml-8 space-y-1">
                        <a href="{{ route('admin.orders.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-red-600 rounded-lg hover:bg-red-50 transition {{ request()->routeIs('admin.orders.index') && !request('status') ? 'text-red-600 bg-red-50' : '' }}">Toutes</a>
                        <a href="{{ route('admin.orders.index', ['status' => 'en_attente']) }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-red-600 rounded-lg hover:bg-red-50 transition {{ request('status') == 'en_attente' ? 'text-red-600 bg-red-50' : '' }}">En cours</a>
                        <a href="{{ route('admin.orders.index', ['status' => 'termine']) }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-red-600 rounded-lg hover:bg-red-50 transition {{ request('status') == 'termine' ? 'text-red-600 bg-red-50' : '' }}">Terminées</a>
                        <a href="{{ route('admin.orders.index', ['status' => 'annule']) }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-red-600 rounded-lg hover:bg-red-50 transition {{ request('status') == 'annule' ? 'text-red-600 bg-red-50' : '' }}">Annulées</a>
                    </div>
                </div>
                @endcan

                @can('view_users')
                <!-- Gestion Utilisateurs -->
                <div class="mt-1" x-data="{ open: {{ request()->routeIs('admin.users*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 mb-1 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('admin.users*') ? 'bg-red-50 text-red-600' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span x-show="sidebarOpen" class="font-medium">Utilisateurs</span>
                        </div>
                        <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse class="ml-8 space-y-1">
                        <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-red-600 rounded-lg hover:bg-red-50 transition {{ request()->routeIs('admin.users.index') ? 'text-red-600 bg-red-50' : '' }}">Liste utilisateurs</a>
                        <a href="{{ route('admin.users.create') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-red-600 rounded-lg hover:bg-red-50 transition {{ request()->routeIs('admin.users.create') ? 'text-red-600 bg-red-50' : '' }}">Ajouter un utilisateur</a>
                        @can('manage_admins')
                        <a href="{{ route('admin.admins.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-purple-600 rounded-lg hover:bg-purple-50 transition {{ request()->routeIs('admin.admins*') ? 'text-purple-600 bg-purple-50' : '' }}">Administrateurs</a>
                        @endcan
                    </div>
                </div>
                @endcan

                @can('manage_products')
                <!-- Gestion Articles -->
                <a href="{{ route('admin.plats.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-600 transition {{ request()->routeIs('admin.plats*') ? 'bg-red-50 text-red-600 font-bold' : '' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.plats*') ? 'text-red-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span x-show="sidebarOpen" class="font-medium">Articles</span>
                </a>
                @endcan

                @can('manage_categories')
                <!-- Catégories Articles -->
                <a href="{{ route('admin.categories.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('admin.categories*') ? 'bg-red-50 text-red-600' : '' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.categories*') ? 'text-red-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span x-show="sidebarOpen" class="font-medium">Catégories Articles</span>
                </a>
                @endcan

                @can('view_vendors')
                <!-- Avis & Évaluations -->
                <a href="{{-- route('admin.reviews.index') --}}"
                   class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('admin.reviews*') ? 'bg-gray-100' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    <span x-show="sidebarOpen" class="font-medium">Avis</span>
                </a>
                @endcan

                @can('manage_zones')
                <!-- Zones & Localisation -->
                <a href="{{ route('admin.zones.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('admin.zones*') ? 'bg-red-50 text-red-600' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span x-show="sidebarOpen" class="font-medium">Zones</span>
                </a>
                @endcan

                @can('view_vendor_categories')
                <!-- Catégories Boutiques -->
                <a href="{{ route('admin.vendor-categories.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('admin.vendor-categories*') ? 'bg-orange-50 text-orange-600' : '' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.vendor-categories*') ? 'text-orange-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span x-show="sidebarOpen" class="font-medium">Catégories Boutiques</span>
                </a>
                @endcan

                @can('manage_products')
                <!-- Promotions & Mise en avant -->
                <a href="{{-- route('admin.promotions.index') --}}"
                   class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('admin.promotions*') ? 'bg-gray-100' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span x-show="sidebarOpen" class="font-medium">Promotions</span>
                </a>
                @endcan

                @can('view_finance')
                <!-- Paiements & Transactions -->
                <a href="{{ route('admin.finance.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('admin.finance*') ? 'bg-red-50 text-red-600' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <span x-show="sidebarOpen" class="font-medium">Finance</span>
                </a>
                @endcan

                @can('view_dashboard')
                <!-- Statistiques -->
                <a href="{{-- route('admin.analytics.index') --}}"
                   class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('admin.analytics*') ? 'bg-gray-100' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span x-show="sidebarOpen" class="font-medium">Statistiques</span>
                </a>
                @endcan

                <div class="my-4 border-t border-gray-200"></div>

                @can('view_security')
                <!-- Sécurité -->
                <a href="{{ route('admin.security.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('admin.security*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.security*') ? 'text-indigo-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span x-show="sidebarOpen" class="font-medium">Sécurité</span>
                </a>
                @endcan

                @can('manage_settings')
                <!-- Paramètres -->
                <div x-data="{ open: {{ request()->routeIs('admin.settings*') || request()->routeIs('admin.countries*') ? 'true' : 'false' }} }" class="mb-1">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('admin.settings*') || request()->routeIs('admin.countries*') ? 'bg-red-50 text-red-600' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span x-show="sidebarOpen" class="font-medium">Paramètres</span>
                        </div>
                        <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div x-show="open && sidebarOpen" x-cloak class="mt-1 ml-9 space-y-1">
                        <a href="{{ route('admin.settings.index') }}" 
                           class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('admin.settings*') ? 'text-red-600 font-bold' : 'text-gray-600 hover:text-red-600 hover:bg-red-50' }}">
                            Configuration Générale
                        </a>
                        <a href="{{ route('admin.countries.index') }}" 
                           class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('admin.countries*') ? 'text-red-600 font-bold' : 'text-gray-600 hover:text-red-600 hover:bg-red-50' }}">
                            Pays & Indicatifs
                        </a>
                    </div>
                </div>
                @endcan
            </nav>

            <!-- User Profile -->
            <div class="border-t border-gray-200 p-4">
                <div class="flex items-center gap-3" x-show="sidebarOpen">
                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name) }}"
                         class="w-10 h-10 rounded-full border-2 border-red-200"
                         alt="Admin">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">Administrateur</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-2" x-show="sidebarOpen">
                    @csrf
                    <button type="submit" class="w-full px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition text-left">
                        Déconnexion
                    </button>
                </form>
            </div>
        </aside>

        <!-- Mobile Sidebar -->
        <div x-show="mobileMenuOpen"
             @click="mobileMenuOpen = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden">
        </div>

        <aside x-show="mobileMenuOpen"
               x-transition:enter="transition ease-out duration-300 transform"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-200 transform"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 z-50 lg:hidden overflow-y-auto">
            <!-- Mobile menu content (same as desktop sidebar) -->
        </aside>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 transition-all duration-300" :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'">
            <!-- Top Header -->
            <header class="bg-white border-b border-gray-200 sticky top-0 z-20">
                <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    <button @click="mobileMenuOpen = true" class="lg:hidden p-2 hover:bg-gray-100 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <div class="flex-1 max-w-md mx-4">
                        <div class="relative">
                            <input type="search"
                                   placeholder="Rechercher..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open; if(open) markNotificationsRead()" 
                                    class="relative p-2 hover:bg-gray-100 rounded-lg">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <template x-if="unreadCount > 0">
                                    <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full"></span>
                                </template>
                            </button>

                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50 overflow-hidden"
                                 x-cloak>
                                
                                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                                    <h3 class="text-sm font-bold text-gray-900">Notifications</h3>
                                    <span class="px-2 py-0.5 bg-red-50 text-red-600 rounded text-[10px] font-bold" x-text="unreadCount + ' nouvelles'"></span>
                                </div>

                                <div class="max-h-96 overflow-y-auto">
                                    <template x-for="notif in notifications" :key="notif.id_notification">
                                        <div class="p-4 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0 cursor-pointer">
                                            <div class="flex gap-3">
                                                <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center text-orange-600 shrink-0">
                                                    <svg x-show="notif.type_notification === 'nouvel_utilisateur'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                    <svg x-show="notif.type_notification === 'demande_vendeur'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                    <svg x-show="notif.type_notification === 'nouvelle_commande'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                                    <svg x-show="!['nouvel_utilisateur', 'demande_vendeur', 'nouvelle_commande'].includes(notif.type_notification)" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-bold text-gray-900 truncate" x-text="notif.titre"></p>
                                                    <p class="text-[11px] text-gray-500 leading-tight" x-text="notif.message"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="notifications.length === 0">
                                        <div class="p-8 text-center text-gray-400">
                                            <p class="text-xs italic">Aucune notification en attente</p>
                                        </div>
                                    </template>
                                </div>

                                <a href="#" class="block p-3 bg-gray-50 text-center text-[11px] font-bold text-gray-600 hover:text-red-600 transition-colors">
                                    Tout voir
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-6">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Core Scripts -->
</body>
</html>
