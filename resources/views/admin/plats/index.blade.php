@extends('layouts.admin')

@section('title', 'Gestion Catalogue')

@section('content')
<div x-data="{ searchQuery: '{{ request('search') }}', categoryFilter: '{{ request('category') }}', vendorFilter: '{{ request('vendor') }}' }" class="space-y-4">
    
    <!-- Header with Stats -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight leading-none uppercase">Catalogue Articles</h1>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1.5 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                Inventaire & Disponibilité
            </p>
        </div>
        
        <div class="flex items-center gap-2">
            <div class="hidden sm:flex flex-col items-end">
                <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">Total Articles</p>
                <p class="text-sm font-black text-gray-900 leading-none mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="w-px h-6 bg-gray-100 hidden sm:block mx-2"></div>
            @can('create_plat')
                <a href="{{ route('admin.plats.create') }}" class="px-4 py-2.5 bg-gray-900 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-lg active:scale-95">
                    Ajouter Article
                </a>
            @endcan
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm group relative overflow-hidden">
            <p class="text-[8px] font-black text-emerald-500 uppercase tracking-widest mb-1.5">En Ligne</p>
            <p class="text-xl font-black text-gray-900 leading-none">{{ $stats['available'] }}</p>
            <div class="absolute -right-2 -bottom-2 w-10 h-10 bg-emerald-50 rounded-full group-hover:scale-150 transition-transform"></div>
        </div>

        <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm group relative overflow-hidden">
            <p class="text-[8px] font-black text-orange-500 uppercase tracking-widest mb-1.5">Promotions</p>
            <p class="text-xl font-black text-gray-900 leading-none">{{ $stats['promoted'] }}</p>
            <div class="absolute -right-2 -bottom-2 w-10 h-10 bg-orange-50 rounded-full group-hover:scale-150 transition-transform"></div>
        </div>

        <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm group relative overflow-hidden">
            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Hors Ligne</p>
            <p class="text-xl font-black text-gray-900 leading-none">{{ $stats['unavailable'] }}</p>
            <div class="absolute -right-2 -bottom-2 w-10 h-10 bg-gray-50 rounded-full group-hover:scale-150 transition-transform"></div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-1.5 rounded-2xl border border-gray-100 shadow-sm">
        <form action="{{ route('admin.plats.index') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center gap-1">
            <div class="flex-1 px-3 py-1">
                <div class="relative group">
                    <input type="text" name="search" x-model="searchQuery" placeholder="Recherche article ou boutique..." 
                           class="w-full pl-8 pr-4 py-2 bg-transparent border-none focus:ring-0 text-xs font-bold text-gray-900 placeholder:text-gray-300 placeholder:uppercase">
                    <svg class="absolute left-0 top-2 w-4 h-4 text-gray-300 group-focus-within:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div class="h-6 w-px bg-gray-50 hidden lg:block mx-1"></div>

            <div class="flex items-center gap-1.5 px-3">
                <select name="category" x-model="categoryFilter" class="bg-gray-50 border-none rounded-xl text-[10px] font-bold text-gray-600 focus:ring-0">
                    <option value="">Toutes catégories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id_categorie }}">{{ $cat->nom_categorie }}</option>
                    @endforeach
                </select>
                <select name="vendor" x-model="vendorFilter" class="bg-gray-50 border-none rounded-xl text-[10px] font-bold text-gray-600 focus:ring-0">
                    <option value="">Tous les vendeurs</option>
                    @foreach($vendors as $v)
                        <option value="{{ $v->id_vendeur }}">{{ $v->nom_commercial }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-1.5 p-1.5 ml-auto">
                <button type="submit" class="px-5 py-2.5 bg-gray-900 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-black transition-all shadow-lg shadow-gray-900/10">Appliquer</button>
                @if($searchQuery || $categoryFilter || $vendorFilter)
                    <a href="{{ route('admin.plats.index') }}" class="p-2.5 bg-gray-50 text-gray-400 hover:text-red-600 rounded-xl transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden text-[10px]">
        @if($plats->count())
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="pl-4 pr-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Article</th>
                        <th class="px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Vendeur</th>
                        <th class="hidden sm:table-cell px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Détails</th>
                        <th class="px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Prix</th>
                        <th class="px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Statut</th>
                        <th class="pr-4 pl-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($plats as $plat)
                        <tr class="group hover:bg-gray-50/50 transition-all duration-200">
                            <td class="pl-4 pr-3 py-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="relative w-8 h-8 rounded-lg overflow-hidden flex-shrink-0 shadow-inner bg-gray-100">
                                        @if($plat->image_principale)
                                            <img src="{{ asset('storage/' . $plat->image_principale) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-black text-gray-900 truncate">{{ $plat->nom_plat }}</p>
                                        <p class="text-[8px] text-gray-400 font-bold uppercase truncate">{{ $plat->categorie->nom_categorie ?? 'Sans catégorie' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-2.5">
                                <span class="text-[9px] font-black text-gray-700 uppercase tracking-tighter">{{ $plat->vendeur->nom_commercial }}</span>
                            </td>
                            <td class="hidden sm:table-cell px-3 py-2.5">
                                <span class="text-[8px] text-gray-400 font-bold uppercase truncate max-w-[150px] inline-block">{{ $plat->description }}</span>
                            </td>
                            <td class="px-3 py-2.5">
                                <div class="flex flex-col">
                                    @if($plat->en_promotion)
                                        <span class="font-black text-orange-600 leading-none">{{ number_format($plat->prix_promotion, 0, ',', ' ') }} F</span>
                                        <span class="text-[7px] text-gray-300 line-through mt-0.5">{{ number_format($plat->prix, 0, ',', ' ') }} F</span>
                                    @else
                                        <span class="font-black text-gray-900 leading-none">{{ number_format($plat->prix, 0, ',', ' ') }} F</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-2.5">
                                <span class="font-black tracking-tighter {{ $plat->disponible ? 'text-emerald-500' : 'text-gray-300' }} uppercase">
                                    {{ $plat->disponible ? 'ACTIF' : 'OFF' }}
                                </span>
                            </td>
                            <td class="pr-4 pl-3 py-2.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.plats.show', $plat->id_plat) }}" class="p-2 bg-gray-50 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.plats.toggle-availability', $plat->id_plat) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="p-2 {{ $plat->disponible ? 'bg-orange-50 text-orange-400 hover:text-orange-600' : 'bg-emerald-50 text-emerald-400 hover:text-emerald-600' }} rounded-lg transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.plats.destroy', $plat->id_plat) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 bg-gray-50 text-gray-400 hover:text-red-500 rounded-lg transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="px-4 py-3 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between text-[8px] font-bold text-gray-400 uppercase tracking-widest">
                <span>{{ $plats->firstItem() }}-{{ $plats->lastItem() }} sur {{ $plats->total() }}</span>
                <div>{{ $plats->links() }}</div>
            </div>
        @else
            <div class="py-12 text-center">
                <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest italic">Aucun article trouvé</p>
                <a href="{{ route('admin.plats.index') }}" class="inline-block mt-4 text-[8px] font-black text-orange-500 uppercase underline">Réinitialiser</a>
            </div>
        @endif
    </div>
</div>
@endsection