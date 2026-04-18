@extends('layouts.admin')

@section('title', 'Gestion Zones Géographiques')

@section('content')
<div x-data="zonesManager()" @keydown.escape="showAddModal = false; showEditModal = false" class="space-y-4">
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
                                    <button @click="openEditModal({{ json_encode([
                                        'id' => $zone->id_zone,
                                        'nom' => $zone->nom,
                                        'description' => $zone->description,
                                        'ville' => $zone->ville,
                                        'code_postal' => $zone->code_postal,
                                        'latitude' => $zone->latitude,
                                        'longitude' => $zone->longitude,
                                        'rayon_km' => $zone->rayon_km,
                                        'actif' => $zone->actif,
                                    ]) }})" class="p-2 bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
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

    {{-- ==================== MODAL AJOUTER ZONE ==================== --}}
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div @click.away="showAddModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-hidden flex flex-col"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            <div class="px-5 py-4 bg-gray-900 text-white flex items-center justify-between shrink-0">
                <div>
                    <h2 class="text-xs font-black uppercase tracking-widest">Ajouter une Zone</h2>
                    <p class="text-[8px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Nouvelle zone de livraison</p>
                </div>
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
                        <input type="text" x-model="addSearch.query" placeholder="Recherche (ex: Lome, Togo)" class="flex-1 px-3 py-1.5 bg-white border-none rounded-lg text-xs font-bold text-gray-900 focus:ring-2 focus:ring-blue-500/20">
                        <button type="button" @click="searchAddLocation()" :disabled="!addSearch.query || addSearch.searching" class="px-4 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 text-[10px] font-black uppercase tracking-widest transition-all">
                            <span x-show="!addSearch.searching">Chercher</span>
                            <span x-show="addSearch.searching">...</span>
                        </button>
                    </div>

                    <div x-show="addSearch.results.length > 0" class="space-y-1 max-h-32 overflow-y-auto">
                        <template x-for="result in addSearch.results" :key="result.lon">
                            <button type="button" @click="selectAddResult(result)" class="w-full text-left p-2 bg-white border border-gray-100 rounded-lg hover:bg-gray-50 transition text-[8px] font-bold uppercase">
                                <div class="text-gray-900" x-text="result.display_name"></div>
                                <div class="text-gray-400 mt-0.5" x-text="'COORD: ' + result.lat.toFixed(4) + ', ' + result.lon.toFixed(4)"></div>
                            </button>
                        </template>
                    </div>

                    <div x-show="addSearch.error" class="p-2 bg-red-50 text-red-600 text-[8px] font-black uppercase rounded-lg border border-red-100" x-text="addSearch.error"></div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Ville</label>
                        <input type="text" name="ville" x-model="addForm.ville" required class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500/10 transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Code Postal</label>
                        <input type="text" name="code_postal" x-model="addForm.code_postal" class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500/10 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Rayon (KM)</label>
                        <input type="number" name="rayon_km" step="0.1" value="5.0" required class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500/10 transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Statut</label>
                        <select name="actif" required class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500/10 transition-all">
                            <option value="1">Actif</option>
                            <option value="0">Inactif</option>
                        </select>
                    </div>
                </div>

                <input type="hidden" name="latitude" x-model="addForm.latitude">
                <input type="hidden" name="longitude" x-model="addForm.longitude">

                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 py-3 bg-gray-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all shadow-lg shadow-gray-900/10">Créer Zone</button>
                    <button type="button" @click="showAddModal = false" class="px-6 py-3 bg-gray-100 text-gray-400 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-200 transition-all">Annuler</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ==================== MODAL ÉDITER ZONE ==================== --}}
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div @click.away="showEditModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-hidden flex flex-col"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            <div class="px-5 py-4 bg-orange-600 text-white flex items-center justify-between shrink-0">
                <div>
                    <h2 class="text-xs font-black uppercase tracking-widest">Modifier la Zone</h2>
                    <p class="text-[8px] text-orange-200 font-bold uppercase tracking-widest mt-0.5" x-text="'Zone : ' + editForm.nom"></p>
                </div>
                <button @click="showEditModal = false" class="text-orange-200 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="editAction" method="POST" class="p-6 space-y-4 overflow-y-auto">
                @csrf
                @method('PUT')

                <div class="space-y-1.5">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Nom de la zone</label>
                    <input type="text" name="nom" x-model="editForm.nom" required class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500/10 transition-all">
                </div>

                <div class="space-y-1.5">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Description</label>
                    <textarea name="description" rows="2" x-model="editForm.description" class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500/10 transition-all"></textarea>
                </div>

                <div class="bg-orange-50/50 rounded-xl p-3 border border-orange-100 space-y-3">
                    <div class="text-[8px] font-black text-orange-500 uppercase tracking-widest mb-2">📍 Géolocalisation</div>
                    <div class="flex gap-2">
                        <input type="text" x-model="editSearch.query" placeholder="Modifier l'emplacement (ex: Lomé, Togo)" class="flex-1 px-3 py-1.5 bg-white border-none rounded-lg text-xs font-bold text-gray-900 focus:ring-2 focus:ring-orange-500/20">
                        <button type="button" @click="searchEditLocation()" :disabled="!editSearch.query || editSearch.searching" class="px-4 py-1.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 disabled:opacity-50 text-[10px] font-black uppercase tracking-widest transition-all">
                            <span x-show="!editSearch.searching">Chercher</span>
                            <span x-show="editSearch.searching">...</span>
                        </button>
                    </div>

                    <div x-show="editSearch.results.length > 0" class="space-y-1 max-h-32 overflow-y-auto">
                        <template x-for="result in editSearch.results" :key="result.lon">
                            <button type="button" @click="selectEditResult(result)" class="w-full text-left p-2 bg-white border border-gray-100 rounded-lg hover:bg-gray-50 transition text-[8px] font-bold uppercase">
                                <div class="text-gray-900" x-text="result.display_name"></div>
                                <div class="text-gray-400 mt-0.5" x-text="'COORD: ' + result.lat.toFixed(4) + ', ' + result.lon.toFixed(4)"></div>
                            </button>
                        </template>
                    </div>

                    <div x-show="editSearch.error" class="p-2 bg-red-50 text-red-600 text-[8px] font-black uppercase rounded-lg border border-red-100" x-text="editSearch.error"></div>

                    <!-- Coordonnées actuelles -->
                    <div class="grid grid-cols-2 gap-3 pt-2 border-t border-orange-100">
                        <div class="space-y-1">
                            <label class="text-[7px] font-black text-gray-400 uppercase tracking-widest">Latitude</label>
                            <input type="number" name="latitude" step="0.000001" x-model="editForm.latitude" class="w-full px-2 py-1.5 bg-white border border-gray-100 rounded-lg text-[10px] font-bold text-gray-900 focus:ring-2 focus:ring-orange-500/20">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[7px] font-black text-gray-400 uppercase tracking-widest">Longitude</label>
                            <input type="number" name="longitude" step="0.000001" x-model="editForm.longitude" class="w-full px-2 py-1.5 bg-white border border-gray-100 rounded-lg text-[10px] font-bold text-gray-900 focus:ring-2 focus:ring-orange-500/20">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Ville</label>
                        <input type="text" name="ville" x-model="editForm.ville" required class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500/10 transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Code Postal</label>
                        <input type="text" name="code_postal" x-model="editForm.code_postal" class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500/10 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Rayon (KM)</label>
                        <input type="number" name="rayon_km" step="0.1" x-model="editForm.rayon_km" required class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500/10 transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Statut</label>
                        <select name="actif" x-model="editForm.actif" required class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500/10 transition-all">
                            <option value="1">Actif</option>
                            <option value="0">Inactif</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 py-3 bg-orange-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-700 transition-all shadow-lg shadow-orange-600/20">Mettre à jour</button>
                    <button type="button" @click="showEditModal = false" class="px-6 py-3 bg-gray-100 text-gray-400 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-200 transition-all">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function zonesManager() {
        return {
            showAddModal: false,
            showEditModal: false,

            // ===== ADD FORM =====
            addForm: { ville: '', code_postal: '', latitude: '', longitude: '' },
            addSearch: { query: '', results: [], error: '', searching: false },

            async searchAddLocation() {
                this.addSearch.searching = true;
                this.addSearch.error = '';
                this.addSearch.results = [];
                try {
                    const response = await fetch('{{ route("admin.zones.search-coordinates") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ search: this.addSearch.query })
                    });
                    const data = await response.json();
                    if (!data.success) { this.addSearch.error = data.message || 'Erreur'; }
                    else { this.addSearch.results = data.results || []; }
                } catch (error) { this.addSearch.error = 'Erreur réseau'; }
                this.addSearch.searching = false;
            },

            selectAddResult(result) {
                this.addForm.latitude = result.lat;
                this.addForm.longitude = result.lon;
                const parts = result.display_name.split(',');
                if (parts.length > 0) this.addForm.ville = parts[0].trim();
                this.addSearch.results = [];
                this.addSearch.query = '';
            },

            // ===== EDIT FORM =====
            editForm: { id: '', nom: '', description: '', ville: '', code_postal: '', latitude: '', longitude: '', rayon_km: '', actif: '1' },
            editSearch: { query: '', results: [], error: '', searching: false },
            editAction: '',

            openEditModal(zone) {
                this.editForm.id = zone.id;
                this.editForm.nom = zone.nom || '';
                this.editForm.description = zone.description || '';
                this.editForm.ville = zone.ville || '';
                this.editForm.code_postal = zone.code_postal || '';
                this.editForm.latitude = zone.latitude || '';
                this.editForm.longitude = zone.longitude || '';
                this.editForm.rayon_km = zone.rayon_km || '';
                this.editForm.actif = zone.actif ? '1' : '0';
                this.editAction = '{{ url("admin/zones") }}/' + zone.id;
                this.editSearch = { query: '', results: [], error: '', searching: false };
                this.showEditModal = true;
            },

            async searchEditLocation() {
                this.editSearch.searching = true;
                this.editSearch.error = '';
                this.editSearch.results = [];
                try {
                    const response = await fetch('{{ route("admin.zones.search-coordinates") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ search: this.editSearch.query })
                    });
                    const data = await response.json();
                    if (!data.success) { this.editSearch.error = data.message || 'Erreur'; }
                    else { this.editSearch.results = data.results || []; }
                } catch (error) { this.editSearch.error = 'Erreur réseau'; }
                this.editSearch.searching = false;
            },

            selectEditResult(result) {
                this.editForm.latitude = result.lat;
                this.editForm.longitude = result.lon;
                const parts = result.display_name.split(',');
                if (parts.length > 0) this.editForm.ville = parts[0].trim();
                this.editSearch.results = [];
                this.editSearch.query = '';
            }
        };
    }
</script>
@endsection
