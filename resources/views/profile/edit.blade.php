@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <!-- Header -->
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
            <ul class="list-disc list-inside text-sm font-bold">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Sidebar Navigation (Desktop) -->
        <div class="hidden md:block col-span-1 space-y-2">
            <a href="#general" class="block px-4 py-3 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 font-bold rounded-xl text-sm">Informations Générales</a>
            <a href="#security" class="block px-4 py-3 text-gray-500 dark:text-gray-400 font-medium hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white rounded-xl text-sm transition-colors">Sécurité</a>
            <a href="#preferences" class="block px-4 py-3 text-gray-500 dark:text-gray-400 font-medium hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white rounded-xl text-sm transition-colors">Préférences</a>
            <form action="{{ route('logout') }}" method="POST" class="pt-4 mt-4 border-t border-gray-100 dark:border-gray-800">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-3 text-red-600 dark:text-red-400 font-bold hover:bg-red-50 dark:hover:bg-red-900/10 rounded-xl text-sm transition-colors">
                    Se déconnecter
                </button>
            </form>
        </div>

        <!-- Main Content (Forms) -->
        <div class="col-span-1 md:col-span-2 space-y-12">
            
            <!-- General Info Section -->
            <section id="general" class="space-y-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-500 dark:text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h2 class="text-xl font-black text-gray-900 dark:text-white">Informations Générales</h2>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-900 rounded-[2rem] p-8 border border-gray-100 dark:border-gray-800 shadow-sm space-y-6">
                    @csrf
                    @method('PATCH')

                    <!-- Photo Upload -->
                    <div class="flex items-center gap-6">
                        <div class="relative group">
                            <div class="w-24 h-24 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800">
                                @if($user->photo_profil)
                                    <img src="{{ asset('storage/' . $user->photo_profil) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center text-white text-3xl font-black">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <label class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer rounded-2xl">
                                <span class="text-xs font-bold text-white uppercase tracking-widest">Modifier</span>
                                <input type="file" name="photo_profil" class="hidden" accept="image/*" onchange="form.submit()">
                            </label>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest mb-1">Photo de profil</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">JPG, GIF ou PNG. 2MB max.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-50 dark:border-gray-800">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Nom complet</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border border-transparent rounded-xl text-sm font-bold text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-red-500 transition-all outline-none">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border border-transparent rounded-xl text-sm font-bold text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-red-500 transition-all outline-none">
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Téléphone</label>
                            <input type="tel" name="telephone" value="{{ old('telephone', $user->telephone) }}" placeholder="+228..." class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border border-transparent rounded-xl text-sm font-bold text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-red-500 transition-all outline-none">
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl text-[11px] font-black uppercase tracking-widest hover:bg-black dark:hover:bg-gray-200 transition-all">
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </section>

            <!-- Security Section -->
            <section id="security" class="space-y-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-500 dark:text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h2 class="text-xl font-black text-gray-900 dark:text-white">Sécurité</h2>
                </div>

                <form action="{{ route('password.update') }}" method="POST" class="bg-white dark:bg-gray-900 rounded-[2rem] p-8 border border-gray-100 dark:border-gray-800 shadow-sm space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Mot de passe actuel</label>
                            <input type="password" name="current_password" class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border border-transparent rounded-xl text-sm font-bold text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-red-500 transition-all outline-none">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Nouveau mot de passe</label>
                                <input type="password" name="password" class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border border-transparent rounded-xl text-sm font-bold text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-red-500 transition-all outline-none">
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Confirmer le nouveau mot de passe</label>
                                <input type="password" name="password_confirmation" class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border border-transparent rounded-xl text-sm font-bold text-gray-900 dark:text-white focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-red-500 transition-all outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-red-600 text-white rounded-xl text-[11px] font-black uppercase tracking-widest hover:bg-red-700 transition-all shadow-lg shadow-red-600/20">
                            Changer le mot de passe
                        </button>
                    </div>
                </form>
            </section>

        </div>
    </div>
</div>
@endsection
