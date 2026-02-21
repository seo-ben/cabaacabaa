@extends('layouts.vendor')

@section('title', 'Nouvel Article')
@section('page_title', 'Ajouter un article')

@section('content')
<div class="max-w-4xl mx-auto" x-data="variantManager()">

    {{-- ── Header / Breadcrumb ── --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ vendor_route('vendeur.slug.plats.index') }}"
           class="flex items-center justify-center w-9 h-9 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 text-gray-500 hover:text-gray-900 dark:hover:text-white rounded-xl transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-base font-black text-gray-900 dark:text-white leading-none">Nouvel article</h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Remplissez les informations ci-dessous</p>
        </div>
    </div>

    <form action="{{ vendor_route('vendeur.slug.plats.store') }}" method="POST" enctype="multipart/form-data" id="create-plat-form">
        @csrf

        {{-- ───────────────────────────────────────
             Section 1 : IMAGE (en premier sur mobile)
        ──────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden mb-4">
            <div class="p-5 border-b border-gray-50 dark:border-gray-800">
                <h2 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                    <span class="w-5 h-5 bg-red-50 dark:bg-red-900/30 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400 text-[10px] font-black">1</span>
                    Photo de l'article
                </h2>
            </div>
            <div class="p-5" x-data="{ photoPreview: null }">
                <input type="file" name="image" class="hidden" x-ref="photo" accept="image/*"
                       @change="
                            const reader = new FileReader();
                            reader.onload = (e) => { photoPreview = e.target.result; };
                            reader.readAsDataURL($event.target.files[0]);
                       ">
                <div class="relative w-full aspect-video sm:aspect-square max-w-xs mx-auto sm:max-w-none rounded-2xl overflow-hidden bg-gray-50 dark:bg-gray-800 border-2 border-dashed border-gray-200 dark:border-gray-700 group hover:border-red-300 dark:hover:border-red-700 transition-all cursor-pointer"
                     @click="$refs.photo.click()">
                    <template x-if="!photoPreview">
                        <div class="absolute inset-0 flex flex-col items-center justify-center gap-3 p-6 text-center">
                            <div class="w-14 h-14 bg-white dark:bg-gray-700 rounded-2xl flex items-center justify-center shadow-sm text-gray-300 dark:text-gray-500 group-hover:text-red-400 transition-colors">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-black text-gray-500 dark:text-gray-400">Appuyez pour ajouter</p>
                                <p class="text-[9px] font-bold text-gray-300 dark:text-gray-600 uppercase tracking-widest mt-1">JPG, PNG, WEBP — carré recommandé</p>
                            </div>
                        </div>
                    </template>
                    <template x-if="photoPreview">
                        <div class="absolute inset-0">
                            <img :src="photoPreview" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <span class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-xl text-white text-[10px] font-black uppercase tracking-widest border border-white/20">Changer</span>
                            </div>
                        </div>
                    </template>
                </div>
                @error('image') <p class="text-red-600 text-[10px] mt-2 font-black uppercase tracking-widest text-center">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- ─────────────────────────────────────────
             Section 2 : INFOS PRINCIPALES
        ──────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden mb-4">
            <div class="p-5 border-b border-gray-50 dark:border-gray-800">
                <h2 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                    <span class="w-5 h-5 bg-red-50 dark:bg-red-900/30 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400 text-[10px] font-black">2</span>
                    Informations
                </h2>
            </div>
            <div class="p-5 space-y-5">
                {{-- Nom --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Nom de l'article *</label>
                    <input type="text" name="nom_plat" value="{{ old('nom_plat') }}" required
                           placeholder="Ex: Riz sauté poulet, Pizza Margherita..."
                           class="w-full px-4 py-3.5 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-bold text-gray-900 dark:text-white text-sm placeholder:text-gray-300 dark:placeholder:text-gray-600">
                    @error('nom_plat') <p class="text-red-600 text-[10px] mt-1.5 font-black uppercase tracking-widest">{{ $message }}</p> @enderror
                </div>

                {{-- Catégorie + Prix --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Catégorie *</label>
                        <select name="id_categorie" required
                                class="w-full px-4 py-3.5 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-bold text-gray-900 dark:text-white text-sm appearance-none">
                            <option value="">Choisir...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id_categorie }}" {{ old('id_categorie') == $category->id_categorie ? 'selected' : '' }}>
                                    {{ $category->nom_categorie }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_categorie') <p class="text-red-600 text-[10px] mt-1.5 font-black uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Prix (FCFA) *</label>
                        <div class="relative">
                            <input type="number" name="prix" value="{{ old('prix') }}" required placeholder="0"
                                   class="w-full pl-4 pr-10 py-3.5 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-bold text-gray-900 dark:text-white text-sm">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[9px] font-black text-gray-300 dark:text-gray-600 uppercase">XOF</span>
                        </div>
                        @error('prix') <p class="text-red-600 text-[10px] mt-1.5 font-black uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Description</label>
                    <textarea name="description" rows="4"
                              placeholder="Décrivez l'article : ingrédients, allergènes, particularités..."
                              class="w-full px-4 py-3.5 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-medium text-gray-700 dark:text-gray-300 text-sm leading-relaxed resize-none">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-600 text-[10px] mt-1.5 font-black uppercase tracking-widest">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────
             Section 3 : OPTIONS & SUPPLÉMENTS
        ──────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden mb-4">
            <div class="p-5 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between">
                <h2 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                    <span class="w-5 h-5 bg-red-50 dark:bg-red-900/30 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400 text-[10px] font-black">3</span>
                    Options & Suppléments
                </h2>
                <button type="button" @click="addGroup()"
                        class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-black dark:hover:bg-gray-200 transition-colors active:scale-95">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                    Groupe
                </button>
            </div>
            <div class="p-5 space-y-4">
                <template x-for="(group, gIndex) in groups" :key="gIndex">
                    <div class="p-4 bg-gray-50 dark:bg-gray-800/60 rounded-2xl border border-gray-100 dark:border-gray-700 space-y-4 relative">
                        <button type="button" @click="removeGroup(gIndex)"
                                class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-xl bg-white dark:bg-gray-700 text-gray-300 dark:text-gray-500 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        {{-- Nom du groupe --}}
                        <input type="text" :name="`variants[${gIndex}][groupe_nom]`" x-model="group.name"
                               placeholder="Nom du groupe (ex: Taille, Sauce...)" required
                               class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl text-xs font-bold text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-gray-900 dark:focus:ring-white outline-none pr-10">

                        {{-- Toggles --}}
                        <div class="flex flex-wrap gap-3">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <div class="relative">
                                    <input type="checkbox" :name="`variants[${gIndex}][obligatoire]`" value="1" x-model="group.obligatoire" class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 dark:bg-gray-700 peer-checked:bg-red-500 rounded-full transition-colors"></div>
                                    <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow-sm transition-all peer-checked:translate-x-4"></div>
                                </div>
                                <span class="text-[10px] font-black uppercase text-gray-500 dark:text-gray-400">Obligatoire</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <div class="relative">
                                    <input type="checkbox" :name="`variants[${gIndex}][choix_multiple]`" value="1" x-model="group.multiple" class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 dark:bg-gray-700 peer-checked:bg-gray-900 dark:peer-checked:bg-orange-500 rounded-full transition-colors"></div>
                                    <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow-sm transition-all peer-checked:translate-x-4"></div>
                                </div>
                                <span class="text-[10px] font-black uppercase text-gray-500 dark:text-gray-400">Multi-choix</span>
                            </label>
                        </div>

                        {{-- Min/Max (si multiple) --}}
                        <div x-show="group.multiple" class="flex gap-3">
                            <div class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-600">
                                <span class="text-[9px] font-black uppercase text-gray-400">Min</span>
                                <input type="number" :name="`variants[${gIndex}][min_choix]`" min="0" value="0"
                                       class="w-10 text-center text-xs font-bold text-gray-900 dark:text-white bg-transparent outline-none">
                            </div>
                            <div class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-600">
                                <span class="text-[9px] font-black uppercase text-gray-400">Max</span>
                                <input type="number" :name="`variants[${gIndex}][max_choix]`" min="1" value="5"
                                       class="w-10 text-center text-xs font-bold text-gray-900 dark:text-white bg-transparent outline-none">
                            </div>
                        </div>

                        {{-- Options --}}
                        <div class="space-y-2 pl-3 border-l-2 border-gray-200 dark:border-gray-600">
                            <template x-for="(option, oIndex) in group.options" :key="oIndex">
                                <div class="flex items-center gap-2">
                                    <input type="text" :name="`variants[${gIndex}][options][${oIndex}][nom]`" x-model="option.name"
                                           placeholder="Option (ex: Ketchup)" required
                                           class="flex-1 px-3 py-2.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-600 rounded-xl text-xs font-semibold text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 outline-none">
                                    <div class="relative w-24">
                                        <input type="number" :name="`variants[${gIndex}][options][${oIndex}][prix]`" x-model="option.price"
                                               placeholder="0" min="0" step="25"
                                               class="w-full pl-2 pr-7 py-2.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-600 rounded-xl text-xs font-bold text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 outline-none">
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[8px] font-black text-gray-300 dark:text-gray-500">F</span>
                                    </div>
                                    <button type="button" @click="removeOption(gIndex, oIndex)"
                                            class="flex-shrink-0 w-7 h-7 flex items-center justify-center text-gray-300 dark:text-gray-600 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                            <button type="button" @click="addOption(gIndex)"
                                    class="mt-1 flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                Ajouter une option
                            </button>
                        </div>
                    </div>
                </template>

                <div x-show="groups.length === 0" class="py-8 text-center">
                    <div class="w-12 h-12 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-3 text-gray-200 dark:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    </div>
                    <p class="text-xs font-bold text-gray-400 dark:text-gray-600">Aucune option — appuyez <strong>+ Groupe</strong> pour en créer</p>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────
             Bouton SUBMIT fixe en bas sur mobile
        ──────────────────────────────────────────── --}}
        <div class="sticky bottom-20 lg:bottom-0 lg:static mt-4 mb-2">
            <div class="bg-white/95 dark:bg-gray-950/95 backdrop-blur-md rounded-3xl border border-gray-100 dark:border-gray-800 shadow-2xl shadow-black/10 p-4 flex gap-3">
                <a href="{{ vendor_route('vendeur.slug.plats.index') }}"
                   class="flex-shrink-0 px-5 py-3.5 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-100 dark:hover:bg-gray-800 transition-all active:scale-95">
                    Annuler
                </a>
                <button type="submit" form="create-plat-form"
                        class="flex-1 py-3.5 bg-red-600 text-white rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-red-700 transition-all shadow-xl shadow-red-600/20 active:scale-95">
                    ✓ Publier au catalogue
                </button>
            </div>
        </div>

    </form>
</div>

<script>
    function variantManager() {
        return {
            groups: [],
            addGroup() {
                this.groups.push({ name: '', obligatoire: false, multiple: false, options: [{ name: '', price: 0 }] });
            },
            removeGroup(index) { this.groups.splice(index, 1); },
            addOption(gIndex) { this.groups[gIndex].options.push({ name: '', price: 0 }); },
            removeOption(gIndex, oIndex) { this.groups[gIndex].options.splice(oIndex, 1); }
        }
    }
</script>
@endsection
