@extends('layouts.admin')

@section('title', 'Gestion des Catégories')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight leading-none uppercase">Catégories Globales</h1>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1.5 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                Classification & Taxonomie
            </p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2.5 bg-gray-900 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-lg active:scale-95">
            Nouvelle Catégorie
        </a>
    </div>

    @if(session('success'))
        <div class="px-4 py-2 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-600 text-[10px] font-black uppercase tracking-widest flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
        @forelse($categories as $category)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden group hover:border-red-500 transition-all flex flex-col">
                <!-- Category Image Header -->
                <div class="relative h-24 overflow-hidden">
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->nom_categorie }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                    @else
                        <div class="w-full h-full bg-gray-50 flex items-center justify-center">
                            <span class="text-lg opacity-40">{{ $category->icone ?: '🍔' }}</span>
                        </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                        <a href="{{ route('admin.categories.edit', $category->id_categorie) }}" class="p-1.5 bg-white/90 backdrop-blur-sm text-gray-600 rounded-lg hover:text-blue-600 transition-all shadow-lg">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        </a>
                        <form action="{{ route('admin.categories.destroy', $category->id_categorie) }}" method="POST" onsubmit="return confirm('Confirmer ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 bg-white/90 backdrop-blur-sm text-gray-600 rounded-lg hover:text-red-600 transition-all shadow-lg">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="p-3 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-[11px] font-black text-gray-900 tracking-tight leading-tight uppercase truncate">{{ $category->nom_categorie }}</h3>
                        <p class="text-[7px] font-black text-gray-400 uppercase tracking-widest mt-1 line-clamp-2 leading-relaxed">{{ $category->description ?: 'Aucune description' }}</p>
                    </div>

                    <div class="mt-3 flex items-center justify-between border-t border-gray-50 pt-2.5">
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full {{ $category->actif ? 'bg-emerald-500' : 'bg-gray-200' }}"></span>
                            <span class="text-[7px] font-black text-gray-400 uppercase tracking-widest">{{ $category->actif ? 'ACTIVE' : 'OFF' }}</span>
                        </div>
                        <div class="text-[7px] font-black text-gray-400 uppercase tracking-widest">
                            #{{ $category->ordre_affichage ?: 0 }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center">
                <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest italic">Aucune catégorie trouvée</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
