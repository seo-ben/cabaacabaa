@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#FDFCFB] dark:bg-gray-950 py-24 transition-colors duration-300">
    <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14">
        <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
            @csrf
            <!-- Hidden Fields for Location -->
            <input type="hidden" name="lat" id="user-lat">
            <input type="hidden" name="lng" id="user-lng">
            <input type="hidden" name="delivery_fee" id="delivery-fee-val" value="0">

            <div class="flex flex-col lg:flex-row gap-12">
                
                <!-- Form Side -->
                <div class="flex-1 space-y-12">
                    <div class="space-y-4">
                        <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter">Finalisez votre commande</h1>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">Laissez-nous vos coordonnées pour la suite.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-8 bg-white dark:bg-gray-900 rounded-2xl p-10 border border-gray-100 dark:border-gray-800 shadow-sm">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 ml-4">Nom Complet</label>
                            <input type="text" name="nom_complet" value="{{ auth()->check() ? auth()->user()->nom_complet : '' }}" required
                                   class="w-full px-8 py-5 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-orange-500 focus:bg-white dark:focus:bg-gray-700 rounded-xl text-sm font-bold text-gray-900 dark:text-white transition-all outline-none placeholder-gray-400 dark:placeholder-gray-600"
                                   placeholder="Ex: Jean Dupont">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 ml-4">Numéro de téléphone</label>
                            <input type="tel" name="phone" value="{{ auth()->check() ? auth()->user()->telephone : '' }}" required
                                   class="w-full px-8 py-5 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-orange-500 focus:bg-white dark:focus:bg-gray-700 rounded-xl text-sm font-bold text-gray-900 dark:text-white transition-all outline-none placeholder-gray-400 dark:placeholder-gray-600"
                                   placeholder="+228 00 00 00 00">
                        </div>

                        <div class="md:col-span-2 pt-10 border-t border-gray-100 dark:border-gray-800 space-y-8">
                            <div class="space-y-1">
                                <h3 class="text-sm font-black uppercase tracking-widest text-gray-900 dark:text-white">Mode de récupération</h3>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium italic">Choisissez comment vous souhaitez recevoir vos articles.</p>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-2 sm:gap-6">
                                <!-- À Emporter -->
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="type_recuperation" value="emporter" class="peer sr-only" checked onchange="toggleDelivery(false)">
                                    <div class="h-full p-2 sm:p-8 bg-white dark:bg-gray-800 border-2 border-gray-50 dark:border-gray-700 rounded-2xl transition-all duration-300 peer-checked:border-orange-500 dark:peer-checked:border-orange-500 peer-checked:bg-orange-50/30 dark:peer-checked:bg-orange-900/20 peer-checked:shadow-2xl peer-checked:shadow-orange-200/40 dark:peer-checked:shadow-none hover:border-gray-200 dark:hover:border-gray-600 group-active:scale-95 flex flex-col items-center text-center gap-2 sm:gap-4">
                                        <div class="w-8 h-8 sm:w-16 sm:h-16 rounded-xl sm:rounded-2xl bg-orange-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/30 group-hover:rotate-6 transition-transform">
                                            <svg class="w-4 h-4 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                        </div>
                                        <div>
                                            <span class="block text-[8px] sm:text-xs font-black uppercase tracking-widest text-gray-900 dark:text-white mb-0 sm:mb-1">À emporter</span>
                                            <span class="hidden sm:block text-[9px] text-gray-400 dark:text-gray-500 font-bold leading-tight">Je récupère moi-même à l'établissement</span>
                                        </div>
                                        <div class="absolute top-1 right-1 sm:top-4 sm:right-4 opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <div class="w-3 h-3 sm:w-5 sm:h-5 bg-orange-500 rounded-full flex items-center justify-center">
                                                <svg class="w-2 h-2 sm:w-3 sm:h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <!-- Sur Place -->
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="type_recuperation" value="sur_place" class="peer sr-only" onchange="toggleDelivery(false)">
                                    <div class="h-full p-2 sm:p-8 bg-white dark:bg-gray-800 border-2 border-gray-50 dark:border-gray-700 rounded-2xl transition-all duration-300 peer-checked:border-orange-500 dark:peer-checked:border-orange-500 peer-checked:bg-orange-50/30 dark:peer-checked:bg-orange-900/20 peer-checked:shadow-2xl peer-checked:shadow-orange-200/40 dark:peer-checked:shadow-none hover:border-gray-200 dark:hover:border-gray-600 group-active:scale-95 flex flex-col items-center text-center gap-2 sm:gap-4">
                                        <div class="w-8 h-8 sm:w-16 sm:h-16 rounded-xl sm:rounded-2xl bg-gray-900 dark:bg-white dark:text-gray-900 text-white flex items-center justify-center shadow-lg shadow-gray-900/20 group-hover:-rotate-6 transition-transform">
                                            <svg class="w-4 h-4 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 16v2m3-6v6m3-3v3M9 12h6M4 21h16a1 1 0 001-1V4a1 1 0 00-1-1H4a1 1 0 00-1 1v16a1 1 0 001 1z"/></svg>
                                        </div>
                                        <div>
                                            <span class="block text-[8px] sm:text-xs font-black uppercase tracking-widest text-gray-900 dark:text-white mb-0 sm:mb-1">Sur place</span>
                                            <span class="hidden sm:block text-[9px] text-gray-400 dark:text-gray-500 font-bold leading-tight">Réservez une table et profitez ici</span>
                                        </div>
                                        <div class="absolute top-1 right-1 sm:top-4 sm:right-4 opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <div class="w-3 h-3 sm:w-5 sm:h-5 bg-orange-500 rounded-full flex items-center justify-center">
                                                <svg class="w-2 h-2 sm:w-3 sm:h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <!-- Livraison -->
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="type_recuperation" value="livraison" class="peer sr-only" onchange="toggleDelivery(true)">
                                    <div class="h-full p-2 sm:p-8 bg-white dark:bg-gray-800 border-2 border-gray-50 dark:border-gray-700 rounded-2xl transition-all duration-300 peer-checked:border-orange-500 dark:peer-checked:border-orange-500 peer-checked:bg-orange-50/30 dark:peer-checked:bg-orange-900/20 peer-checked:shadow-2xl peer-checked:shadow-orange-200/40 dark:peer-checked:shadow-none hover:border-gray-200 dark:hover:border-gray-600 group-active:scale-95 flex flex-col items-center text-center gap-2 sm:gap-4">
                                        <div class="w-8 h-8 sm:w-16 sm:h-16 rounded-xl sm:rounded-2xl bg-orange-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/30 group-hover:rotate-12 transition-transform">
                                            <svg class="w-4 h-4 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        </div>
                                        <div>
                                            <span class="block text-[8px] sm:text-xs font-black uppercase tracking-widest text-gray-900 dark:text-white mb-0 sm:mb-1">Livraison</span>
                                            <span class="hidden sm:block text-[9px] text-gray-400 dark:text-gray-500 font-bold leading-tight">Frais calculés par distance GPS</span>
                                        </div>
                                        <div class="absolute top-1 right-1 sm:top-4 sm:right-4 opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <div class="w-3 h-3 sm:w-5 sm:h-5 bg-orange-500 rounded-full flex items-center justify-center">
                                                <svg class="w-2 h-2 sm:w-3 sm:h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Delivery Details (Hidden initially) -->
                        <div id="delivery-details" class="md:col-span-2 pt-6 border-t border-gray-50 dark:border-gray-800 space-y-6 hidden">
                            
                            <!-- Header -->
                            <div class="space-y-1">
                                <h3 class="text-sm font-black uppercase tracking-widest text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Où livrer votre commande ?
                                </h3>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium italic">Cliquez sur le bouton ci-dessous ou touchez la carte pour indiquer votre position exacte.</p>
                            </div>

                            <!-- GPS Button -->
                            <button type="button" id="gps-btn" onclick="detectLocation()" 
                                    class="w-full py-4 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-orange-500/20 active:scale-95 transition-all flex items-center justify-center gap-3">
                                <svg id="gps-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <svg id="gps-spinner" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span id="gps-text">📍 Prendre ma position GPS</span>
                            </button>

                            <!-- Map Container -->
                            <div class="relative rounded-2xl overflow-hidden border-2 border-gray-100 dark:border-gray-700 shadow-inner">
                                <div id="delivery-map" class="w-full h-64 sm:h-80 z-0"></div>
                                <!-- Map Instruction Overlay -->
                                <div id="map-instruction" class="absolute top-3 left-1/2 -translate-x-1/2 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm px-4 py-2 rounded-full shadow-lg z-[500] pointer-events-none">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                        <span class="text-base">👆</span> Touchez la carte ou glissez le marqueur
                                    </p>
                                </div>
                            </div>

                            <!-- Location Status -->
                            <div id="location-status" class="hidden p-4 bg-green-50 dark:bg-green-900/20 rounded-2xl border border-green-100 dark:border-green-800">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-green-700 dark:text-green-400 uppercase tracking-widest">Position enregistrée ✓</p>
                                        <p class="text-[9px] text-green-600/70 dark:text-green-400/60 font-bold" id="coords-display"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Address Input -->
                            <input type="text" name="adresse_livraison" id="adresse-livraison"
                                   class="w-full px-8 py-5 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-orange-500 focus:bg-white dark:focus:bg-gray-700 rounded-xl text-sm font-bold text-gray-900 dark:text-white transition-all outline-none placeholder-gray-400 dark:placeholder-gray-600"
                                   placeholder="Décrivez votre adresse (Quartier, Rue, Repère...)">
                            
                            <!-- Distance & Fee Info -->
                            <div id="distance-info" class="p-4 bg-orange-50 dark:bg-orange-900/20 rounded-2xl hidden">
                                <p class="text-[10px] font-black text-orange-700 dark:text-orange-400 uppercase tracking-widest flex justify-between">
                                    <span>Distance estimée : <span id="distance-val">0</span> km</span>
                                    <span>Frais : <span id="fee-val">0</span> FCFA</span>
                                </p>
                            </div>
                        </div>

                        <div class="md:col-span-2 pt-6 space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 ml-4">Instructions spéciales (optionnel)</label>
                            <textarea name="notes" rows="3" 
                                      class="w-full px-8 py-5 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-orange-500 focus:bg-white dark:focus:bg-gray-700 rounded-xl text-sm font-bold text-gray-900 dark:text-white transition-all outline-none resize-none placeholder-gray-400 dark:placeholder-gray-600"
                                      placeholder="Ex: Pas de piment, code porte..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Summary Side -->
                <div class="lg:w-96">
                    <div class="sticky top-32 space-y-8">
                        <div class="bg-gray-900 dark:bg-gray-800 rounded-2xl p-6 sm:p-10 text-white space-y-8 shadow-2xl shadow-gray-200 dark:shadow-none border border-transparent dark:border-gray-700 w-full overflow-hidden">
                            <h3 class="text-lg sm:text-xl font-black border-b border-white/10 pb-6 uppercase tracking-widest">Résumé</h3>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between text-white/60">
                                    <span class="text-[10px] font-black uppercase tracking-widest">Articles ({{ count($cart) }})</span>
                                    <span class="text-sm font-bold">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                                </div>
                                <div id="delivery-fee-line" class="hidden justify-between text-white/60">
                                    <span class="text-[10px] font-black uppercase tracking-widest">Livraison</span>
                                    <span class="text-sm font-bold"><span id="summary-fee">0</span> FCFA</span>
                                </div>
                                <div class="pt-6 border-t border-white/10 flex justify-between items-end">
                                    <span class="text-[10px] font-black uppercase tracking-widest">Total</span>
                                    <span class="text-3xl font-black text-orange-400 tracking-tighter">
                                        <span id="total-val">{{ number_format($total, 0, ',', ' ') }}</span> 
                                        <small class="text-[10px]">FCFA</small>
                                    </span>
                                </div>
                            </div>

                            <div class="pt-4 space-y-4">
                                <h3 class="text-[10px] font-black uppercase tracking-widest text-white/40">Mode de paiement</h3>
                                
                                {{-- ============================================================================ --}}
                                {{-- PAIEMENT EN LIGNE - TEMPORAIREMENT DÉSACTIVÉ --}}
                                {{-- ============================================================================ --}}
                                {{-- TODO: Réactiver quand Tmoney, Flooz, carte bancaire seront opérationnels --}}
                                {{-- ============================================================================ --}}
                                
                                <div class="grid grid-cols-1 gap-3">
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="mode_paiement" value="espece" class="peer sr-only" checked>
                                        <div class="py-3 px-4 bg-white/5 border border-white/10 peer-checked:border-orange-500 peer-checked:bg-white/10 rounded-2xl text-[9px] font-black uppercase tracking-widest text-center transition-all">
                                            💵 Paiement en Espèces (Uniquement disponible)
                                        </div>
                                    </label>
                                    
                                    {{-- Option Mobile Money désactivée temporairement --}}
                                    {{--
                                    <label class="cursor-pointer group opacity-50 cursor-not-allowed">
                                        <input type="radio" name="mode_paiement" value="mobile_money" class="peer sr-only" disabled>
                                        <div class="py-3 px-4 bg-white/5 border border-white/10 rounded-2xl text-[9px] font-black uppercase tracking-widest text-center">
                                            Mobile Money (Bientôt disponible)
                                        </div>
                                    </label>
                                    --}}
                                </div>
                                
                                <div class="p-4 bg-orange-500/10 border border-orange-500/20 rounded-xl">
                                    <p class="text-[9px] font-bold text-orange-300 leading-relaxed">
                                        ℹ️ <strong>Information:</strong> Le paiement en ligne (Tmoney, Flooz, carte bancaire) sera bientôt disponible. Pour le moment, seul le paiement en espèces est accepté.
                                    </p>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-6 bg-orange-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-orange-700 transition-all shadow-xl shadow-orange-600/20 dark:shadow-none active:scale-95 flex items-center justify-center gap-3">
                                Confirmer la commande
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
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
    let currentFee = 0;
    let deliveryMap = null;
    let deliveryMarker = null;
    let mapInitialized = false;

    function toggleDelivery(show) {
        const details = document.getElementById('delivery-details');
        const summaryLine = document.getElementById('delivery-fee-line');
        if (show) {
            details.classList.remove('hidden');
            summaryLine.classList.remove('hidden');
            // Init map after a short delay so container has dimensions
            setTimeout(() => {
                if (!mapInitialized) {
                    initDeliveryMap();
                } else {
                    deliveryMap.invalidateSize();
                }
            }, 200);
        } else {
            details.classList.add('hidden');
            summaryLine.classList.add('hidden');
            currentFee = 0;
            document.getElementById('user-lat').value = '';
            document.getElementById('user-lng').value = '';
            document.getElementById('location-status').classList.add('hidden');
            updateTotal();
        }
    }

    function initDeliveryMap() {
        // Default center: Lomé
        const defaultLat = 6.1319;
        const defaultLng = 1.2227;

        deliveryMap = L.map('delivery-map', {
            zoomControl: true,
            tap: true
        }).setView([defaultLat, defaultLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap',
            maxZoom: 19
        }).addTo(deliveryMap);

        // Create a draggable marker icon
        const customerIcon = L.divIcon({
            className: 'custom-customer-marker',
            html: `<div style="position:relative">
                        <div style="background:#f97316; width:40px; height:40px; border-radius:50%; border:3px solid white; box-shadow:0 4px 14px rgba(249,115,22,0.4); display:flex; align-items:center; justify-content:center; font-size:18px; cursor:grab;">📍</div>
                        <div style="position:absolute; bottom:-4px; left:50%; transform:translateX(-50%); width:16px; height:6px; background:rgba(0,0,0,0.15); border-radius:50%; filter:blur(1px);"></div>
                    </div>`,
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        });

        deliveryMarker = L.marker([defaultLat, defaultLng], {
            draggable: true,
            icon: customerIcon
        }).addTo(deliveryMap);

        // Marker drag
        deliveryMarker.on('dragend', function() {
            const pos = deliveryMarker.getLatLng();
            setDeliveryPosition(pos.lat, pos.lng);
            // Hide instruction after first interaction
            document.getElementById('map-instruction').classList.add('hidden');
        });

        // Map click
        deliveryMap.on('click', function(e) {
            deliveryMarker.setLatLng(e.latlng);
            setDeliveryPosition(e.latlng.lat, e.latlng.lng);
            document.getElementById('map-instruction').classList.add('hidden');
        });

        mapInitialized = true;
    }

    function setDeliveryPosition(lat, lng) {
        document.getElementById('user-lat').value = lat;
        document.getElementById('user-lng').value = lng;

        // Show location status
        const status = document.getElementById('location-status');
        status.classList.remove('hidden');
        document.getElementById('coords-display').textContent = `Lat: ${lat.toFixed(5)}, Lng: ${lng.toFixed(5)}`;

        // Auto calc fee
        calculateFee(lat, lng);
    }

    function detectLocation() {
        const btn = document.getElementById('gps-btn');
        const icon = document.getElementById('gps-icon');
        const spinner = document.getElementById('gps-spinner');
        const text = document.getElementById('gps-text');

        if ("geolocation" in navigator) {
            // Show loading state
            icon.classList.add('hidden');
            spinner.classList.remove('hidden');
            text.textContent = 'Localisation en cours...';
            btn.disabled = true;
            btn.classList.add('opacity-70');

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    // Update marker and map
                    if (deliveryMap && deliveryMarker) {
                        deliveryMarker.setLatLng([lat, lng]);
                        deliveryMap.flyTo([lat, lng], 17, { duration: 1.2 });
                    } else {
                        // Map not ready yet, init then move
                        initDeliveryMap();
                        setTimeout(() => {
                            deliveryMarker.setLatLng([lat, lng]);
                            deliveryMap.flyTo([lat, lng], 17, { duration: 1.2 });
                        }, 300);
                    }

                    setDeliveryPosition(lat, lng);
                    document.getElementById('map-instruction').classList.add('hidden');

                    // Reset button to success state
                    icon.classList.remove('hidden');
                    spinner.classList.add('hidden');
                    text.textContent = '✅ Position détectée ! Recliquez pour mettre à jour';
                    btn.disabled = false;
                    btn.classList.remove('opacity-70');
                    btn.classList.remove('from-orange-500', 'to-orange-600');
                    btn.classList.add('from-green-500', 'to-green-600', 'shadow-green-500/20');

                    // Show toast
                    if (typeof showToast === 'function') {
                        showToast('Position GPS détectée avec succès !', 'success');
                    }
                },
                function(error) {
                    // Reset button on error
                    icon.classList.remove('hidden');
                    spinner.classList.add('hidden');
                    text.textContent = '📍 Prendre ma position GPS';
                    btn.disabled = false;
                    btn.classList.remove('opacity-70');

                    let msg = 'Impossible de détecter votre position.';
                    if (error.code === 1) msg = 'Veuillez autoriser l\'accès à votre position dans les réglages de votre navigateur.';
                    if (error.code === 2) msg = 'Position indisponible. Vérifiez votre GPS.';
                    if (error.code === 3) msg = 'La détection a pris trop de temps. Réessayez.';

                    if (typeof showToast === 'function') {
                        showToast(msg, 'error');
                    } else {
                        alert(msg);
                    }
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        } else {
            alert('Votre navigateur ne supporte pas la géolocalisation. Touchez directement la carte pour indiquer votre position.');
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
                const msg = "Désolé, votre adresse est trop éloignée (" + data.distance + " km). Distance max : " + data.max_distance + " km.";
                if (typeof showToast === 'function') {
                    showToast(msg, 'error');
                } else {
                    alert(msg);
                }
                document.querySelector('input[name="type_recuperation"][value="emporter"]').checked = true;
                toggleDelivery(false);
                return;
            }

            currentFee = data.fee;
            document.getElementById('distance-info').classList.remove('hidden');
            
            let infoBox = document.getElementById('distance-info');
            infoBox.innerHTML = `
                <div class="flex flex-col gap-2">
                    <p class="text-[10px] font-black text-orange-700 dark:text-orange-400 uppercase tracking-widest flex justify-between">
                        <span>Distance : ${data.distance} km</span>
                        <span>Frais : ${data.fee.toLocaleString()} FCFA</span>
                    </p>
                    <p class="text-[9px] font-bold text-orange-600/70 dark:text-orange-400/70 uppercase tracking-[0.2em] flex items-center gap-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Arrivée estimée dans ${data.estimated_time} minutes
                    </p>
                </div>
            `;

            document.getElementById('summary-fee').innerText = data.fee.toLocaleString();
            document.getElementById('delivery-fee-val').value = data.fee;
            updateTotal();
        });
    }

    function updateTotal() {
        let total = subTotal + currentFee;
        document.getElementById('total-val').innerText = total.toLocaleString();
    }

    // Validate form before submit - ensure location is set when delivery is selected
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        const type = document.querySelector('input[name="type_recuperation"]:checked').value;
        if (type === 'livraison') {
            const lat = document.getElementById('user-lat').value;
            const lng = document.getElementById('user-lng').value;
            if (!lat || !lng) {
                e.preventDefault();
                const msg = 'Veuillez indiquer votre position de livraison en cliquant sur "Prendre ma position GPS" ou en touchant la carte.';
                if (typeof showToast === 'function') {
                    showToast(msg, 'error');
                } else {
                    alert(msg);
                }
                return false;
            }
        }
    });
</script>
@endsection
