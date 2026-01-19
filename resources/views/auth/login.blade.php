@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
<div class="min-h-screen flex" x-data="{ showError: {{ $errors->any() ? 'true' : 'false' }} }">
    <!-- Left Side - Branding & Trust Signals -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-[#ff4d00] via-orange-600 to-red-700 p-12 flex-col justify-between relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-1/2 translate-y-1/2"></div>
        </div>

        <div class="relative z-10">
            <!-- Logo -->
            <div class="flex items-center gap-3 mb-12">
                @if($siteLogo ?? false)
                    <img src="{{ $siteLogo }}" alt="{{ $siteName ?? config('app.name') }}" class="h-12 w-auto">
                @else
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-xl">
                        <svg class="w-7 h-7 text-[#ff4d00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                @endif
                <span class="text-3xl font-black text-white tracking-tight">{{ $siteName ?? config('app.name') }}</span>
            </div>

            <!-- Value Proposition -->
            <div class="space-y-8">
                <h1 class="text-5xl font-black text-white leading-tight">
                    Votre plateforme<br>de confiance
                </h1>
                <p class="text-xl text-orange-100 leading-relaxed max-w-md">
                    Connectez-vous pour gérer vos commandes, suivre vos livraisons et développer votre activité.
                </p>
            </div>
        </div>

        <!-- Trust Indicators -->
        <div class="relative z-10 grid grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-3xl font-black text-white mb-1">10K+</div>
                <div class="text-sm text-orange-100">Utilisateurs</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-black text-white mb-1">99.9%</div>
                <div class="text-sm text-orange-100">Disponibilité</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-black text-white mb-1">24/7</div>
                <div class="text-sm text-orange-100">Support</div>
            </div>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="flex-1 flex items-center justify-center p-8 bg-gray-50">
        <div class="w-full max-w-md">
            <!-- Mobile Logo -->
            <div class="lg:hidden flex items-center justify-center gap-3 mb-8">
                @if($siteLogo ?? false)
                    <img src="{{ $siteLogo }}" alt="{{ $siteName ?? config('app.name') }}" class="h-10 w-auto">
                @endif
                <span class="text-2xl font-black text-gray-900">{{ $siteName ?? config('app.name') }}</span>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 p-8">
                <div class="mb-8">
                    <h2 class="text-3xl font-black text-gray-900 mb-2">Bon retour !</h2>
                    <p class="text-gray-500">Connectez-vous à votre compte</p>
                </div>

                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Adresse email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="w-full pl-12 pr-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition-all"
                                   placeholder="vous@exemple.com">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Mot de passe</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input type="password" name="password" required
                                   class="w-full pl-12 pr-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition-all"
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-[#ff4d00] border-gray-300 rounded focus:ring-[#ff4d00]">
                            <span class="text-sm text-gray-600">Se souvenir de moi</span>
                        </label>
                        <a href="#" class="text-sm font-bold text-[#ff4d00] hover:text-orange-600 transition">Mot de passe oublié ?</a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-[#ff4d00] to-orange-600 text-white font-black py-4 rounded-xl hover:shadow-2xl hover:shadow-orange-200 transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                        Se connecter
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500 font-medium">Nouveau sur la plateforme ?</span>
                    </div>
                </div>

                <!-- Register Link -->
                <a href="{{ route('register') }}" 
                   class="block w-full text-center py-4 border-2 border-gray-200 rounded-xl font-bold text-gray-700 hover:border-[#ff4d00] hover:text-[#ff4d00] hover:bg-orange-50 transition-all">
                    Créer un compte
                </a>
            </div>

            <!-- Security Badge -->
            <div class="mt-6 flex items-center justify-center gap-2 text-sm text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span>Connexion sécurisée SSL</span>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div x-show="showError" 
         x-cloak
         @click.away="showError = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 transform"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             @click.stop>
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-black text-gray-900 mb-2">Erreur de connexion</h3>
                    <ul class="space-y-1 text-sm text-gray-600">
                        @foreach($errors->all() as $error)
                            <li class="flex items-start gap-2">
                                <span class="text-red-500 mt-0.5">•</span>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button @click="showError = false" 
                    class="mt-6 w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl transition">
                Fermer
            </button>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
