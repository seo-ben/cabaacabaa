@extends('layouts.admin')

@section('title', 'Gestion Catalogue')

@section('content')
<div x-data="{ searchQuery: '{{ request('search') }}', categoryFilter: '{{ request('category') }}', vendorFilter: '{{ request('vendor') }}' }">
    
    <!-- Header with Stats -->
    <div class="mb-8 space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Catalogue d'Articles</h1>
                <p class="text-gray-600">Gérez l'ensemble des articles publiés par vos vendeurs</p>
            </div>
            <div class="flex items-center gap-3 px-5 py-3 bg-gradient-to-r from-red-500 to-orange-500 text-white rounded-xl shadow-lg">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                    <span class="text-2xl font-bold">{{ $stats['total'] }}</span>
                </div>
                <div class="text-sm font-semibold">
                    <div class="opacity-90">Total</div>
                    <div>Articles</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-2xl border border-green-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-green-600 bg-green-100 px-2.5 py-1 rounded-full">Actif</span>
                </div>
                <p class="text-sm font-medium text-green-700 mb-1">En Stock / Disponible</p>
                <h3 class="text-3xl font-bold text-green-600">{{ $stats['available'] }}</h3>
            </div>

            <div class="bg-gradient-to-br from-gray-50 to-slate-50 p-6 rounded-2xl border border-gray-200 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-gray-400 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-gray-600 bg-gray-100 px-2.5 py-1 rounded-full">Inactif</span>
                </div>
                <p class="text-sm font-medium text-gray-600 mb-1">Hors Ligne</p>
                <h3 class="text-3xl font-bold text-gray-500">{{ $stats['unavailable'] }}</h3>
            </div>

            <div class="bg-gradient-to-br from-orange-50 to-amber-50 p-6 rounded-2xl border border-orange-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-orange-600 bg-orange-100 px-2.5 py-1 rounded-full">Promo</span>
                </div>
                <p class="text-sm font-medium text-orange-700 mb-1">En Promotion</p>
                <h3 class="text-3xl font-bold text-orange-600">{{ $stats['promoted'] }}</h3>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-2xl border border-blue-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-blue-600 bg-blue-100 px-2.5 py-1 rounded-full">Stats</span>
                </div>
                <p class="text-sm font-medium text-blue-700 mb-1">Moyenne / Vendeur</p>
                <h3 class="text-3xl font-bold text-blue-600">{{ $vendors->count() > 0 ? round($stats['total'] / $vendors->count(), 1) : 0 }}</h3>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6">
        <form action="{{ route('admin.plats.index') }}" method="GET" class="space-y-4">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-1 relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" x-model="searchQuery" placeholder="Rechercher un article, description ou établissement..." 
                        class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg font-medium text-gray-900 placeholder:text-gray-400 focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none transition-all">
                </div>
                <div class="w-full md:w-56">
                    <select name="category" x-model="categoryFilter" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg font-medium text-gray-900 focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none transition-all cursor-pointer">
                        <option value="">Toutes catégories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id_categorie }}">{{ $cat->nom_categorie }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-56">
                    <select name="vendor" x-model="vendorFilter" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg font-medium text-gray-900 focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none transition-all cursor-pointer">
                        <option value="">Tous les vendeurs</option>
                        @foreach($vendors as $v)
                            <option value="{{ $v->id_vendeur }}">{{ $v->nom_commercial }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-8 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 focus:ring-4 focus:ring-red-100 transition-all shadow-sm">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @if($plats->count())
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Article</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Vendeur</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Catégorie</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Prix</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($plats as $plat)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="relative w-14 h-14 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                            @if($plat->image_principale)
                                                <img src="{{ asset('storage/' . $plat->image_principale) }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                            @endif
                                            @if($plat->en_promotion)
                                                <div class="absolute top-0 right-0 bg-orange-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-bl-md">PROMO</div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-gray-900 truncate">{{ $plat->nom_plat }}</div>
                                            <div class="text-xs text-gray-500 truncate max-w-[250px]">{{ $plat->description }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-gray-700">{{ $plat->vendeur->nom_commercial }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-medium">
                                        {{ $plat->categorie->nom_categorie ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-0.5">
                                        @if($plat->en_promotion)
                                            <span class="text-sm font-bold text-orange-600">{{ number_format($plat->prix_promotion, 0, ',', ' ') }} {{ $currency }}</span>
                                            <span class="text-xs text-gray-400 line-through">{{ number_format($plat->prix, 0, ',', ' ') }} {{ $currency }}</span>
                                        @else
                                            <span class="text-sm font-semibold text-gray-900">{{ number_format($plat->prix, 0, ',', ' ') }} {{ $currency }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($plat->disponible)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 text-green-700 rounded-md text-xs font-medium border border-green-200">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                            Disponible
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 text-gray-600 rounded-md text-xs font-medium border border-gray-200">
                                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                            Hors ligne
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.plats.show', $plat->id_plat) }}" class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="Voir les détails">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>

                                        <form action="{{ route('admin.plats.toggle-availability', $plat->id_plat) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="p-2 {{ $plat->disponible ? 'bg-orange-50 text-orange-600 hover:bg-orange-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }} rounded-lg transition-colors" title="{{ $plat->disponible ? 'Désactiver' : 'Activer' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if($plat->disponible)
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 1.228 0 2.391.272 3.436.754M9 12a3 3 0 113 3 3 3 0 01-3-3zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    @endif
                                                </svg>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.plats.destroy', $plat->id_plat) }}" method="POST" onsubmit="return confirm('Supprimer cet article définitivement du catalogue ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="Supprimer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between bg-gray-50">
                <p class="text-sm text-gray-600">Affichage de <span class="font-medium">{{ $plats->firstItem() }}</span> à <span class="font-medium">{{ $plats->lastItem() }}</span> sur <span class="font-medium">{{ $plats->total() }}</span> résultats</p>
                <div>
                    {{ $plats->links() }}
                </div>
            </div>
        @else
            <div class="py-20 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Aucun produit trouvé</h3>
                <p class="text-gray-500 mb-6">Essayez de modifier vos filtres ou lancez une nouvelle recherche</p>
                <a href="{{ route('admin.plats.index') }}" class="inline-block px-6 py-2.5 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors shadow-sm">Réinitialiser les filtres</a>
            </div>
        @endif
    </div>
</div>
@endsection