@extends('layouts.admin')

@section('title', 'Gestion Zones Géographiques')

@section('content')
<div x-data="{ showAddModal: false }" @keydown.escape="showAddModal = false">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Gestion des Zones Géographiques</h1>
        <button @click="showAddModal = true" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
            + Ajouter une zone
        </button>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg border border-green-300 flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.style.display='none'" class="text-green-600 hover:text-green-800">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg border border-red-300 flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.style.display='none'" class="text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
    @endif

    <!-- Zones Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($zones->count())
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Zone</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Localisation</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Rayon (km)</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Vendeurs</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Statut</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($zones as $zone)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $zone->nom }}</div>
                                <div class="text-sm text-gray-500">{{ $zone->description ? Str::limit($zone->description, 50) : 'Pas de description' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="text-gray-900">{{ $zone->ville ?? 'Non spécifiée' }}</div>
                                <div class="text-xs text-gray-500">{{ $zone->code_postal ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                {{ number_format($zone->rayon_km, 1) }} km
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded font-semibold">
                                    {{ $zone->vendeurs()->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($zone->actif)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">Actif</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-semibold">Inactif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <a href="{{ route('admin.zones.edit', $zone->id_zone) }}" class="text-blue-600 hover:text-blue-800">Éditer</a>
                                <form action="{{ route('admin.zones.destroy', $zone->id_zone) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $zones->links() }}
            </div>
        @else
            <div class="p-8 text-center">
                <p class="text-gray-500">Aucune zone créée.</p>
            </div>
        @endif
    </div>

    <!-- Modal Ajouter Zone -->
    <div @click.self="showAddModal = false" x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" style="display: none;" x-data="zoneForm()">
        <!-- Modal -->
        <div class="relative bg-white rounded-lg shadow-lg w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-white">
                <h2 class="text-xl font-bold">Ajouter une Zone</h2>
                <button @click="showAddModal = false" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.zones.store') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la zone</label>
                    <input type="text" name="nom" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                </div>

                <!-- Recherche d'adresses -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher une localisation</label>
                    <div class="flex gap-2">
                        <input type="text" x-model="searchQuery" placeholder="Lome, Togo" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <button type="button" @click="searchLocation()" :disabled="!searchQuery || searching" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 text-sm font-medium">
                            <span x-show="!searching">Chercher</span>
                            <span x-show="searching">...</span>
                        </button>
                    </div>

                    <!-- Résultats de recherche -->
                    <div x-show="searchResults.length > 0" class="mt-3 space-y-2 max-h-48 overflow-y-auto">
                        <template x-for="result in searchResults" :key="result.lon">
                            <button type="button" @click="selectResult(result)" class="w-full text-left p-2 bg-white border border-gray-200 rounded hover:bg-gray-50 transition text-xs">
                                <div class="font-medium text-gray-900" x-text="result.display_name"></div>
                                <div class="text-gray-500" x-text="'Lat: ' + result.lat.toFixed(4) + ', Lon: ' + result.lon.toFixed(4)"></div>
                            </button>
                        </template>
                    </div>

                    <div x-show="searchError" class="mt-2 text-xs text-red-600" x-text="searchError"></div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                        <input type="text" name="ville" x-model="form.ville" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code Postal</label>
                        <input type="text" name="code_postal" x-model="form.code_postal" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                        <input type="number" name="latitude" x-model="form.latitude" step="0.0001" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Auto (par adresse)">
                        <p class="text-xs text-gray-500 mt-1">Optionnel - récupéré automatiquement</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                        <input type="number" name="longitude" x-model="form.longitude" step="0.0001" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Auto (par adresse)">
                        <p class="text-xs text-gray-500 mt-1">Optionnel - récupéré automatiquement</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rayon de couverture (km)</label>
                    <input type="number" name="rayon_km" step="0.1" min="0.1" max="100" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="ex: 5">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select name="actif" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="1">Actif</option>
                        <option value="0">Inactif</option>
                    </select>
                </div>

                <div class="flex gap-3 pt-4 sticky bottom-0 bg-white border-t">
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                        Créer
                    </button>
                    <button type="button" @click="showAddModal = false" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                        Annuler
                    </button>
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
@endsection
