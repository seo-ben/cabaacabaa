@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#FDFCFB] dark:bg-gray-950 py-24 transition-colors duration-300">
    <div class="max-w-3xl mx-auto px-6 space-y-12">
        
        <div class="text-center space-y-4">
            <!-- Success Icon -->
            <div class="relative inline-block mb-8">
                <div class="w-32 h-32  dark:bg-green-900/40 rounded-2xl flex items-center justify-center text-green-600 dark:text-green-400 relative z-10 animate-bounce-slow">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="absolute -inset-4 bg-green-50 dark:bg-green-900/20 rounded-2xl animate-pulse"></div>
            </div>

            <h1 class="text-5xl font-black text-gray-900 dark:text-white tracking-tighter">
                @if($commande->statut == 'termine')
                    Commande livrée !
                @elseif($commande->statut == 'annule')
                    Commande annulée.
                @else
                    Suivi de votre commande
                @endif
            </h1>
            <p class="text-lg text-gray-500 dark:text-gray-400 font-medium max-w-md mx-auto">
                {{ $commande->vendeur->nom_commercial }} s'occupe de tout pour la commande 
                <span class="font-black text-gray-900 dark:text-white">#{{ $commande->numero_commande }}</span>.
            </p>
        </div>

        <!-- Tracking Timeline -->
        @if($commande->statut != 'annule')
        <div class="bg-white dark:bg-gray-900 rounded-2xl p-10 border border-gray-100 dark:border-gray-800 shadow-xl dark:shadow-none overflow-x-auto">
            <div class="flex items-center justify-between min-w-[500px] relative">
                <!-- Line background -->
                <div class="absolute top-1/2 left-10 right-10 h-1 bg-gray-100 dark:bg-gray-800 -translate-y-1/2 z-0"></div>
                <!-- Active Line -->
                <div class="absolute top-1/2 left-10 -translate-y-1/2 h-1 bg-orange-500 transition-all duration-1000 z-0"
                    style="width: 
                    @if($commande->statut == 'en_attente') 0% 
                    @elseif($commande->statut == 'en_preparation') 33% 
                    @elseif($commande->statut == 'pret') 66% 
                    @elseif($commande->statut == 'termine') 100% 
                    @endif">
                </div>

                @php
                    $steps = [
                        ['id' => 'en_attente', 'label' => 'Reçue', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                        ['id' => 'en_preparation', 'label' => 'Préparation', 'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m12 0a2 2 0 100-4m0 4a2 2 0 110-4'],
                        ['id' => 'pret', 'label' => 'Prête', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                        ['id' => 'termine', 'label' => 'Livre', 'icon' => 'M5 13l4 4L19 7']
                    ];
                    $currentStatusRank = [
                        'en_attente' => 0,
                        'en_preparation' => 1,
                        'pret' => 2,
                        'termine' => 3
                    ][$commande->statut] ?? 0;
                @endphp

                @foreach($steps as $index => $step)
                    <div class="relative z-10 flex flex-col items-center step-item" data-rank="{{ $index }}">
                        <div class="step-icon w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-500 {{ $index <= $currentStatusRank ? 'bg-orange-500 text-white shadow-lg shadow-orange-200 scale-110' : 'bg-white dark:bg-gray-800 border-2 border-gray-100 dark:border-gray-700 text-gray-300 dark:text-gray-600' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/></svg>
                        </div>
                        <span class="step-label mt-4 text-[10px] font-black uppercase tracking-widest {{ $index <= $currentStatusRank ? 'text-orange-600 dark:text-orange-400' : 'text-gray-400 dark:text-gray-600' }}">{{ $step['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Order Summary Card -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-2xl shadow-gray-200/50 dark:shadow-none overflow-hidden">
            <div class="p-10 border-b border-gray-50 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white dark:bg-gray-700 rounded-2xl flex items-center justify-center text-orange-600 dark:text-orange-400 shadow-sm font-black italic border border-gray-100 dark:border-gray-600">
                        {{ substr($commande->vendeur->nom_commercial, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Prestataire</h2>
                        <p class="text-sm font-black text-gray-900 dark:text-white">{{ $commande->vendeur->nom_commercial }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <h2 class="text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1">Status Actuel</h2>
                    <span id="current-status-badge" class="px-5 py-2.5 
                        @if($commande->statut == 'en_attente') bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border-orange-100 dark:border-orange-900/30
                        @elseif($commande->statut == 'confirmee' || $commande->statut == 'termine') bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border-green-100 dark:border-green-900/30
                        @elseif($commande->statut == 'annule') bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border-red-100 dark:border-red-900/30
                        @else bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border-blue-100 dark:border-blue-900/30 @endif
                        rounded-full text-[10px] font-black uppercase tracking-widest border border-current">
                        {{ ucfirst(str_replace('_', ' ', $commande->statut)) }}
                    </span>
                </div>
            </div>

            <div class="p-10 space-y-8">
                <div class="space-y-6">
                    @foreach($commande->lignes as $ligne)
                        <div class="flex justify-between items-center group">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-gray-50 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-400 dark:text-gray-500 group-hover:bg-orange-50 dark:group-hover:bg-orange-900/20 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors font-black text-xs">
                                    {{ $ligne->quantite }}x
                                </div>
                                <div>
                                    <span class="text-sm font-black text-gray-900 dark:text-white block">{{ $ligne->nom_plat_snapshot }}</span>
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest">{{ number_format($ligne->prix_unitaire, 0) }} FCFA / unité</span>
                                </div>
                            </div>
                            <span class="text-sm font-black text-gray-900 dark:text-white">{{ number_format($ligne->sous_total, 0) }} FCFA</span>
                        </div>
                    @endforeach
                </div>

                <div class="pt-8 border-t border-gray-50 dark:border-gray-800 grid grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div>
                            <h2 class="text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-2">Récupération</h2>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded-lg text-gray-900 dark:text-white">
                                    @if($commande->type_recuperation == 'livraison')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    @endif
                                </div>
                                <span class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-widest">{{ $commande->type_recuperation }}</span>
                            </div>
                        </div>
                        @if($commande->instructions_speciales)
                        <div>
                            <h2 class="text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1">Notes</h2>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium italic">"{{ $commande->instructions_speciales }}"</p>
                        </div>
                        @endif
                    </div>
                    <div class="text-right space-y-4">
                        <div>
                            <h2 class="text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-2">Paiement</h2>
                            <div class="flex flex-col items-end gap-1">
                                <span class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-widest">{{ $commande->mode_paiement_prevu }}</span>
                                @if($commande->paiement_effectue)
                                    <span class="text-[9px] font-black text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-lg uppercase tracking-widest shrink-0">Confirmé</span>
                                @else
                                    <span class="text-[9px] font-black text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/20 px-2 py-1 rounded-lg uppercase tracking-widest shrink-0">@if($commande->mode_paiement_prevu == 'espece') À régler sur place @else En attente @endif</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-8 border-t border-gray-50 dark:border-gray-800">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Date de commande</span>
                        <span class="text-sm font-black text-gray-900 dark:text-white">{{ $commande->date_commande ? $commande->date_commande->format('d M Y à H:i') : now()->format('d M Y à H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Total payé</span>
                        <span class="text-4xl font-black text-orange-600 dark:text-orange-400 tracking-tighter">{{ number_format($commande->montant_total, 0) }} <small class="text-xs uppercase font-black">FCFA</small></span>
                    </div>
                </div>
            </div>

            <!-- Rating Section -->
            <div id="rating-section" class="{{ ($commande->statut == 'termine' && !$commande->avis) ? '' : 'hidden' }} bg-white dark:bg-gray-900 rounded-2xl p-10 border border-orange-100 dark:border-orange-900/30 shadow-xl dark:shadow-none space-y-8 animate-in zoom-in duration-500">
                <div class="text-center space-y-2">
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Alors, c'était comment ?</h3>
                    <p class="text-gray-500 dark:text-gray-400 font-medium whitespace-pre-wrap">Laissez une note pour aider <span class="text-gray-900 dark:text-white font-black">{{ $commande->vendeur->nom_commercial }}</span> à s'améliorer.</p>
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

                    <button type="submit" :disabled="rating === 0" class="w-full py-5 bg-orange-600 text-white rounded-xl font-black uppercase tracking-widest text-xs hover:bg-orange-700 transition-all shadow-lg shadow-orange-600/20 disabled:opacity-50 disabled:cursor-not-allowed">
                        Publier mon avis
                    </button>
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
            <div class="bg-gray-900 dark:bg-gray-800 p-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gray-800 dark:bg-gray-700 rounded-2xl flex items-center justify-center text-white font-black italic relative">
                        {{ substr($commande->vendeur->nom_commercial, 0, 1) }}
                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-gray-900 dark:border-gray-800 rounded-full"></div>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Besoin d'aide ?</p>
                        <p class="text-xs font-black text-white">Contactez l'établissement</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="tel:{{ $commande->vendeur->telephone_commercial }}" class="px-6 py-4 bg-white text-[10px] font-black uppercase tracking-widest text-gray-900 rounded-2xl hover:bg-orange-600 hover:text-white transition-all shadow-sm flex items-center gap-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        Appeler
                    </a>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
            <a href="{{ route('home') }}" class="px-8 py-4 bg-white dark:bg-gray-900 text-gray-900 dark:text-white border-2 border-gray-100 dark:border-gray-800 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:border-orange-500 hover:text-orange-600 dark:hover:text-orange-400 transition-all active:scale-95">Explorer d'autres articles</a>
            
            @if($commande->statut == 'en_attente')
                <a href="{{ route('orders.cancel', $commande->id_commande) }}" 
                   onclick="return confirm('Voulez-vous vraiment annuler cette commande ?')"
                   class="px-8 py-4 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-red-600 hover:text-white transition-all active:scale-95">
                   Annuler la commande
                </a>
            @endif

            <a href="{{ route('orders.reorder', $commande->id_commande) }}" class="px-8 py-4 bg-orange-600 text-white rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-orange-700 transition-all shadow-xl shadow-orange-100 dark:shadow-none active:scale-95">
               Commander à nouveau
            </a>

            @if($commande->statut == 'termine')
                <a href="{{ route('order.receipt', ['code' => $commande->numero_commande]) }}" target="_blank"
                   class="px-8 py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-black dark:hover:bg-gray-200 transition-all active:scale-95 flex items-center gap-2">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Télécharger le reçu
                </a>
            @endif
            
            <a href="{{ route('orders.index') }}" class="px-8 py-4 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                Toutes mes commandes
            </a>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const orderCode = "{{ $commande->numero_commande }}";
    const statusRanks = {
        'en_attente': 0,
        'confirmee': 0,
        'en_preparation': 1,
        'pret': 2,
        'termine': 3
    };

    function updateTrackingUI(status) {
        const rank = statusRanks[status] ?? 0;
        const progress = (rank / 3) * 100;
        
        // Update line
        const activeLine = document.querySelector('.absolute.top-1\\/2.left-10.-translate-y-1\\/2.h-1.bg-orange-500');
        if(activeLine) activeLine.style.width = progress + '%';
        
        // Update steps
        document.querySelectorAll('.step-item').forEach(item => {
            const itemRank = parseInt(item.getAttribute('data-rank'));
            const icon = item.querySelector('.step-icon');
            const label = item.querySelector('.step-label');
            
            if (itemRank <= rank) {
                icon.classList.remove('bg-white', 'dark:bg-gray-800', 'border-2', 'border-gray-100', 'dark:border-gray-700', 'text-gray-300', 'dark:text-gray-600');
                icon.classList.add('bg-orange-500', 'text-white', 'shadow-lg', 'shadow-orange-200', 'scale-110');
                label.classList.remove('text-gray-400', 'dark:text-gray-600');
                label.classList.add('text-orange-600', 'dark:text-orange-400');
            } else {
                icon.classList.add('bg-white', 'dark:bg-gray-800', 'border-2', 'border-gray-100', 'dark:border-gray-700', 'text-gray-300', 'dark:text-gray-600');
                icon.classList.remove('bg-orange-500', 'text-white', 'shadow-lg', 'shadow-orange-200', 'scale-110');
                label.classList.add('text-gray-400', 'dark:text-gray-600');
                label.classList.remove('text-orange-600', 'dark:text-orange-400');
            }
        });

        // Update badge
        const badge = document.getElementById('current-status-badge');
        if(badge) {
            badge.innerText = status.replace('_', ' ').toUpperCase();
            
            // Color update
            badge.className = "px-5 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest border border-current ";
            if(status === 'en_attente') badge.className += "bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border-orange-100 dark:border-orange-900/30";
            else if(status === 'confirmee' || status === 'termine') badge.className += "bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border-green-100 dark:border-green-900/30";
            else if(status === 'annule' || status === 'annulee') badge.className += "bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border-red-100 dark:border-red-900/30";
            else badge.className += "bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border-blue-100 dark:border-blue-900/30";
        }

        // Handle rating form appearance
        if (status === 'termine') {
            const ratingSection = document.getElementById('rating-section');
            const thankYouSection = document.getElementById('thank-you-section');
            if (ratingSection && thankYouSection && thankYouSection.classList.contains('hidden')) {
                ratingSection.classList.remove('hidden');
            }
        }
    }

    // Real-time with Echo
    if (window.Echo) {
        window.Echo.channel('order.' + orderCode)
            .listen('.order.status.changed', (e) => {
                console.log('Real-time status update:', e.statut);
                updateTrackingUI(e.statut);
            });
    }

    // Polling fallback (every 30 seconds instead of 5)
    setInterval(() => {
        fetch(`/api/order-status/${orderCode}`)
            .then(res => res.json())
            .then(data => {
                if (data.statut) {
                    updateTrackingUI(data.statut);
                }
            })
            .catch(err => console.error('Erreur de tracking:', err));
    }, 30000);
</script>

<style>
@keyframes bounce-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
.animate-bounce-slow {
    animation: bounce-slow 3s ease-in-out infinite;
}
</style>
@endsection
