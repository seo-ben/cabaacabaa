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
            activeDropdown: '{{ request()->routeIs('admin.vendors*') ? 'vendors' : (request()->routeIs('admin.orders*') ? 'orders' : (request()->routeIs('admin.users*') ? 'users' : (request()->routeIs('admin.settings*') || request()->routeIs('admin.countries*') ? 'settings' : ''))) }}',
            notifications: [],
            unreadCount: 0,
            
            init() {
                this.fetchNotifications();
                setInterval(() => this.fetchNotifications(), 30000); // Poll every 30 seconds

                // Use single quotes for JS strings to avoid breaking x-data double quotes
                @if(session('success'))
                    setTimeout(() => this.showToast('{{ addslashes(session('success')) }}', 'success'), 500);
                @endif
                @if(session('error'))
                    setTimeout(() => this.showToast('{{ addslashes(session('error')) }}', 'error'), 500);
                @endif
                @if(session('warning'))
                    setTimeout(() => this.showToast('{{ addslashes(session('warning')) }}', 'warning'), 500);
                @endif
                @if($errors->any())
                    setTimeout(() => this.showToast('Veuillez corriger les erreurs dans le formulaire.', 'error'), 500);
                @endif
            },

            showModal: false,
            modalTitle: '',
            modalMessage: '',
            modalType: 'success', // success, error, warning

            showToast(message, type = 'success') {
                this.modalMessage = message;
                this.modalType = type;
                this.modalTitle = type === 'success' ? 'Succès' : (type === 'error' ? 'Erreur' : 'Attention');
                this.showModal = true;
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
            class="bg-white border-r border-gray-100 hidden lg:flex flex-col transition-all duration-500 ease-in-out fixed h-screen z-30 shadow-[4px_0_24px_rgba(0,0,0,0.02)]"
        >
            <!-- Logo & Toggle -->
            <div class="h-16 flex items-center justify-between px-5 border-b border-gray-50 bg-white/50 backdrop-blur-md sticky top-0 z-10 transition-all duration-500" :class="sidebarOpen ? 'px-5' : 'px-4 justify-center'">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 overflow-hidden transition-all duration-500" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    @if($finalLogo)
                        <img src="{{ $finalLogo }}" alt="{{ $siteName }}" class="w-8 h-8 object-contain drop-shadow-sm">
                    @endif
                    <span class="text-lg font-black bg-linear-to-r from-red-600 via-orange-500 to-red-600 bg-clip-text text-transparent tracking-tighter whitespace-nowrap">
                        {{ $siteName }}
                    </span>
                </a>
                <button @click="sidebarOpen = !sidebarOpen; if(!sidebarOpen) activeDropdown = null" class="p-2 hover:bg-gray-50 rounded-lg transition-all duration-300 group shadow-sm bg-white border border-gray-100 hover:border-red-100 active:scale-90" :class="!sidebarOpen ? 'mx-auto' : ''">
                    <svg class="w-4 h-4 text-gray-500 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" :class="!sidebarOpen ? 'rotate-180' : ''">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4 px-2">
                
                @can('view_dashboard')
                <!-- Tableau de bord -->
                <div class="px-1.5">
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-xl transition-all duration-300 group relative overflow-hidden active:scale-95"
                       :class="{{ request()->routeIs('admin.dashboard') ? 'true' : 'false' }} ? 'bg-red-600 text-white shadow-lg shadow-red-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 border border-transparent'">
                        
                        @if(request()->routeIs('admin.dashboard'))
                            <div class="absolute inset-0 bg-linear-to-r from-red-600 to-orange-500 opacity-100"></div>
                        @endif

                        <div class="relative z-10 w-7 h-7 flex items-center justify-center rounded-lg transition-all duration-300"
                             :class="{{ request()->routeIs('admin.dashboard') ? 'true' : 'false' }} ? 'bg-white/20' : 'bg-gray-50 text-gray-400 group-hover:bg-gray-100 group-hover:text-gray-600'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </div>
                        <span x-show="sidebarOpen" class="relative z-10 font-black text-[10px] uppercase tracking-widest" x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">Dashboard</span>
                    </a>
                </div>
                @endcan

                @can('view_vendors')
                @php $pendingCount = \App\Models\Vendeur::where('statut_verification', 'en_cours')->count(); @endphp
                <!-- Gestion Vendeurs -->
                <div class="px-1.5">
                    <button @click="activeDropdown = activeDropdown === 'vendors' ? null : 'vendors'" 
                            class="w-full flex items-center justify-between px-3 py-2.5 mb-1 rounded-xl transition-all duration-300 group relative overflow-hidden active:scale-95"
                            :class="activeDropdown === 'vendors' ? 'bg-red-50/50 text-red-600 shadow-sm border border-red-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 border border-transparent'">
                        
                        <div class="flex items-center gap-3 relative z-10">
                            <div class="w-7 h-7 flex items-center justify-center rounded-lg transition-all duration-300"
                                 :class="activeDropdown === 'vendors' ? 'bg-red-600 text-white shadow-md shadow-red-200' : 'bg-gray-50 text-gray-400 group-hover:bg-gray-100 group-hover:text-gray-600'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <span x-show="sidebarOpen" class="font-black text-[10px] uppercase tracking-widest" x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">Vendeurs</span>
                        </div>
                        
                        <svg x-show="sidebarOpen" :class="activeDropdown === 'vendors' ? 'rotate-180' : ''" class="w-3 h-3 transition-transform duration-300 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div x-show="activeDropdown === 'vendors' && sidebarOpen" x-collapse class="space-y-0.5 my-1 pr-1.5">
                        <a href="{{ route('admin.vendors.index') }}" 
                           class="flex items-center gap-2.5 ml-10 px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all duration-200 border-l-2 {{ request()->routeIs('admin.vendors.index') && !request('status') ? 'text-red-600 border-red-600 bg-red-50/30' : 'text-gray-400 border-transparent hover:text-red-500 hover:bg-red-50/20 hover:border-red-200' }}">
                            <span class="w-1 h-1 rounded-full bg-current opacity-40"></span>
                            Liste complète
                        </a>
                        <a href="{{ route('admin.vendors.index') }}?status=en_cours" 
                           class="flex items-center gap-2.5 ml-10 px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all duration-200 border-l-2 {{ request('status') == 'en_cours' ? 'text-orange-600 border-orange-600 bg-orange-50/30' : 'text-gray-400 border-transparent hover:text-orange-500 hover:bg-orange-50/20 hover:border-orange-200' }}">
                            <span class="w-1 h-1 rounded-full bg-current opacity-40"></span>
                            En attente
                            @if($pendingCount > 0)
                                <span class="ml-auto px-1 py-0.5 bg-orange-500 text-white rounded text-[7px] animate-pulse">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>
                @endcan

                @can('view_orders')
                <!-- Gestion Commandes -->
                <div class="px-1.5">
                    <button @click="activeDropdown = activeDropdown === 'orders' ? null : 'orders'" 
                            class="w-full flex items-center justify-between px-3 py-2.5 mb-1 rounded-xl transition-all duration-300 group relative overflow-hidden active:scale-95"
                            :class="activeDropdown === 'orders' ? 'bg-red-50/50 text-red-600 shadow-sm border border-red-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 border border-transparent'">
                        
                        <div class="flex items-center gap-3 relative z-10">
                            <div class="w-7 h-7 flex items-center justify-center rounded-lg transition-all duration-300"
                                 :class="activeDropdown === 'orders' ? 'bg-red-600 text-white shadow-md shadow-red-200' : 'bg-gray-50 text-gray-400 group-hover:bg-gray-100 group-hover:text-gray-600'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2-2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <span x-show="sidebarOpen" class="font-black text-[10px] uppercase tracking-widest" x-transition:enter="transition ease-out duration-300 delay-100">Commandes</span>
                        </div>
                        
                        <svg x-show="sidebarOpen" :class="activeDropdown === 'orders' ? 'rotate-180' : ''" class="w-3 h-3 transition-transform duration-300 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div x-show="activeDropdown === 'orders' && sidebarOpen" x-collapse class="space-y-0.5 my-1 pr-1.5">
                        <a href="{{ route('admin.orders.index') }}" 
                           class="flex items-center gap-2.5 ml-10 px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all duration-200 border-l-2 {{ request()->routeIs('admin.orders.index') && !request('status') ? 'text-red-600 border-red-600 bg-red-50/30' : 'text-gray-400 border-transparent hover:text-red-500 hover:bg-red-50/20 hover:border-red-200' }}">
                            <span class="w-1 h-1 rounded-full bg-current opacity-40"></span>
                            Toutes
                        </a>
                        <a href="{{ route('admin.orders.index', ['status' => 'en_attente']) }}" 
                           class="flex items-center gap-2.5 ml-10 px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all duration-200 border-l-2 {{ request('status') == 'en_attente' ? 'text-orange-600 border-orange-600 bg-orange-50/30' : 'text-gray-400 border-transparent hover:text-orange-500 hover:bg-orange-50/20 hover:border-orange-200' }}">
                            <span class="w-1 h-1 rounded-full bg-current opacity-40"></span>
                            En cours
                        </a>
                    </div>
                </div>
                @endcan

                @can('view_users')
                <!-- Gestion Utilisateurs -->
                <div class="px-1.5">
                    <button @click="activeDropdown = activeDropdown === 'users' ? null : 'users'" 
                            class="w-full flex items-center justify-between px-3 py-2.5 mb-1 rounded-xl transition-all duration-300 group relative overflow-hidden active:scale-95"
                            :class="activeDropdown === 'users' ? 'bg-indigo-50/50 text-indigo-600 shadow-sm border border-indigo-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 border border-transparent'">
                        
                        <div class="flex items-center gap-3 relative z-10">
                            <div class="w-7 h-7 flex items-center justify-center rounded-lg transition-all duration-300"
                                 :class="activeDropdown === 'users' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'bg-gray-50 text-gray-400 group-hover:bg-gray-100 group-hover:text-indigo-600'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <span x-show="sidebarOpen" class="font-black text-[10px] uppercase tracking-widest" x-transition:enter="transition ease-out duration-300 delay-100">Utilisateurs</span>
                        </div>
                        
                        <svg x-show="sidebarOpen" :class="activeDropdown === 'users' ? 'rotate-180' : ''" class="w-3 h-3 transition-transform duration-300 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="activeDropdown === 'users' && sidebarOpen" x-collapse class="space-y-0.5 my-1 pr-1.5">
                        <a href="{{ route('admin.users.index') }}" 
                           class="flex items-center gap-2.5 ml-10 px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all duration-200 border-l-2 {{ request()->routeIs('admin.users.index') ? 'text-indigo-600 border-indigo-600 bg-indigo-50/30' : 'text-gray-400 border-transparent hover:text-indigo-500 hover:bg-indigo-50/20 hover:border-indigo-200' }}">
                            <span class="w-1 h-1 rounded-full bg-current opacity-40"></span>
                            Clients
                        </a>
                        @can('manage_admins')
                        <a href="{{ route('admin.admins.index') }}" 
                           class="flex items-center gap-2.5 ml-10 px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all duration-200 border-l-2 {{ request()->routeIs('admin.admins*') ? 'text-purple-600 border-purple-600 bg-purple-50/30' : 'text-gray-400 border-transparent hover:text-purple-500 hover:bg-purple-50/20 hover:border-purple-200' }}">
                            <span class="w-1 h-1 rounded-full bg-current opacity-40"></span>
                            Admins
                        </a>
                        @endcan
                    </div>
                </div>
                @endcan

                @can('manage_products')
                <!-- Gestion Articles -->
                <div class="px-1.5">
                    <a href="{{ route('admin.plats.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-xl transition-all duration-300 group relative overflow-hidden active:scale-95"
                       :class="{{ request()->routeIs('admin.plats*') ? 'true' : 'false' }} ? 'bg-red-50/50 text-red-600 shadow-sm border border-red-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 border border-transparent'">
                        
                        <div class="relative z-10 w-7 h-7 flex items-center justify-center rounded-lg transition-all duration-300"
                             :class="{{ request()->routeIs('admin.plats*') ? 'true' : 'false' }} ? 'bg-red-600 text-white shadow-md shadow-red-200' : 'bg-gray-50 text-gray-400 group-hover:bg-gray-100 group-hover:text-red-600'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <span x-show="sidebarOpen" class="relative z-10 font-black text-[10px] uppercase tracking-widest" x-transition:enter="transition ease-out duration-300 delay-100">Articles</span>
                    </a>
                </div>
                @endcan

                @can('manage_categories')
                <!-- Catégories Articles -->
                <div class="px-1.5">
                    <a href="{{ route('admin.categories.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-xl transition-all duration-300 group relative overflow-hidden active:scale-95"
                       :class="{{ request()->routeIs('admin.categories*') ? 'true' : 'false' }} ? 'bg-red-50/50 text-red-600 shadow-sm border border-red-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 border border-transparent'">
                        
                        <div class="relative z-10 w-7 h-7 flex items-center justify-center rounded-lg transition-all duration-300"
                             :class="{{ request()->routeIs('admin.categories*') ? 'true' : 'false' }} ? 'bg-red-600 text-white shadow-md shadow-red-200' : 'bg-gray-50 text-gray-400 group-hover:bg-gray-100 group-hover:text-red-600'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <span x-show="sidebarOpen" class="relative z-10 font-black text-[10px] uppercase tracking-widest" x-transition:enter="transition ease-out duration-300 delay-100">Catégories</span>
                    </a>
                </div>
                @endcan

                @can('view_vendors')
                <!-- Avis & Évaluations -->
                <div class="px-1.5">
                    <a href="{{-- route('admin.reviews.index') --}}"
                       class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-xl transition-all duration-300 group relative overflow-hidden active:scale-95"
                       :class="{{ request()->routeIs('admin.reviews*') ? 'true' : 'false' }} ? 'bg-red-50/50 text-red-600 shadow-sm border border-red-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 border border-transparent'">
                        
                        <div class="relative z-10 w-7 h-7 flex items-center justify-center rounded-lg transition-all duration-300"
                             :class="{{ request()->routeIs('admin.reviews*') ? 'true' : 'false' }} ? 'bg-red-600 text-white shadow-md shadow-red-200' : 'bg-gray-50 text-gray-400 group-hover:bg-gray-100 group-hover:text-red-600'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </div>
                        <span x-show="sidebarOpen" class="relative z-10 font-black text-[10px] uppercase tracking-widest" x-transition:enter="transition ease-out duration-300 delay-100">Avis Clients</span>
                    </a>
                </div>
                @endcan

                @can('manage_zones')
                <!-- Zones -->
                <div class="px-1.5">
                    <a href="{{ route('admin.zones.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-xl transition-all duration-300 group relative overflow-hidden active:scale-95"
                       :class="{{ request()->routeIs('admin.zones*') ? 'true' : 'false' }} ? 'bg-red-50/50 text-red-600 shadow-sm border border-red-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 border border-transparent'">
                        
                        <div class="relative z-10 w-7 h-7 flex items-center justify-center rounded-lg transition-all duration-300"
                             :class="{{ request()->routeIs('admin.zones*') ? 'true' : 'false' }} ? 'bg-red-600 text-white shadow-md shadow-red-200' : 'bg-gray-50 text-gray-400 group-hover:bg-gray-100 group-hover:text-red-600'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <span x-show="sidebarOpen" class="relative z-10 font-black text-[10px] uppercase tracking-widest" x-transition:enter="transition ease-out duration-300 delay-100">Zones</span>
                    </a>
                </div>
                @endcan

                @can('view_vendor_categories')
                <!-- Catégories Boutiques -->
                <div class="px-1.5">
                    <a href="{{ route('admin.vendor-categories.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-xl transition-all duration-300 group relative overflow-hidden active:scale-95"
                       :class="{{ request()->routeIs('admin.vendor-categories*') ? 'true' : 'false' }} ? 'bg-orange-50/50 text-orange-600 shadow-sm border border-orange-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 border border-transparent'">
                        
                        <div class="relative z-10 w-7 h-7 flex items-center justify-center rounded-lg transition-all duration-300"
                             :class="{{ request()->routeIs('admin.vendor-categories*') ? 'true' : 'false' }} ? 'bg-orange-600 text-white shadow-md shadow-orange-200' : 'bg-gray-50 text-gray-400 group-hover:bg-gray-100 group-hover:text-orange-600'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <span x-show="sidebarOpen" class="relative z-10 font-black text-[10px] uppercase tracking-widest" x-transition:enter="transition ease-out duration-300 delay-100">Boutiques</span>
                    </a>
                </div>
                @endcan

                @can('manage_products')
                <!-- Promotions -->
                <div class="px-1.5">
                    <a href="{{-- route('admin.promotions.index') --}}"
                       class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-xl transition-all duration-300 group relative overflow-hidden active:scale-95"
                       :class="{{ request()->routeIs('admin.promotions*') ? 'true' : 'false' }} ? 'bg-red-50/50 text-red-600 shadow-sm border border-red-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 border border-transparent'">
                        
                        <div class="relative z-10 w-7 h-7 flex items-center justify-center rounded-lg transition-all duration-300"
                             :class="{{ request()->routeIs('admin.promotions*') ? 'true' : 'false' }} ? 'bg-red-600 text-white shadow-md shadow-red-200' : 'bg-gray-50 text-gray-400 group-hover:bg-gray-100 group-hover:text-red-600'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <span x-show="sidebarOpen" class="relative z-10 font-black text-[10px] uppercase tracking-widest" x-transition:enter="transition ease-out duration-300 delay-100">Promotions</span>
                    </a>
                </div>
                @endcan

                @can('view_finance')
                <!-- Finance -->
                <div class="px-1.5">
                    <a href="{{ route('admin.finance.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-xl transition-all duration-300 group relative overflow-hidden active:scale-95"
                       :class="{{ request()->routeIs('admin.finance*') ? 'true' : 'false' }} ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 border border-transparent'">
                        
                        @if(request()->routeIs('admin.finance*'))
                            <div class="absolute inset-0 bg-linear-to-r from-emerald-600 to-teal-500 opacity-100"></div>
                        @endif

                        <div class="relative z-10 w-7 h-7 flex items-center justify-center rounded-lg transition-all duration-300"
                             :class="{{ request()->routeIs('admin.finance*') ? 'true' : 'false' }} ? 'bg-white/20' : 'bg-gray-50 text-gray-400 group-hover:bg-gray-100 group-hover:text-emerald-600'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <span x-show="sidebarOpen" class="relative z-10 font-black text-[10px] uppercase tracking-widest" x-transition:enter="transition ease-out duration-300 delay-100">Finance</span>
                    </a>
                </div>
                @endcan

                @can('view_dashboard')
                <!-- Statistiques -->
                <div class="px-1.5">
                    <a href="{{-- route('admin.analytics.index') --}}"
                       class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-xl transition-all duration-300 group relative overflow-hidden active:scale-95"
                       :class="{{ request()->routeIs('admin.analytics*') ? 'true' : 'false' }} ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 border border-transparent'">
                        
                        @if(request()->routeIs('admin.analytics*'))
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-500 opacity-100"></div>
                        @endif

                        <div class="relative z-10 w-7 h-7 flex items-center justify-center rounded-lg transition-all duration-300"
                             :class="{{ request()->routeIs('admin.analytics*') ? 'true' : 'false' }} ? 'bg-white/20' : 'bg-gray-50 text-gray-400 group-hover:bg-gray-100 group-hover:text-blue-600'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <span x-show="sidebarOpen" class="relative z-10 font-black text-[10px] uppercase tracking-widest" x-transition:enter="transition ease-out duration-300 delay-100">Analytics</span>
                    </a>
                </div>
                @endcan

                <div class="my-4 border-t border-gray-200"></div>

                @can('view_security')
                <!-- Sécurité -->
                <div class="px-1.5">
                    <a href="{{ route('admin.security.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-xl transition-all duration-300 group relative overflow-hidden active:scale-95"
                       :class="{{ request()->routeIs('admin.security*') ? 'true' : 'false' }} ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 border border-transparent'">
                        
                        @if(request()->routeIs('admin.security*'))
                            <div class="absolute inset-0 bg-slate-900 opacity-100"></div>
                        @endif

                        <div class="relative z-10 w-7 h-7 flex items-center justify-center rounded-lg transition-all duration-300"
                             :class="{{ request()->routeIs('admin.security*') ? 'true' : 'false' }} ? 'bg-white/20' : 'bg-gray-50 text-gray-400 group-hover:bg-gray-100 group-hover:text-slate-900'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <span x-show="sidebarOpen" class="relative z-10 font-black text-[10px] uppercase tracking-widest" x-transition:enter="transition ease-out duration-300 delay-100">Sécurité</span>
                    </a>
                </div>
                @endcan

                @can('manage_settings')
                <!-- Paramètres -->
                <div class="px-1.5 mb-1">
                    <button @click="activeDropdown = activeDropdown === 'settings' ? null : 'settings'" 
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-300 group relative overflow-hidden active:scale-95"
                            :class="activeDropdown === 'settings' ? 'bg-gray-50/50 text-gray-900 shadow-sm border border-gray-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 border border-transparent'">
                        
                        <div class="flex items-center gap-3 relative z-10">
                            <div class="w-7 h-7 flex items-center justify-center rounded-lg transition-all duration-300"
                                 :class="activeDropdown === 'settings' ? 'bg-gray-900 text-white shadow-md shadow-gray-200' : 'bg-gray-50 text-gray-400 group-hover:bg-gray-100 group-hover:text-gray-900'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <span x-show="sidebarOpen" class="font-black text-[10px] uppercase tracking-widest" x-transition:enter="transition ease-out duration-300 delay-100">Système</span>
                        </div>
                        <svg x-show="sidebarOpen" class="w-3 h-3 transition-transform duration-300 relative z-10" :class="activeDropdown === 'settings' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div x-show="activeDropdown === 'settings' && sidebarOpen" x-cloak class="space-y-0.5 my-1 pr-1.5">
                        <a href="{{ route('admin.settings.index') }}" 
                           class="flex items-center gap-2.5 ml-10 px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all duration-200 border-l-2 {{ request()->routeIs('admin.settings*') ? 'text-gray-900 border-gray-900 bg-gray-50' : 'text-gray-400 border-transparent hover:text-gray-600 hover:bg-gray-50/50 hover:border-gray-200' }}">
                            <span class="w-1 h-1 rounded-full bg-current opacity-40"></span>
                            Configuration
                        </a>
                        <a href="{{ route('admin.countries.index') }}" 
                           class="flex items-center gap-2.5 ml-10 px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all duration-200 border-l-2 {{ request()->routeIs('admin.countries*') ? 'text-gray-900 border-gray-900 bg-gray-50' : 'text-gray-400 border-transparent hover:text-gray-600 hover:bg-gray-50/50 hover:border-gray-200' }}">
                            <span class="w-1 h-1 rounded-full bg-current opacity-40"></span>
                            Pays & Localisation
                        </a>
                    </div>
                </div>
                @endcan
            </nav>

            <!-- User Profile -->
            <div class="mt-auto border-t border-gray-50 p-4 bg-gray-50/30">
                <div class="flex items-center gap-3 group cursor-pointer" x-show="sidebarOpen">
                    <div class="relative">
                        <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name) }}"
                             class="w-9 h-9 rounded-xl border-2 border-white shadow-sm transition-transform group-hover:scale-105"
                             alt="Admin">
                        <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full"></div>
                    </div>
                    <div class="flex-1 min-w-0 transition-opacity duration-300">
                        <p class="text-[11px] font-black text-gray-900 truncate tracking-tight">{{ auth()->user()->name }}</p>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Master Admin</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-4" x-show="sidebarOpen">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-xl transition-all duration-300 font-black text-[9px] uppercase tracking-widest shadow-sm hover:shadow-lg hover:shadow-red-200 active:scale-95 group">
                        <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Quitter
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
               x-transition:enter="transition ease-out duration-500 transform"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-400 transform"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               @click.away="mobileMenuOpen = false"
               class="fixed inset-y-0 left-0 w-88 bg-white/95 backdrop-blur-2xl border-r border-gray-100 z-50 lg:hidden overflow-y-auto flex flex-col shadow-[24px_0_48px_rgba(0,0,0,0.1)]">
            
            <!-- Mobile Header Logo -->
            <div class="h-20 flex items-center justify-between px-8 border-b border-gray-50 shrink-0">
                <div class="flex items-center gap-4">
                    @if($finalLogo)
                        <img src="{{ $finalLogo }}" alt="{{ $siteName }}" class="w-9 h-9 object-contain drop-shadow-sm">
                    @endif
                    <span class="text-xl font-black bg-linear-to-r from-red-600 via-orange-500 to-red-600 bg-clip-text text-transparent tracking-tighter">
                        {{ $siteName }}
                    </span>
                </div>
                <button @click="mobileMenuOpen = false" class="p-2.5 bg-gray-50 text-gray-400 hover:text-red-500 rounded-xl transition-all active:scale-90 border border-transparent hover:border-red-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <div class="flex-1 px-4 py-6 space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }} transition-all font-bold text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Tableau de bord
                </a>

                @can('view_vendors')
                <div class="px-2">
                    <button @click="activeDropdown = activeDropdown === 'mobile-vendors' ? null : 'mobile-vendors'" 
                            class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all font-bold text-sm"
                            :class="activeDropdown === 'mobile-vendors' ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50'">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Vendeurs
                        </div>
                        <svg :class="activeDropdown === 'mobile-vendors' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="activeDropdown === 'mobile-vendors'" x-collapse class="pl-12 pr-4 py-2 space-y-1">
                        <a href="{{ route('admin.vendors.index') }}" class="block py-2 text-xs font-bold {{ request()->routeIs('admin.vendors.index') && !request('status') ? 'text-red-600' : 'text-gray-500 hover:text-red-600' }}">Tous les vendeurs</a>
                        <a href="{{ route('admin.vendors.index', ['status' => 'en_cours']) }}" class="block py-2 text-xs font-bold {{ request('status') == 'en_cours' ? 'text-orange-600' : 'text-gray-500 hover:text-orange-600' }}">En attente</a>
                    </div>
                </div>
                @endcan

                @can('view_orders')
                <div class="px-2">
                     <button @click="activeDropdown = activeDropdown === 'mobile-orders' ? null : 'mobile-orders'" 
                            class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all font-bold text-sm"
                            :class="activeDropdown === 'mobile-orders' ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50'">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2-2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Commandes
                        </div>
                        <svg :class="activeDropdown === 'mobile-orders' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="activeDropdown === 'mobile-orders'" x-collapse class="pl-12 pr-4 py-2 space-y-1">
                        <a href="{{ route('admin.orders.index') }}" class="block py-2 text-xs font-bold {{ request()->routeIs('admin.orders.index') && !request('status') ? 'text-red-600' : 'text-gray-500 hover:text-red-600' }}">Toutes</a>
                         <a href="{{ route('admin.orders.index', ['status' => 'en_attente']) }}" class="block py-2 text-xs font-bold {{ request('status') == 'en_attente' ? 'text-orange-600' : 'text-gray-500 hover:text-orange-600' }}">En cours</a>
                    </div>
                </div>
                @endcan

                @can('view_users')
                <div class="px-2">
                    <button @click="activeDropdown = activeDropdown === 'mobile-users' ? null : 'mobile-users'"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all font-bold text-sm"
                            :class="activeDropdown === 'mobile-users' ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50'">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Utilisateurs
                        </div>
                        <svg :class="activeDropdown === 'mobile-users' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="activeDropdown === 'mobile-users'" x-collapse class="pl-12 pr-4 py-2 space-y-1">
                        <a href="{{ route('admin.users.index') }}" class="block py-2 text-xs font-bold {{ request()->routeIs('admin.users.index') ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-600' }}">Clients</a>
                        @can('manage_admins')
                        <a href="{{ route('admin.admins.index') }}" class="block py-2 text-xs font-bold {{ request()->routeIs('admin.admins*') ? 'text-purple-600' : 'text-gray-500 hover:text-purple-600' }}">Admins</a>
                        @endcan
                    </div>
                </div>
                @endcan

                @can('manage_products')
                <a href="{{ route('admin.plats.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.plats*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }} transition-all font-bold text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Articles
                </a>
                @endcan

                @can('manage_categories')
                <a href="{{ route('admin.categories.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.categories*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }} transition-all font-bold text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    Catégories
                </a>
                @endcan

                @can('view_vendors')
                <a href="{{-- route('admin.reviews.index') --}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.reviews*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }} transition-all font-bold text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    Avis Clients
                </a>
                @endcan

                @can('manage_zones')
                <a href="{{ route('admin.zones.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.zones*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }} transition-all font-bold text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Zones
                </a>
                @endcan

                @can('view_vendor_categories')
                <a href="{{ route('admin.vendor-categories.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.vendor-categories*') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50' }} transition-all font-bold text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Boutiques
                </a>
                @endcan

                @can('manage_products')
                <a href="{{-- route('admin.promotions.index') --}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.promotions*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }} transition-all font-bold text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    Promotions
                </a>
                @endcan

                @can('view_finance')
                <a href="{{ route('admin.finance.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.finance*') ? 'bg-emerald-50 text-emerald-600' : 'text-gray-600 hover:bg-gray-50' }} transition-all font-bold text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Finance
                </a>
                @endcan

                @can('view_dashboard')
                <a href="{{-- route('admin.analytics.index') --}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.analytics*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }} transition-all font-bold text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Analytics
                </a>
                @endcan

                @can('view_security')
                <a href="{{ route('admin.security.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.security*') ? 'bg-slate-800 text-white' : 'text-gray-600 hover:bg-gray-50' }} transition-all font-bold text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Sécurité
                </a>
                @endcan
                
                @can('manage_settings')
                <div class="px-2">
                    <button @click="activeDropdown = activeDropdown === 'mobile-settings' ? null : 'mobile-settings'"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all font-bold text-sm"
                            :class="activeDropdown === 'mobile-settings' ? 'bg-gray-50 text-gray-900' : 'text-gray-600 hover:bg-gray-50'">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Paramètres
                        </div>
                        <svg :class="activeDropdown === 'mobile-settings' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="activeDropdown === 'mobile-settings'" x-collapse class="pl-12 pr-4 py-2 space-y-1">
                        <a href="{{ route('admin.settings.index') }}" class="block py-2 text-xs font-bold {{ request()->routeIs('admin.settings.index') ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">Configuration</a>
                         <a href="{{ route('admin.countries.index') }}" class="block py-2 text-xs font-bold {{ request()->routeIs('admin.countries.index') ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">Pays & Loc</a>
                    </div>
                </div>
                @endcan
            </div>

            <!-- Mobile Footer User -->
            <div class="p-6 border-t border-gray-100 bg-gray-50/50 space-y-4">
                <div class="flex items-center gap-3">
                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name) }}" class="w-10 h-10 rounded-xl border border-white shadow-sm">
                    <div class="min-w-0">
                        <p class="text-sm font-black text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Administrateur</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-red-50 text-red-600 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all">
                        Déconnexion
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 transition-all duration-500 ease-in-out" :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'">
            <!-- Top Header -->
            <header class="bg-white border-b border-gray-200 sticky top-0 z-20">
                <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    <button @click="mobileMenuOpen = true" class="lg:hidden p-2 hover:bg-gray-100 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <div class="hidden md:flex flex-1 max-w-md mx-4">
                        <div class="relative w-full">
                            <input type="search"
                                   placeholder="Rechercher..."
                                   class="w-full pl-10 pr-4 py-2 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all outline-none text-sm">
                            <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="p-4 sm:p-6 lg:p-8">
                <!-- Alerts are now handled by the Global Modal -->

                @yield('content')
            </div>
        </main>
    </div>

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
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-100">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10 transition-colors duration-200"
                             :class="{
                                'bg-green-100 text-green-600': modalType === 'success',
                                'bg-red-100 text-red-600': modalType === 'error',
                                'bg-yellow-100 text-yellow-600': modalType === 'warning'
                             }">
                            <!-- Success Icon -->
                            <svg x-show="modalType === 'success'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <!-- Error Icon -->
                            <svg x-show="modalType === 'error'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <!-- Warning Icon -->
                            <svg x-show="modalType === 'warning'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-black text-gray-900" x-text="modalTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 font-medium" x-text="modalMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                    <button type="button" 
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 text-base font-bold text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm transition-all active:scale-95"
                            :class="{
                                'bg-green-600 hover:bg-green-700 focus:ring-green-500': modalType === 'success',
                                'bg-red-600 hover:bg-red-700 focus:ring-red-500': modalType === 'error',
                                'bg-yellow-500 hover:bg-yellow-600 focus:ring-yellow-400': modalType === 'warning'
                            }"
                            @click="showModal = false">
                        Compris
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
