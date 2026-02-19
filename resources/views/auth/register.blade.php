@extends('layouts.auth')

@section('title', 'Inscription')

@section('content')
<div class="min-h-screen flex" x-data="{ showError: {{ $errors->any() ? 'true' : 'false' }}, showPass: false, showPassConf: false }">

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
                <h1 class="text-5xl font-black text-white leading-tight">Rejoignez notre<br>communauté</h1>
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center shrink-0"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
                        <div><h3 class="text-white font-bold text-lg">Inscription gratuite</h3><p class="text-orange-100 text-sm">Créez votre compte en quelques secondes</p></div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center shrink-0"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
                        <div><h3 class="text-white font-bold text-lg">Sécurisé & Fiable</h3><p class="text-orange-100 text-sm">Vos données sont protégées</p></div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center shrink-0"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div>
                        <div><h3 class="text-white font-bold text-lg">Démarrage rapide</h3><p class="text-orange-100 text-sm">Commencez immédiatement après inscription</p></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative z-10 flex items-center gap-3 text-white/80">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <span class="text-sm font-medium">Vos informations sont sécurisées et confidentielles</span>
        </div>
    </div>

    {{-- ===================== RIGHT / MOBILE SIDE ===================== --}}
    <div class="flex-1 flex flex-col lg:items-center lg:justify-center relative bg-gray-50 lg:p-8 lg:overflow-y-auto">

        {{-- ---- MOBILE ONLY HERO ---- --}}
        <div class="lg:hidden relative h-[32vh] min-h-[200px] bg-gradient-to-br from-[#ff4d00] via-orange-600 to-red-700 overflow-hidden flex-shrink-0">
            {{-- Animated blobs --}}
            <div class="absolute -top-12 -right-12 w-44 h-44 bg-white/10 rounded-full animate-pulse"></div>
            <div class="absolute top-6 -left-8 w-28 h-28 bg-white/10 rounded-full animate-ping" style="animation-duration:3.5s"></div>
            <div class="absolute bottom-4 right-12 w-16 h-16 bg-white/15 rounded-full"></div>
            <div class="absolute bottom-10 left-4 w-10 h-10 bg-white/10 rounded-full"></div>
            {{-- Content --}}
            <div class="absolute inset-0 flex flex-col items-center justify-center gap-4 px-8 text-center">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-2xl border border-white/30">
                    @if($siteLogo ?? false)
                        <img src="{{ $siteLogo }}" class="h-10 w-auto">
                    @else
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-black text-white">Créer un compte</h1>
                    <p class="text-orange-100 text-xs font-medium mt-1">Gratuit & rapide ✨</p>
                </div>
            </div>
        </div>

        {{-- ---- MOBILE FORM (bottom-sheet style) ---- --}}
        <div class="lg:hidden flex-1 bg-white rounded-t-[2rem] -mt-5 relative z-10 shadow-2xl shadow-black/10 overflow-y-auto">
            <div class="px-6 pt-5 pb-36">
                {{-- Pull indicator --}}
                <div class="w-10 h-1.5 bg-gray-200 rounded-full mx-auto mb-5"></div>

                {{-- Errors inline --}}
                @if($errors->any())
                <div class="mb-5 p-4 bg-red-50 border border-red-100 rounded-2xl flex items-start gap-3">
                    <div class="w-8 h-8 bg-red-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-red-700 uppercase tracking-widest mb-1">Erreur d'inscription</p>
                        @foreach($errors->all() as $error)
                        <p class="text-xs text-red-600 font-medium leading-relaxed">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
                @endif

                <h2 class="text-2xl font-black text-gray-900 mb-0.5">Inscription</h2>
                <p class="text-xs text-gray-500 mb-5 font-medium">Remplissez vos informations ci-dessous</p>

                <form action="{{ route('register') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Nom --}}
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nom complet</label>
                        <div class="relative flex items-center bg-gray-50 rounded-2xl border-2 border-transparent focus-within:border-orange-500 focus-within:bg-white transition-all">
                            <div class="absolute left-4 text-gray-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full pl-11 pr-4 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900 placeholder-gray-400"
                                   placeholder="Jean Dupont">
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Adresse email</label>
                        <div class="relative flex items-center bg-gray-50 rounded-2xl border-2 border-transparent focus-within:border-orange-500 focus-within:bg-white transition-all">
                            <div class="absolute left-4 text-gray-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full pl-11 pr-4 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900 placeholder-gray-400"
                                   placeholder="vous@exemple.com">
                        </div>
                    </div>

                    {{-- Téléphone --}}
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Téléphone</label>
                        <div x-data="{
                                open: false,
                                selected: '{{ old('phone_prefix', '+228') }}',
                                options: [
                                    @foreach($countries as $country)
                                        { prefix: '{{ $country->phone_prefix }}', name: '{{ $country->name }}', icon: '{{ $country->flag_icon }}' },
                                    @endforeach
                                ],
                                get selectedOption() {
                                    return this.options.find(o => o.prefix === this.selected) || (this.options.length > 0 ? this.options[0] : {prefix: '', icon: ''});
                                }
                            }" class="flex gap-2">
                            <input type="hidden" name="phone_prefix" :value="selected">
                            {{-- Prefix Picker --}}
                            <div class="relative">
                                <button type="button" @click="open = !open"
                                        class="h-full px-3 bg-gray-50 rounded-2xl border-2 border-transparent flex items-center gap-2 font-bold text-sm text-gray-900 active:scale-95 transition-all whitespace-nowrap">
                                    <span class="text-base" x-text="selectedOption.icon"></span>
                                    <span class="text-xs font-black" x-text="selected"></span>
                                    <svg class="w-3 h-3 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak
                                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     class="absolute z-50 mt-2 w-64 bg-white rounded-2xl shadow-2xl shadow-black/10 border border-gray-100 py-2 max-h-56 overflow-y-auto left-0">
                                    <template x-for="opt in options" :key="opt.prefix">
                                        <button type="button" @click="selected = opt.prefix; open = false"
                                                class="w-full px-4 py-2.5 text-left flex items-center gap-3 transition-colors"
                                                :class="selected === opt.prefix ? 'bg-orange-50 text-orange-600' : 'hover:bg-gray-50 text-gray-700'">
                                            <span class="text-xl" x-text="opt.icon"></span>
                                            <div>
                                                <div class="text-xs font-black" x-text="opt.prefix"></div>
                                                <div class="text-[9px] font-bold text-gray-400 uppercase tracking-tight" x-text="opt.name"></div>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            {{-- Number input --}}
                            <div class="flex-1 flex items-center bg-gray-50 rounded-2xl border-2 border-transparent focus-within:border-orange-500 focus-within:bg-white transition-all">
                                <input type="tel" name="telephone" value="{{ old('telephone') }}" required
                                       class="w-full px-4 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900 placeholder-gray-400"
                                       placeholder="XX XXX XX XX">
                            </div>
                        </div>
                    </div>

                    {{-- Mot de passe --}}
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Mot de passe</label>
                        <div class="relative flex items-center bg-gray-50 rounded-2xl border-2 border-transparent focus-within:border-orange-500 focus-within:bg-white transition-all">
                            <div class="absolute left-4 text-gray-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input :type="showPass ? 'text' : 'password'" name="password" required
                                   class="w-full pl-11 pr-12 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900 placeholder-gray-400"
                                   placeholder="Min. 8 caractères">
                            <button type="button" @click="showPass = !showPass" class="absolute right-4 text-gray-400 active:text-orange-500 transition-colors">
                                <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Confirmation mot de passe --}}
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Confirmer le mot de passe</label>
                        <div class="relative flex items-center bg-gray-50 rounded-2xl border-2 border-transparent focus-within:border-orange-500 focus-within:bg-white transition-all">
                            <div class="absolute left-4 text-gray-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <input :type="showPassConf ? 'text' : 'password'" name="password_confirmation" required
                                   class="w-full pl-11 pr-12 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900 placeholder-gray-400"
                                   placeholder="Répétez le mot de passe">
                            <button type="button" @click="showPassConf = !showPassConf" class="absolute right-4 text-gray-400 active:text-orange-500 transition-colors">
                                <svg x-show="!showPassConf" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPassConf" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- CGU --}}
                    <label class="flex items-start gap-3 cursor-pointer py-1">
                        <div class="relative mt-0.5">
                            <input type="checkbox" name="terms" required
                                   class="w-5 h-5 text-orange-500 border-2 border-gray-300 rounded-lg focus:ring-orange-500 focus:ring-2">
                        </div>
                        <span class="text-xs text-gray-600 leading-relaxed font-medium">
                            J'accepte les <a href="#" class="text-orange-500 font-black underline">conditions d'utilisation</a> et la <a href="#" class="text-orange-500 font-black underline">politique de confidentialité</a>
                        </span>
                    </label>

                    {{-- Submit --}}
                    <button type="submit"
                            class="w-full py-4 bg-gradient-to-r from-[#ff4d00] to-orange-500 text-white font-black rounded-2xl text-sm uppercase tracking-widest shadow-xl shadow-orange-500/30 active:scale-[0.97] transition-all">
                        Créer mon compte →
                    </button>
                </form>

                {{-- Divider --}}
                <div class="relative my-5">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-100"></div></div>
                    <div class="relative flex justify-center"><span class="px-4 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest">Déjà inscrit ?</span></div>
                </div>

                {{-- Login link --}}
                <a href="{{ route('login') }}" class="block w-full text-center py-4 border-2 border-gray-100 rounded-2xl font-black text-sm text-gray-700 active:border-orange-500 active:text-orange-500 transition-all">
                    Se connecter
                </a>
            </div>
        </div>

        {{-- ---- DESKTOP FORM CARD ---- --}}
        <div class="hidden lg:block w-full max-w-md py-8">
            <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 p-8">
                <div class="mb-8">
                    <h2 class="text-3xl font-black text-gray-900 mb-2">Créer un compte</h2>
                    <p class="text-gray-500">Remplissez vos informations ci-dessous</p>
                </div>
                <form action="{{ route('register') }}" method="POST" class="space-y-4">
                    @csrf
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">Nom complet</label><input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition-all" placeholder="Jean Dupont"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">Adresse email</label><input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition-all" placeholder="vous@exemple.com"></div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Téléphone</label>
                        <div class="flex gap-2">
                            <div x-data="{
                                    open: false,
                                    selected: '{{ old('phone_prefix', '+228') }}',
                                    options: [
                                        @foreach($countries as $country){ prefix: '{{ $country->phone_prefix }}', name: '{{ $country->name }}', icon: '{{ $country->flag_icon }}' },@endforeach
                                    ],
                                    get selectedOption() { return this.options.find(o => o.prefix === this.selected) || (this.options.length > 0 ? this.options[0] : {prefix: '', icon: ''}); }
                                }" class="relative w-32">
                                <input type="hidden" name="phone_prefix" :value="selected">
                                <button type="button" @click="open = !open" class="w-full px-3 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition-all bg-gray-50 font-bold text-sm flex items-center justify-between">
                                    <div class="flex items-center gap-2"><span class="text-lg" x-text="selectedOption.icon"></span><span x-text="selected"></span></div>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="absolute z-50 mt-2 w-64 bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 max-h-64 overflow-y-auto">
                                    <template x-for="opt in options" :key="opt.prefix"><button type="button" @click="selected = opt.prefix; open = false" class="w-full px-4 py-3 text-left hover:bg-orange-50 transition-colors flex items-center gap-3" :class="selected === opt.prefix ? 'bg-orange-50 text-[#ff4d00]' : 'text-gray-700'"><span class="text-2xl" x-text="opt.icon"></span><div><div class="font-black text-sm" x-text="opt.prefix"></div><div class="text-[10px] font-bold text-gray-400 uppercase tracking-tight" x-text="opt.name"></div></div></button></template>
                                </div>
                            </div>
                            <input type="tel" name="telephone" value="{{ old('telephone') }}" required class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition-all" placeholder="XX XXX XX XX">
                        </div>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">Mot de passe</label><input type="password" name="password" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition-all" placeholder="••••••••"><p class="mt-1 text-xs text-gray-500">Minimum 8 caractères</p></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">Confirmer le mot de passe</label><input type="password" name="password_confirmation" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition-all" placeholder="••••••••"></div>
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="terms" required class="mt-1 w-5 h-5 text-[#ff4d00] border-gray-300 rounded focus:ring-[#ff4d00]">
                        <span class="text-sm text-gray-600">J'accepte les <a href="#" class="text-[#ff4d00] font-bold hover:underline">conditions d'utilisation</a> et la <a href="#" class="text-[#ff4d00] font-bold hover:underline">politique de confidentialité</a></span>
                    </label>
                    <button type="submit" class="w-full bg-gradient-to-r from-[#ff4d00] to-orange-600 text-white font-black py-4 rounded-xl hover:shadow-2xl hover:shadow-orange-200 transition-all transform hover:scale-[1.02] active:scale-[0.98]">Créer mon compte</button>
                </form>
                <div class="relative my-6"><div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div><div class="relative flex justify-center text-sm"><span class="px-4 bg-white text-gray-500 font-medium">Déjà inscrit ?</span></div></div>
                <a href="{{ route('login') }}" class="block w-full text-center py-4 border-2 border-gray-200 rounded-xl font-bold text-gray-700 hover:border-[#ff4d00] hover:text-[#ff4d00] hover:bg-orange-50 transition-all">Se connecter</a>
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
                    <h3 class="text-lg font-black text-gray-900 mb-2">Erreur d'inscription</h3>
                    <ul class="space-y-1 text-sm text-gray-600">@foreach($errors->all() as $error)<li class="flex items-start gap-2"><span class="text-red-500 mt-0.5">•</span><span>{{ $error }}</span></li>@endforeach</ul>
                </div>
            </div>
            <button @click="showError = false" class="mt-6 w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl transition">Fermer</button>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
