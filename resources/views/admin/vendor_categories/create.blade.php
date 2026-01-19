@extends('layouts.admin')

@section('title', 'Créer une Catégorie de Boutique')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-10">
        <a href="{{ route('admin.vendor-categories.index') }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-orange-600 font-bold text-sm transition-colors group">
            <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            RETOUR À LA LISTE
        </a>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight mt-4">Nouvelle Catégorie</h1>
    </div>

    <form action="{{ route('admin.vendor-categories.store') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="bg-white p-10 rounded-[3rem] border border-gray-100 shadow-sm space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-400 pl-4">Nom de la Catégorie</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ex: Supermarché"
                           class="w-full px-8 py-5 bg-gray-50 border-0 rounded-[2rem] focus:bg-white focus:ring-4 focus:ring-orange-500/10 outline-none transition-all text-sm font-bold text-gray-900">
                    @error('name') <p class="text-red-500 text-[10px] font-bold mt-1 pl-4">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-400 pl-4">Icône (Emoji)</label>
                    <input type="text" name="icon" value="{{ old('icon', '🏪') }}" placeholder="Ex: 🛒"
                           class="w-full px-8 py-5 bg-gray-50 border-0 rounded-[2rem] focus:bg-white focus:ring-4 focus:ring-orange-500/10 outline-none transition-all text-sm font-bold text-gray-900">
                    @error('icon') <p class="text-red-500 text-[10px] font-bold mt-1 pl-4">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-400 pl-4">Description</label>
                <textarea name="description" rows="4" placeholder="Décrivez brièvement ce type de boutique..."
                          class="w-full px-8 py-5 bg-gray-50 border-0 rounded-[2rem] focus:bg-white focus:ring-4 focus:ring-orange-500/10 outline-none transition-all text-sm font-bold text-gray-900 resize-none">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-[10px] font-bold mt-1 pl-4">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-4 pl-4 pt-4">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-14 h-8 bg-gray-100 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-500"></div>
                    <span class="ml-4 text-[11px] font-black uppercase tracking-widest text-gray-400">Catégorie Active</span>
                </label>
            </div>
        </div>

        <button type="submit" class="w-full py-6 bg-black text-white rounded-[2rem] font-black text-xs uppercase tracking-[0.3em] hover:bg-orange-600 hover:shadow-2xl hover:shadow-orange-200 transition-all transform hover:-translate-y-1 active:scale-95">
            Créer la Catégorie
        </button>
    </form>
</div>
@endsection
