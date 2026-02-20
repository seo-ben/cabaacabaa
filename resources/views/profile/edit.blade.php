@extends('layouts.app')

@section('content')

{{-- MOBILE --}}
<div class="block lg:hidden bg-gray-50 dark:bg-slate-950 min-h-screen pb-28" x-data="{ tab: 'info', sp: false, np: false, cp: false }">

    {{-- Header --}}
    <div class="relative bg-gradient-to-br from-gray-900 via-slate-800 to-gray-900 px-5 pt-10 pb-14 overflow-hidden">
        <div class="absolute -top-8 -right-8 w-32 h-32 bg-red-500/20 rounded-full blur-3xl"></div>
        <div class="relative z-10 flex items-center gap-4">
            <div class="relative">
                <div class="w-16 h-16 rounded-2xl overflow-hidden bg-gray-800 shrink-0">
                    @if($user->photo_profil)
                        <img src="{{ asset('storage/' . $user->photo_profil) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center text-white text-2xl font-black">{{ substr($user->name, 0, 1) }}</div>
                    @endif
                </div>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="avf">
                    @csrf @method('PATCH')
                    <label class="absolute -bottom-1 -right-1 w-6 h-6 bg-orange-500 rounded-lg flex items-center justify-center cursor-pointer shadow-lg">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        <input type="file" name="photo_profil" class="hidden" accept="image/*" onchange="document.getElementById('avf').submit()">
                    </label>
                </form>
            </div>
            <div>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Mon Profil</p>
                <h1 class="text-xl font-black text-white">{{ $user->name }}</h1>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ $user->email }}</p>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    <div class="px-4 mt-4 space-y-2">
        @if(session('success'))
        <div class="flex items-center gap-3 p-3.5 bg-green-50 dark:bg-green-900/20 rounded-2xl border border-green-100 dark:border-green-800">
            <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span class="text-xs font-bold text-green-700 dark:text-green-400">{{ session('success') }}</span>
        </div>
        @endif
        @if($errors->any())
        <div class="p-3.5 bg-red-50 dark:bg-red-900/20 rounded-2xl border border-red-100 dark:border-red-800">
            @foreach($errors->all() as $e)<p class="text-xs font-bold text-red-700 dark:text-red-400">{{ $e }}</p>@endforeach
        </div>
        @endif
    </div>

    {{-- Tab Switcher --}}
    <div class="px-4 mt-4">
        <div class="flex gap-2 bg-white dark:bg-slate-900 rounded-2xl p-1.5 border border-gray-100 dark:border-slate-800 shadow-sm">
            <button @click="tab='info'" :class="tab==='info'?'bg-gradient-to-r from-red-600 to-orange-500 text-white shadow-lg':'text-gray-500'" class="flex-1 py-2.5 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">Informations</button>
            <button @click="tab='security'" :class="tab==='security'?'bg-gradient-to-r from-red-600 to-orange-500 text-white shadow-lg':'text-gray-500'" class="flex-1 py-2.5 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">Sécurité</button>
        </div>
    </div>

    {{-- Informations Tab --}}
    <div x-show="tab==='info'" x-transition class="px-4 mt-4">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf @method('PATCH')
            @foreach([['name','Nom complet','text','M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', old('name',$user->name)],['email','Email','email','M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', old('email',$user->email)],['telephone','Téléphone','tel','M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', old('telephone',$user->telephone)]] as [$n,$l,$t,$icon,$v])
            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest pl-1">{{ $l }}</label>
                <div class="flex items-center bg-white dark:bg-slate-900 rounded-2xl border-2 border-transparent focus-within:border-red-500 transition-all shadow-sm">
                    <div class="pl-4 text-gray-400 shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg></div>
                    <input type="{{ $t }}" name="{{ $n }}" value="{{ $v }}" class="w-full pl-3 pr-4 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900 dark:text-white placeholder-gray-400">
                </div>
            </div>
            @endforeach
            <button type="submit" class="w-full py-4 bg-gradient-to-r from-gray-900 to-gray-800 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg active:scale-[0.98] transition-all">Enregistrer</button>
        </form>
    </div>

    {{-- Security Tab --}}
    <div x-show="tab==='security'" x-transition class="px-4 mt-4 space-y-3">
        <form action="{{ route('password.update') }}" method="POST" class="space-y-3">
            @csrf @method('PUT')

            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest pl-1">Mot de passe actuel</label>
                <div class="relative flex items-center bg-white dark:bg-slate-900 rounded-2xl border-2 border-transparent focus-within:border-red-500 transition-all shadow-sm">
                    <div class="pl-4 text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
                    <input :type="sp?'text':'password'" name="current_password" class="w-full pl-3 pr-12 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900 dark:text-white" placeholder="••••••••">
                    <button type="button" @click="sp=!sp" class="absolute right-4 text-gray-400">
                        <svg x-show="!sp" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="sp" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest pl-1">Nouveau mot de passe</label>
                <div class="relative flex items-center bg-white dark:bg-slate-900 rounded-2xl border-2 border-transparent focus-within:border-red-500 transition-all shadow-sm">
                    <div class="pl-4 text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg></div>
                    <input :type="np?'text':'password'" name="password" class="w-full pl-3 pr-12 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900 dark:text-white" placeholder="••••••••">
                    <button type="button" @click="np=!np" class="absolute right-4 text-gray-400">
                        <svg x-show="!np" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="np" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest pl-1">Confirmer le nouveau mot de passe</label>
                <div class="relative flex items-center bg-white dark:bg-slate-900 rounded-2xl border-2 border-transparent focus-within:border-red-500 transition-all shadow-sm">
                    <div class="pl-4 text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
                    <input :type="cp?'text':'password'" name="password_confirmation" class="w-full pl-3 pr-12 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900 dark:text-white" placeholder="••••••••">
                    <button type="button" @click="cp=!cp" class="absolute right-4 text-gray-400">
                        <svg x-show="!cp" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="cp" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full py-4 bg-gradient-to-r from-red-600 to-orange-500 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-red-500/25 active:scale-[0.98] transition-all">Changer le mot de passe</button>
        </form>

        <form action="{{ route('logout') }}" method="POST" class="mt-2">
            @csrf
            <button type="submit" class="w-full py-4 bg-white dark:bg-slate-900 border border-red-100 dark:border-red-900/30 text-red-600 dark:text-red-400 rounded-2xl text-[10px] font-black uppercase tracking-widest active:scale-[0.98] transition-all">Se déconnecter</button>
        </form>
    </div>
