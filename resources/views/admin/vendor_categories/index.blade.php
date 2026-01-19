@extends('layouts.admin')

@section('title', 'Gestion des Catégories de Vendeurs')

@section('content')
<div class="space-y-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Catégories de Boutiques</h1>
            <p class="text-gray-500 font-medium mt-1 text-sm uppercase tracking-widest">Types de commerces (Supermarchés, Restaurants, etc.)</p>
        </div>
        <a href="{{ route('admin.vendor-categories.create') }}" class="px-8 py-4 bg-orange-600 text-white rounded-[1.5rem] font-black shadow-xl shadow-orange-200 hover:bg-orange-700 transition flex items-center gap-3 active:scale-95">
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

    @if(session('error'))
        <div class="p-6 bg-red-50 border border-red-100 rounded-[2rem] text-red-600 font-bold flex items-center justify-between animate-fade-in">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($categories as $category)
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden group hover:border-orange-500 transition-all">
                <div class="flex items-start justify-between">
                    <div class="w-16 h-16 bg-orange-50 rounded-[1.5rem] flex items-center justify-center text-3xl group-hover:scale-110 transition-transform text-orange-600">
                        @if(\Illuminate\Support\Str::startsWith($category->icon, ['M', 'm']))
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $category->icon }}"/>
                            </svg>
                        @elseif(\Illuminate\Support\Str::startsWith($category->icon, '<svg'))
                            {!! $category->icon !!}
                        @else
                            {{ $category->icon ?: '🏪' }}
                        @endif
                    </div>
                    <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('admin.vendor-categories.edit', $category->id_category_vendeur) }}" class="p-3 bg-gray-50 text-gray-400 rounded-xl hover:text-blue-600 hover:bg-blue-50 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        </a>
                        <form action="{{ route('admin.vendor-categories.destroy', $category->id_category_vendeur) }}" method="POST" onsubmit="return confirm('Supprimer cette catégorie ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-3 bg-gray-50 text-gray-400 rounded-xl hover:text-red-600 hover:bg-red-50 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-xl font-black text-gray-900 tracking-tight">{{ $category->name }}</h3>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ $category->description ?: 'Aucune description' }}</p>
                </div>

                <div class="mt-8 flex items-center justify-between border-t border-gray-50 pt-6">
                    <div class="flex items-center gap-2">
                        @if($category->is_active)
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            <span class="text-[10px] font-black text-green-600 uppercase tracking-widest">Active</span>
                        @else
                            <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Inactive</span>
                        @endif
                    </div>
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        Boutiques: {{ $category->vendeurs()->count() }}
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-32 bg-white rounded-[3rem] border-2 border-dashed border-gray-100 text-center">
                <div class="w-24 h-24 bg-gray-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8 text-gray-200">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Aucune catégorie de boutique</h3>
                <p class="text-gray-400 font-medium mt-2">Commencez par créer des types comme 'Supermarché', 'Cosmétique', etc.</p>
                <a href="{{ route('admin.vendor-categories.create') }}" class="mt-8 inline-block px-10 py-4 bg-orange-600 text-white rounded-2xl font-black uppercase tracking-widest text-[11px] shadow-xl shadow-orange-200 hover:bg-black transition-all">Ajouter une catégorie</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
