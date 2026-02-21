@extends('layouts.vendor')

@section('title', 'Modifier l\'Article')
@section('page_title', 'Modifier un article')

@section('content')
<div class="max-w-4xl mx-auto" x-data="variantManager()">

    {{-- ── Header / Breadcrumb ── --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ vendor_route('vendeur.slug.plats.index') }}"
           class="flex items-center justify-center w-9 h-9 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 text-gray-500 hover:text-gray-900 dark:hover:text-white rounded-xl transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-base font-black text-gray-900 dark:text-white leading-none">Modifier : {{ $plat->nom_plat }}</h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Mettez à jour les informations</p>
        </div>
    </div>

    <form action="{{ vendor_route('vendeur.slug.plats.update', $plat->id_plat) }}" method="POST" enctype="multipart/form-data" id="edit-plat-form">
        @csrf
        @method('PUT')

        {{-- ───────────────────────────────────────
             Section 1 : IMAGE (Visuel)
        ──────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden mb-4">
            <div class="p-5 border-b border-gray-50 dark:border-gray-800">
                <h2 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                    <span class="w-5 h-5 bg-red-50 dark:bg-red-900/30 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400 text-[10px] font-black">1</span>
                    Photo de l'article
                </h2>
            </div>
            <div class="p-5" x-data="{ photoPreview: '{{ $plat->image_principale ? asset('storage/' . $plat->image_principale) : '' }}' }">
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
                                <span class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-xl text-white text-[10px] font-black uppercase tracking-widest border border-white/20">Changer la photo</span>
                            </div>
                        </div>
                    </template>
                </div>
                @error('image') <p class="text-red-600 text-[10px] mt-2 font-black uppercase tracking-widest text-center">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- ─────────────────────────────────────────
             Section 2 : STATUT & VISIBILITÉ
        ──────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden mb-4">
            <div class="p-5 border-b border-gray-50 dark:border-gray-800">
                <h2 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                    <span class="w-5 h-5 bg-green-50 dark:bg-green-900/30 rounded-lg flex items-center justify-center text-green-600 dark:text-green-400 text-[10px] font-black">2</span>
                    Disponibilité & Statut
                </h2>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-3" x-data="{ isPromo: {{ old('en_promotion', $plat->en_promotion) ? 'true' : 'false' }} }">
                {{-- Disponibilité --}}
                <label class="relative flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800/60 rounded-2xl cursor-pointer hover:bg-green-50 dark:hover:bg-green-900/10 transition-colors select-none group">
                    <div class="relative">
                        <input type="checkbox" name="disponible" value="1" {{ old('disponible', $plat->disponible) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-10 h-6 bg-gray-200 dark:bg-gray-700 peer-checked:bg-green-500 rounded-full transition-colors"></div>
                        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow-sm transition-all peer-checked:translate-x-4"></div>
                    </div>
                    <div>
                        <span class="block text-xs font-black text-gray-900 dark:text-white uppercase">Disponible</span>
                        <span class="block text-[9px] font-bold text-gray-400 uppercase">Visible par les clients</span>
                    </div>
                </label>

                {{-- Promotion --}}
                <label class="relative flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800/60 rounded-2xl cursor-pointer hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors select-none group">
                    <div class="relative">
                        <input type="checkbox" name="en_promotion" value="1" x-model="isPromo" {{ old('en_promotion', $plat->en_promotion) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-10 h-6 bg-gray-200 dark:bg-gray-700 peer-checked:bg-red-500 rounded-full transition-colors"></div>
                        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow-sm transition-all peer-checked:translate-x-4"></div>
                    </div>
                    <div>
                        <span class="block text-xs font-black text-gray-900 dark:text-white uppercase">En Promotion</span>
                        <span class="block text-[9px] font-bold text-gray-400 uppercase">Mettre en avant l'article</span>
                    </div>
                </label>

                {{-- Prix Promo (conditionnel) --}}
                <div x-show="isPromo" x-transition.opacity class="col-span-full mt-2">
                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Prix Promotionnel (FCFA) *</label>
                    <div class="relative">
                        <input type="number" name="prix_promotion" value="{{ old('prix_promotion', $plat->prix_promotion) }}"
                               placeholder="Ex: 4500"
                               class="w-full px-4 py-3.5 bg-gray-50 dark:bg-gray-800 border border-red-100 dark:border-red-900/30 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-bold text-red-600 dark:text-red-400 text-sm">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[9px] font-black text-red-300 dark:text-red-900 uppercase">PROMO</span>
                    </div>
                    @error('prix_promotion') <p class="text-red-600 text-[10px] mt-1.5 font-black uppercase tracking-widest">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────
             Section 3 : INFOS PRINCIPALES
        ──────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden mb-4">
            <div class="p-5 border-b border-gray-50 dark:border-gray-800">
                <h2 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                    <span class="w-5 h-5 bg-orange-50 dark:bg-orange-900/30 rounded-lg flex items-center justify-center text-orange-600 dark:text-orange-400 text-[10px] font-black">3</span>
                    Détails de l'article
                </h2>
            </div>
            <div class="p-5 space-y-5">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Nom de l'article *</label>
                    <input type="text" name="nom_plat" value="{{ old('nom_plat', $plat->nom_plat) }}" required
                           class="w-full px-4 py-3.5 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-bold text-gray-900 dark:text-white text-sm">
                    @error('nom_plat') <p class="text-red-600 text-[10px] mt-1.5 font-black uppercase tracking-widest">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Catégorie *</label>
                        <select name="id_categorie" required
                                class="w-full px-4 py-3.5 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-bold text-gray-900 dark:text-white text-sm">
                            @foreach($categories as $category)
                                <option value="{{ $category->id_categorie }}" {{ old('id_categorie', $plat->id_categorie) == $category->id_categorie ? 'selected' : '' }}>
                                    {{ $category->nom_categorie }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Prix (FCFA) *</label>
                        <div class="relative">
                            <input type="number" name="prix" value="{{ old('prix', $plat->prix) }}" required
                                   class="w-full pl-4 pr-10 py-3.5 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-bold text-gray-900 dark:text-white text-sm">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[9px] font-black text-gray-300 dark:text-gray-600 uppercase">XOF</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full px-4 py-3.5 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-medium text-gray-700 dark:text-gray-300 text-sm leading-relaxed resize-none">{{ old('description', $plat->description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────
             Section 4 : OPTIONS & SUPPLÉMENTS
        ──────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden mb-4">
            <div class="p-5 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between">
                <h2 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                    <span class="w-5 h-5 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-[10px] font-black">4</span>
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
                               placeholder="Nom du groupe..." required
                               class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl text-xs font-bold text-gray-900 dark:text-white outline-none pr-10">

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
                                    <div class="w-9 h-5 bg-gray-200 dark:bg-gray-700 peer-checked:bg-orange-500 rounded-full transition-colors"></div>
                                    <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow-sm transition-all peer-checked:translate-x-4"></div>
                                </div>
                                <span class="text-[10px] font-black uppercase text-gray-500 dark:text-gray-400">Multi-choix</span>
                            </label>
                        </div>

                        {{-- Min/Max (si multiple) --}}
                        <div x-show="group.multiple" class="flex gap-3">
                            <div class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-600">
                                <span class="text-[9px] font-black uppercase text-gray-400">Min</span>
                                <input type="number" :name="`variants[${gIndex}][min_choix]`" x-model="group.min_choix"
                                       class="w-10 text-center text-xs font-bold text-gray-900 dark:text-white bg-transparent outline-none">
                            </div>
                            <div class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-600">
                                <span class="text-[9px] font-black uppercase text-gray-400">Max</span>
                                <input type="number" :name="`variants[${gIndex}][max_choix]`" x-model="group.max_choix"
                                       class="w-10 text-center text-xs font-bold text-gray-900 dark:text-white bg-transparent outline-none">
                            </div>
                        </div>

                        {{-- Options --}}
                        <div class="space-y-2 pl-3 border-l-2 border-gray-200 dark:border-gray-600">
                            <template x-for="(option, oIndex) in group.options" :key="oIndex">
                                <div class="flex items-center gap-2">
                                    <input type="text" :name="`variants[${gIndex}][options][${oIndex}][nom]`" x-model="option.name"
                                           placeholder="Nom..." required
                                           class="flex-1 px-3 py-2.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-600 rounded-xl text-xs font-semibold text-gray-900 dark:text-white outline-none">
                                    <div class="relative w-24">
                                        <input type="number" :name="`variants[${gIndex}][options][${oIndex}][prix]`" x-model="option.price"
                                               placeholder="0" min="0" step="25"
                                               class="w-full pl-2 pr-7 py-2.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-600 rounded-xl text-xs font-bold text-gray-900 dark:text-white outline-none">
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
            </div>
        </div>

        {{-- ─────────────────────────────────────────
             Boutons ACTION fixe en bas sur mobile
        ──────────────────────────────────────────── --}}
        <div class="sticky bottom-20 lg:bottom-0 lg:static mt-4 mb-2">
            <div class="bg-white/95 dark:bg-gray-950/95 backdrop-blur-md rounded-3xl border border-gray-100 dark:border-gray-800 shadow-2xl shadow-black/10 p-4 flex gap-3">
                <a href="{{ vendor_route('vendeur.slug.plats.index') }}"
                   class="flex-shrink-0 px-5 py-3.5 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 rounded-2xl font-black text-xs uppercase tracking-widest transition-all active:scale-95">
                    Annuler
                </a>
                <button type="submit" form="edit-plat-form"
                        class="flex-1 py-3.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-black dark:hover:bg-gray-100 transition-all shadow-xl active:scale-95">
                    ✓ Sauvegarder
                </button>
            </div>
        </div>

    </form>
</div>

<script>
    function variantManager() {
        return {
            groups: @json($plat->variants ?? []),
            init() {
                // Ensure correct structure if loading from JSON
                if (typeof this.groups === 'string') this.groups = JSON.parse(this.groups);
                this.groups = this.groups.map(g => ({
                    name: g.groupe_nom,
                    obligatoire: g.obligatoire == 1,
                    multiple: g.choix_multiple == 1,
                    min_choix: g.min_choix || 0,
                    max_choix: g.max_choix || 1,
                    options: g.options.map(o => ({ name: o.nom, price: o.prix }))
                }));
            },
            addGroup() {
                this.groups.push({ name: '', obligatoire: false, multiple: false, min_choix: 0, max_choix: 1, options: [{ name: '', price: 0 }] });
            },
            removeGroup(index) { this.groups.splice(index, 1); },
            addOption(gIndex) { this.groups[gIndex].options.push({ name: '', price: 0 }); },
            removeOption(gIndex, oIndex) { this.groups[gIndex].options.splice(oIndex, 1); }
        }
    }
</script>
@endsection
