@extends('layouts.auth')

@section('title', 'Mot de passe oublié')

@section('content')
<div class="min-h-screen flex" x-data="{}">

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
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </div>
            <h2 class="text-4xl font-black text-white leading-tight">Récupérez<br>votre compte</h2>
            <p class="text-white/70 text-lg leading-relaxed">Entrez votre email et nous vous enverrons un lien sécurisé pour réinitialiser votre mot de passe.</p>
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
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <p class="text-white font-black text-xl tracking-tight">Mot de passe oublié ?</p>
                <p class="text-white/70 text-xs text-center px-8">Pas de panique, on vous envoie un lien de réinitialisation</p>
            </div>
        </div>

        {{-- Form Area --}}
        <div class="lg:hidden flex-1 bg-white rounded-t-[2rem] -mt-6 relative z-10 shadow-2xl shadow-black/10 overflow-y-auto">
            <div class="px-6 pt-6 pb-32">
                <div class="w-10 h-1.5 bg-gray-200 rounded-full mx-auto mb-6"></div>

                @if(session('success'))
                <div class="mb-5 p-4 bg-green-50 border border-green-100 rounded-2xl flex items-start gap-3">
                    <div class="w-8 h-8 bg-green-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-sm text-green-700 font-bold leading-relaxed">{{ session('success') }}</p>
                </div>
                @endif

                @if($errors->any())
                <div class="mb-5 p-4 bg-red-50 border border-red-100 rounded-2xl">
                    @foreach($errors->all() as $e)<p class="text-sm text-red-600 font-bold">{{ $e }}</p>@endforeach
                </div>
                @endif

                @if(!session('success'))
                <form action="{{ route('password.forgot.post') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Votre adresse email</label>
                        <div class="flex items-center bg-gray-50 rounded-2xl border-2 border-transparent focus-within:border-orange-500 focus-within:bg-white transition-all">
                            <div class="pl-4 text-gray-400 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="w-full pl-3 pr-4 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900 placeholder-gray-400"
                                   placeholder="votre@email.com">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-[#ff4d00] to-orange-500 text-white font-black rounded-2xl text-[11px] uppercase tracking-widest shadow-xl shadow-orange-500/30 active:scale-[0.97] transition-all">
                        Envoyer le lien →
                    </button>
                </form>
                @endif

                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Retour à la connexion
                    </a>
                </div>
            </div>
        </div>

        {{-- Desktop Form --}}
        <div class="hidden lg:flex flex-1 items-center justify-center p-12">
            <div class="w-full max-w-md space-y-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Mot de passe oublié ?</h1>
                    <p class="text-gray-500 font-medium mt-2">Entrez votre email et nous vous enverrons un lien sécurisé.</p>
                </div>

                @if(session('success'))
                <div class="p-5 bg-green-50 border border-green-100 rounded-2xl flex items-start gap-4">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-sm text-green-700 font-bold leading-relaxed">{{ session('success') }}</p>
                </div>
                @endif

                @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-100 rounded-2xl">
                    @foreach($errors->all() as $e)<p class="text-sm text-red-600 font-bold">{{ $e }}</p>@endforeach
                </div>
                @endif

                @if(!session('success'))
                <form action="{{ route('password.forgot.post') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Adresse email</label>
                        <div class="flex items-center bg-gray-50 rounded-2xl border-2 border-transparent focus-within:border-orange-500 focus-within:bg-white transition-all">
                            <div class="pl-4 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="w-full pl-3 pr-5 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900 placeholder-gray-400"
                                   placeholder="votre@email.com">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-[#ff4d00] to-orange-500 text-white font-black rounded-2xl text-[11px] uppercase tracking-widest shadow-xl shadow-orange-500/30 hover:shadow-orange-500/40 transition-all active:scale-[0.98]">
                        Envoyer le lien de réinitialisation →
                    </button>
                    <p class="text-center text-xs text-gray-400 font-medium">Vérifiez aussi votre dossier spam si vous ne recevez pas l'email.</p>
                </form>
                @endif

                <div class="text-center">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Retour à la connexion
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
