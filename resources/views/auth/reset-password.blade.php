@extends('layouts.auth')

@section('title', 'Nouveau mot de passe')

@section('content')
<div class="min-h-screen flex" x-data="{ sp: false, cp: false }">

    {{-- LEFT PANEL (Desktop) --}}
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-[#ff4d00] via-orange-600 to-red-700 p-12 flex-col justify-between relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-1/2 translate-y-1/2"></div>
        </div>
        <div class="relative z-10">
            <a href="/" class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-2xl font-black text-white tracking-tight">{{ config('app.name') }}</span>
            </a>
        </div>
        <div class="relative z-10 space-y-6">
            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h2 class="text-4xl font-black text-white leading-tight">Créez un<br>nouveau mot<br>de passe</h2>
            <p class="text-white/70 text-lg leading-relaxed">Choisissez un mot de passe fort d'au moins 8 caractères.</p>
            <div class="space-y-3">
                @foreach(['Au moins 8 caractères', 'Lettres et chiffres recommandés', 'Ne pas réutiliser l\'ancien'] as $tip)
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 bg-white/20 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-white/80 text-sm font-medium">{{ $tip }}</span>
                </div>
                @endforeach
            </div>
        </div>
        <div class="relative z-10 text-white/50 text-sm">© {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</div>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="flex-1 flex flex-col bg-white relative overflow-hidden">

        {{-- Mobile Hero --}}
        <div class="lg:hidden relative h-48 bg-gradient-to-br from-[#ff4d00] via-orange-600 to-red-700 overflow-hidden shrink-0">
            <div class="absolute -top-8 -right-8 w-32 h-32 bg-white/10 rounded-full"></div>
            <div class="absolute inset-0 flex flex-col items-center justify-center gap-3">
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <p class="text-white font-black text-xl tracking-tight">Nouveau mot de passe</p>
                <p class="text-white/70 text-xs">Minimum 8 caractères</p>
            </div>
        </div>

        {{-- Mobile Form --}}
        <div class="lg:hidden flex-1 bg-white rounded-t-[2rem] -mt-6 relative z-10 shadow-2xl shadow-black/10 overflow-y-auto">
            <div class="px-6 pt-6 pb-32">
                <div class="w-10 h-1.5 bg-gray-200 rounded-full mx-auto mb-6"></div>

                @if($errors->any())
                <div class="mb-5 p-4 bg-red-50 border border-red-100 rounded-2xl">
                    @foreach($errors->all() as $e)<p class="text-sm text-red-600 font-bold">{{ $e }}</p>@endforeach
                </div>
                @endif

                <form action="{{ route('password.reset.post') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nouveau mot de passe</label>
                        <div class="relative flex items-center bg-gray-50 rounded-2xl border-2 border-transparent focus-within:border-orange-500 focus-within:bg-white transition-all">
                            <div class="pl-4 text-gray-400 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            </div>
                            <input :type="sp ? 'text' : 'password'" name="password" required
                                   class="w-full pl-3 pr-12 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900"
                                   placeholder="••••••••" minlength="8">
                            <button type="button" @click="sp=!sp" class="absolute right-4 text-gray-400">
                                <svg x-show="!sp" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="sp" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Confirmer le mot de passe</label>
                        <div class="relative flex items-center bg-gray-50 rounded-2xl border-2 border-transparent focus-within:border-orange-500 focus-within:bg-white transition-all">
                            <div class="pl-4 text-gray-400 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <input :type="cp ? 'text' : 'password'" name="password_confirmation" required
                                   class="w-full pl-3 pr-12 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900"
                                   placeholder="••••••••">
                            <button type="button" @click="cp=!cp" class="absolute right-4 text-gray-400">
                                <svg x-show="!cp" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="cp" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-[#ff4d00] to-orange-500 text-white font-black rounded-2xl text-[11px] uppercase tracking-widest shadow-xl shadow-orange-500/30 active:scale-[0.97] transition-all">
                        Enregistrer le nouveau mot de passe →
                    </button>
                </form>
            </div>
        </div>

        {{-- Desktop Form --}}
        <div class="hidden lg:flex flex-1 items-center justify-center p-12">
            <div class="w-full max-w-md space-y-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Créez votre nouveau mot de passe</h1>
                    <p class="text-gray-500 font-medium mt-2">Choisissez un mot de passe fort d'au moins 8 caractères.</p>
                </div>

                @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-100 rounded-2xl">
                    @foreach($errors->all() as $e)<p class="text-sm text-red-600 font-bold">{{ $e }}</p>@endforeach
                </div>
                @endif

                <form action="{{ route('password.reset.post') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nouveau mot de passe</label>
                        <div class="relative flex items-center bg-gray-50 rounded-2xl border-2 border-transparent focus-within:border-orange-500 focus-within:bg-white transition-all">
                            <div class="pl-4 text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg></div>
                            <input :type="sp ? 'text' : 'password'" name="password" required minlength="8"
                                   class="w-full pl-3 pr-12 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900"
                                   placeholder="Minimum 8 caractères">
                            <button type="button" @click="sp=!sp" class="absolute right-4 text-gray-400 hover:text-gray-600 transition-colors">
                                <svg x-show="!sp" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="sp" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Confirmer le mot de passe</label>
                        <div class="relative flex items-center bg-gray-50 rounded-2xl border-2 border-transparent focus-within:border-orange-500 focus-within:bg-white transition-all">
                            <div class="pl-4 text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
                            <input :type="cp ? 'text' : 'password'" name="password_confirmation" required
                                   class="w-full pl-3 pr-12 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900"
                                   placeholder="Répétez le mot de passe">
                            <button type="button" @click="cp=!cp" class="absolute right-4 text-gray-400 hover:text-gray-600 transition-colors">
                                <svg x-show="!cp" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="cp" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-[#ff4d00] to-orange-500 text-white font-black rounded-2xl text-[11px] uppercase tracking-widest shadow-xl shadow-orange-500/30 hover:shadow-orange-500/40 transition-all active:scale-[0.98]">
                        Enregistrer le nouveau mot de passe →
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
