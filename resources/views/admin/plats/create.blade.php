@extends('layouts.admin')

@section('title', 'Nouvel Article')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.plats.index') }}" class="w-12 h-12 bg-white border border-gray-100 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-600 hover:border-red-100 transition shadow-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight uppercase">Ajouter un Article</h1>
            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-1">Nouveau produit dans le catalogue global</p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.plats.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf
        
        <!-- Left Side: Basic Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Vendor Selection -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Vendeur / Boutique</label>
                        <select name="id_vendeur" required class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-900 focus:ring-2 focus:ring-gray-900/5 transition">
                            <option value="">Sélectionner un vendeur</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id_vendeur }}" {{ old('id_vendeur') == $vendor->id_vendeur ? 'selected' : '' }}>
                                    {{ $vendor->nom_commercial }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_vendeur') <p class="text-[9px] text-red-600 font-black mt-1 uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>

                    <!-- Category Selection -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Catégorie de produit</label>
                        <select name="id_categorie" required class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-900 focus:ring-2 focus:ring-gray-900/5 transition">
                            <option value="">Sélectionner une catégorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id_categorie }}" {{ old('id_categorie') == $category->id_categorie ? 'selected' : '' }}>
                                    {{ $category->nom_categorie }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_categorie') <p class="text-[9px] text-red-600 font-black mt-1 uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nom de l'article</label>
                    <input type="text" name="nom_plat" value="{{ old('nom_plat') }}" required placeholder="Ex: Pizza Margherita"
                           class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-900 focus:ring-2 focus:ring-gray-900/5 transition placeholder:text-gray-300">
                    @error('nom_plat') <p class="text-[9px] text-red-600 font-black mt-1 uppercase tracking-widest">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Description</label>
                    <textarea name="description" rows="5" placeholder="Détails du produit, ingrédients, etc..."
                              class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-900 focus:ring-2 focus:ring-gray-900/5 transition placeholder:text-gray-300">{{ old('description') }}</textarea>
                    @error('description') <p class="text-[9px] text-red-600 font-black mt-1 uppercase tracking-widest">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Right Side: Pricing & Image -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Pricing -->
            <div class="bg-gray-900 p-8 rounded-2xl border border-gray-800 shadow-2xl space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Prix de base (FCFA)</label>
                    <input type="number" name="prix" value="{{ old('prix') }}" required placeholder="0"
                           class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-lg font-black text-white focus:ring-2 focus:ring-white/20 transition placeholder:text-gray-700">
                    @error('prix') <p class="text-[9px] text-red-400 font-black mt-1 uppercase tracking-widest">{{ $message }}</p> @enderror
                </div>
                
                <p class="text-[8px] text-gray-500 font-bold uppercase tracking-widest leading-relaxed">
                    Ce prix correspond au tarif standard affiché pour les clients avant toute promotion ou option.
                </p>

                <button type="submit" class="w-full py-5 bg-white text-gray-900 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-100 transition shadow-xl active:scale-95 flex items-center justify-center gap-2">
                    <span>Enregistrer</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </button>
            </div>

            <!-- Image Upload -->
            <div x-data="{ photoName: null, photoPreview: null }" class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Image principale</label>
                
                <div class="relative group">
                    <input type="file" name="image" class="hidden" x-ref="photo" 
                           @change="
                                photoName = $refs.photo.files[0].name;
                                const reader = new FileReader();
                                reader.onload = (e) => { photoPreview = e.target.result; };
                                reader.readAsDataURL($refs.photo.files[0]);
                           ">
                    
                    <div class="aspect-square bg-gray-50 rounded-2xl border-2 border-dashed border-gray-100 flex flex-col items-center justify-center cursor-pointer hover:bg-gray-100 transition overflow-hidden relative"
                         @click="$refs.photo.click()"
                         x-show="!photoPreview">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mt-4">Cliquer pour uploader</p>
                    </div>

                    <div class="aspect-square rounded-2xl overflow-hidden bg-gray-50 cursor-pointer relative"
                         x-show="photoPreview" @click="$refs.photo.click()">
                        <img :src="photoPreview" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-[10px] font-black uppercase tracking-widest">
                            Changer l'image
                        </div>
                    </div>
                </div>
                @error('image') <p class="text-[9px] text-red-600 font-black mt-1 uppercase tracking-widest">{{ $message }}</p> @enderror
            </div>
        </div>
    </form>
</div>
@endsection
