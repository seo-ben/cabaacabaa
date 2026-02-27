@extends('layouts.app')

@section('content')

{{-- ================= MOBILE VIEW (< lg) ================= --}}
<main class="block lg:hidden bg-gray-50 dark:bg-slate-950 min-h-screen font-sans pb-24">

    {{-- Sticky Header --}}
    <div class="sticky top-0 z-30 bg-white/95 dark:bg-slate-950/95 backdrop-blur-xl border-b border-gray-100 dark:border-slate-800/50 px-4 py-4">
        <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Suivi Commande</h1>
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-0.5">Suivez votre commande en temps réel</p>
    </div>

    {{-- Search Bar --}}
    <section class="px-4 pt-5 pb-4">
        <form action="{{ route('orders.track') }}" method="GET" class="relative flex items-center bg-white dark:bg-slate-900/80 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="flex-1 flex items-center px-4 gap-2">
                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="code" value="{{ $code }}" placeholder="CMD-XXXXXX" required class="w-full py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-gray-900 dark:text-white font-black tracking-wider placeholder-gray-400 text-sm">
            </div>
            <button type="submit" class="m-2 px-5 py-3 bg-orange-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest active:scale-95 transition-transform shadow-lg shadow-orange-500/20">
                Suivre
            </button>
        </form>
    </section>

    @if($commande)

    {{-- Real-time indicator --}}
    <div class="px-4 mb-4">
        <div class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 shadow-sm">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
            </span>
            <span class="text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest">Mise à jour automatique</span>
        </div>
    </div>

    {{-- Tracking Steps Card --}}
    <section class="px-4 mb-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm p-5">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Suivi de progression</p>
            {{-- Vertical Steps (Mobile-friendly) --}}
            @php
                $steps = [
                    ['id' => 'en_attente', 'label' => 'Reçue', 'desc' => 'Commande reçue', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['id' => 'en_preparation', 'label' => 'Cuisine', 'desc' => 'En préparation', 'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4'],
                    ['id' => 'pret', 'label' => 'Prête', 'desc' => 'Prête pour livraison', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                    ['id' => 'en_livraison', 'label' => 'Livraison', 'desc' => 'En route vers vous', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['id' => 'termine', 'label' => 'Livrée', 'desc' => 'Bon appétit !', 'icon' => 'M5 13l4 4L19 7'],
                ];
                $statusRanks = ['en_attente' => 0, 'confirmee' => 0, 'en_preparation' => 1, 'pret' => 2, 'en_livraison' => 3, 'termine' => 4];
                $currentRank = $statusRanks[$commande->statut] ?? 0;
            @endphp

            <div class="space-y-0">
                @foreach($steps as $index => $step)
                @php $done = $index <= $currentRank; @endphp
                <div class="flex items-start gap-3 relative step-item-mobile" data-id="{{ $step['id'] }}" data-rank="{{ $index }}">
                    {{-- Line --}}
                    @if(!$loop->last)
                    <div class="absolute left-[18px] top-9 bottom-0 w-0.5 {{ $done ? 'bg-orange-500' : 'bg-gray-100 dark:bg-slate-800' }} transition-colors duration-700 z-0"></div>
                    @endif
                    {{-- Icon --}}
                    <div class="step-icon-mobile relative z-10 w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-all duration-500 {{ $done ? 'bg-orange-500 text-white shadow-lg shadow-orange-200 dark:shadow-orange-900/30' : 'bg-gray-100 dark:bg-slate-800 text-gray-400 dark:text-gray-600' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/></svg>
                    </div>
                    {{-- Content --}}
                    <div class="pb-5 pt-1">
                        <p class="text-sm font-black {{ $done ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-600' }} step-label-mobile transition-colors duration-500">{{ $step['label'] }}</p>
                        <p class="text-[10px] font-medium text-gray-400">{{ $step['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    
    {{-- Map Section (Mobile) --}}
    @if($commande->id_livreur)
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <section class="px-4 mb-4" id="order-map-mobile-container" style="{{ $commande->statut == 'en_livraison' ? '' : 'display:none' }}">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Position du livreur</span>
                <div class="flex items-center gap-1.5 px-2 py-0.5 bg-green-50 text-green-600 rounded-full text-[8px] font-black uppercase tracking-widest">
                    <span class="w-1 h-1 rounded-full bg-green-500 animate-pulse"></span>
                    En direct
                </div>
            </div>
            <div id="order-map-mobile" class="w-full h-48 z-0"></div>
        </div>
    </section>
    @endif

    {{-- Order Info Card --}}
    <section class="px-4 mb-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 bg-gray-50/50 dark:bg-slate-800/30 border-b border-gray-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center font-black text-[10px] text-orange-600 dark:text-orange-400">
                        {{ substr($commande->vendeur->nom_commercial, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Prestataire</p>
                        <p class="text-xs font-black text-gray-900 dark:text-white">{{ $commande->vendeur->nom_commercial }}</p>
                    </div>
                </div>
                <span id="current-status-badge" class="px-3 py-1.5 text-[8px] font-black uppercase tracking-widest rounded-xl bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border border-orange-100 dark:border-orange-900/30">
                    {{ ucfirst(str_replace('_', ' ', $commande->statut)) }}
                </span>
            </div>
            <div class="flex items-center justify-between px-4 py-4">
                <div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Commande</p>
                    <p class="text-lg font-black text-gray-900 dark:text-white">#{{ $commande->numero_commande }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Montant</p>
                    <p class="text-lg font-black text-orange-600 dark:text-orange-400">{{ number_format($commande->montant_total, 0) }} <small class="text-[9px]">FCFA</small></p>
                </div>
            </div>
            @if($commande->statut == 'termine')
            <div class="px-4 pb-4">
                <a href="{{ route('order.receipt', ['code' => $commande->numero_commande]) }}" target="_blank" class="w-full py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-black uppercase tracking-widest text-[10px] flex items-center justify-center gap-2 active:scale-95 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Voir le reçu
                </a>
            </div>
            @endif
        </div>
    </section>

    {{-- Rating Section (Mobile) --}}
    <div id="rating-section-mobile" class="{{ ($commande->statut == 'termine' && !$commande->avis) ? '' : 'hidden' }} px-4 mb-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-orange-100 dark:border-orange-900/30 shadow-sm p-5 space-y-5">
            <div class="text-center">
                <h3 class="text-lg font-black text-gray-900 dark:text-white">Votre avis ?</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Aidez <span class="font-black text-gray-900 dark:text-white">{{ $commande->vendeur->nom_commercial }}</span> à s'améliorer</p>
            </div>
            <form action="{{ route('reviews.store') }}" method="POST" x-data="{ rating: 0 }" class="space-y-4">
                @csrf
                <input type="hidden" name="id_commande" value="{{ $commande->id_commande }}">
                <input type="hidden" name="note" :value="rating">
                <div class="flex justify-center gap-3">
                    <template x-for="i in 5">
                        <button type="button" @click="rating = i" class="transition-all duration-200" :class="rating >= i ? 'scale-125' : 'hover:scale-110'">
                            <svg class="w-10 h-10 transition-colors duration-200" :class="rating >= i ? 'text-orange-500 fill-current' : 'text-gray-200 dark:text-gray-700'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.783.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        </button>
                    </template>
                </div>
                <textarea name="commentaire" rows="3" class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-800 border-none rounded-xl text-sm font-medium text-gray-900 dark:text-white placeholder-gray-400 outline-none focus:ring-2 focus:ring-orange-500 resize-none" placeholder="Partagez votre expérience..."></textarea>
                <button type="submit" :disabled="rating === 0" class="w-full py-4 bg-orange-600 text-white rounded-xl font-black uppercase tracking-widest text-[11px] active:scale-95 transition-transform disabled:opacity-40">Publier mon avis</button>
            </form>
        </div>
    </div>

    <div id="thank-you-section-mobile" class="{{ $commande->avis ? '' : 'hidden' }} px-4 mb-4">
        <div class="bg-green-50 dark:bg-green-900/20 rounded-2xl border border-green-100 dark:border-green-900/30 p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/40 text-green-600 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <p class="text-sm font-black text-gray-900 dark:text-white">Merci pour votre avis !</p>
                <p class="text-[10px] text-green-700 dark:text-green-400 font-medium">{{ $commande->vendeur->nom_commercial }} vous remercie !</p>
            </div>
        </div>
    </div>

    {{-- Chat Section (Mobile) --}}
    <section class="px-4 mb-4" id="order-chat-section">
        @include('partials.order-chat', ['orderId' => $commande->id_commande])
    </section>

    @elseif($code)
    <div class="flex flex-col items-center justify-center min-h-[50vh] px-8 text-center">
        <div class="w-20 h-20 bg-red-50 dark:bg-red-900/20 text-red-400 rounded-2xl flex items-center justify-center mb-5">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Code invalide</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 italic">Aucune commande pour "{{ $code }}"</p>
    </div>
    @endif

</main>

{{-- ================= DESKTOP VIEW (>= lg) ================= --}}
<div class="hidden lg:block min-h-screen bg-[#FDFCFB] dark:bg-gray-950 py-24 transition-colors duration-300">
    <div class="max-w-5xl mx-auto px-6 space-y-8">
        
        <!-- Search Section -->
        <div class="text-center space-y-8">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 rounded-full text-[10px] font-black uppercase tracking-widest border border-orange-100 dark:border-orange-900/50 mb-4">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Suivi en temps réel
            </div>
            <h1 class="text-5xl font-black text-gray-900 dark:text-white tracking-tighter">Où est votre délice ?</h1>
            <p class="text-gray-500 dark:text-gray-400 font-medium max-w-md mx-auto">Saisissez votre numéro de commande pour suivre chaque étape de sa préparation.</p>
            
            <form action="{{ route('orders.track') }}" method="GET" class="max-w-md mx-auto relative group">
                <input type="text" name="code" value="{{ $code }}" placeholder="Ex: CMD-XXXXXX" required
                       class="w-full px-8 py-6 bg-white dark:bg-gray-900 border-2 border-gray-100 dark:border-gray-800 rounded-2xl text-lg font-black tracking-tight text-gray-900 dark:text-white outline-none focus:border-orange-500 dark:focus:border-orange-500 shadow-xl shadow-gray-200/40 dark:shadow-none transition-all placeholder-gray-400 dark:placeholder-gray-600">
                <button type="submit" class="absolute right-3 top-3 bottom-3 px-6 bg-orange-600 text-white rounded-xl font-black uppercase tracking-widest text-[11px] hover:bg-orange-700 transition-all active:scale-95 shadow-lg shadow-orange-600/20 dark:shadow-none">
                    Suivre
                </button>
            </form>
        </div>

        @if($commande)
        <!-- Mobile Chat Trigger (Desktop still uses this for chat scroll) -->
        <div class="lg:hidden fixed bottom-6 right-6 z-40">
            <button @click="document.getElementById('order-chat-section').scrollIntoView({behavior: 'smooth'})" 
                    class="w-14 h-14 bg-orange-600 text-white rounded-full shadow-2xl flex items-center justify-center animate-bounce">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </button>
        </div>
        <!-- Tracking Display -->
        <div id="tracking-content" class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-in fade-in slide-in-from-bottom-5 duration-700">
            
            <!-- Left Column: Order Status & Info -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Real-time Status Badge -->
                <div class="flex justify-center">
                    <div class="flex items-center gap-3 px-6 py-3 bg-white dark:bg-gray-900 rounded-full border border-gray-100 dark:border-gray-800 shadow-sm">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                        <span class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Mise à jour automatique activée</span>
                    </div>
                </div>

                <!-- Tracking Map (Desktop) -->
                @if($commande->id_livreur)
                <div id="order-map-desktop-container" class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-700" style="{{ $commande->statut == 'en_livraison' ? '' : 'display:none' }}">
                     <div class="p-6 border-b border-gray-50 dark:border-gray-800 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 text-orange-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-gray-900 dark:text-white">Suivi livreur</h3>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Position en temps réel</p>
                            </div>
                        </div>
                    </div>
                    <div id="order-map-desktop" class="w-full h-80 z-0"></div>
                </div>
                @endif

                <!-- Tracking Timeline -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl p-10 border border-gray-100 dark:border-gray-800 shadow-xl dark:shadow-none overflow-x-auto">
                    <div class="flex items-center justify-between min-w-[500px] relative">
                        <div class="absolute top-1/2 left-10 right-10 h-1 bg-gray-100 dark:bg-gray-800 -translate-y-1/2 z-0"></div>
                        <div id="active-line" class="absolute top-1/2 left-10 -translate-y-1/2 h-1 bg-orange-500 transition-all duration-1000 z-0" style="width: 0%"></div>

                        @php
                            $steps = [
                                ['id' => 'en_attente', 'label' => 'Reçue', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                                ['id' => 'en_preparation', 'label' => 'Cuisine', 'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m12 0a2 2 0 100-4m0 4a2 2 0 110-4'],
                                ['id' => 'pret', 'label' => 'Prête', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                                ['id' => 'termine', 'label' => 'Livrée', 'icon' => 'M5 13l4 4L19 7'],
                            ];
                        @endphp

                        @foreach($steps as $index => $step)
                            <div class="relative z-10 flex flex-col items-center step-item" data-id="{{ $step['id'] }}" data-rank="{{ $index }}">
                                <div class="step-icon w-12 h-12 rounded-xl flex items-center justify-center transition-all duration-500 bg-white dark:bg-gray-800 border-2 border-gray-100 dark:border-gray-700 text-gray-300 dark:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/></svg>
                                </div>
                                <span class="step-label mt-4 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-600">{{ $step['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order Info Card -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-2xl shadow-gray-200/50 dark:shadow-none overflow-hidden">
                    <div class="p-8 border-b border-gray-50 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white dark:bg-gray-700 rounded-xl flex items-center justify-center text-orange-600 dark:text-orange-400 shadow-sm font-black italic border border-gray-100 dark:border-gray-600">
                                {{ substr($commande->vendeur->nom_commercial, 0, 1) }}
                            </div>
                            <div>
                                <h2 class="text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Prestataire</h2>
                                <p class="text-sm font-black text-gray-900 dark:text-white">{{ $commande->vendeur->nom_commercial }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <h2 class="text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1">Status Actuel</h2>
                            <span id="current-status-badge" class="px-5 py-2.5 bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border border-orange-100 dark:border-orange-900/30 rounded-full text-[10px] font-black uppercase tracking-widest">
                                {{ ucfirst(str_replace('_', ' ', $commande->statut)) }}
                            </span>
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="flex justify-between items-center">
                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Numéro de commande</p>
                                <p class="text-xl font-black text-gray-900 dark:text-white">#{{ $commande->numero_commande }}</p>
                            </div>
                            <div class="text-right space-y-1">
                                <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Montant Total</p>
                                <p class="text-xl font-black text-orange-600 dark:text-orange-400">{{ number_format($commande->montant_total, 0) }} FCFA</p>
                            </div>
                        </div>
                        @if($commande->statut == 'termine')
                        <div class="mt-8 pt-8 border-t border-gray-50 dark:border-gray-800">
                             <a href="{{ route('order.receipt', ['code' => $commande->numero_commande]) }}" target="_blank"
                               class="w-full py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl font-black uppercase tracking-widest text-[11px] hover:bg-black dark:hover:bg-gray-200 transition-all flex items-center justify-center gap-3 shadow-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Consulter mon reçu de commande
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Rating Section -->
                <div id="rating-section" class="{{ ($commande->statut == 'termine' && !$commande->avis) ? '' : 'hidden' }} bg-white dark:bg-gray-900 rounded-2xl p-10 border border-orange-100 dark:border-orange-900/30 shadow-xl dark:shadow-none space-y-8 animate-in zoom-in duration-500">
                    <div class="text-center space-y-2">
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Alors, c'était comment ?</h3>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">Laissez une note pour aider <span class="text-gray-900 dark:text-white font-black">{{ $commande->vendeur->nom_commercial }}</span> à s'améliorer.</p>
                    </div>
                    <form action="{{ route('reviews.store') }}" method="POST" x-data="{ rating: 0 }" class="space-y-6">
                        @csrf
                        <input type="hidden" name="id_commande" value="{{ $commande->id_commande }}">
                        <input type="hidden" name="note" :value="rating">
                        <div class="flex justify-center gap-4">
                            <template x-for="i in 5">
                                <button type="button" @click="rating = i" class="group transition-all duration-300 transform" :class="rating >= i ? 'scale-125' : 'hover:scale-110'">
                                    <svg class="w-12 h-12 transition-colors duration-300" :class="rating >= i ? 'text-orange-500 fill-current' : 'text-gray-200 dark:text-gray-700'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.783.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                </button>
                            </template>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 ml-4">Votre commentaire (optionnel)</label>
                            <textarea name="commentaire" rows="3" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-orange-500 focus:bg-white dark:focus:bg-gray-700 dark:text-white rounded-xl text-sm font-bold transition-all outline-none resize-none" placeholder="Partagez votre expérience..."></textarea>
                        </div>
                        <button type="submit" :disabled="rating === 0" class="w-full py-5 bg-orange-600 text-white rounded-xl font-black uppercase tracking-widest text-xs hover:bg-orange-700 transition-all shadow-lg shadow-orange-600/20 disabled:opacity-50 disabled:cursor-not-allowed">Publier mon avis</button>
                    </form>
                </div>

                <div id="thank-you-section" class="{{ $commande->avis ? '' : 'hidden' }} bg-green-50 dark:bg-green-900/20 rounded-2xl p-8 border border-green-100 dark:border-green-900/30 flex items-center gap-6 animate-in slide-in-from-top-4 duration-500">
                    <div class="w-14 h-14 bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">Merci pour votre avis !</h3>
                        <p class="text-[11px] text-green-700 dark:text-green-400 font-medium">Votre note a bien été enregistrée. {{ $commande->vendeur->nom_commercial }} vous remercie !</p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Chat -->
            <div class="lg:col-span-1" id="order-chat-section">
                @include('partials.order-chat', ['orderId' => $commande->id_commande])
            </div>
        </div>
        @elseif($code)
        <div class="py-20 text-center animate-in fade-in zoom-in duration-500">
            <div class="w-24 h-24 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Code Invalide</h3>
            <p class="text-gray-500 dark:text-gray-400 font-medium italic">Nous ne trouvons aucune commande correspondant à "{{ $code }}".</p>
        </div>
        @endif
    </div>
</div>

@endsection

@if($commande)
@section('scripts')
<script>
    const orderCode = "{{ $commande->numero_commande }}";
    const statusRanks = {
        'en_attente': 0, 'confirmee': 0, 'en_preparation': 1, 'pret': 2, 'en_livraison': 3, 'termine': 4
    };

    @if($commande->id_livreur)
    // Map Logic
    let map = null;
    let driverMarker = null;
    const assignedDriverId = "{{ $commande->id_livreur }}";
    
    function initMap() {
        const mapContainer = window.innerWidth >= 1024 ? 'order-map-desktop' : 'order-map-mobile';
        if (!document.getElementById(mapContainer)) return;
        
        map = L.map(mapContainer).setView([6.1375, 1.2123], 16);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '© OpenStreetMap, © CartoDB',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        // Add vendor marker
        const vendorLat = {{ $commande->vendeur->latitude ?? '6.1375' }};
        const vendorLng = {{ $commande->vendeur->longitude ?? '1.2123' }};
        L.marker([vendorLat, vendorLng], {
            icon: L.divIcon({
                className: 'vendor-marker',
                html: '<div class="w-8 h-8 bg-red-600 rounded-lg border-2 border-white shadow-lg flex items-center justify-center text-white font-bold">🏢</div>',
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            })
        }).addTo(map).bindPopup('<b>Boutique</b><br>{{ $commande->vendeur->nom_commercial }}');

        // Initial fetch
        fetch('/api/drivers/online')
            .then(res => res.json())
            .then(drivers => {
                const myDriver = drivers.find(d => d.driverId == assignedDriverId);
                if (myDriver) updateDriverMarker(myDriver);
            });
    }

    function updateDriverMarker(data) {
        if (!map) return;
        const latlng = [parseFloat(data.latitude), parseFloat(data.longitude)];
        
        if (driverMarker) {
            driverMarker.setLatLng(latlng);
        } else {
            driverMarker = L.marker(latlng, {
                icon: L.divIcon({
                    className: 'driver-marker',
                    html: '<div class="w-10 h-10 bg-gray-900 rounded-xl border-2 border-white shadow-xl flex items-center justify-center text-xl transform scale-110">🏍️</div>',
                    iconSize: [40, 40],
                    iconAnchor: [20, 20]
                })
            }).addTo(map);
            driverMarker.bindPopup('<b>Votre livreur</b>').openPopup();
        }
        
        // Follow driver if in delivery
        if ("{{ $commande->statut }}" === 'en_livraison') {
            map.panTo(latlng);
        }
    }

    // Load Leaflet JS
    const script = document.createElement('script');
    script.src = "https://unpkg.com/leaflet@1.9.4/dist/leaflet.js";
    script.onload = initMap;
    document.head.appendChild(script);
    @endif

    function updateTrackingUI(status, label = null) {
        const rank = statusRanks[status] ?? 0;
        const progress = (rank / 4) * 100;
        
        // Show/Hide Map
        const mapM = document.getElementById('order-map-mobile-container');
        const mapD = document.getElementById('order-map-desktop-container');
        if (status === 'en_livraison') {
            if (mapM) mapM.style.display = 'block';
            if (mapD) mapD.style.display = 'block';
            if (map) map.invalidateSize();
        }

        // Desktop: Update line
        const activeLine = document.getElementById('active-line');
        if (activeLine) activeLine.style.width = progress + '%';
        
        // Desktop: Update steps
        document.querySelectorAll('.step-item').forEach(item => {
            const itemRank = parseInt(item.getAttribute('data-rank'));
            const icon = item.querySelector('.step-icon');
            const labelEl = item.querySelector('.step-label');
            if (itemRank <= rank) {
                icon.classList.remove('bg-white','dark:bg-gray-800','border-2','border-gray-100','dark:border-gray-700','text-gray-300','dark:text-gray-600');
                icon.classList.add('bg-orange-500','text-white','shadow-lg','shadow-orange-200','scale-110');
                if (labelEl) { labelEl.classList.remove('text-gray-400','dark:text-gray-600'); labelEl.classList.add('text-orange-600','dark:text-orange-400'); }
            } else {
                icon.classList.add('bg-white','dark:bg-gray-800','border-2','border-gray-100','dark:border-gray-700','text-gray-300','dark:text-gray-600');
                icon.classList.remove('bg-orange-500','text-white','shadow-lg','shadow-orange-200','scale-110');
                if (labelEl) { labelEl.classList.add('text-gray-400','dark:text-gray-600'); labelEl.classList.remove('text-orange-600','dark:text-orange-400'); }
            }
        });

        // Mobile: Update vertical steps
        document.querySelectorAll('.step-item-mobile').forEach(item => {
            const itemRank = parseInt(item.getAttribute('data-rank'));
            const icon = item.querySelector('.step-icon-mobile');
            const labelEl = item.querySelector('.step-label-mobile');
            if (itemRank <= rank) {
                icon.classList.remove('bg-gray-100','dark:bg-slate-800','text-gray-400','dark:text-gray-600');
                icon.classList.add('bg-orange-500','text-white','shadow-lg','shadow-orange-200');
                if (labelEl) { labelEl.classList.remove('text-gray-400','dark:text-gray-600'); labelEl.classList.add('text-gray-900','dark:text-white'); }
            } else {
                icon.classList.add('bg-gray-100','dark:bg-slate-800','text-gray-400','dark:text-gray-600');
                icon.classList.remove('bg-orange-500','text-white','shadow-lg','shadow-orange-200');
                if (labelEl) { labelEl.classList.add('text-gray-400','dark:text-gray-600'); labelEl.classList.remove('text-gray-900','dark:text-white'); }
            }
        });

        // Update badges
        ['current-status-badge'].forEach(id => {
            const badge = document.getElementById(id);
            if (badge) {
                const displayLabel = label || status.replace('_', ' ');
                badge.innerText = displayLabel.charAt(0).toUpperCase() + displayLabel.slice(1);
            }
        });

        // Handle rating form
        if (status === 'termine') {
            ['rating-section', 'rating-section-mobile'].forEach(id => {
                const el = document.getElementById(id);
                const tyId = id.replace('rating-section', 'thank-you-section');
                const ty = document.getElementById(tyId);
                if (el && ty && ty.classList.contains('hidden')) el.classList.remove('hidden');
            });
        }

        // Handle canceled
        if (status === 'annule' || status === 'annulee') {
            const trackingContent = document.getElementById('tracking-content');
            if (trackingContent) {
                trackingContent.innerHTML = `<div class="py-20 text-center"><div class="w-24 h-24 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center mx-auto mb-6"><svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></div><h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Commande Annulée</h3></div>`;
            }
        }
    }

    updateTrackingUI("{{ $commande->statut }}");

    if (window.Echo) {
        window.Echo.channel('order.' + orderCode).listen('.order.status.changed', (e) => {
            updateTrackingUI(e.statut, e.label);
        });
        
        @if($commande->id_livreur)
        window.Echo.channel('drivers-map').listen('.location.updated', (data) => {
            if (data.driverId == assignedDriverId) {
                updateDriverMarker(data);
            }
        });
        @endif
    }

    const pollInterval = window.Echo ? 30000 : 10000;
    setInterval(() => {
        fetch(`/commande/check-status/${orderCode}`)
            .then(res => res.json())
            .then(data => { if (data.statut) updateTrackingUI(data.statut, data.statut_label); })
            .catch(err => console.error('Erreur tracking:', err));
    }, pollInterval);
</script>
@endsection
@endif
