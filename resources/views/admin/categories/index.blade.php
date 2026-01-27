@extends('layouts.admin')

@section('title', 'Gestion des Catégories')

@section('content')
<div class="space-y-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Catégories Globales</h1>
            <p class="text-gray-500 font-medium mt-1 text-sm uppercase tracking-widest">Catégories d'articles et types d'activités</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="px-8 py-4 bg-red-600 text-white rounded-[1.5rem] font-black shadow-xl shadow-red-200 hover:bg-red-700 transition flex items-center gap-3 active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Nouvelle Catégorie
        </a>
    </div>

    @if(session('success'))
        <div class="p-6 bg-green-50 border border-green-100 rounded-[2rem] text-green-600 font-bold flex items-center justify-between animate-fade-in">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-400 hover:text-green-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($categories as $category)
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden group hover:border-red-500 transition-all">
                <!-- Category Image Header -->
                <div class="relative h-40 overflow-hidden">
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->nom_categorie }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-red-50 to-orange-50 flex items-center justify-center">
                            <span class="text-5xl">{{ $category->icone ?: '🍔' }}</span>
                        </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                        <a href="{{ route('admin.categories.edit', $category->id_categorie) }}" class="p-3 bg-white/90 backdrop-blur-sm text-gray-600 rounded-xl hover:text-blue-600 hover:bg-blue-50 transition-all shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        </a>
                        <form action="{{ route('admin.categories.destroy', $category->id_categorie) }}" method="POST" onsubmit="return confirm('Supprimer cette catégorie ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-3 bg-white/90 backdrop-blur-sm text-gray-600 rounded-xl hover:text-red-600 hover:bg-red-50 transition-all shadow-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                    
                    <!-- Image Badge -->
                    @if($category->image)
                    <div class="absolute bottom-4 left-4 flex items-center gap-2">
                        <span class="px-3 py-1 bg-white/90 backdrop-blur-sm rounded-full text-[10px] font-black text-gray-700 shadow-lg flex items-center gap-2">
                            <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Image
                        </span>
                    </div>
                    @else
                    <div class="absolute bottom-4 left-4 flex items-center gap-2">
                        <span class="px-3 py-1 bg-orange-100 rounded-full text-[10px] font-black text-orange-600 shadow-lg flex items-center gap-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Aucune image
                        </span>
                    </div>
                    @endif
                </div>

                <div class="p-8">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-xl font-black text-gray-900 tracking-tight">{{ $category->nom_categorie }}</h3>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ $category->description ?: 'Aucune description' }}</p>
                        </div>
                        @if($category->icone)
                        <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-lg">
                            {{ $category->icone }}
                        </div>
                        @endif
                    </div>

                    <div class="mt-6 flex items-center justify-between border-t border-gray-50 pt-6">
                        <div class="flex items-center gap-2">
                            @if($category->actif)
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                <span class="text-[10px] font-black text-green-600 uppercase tracking-widest">Active</span>
                            @else
                                <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Inactive</span>
                            @endif
                        </div>
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                            Ordre: #{{ $category->ordre_affichage ?: 0 }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-32 bg-white rounded-[3rem] border-2 border-dashed border-gray-100 text-center">
                <div class="w-24 h-24 bg-gray-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8 text-gray-200">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                </div>
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Zéro catégorie trouvée</h3>
                <p class="text-gray-400 font-medium mt-2">Commencez par créer la première catégorie d'articles.</p>
                <a href="{{ route('admin.categories.create') }}" class="mt-8 inline-block px-10 py-4 bg-red-600 text-white rounded-2xl font-black uppercase tracking-widest text-[11px] shadow-xl shadow-red-200 hover:bg-black transition-all">Ajouter une catégorie</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
