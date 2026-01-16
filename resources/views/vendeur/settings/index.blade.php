@extends('layouts.vendor')

@section('title', 'Paramètres Boutique')
@section('page_title', 'Configuration Boutique')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    <!-- Status Compact -->
    <div class="bg-gradient-to-r {{ $vendeur->actif ? 'from-emerald-600 to-green-600' : 'from-rose-600 to-red-600' }} rounded-2xl p-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($vendeur->actif)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        @endif
                    </svg>
                </div>
                <div class="text-white">
                    <h3 class="text-lg font-bold">{{ $vendeur->actif ? 'Boutique Ouverte' : 'Boutique Fermée' }}</h3>
                    <p class="text-xs text-white/70">{{ $vendeur->actif ? 'Visible pour les clients' : 'Invisible pour les clients' }}</p>
                </div>
            </div>
            <form action="{{ vendor_route('vendeur.slug.settings.toggle') }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-900 rounded-lg text-xs font-bold uppercase tracking-wide transition-all shadow-lg hover:scale-105 active:scale-95">
                    {{ $vendeur->actif ? 'Fermer' : 'Ouvrir' }}
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Profile Compact -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="bg-gray-900 dark:bg-gray-950 px-6 py-3 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white">Identité Commerciale</h3>
                    <span class="px-2 py-1 bg-white/10 text-white rounded text-[10px] font-bold">Public</span>
                </div>
                
                <form action="{{ vendor_route('vendeur.slug.settings.profile') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    
                    <div class="flex gap-5">
                        <div class="flex-shrink-0">
                            <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Photo</label>
                            <div class="relative group">
                                <div class="w-24 h-24 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600">
                                    <img src="{{ $vendeur->image_principale ? asset('storage/'.$vendeur->image_principale) : 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=500&q=80' }}" class="w-full h-full object-cover">
                                </div>
                                <label class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer rounded-xl">
                                    <input type="file" name="image_principale" accept="image/*" class="hidden">
                                    <span class="text-[10px] font-bold text-white uppercase">Modifier</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex-1 space-y-4">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1.5">Nom *</label>
                                <input type="text" name="nom_commercial" value="{{ $vendeur->nom_commercial }}" required
                                       class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-500/20 outline-none transition-all font-semibold text-gray-900 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1.5">Type *</label>
                                <select name="type_vendeur" required
                                        class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-500/20 outline-none transition-all font-semibold text-gray-900 dark:text-white">
                                    <option>{{ $vendeur->type_vendeur }}</option>
                                    <option>Restaurant</option>
                                    <option>Fast Food</option>
                                    <option>Café</option>
                                    <option>Boulangerie</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1.5">Description</label>
                        <textarea name="description" rows="3" 
                                  class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-500/20 outline-none transition-all text-gray-900 dark:text-white">{{ $vendeur->description }}</textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1.5">Adresse</label>
                        <input type="text" name="adresse_complete" value="{{ $vendeur->adresse_complete }}"
                               class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-500/20 outline-none transition-all font-semibold text-gray-900 dark:text-white">
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                        <h4 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-3">Réseaux Sociaux</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <input type="url" name="facebook_url" value="{{ $vendeur->facebook_url }}" placeholder="Facebook"
                                   class="px-3 py-2 text-xs bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:border-blue-500 outline-none transition-all font-semibold text-gray-900 dark:text-white placeholder-gray-400">
                            <input type="url" name="instagram_url" value="{{ $vendeur->instagram_url }}" placeholder="Instagram"
                                   class="px-3 py-2 text-xs bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:border-pink-500 outline-none transition-all font-semibold text-gray-900 dark:text-white placeholder-gray-400">
                            <input type="text" name="whatsapp_number" value="{{ $vendeur->whatsapp_number }}" placeholder="WhatsApp"
                                   class="px-3 py-2 text-xs bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:border-green-500 outline-none transition-all font-semibold text-gray-900 dark:text-white placeholder-gray-400">
                            <input type="url" name="website_url" value="{{ $vendeur->website_url }}" placeholder="Site Web"
                                   class="px-3 py-2 text-xs bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:border-gray-500 outline-none transition-all font-semibold text-gray-900 dark:text-white placeholder-gray-400">
                        </div>
                    </div>

                    <button type="submit" class="w-full px-4 py-2.5 bg-gray-900 hover:bg-black dark:bg-white dark:hover:bg-gray-100 text-white dark:text-gray-900 rounded-lg text-xs font-bold uppercase tracking-wide transition-all shadow-md hover:shadow-lg">
                        Enregistrer
                    </button>
                </form>
            </div>

            <!-- Categories Compact -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="bg-red-600 px-6 py-3 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white">Spécialités</h3>
                    <span class="px-2 py-1 bg-white/20 text-white rounded text-[10px] font-bold">Requis</span>
                </div>

                <form action="{{ vendor_route('vendeur.slug.settings.categories') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                        @php $vendeurCatIds = $vendeur->categories->pluck('id_categorie')->toArray(); @endphp
                        @foreach($allCategories as $cat)
                        <label class="relative cursor-pointer">
                            <input type="checkbox" name="categories[]" value="{{ $cat->id_categorie }}" class="sr-only peer" {{ in_array($cat->id_categorie, $vendeurCatIds) ? 'checked' : '' }}>
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg transition-all hover:border-red-300 peer-checked:bg-red-600 peer-checked:border-red-600 peer-checked:text-white dark:peer-checked:bg-red-600 dark:peer-checked:border-red-600 dark:peer-checked:text-white">
                                <p class="text-[10px] font-bold uppercase text-center truncate text-gray-700 dark:text-gray-300 peer-checked:text-white dark:peer-checked:text-white">{{ $cat->nom_categorie }}</p>
                            </div>
                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-white rounded-full shadow-md opacity-0 peer-checked:opacity-100 transition-opacity flex items-center justify-center">
                                <svg class="w-2.5 h-2.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                        <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Autre spécialité ?</label>
                        <input type="text" name="new_specialty" placeholder="Ex: Cuisine Vegan..." 
                               class="w-full px-3 py-2 text-xs bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:border-red-500 outline-none transition-all font-semibold text-gray-900 dark:text-white">
                    </div>

                    <button type="submit" class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold uppercase tracking-wide transition-all shadow-md hover:shadow-lg">
                        Mettre à Jour
                    </button>
                </form>
            </div>

        </div>

        <!-- Sidebar Compact -->
        <div class="space-y-6">
            
            <!-- Hours Compact -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="bg-indigo-600 px-6 py-3">
                    <h3 class="text-sm font-bold text-white">Horaires</h3>
                </div>
                
                <form action="{{ vendor_route('vendeur.slug.settings.hours') }}" method="POST" class="p-5 space-y-3">
                    @csrf
                    @php 
                        $jours = [
                            1 => 'Lun', 2 => 'Mar', 3 => 'Mer', 
                            4 => 'Jeu', 5 => 'Ven', 6 => 'Sam', 0 => 'Dim'
                        ];
                        $horairesExistants = $vendeur->horaires->keyBy('jour_semaine');
                    @endphp

                    @if($errors->has('hours.*'))
                        <div class="p-2 mb-2 bg-red-50 text-red-600 text-[10px] font-bold rounded-lg border border-red-100">
                            Format invalide (Ex: 08:00).
                        </div>
                    @endif

                    @foreach($jours as $id => $nom)
                    <div class="flex items-center gap-2">
                        <span class="w-8 text-[10px] font-bold text-gray-600 dark:text-gray-400">{{ $nom }}</span>
                        <input type="time" name="hours[{{ $id }}][heure_ouverture]" 
                               value="{{ optional($horairesExistants->get($id))->heure_ouverture ? substr($horairesExistants->get($id)->heure_ouverture, 0, 5) : '' }}"
                               class="w-24 px-2 py-1.5 text-[11px] bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-center font-bold focus:border-indigo-500 outline-none text-gray-900 dark:text-white">
                        <span class="text-gray-300 dark:text-gray-600">-</span>
                        <input type="time" name="hours[{{ $id }}][heure_fermeture]"
                               value="{{ optional($horairesExistants->get($id))->heure_fermeture ? substr($horairesExistants->get($id)->heure_fermeture, 0, 5) : '' }}"
                               class="w-24 px-2 py-1.5 text-[11px] bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-center font-bold focus:border-indigo-500 outline-none text-gray-900 dark:text-white">
                        <label class="ml-auto cursor-pointer">
                            <input type="checkbox" name="hours[{{ $id }}][ferme]" {{ optional($horairesExistants->get($id))->ferme ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-8 h-4 bg-gray-200 dark:bg-gray-600 peer-checked:bg-red-500 rounded-full relative transition-colors">
                                <div class="absolute top-0.5 left-0.5 w-3 h-3 bg-white rounded-full transition-transform peer-checked:translate-x-4"></div>
                            </div>
                        </label>
                    </div>
                    @endforeach

                    <button type="submit" class="w-full mt-3 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold uppercase tracking-wide transition-all shadow-md">
                        Sauvegarder
                    </button>
                </form>
            </div>

            <!-- Stats Compact -->
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white shadow-lg">
                <h4 class="text-xs font-bold uppercase mb-4 opacity-70">Performance</h4>
                <div class="space-y-4">
                    <div class="bg-white/10 backdrop-blur rounded-xl p-3">
                        <p class="text-[10px] font-bold opacity-60 mb-1">Note Moyenne</p>
                        <div class="flex items-end gap-2">
                            <p class="text-2xl font-black">{{ number_format($vendeur->note_moyenne, 1) }}</p>
                            <span class="text-sm opacity-50 pb-0.5">/5</span>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur rounded-xl p-3">
                        <p class="text-[10px] font-bold opacity-60 mb-1">Commandes ce Mois</p>
                        <p class="text-2xl font-black">{{ $vendeur->nombre_commandes_mois ?? 0 }}</p>
                    </div>

                    <div class="bg-white/10 backdrop-blur rounded-xl p-3">
                        <p class="text-[10px] font-bold opacity-60 mb-1">Revenus Estimés</p>
                        <p class="text-xl font-black">{{ number_format(($vendeur->nombre_commandes_mois ?? 0) * 8500) }} <span class="text-xs">FCFA</span></p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-4">
                <h4 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-3">Actions Rapides</h4>
                <div class="space-y-2">
                    <a href="#" class="flex items-center justify-between px-3 py-2 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-all group">
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Gérer Plats</span>
                        <svg class="w-3 h-3 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="#" class="flex items-center justify-between px-3 py-2 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-all group">
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Commandes</span>
                        <svg class="w-3 h-3 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="#" class="flex items-center justify-between px-3 py-2 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-all group">
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Statistiques</span>
                        <svg class="w-3 h-3 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

@if(session('success'))
<div class="fixed bottom-4 right-4 z-50 animate-slide-in">
    <div class="bg-green-600 text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span class="text-sm font-bold">{{ session('success') }}</span>
    </div>
</div>
@endif

@endsection