@extends('layouts.admin')

@section('title', 'Éditer Zone')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4" x-data="zoneEditForm()">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('admin.zones.index') }}" class="p-2 bg-white rounded-xl text-gray-400 hover:text-gray-900 transition-all border border-gray-100 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Zone: {{ $zone->nom }}</h1>
            </div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-12">Gestion et configuration de la zone géographique</p>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="px-4 py-2 bg-blue-50 rounded-2xl border border-blue-100/50">
                <div class="text-[8px] font-black text-blue-400 uppercase tracking-widest leading-none mb-1">Vendeurs Actifs</div>
                <div class="text-lg font-black text-blue-600 leading-none">{{ $zone->vendeurs()->count() }}</div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl flex items-start gap-3">
            <div class="p-2 bg-red-100 text-red-600 rounded-xl">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div class="flex-1">
                <p class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-1">Erreurs détectées</p>
                <ul class="text-[11px] font-bold text-red-500/80 leading-relaxed">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.zones.update', $zone->id_zone) }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf
        @method('PUT')

        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/40 border border-gray-100">
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                             Nom de la zone
                        </label>
                        <input type="text" name="nom" value="{{ old('nom', $zone->nom) }}" required
                               class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-900 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all placeholder:text-gray-300">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Description détaillés</label>
                        <textarea name="description" rows="4" 
                                  class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-900 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all placeholder:text-gray-300"
                                  placeholder="Quartiers couverts, spécificités...">{{ old('description', $zone->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-6 pt-4 border-t border-gray-50">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Ville</label>
                            <input type="text" name="ville" x-model="form.ville" required
                                   class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-900 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Code Postal</label>
                            <input type="text" name="code_postal" x-model="form.code_postal"
                                   class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-900 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Geolocation Block -->
            <div class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/40 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Localisation Précise</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Coordonnées GPS et rayon de service</p>
                    </div>
                    <div class="px-3 py-1 bg-blue-600/10 text-blue-600 rounded-full text-[9px] font-black uppercase tracking-widest">Maps Engine</div>
                </div>

                <div class="space-y-6">
                    <!-- Barre de recherche moderne -->
                    <div class="group relative">
                        <div class="flex gap-2 p-1.5 bg-blue-50 rounded-2xl border border-blue-100 group-focus-within:border-blue-300 transition-all shadow-sm">
                            <input type="text" x-model="searchQuery" placeholder="Modifier l'emplacement (ex: Lomé, Togo)" 
                                   class="flex-1 bg-transparent border-none rounded-xl text-xs font-bold text-blue-900 placeholder:text-blue-300 focus:ring-0">
                            <button type="button" @click="searchLocation()" :disabled="!searchQuery || searching" 
                                    class="px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 disabled:opacity-50 text-[10px] font-black uppercase tracking-tighter transition-all shadow-lg shadow-blue-500/20">
                                <span x-show="!searching">Actualiser</span>
                                <span x-show="searching" class="flex items-center gap-2">
                                     Recherche...
                                </span>
                            </button>
                        </div>
                        
                        <!-- Résultats -->
                        <div x-show="searchResults.length > 0" class="absolute z-10 left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden max-h-48 overflow-y-auto">
                            <template x-for="result in searchResults" :key="result.lon">
                                <button type="button" @click="selectResult(result)" class="w-full text-left p-4 hover:bg-gray-50 transition border-b border-gray-50 last:border-0">
                                    <div class="text-[10px] font-black text-gray-900 uppercase leading-tight" x-text="result.display_name"></div>
                                    <div class="text-[8px] font-bold text-gray-400 mt-1 uppercase tracking-widest" x-text="'Coordonnées: ' + result.lat.toFixed(6) + ' / ' + result.lon.toFixed(6)"></div>
                                </button>
                            </template>
                        </div>

                        <div x-show="searchError" class="mt-2 p-2 bg-red-50 text-red-600 text-[8px] font-black uppercase rounded-xl border border-red-100" x-text="searchError"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2 px-4 py-3 bg-gray-50 rounded-2xl">
                            <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Latitude Actuelle</label>
                            <input type="number" name="latitude" step="0.000001" x-model="form.latitude" required
                                   class="w-full bg-transparent border-none p-0 text-sm font-black text-gray-900 focus:ring-0">
                        </div>
                        <div class="space-y-2 px-4 py-3 bg-gray-50 rounded-2xl">
                            <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Longitude Actuelle</label>
                            <input type="number" name="longitude" step="0.000001" x-model="form.longitude" required
                                   class="w-full bg-transparent border-none p-0 text-sm font-black text-gray-900 focus:ring-0">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-6">
            <div class="bg-gray-900 rounded-2xl p-8 shadow-2xl shadow-gray-900/20 text-white">
                <h3 class="text-sm font-black uppercase tracking-widest mb-6 border-b border-white/10 pb-4">Configuration</h3>
                
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-white/40 uppercase tracking-widest">Rayon (Kilomètres)</label>
                        <div class="relative">
                            <input type="number" name="rayon_km" step="1" min="1" max="100" value="{{ old('rayon_km', $zone->rayon_km) }}" required
                                   class="w-full px-5 py-4 bg-white/10 border-none rounded-2xl text-sm font-black text-white focus:bg-white/20 focus:ring-4 focus:ring-white/5 transition-all">
                            <span class="absolute right-5 top-1/2 -translate-y-1/2 text-xs font-black text-white/20 uppercase">KM</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-white/40 uppercase tracking-widest">Statut de la zone</label>
                        <select name="actif" required 
                                class="w-full px-5 py-4 bg-white/10 border-none rounded-2xl text-sm font-black text-white focus:bg-white/20 focus:ring-4 focus:ring-white/5 transition-all appearance-none cursor-pointer">
                            <option value="1" @selected($zone->actif) class="text-gray-900">Actif (Visible)</option>
                            <option value="0" @selected(!$zone->actif) class="text-gray-900">Inactif (Masqué)</option>
                        </select>
                    </div>

                    <div class="pt-6 space-y-3">
                        <button type="submit" class="w-full py-4 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-500 transition-all shadow-xl shadow-blue-500/20">
                            Sauvegarder
                        </button>
                        <a href="{{ route('admin.zones.index') }}" class="block w-full py-4 bg-white/5 text-white/60 text-center rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-white/10 transition-all border border-white/5">
                            Annuler
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xl shadow-gray-200/20 flex items-center justify-between group cursor-help transition-all hover:bg-gray-50">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-orange-50 text-orange-500 rounded-2xl group-hover:bg-orange-100 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Besoin d'aide ?</div>
                        <div class="text-xs font-black text-gray-900 uppercase">Voir la documentation</div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function zoneEditForm() {
        return {
            searchQuery: '',
            searchResults: [],
            searchError: '',
            searching: false,
            form: {
                ville: '{{ $zone->ville ?? "" }}',
                code_postal: '{{ $zone->code_postal ?? "" }}',
                latitude: '{{ $zone->latitude ?? "" }}',
                longitude: '{{ $zone->longitude ?? "" }}',
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
                        this.searchError = data.message || 'Erreur lors de la recherche';
                    } else {
                        this.searchResults = data.results || [];
                        if (this.searchResults.length === 0) {
                            this.searchError = 'Aucun résultat trouvé';
                        }
                    }
                } catch (error) {
                    this.searchError = 'Erreur réseau: ' + error.message;
                }

                this.searching = false;
            },
            selectResult(result) {
                this.form.latitude = result.lat;
                this.form.longitude = result.lon;
                // Essayer d'extraire la ville du display_name
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
