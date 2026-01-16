@extends('layouts.admin')

@section('title', 'Éditer Vendeur')

@section('content')
<div class="max-w-4xl mx-auto space-y-10">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.vendors.index') }}" class="group flex items-center gap-3 text-gray-400 hover:text-red-600 transition-colors">
            <div class="w-10 h-10 bg-white border border-gray-100 rounded-xl flex items-center justify-center group-hover:bg-red-50 group-hover:border-red-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </div>
            <span class="text-xs font-black uppercase tracking-widest">Retour à la liste</span>
        </a>
        <div>
            <span class="px-4 py-2 bg-gray-50 text-gray-400 rounded-xl text-[10px] font-black uppercase tracking-widest border border-gray-100">ID Vendeur: #{{ $vendeur->id_vendeur }}</span>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-10 py-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/20">
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Profil de {{ $vendeur->nom_commercial }}</h1>
                <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest mt-1">Édition des informations administratives</p>
            </div>
            <div class="flex items-center gap-4">
                @if($vendeur->actif)
                    <span class="flex items-center gap-2 px-3 py-1 bg-green-50 text-green-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-green-100">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                        Compte Actif
                    </span>
                @else
                    <span class="px-3 py-1 bg-red-50 text-red-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-red-100">
                        Compte Inactif
                    </span>
                @endif
            </div>
        </div>

        <form action="{{ route('admin.vendors.update', $vendeur->id_vendeur) }}" method="POST" class="p-10 space-y-10">
            @csrf
            @method('PUT')

            <!-- Section: Responsable -->
            <div class="space-y-6">
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center text-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Informations du Gérant</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Nom complet</label>
                        <input type="text" name="name" value="{{ $vendeur->user->name ?? old('name') }}" required
                               class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all @error('name') border-red-500 @enderror">
                        @error('name')<span class="text-red-600 text-[10px] font-bold ml-2">{{ $message }}</span>@enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Email de connexion</label>
                        <input type="email" name="email" value="{{ $vendeur->user->email ?? old('email') }}" required
                               class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all @error('email') border-red-500 @enderror">
                        @error('email')<span class="text-red-600 text-[10px] font-bold ml-2">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <!-- Section: Boutique -->
            <div class="space-y-6">
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center text-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Détails de la Boutique</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Nom Commercial</label>
                        <input type="text" name="nom_commercial" value="{{ $vendeur->nom_commercial ?? old('nom_commercial') }}" required
                               class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all @error('nom_commercial') border-red-500 @enderror">
                        @error('nom_commercial')<span class="text-red-600 text-[10px] font-bold ml-2">{{ $message }}</span>@enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Type d'Établissement</label>
                        <select name="type_vendeur" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all appearance-none cursor-pointer">
                            <option value="restaurant" @selected($vendeur->type_vendeur === 'restaurant')>Restaurant</option>
                            <option value="epicerie" @selected($vendeur->type_vendeur === 'epicerie')>Épicerie</option>
                            <option value="cafe" @selected($vendeur->type_vendeur === 'cafe')>Café / Lounge</option>
                            <option value="boulangerie" @selected($vendeur->type_vendeur === 'boulangerie')>Boulangerie</option>
                            <option value="other" @selected($vendeur->type_vendeur === 'other')>Autre Service</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Adresse Complète</label>
                    <input type="text" name="adresse_complete" value="{{ $vendeur->adresse_complete ?? old('adresse_complete') }}" required
                           class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all @error('adresse_complete') border-red-500 @enderror">
                    @error('adresse_complete')<span class="text-red-600 text-[10px] font-bold ml-2">{{ $message }}</span>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Téléphone Commercial</label>
                        <input type="text" name="telephone_commercial" value="{{ $vendeur->telephone_commercial ?? old('telephone_commercial') }}"
                               class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Zone Géographique</label>
                        <select name="id_zone" class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all appearance-none cursor-pointer">
                            <option value="">Aucune zone spécifique</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id_zone }}" @selected($vendeur->id_zone === $zone->id_zone)>
                                    {{ $zone->nom ?? 'Zone ' . $zone->id_zone }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Description Boutique</label>
                    <textarea name="description" rows="4" class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-[2rem] font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all resize-none @error('description') border-red-500 @enderror">{{ $vendeur->description ?? old('description') }}</textarea>
                    @error('description')<span class="text-red-600 text-[10px] font-bold ml-2">{{ $message }}</span>@enderror
                </div>
            </div>

            <!-- Section: Statut -->
            <div class="bg-gray-50 rounded-[2rem] p-8 space-y-6 border border-gray-100">
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Contrôle de l'accès</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Niveau de Vérification</label>
                        <select name="statut_verification" required class="w-full px-6 py-4 bg-white border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:border-black outline-none transition-all appearance-none cursor-pointer shadow-sm">
                            <option value="non_verifie" @selected($vendeur->statut_verification === 'non_verifie')>Compte Incomplet / Non vérifié</option>
                            <option value="en_cours" @selected($vendeur->statut_verification === 'en_cours')>Attente de validation (En cours)</option>
                            <option value="verifie" @selected($vendeur->statut_verification === 'verifie')>Boutique Validée (Officiel)</option>
                            <option value="suspendu" @selected($vendeur->statut_verification === 'suspendu')>Accès Suspendu</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Statut d'Affichage</label>
                        <select name="actif" required class="w-full px-6 py-4 bg-white border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:border-black outline-none transition-all appearance-none cursor-pointer shadow-sm">
                            <option value="0" @selected(!$vendeur->actif)>Masqué (Inactif sur le site)</option>
                            <option value="1" @selected($vendeur->actif)>Visible (Actif sur le site)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 pt-4 border-t border-gray-50 pb-10">
                <button type="submit" class="flex-1 px-10 py-5 bg-red-600 text-white rounded-[2rem] font-black uppercase text-[11px] tracking-widest shadow-xl shadow-red-200 hover:bg-black hover:shadow-none transition-all active:scale-95 flex items-center justify-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Enregistrer les changements
                </button>
                <a href="{{ route('admin.vendors.index') }}" class="px-10 py-5 bg-gray-100 text-gray-600 rounded-[2rem] font-black uppercase text-[11px] tracking-widest hover:bg-gray-200 transition-all">
                    Annuler
                </a>
            </div>
        </form>
    </div>

    <!-- Payout Information (Coming Soon or Wallet Info) -->
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-10 flex items-center justify-between group overflow-hidden relative">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-gray-50 rounded-full scale-0 group-hover:scale-100 transition duration-700"></div>
        <div class="relative z-10 flex items-center gap-6">
            <div class="w-16 h-16 bg-red-50 rounded-[1.5rem] flex items-center justify-center text-red-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h4 class="text-lg font-black text-gray-900 tracking-tight">Solde du Portefeuille</h4>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Le vendeur possède actuellement : {{ number_format($vendeur->wallet_balance ?? 0, 0, ',', ' ') }} {{ $currency }}</p>
            </div>
        </div>
        <div class="relative z-10">
            <a href="{{ route('admin.finance.index', ['search' => $vendeur->nom_commercial]) }}" class="px-8 py-4 bg-gray-900 text-white rounded-[1.5rem] font-black uppercase text-[10px] tracking-widest hover:bg-black transition-all shadow-xl active:scale-95">
                Voir les transactions
            </a>
        </div>
    </div>
</div>
@endsection
