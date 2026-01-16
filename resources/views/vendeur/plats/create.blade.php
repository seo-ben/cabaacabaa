@extends('layouts.vendor')

@section('title', 'Nouveau Plat')
@section('page_title', 'Ajouter une spécialité')

@section('content')
<div class="max-w-4xl mx-auto space-y-10">
    
    <div class="flex items-center justify-between">
        <a href="{{ vendor_route('vendeur.slug.plats.index') }}" class="flex items-center gap-3 px-6 py-3 bg-white border border-gray-100 text-gray-400 hover:text-gray-900 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            RETOUR AU MENU
        </a>
    </div>

    <form action="{{ vendor_route('vendeur.slug.plats.store') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <!-- Left: Main Info -->
            <div class="lg:col-span-2 space-y-10">
                <div class="bg-white rounded-[3rem] p-10 border border-gray-100 shadow-sm space-y-8">
                    <h3 class="text-xl font-black text-gray-900 border-b border-gray-50 pb-6">Informations du Plat</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Dénomination du Chef</label>
                            <input type="text" name="nom_plat" value="{{ old('nom_plat') }}" required placeholder="Ex: Risotto aux Champignons des Bois"
                                   class="w-full px-8 py-5 bg-gray-50 border border-gray-100 rounded-[1.5rem] focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-bold text-gray-900 placeholder:text-gray-300">
                            @error('nom_plat') <p class="text-red-600 text-[10px] mt-2 font-black uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Univers Culinaire</label>
                                <select name="id_categorie" required class="w-full px-8 py-5 bg-gray-50 border border-gray-100 rounded-[1.5rem] focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-bold text-gray-900 appearance-none">
                                    <option value="">Sélectionner...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id_categorie }}" {{ old('id_categorie') == $category->id_categorie ? 'selected' : '' }}>
                                            {{ $category->nom_categorie }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_categorie') <p class="text-red-600 text-[10px] mt-2 font-black uppercase tracking-widest">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Tarification (FCFA)</label>
                                <div class="relative">
                                    <input type="number" name="prix" value="{{ old('prix') }}" required placeholder="Ex: 5000"
                                           class="w-full px-8 py-5 bg-gray-50 border border-gray-100 rounded-[1.5rem] focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-bold text-gray-900">
                                    <span class="absolute right-8 top-1/2 -translate-y-1/2 text-gray-300 font-black text-xs uppercase tracking-tighter">XOF</span>
                                </div>
                                @error('prix') <p class="text-red-600 text-[10px] mt-2 font-black uppercase tracking-widest">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Secret du Chef & Ingrédients</label>
                            <textarea name="description" rows="5" placeholder="Décrivez l'expérience gustative, les allergènes et la préparation..."
                                      class="w-full px-8 py-5 bg-gray-50 border border-gray-100 rounded-[1.5rem] focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-medium text-gray-700 leading-relaxed">{{ old('description') }}</textarea>
                            @error('description') <p class="text-red-600 text-[10px] mt-2 font-black uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Extensions & Options (Variants) - Spanning Full Width or integrated in Flow -->
            <div class="lg:col-span-3 space-y-10">
                <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-10 border border-gray-100 dark:border-gray-700 shadow-sm space-y-8" x-data="variantManager()">
                    <div class="flex items-center justify-between border-b border-gray-50 dark:border-gray-700 pb-6">
                        <h3 class="text-xl font-black text-gray-900 dark:text-white">Options & Suppléments</h3>
                        <button type="button" @click="addGroup()" class="px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-black dark:hover:bg-gray-200 transition-colors">
                            + Groupe
                        </button>
                    </div>

                    <div class="space-y-8">
                        <template x-for="(group, gIndex) in groups" :key="gIndex">
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/50 rounded-[2rem] border border-gray-200 dark:border-gray-600 space-y-6 relative group">
                                <button type="button" @click="removeGroup(gIndex)" class="absolute top-4 right-4 text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>

                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <input type="text" :name="`variants[${gIndex}][groupe_nom]`" x-model="group.name" placeholder="Nom du groupe (ex: Taille, Sauce)" required
                                                class="w-full px-5 py-3 bg-white dark:bg-gray-800 border border-transparent rounded-xl text-xs font-bold text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-gray-900 dark:focus:ring-white outline-none">
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" :name="`variants[${gIndex}][obligatoire]`" value="1" x-model="group.obligatoire" class="w-4 h-4 rounded text-red-600 dark:text-red-500 focus:ring-red-500 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600">
                                                <span class="text-[10px] font-black uppercase text-gray-500 dark:text-gray-400">Obligatoire</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" :name="`variants[${gIndex}][choix_multiple]`" value="1" x-model="group.multiple" class="w-4 h-4 rounded text-red-600 dark:text-red-500 focus:ring-red-500 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600">
                                                <span class="text-[10px] font-black uppercase text-gray-500 dark:text-gray-400">Choix Multiple</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div x-show="group.multiple" class="flex gap-4 p-3 bg-gray-100 dark:bg-gray-800 rounded-xl">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-black uppercase text-gray-500 dark:text-gray-400">Min:</span>
                                            <input type="number" :name="`variants[${gIndex}][min_choix]`" min="0" value="0" class="w-16 px-2 py-1 bg-white dark:bg-gray-700 rounded text-center text-xs font-bold text-gray-900 dark:text-white">
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-black uppercase text-gray-500 dark:text-gray-400">Max:</span>
                                            <input type="number" :name="`variants[${gIndex}][max_choix]`" min="1" value="5" class="w-16 px-2 py-1 bg-white dark:bg-gray-700 rounded text-center text-xs font-bold text-gray-900 dark:text-white">
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-3 pl-4 border-l-2 border-gray-200 dark:border-gray-600">
                                    <template x-for="(option, oIndex) in group.options" :key="oIndex">
                                        <div class="flex items-center gap-3">
                                            <input type="text" :name="`variants[${gIndex}][options][${oIndex}][nom]`" x-model="option.name" placeholder="Option (ex: Ketchup)" required
                                                class="flex-1 px-4 py-2 bg-white dark:bg-gray-800 border border-transparent rounded-lg text-xs font-semibold text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 outline-none">
                                            
                                            <div class="relative w-32">
                                                <input type="number" :name="`variants[${gIndex}][options][${oIndex}][prix]`" x-model="option.price" placeholder="0" min="0" step="25"
                                                    class="w-full pl-3 pr-8 py-2 bg-white dark:bg-gray-800 border border-transparent rounded-lg text-xs font-bold text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 outline-none">
                                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[9px] font-black text-gray-300 dark:text-gray-500">FCFA</span>
                                            </div>

                                            <button type="button" @click="removeOption(gIndex, oIndex)" class="p-2 text-gray-300 dark:text-gray-600 hover:text-red-500 dark:hover:text-red-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                    
                                    <button type="button" @click="addOption(gIndex)" class="mt-2 text-[10px] font-black uppercase tracking-widest text-red-600 dark:text-red-400 hover:underline">
                                        + Ajouter une option
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div x-show="groups.length === 0" class="text-center py-8 text-gray-400 dark:text-gray-500">
                            <p class="text-xs italic">Aucune option configurée pour ce plat.</p>
                        </div>
                    </div>

                    <script>
                        function variantManager() {
                            return {
                                groups: [],
                                addGroup() {
                                    this.groups.push({
                                        name: '',
                                        obligatoire: false,
                                        multiple: false,
                                        options: [{ name: '', price: 0 }]
                                    });
                                },
                                removeGroup(index) {
                                    this.groups.splice(index, 1);
                                },
                                addOption(gIndex) {
                                    this.groups[gIndex].options.push({ name: '', price: 0 });
                                },
                                removeOption(gIndex, oIndex) {
                                    this.groups[gIndex].options.splice(oIndex, 1);
                                }
                            }
                        }
                    </script>
                </div>
            </div>

            <!-- Right: Media & Status -->
            <div class="space-y-10">
                <!-- Image Upload -->
                <div class="bg-white rounded-[3rem] p-10 border border-gray-100 shadow-sm space-y-8">
                    <h3 class="text-xl font-black text-gray-900 border-b border-gray-50 pb-6">Visuel</h3>
                    <div x-data="{ photoName: null, photoPreview: null }" class="space-y-6">
                        <!-- Input réel caché -->
                        <input type="file" name="image" class="hidden" x-ref="photo"
                               @change="
                                    photoName = $event.target.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($event.target.files[0]);
                               ">

                        <!-- Zone de preview/drop -->
                        <div class="relative aspect-square rounded-[2rem] overflow-hidden bg-gray-50 border-2 border-dashed border-gray-200 group hover:border-red-200 transition-all cursor-pointer"
                             @click="$refs.photo.click()">
                            
                            <template x-if="!photoPreview">
                                <div class="absolute inset-0 flex flex-col items-center justify-center p-8 text-center space-y-4">
                                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-gray-300 group-hover:text-red-500 shadow-sm transition-colors">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 00-2 2z"/></svg>
                                    </div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Cliquez pour ajouter la photo principale</p>
                                </div>
                            </template>

                            <template x-if="photoPreview">
                                <div class="absolute inset-0">
                                    <img :src="photoPreview" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-white">Changer la photo</span>
                                    </div>
                                </div>
                            </template>
                        </div>
                        @error('image') <p class="text-red-600 text-[10px] mt-2 font-black uppercase tracking-widest">{{ $message }}</p> @enderror
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest text-center">Format recommandé : Carré (1080x1080)</p>
                    </div>
                </div>

                <!-- Submit Card -->
                <div class="bg-gray-900 rounded-[3rem] p-10 shadow-2xl shadow-gray-200 space-y-6">
                    <p class="text-[10px] font-black text-white/40 uppercase tracking-[0.2em]">Finalisation</p>
                    <button type="submit" class="w-full py-5 bg-red-600 text-white rounded-[1.5rem] font-black text-sm uppercase tracking-widest hover:bg-red-700 transition-all shadow-xl shadow-red-600/20 active:scale-95">
                        PUBLIER AU MENU
                    </button>
                    <a href="{{ vendor_route('vendeur.slug.plats.index') }}" class="block w-full text-center text-[10px] font-black text-white/60 uppercase tracking-widest hover:text-white transition-colors">
                        Abandonner
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
