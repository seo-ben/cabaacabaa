@extends('layouts.vendor')

@section('title', 'Modifier l\'Article')
@section('page_title', 'Modifier un article')

@section('content')
<div class="max-w-4xl mx-auto space-y-10">
    
    <div class="flex items-center justify-between">
        <a href="{{ vendor_route('vendeur.slug.plats.index') }}" class="flex items-center gap-3 px-6 py-3 bg-white border border-gray-100 text-gray-400 hover:text-gray-900 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            RETOUR AU CATALOGUE
        </a>
    </div>

    <form action="{{ vendor_route('vendeur.slug.plats.update', $plat->id_plat) }}" method="POST" enctype="multipart/form-data" class="space-y-10">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <!-- Left: Main Info -->
            <div class="lg:col-span-2 space-y-10">
                <div class="bg-white rounded-[3rem] p-10 border border-gray-100 shadow-sm space-y-8">
                    <h3 class="text-xl font-black text-gray-900 border-b border-gray-50 pb-6">Informations de l'Article</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Dénomination de l'Article</label>
                            <input type="text" name="nom_plat" value="{{ old('nom_plat', $plat->nom_plat) }}" required placeholder="Ex: Produit ou Service"
                                   class="w-full px-8 py-5 bg-gray-50 border border-gray-100 rounded-[1.5rem] focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-bold text-gray-900 placeholder:text-gray-300">
                            @error('nom_plat') <p class="text-red-600 text-[10px] mt-2 font-black uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Catégorie d'Article</label>
                                <select name="id_categorie" required class="w-full px-8 py-5 bg-gray-50 border border-gray-100 rounded-[1.5rem] focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-bold text-gray-900 appearance-none">
                                    <option value="">Sélectionner...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id_categorie }}" {{ old('id_categorie', $plat->id_categorie) == $category->id_categorie ? 'selected' : '' }}>
                                            {{ $category->nom_categorie }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_categorie') <p class="text-red-600 text-[10px] mt-2 font-black uppercase tracking-widest">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Tarification (FCFA)</label>
                                <div class="relative">
                                    <input type="number" name="prix" value="{{ old('prix', $plat->prix) }}" required placeholder="Ex: 5000"
                                           class="w-full px-8 py-5 bg-gray-50 border border-gray-100 rounded-[1.5rem] focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-bold text-gray-900">
                                    <span class="absolute right-8 top-1/2 -translate-y-1/2 text-gray-300 font-black text-xs uppercase tracking-tighter">XOF</span>
                                </div>
                                @error('prix') <p class="text-red-600 text-[10px] mt-2 font-black uppercase tracking-widest">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Description & Détails</label>
                            <textarea name="description" rows="5" placeholder="Décrivez les caractéristiques, les spécifications et les détails de l'article..."
                                      class="w-full px-8 py-5 bg-gray-50 border border-gray-100 rounded-[1.5rem] focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-medium text-gray-700 leading-relaxed">{{ old('description', $plat->description) }}</textarea>
                            @error('description') <p class="text-red-600 text-[10px] mt-2 font-black uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <label class="relative flex items-center gap-4 p-6 bg-gray-50 rounded-2xl cursor-pointer hover:bg-red-50 transition-colors group">
                                <input type="checkbox" name="en_promotion" value="1" {{ old('en_promotion', $plat->en_promotion) ? 'checked' : '' }} class="w-5 h-5 rounded-lg text-red-600 focus:ring-red-500">
                                <div>
                                    <span class="block text-[10px] font-black uppercase tracking-widest text-gray-900">En Promotion</span>
                                    <span class="block text-[9px] font-bold text-gray-400 uppercase">Mettre en avant cet article</span>
                                </div>
                            </label>

                            <label class="relative flex items-center gap-4 p-6 bg-gray-50 rounded-2xl cursor-pointer hover:bg-green-50 transition-colors group">
                                <input type="checkbox" name="disponible" value="1" {{ old('disponible', $plat->disponible) ? 'checked' : '' }} class="w-5 h-5 rounded-lg text-green-600 focus:ring-green-500">
                                <div>
                                    <span class="block text-[10px] font-black uppercase tracking-widest text-gray-900">Disponible</span>
                                    <span class="block text-[9px] font-bold text-gray-400 uppercase">Visible par les clients</span>
                                </div>
                            </label>
                        </div>

                        <div x-data="{ showPromo: {{ old('en_promotion', $plat->en_promotion) ? 'true' : 'false' }} }" x-show="showPromo" class="pt-6">
                             <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Prix Promotionnel (FCFA)</label>
                             <div class="relative">
                                 <input type="number" name="prix_promotion" value="{{ old('prix_promotion', $plat->prix_promotion) }}" placeholder="Prix si promo..."
                                        class="w-full px-8 py-5 bg-gray-50 border border-gray-100 rounded-[1.5rem] focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all font-bold text-gray-900">
                                 <span class="absolute right-8 top-1/2 -translate-y-1/2 text-gray-300 font-black text-xs uppercase tracking-tighter">XOF</span>
                             </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Media & Status -->
            <div class="space-y-10">
                <!-- Image Upload -->
                <div class="bg-white rounded-[3rem] p-10 border border-gray-100 shadow-sm space-y-8">
                    <h3 class="text-xl font-black text-gray-900 border-b border-gray-50 pb-6">Visuel</h3>
                    <div x-data="{ photoName: null, photoPreview: '{{ $plat->image_principale ? asset('storage/' . $plat->image_principale) : '' }}' }" class="space-y-6">
                        <input type="file" name="image" class="hidden" x-ref="photo"
                               @change="
                                    photoName = $event.target.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($event.target.files[0]);
                               ">

                        <div class="relative aspect-square rounded-[2rem] overflow-hidden bg-gray-50 border-2 border-dashed border-gray-200 group hover:border-red-200 transition-all cursor-pointer"
                             @click="$refs.photo.click()">
                            
                            <template x-if="!photoPreview">
                                <div class="absolute inset-0 flex flex-col items-center justify-center p-8 text-center space-y-4">
                                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-gray-300 group-hover:text-red-500 shadow-sm transition-colors">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 00-2 2z"/></svg>
                                    </div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Changer la photo</p>
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
                    </div>
                </div>

                <!-- Submit Card -->
                <div class="bg-gray-900 rounded-[3rem] p-10 shadow-2xl shadow-gray-200 space-y-6">
                    <p class="text-[10px] font-black text-white/40 uppercase tracking-[0.2em]">Finalisation</p>
                    <button type="submit" class="w-full py-5 bg-red-600 text-white rounded-[1.5rem] font-black text-sm uppercase tracking-widest hover:bg-red-700 transition-all shadow-xl shadow-red-600/20 active:scale-95">
                        ENREGISTRER LES MODIFICATIONS
                    </button>
                    <a href="{{ vendor_route('vendeur.slug.plats.index') }}" class="block w-full text-center text-[10px] font-black text-white/60 uppercase tracking-widest hover:text-white transition-colors">
                        Annuler
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
