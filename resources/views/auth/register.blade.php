<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>S'inscrire | {{ config('app.name', 'Cabaacabaa') }}</title>

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">

    <script>
        // Check for dark mode preference immediately to prevent flash
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
                        },
                        colors: {
                            slate: {
                                950: '#020617',
                            }
                        }
                    }
                }
            }
        </script>
    @endif

    <style>
        [x-cloak] { display: none !important; }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }

        .dark .glass-card {
            background: rgba(17, 24, 39, 0.8);
            border: 1px solid rgba(55, 65, 81, 0.5);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.2);
        }

        .bg-gradient-premium {
            background: radial-gradient(circle at top right, rgba(249, 115, 22, 0.05), transparent),
                        radial-gradient(circle at bottom left, rgba(239, 68, 68, 0.05), transparent),
                        #f8fafc;
        }

        .dark .bg-gradient-premium {
             background: radial-gradient(circle at top right, rgba(249, 115, 22, 0.1), transparent),
                        radial-gradient(circle at bottom left, rgba(239, 68, 68, 0.1), transparent),
                        #030712;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out forwards;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-premium font-sans selection:bg-orange-100 selection:text-orange-900 transition-colors duration-300">

    <div class="min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 lg:p-8 overflow-y-auto">
        
        <div class="w-full max-w-xl animate-fadeIn">
            
            <!-- Branding/Logo -->
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center gap-2 group mb-6">
                    <div class="w-12 h-12 bg-orange-600 rounded-2xl flex items-center justify-center shadow-lg shadow-orange-200 dark:shadow-none group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-display font-bold text-slate-900 dark:text-white tracking-tight">{{ config('app.name', 'Cabaacabaa') }}</span>
                </a>
                <h1 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2">Créer un compte</h1>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Rejoignez-nous et commencez votre aventure culinaire.</p>
            </div>

            <div class="glass-card rounded-3xl p-6 sm:p-10">
                
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-900/30 rounded-2xl">
                        <div class="flex items-center gap-2 text-red-600 dark:text-red-400 mb-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-bold">Veuillez corriger les erreurs suivantes:</span>
                        </div>
                        <ul class="text-sm text-red-500 dark:text-red-400 list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.post') }}" class="space-y-5">
                    @csrf
                    
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 ml-1">Nom Complet</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-4 flex items-center text-slate-400 dark:text-slate-500 group-focus-within:text-orange-600 dark:group-focus-within:text-orange-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input type="text" name="name" value="{{ old('name') }}" required autoFocus
                                   class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-gray-900/50 border border-slate-200 dark:border-gray-700 rounded-xl focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-orange-500/10 dark:focus:ring-orange-500/20 focus:border-orange-500 dark:focus:border-orange-500 outline-none transition-all text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600"
                                   placeholder="Votre nom complet">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 ml-1">Adresse Email</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-4 flex items-center text-slate-400 dark:text-slate-500 group-focus-within:text-orange-600 dark:group-focus-within:text-orange-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 00-2 2z"/>
                                </svg>
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-gray-900/50 border border-slate-200 dark:border-gray-700 rounded-xl focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-orange-500/10 dark:focus:ring-orange-500/20 focus:border-orange-500 dark:focus:border-orange-500 outline-none transition-all text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600"
                                   placeholder="email@exemple.com">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 ml-1">Mot de Passe</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-4 flex items-center text-slate-400 dark:text-slate-500 group-focus-within:text-orange-600 dark:group-focus-within:text-orange-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input type="password" name="password" required
                                       class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-gray-900/50 border border-slate-200 dark:border-gray-700 rounded-xl focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-orange-500/10 dark:focus:ring-orange-500/20 focus:border-orange-500 dark:focus:border-orange-500 outline-none transition-all text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600"
                                       placeholder="••••••••">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 ml-1">Confirmation</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-4 flex items-center text-slate-400 dark:text-slate-500 group-focus-within:text-orange-600 dark:group-focus-within:text-orange-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <input type="password" name="password_confirmation" required
                                       class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-gray-900/50 border border-slate-200 dark:border-gray-700 rounded-xl focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-orange-500/10 dark:focus:ring-orange-500/20 focus:border-orange-500 dark:focus:border-orange-500 outline-none transition-all text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600"
                                       placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-4 bg-orange-600 text-white font-bold rounded-xl shadow-lg shadow-orange-200 dark:shadow-none hover:bg-orange-700 hover:shadow-orange-300 hover:-translate-y-0.5 transition-all active:scale-[0.98]">
                            Créer mon compte
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-8 border-t border-slate-100 dark:border-gray-700 text-center">
                    <p class="text-slate-600 dark:text-slate-400">
                        Déjà membre ? 
                        <a href="{{ route('login') }}" class="text-orange-600 dark:text-orange-400 font-bold hover:underline ml-1">Se connecter</a>
                    </p>
                </div>
            </div>

            <div class="mt-8 flex flex-col items-center gap-4 text-center">
                <p class="text-xs text-slate-400 dark:text-slate-500 leading-relaxed max-w-sm">
                    En vous inscrivant, vous acceptez nos <a href="{{ route('terms') }}" class="text-slate-600 dark:text-slate-400 hover:underline">Conditions d'Utilisation</a> et notre <a href="{{ route('privacy') }}" class="text-slate-600 dark:text-slate-400 hover:underline">Politique de Confidentialité</a>.
                </p>
                <div class="flex items-center gap-6">
                    <a href="/" class="text-xs font-bold text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 flex items-center gap-1 transition-colors uppercase tracking-wider">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Accueil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
