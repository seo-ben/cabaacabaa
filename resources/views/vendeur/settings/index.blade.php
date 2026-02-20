@extends('layouts.vendor')

@section('title', 'Paramètres Boutique')
@section('page_title', 'Configuration Boutique')

@section('extra_head')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .leaflet-container { font-family: inherit; }
</style>
@endsection

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
                    
                    @if($errors->any())
                        <div class="p-3 bg-red-50 border border-red-200 rounded-xl">
                            <ul class="list-disc list-inside text-[11px] text-red-600 font-bold">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
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
                                <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1.5">Type de Boutique *</label>
                                @if($vendeur->id_category_vendeur)
                                    <div class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg font-semibold text-gray-500 flex items-center justify-between">
                                        {{ $vendeur->category->name }}
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    </div>
                                    <input type="hidden" name="id_category_vendeur" value="{{ $vendeur->id_category_vendeur }}">
                                @else
                                    <select name="id_category_vendeur" required
                                            class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-500/20 outline-none transition-all font-semibold text-gray-900 dark:text-white">
                                        <option value="" disabled selected>Choisir un type...</option>
                                        @foreach($vendorCategories as $cat)
                                            <option value="{{ $cat->id_category_vendeur }}" {{ $vendeur->id_category_vendeur == $cat->id_category_vendeur ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                                <p class="text-[9px] text-gray-400 mt-1 italic font-medium">Le type de boutique ne peut plus être modifié une fois défini pour plus de sécurité.</p>
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
                            <input type="text" name="facebook_url" value="{{ old('facebook_url', $vendeur->facebook_url) }}" placeholder="Lien Facebook (ex: facebook.com/votrepage)"
                                   class="px-3 py-2 text-xs bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:border-blue-500 outline-none transition-all font-semibold text-gray-900 dark:text-white placeholder-gray-400">
                            <input type="text" name="instagram_url" value="{{ old('instagram_url', $vendeur->instagram_url) }}" placeholder="Lien Instagram"
                                   class="px-3 py-2 text-xs bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:border-pink-500 outline-none transition-all font-semibold text-gray-900 dark:text-white placeholder-gray-400">
                            <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $vendeur->whatsapp_number) }}" placeholder="Numéro WhatsApp"
                                   class="px-3 py-2 text-xs bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:border-green-500 outline-none transition-all font-semibold text-gray-900 dark:text-white placeholder-gray-400">
                            <input type="text" name="website_url" value="{{ old('website_url', $vendeur->website_url) }}" placeholder="Site Web"
                                   class="px-3 py-2 text-xs bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:border-gray-500 outline-none transition-all font-semibold text-gray-900 dark:text-white placeholder-gray-400">
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                            <div>
                                <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">Localisation de la Boutique</h4>
                                <p class="text-[10px] text-gray-400 font-medium">Déplacez le marqueur pour définir l'emplacement exact.</p>
                            </div>
                            <button type="button" onclick="detectMyPosition()" class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-100 transition-all active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                Trouver ma position
                            </button>
                        </div>
                        
                        <div class="relative group">
                            <div id="vendor-map-settings" class="w-full h-72 sm:h-96 rounded-2xl border-2 border-gray-100 dark:border-gray-700 overflow-hidden z-0 shadow-inner"></div>
                            <div class="absolute bottom-4 left-4 z-[1000] pointer-events-none">
                                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-gray-100 dark:border-gray-700 shadow-sm">
                                    <p class="text-[9px] font-black uppercase text-gray-400 tracking-tighter">Note</p>
                                    <p class="text-[10px] font-bold text-gray-900 dark:text-white">Glissez le marqueur 🏢</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div class="p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-800">
                                <label class="block text-[9px] font-black text-gray-400 uppercase mb-1 tracking-widest">Latitude</label>
                                <input type="text" name="latitude" id="lat-input" value="{{ $vendeur->latitude }}" readonly
                                       class="w-full bg-transparent text-xs font-mono font-bold text-gray-600 dark:text-gray-400 outline-none">
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-800">
                                <label class="block text-[9px] font-black text-gray-400 uppercase mb-1 tracking-widest">Longitude</label>
                                <input type="text" name="longitude" id="lng-input" value="{{ $vendeur->longitude }}" readonly
                                       class="w-full bg-transparent text-xs font-mono font-bold text-gray-600 dark:text-gray-400 outline-none">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full px-4 py-2.5 bg-gray-900 border-none hover:bg-black dark:bg-white dark:hover:bg-gray-100 text-white dark:text-gray-900 rounded-lg text-xs font-bold uppercase tracking-wide transition-all shadow-md hover:shadow-lg">
                        Enregistrer les modifications
                    </button>
                </form>
            </div>

            <!-- Categories Compact -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="bg-red-600 px-6 py-3 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white">Catégories d'Articles</h3>
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
                        <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Autre catégorie ?</label>
                        <input type="text" name="new_specialty" placeholder="Ex: Électronique, Mode..." 
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
                    <a href="{{ vendor_route('vendeur.slug.plats.index') }}" class="flex items-center justify-between px-3 py-2 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-all group">
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Gérer Articles</span>
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

