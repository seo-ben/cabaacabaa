@extends('layouts.admin')

@section('title', 'CMS Profil Vendeur')

@section('content')
<div class="max-w-6xl mx-auto" x-data="{ 
    tab: 'infos',
    sections: [],
    days: [
        { id: 1, name: 'Lundi' }, { id: 2, name: 'Mardi' }, { id: 3, name: 'Mercredi' },
        { id: 4, name: 'Jeudi' }, { id: 5, name: 'Vendredi' }, { id: 6, name: 'Samedi' }, { id: 0, name: 'Dimanche' }
    ],
    addSection() {
        this.sections.push({ type: 'texte', titre: '', contenu: '', active: true });
    },
    removeSection(index) {
        this.sections.splice(index, 1);
    }
}">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Configuration Commerciale</h1>
            <p class="text-gray-500 font-medium italic">Enrichissement du profil pour {{ $user->nom_complet }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 rounded-xl font-bold hover:bg-gray-50 transition shadow-sm">
                Annuler
            </a>
            <button form="enrichment-form" type="submit" class="px-8 py-2.5 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition shadow-lg shadow-red-100 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.1" d="M5 13l4 4L19 7"/></svg>
                Enregistrer Tout
            </button>
        </div>
    </div>

    <!-- Navigation par Onglets -->
    <div class="flex overflow-x-auto gap-2 mb-6 p-1 bg-gray-100 rounded-2xl w-fit border border-gray-200/50">
        <button @click="tab = 'infos'" :class="tab === 'infos' ? 'bg-white text-red-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-200">
            1. Informations
        </button>
        <button @click="tab = 'contacts'" :class="tab === 'contacts' ? 'bg-white text-red-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-200">
            2. Contacts
        </button>
        <button @click="tab = 'horaires'" :class="tab === 'horaires' ? 'bg-white text-red-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-200">
            3. Horaires
        </button>
        <button @click="tab = 'sections'" :class="tab === 'sections' ? 'bg-white text-red-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-200">
            4. Modules CMS
        </button>
        <button @click="tab = 'media'" :class="tab === 'media' ? 'bg-white text-red-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-200">
            5. Galerie
        </button>
        <button @click="tab = 'menu'" :class="tab === 'menu' ? 'bg-white text-red-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-200">
            6. Mon Menu
        </button>
    </div>

    <form id="enrichment-form" action="{{ route('admin.vendors.store-enrichment', $user->id_user) }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- ONGLET 1: INFOS -->
        <div x-show="tab === 'infos'" x-transition class="space-y-6">
            <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nom de l'enseigne</label>
                        <input type="text" name="nom_commercial" value="{{ old('nom_commercial', $user->nom_complet) }}" required 
                               class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-red-500 focus:bg-white outline-none transition-all font-bold text-gray-900">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Type de Boutique / Catégorie</label>
                        <select name="id_category_vendeur" required class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-red-500 focus:bg-white outline-none transition-all font-bold text-gray-900">
                            <option value="">Sélectionner une catégorie...</option>
                            @foreach($vendorCategories as $cat)
                                <option value="{{ $cat->id_category_vendeur }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Description Commerciale</label>
                    <textarea name="description" rows="4" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-red-500 focus:bg-white outline-none transition-all font-medium text-gray-700" placeholder="Racontez votre histoire..."></textarea>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Zone de Service</label>
                    <select name="id_zone" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-red-500 focus:bg-white outline-none transition-all font-bold text-gray-900">
                        <option value="">Sélectionner une zone</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id_zone }}">{{ $zone->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- ONGLET 2: CONTACTS -->
        <div x-show="tab === 'contacts'" x-transition class="space-y-6">
            <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <h3 class="text-sm font-black text-gray-900 uppercase">Coordonnées Physiques</h3>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Adresse Principale</label>
                            <input type="text" name="contact[adresse_ligne_1]" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Quartier</label>
                                <input type="text" name="contact[quartier]" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Ville</label>
                                <input type="text" name="contact[ville]" value="Paris" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Lien Google Maps</label>
                            <input type="url" name="contact[lien_google_maps]" placeholder="https://goo.gl/maps/..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        </div>
                    </div>
                    <div class="space-y-6">
                        <h3 class="text-sm font-black text-gray-900 uppercase">Communications</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Tél Principal</label>
                                <input type="text" name="contact[telephone_principal]" value="{{ $user->telephone }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">WhatsApp</label>
                                <input type="text" name="contact[whatsapp]" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none transition-all" placeholder="+33...">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Email de contact direct</label>
                            <input type="email" name="contact[email_contact]" value="{{ $user->email }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Rayon de livraison (mètres)</label>
                            <input type="number" name="contact[rayon_service_metre]" value="5000" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ONGLET 3: HORAIRES -->
        <div x-show="tab === 'horaires'" x-transition class="space-y-6">
            <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="text-left border-b border-gray-100">
                            <th class="pb-4 text-xs font-black text-gray-400 uppercase tracking-widest w-1/4">Jour</th>
                            <th class="pb-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Ouverture</th>
                            <th class="pb-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Fermeture</th>
                            <th class="pb-4 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Fermé ?</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="(day, index) in days" :key="day.id">
                            <tr>
                                <td class="py-5">
                                    <span class="font-black text-gray-900" x-text="day.name"></span>
                                    <input type="hidden" :name="'horaires['+day.id+'][jour_semaine]'" :value="day.id">
                                </td>
                                <td class="py-5 text-center">
                                    <input type="time" :name="'horaires['+day.id+'][heure_ouverture]'" value="09:00" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-red-500">
                                </td>
                                <td class="py-5 text-center">
                                    <input type="time" :name="'horaires['+day.id+'][heure_fermeture]'" value="18:00" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-red-500">
                                </td>
                                <td class="py-5 text-right">
                                    <input type="checkbox" :name="'horaires['+day.id+'][ferme]'" class="w-5 h-5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ONGLET 4: SECTIONS CMS -->
        <div x-show="tab === 'sections'" x-transition class="space-y-6">
            <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-black text-gray-900">Blocs de contenu</h2>
                    <button type="button" @click="addSection()" class="px-4 py-2 bg-gray-900 text-white rounded-xl text-sm font-bold hover:bg-black transition">
                        + Ajouter un bloc
                    </button>
                </div>
                
                <div class="space-y-4">
                    <template x-for="(section, index) in sections" :key="index">
                        <div class="p-6 bg-gray-50 rounded-2xl border border-gray-200 relative group">
                            <button @click="removeSection(index)" class="absolute -top-2 -right-2 w-8 h-8 bg-white border border-gray-200 text-red-600 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Type</label>
                                    <select :name="'sections['+index+'][type_section]'" x-model="section.type" class="w-full px-3 py-2 border rounded-lg bg-white">
                                        <option value="texte">📝 Texte / Histoire</option>
                                        <option value="info">ℹ️ Informations Clés</option>
                                        <option value="gallerie">🖼️ Galerie Photos</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Titre du bloc</label>
                                    <input type="text" :name="'sections['+index+'][titre]'" x-model="section.titre" class="w-full px-3 py-2 border rounded-lg bg-white" placeholder="Ex: Notre Savoir-faire">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Contenu</label>
                                <textarea :name="'sections['+index+'][contenu]'" x-model="section.contenu" rows="3" class="w-full px-3 py-2 border rounded-lg bg-white"></textarea>
                            </div>
                            <input type="hidden" :name="'sections['+index+'][position]'" :value="index">
                        </div>
                    </template>
                    <div x-show="sections.length === 0" class="py-12 text-center text-gray-400 border-2 border-dashed border-gray-100 rounded-3xl font-medium">
                        Aucun bloc de contenu personnalisé ajouté.
                    </div>
                </div>
            </div>
        </div>

        <!-- ONGLET 5: MEDIA -->
        <div x-show="tab === 'media'" x-transition class="space-y-6">
            <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm space-y-8">
                <div>
                    <h3 class="text-sm font-black text-gray-900 uppercase mb-4">Images Vitrine</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-6 border-2 border-dashed border-gray-200 rounded-3xl text-center space-y-4">
                            <div class="w-16 h-16 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center mx-auto">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-gray-900 font-bold">Image Principale (Banner)</p>
                                <p class="text-xs text-gray-500 mt-1">Recommandé: 1200x400px</p>
                            </div>
                            <input type="file" name="image_principale" class="hidden" id="main_img">
                            <label for="main_img" class="inline-block px-5 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold hover:bg-gray-50 cursor-pointer transition shadow-sm">Choisir un fichier</label>
                        </div>
                        <div class="p-6 border-2 border-dashed border-gray-200 rounded-3xl text-center space-y-4">
                            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                            </div>
                            <div>
                                <p class="text-gray-900 font-bold">Galerie d'activité</p>
                                <p class="text-xs text-gray-500 mt-1">Plusieurs photos (Ambiance, Plats)</p>
                            </div>
                            <input type="file" name="gallery[]" multiple class="hidden" id="gal_imgs">
                            <label for="gal_imgs" class="inline-block px-5 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold hover:bg-gray-50 cursor-pointer transition shadow-sm">Multi-sélection</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ONGLET 6: MON MENU (CATÉGORIES) -->
        <div x-show="tab === 'menu'" x-transition class="space-y-6 text-left">
            <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm space-y-6">
                <div>
                    <h2 class="text-xl font-black text-gray-900">Spécialités & Menu</h2>
                    <p class="text-gray-500 font-medium mb-8 italic text-sm">Sélectionnez les catégories de plats que ce vendeur est autorisé à publier. Il ne pourra pas ajouter de plats hors de cette sélection.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($categories as $category)
                        <label class="group relative flex items-center gap-4 p-4 rounded-2xl border border-gray-100 hover:border-red-200 hover:bg-red-50 transition-all cursor-pointer">
                            <input type="checkbox" name="categories[]" value="{{ $category->id_categorie }}" 
                                   class="w-6 h-6 rounded-lg border-gray-300 text-red-600 focus:ring-red-500 transition-all">
                            <div class="flex-1">
                                <span class="block font-bold text-gray-900 group-hover:text-red-700 transition-colors">{{ $category->nom_categorie }}</span>
                                @if($category->description)
                                    <span class="block text-[10px] text-gray-400 font-medium leading-tight mt-0.5">{{ Str::limit($category->description, 40) }}</span>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>

                @if(count($categories) === 0)
                    <div class="py-12 text-center text-gray-400 border-2 border-dashed border-gray-100 rounded-3xl font-medium">
                        Aucune catégorie disponible. Veuillez en créer dans la gestion des catégories.
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>

<style>
    [x-cloak] { display: none !important; }
    input:focus, select:focus, textarea:focus {
        border-color: #EF4444;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
    }
</style>
@endsection
