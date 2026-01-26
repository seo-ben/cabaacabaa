@extends('layouts.vendor')

@section('title', 'Gestion des Livreurs')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-display font-black text-gray-900 dark:text-white tracking-tight">Gestion des Livreurs</h1>
            <p class="text-gray-500 font-medium">Configurez vos demandes de livreurs et gérez vos partenaires.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Configuration Demande -->
        <div class="lg:col-span-1 space-y-10">
            <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] p-8 shadow-xl border border-gray-100 dark:border-gray-800">
                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-50 dark:bg-orange-900/30 rounded-xl flex items-center justify-center text-orange-600 dark:text-orange-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    </div>
                    Recrutement
                </h3>

                @if($activeRequest)
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 rounded-2xl p-6 mb-8 text-center">
                        <span class="w-3 h-3 bg-green-500 rounded-full inline-block animate-pulse mb-2"></span>
                        <p class="text-sm font-black text-green-700 dark:text-green-400 uppercase tracking-widest">Recrutement Actif</p>
                        <p class="text-[11px] text-green-600 dark:text-green-500 font-bold mt-1">Votre annonce est visible par tous.</p>
                    </div>

                    <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-2xl mb-8">
                        <p class="text-xs text-gray-400 font-black uppercase tracking-widest mb-2">Message actuel</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 italic">"{{ $activeRequest->message }}"</p>
                    </div>

                    <form action="{{ route('vendeur.slug.delivery.request.close', ['vendor_slug' => $vendeur->slug]) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-4 border-2 border-red-100 dark:border-red-900/30 text-red-600 dark:text-red-400 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                            Clôturer l'annonce
                        </button>
                    </form>
                @else
                    <form action="{{ route('vendeur.slug.delivery.request', ['vendor_slug' => $vendeur->slug]) }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Message de recrutement</label>
                            <textarea name="message" rows="4" required
                                class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border border-transparent focus:border-red-500 rounded-2xl text-sm font-medium text-gray-900 dark:text-white transition-all outline-none"
                                placeholder="Ex: Nous cherchons des livreurs dynamiques avec scooter pour le quartier X..."></textarea>
                        </div>
                        <button type="submit" class="w-full py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-black dark:hover:bg-gray-100 transition-all shadow-xl">
                            Publier l'annonce
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Candidatures -->
        <div class="lg:col-span-2 space-y-10">
            <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] p-8 shadow-xl border border-gray-100 dark:border-gray-800">
                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-8 flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    Candidatures reçues
                </h3>

                @if($applications && count($applications) > 0)
                    <div class="space-y-4">
                        @foreach($applications as $app)
                            @if($app->status === 'pending')
                                <div class="flex items-center gap-6 p-6 bg-gray-50 dark:bg-gray-800 rounded-[2rem] border border-gray-100 dark:border-gray-700/50">
                                    <img src="{{ $app->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($app->user->name) }}" 
                                         class="w-14 h-14 rounded-2xl object-cover shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-black text-gray-900 dark:text-white">{{ $app->user->name }}</p>
                                        <p class="text-xs text-gray-400 font-medium">{{ $app->user->email }} • {{ $app->user->telephone }}</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <form action="{{ route('vendeur.slug.delivery.application', ['vendor_slug' => $vendeur->slug, 'id' => $app->id, 'action' => 'reject']) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="p-3 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </form>
                                        <form action="{{ route('vendeur.slug.delivery.application', ['vendor_slug' => $vendeur->slug, 'id' => $app->id, 'action' => 'accept']) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="p-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all shadow-lg shadow-green-200">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-sm text-gray-400 font-bold uppercase tracking-widest">Aucune candidature en attente</p>
                    </div>
                @endif
            </div>

            <!-- Liste des Livreurs Acceptés -->
            <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] p-8 shadow-xl border border-gray-100 dark:border-gray-800">
                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-8 flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-50 dark:bg-green-900/30 rounded-xl flex items-center justify-center text-green-600 dark:text-green-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    Mes Livreurs Partenaires
                </h3>

                @if($acceptedDrivers && count($acceptedDrivers) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($acceptedDrivers as $driver)
                            <div class="flex items-center gap-4 p-5 border border-gray-100 dark:border-gray-800 rounded-2xl">
                                <img src="{{ $driver->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($driver->user->name) }}" class="w-12 h-12 rounded-xl object-cover">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-black text-gray-900 dark:text-white truncate">{{ $driver->user->name }}</p>
                                    <p class="text-[10px] text-green-600 font-black uppercase tracking-widest">Actif</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-sm text-gray-400 font-bold uppercase tracking-widest">Vous n'avez pas encore de livreurs</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