</div>
{{-- END MOBILE --}}


{{-- DESKTOP --}}
<div class="hidden lg:block max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-10">
        <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Mon Profil</h1>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-2">Gérez vos informations personnelles et préférences de sécurité.</p>
    </div>
    @if(session('success'))
        <div class="mb-8 p-4 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-2xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="text-sm font-bold">{{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="mb-8 p-4 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-2xl">
            <ul class="list-disc list-inside text-sm font-bold">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="hidden md:block col-span-1 space-y-2">
            <a href="#general" class="block px-4 py-3 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 font-bold rounded-xl text-sm">Informations Générales</a>
            <a href="#security" class="block px-4 py-3 text-gray-500 dark:text-gray-400 font-medium hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white rounded-xl text-sm transition-colors">Sécurité</a>
            <form action="{{ route('logout') }}" method="POST" class="pt-4 mt-4 border-t border-gray-100 dark:border-gray-800">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-3 text-red-600 dark:text-red-400 font-bold hover:bg-red-50 dark:hover:bg-red-900/10 rounded-xl text-sm transition-colors">Se déconnecter</button>
            </form>
        </div>
        <div class="col-span-1 md:col-span-2 space-y-12">
            <section id="general" class="space-y-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                    <h2 class="text-xl font-black text-gray-900 dark:text-white">Informations Générales</h2>
                </div>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-900 rounded-[2rem] p-8 border border-gray-100 dark:border-gray-800 shadow-sm space-y-6">
                    @csrf @method('PATCH')
                    <div class="flex items-center gap-6">
                        <div class="relative group">
                            <div class="w-24 h-24 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800">
                                @if($user->photo_profil)<img src="{{ asset('storage/' . $user->photo_profil) }}" class="w-full h-full object-cover">
                                @else<div class="w-full h-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center text-white text-3xl font-black">{{ substr($user->name, 0, 1) }}</div>@endif
                            </div>
                            <label class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer rounded-2xl">
                                <span class="text-xs font-bold text-white uppercase tracking-widest">Modifier</span>
                                <input type="file" name="photo_profil" class="hidden" accept="image/*" onchange="form.submit()">
                            </label>
                        </div>
                        <div><h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest mb-1">Photo de profil</h3><p class="text-xs text-gray-500 dark:text-gray-400">JPG, GIF ou PNG. 2MB max.</p></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-50 dark:border-gray-800">
                        <div class="space-y-2"><label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nom complet</label><input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border border-transparent rounded-xl text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 outline-none"></div>
                        <div class="space-y-2"><label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border border-transparent rounded-xl text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 outline-none"></div>
                        <div class="space-y-2 md:col-span-2"><label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Téléphone</label><input type="tel" name="telephone" value="{{ old('telephone', $user->telephone) }}" class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border border-transparent rounded-xl text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 outline-none"></div>
                    </div>
                    <div class="pt-4 flex justify-end"><button type="submit" class="px-8 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl text-[11px] font-black uppercase tracking-widest hover:bg-black transition-all">Enregistrer les modifications</button></div>
                </form>
            </section>
            <section id="security" class="space-y-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
                    <h2 class="text-xl font-black text-gray-900 dark:text-white">Sécurité</h2>
                </div>
                <form action="{{ route('password.update') }}" method="POST" class="bg-white dark:bg-gray-900 rounded-[2rem] p-8 border border-gray-100 dark:border-gray-800 shadow-sm space-y-6">
                    @csrf @method('PUT')
                    <div class="space-y-4">
                        <div class="space-y-2"><label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Mot de passe actuel</label><input type="password" name="current_password" class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border border-transparent rounded-xl text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 outline-none"></div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2"><label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nouveau mot de passe</label><input type="password" name="password" class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border border-transparent rounded-xl text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 outline-none"></div>
                            <div class="space-y-2"><label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Confirmer</label><input type="password" name="password_confirmation" class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border border-transparent rounded-xl text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 outline-none"></div>
                        </div>
                    </div>
                    <div class="pt-4 flex justify-end"><button type="submit" class="px-8 py-3 bg-red-600 text-white rounded-xl text-[11px] font-black uppercase tracking-widest hover:bg-red-700 transition-all shadow-lg shadow-red-600/20">Changer le mot de passe</button></div>
                </form>
            </section>
        </div>
    </div>
</div>
{{-- END DESKTOP --}}

@endsection
