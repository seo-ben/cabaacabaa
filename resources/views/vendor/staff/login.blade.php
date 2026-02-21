@extends('layouts.auth')

@section('title', 'Connexion Staff - ' . $vendor->nom_commercial)

@section('content')
<div class="min-h-screen flex" x-data="{ showError: {{ $errors->any() ? 'true' : 'false' }}, showPass: false }">

    {{-- ===================== DESKTOP LEFT PANEL (Vendor Branding) ===================== --}}
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-orange-600 via-red-600 to-red-800 p-12 flex-col justify-between relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-1/2 translate-y-1/2"></div>
        </div>
        
        <div class="relative z-10">
            <div class="flex items-center gap-4 mb-16">
                @if($vendor->image_principale)
                    <img src="{{ asset('storage/' . $vendor->image_principale) }}" alt="{{ $vendor->nom_commercial }}" class="w-16 h-16 rounded-2xl object-cover border-4 border-white/20 shadow-2xl">
                @else
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-2xl">
                        <span class="text-3xl font-black text-red-600 uppercase">{{ mb_substr($vendor->nom_commercial, 0, 1) }}</span>
                    </div>
                @endif
                <div>
                    <span class="text-3xl font-black text-white tracking-tight">{{ $vendor->nom_commercial }}</span>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-orange-200">Espace Collaborateurs</p>
                </div>
            </div>

            <div class="space-y-8">
                <h1 class="text-5xl font-black text-white leading-tight">Gérez votre boutique<br>avec efficacité.</h1>
                <p class="text-xl text-orange-100 leading-relaxed max-w-md">Accédez à votre espace de travail sécurisé pour traiter les commandes et servir vos clients.</p>
            </div>
        </div>

        <div class="relative z-10 flex items-center gap-8 text-orange-100/60 font-black text-[10px] uppercase tracking-widest">
            <div class="flex items-center gap-2">
                <div class="w-1.5 h-1.5 rounded-full bg-green-400"></div>
                Sécurisé
            </div>
            <div class="flex items-center gap-2">
                <div class="w-1.5 h-1.5 rounded-full bg-orange-400"></div>
                Temps Réel
            </div>
            <div class="flex items-center gap-2">
                <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                Optimisé
            </div>
        </div>
    </div>

    {{-- ===================== RIGHT / MOBILE SIDE ===================== --}}
    <div class="flex-1 flex flex-col lg:items-center lg:justify-center relative bg-gray-50 lg:p-8">

        {{-- ---- MOBILE HERO ---- --}}
        <div class="lg:hidden relative h-[38vh] min-h-[240px] bg-gradient-to-br from-orange-600 via-red-600 to-red-800 overflow-hidden flex-shrink-0">
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full translate-x-1/3 -translate-y-1/3"></div>
            </div>
            <div class="absolute inset-0 flex flex-col items-center justify-center gap-6 px-8 text-center">
                <div class="relative">
                    @if($vendor->image_principale)
                        <img src="{{ asset('storage/' . $vendor->image_principale) }}" class="w-24 h-24 rounded-3xl object-cover shadow-2xl border-4 border-white/30">
                    @else
                        <div class="w-24 h-24 bg-white rounded-3xl flex items-center justify-center shadow-2xl">
                            <span class="text-4xl font-black text-red-600 uppercase">{{ mb_substr($vendor->nom_commercial, 0, 1) }}</span>
                        </div>
                    @endif
                    <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 border-4 border-white rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-black text-white tracking-tight">{{ $vendor->nom_commercial }}</h2>
                    <p class="text-orange-100 text-[10px] font-black uppercase tracking-[0.2em] mt-2 italic shadow-sm">Connexion Employé</p>
                </div>
            </div>
        </div>

        {{-- ---- FORM CARD ---- --}}
        <div class="flex-1 bg-white lg:bg-white lg:max-w-md lg:w-full lg:rounded-[2.5rem] lg:shadow-2xl lg:border lg:border-gray-100 lg:p-10 -mt-8 lg:mt-0 rounded-t-[2.5rem] relative z-10 shadow-2xl shadow-black/10 overflow-y-auto">
            <div class="px-8 pt-8 pb-12 lg:p-0">
                {{-- Desktop Header --}}
                <div class="hidden lg:block mb-8">
                    <h2 class="text-3xl font-black text-gray-900 tracking-tight">Bonjour ! 👋</h2>
                    <p class="text-gray-400 font-bold text-sm mt-1">Accédez à votre terminal de vente</p>
                </div>

                {{-- Errors --}}
                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl flex items-start gap-3">
                        <div class="w-8 h-8 bg-red-100 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-black text-red-700 uppercase tracking-widest mb-1">Attention</p>
                            @foreach($errors->all() as $error)
                                <p class="text-xs text-red-600 font-bold leading-tight">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form action="{{ route('vendor.staff.login.post', ['vendor_slug' => $vendor->slug]) }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    {{-- Email --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Adresse Email Pro</label>
                        <div class="relative group">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-red-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl text-sm font-bold text-gray-900 dark:text-white focus:ring-4 focus:ring-red-500/10 transition-all outline-none"
                                   placeholder="votre.nom@boutique.com">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Mot de Passe Personnel</label>
                        <div class="relative group">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-red-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input :type="showPass ? 'text' : 'password'" name="password" required
                                   class="w-full pl-12 pr-12 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl text-sm font-bold text-gray-900 dark:text-white focus:ring-4 focus:ring-red-500/10 transition-all outline-none"
                                   placeholder="••••••••">
                            <button type="button" @click="showPass = !showPass" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors">
                                <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Link Info --}}
                    <div class="px-4 py-3 bg-blue-50 dark:bg-blue-900/10 rounded-2xl flex items-center gap-3">
                        <svg class="w-5 min-w-[20px] h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        <p class="text-[10px] font-black text-blue-600/70 uppercase tracking-widest leading-relaxed line-clamp-2">Lien d'accès unique sécurisé détecté</p>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" 
                            class="w-full py-5 bg-gradient-to-r from-red-600 to-orange-500 text-white text-xs font-black uppercase tracking-[0.3em] rounded-2xl shadow-2xl shadow-red-500/30 hover:shadow-red-600/40 active:scale-[0.98] transition-all">
                        Se connecter →
                    </button>
                </form>

                <div class="mt-10 pt-8 border-t border-gray-100 dark:border-gray-800 text-center space-y-4">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-loose">
                        Ceci est un accès réservé aux employés.<br>
                        En cas de problème, contactez votre gérant.
                    </p>
                    
                    <div class="flex items-center justify-center gap-2 text-gray-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span class="text-[10px] font-bold uppercase tracking-widest">Protocole SSL 256 bits</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    /* Mobile flow optimization */
    @media (max-width: 1023px) {
        html, body { height: 100%; overflow: hidden; background: #000; }
        body > div, .min-h-screen { height: 100%; }
        .flex-1 { height: 100%; overflow: hidden; display: flex; flex-direction: column; }
    }
</style>
@endsection
