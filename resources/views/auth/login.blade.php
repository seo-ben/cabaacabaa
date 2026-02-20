@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
<div class="min-h-screen flex" x-data="{ showError: {{ $errors->any() ? 'true' : 'false' }}, showPass: false }">

    {{-- ===================== DESKTOP LEFT PANEL ===================== --}}
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-[#ff4d00] via-orange-600 to-red-700 p-12 flex-col justify-between relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-1/2 translate-y-1/2"></div>
        </div>
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-12">
                @if($siteLogo ?? false)
                    <img src="{{ $siteLogo }}" alt="{{ $siteName ?? config('app.name') }}" class="h-12 w-auto">
                @else
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-xl">
                        <svg class="w-7 h-7 text-[#ff4d00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                @endif
                <span class="text-3xl font-black text-white tracking-tight">{{ $siteName ?? config('app.name') }}</span>
            </div>
            <div class="space-y-8">
                <h1 class="text-5xl font-black text-white leading-tight">Votre plateforme<br>de confiance</h1>
                <p class="text-xl text-orange-100 leading-relaxed max-w-md">Connectez-vous pour gérer vos commandes, suivre vos livraisons et développer votre activité.</p>
            </div>
        </div>
        <div class="relative z-10 grid grid-cols-3 gap-6">
            <div class="text-center"><div class="text-3xl font-black text-white mb-1">10K+</div><div class="text-sm text-orange-100">Utilisateurs</div></div>
            <div class="text-center"><div class="text-3xl font-black text-white mb-1">99.9%</div><div class="text-sm text-orange-100">Disponibilité</div></div>
            <div class="text-center"><div class="text-3xl font-black text-white mb-1">24/7</div><div class="text-sm text-orange-100">Support</div></div>
        </div>
    </div>

    {{-- ===================== RIGHT / MOBILE SIDE ===================== --}}
    <div class="flex-1 flex flex-col lg:items-center lg:justify-center relative bg-gray-50 lg:p-8">

        {{-- ---- MOBILE ONLY HERO (hidden on lg) ---- --}}
        <div class="lg:hidden relative h-[42vh] min-h-[260px] bg-gradient-to-br from-[#ff4d00] via-orange-600 to-red-700 overflow-hidden flex-shrink-0">
            {{-- Animated blobs --}}
            <div class="absolute -top-16 -right-16 w-56 h-56 bg-white/10 rounded-full animate-pulse"></div>
            <div class="absolute top-8 -left-10 w-32 h-32 bg-white/10 rounded-full animate-ping" style="animation-duration:3s"></div>
            <div class="absolute bottom-8 right-8 w-20 h-20 bg-white/15 rounded-full"></div>
            {{-- Floating icon --}}
            <div class="absolute inset-0 flex flex-col items-center justify-center gap-5 px-8 text-center">
                <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-3xl flex items-center justify-center shadow-2xl border border-white/30">
                    @if($siteLogo ?? false)
                        <img src="{{ $siteLogo }}" class="h-12 w-auto">
                    @else
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    @endif
                </div>
                <div>
                    <h1 class="text-3xl font-black text-white tracking-tight">{{ $siteName ?? config('app.name') }}</h1>
                    <p class="text-orange-100 text-sm font-medium mt-1">Bon retour ! 👋</p>
                </div>
            </div>
        </div>

        {{-- ---- FORM CARD (appears as bottom-sheet on mobile) ---- --}}
        <div class="lg:hidden flex-1 bg-white rounded-t-[2rem] -mt-6 relative z-10 shadow-2xl shadow-black/10 overflow-y-auto">
            <div class="px-6 pt-6 pb-32">
                {{-- Pull indicator --}}
                <div class="w-10 h-1.5 bg-gray-200 rounded-full mx-auto mb-6"></div>

                {{-- Errors inline (mobile) --}}
                @if($errors->any())
                <div class="mb-5 p-4 bg-red-50 border border-red-100 rounded-2xl flex items-start gap-3">
                    <div class="w-8 h-8 bg-red-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-black text-red-700 uppercase tracking-widest mb-1">Erreur</p>
                        @foreach($errors->all() as $error)
                        <p class="text-xs text-red-600 font-medium">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
                @endif

                <h2 class="text-2xl font-black text-gray-900 mb-1">Connexion</h2>
                <p class="text-sm text-gray-500 mb-6 font-medium">Connectez-vous à votre compte</p>

                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Identifiant --}}
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Email/Telephone</label>
                        <div class="relative flex items-center bg-gray-50 rounded-2xl border-2 border-transparent focus-within:border-orange-500 focus-within:bg-white transition-all">
                            <div class="absolute left-4 text-gray-400">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input type="text" name="login" value="{{ old('login') }}" required autofocus
                                   class="w-full pl-11 pr-4 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900 placeholder-gray-400"
                                   placeholder="Email ou +228...">
                        </div>
                    </div>

                    {{-- Mot de passe --}}
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Mot de passe</label>
                        <div class="relative flex items-center bg-gray-50 rounded-2xl border-2 border-transparent focus-within:border-orange-500 focus-within:bg-white transition-all">
                            <div class="absolute left-4 text-gray-400">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input :type="showPass ? 'text' : 'password'" name="password" required
                                   class="w-full pl-11 pr-12 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900 placeholder-gray-400"
                                   placeholder="••••••••">
                            <button type="button" @click="showPass = !showPass" class="absolute right-4 text-gray-400 active:text-orange-500 transition-colors">
                                <svg x-show="!showPass" class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPass" x-cloak class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Se souvenir + Oublié --}}
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500">
                            <span class="text-xs font-bold text-gray-600">Se souvenir</span>
                        </label>
                        <a href="{{ route('password.forgot') }}" class="text-xs font-black text-orange-500 active:text-orange-700 transition-colors">Mot de passe oublié ?</a>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            class="w-full py-4 bg-gradient-to-r from-[#ff4d00] to-orange-500 text-white font-black rounded-2xl text-sm uppercase tracking-widest shadow-xl shadow-orange-500/30 active:scale-[0.97] transition-all mt-2">
                        Se connecter →
                    </button>
                </form>

                {{-- Divider --}}
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-100"></div></div>
                    <div class="relative flex justify-center"><span class="px-4 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest">Nouveau sur la plateforme ?</span></div>
                </div>

                {{-- Register link --}}
                <a href="{{ route('register') }}" class="block w-full text-center py-4 border-2 border-gray-100 rounded-2xl font-black text-sm text-gray-700 active:border-orange-500 active:text-orange-500 transition-all">
                    Créer un compte
                </a>

                {{-- Security --}}
                <div class="mt-5 flex items-center justify-center gap-2 text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span class="text-[10px] font-bold uppercase tracking-widest">Connexion sécurisée SSL</span>
                </div>
            </div>
        </div>

        {{-- ---- DESKTOP FORM CARD ---- --}}
        <div class="hidden lg:block w-full max-w-md">
            <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 p-8">
                <div class="mb-8">
                    <h2 class="text-3xl font-black text-gray-900 mb-2">Bon retour !</h2>
                    <p class="text-gray-500">Connectez-vous à votre compte</p>
                </div>
                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Identifiant (Email ou Téléphone)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                            <input type="text" name="login" value="{{ old('login') }}" required autofocus class="w-full pl-12 pr-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition-all" placeholder="Email ou +228...">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Mot de passe</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
                            <input :type="showPass ? 'text' : 'password'" name="password" required class="w-full pl-12 pr-12 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition-all" placeholder="••••••••">
                            <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-orange-500 transition-colors">
                                <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPass" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-[#ff4d00] border-gray-300 rounded focus:ring-[#ff4d00]">
                            <span class="text-sm text-gray-600">Se souvenir de moi</span>
                        </label>
                        <a href="{{ route('password.forgot') }}" class="text-sm font-bold text-[#ff4d00] hover:text-orange-600 transition">Mot de passe oublié ?</a>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-[#ff4d00] to-orange-600 text-white font-black py-4 rounded-xl hover:shadow-2xl hover:shadow-orange-200 transition-all transform hover:scale-[1.02] active:scale-[0.98]">Se connecter</button>
                </form>
                <div class="relative my-8"><div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div><div class="relative flex justify-center text-sm"><span class="px-4 bg-white text-gray-500 font-medium">Nouveau sur la plateforme ?</span></div></div>
                <a href="{{ route('register') }}" class="block w-full text-center py-4 border-2 border-gray-200 rounded-xl font-bold text-gray-700 hover:border-[#ff4d00] hover:text-[#ff4d00] hover:bg-orange-50 transition-all">Créer un compte</a>
            </div>
            <div class="mt-6 flex items-center justify-center gap-2 text-sm text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Connexion sécurisée SSL</span>
            </div>
        </div>
    </div>

    {{-- ===================== ERROR MODAL (Desktop) ===================== --}}
    <div x-show="showError" x-cloak @click.away="showError = false"
         class="hidden lg:flex fixed inset-0 z-50 items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" @click.stop>
            <div class="flex items-start gap-4">
                <div class="shrink-0 w-12 h-12 bg-red-100 rounded-2xl flex items-center justify-center"><svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <div class="flex-1">
                    <h3 class="text-lg font-black text-gray-900 mb-2">Erreur de connexion</h3>
                    <ul class="space-y-1 text-sm text-gray-600">@foreach($errors->all() as $error)<li class="flex items-start gap-2"><span class="text-red-500 mt-0.5">•</span><span>{{ $error }}</span></li>@endforeach</ul>
                </div>
            </div>
            <button @click="showError = false" class="mt-6 w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl transition">Fermer</button>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    /* Smooth bottom-sheet rounded top */
    @media (max-width: 1023px) {
        html, body { height: 100%; overflow: hidden; }
        body > div, .min-h-screen { height: 100%; }
        /* The form container scrolls internally */
    }
</style>
@endsection
