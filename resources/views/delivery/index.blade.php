@extends('layouts.app')

@section('title', 'Opportunités pour Livreurs')

@section('content')
<div class="bg-gray-50 dark:bg-gray-950 py-16 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-16">
            <h1 class="text-4xl font-display font-black text-gray-900 dark:text-white mb-4 tracking-tight">
                Devenez livreur partenaire
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Retrouvez toutes les boutiques et vendeurs qui recherchent des livreurs pour leurs commandes.
            </p>
        </div>

        @if($requests->isEmpty())
            <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] p-16 text-center shadow-xl border border-gray-100 dark:border-gray-800">
                <div class="w-24 h-24 bg-gray-50 dark:bg-gray-800 rounded-3xl flex items-center justify-center mx-auto mb-8 text-gray-300">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-4">Aucune opportunité pour le moment</h3>
                <p class="text-gray-500 dark:text-gray-400">Revenez plus tard pour voir les nouvelles demandes des boutiques.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($requests as $request)
                    <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] overflow-hidden shadow-xl border border-gray-100 dark:border-gray-800 group hover:shadow-2xl transition-all duration-300">
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ $request->vendeur->image_principale ?? 'https://via.placeholder.com/400x200' }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                                 alt="{{ $request->vendeur->nom_commercial }}">
                            <div class="absolute inset-0 bg-linear-to-t from-gray-900/60 to-transparent"></div>
                            <div class="absolute bottom-6 left-6">
                                <span class="bg-red-600 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg shadow-lg">
                                    {{ $request->vendeur->type_vendeur ?? 'Boutique' }}
                                </span>
                            </div>
                        </div>

                        <div class="p-8">
                            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ $request->vendeur->nom_commercial }}</h3>
                            <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400 text-sm mb-6 font-medium italic">
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                {{ $request->vendeur->adresse_complete }}
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-4 mb-8">
                                <p class="text-xs text-gray-600 dark:text-gray-400 font-bold uppercase tracking-widest mb-2">Message du vendeur</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300 italic">"{{ $request->message }}"</p>
                            </div>

                            @auth
                                @php
                                    $hasApplied = $request->applications()->where('id_user', auth()->id())->exists();
                                @endphp

                                @if($hasApplied)
                                    <button disabled class="w-full py-4 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-2xl text-xs font-black uppercase tracking-[0.2em] cursor-not-allowed">
                                        Candidature envoyée
                                    </button>
                                @else
                                    <form action="{{ route('delivery.apply', $request->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl text-xs font-black uppercase tracking-[0.2em] hover:bg-black dark:hover:bg-gray-100 transition-all shadow-xl shadow-gray-200 dark:shadow-none">
                                            Postuler maintenant
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="block text-center w-full py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl text-xs font-black uppercase tracking-[0.2em] hover:bg-black dark:hover:bg-gray-100 transition-all shadow-xl">
                                    Se connecter pour postuler
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-16">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
