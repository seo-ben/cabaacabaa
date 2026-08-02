@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-950 py-12 lg:py-24 transition-colors duration-300">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs / Back -->
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-500 hover:text-red-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                Retour au panier
            </a>
            <div class="lg:hidden text-[10px] font-black uppercase tracking-widest text-gray-400">Étape 2 sur 2</div>
        </div>

        <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
            @csrf

            @if(session('error'))
            <div class="mb-8 p-4 bg-red-50 border-2 border-red-200 rounded-2xl flex items-center gap-3 text-red-600 font-bold text-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-8 p-4 bg-red-50 border-2 border-red-200 rounded-2xl space-y-2">
                <div class="flex items-center gap-3 text-red-600 font-black text-xs uppercase tracking-widest">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Certaines informations sont incorrectes
                </div>
                <ul class="list-disc list-inside text-[10px] text-red-500 font-bold ml-8">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <!-- Hidden Fields for Location -->
            <input type="hidden" name="lat" id="user-lat" value="{{ old('lat') }}">
            <input type="hidden" name="lng" id="user-lng" value="{{ old('lng') }}">
            <input type="hidden" name="delivery_fee" id="delivery-fee-val" value="{{ old('delivery_fee', 0) }}">
            <input type="hidden" name="mode_paiement" value="espece">

            <!-- Progress Tracker -->
            <div class="mb-12 max-w-2xl mx-auto">
                <div class="flex items-center justify-between relative">
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 h-0.5 bg-gray-200 dark:bg-gray-800 w-full z-0"></div>
                    <div id="progress-bar" class="absolute left-0 top-1/2 -translate-y-1/2 h-0.5 bg-red-600 w-0 z-0 transition-all duration-500"></div>
                    
                    <div class="relative z-10 flex flex-col items-center gap-2">
                        <div id="step-1-indicator" class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center font-black text-sm transition-colors duration-300">1</div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-gray-900 dark:text-white">Coordonnées</span>
                    </div>
                    <div class="relative z-10 flex flex-col items-center gap-2">
                        <div id="step-2-indicator" class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-800 text-gray-400 flex items-center justify-center font-black text-sm transition-colors duration-300">2</div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Paiement</span>
                    </div>
                </div>
            </div>

            <div class="max-w-4xl mx-auto">
                
                <!-- Step 1: Contact & Recovery -->
                <div id="step-1-content" class="space-y-8 transition-all duration-500">
                    <div class="space-y-2 mb-8">
                        <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Finaliser ma commande</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium italic">Étape 1 : Vos informations et mode de récupération</p>
                    </div>

                    <!-- Section 01: Identity -->
                    <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 sm:p-10 border border-gray-100 dark:border-gray-800 shadow-sm space-y-8">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center font-black text-sm">01</div>
                            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-gray-900 dark:text-white">Informations de contact</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 ml-2">Nom Complet</label>
                                <input type="text" name="nom_complet" id="nom_complet" value="{{ old('nom_complet', auth()->check() ? auth()->user()->nom_complet : '') }}" required
                                       class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-red-500 focus:bg-white dark:focus:bg-gray-700 rounded-2xl text-sm font-bold text-gray-900 dark:text-white transition-all outline-none placeholder-gray-400 dark:placeholder-gray-600 @error('nom_complet') border-red-500 @enderror"
                                       placeholder="Ex: Koffi Ablo">
                                @error('nom_complet') <p class="text-[10px] text-red-500 font-bold ml-2">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 ml-2">Adresse E-mail</label>
                                <input type="email" name="email" id="email" value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}" required
                                       class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-red-500 focus:bg-white dark:focus:bg-gray-700 rounded-2xl text-sm font-bold text-gray-900 dark:text-white transition-all outline-none placeholder-gray-400 dark:placeholder-gray-600 @error('email') border-red-500 @enderror"
                                       placeholder="jean@exemple.com">
                                @error('email') <p class="text-[10px] text-red-500 font-bold ml-2">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 ml-2">Numéro de téléphone</label>
                                <input type="tel" name="phone" id="phone" value="{{ old('phone', auth()->check() ? auth()->user()->telephone : '') }}" required
                                       class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-red-500 focus:bg-white dark:focus:bg-gray-700 rounded-2xl text-sm font-bold text-gray-900 dark:text-white transition-all outline-none placeholder-gray-400 dark:placeholder-gray-600 @error('phone') border-red-500 @enderror"
                                       placeholder="+228 00 00 00 00">
                                @error('phone') <p class="text-[10px] text-red-500 font-bold ml-2">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 02: Recovery Mode -->
                    <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 sm:p-10 border border-gray-100 dark:border-gray-800 shadow-sm space-y-8">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center font-black text-sm">02</div>
                            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-gray-900 dark:text-white">Mode de récupération</h2>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-3">
                            @php
                                $modes = [
                                    ['val' => 'emporter', 'label' => 'Emporter', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'desc' => 'Je récupère'],
                                    ['val' => 'sur_place', 'label' => 'Sur Place', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'desc' => 'Je mange ici'],
                                    ['val' => 'livraison', 'label' => 'Livraison', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'desc' => 'On me livre']
                                ];
                            @endphp
                            @foreach($modes as $mode)
                            <label class="relative cursor-pointer group">
                                 <input type="radio" name="type_recuperation" value="{{ $mode['val'] }}" class="peer sr-only" 
                                        {{ old('type_recuperation', 'emporter') == $mode['val'] ? 'checked' : '' }} 
                                        onchange="toggleRecoveryMode(this.value)">
                                 <div class="h-full py-4 bg-gray-50 dark:bg-gray-800/50 border-2 border-transparent rounded-2xl transition-all duration-300 peer-checked:border-red-500 peer-checked:bg-white dark:peer-checked:bg-gray-800 peer-checked:shadow-xl group-active:scale-95 flex flex-col items-center justify-center text-center gap-2">
                                     <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-gray-200 dark:bg-gray-700 text-gray-500 peer-checked:bg-red-500 peer-checked:text-white transition-colors">
                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $mode['icon'] }}"/></svg>
                                     </div>
                                     <div class="px-1">
                                         <span class="block text-[8px] sm:text-[10px] font-black uppercase tracking-widest text-gray-900 dark:text-white">{{ $mode['label'] }}</span>
                                         <span class="hidden sm:block text-[8px] text-gray-400 font-bold uppercase tracking-tighter">{{ $mode['desc'] }}</span>
                                     </div>
                                 </div>
                             </label>
                            @endforeach
                        </div>

                        <!-- Time Slot Selection -->
                        <div class="space-y-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Créneau horaire</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @php
                                    $slots = [
                                        ['val' => 'ASAP', 'label' => 'Dès que possible'],
                                        ['val' => '+30min', 'label' => '+30 minutes'],
                                        ['val' => '+1h', 'label' => '+1 heure'],
                                        ['val' => '+2h', 'label' => '+2 heures'],
                                    ];
                                @endphp
                                @foreach($slots as $slot)
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="heure_recuperation_souhaitee" value="{{ $slot['val'] }}" class="peer sr-only" 
                                           {{ old('heure_recuperation_souhaitee', 'ASAP') == $slot['val'] ? 'checked' : '' }}>
                                    <div class="py-3 px-2 bg-gray-50 dark:bg-gray-800/50 border-2 border-transparent rounded-xl transition-all peer-checked:border-red-500 peer-checked:bg-white dark:peer-checked:bg-gray-800 text-center">
                                        <span class="block text-[9px] font-black uppercase tracking-widest text-gray-900 dark:text-white">{{ $slot['label'] }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Delivery Specifics -->
                        <div id="delivery-details" class="pt-6 space-y-6 {{ old('type_recuperation') == 'livraison' ? '' : 'hidden' }}">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Position de livraison</h3>
                                    <span class="text-[9px] font-bold text-red-500 italic">* Requis pour la livraison</span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <button type="button" id="gps-btn" onclick="detectLocation()" 
                                            class="w-full py-4 bg-red-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-red-500/20 active:scale-95 transition-all flex items-center justify-center gap-3">
                                        <svg id="gps-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <svg id="gps-spinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span id="gps-text">Ma position GPS</span>
                                    </button>
                                     <div class="relative rounded-2xl overflow-hidden border-2 border-slate-100 dark:border-slate-800 h-64 sm:h-72 group shadow-inner">
                                         <div id="delivery-map" class="w-full h-full z-0 bg-slate-100 dark:bg-slate-900"></div>
                                         <div id="map-overlay-info" class="absolute inset-x-0 bottom-0 p-3 bg-black/60 backdrop-blur-md text-center opacity-0 group-hover:opacity-100 transition-all pointer-events-none z-10">
                                             <span id="map-action-text" class="text-[10px] font-black text-white uppercase tracking-widest">Glissez le marqueur pour ajuster</span>
                                         </div>
                                         <div id="map-instructions" class="absolute inset-0 flex items-center justify-center bg-black/20 backdrop-blur-[2px] pointer-events-none z-10 transition-opacity">
                                             <div class="px-4 py-2 bg-white/90 dark:bg-slate-900/90 rounded-full shadow-xl">
                                                 <p id="map-prompt" class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-widest">Indiquez votre position sur la carte</p>
                                             </div>
                                         </div>
                                     </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 ml-2">Adresse détaillée</label>
                                        <input type="text" name="adresse_livraison" id="adresse-livraison" value="{{ old('adresse_livraison') }}"
                                               class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-red-500 focus:bg-white dark:focus:bg-gray-700 rounded-2xl text-sm font-bold text-gray-900 dark:text-white transition-all outline-none @error('adresse_livraison') border-red-500 @enderror"
                                               placeholder="Quartier, N° Maison, Repère visuel...">
                                        @error('adresse_livraison') <p class="text-[10px] text-red-500 font-bold ml-2">{{ $message }}</p> @enderror
                                    </div>

                                    <div id="distance-info" class="p-4 bg-red-50 dark:bg-red-900/10 rounded-2xl border border-red-100 dark:border-red-900/20 hidden">
                                        <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest text-red-600 dark:text-red-400">
                                            <span>Distance : <span id="distance-val">0</span> km</span>
                                            <span class="px-2 py-0.5 bg-red-600 text-white rounded-md">+ <span id="fee-val">0</span> FCFA</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Next Button -->
                    <div class="flex justify-end pt-4">
                        <button type="button" id="next-btn" onclick="nextStep()" disabled
                                class="w-full sm:w-auto px-12 py-6 bg-gray-400 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] transition-all flex items-center justify-center gap-3 cursor-not-allowed">
                            Suivant
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Summary & Payment -->
                <div id="step-2-content" class="hidden space-y-8 transition-all duration-500">
                    <div class="space-y-2 mb-8">
                        <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Vérification & Paiement</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium italic">Étape 2 : Vérifiez votre commande et finalisez</p>
                    </div>

                    <!-- Section 03: Special Notes -->
                    <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 sm:p-10 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                        <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 ml-2">Instructions Spéciales (Optionnel)</label>
                        <textarea name="notes" rows="2" 
                                  class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-red-500 focus:bg-white dark:focus:bg-gray-700 rounded-2xl text-sm font-bold text-gray-900 dark:text-white transition-all outline-none resize-none"
                                  placeholder="Ex: Sans piment, code entrée 1234...">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Section 04: Mon Panier -->
                    <div class="bg-gray-900 dark:bg-gray-900 rounded-2xl p-6 sm:p-10 text-white shadow-2xl border border-white/5 space-y-8 overflow-hidden relative">
                        <!-- Subtle Glow -->
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-red-500/20 blur-3xl rounded-full"></div>
                        
                        <h2 class="text-xl font-black uppercase tracking-widest border-b border-white/10 pb-6 mb-2">Mon Panier</h2>
                        
                        <!-- Items List -->
                        <div class="space-y-4 max-h-[400px] overflow-y-auto no-scrollbar">
                            @foreach($cart as $item)
                            <div class="flex items-center gap-6 py-4 border-b border-white/5 last:border-0">
                                <div class="w-16 h-16 rounded-xl bg-gray-800 overflow-hidden shrink-0 border border-white/10">
                                    <img src="{{ $item['image'] ? asset('storage/'.$item['image']) : asset('assets/default-plat.png') }}" class="w-full h-full object-cover" alt="">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-black uppercase tracking-tight truncate">{{ $item['name'] }}</p>
                                    <p class="text-[10px] text-gray-400 font-bold">Quantité : {{ $item['quantity'] }} • {{ number_format($item['price'], 0, ',', ' ') }} FCFA / unité</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-black">{{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }} FCFA</p>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Totals -->
                        <div class="pt-6 space-y-4 border-t border-white/10">
                            <div class="flex justify-between text-white/50 text-[10px] font-black uppercase tracking-widest">
                                <span>Sous-total</span>
                                <span class="text-white">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div id="delivery-fee-line-step2" class="hidden justify-between text-white/50 text-[10px] font-black uppercase tracking-widest">
                                <span>Livraison</span>
                                <span class="text-white"><span id="summary-fee-step2">0</span> FCFA</span>
                            </div>
                            <div class="flex justify-between items-end pt-4">
                                <p class="text-[10px] font-black uppercase tracking-widest text-red-500">Total à payer</p>
                                <p class="text-4xl font-black tracking-tighter text-white">
                                    <span id="total-val-step2">{{ number_format($total, 0, ',', ' ') }}</span>
                                    <span class="text-[10px] text-gray-400 ml-1">FCFA</span>
                                </p>
                            </div>
                        </div>

                        <!-- Payment Info -->
                        <div class="space-y-4">
                            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-white/30">Paiement</p>
                            <div class="flex items-center gap-3 px-4 py-4 bg-white/5 border border-white/10 rounded-2xl">
                                <div class="w-8 h-8 rounded-lg bg-green-500/20 text-green-400 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[10px] font-black uppercase tracking-widest">Espèces</p>
                                    <p class="text-[8px] text-white/40 font-bold uppercase tracking-tighter">À la livraison / Sur place</p>
                                </div>
                            </div>
                        </div>

                        <!-- Final Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                            <button type="button" onclick="prevStep()" class="flex-1 py-6 bg-white/10 hover:bg-white/20 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] transition-all flex items-center justify-center gap-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                                Retour
                            </button>
                            <button type="submit" class="flex-[2] py-6 bg-red-600 hover:bg-red-700 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] transition-all shadow-xl shadow-red-600/20 active:scale-95 flex items-center justify-center gap-3">
                                Confirmer & Payer
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('head')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .leaflet-control-zoom { border: none !important; border-radius: 12px !important; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; }
    .leaflet-control-zoom-in, .leaflet-control-zoom-out { background-color: white !important; color: #111827 !important; font-weight: bold !important; }
</style>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let subTotal = {{ $total }};
    let currentFee = {{ old('delivery_fee', 0) }};
    let deliveryMap = null;
    let deliveryMarker = null;
    let mapInitialized = false;

    // Check if we already have old input or errors to stay on Step 2
    const hasErrors = @json($errors->any());
    const hasOldStep2 = @json(old('notes') || old('mode_paiement'));
    
    setTimeout(() => {
        toggleRecoveryMode(initialMode);
        validateStep1();
        
        if (hasErrors || hasOldStep2) {
            // Try to jump to Step 2 if Step 1 is valid
            const nom = document.getElementById('nom_complet').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            if (nom && email && phone) {
                nextStep();
            }
        }
    }, 100);

    function toggleRecoveryMode(mode) {
        const showDelivery = (mode === 'livraison');
        const details = document.getElementById('delivery-details');
        const summaryLine = document.getElementById('delivery-fee-line-step2');
        const gpsBtn = document.getElementById('gps-btn');
        const mapPrompt = document.getElementById('map-prompt');
        const mapActionText = document.getElementById('map-action-text');

        if (showDelivery) {
            details.classList.remove('hidden');
            summaryLine.classList.remove('hidden');
            if(gpsBtn) gpsBtn.classList.remove('hidden');
            if(mapPrompt) mapPrompt.innerText = "Indiquez votre position sur la carte";
            if(mapActionText) mapActionText.innerText = "Glissez le marqueur pour ajuster";
        } else {
            details.classList.add('hidden');
            summaryLine.classList.add('hidden');
            if(gpsBtn) gpsBtn.classList.add('hidden');
            currentFee = 0;
            document.getElementById('distance-info').classList.add('hidden');
            updateTotal();
        }

        // Re-init or update map
        requestAnimationFrame(() => {
            setTimeout(() => {
                if (!mapInitialized) {
                    initDeliveryMap();
                } else {
                    deliveryMap.invalidateSize();
                    refreshMapContext(mode);
                }
                // Double check size after transition
                setTimeout(() => deliveryMap && deliveryMap.invalidateSize(), 500);
            }, 300);
        });
    }

    function refreshMapContext(mode) {
        if (!deliveryMap || !deliveryMarker) return;
        
        if (mode === 'livraison') {
            const lat = document.getElementById('user-lat').value;
            const lng = document.getElementById('user-lng').value;
            if(lat && lng) {
                deliveryMarker.setLatLng([lat, lng]);
                deliveryMap.setView([lat, lng], 17);
            }
            deliveryMarker.dragging.enable();
        } else {
            // Show vendor location
            const vLat = {{ $vendeur->latitude ?? '6.1319' }};
            const vLng = {{ $vendeur->longitude ?? '1.2227' }};
            deliveryMarker.setLatLng([vLat, vLng]);
            deliveryMap.setView([vLat, vLng], 17);
            deliveryMarker.dragging.disable();
            
            // Hide instructions overlay
            const instr = document.getElementById('map-instructions');
            if(instr) instr.classList.add('opacity-0');
        }
    }

    function initDeliveryMap() {
        const mode = document.querySelector('input[name="type_recuperation"]:checked').value;
        const oldLat = document.getElementById('user-lat').value;
        const oldLng = document.getElementById('user-lng').value;
        
        let defaultLat = 6.1319;
        let defaultLng = 1.2227;

        if (mode === 'livraison') {
            defaultLat = oldLat ? parseFloat(oldLat) : 6.1319;
            defaultLng = oldLng ? parseFloat(oldLng) : 1.2227;
        } else {
            defaultLat = {{ $vendeur->latitude ?? '6.1319' }};
            defaultLng = {{ $vendeur->longitude ?? '1.2227' }};
        }

        deliveryMap = L.map('delivery-map', {
            zoomControl: false,
            tap: true
        }).setView([defaultLat, defaultLng], (mode === 'livraison' && !oldLat) ? 13 : 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(deliveryMap);

        // Force tile loading update
        setTimeout(() => {
            deliveryMap.invalidateSize(true);
        }, 500);

        const customerIcon = L.divIcon({
            className: 'custom-customer-marker',
            html: `<div style="position:relative">
                        <div style="background:#ef4444; width:40px; height:40px; border-radius:50%; border:3px solid white; box-shadow:0 4px 14px rgba(239,68,68,0.4); display:flex; align-items:center; justify-content:center; font-size:18px; cursor:grab;">📍</div>
                    </div>`,
            iconSize: [40, 40],
            iconAnchor: [20, 40]
        });

        deliveryMarker = L.marker([defaultLat, defaultLng], {
            draggable: true,
            icon: customerIcon
        }).addTo(deliveryMap);

        if (oldLat) {
            calculateFee(oldLat, oldLng);
        }

        deliveryMarker.on('dragend', function() {
            const pos = deliveryMarker.getLatLng();
            setDeliveryPosition(pos.lat, pos.lng);
        });

        deliveryMap.on('click', function(e) {
            deliveryMarker.setLatLng(e.latlng);
            setDeliveryPosition(e.latlng.lat, e.latlng.lng);
        });

        mapInitialized = true;
    }

    function setDeliveryPosition(lat, lng) {
        document.getElementById('user-lat').value = lat;
        document.getElementById('user-lng').value = lng;
        
        // Hide instructions overlay once position is set
        const instr = document.getElementById('map-instructions');
        if(instr) instr.classList.add('opacity-0');
        
        calculateFee(lat, lng);
        validateStep1();
    }

    function detectLocation() {
        const btn = document.getElementById('gps-btn');
        const icon = document.getElementById('gps-icon');
        const spinner = document.getElementById('gps-spinner');
        const text = document.getElementById('gps-text');

        if ("geolocation" in navigator) {
            icon.classList.add('hidden');
            spinner.classList.remove('hidden');
            text.textContent = 'Localisation...';
            btn.disabled = true;

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    if (deliveryMap && deliveryMarker) {
                        setTimeout(() => {
                            deliveryMap.invalidateSize(true);
                        }, 400);
                        
                        deliveryMarker.setLatLng([lat, lng]);
                        deliveryMap.flyTo([lat, lng], 17, { duration: 1.2 });
                    } else {
                        initDeliveryMap();
                        setTimeout(() => {
                            deliveryMarker.setLatLng([lat, lng]);
                            deliveryMap.flyTo([lat, lng], 17, { duration: 1.2 });
                        }, 300);
                    }

                    setDeliveryPosition(lat, lng);
                    icon.classList.remove('hidden');
                    spinner.classList.add('hidden');
                    text.textContent = 'Position OK !';
                    btn.disabled = false;
                    btn.classList.replace('bg-red-600', 'bg-green-600');

                    if (window.showToast) showToast('Position détectée !', 'success');
                },
                function(error) {
                    icon.classList.remove('hidden');
                    spinner.classList.add('hidden');
                    text.textContent = 'Ma position GPS';
                    btn.disabled = false;
                    let msg = 'Erreur GPS : ' + error.message;
                    if (window.showToast) showToast(msg, 'error');
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        }
    }

    function calculateFee(lat, lng) {
        fetch('{{ route('checkout.calculate-delivery') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                vendeur_id: {{ $vendeur->id_vendeur }},
                lat: lat,
                lng: lng
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.out_of_range) {
                if (window.showToast) showToast("Désolé, votre adresse est trop éloignée (" + data.distance + " km). Distance max: " + data.max_distance + " km", 'error');
                document.getElementById('fee-val').innerText = 'TROP LOIN';
                document.getElementById('distance-info').classList.replace('bg-red-50', 'bg-red-600');
                document.getElementById('distance-info').classList.add('text-white');
                document.querySelector('button[type="submit"]').disabled = true;
                document.querySelector('button[type="submit"]').innerText = 'Zone non couverte';
                document.querySelector('button[type="submit"]').classList.replace('bg-red-600', 'bg-gray-400');
                return;
            }

            // In range
            document.querySelector('button[type="submit"]').disabled = false;
            document.querySelector('button[type="submit"]').innerText = 'Commander';
            document.querySelector('button[type="submit"]').classList.replace('bg-gray-400', 'bg-red-600');
            document.getElementById('distance-info').classList.replace('bg-red-600', 'bg-red-50');
            document.getElementById('distance-info').classList.remove('text-white');

            currentFee = data.fee;
            document.getElementById('distance-info').classList.remove('hidden');
            document.getElementById('distance-val').innerText = data.distance;
            document.getElementById('fee-val').innerText = data.fee.toLocaleString();
            document.getElementById('summary-fee-step2').innerText = data.fee.toLocaleString();
            document.getElementById('delivery-fee-val').value = data.fee;
            updateTotal();
        });
    }

    function updateTotal() {
        let total = subTotal + currentFee;
        document.getElementById('total-val-step2').innerText = total.toLocaleString();
    }

    function nextStep() {
        // Validation Step 1
        const nom = document.getElementById('nom_complet').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        const type = document.querySelector('input[name="type_recuperation"]:checked').value;

        if (!nom || !email || !phone) {
            if (window.showToast) showToast('Veuillez remplir toutes vos coordonnées.', 'error');
            return;
        }

        // Email regex
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            if (window.showToast) showToast('Veuillez entrer une adresse e-mail valide.', 'error');
            return;
        }

        if (type === 'livraison') {
            const lat = document.getElementById('user-lat').value;
            const lng = document.getElementById('user-lng').value;
            const addr = document.getElementById('adresse-livraison').value;
            if (!lat || !lng || !addr) {
                if (window.showToast) showToast('Veuillez indiquer votre position et adresse de livraison.', 'error');
                return;
            }
        }

        // Transition
        document.getElementById('step-1-content').classList.add('hidden');
        document.getElementById('step-2-content').classList.remove('hidden');
        
        // Progress UI
        document.getElementById('progress-bar').style.width = '100%';
        document.getElementById('step-2-indicator').classList.replace('bg-gray-200', 'bg-red-600');
        document.getElementById('step-2-indicator').classList.replace('dark:bg-gray-800', 'bg-red-600');
        document.getElementById('step-2-indicator').classList.replace('text-gray-400', 'text-white');
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function prevStep() {
        document.getElementById('step-2-content').classList.add('hidden');
        document.getElementById('step-1-content').classList.remove('hidden');
        
        // Progress UI
        document.getElementById('progress-bar').style.width = '0%';
        document.getElementById('step-2-indicator').classList.replace('bg-red-600', 'bg-gray-200');
        document.getElementById('step-2-indicator').classList.add('dark:bg-gray-800');
        document.getElementById('step-2-indicator').classList.replace('text-white', 'text-gray-400');
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    document.getElementById('checkout-form').addEventListener('input', validateStep1);
    document.getElementById('checkout-form').addEventListener('change', validateStep1);

    function validateStep1() {
        const nom = document.getElementById('nom_complet').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        const type = document.querySelector('input[name="type_recuperation"]:checked').value;
        const nextBtn = document.getElementById('next-btn');

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        let isValid = nom.length > 2 && emailRegex.test(email) && phone.length >= 8;

        if (type === 'livraison') {
            const lat = document.getElementById('user-lat').value;
            const lng = document.getElementById('user-lng').value;
            const addr = document.getElementById('adresse-livraison').value;
            if (!lat || !lng || addr.length < 5) {
                isValid = false;
            }
        }

        if (isValid) {
            nextBtn.disabled = false;
            nextBtn.classList.replace('bg-gray-400', 'bg-red-600');
            nextBtn.classList.replace('cursor-not-allowed', 'cursor-pointer');
            nextBtn.classList.add('hover:bg-red-700', 'shadow-xl', 'shadow-red-600/20');
        } else {
            nextBtn.disabled = true;
            nextBtn.classList.replace('bg-red-600', 'bg-gray-400');
            nextBtn.classList.replace('cursor-pointer', 'cursor-not-allowed');
            nextBtn.classList.remove('hover:bg-red-700', 'shadow-xl', 'shadow-red-600/20');
        }
    }

    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        const type = document.querySelector('input[name="type_recuperation"]:checked').value;
        if (type === 'livraison') {
            const lat = document.getElementById('user-lat').value;
            const lng = document.getElementById('user-lng').value;
            if (!lat || !lng) {
                e.preventDefault();
                if (window.showToast) showToast('Veuillez indiquer votre position sur la carte.', 'error');
                return false;
            }
        }
    });
</script>
@endsection
