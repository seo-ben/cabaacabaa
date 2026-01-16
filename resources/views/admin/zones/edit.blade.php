@extends('layouts.admin')

@section('title', 'Éditer Zone Géographique')

@section('content')
<div class="max-w-2xl mx-auto" x-data="zoneEditForm()">
    <div class="mb-6">
        <a href="{{ route('admin.zones.index') }}" class="text-blue-600 hover:text-blue-800">← Retour à la liste</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-3xl font-bold mb-6">Éditer Zone: {{ $zone->nom }}</h1>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.zones.update', $zone->id_zone) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la zone</label>
                <input type="text" name="nom" value="{{ $zone->nom ?? old('nom') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 @error('nom') border-red-500 @enderror">
                @error('nom')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 @error('description') border-red-500 @enderror">{{ $zone->description ?? old('description') }}</textarea>
                @error('description')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                    <input type="text" name="ville" value="{{ $zone->ville ?? old('ville') }}" x-model="form.ville"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 @error('ville') border-red-500 @enderror">
                    @error('ville')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code Postal</label>
                    <input type="text" name="code_postal" value="{{ $zone->code_postal ?? old('code_postal') }}" x-model="form.code_postal"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 @error('code_postal') border-red-500 @enderror">
                    @error('code_postal')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            <!-- Recherche d'adresses -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher une nouvelle localisation</label>
                <div class="flex gap-2">
                    <input type="text" x-model="searchQuery" placeholder="Ex: Lome, Togo" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
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

            <div class="border-t pt-6">
                <h3 class="font-semibold text-lg mb-4">Coordonnées GPS</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                        <input type="number" name="latitude" step="0.0001" value="{{ $zone->latitude ?? old('latitude') }}" x-model="form.latitude"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 @error('latitude') border-red-500 @enderror"
                               placeholder="Auto (par adresse)">
                        @error('latitude')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                        <input type="number" name="longitude" step="0.0001" value="{{ $zone->longitude ?? old('longitude') }}" x-model="form.longitude"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 @error('longitude') border-red-500 @enderror"
                               placeholder="Auto (par adresse)">
                        @error('longitude')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-900">
                        <strong>Localisation actuelle:</strong>
                        <span x-text="form.latitude ? (parseFloat(form.latitude).toFixed(4) + ', ' + parseFloat(form.longitude).toFixed(4)) : '(Non définie)'"></span>
                    </p>
                    <p class="text-xs text-blue-700 mt-2">
                        <strong>ℹ️</strong> Les coordonnées GPS peuvent être récupérées via la recherche d'adresse ou modifiées manuellement avec <a href="https://maps.google.com" target="_blank" class="underline">Google Maps</a>.
                    </p>
                </div>
            </div>

            <div class="border-t pt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Rayon de couverture (km)</label>
                <input type="number" name="rayon_km" step="0.1" min="0.1" max="100" value="{{ $zone->rayon_km ?? old('rayon_km') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 @error('rayon_km') border-red-500 @enderror"
                       placeholder="ex: 5">
                @error('rayon_km')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                <p class="text-xs text-gray-500 mt-2">Rayon de service autour du point central</p>
            </div>

            <div class="border-t pt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select name="actif" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    <option value="1" @selected($zone->actif)>Actif</option>
                    <option value="0" @selected(!$zone->actif)>Inactif</option>
                </select>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                    Sauvegarder
                </button>
                <a href="{{ route('admin.zones.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    Annuler
                </a>
            </div>
        </form>
    </div>

    <!-- Zone Coverage Info -->
    <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-lg mb-4">Informations de couverture</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Vendeurs dans cette zone</p>
                <p class="text-2xl font-bold text-red-600">{{ $zone->vendeurs()->count() }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Rayon de couverture</p>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($zone->rayon_km, 1) }} km</p>
            </div>
        </div>
    </div>
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
