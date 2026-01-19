@extends('layouts.auth')

@section('title', 'Inscription')

@section('content')
<div class="min-h-screen flex" x-data="{ showError: {{ $errors->any() ? 'true' : 'false' }} }">
    <!-- Left Side - Branding -->
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

            <!-- Benefits -->
            <div class="space-y-8">
                <h1 class="text-5xl font-black text-white leading-tight">
                    Rejoignez notre<br>communauté
                </h1>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-lg">Inscription gratuite</h3>
                            <p class="text-orange-100 text-sm">Créez votre compte en quelques secondes</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-lg">Sécurisé & Fiable</h3>
                            <p class="text-orange-100 text-sm">Vos données sont protégées</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-lg">Démarrage rapide</h3>
                            <p class="text-orange-100 text-sm">Commencez immédiatement après inscription</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trust Badge -->
        <div class="relative z-10 flex items-center gap-3 text-white/80">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span class="text-sm font-medium">Vos informations sont sécurisées et confidentielles</span>
        </div>
    </div>

    <!-- Right Side - Registration Form -->
    <div class="flex-1 flex items-center justify-center p-8 bg-gray-50 overflow-y-auto">
        <div class="w-full max-w-md py-8">
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
                    <h2 class="text-3xl font-black text-gray-900 mb-2">Créer un compte</h2>
                    <p class="text-gray-500">Remplissez vos informations ci-dessous</p>
                </div>

                <form action="{{ route('register') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nom complet</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition-all"
                               placeholder="Jean Dupont">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Adresse email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition-all"
                               placeholder="vous@exemple.com">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Téléphone</label>
                        <input type="tel" name="telephone" value="{{ old('telephone') }}" required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition-all"
                               placeholder="+221 XX XXX XX XX">
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Mot de passe</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition-all"
                               placeholder="••••••••">
                        <p class="mt-1 text-xs text-gray-500">Minimum 8 caractères</p>
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition-all"
                               placeholder="••••••••">
                    </div>

                    <!-- Terms -->
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="terms" required class="mt-1 w-5 h-5 text-[#ff4d00] border-gray-300 rounded focus:ring-[#ff4d00]">
                        <span class="text-sm text-gray-600">
                            J'accepte les <a href="#" class="text-[#ff4d00] font-bold hover:underline">conditions d'utilisation</a> et la <a href="#" class="text-[#ff4d00] font-bold hover:underline">politique de confidentialité</a>
                        </span>
                    </label>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-[#ff4d00] to-orange-600 text-white font-black py-4 rounded-xl hover:shadow-2xl hover:shadow-orange-200 transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                        Créer mon compte
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500 font-medium">Déjà inscrit ?</span>
                    </div>
                </div>

                <!-- Login Link -->
                <a href="{{ route('login') }}" 
                   class="block w-full text-center py-4 border-2 border-gray-200 rounded-xl font-bold text-gray-700 hover:border-[#ff4d00] hover:text-[#ff4d00] hover:bg-orange-50 transition-all">
                    Se connecter
                </a>
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
                    <h3 class="text-lg font-black text-gray-900 mb-2">Erreur d'inscription</h3>
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
