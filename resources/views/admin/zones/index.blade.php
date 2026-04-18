@extends('layouts.admin')

@section('title', 'Gestion Zones Géographiques')

@section('content')
<div x-data="{ showAddModal: false }" @keydown.escape="showAddModal = false" class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight leading-none uppercase">Zones Géographiques</h1>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1.5 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                Sectorisation & Logistique
            </p>
        </div>
        
        <button @click="showAddModal = true" class="px-4 py-2.5 bg-gray-900 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-lg active:scale-95">
            Nouvelle Zone
        </button>
    </div>

    <!-- Zones Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden text-[10px]">
        @if($zones->count())
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="pl-4 pr-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Nom de Zone</th>
                        <th class="px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Localisation</th>
                        <th class="px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Couverture</th>
                        <th class="px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Vendeurs</th>
                        <th class="px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Statut</th>
                        <th class="pr-4 pl-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($zones as $zone)
                        <tr class="group hover:bg-gray-50/50 transition-all duration-200">
                            <td class="pl-4 pr-3 py-2.5">
                                <div class="min-w-0">
                                    <p class="font-black text-gray-900 truncate">{{ $zone->nom }}</p>
                                    <p class="text-[8px] text-gray-400 font-bold uppercase truncate max-w-[200px]">{{ $zone->description ?? 'Pas de description' }}</p>
                                </div>
                            </td>
                            <td class="px-3 py-2.5">
                                <p class="text-[9px] font-black text-gray-700 uppercase tracking-tighter">{{ $zone->ville ?? 'N/A' }}</p>
                                <p class="text-[7px] text-gray-300 font-bold uppercase mt-0.5">{{ $zone->code_postal ?? 'N/A' }}</p>
                            </td>
                            <td class="px-3 py-2.5">
                                <span class="font-black text-gray-900">{{ number_format($zone->rayon_km, 1) }} KM</span>
                                <p class="text-[7px] text-gray-300 font-bold uppercase mt-0.5 italic">Rayon d'action</p>
                            </td>
                            <td class="px-3 py-2.5">
                                <span class="px-1.5 py-0.5 bg-blue-50 text-blue-600 rounded font-black text-[8px] border border-blue-100">
                                    {{ $zone->vendeurs()->count() }} PARTENAIRES
                                </span>
                            </td>
                            <td class="px-3 py-2.5">
                                <span class="font-black tracking-tighter {{ $zone->actif ? 'text-emerald-500' : 'text-gray-300' }} uppercase">
                                    {{ $zone->actif ? 'ACTIF' : 'OFF' }}
                                </span>
                            </td>
                            <td class="pr-4 pl-3 py-2.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.zones.edit', $zone->id_zone) }}" class="p-2 bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.zones.destroy', $zone->id_zone) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 bg-gray-50 text-gray-400 hover:text-red-500 rounded-lg transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="px-4 py-3 bg-gray-50/50 border-t border-gray-100 flex justify-center text-[8px] font-bold text-gray-400 uppercase tracking-widest">
                {{ $zones->links() }}
            </div>
        @else
            <div class="py-12 text-center">
                <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest italic">Aucune zone enregistrée</p>
            </div>
        @endif
    </div>

    <!-- Modal Ajouter Zone (Stylized) -->
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4" x-data="zoneForm()">
        <div @click.away="showAddModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-hidden flex flex-col">
            <div class="px-5 py-4 bg-gray-900 text-white flex items-center justify-between">
                <h2 class="text-xs font-black uppercase tracking-widest">Ajouter une Zone</h2>
                <button @click="showAddModal = false" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.zones.store') }}" method="POST" class="p-6 space-y-4 overflow-y-auto">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Nom de la zone</label>
                    <input type="text" name="nom" required class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500/10 transition-all">
                </div>

                <div class="space-y-1.5">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Description</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500/10 transition-all"></textarea>
                </div>

                <div class="bg-blue-50/50 rounded-xl p-3 border border-blue-100 space-y-3">
                    <div class="flex gap-2">
                        <input type="text" x-model="searchQuery" placeholder="Recherche (ex: Lome, Togo)" class="flex-1 px-3 py-1.5 bg-white border-none rounded-lg text-xs font-bold text-gray-900 focus:ring-2 focus:ring-blue-500/20">
                        <button type="button" @click="searchLocation()" :disabled="!searchQuery || searching" class="px-4 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 text-[10px] font-black uppercase tracking-widest transition-all">
                            <span x-show="!searching">Chercher</span>
                            <span x-show="searching">...</span>
                        </button>
                    </div>

                    <div x-show="searchResults.length > 0" class="space-y-1 max-h-32 overflow-y-auto">
                        <template x-for="result in searchResults" :key="result.lon">
                            <button type="button" @click="selectResult(result)" class="w-full text-left p-2 bg-white border border-gray-100 rounded-lg hover:bg-gray-50 transition text-[8px] font-bold uppercase">
                                <div class="text-gray-900" x-text="result.display_name"></div>
                                <div class="text-gray-400 mt-0.5" x-text="'COORD: ' + result.lat.toFixed(4) + ', ' + result.lon.toFixed(4)"></div>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Ville</label>
                        <input type="text" name="ville" x-model="form.ville" class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500/10 transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Rayon (KM)</label>
                        <input type="number" name="rayon_km" step="0.1" required class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500/10 transition-all">
                    </div>
                </div>

                <input type="hidden" name="latitude" x-model="form.latitude">
                <input type="hidden" name="longitude" x-model="form.longitude">

                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 py-3 bg-gray-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all shadow-lg shadow-gray-900/10">Créer Zone</button>
                    <button type="button" @click="showAddModal = false" class="px-6 py-3 bg-gray-100 text-gray-400 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-200 transition-all">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function zoneForm() {
        return {
            searchQuery: '',
            searchResults: [],
            searchError: '',
            searching: false,
            form: {
                ville: '',
                code_postal: '',
                latitude: '',
                longitude: '',
            },
            async searchLocation() {
                this.searching = true;
                this.searchError = '';
                this.searchResults = [];

                try {
                    const response = await fetch('{{ route("admin.zones.search-coordinates") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ search: this.searchQuery })
                    });

                    const data = await response.json();

                    if (!data.success) {
                        this.searchError = data.message || 'Erreur';
                    } else {
                        this.searchResults = data.results || [];
                    }
                } catch (error) {
                    this.searchError = 'Erreur';
                }

                this.searching = false;
            },
            selectResult(result) {
                this.form.latitude = result.lat;
                this.form.longitude = result.lon;
                const parts = result.display_name.split(',');
                if (parts.length > 0) {
                    this.form.ville = parts[0].trim();
                }
                this.searchResults = [];
                this.searchQuery = '';
            }
        };
    }
</script>
@endsection
