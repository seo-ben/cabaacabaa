@extends('layouts.app')

@section('title', 'Recrutement Livreurs Partenaires')

@section('content')
<div class="bg-gray-50 dark:bg-gray-950 min-h-screen">
    <!-- Hero Section -->
    <div class="relative bg-gray-900 overflow-hidden pt-32 pb-48">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-br from-red-600/20 to-orange-600/20 mix-blend-overlay"></div>
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1595246140625-573b715d11dc?auto=format&fit=crop&q=80')] bg-cover bg-center opacity-40"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl md:text-7xl font-display font-black text-white mb-6 tracking-tight">
                Boostez vos revenus,<br><span class="bg-gradient-to-r from-red-500 to-orange-500 bg-clip-text text-transparent italic">roulez avec nous.</span>
            </h1>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed">
                Rejoignez la plus grande flotte de livraison locale. Choisissez vos horaires, soyez votre propre patron et gagnez de l'argent à chaque livraison.
            </p>
        </div>
    </div>

    <!-- Search & Filter Bar (Floating) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 relative z-10">
        <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-2xl p-4 md:p-6 border border-gray-100 dark:border-gray-800">
            <form action="{{ route('delivery.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div class="relative flex items-center">
                    <svg class="absolute left-5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Poste ou boutique..." 
                           class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-red-600 transition-all">
                </div>
                <div class="relative flex items-center">
                    <svg class="absolute left-5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    <select name="zone" class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl text-sm font-bold appearance-none focus:ring-2 focus:ring-red-600 transition-all">
                        <option value="">Toutes les zones</option>
                        @foreach($zones as $z)
                            <option value="{{ $z->id_zone }}" {{ request('zone') == $z->id_zone ? 'selected' : '' }}>{{ $z->nom_zone }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:hidden lg:block"></div>
                <button type="submit" class="w-full py-4 bg-red-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-red-700 transition-all shadow-lg shadow-red-500/25">
                    Rechercher
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div id="open-positions" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="flex flex-col lg:flex-row gap-12">
            
            <!-- Job List Container -->
            <div class="flex-1">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white">Opportunités Ouvertes</h2>
                        <p class="text-sm text-gray-500 font-medium">{{ $requests->total() }} postes disponibles actuellement</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Live Update</span>
                    </div>
                </div>

                @if($requests->isEmpty())
                    <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] p-16 text-center border-2 border-dashed border-gray-100 dark:border-gray-800">
                        <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-3xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Aucun poste pour le moment</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm italic">Essayez de modifier vos filtres de recherche.</p>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach($requests as $request)
                            <div class="group bg-white dark:bg-gray-900 p-6 md:p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 flex flex-col md:flex-row items-center gap-8 hover:shadow-2xl hover:border-red-100 dark:hover:border-red-900/30 transition-all duration-300">
                                <!-- Company Logo/Avatar -->
                                <div class="shrink-0">
                                    @if($request->vendeur->image_principale)
                                        <img src="{{ asset('storage/'.$request->vendeur->image_principale) }}" 
                                             class="w-20 h-20 rounded-2xl object-cover shadow-lg" 
                                             alt="{{ $request->vendeur->nom_commercial }}">
                                    @else
                                        <div class="w-20 h-20 bg-gradient-to-br from-red-50 to-orange-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl flex items-center justify-center shadow-inner">
                                            <span class="text-3xl font-black text-red-600/30">{{ mb_substr($request->vendeur->nom_commercial, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Job Body -->
                                <div class="flex-1 text-center md:text-left">
                                    <div class="flex flex-wrap justify-center md:justify-start gap-2 mb-3">
                                        <span class="px-2.5 py-1 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-[10px] font-black uppercase tracking-widest rounded-lg">FREELANCE</span>
                                        <span class="px-2.5 py-1 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 text-[10px] font-black uppercase tracking-widest rounded-lg">ACTIF</span>
                                    </div>
                                    <h3 class="text-xl font-black text-gray-900 dark:text-white mb-1 group-hover:text-red-600 transition-colors">Livreur Partenaire - {{ $request->vendeur->nom_commercial }}</h3>
                                    <div class="flex flex-wrap justify-center md:justify-start items-center gap-4 text-gray-500 dark:text-gray-400 text-sm font-medium">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                            {{ $request->vendeur->zone->nom_zone ?? 'Toute la ville' }}
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Publié {{ $request->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Action -->
                                <div class="shrink-0 w-full md:w-auto">
                                    @auth
                                        @php $hasApplied = $request->applications()->where('id_user', auth()->id())->exists(); @endphp
                                        @if($hasApplied)
                                            <button disabled class="w-full px-8 py-4 bg-gray-50 dark:bg-gray-800 text-gray-400 rounded-2xl text-[10px] font-black uppercase tracking-widest cursor-not-allowed border border-gray-100">
                                                Candidature déposée
                                            </button>
                                        @else
                                            <form action="{{ route('delivery.apply', $request->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-full px-8 py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 dark:hover:bg-red-600 hover:text-white transition-all shadow-xl active:scale-95">
                                                    Postuler
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="block px-8 py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl text-[10px] font-black uppercase tracking-widest text-center hover:bg-red-600 dark:hover:bg-red-600 hover:text-white transition-all shadow-xl">
                                            Se connecter
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-12">
                        {{ $requests->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:w-96 space-y-8">
                <!-- Why Join Us Card -->
                <div class="bg-gradient-to-br from-red-600 to-orange-600 rounded-[2.5rem] p-8 text-white shadow-2xl relative overflow-hidden group">
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-700"></div>
                    <h3 class="text-xl font-black mb-6 flex items-center gap-3 italic">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Pourquoi nous ?
                    </h3>
                    <ul class="space-y-6">
                        <li class="flex items-start gap-4">
                            <span class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center shrink-0 text-sm font-black">01</span>
                            <div>
                                <p class="font-black text-sm uppercase tracking-wider mb-1">Flexibilité totale</p>
                                <p class="text-xs text-white/80 leading-relaxed font-medium">Démarrez et arrêtez quand vous voulez. Vous décidez de votre emploi du temps.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <span class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center shrink-0 text-sm font-black">02</span>
                            <div>
                                <p class="font-black text-sm uppercase tracking-wider mb-1">Revenus Instantanés</p>
                                <p class="text-xs text-white/80 leading-relaxed font-medium">Suivez vos gains en temps réel et recevez vos paiements chaque semaine.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <span class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center shrink-0 text-sm font-black">03</span>
                            <div>
                                <p class="font-black text-sm uppercase tracking-wider mb-1">Assurance incluse</p>
                                <p class="text-xs text-white/80 leading-relaxed font-medium">Nous protégeons nos partenaires pendant chaque course effectuée.</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- How it works -->
                <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] p-8 border border-gray-100 dark:border-gray-800 shadow-xl">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white mb-6">Comment postuler ?</h3>
                    <div class="space-y-8">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-gray-50 dark:bg-gray-800 rounded-xl flex items-center justify-center text-red-600 font-bold">1</div>
                            <p class="text-sm font-bold text-gray-600 dark:text-gray-400 italic">Créez votre compte profil livreur.</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-gray-50 dark:bg-gray-800 rounded-xl flex items-center justify-center text-red-600 font-bold">2</div>
                            <p class="text-sm font-bold text-gray-600 dark:text-gray-400 italic">Choisissez une offre et postulez en un clic.</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-gray-50 dark:bg-gray-800 rounded-xl flex items-center justify-center text-red-600 font-bold">3</div>
                            <p class="text-sm font-bold text-gray-600 dark:text-gray-400 italic">Attendez la validation du vendeur et commencez.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Earnings Section -->
    <div class="bg-white dark:bg-gray-900 border-y border-gray-100 dark:border-gray-800 py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-8 leading-tight">
                        Vos gains, vos projets,<br><span class="text-red-600">votre succès.</span>
                    </h2>
                    <div class="space-y-8">
                        <div class="flex items-start gap-6">
                            <div class="w-14 h-14 bg-red-50 dark:bg-red-900/20 rounded-2xl flex items-center justify-center shrink-0">
                                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-gray-900 dark:text-white mb-2 italic uppercase tracking-wider">Tarification Transparente</h4>
                                <p class="text-gray-500 dark:text-gray-400 leading-relaxed font-bold">Chaque livraison est payée selon une grille claire basée sur la distance et le temps.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-6">
                            <div class="w-14 h-14 bg-orange-50 dark:bg-orange-900/20 rounded-2xl flex items-center justify-center shrink-0">
                                <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-gray-900 dark:text-white mb-2 italic uppercase tracking-wider">Bonus de Performance</h4>
                                <p class="text-gray-500 dark:text-gray-400 leading-relaxed font-bold">Gagnez des primes exceptionnelles lors des heures de pointe (déjeuner et dîner).</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-6">
                            <div class="w-14 h-14 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center shrink-0">
                                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09a10.116 10.116 0 001.283-3.562L7 12l.145-4.434a10.116 10.116 0 011.283-3.562l.054-.09A11.003 11.003 0 0011.31 1c2.147 0 4.144.614 5.83 1.678m.454 1.34c.328.911.516 1.884.555 2.885h.045c.983 0 1.936.173 2.825.493m-4.383 1.3l.054-.09A10.116 10.116 0 0016 4.434L15.855 12l-.145 4.434a10.116 10.116 0 01-1.283 3.562l-.054.09"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-gray-900 dark:text-white mb-2 italic uppercase tracking-wider">Indépendance Totale</h4>
                                <p class="text-gray-500 dark:text-gray-400 leading-relaxed font-bold">Nous ne vous imposons rien. Vous choisissez vos courses en fonction de vos préférences.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -inset-4 bg-gradient-to-br from-red-600/20 to-orange-600/20 rounded-[3rem] blur-2xl"></div>
                    <div class="relative bg-gray-900 rounded-[3rem] p-12 overflow-hidden shadow-2xl">
                        <div class="absolute top-0 right-0 p-8">
                            <svg class="w-24 h-24 text-white/5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.82v-1.91c-1.41-.31-2.61-1.12-2.61-2.61h1.56c0 .76.62 1.34 1.56 1.34s1.56-.58 1.56-1.34c0-.62-.43-1.04-1.56-1.44-1.78-.63-3.13-1.22-3.13-3 0-1.39 1.11-2.4 2.61-2.73V6.5h2.82v1.85c1.19.28 2.3 1 2.3 2.45h-1.56c0-.94-.78-1.34-1.51-1.34s-1.51.3-1.51 1.04c0 .66.6 1 1.51 1.31 1.78.59 3.13 1.25 3.13 3.13 0 1.52-1.24 2.5-2.65 2.81z"></path></svg>
                        </div>
                        <div class="relative z-10 space-y-6">
                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-red-600 rounded-full text-[10px] font-black uppercase tracking-widest text-white">Estimation Mensuelle</div>
                            <h3 class="text-5xl font-black text-white italic tracking-tighter">150.000+ FCFA</h3>
                            <p class="text-gray-400 text-sm font-bold uppercase tracking-[0.2em]">Basé sur un temps plein (40h/semaine)</p>
                            <div class="pt-8 grid grid-cols-2 gap-8 text-center border-t border-white/10">
                                <div>
                                    <div class="text-2xl font-black text-white">4.5/5</div>
                                    <div class="text-[10px] text-gray-500 font-black uppercase tracking-widest mt-1">Satisfaction Livreur</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-black text-white">2.5k</div>
                                    <div class="text-[10px] text-gray-500 font-black uppercase tracking-widest mt-1">Courses Partenaires</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gray-50 dark:bg-gray-950 py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-black text-gray-900 dark:text-white mb-8 tracking-tight italic">Prêt à prendre la route ?</h2>
            <p class="text-lg text-gray-600 dark:text-gray-400 mb-12 font-medium">Rejoignez-nous aujourd'hui et commencez à gagner demain. Pas de CV requis, juste votre motivation !</p>
            <div class="flex flex-col sm:flex-row justify-center gap-6">
                @auth
                    <a href="#open-positions" class="px-12 py-6 bg-red-600 text-white rounded-[2rem] font-black text-xs uppercase tracking-widest hover:bg-red-700 transition-all shadow-2xl shadow-red-500/25">
                        Voir les offres
                    </a>
                @else
                    <a href="{{ route('register') }}" class="px-12 py-6 bg-red-600 text-white rounded-[2rem] font-black text-xs uppercase tracking-widest hover:bg-red-700 transition-all shadow-2xl shadow-red-500/25">
                        Créer mon compte
                    </a>
                    <a href="{{ route('login') }}" class="px-12 py-6 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-[2rem] font-black text-xs uppercase tracking-widest hover:bg-black dark:hover:bg-gray-100 transition-all border border-gray-800 dark:border-white shadow-xl">
                        Me connecter
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
