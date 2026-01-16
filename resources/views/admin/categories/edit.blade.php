@extends('layouts.admin')

@section('title', 'Modifier la Catégorie')

@section('content')
<div class="max-w-3xl mx-auto space-y-10">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.categories.index') }}" class="group flex items-center gap-3 text-gray-400 hover:text-red-600 transition-colors">
            <div class="w-10 h-10 bg-white border border-gray-100 rounded-xl flex items-center justify-center group-hover:bg-red-50 group-hover:border-red-100 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </div>
            <span class="text-xs font-black uppercase tracking-widest">Toutes les catégories</span>
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-10 py-8 border-b border-gray-50 bg-gray-50/20 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Modifier: {{ $category->nom_categorie }}</h1>
                <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest mt-1">Mise à jour des paramètres globaux</p>
            </div>
            <div class="w-14 h-14 bg-white border border-gray-100 rounded-2xl flex items-center justify-center text-2xl shadow-sm">
                {{ $category->icone ?: '🍔' }}
            </div>
        </div>

        <form action="{{ route('admin.categories.update', $category->id_categorie) }}" method="POST" class="p-10 space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Nom de la Catégorie</label>
                    <input type="text" name="nom_categorie" value="{{ $category->nom_categorie }}" required
                        class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all">
                </div>
                <div class="space-y-2">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Icône / Emoji</label>
                    <input type="text" name="icone" value="{{ $category->icone }}"
                        class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all">
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Description (Optionnel)</label>
                <textarea name="description" rows="3" class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-[2rem] font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all resize-none">{{ $category->description }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Ordre d'affichage</label>
                    <input type="number" name="ordre_affichage" value="{{ $category->ordre_affichage }}"
                        class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all">
                </div>
                <div class="space-y-2">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Disponibilité</label>
                    <select name="actif" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all appearance-none cursor-pointer">
                        <option value="1" @selected($category->actif)>Disponible (Visible)</option>
                        <option value="0" @selected(!$category->actif)>Désactivé (Masqué)</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-4 pt-6 border-t border-gray-50">
                <button type="submit" class="flex-1 px-10 py-5 bg-red-600 text-white rounded-[2rem] font-black uppercase text-[11px] tracking-widest shadow-xl shadow-red-200 hover:bg-black hover:shadow-none transition-all active:scale-95 flex items-center justify-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Sauvegarder les modifications
                </button>
                <a href="{{ route('admin.categories.index') }}" class="px-10 py-5 bg-gray-100 text-gray-600 rounded-[2rem] font-black uppercase text-[11px] tracking-widest hover:bg-gray-200 transition-all flex items-center justify-center">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
