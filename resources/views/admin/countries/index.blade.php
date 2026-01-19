@extends('layouts.admin')

@section('title', 'Configuration des Pays')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Pays & Indicatifs</h1>
            <p class="text-sm text-gray-500 font-medium">Activez ou désactivez les pays disponibles sur la plateforme</p>
        </div>
    </div>

    <!-- Instructions Card -->
    <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-6 flex items-start gap-4">
        <div class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center text-white shrink-0 shadow-lg shadow-indigo-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <h3 class="font-bold text-indigo-900">Mise à jour rapide</h3>
            <p class="text-sm text-indigo-700 leading-relaxed">Cochez simplement les pays que vous souhaitez autoriser lors de l'inscription et pour les numéros de téléphone commerciaux. Cliquez sur "Enregistrer les modifications" pour valider.</p>
        </div>
    </div>

    <!-- Country Selection Form -->
    <form action="{{ route('admin.countries.update-selection') }}" method="POST">
        @csrf
        <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-0 divide-x divide-y divide-gray-50">
                @foreach($allCountries as $country)
                    <label class="group relative flex items-center gap-4 p-6 cursor-pointer hover:bg-gray-50 transition-colors">
                        <div class="relative flex items-center">
                            <input type="checkbox" name="countries[]" value="{{ $country['phone_prefix'] }}" 
                                   {{ in_array($country['phone_prefix'], $activeCountries) ? 'checked' : '' }}
                                   class="peer h-6 w-6 rounded-lg border-2 border-gray-200 text-red-600 focus:ring-red-500/20 transition-all cursor-pointer appearance-none checked:bg-red-600 checked:border-red-600">
                            <svg class="absolute w-4 h-4 text-white opacity-0 peer-checked:opacity-100 top-1 left-1 pointer-events-none transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <span class="text-2xl" title="{{ $country['name'] }}">{{ $country['flag_icon'] }}</span>
                            <div>
                                <p class="text-sm font-black text-gray-900 leading-none group-hover:text-red-600 transition-colors">{{ $country['name'] }}</p>
                                <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-wider">{{ $country['phone_prefix'] }}</p>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>

            <!-- Footer Action -->
            <div class="p-8 bg-gray-50 border-t border-gray-100 flex justify-end">
                <button type="submit" 
                        class="px-10 py-4 bg-gradient-to-r from-red-600 to-orange-600 text-white font-black rounded-2xl shadow-xl shadow-red-200 hover:shadow-2xl hover:shadow-red-300 transition-all hover:-translate-y-1 active:scale-95 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