@if($errors->any())
<div class="fixed bottom-4 right-4 z-50 animate-slide-in">
    <div class="bg-red-600 text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-sm font-bold">Veuillez corriger les erreurs dans le formulaire.</span>
    </div>
</div>
@endif

@if(session('error'))
<div class="fixed bottom-4 right-4 z-50 animate-slide-in">
    <div class="bg-red-600 text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-sm font-bold">{{ session('error') }}</span>
    </div>
</div>
@endif

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

@section('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initial coordinates (current vendor position or default)
        let lat = {{ $vendeur->latitude ?? 6.1319 }}; // Default to Lome if empty
        let lng = {{ $vendeur->longitude ?? 1.2227 }};
        let zoom = {{ $vendeur->latitude ? 16 : 12 }};

        const map = L.map('vendor-map-settings').setView([lat, lng], zoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Custom icon
        const vendorIcon = L.divIcon({
            className: 'custom-vendor-marker',
            html: '<div style="background:#f97316; width:30px; height:30px; border-radius:50%; border:3px solid white; box-shadow:0 2px 10px rgba(0,0,0,0.2); display:flex; align-items:center; justify-content:center; color:white; font-size:16px;">🛍️</div>',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });

        const marker = L.marker([lat, lng], {
            draggable: true,
            icon: vendorIcon
        }).addTo(map);

        // Update inputs on drag end
        marker.on('dragend', function(event) {
            const position = marker.getLatLng();
            document.getElementById('lat-input').value = position.lat.toFixed(6);
            document.getElementById('lng-input').value = position.lng.toFixed(6);
        });

        // Click on map to move marker
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            document.getElementById('lat-input').value = e.latlng.lat.toFixed(6);
            document.getElementById('lng-input').value = e.latlng.lng.toFixed(6);
        });

        // Helper to show toast (safe fallback)
        const showToast = (message, type = 'info') => {
            if (window.showToast) {
                window.showToast(message, type);
            } else {
                // simple fallback if not defined in layout
                const toast = document.createElement('div');
                toast.className = `fixed bottom-4 right-4 z-[9999] px-6 py-3 rounded-2xl text-white text-xs font-black uppercase tracking-widest shadow-2xl animate-in slide-in-from-right-10 ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
                toast.innerText = message;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 4000);
            }
        };

        // Function to detect position
        window.detectMyPosition = function() {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const newLat = position.coords.latitude;
                    const newLng = position.coords.longitude;
                    
                    map.setView([newLat, newLng], 16);
                    marker.setLatLng([newLat, newLng]);
                    
                    document.getElementById('lat-input').value = newLat.toFixed(6);
                    document.getElementById('lng-input').value = newLng.toFixed(6);
                    
                    showToast('Position détectée avec succès !', 'success');
                }, function(error) {
                    showToast('Impossible de détecter votre position : ' + error.message, 'error');
                }, {
                    enableHighAccuracy: true
                });
            } else {
                showToast('La géolocalisation n\'est pas supportée par votre navigateur.', 'error');
            }
        }
        
        // Fix map size issues if hidden initially
        setTimeout(() => {
            map.invalidateSize();
        }, 500);
    });
</script>
@endsection