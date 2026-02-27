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
            <!-- Hidden Fields for Location -->
            <input type="hidden" name="lat" id="user-lat" value="{{ old('lat') }}">
            <input type="hidden" name="lng" id="user-lng" value="{{ old('lng') }}">
            <input type="hidden" name="delivery_fee" id="delivery-fee-val" value="{{ old('delivery_fee', 0) }}">
            <input type="hidden" name="mode_paiement" value="espece">

            <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
                
                <!-- Main Form (Left) -->
                <div class="flex-1 space-y-8">
                    <div class="space-y-2">
                        <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Finaliser ma commande</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium italic">Veuillez confirmer vos détails pour la préparation.</p>
                    </div>

                    <!-- Step 1: Identity -->
                    <div class="bg-white dark:bg-gray-900 rounded-[2rem] p-6 sm:p-10 border border-gray-100 dark:border-gray-800 shadow-sm space-y-8">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center font-black text-sm">01</div>
                            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-gray-900 dark:text-white">Informations de contact</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 ml-2">Nom Complet</label>
                                <input type="text" name="nom_complet" value="{{ old('nom_complet', auth()->check() ? auth()->user()->nom_complet : '') }}" required
                                       class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-red-500 focus:bg-white dark:focus:bg-gray-700 rounded-2xl text-sm font-bold text-gray-900 dark:text-white transition-all outline-none placeholder-gray-400 dark:placeholder-gray-600 @error('nom_complet') border-red-500 @enderror"
                                       placeholder="Ex: Jean Dupont">
                                @error('nom_complet') <p class="text-[10px] text-red-500 font-bold ml-2">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 ml-2">Numéro de téléphone</label>
                                <input type="tel" name="phone" value="{{ old('phone', auth()->check() ? auth()->user()->telephone : '') }}" required
                                       class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-red-500 focus:bg-white dark:focus:bg-gray-700 rounded-2xl text-sm font-bold text-gray-900 dark:text-white transition-all outline-none placeholder-gray-400 dark:placeholder-gray-600 @error('phone') border-red-500 @enderror"
                                       placeholder="+228 00 00 00 00">
                                @error('phone') <p class="text-[10px] text-red-500 font-bold ml-2">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Recovery Mode -->
                    <div class="bg-white dark:bg-gray-900 rounded-[2rem] p-6 sm:p-10 border border-gray-100 dark:border-gray-800 shadow-sm space-y-8">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center font-black text-sm">02</div>
                            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-gray-900 dark:text-white">Mode de récupération</h2>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-3">
                            @php
                                $modes = [
                                    ['val' => 'emporter', 'label' => 'Emporter', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'desc' => 'Je récupère'],
                                    ['val' => 'sur_place', 'label' => 'Sur Place', 'icon' => 'M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 16v2m3-6v6m3-3v3M9 12h6M4 21h16a1 1 0 001-1V4a1 1 0 00-1-1H4a1 1 0 00-1 1v16a1 1 0 001 1z', 'desc' => 'Je mange ici'],
                                    ['val' => 'livraison', 'label' => 'Livraison', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'desc' => 'On me livre']
                                ];
                            @endphp
                            @foreach($modes as $mode)
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="type_recuperation" value="{{ $mode['val'] }}" class="peer sr-only" 
                                       {{ old('type_recuperation', 'emporter') == $mode['val'] ? 'checked' : '' }} 
                                       onchange="toggleDelivery(this.value === 'livraison')">
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
                                    <div class="relative rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800 h-40 group">
                                        <div id="delivery-map" class="w-full h-full z-0"></div>
                                        <div class="absolute inset-x-0 bottom-0 p-2 bg-black/50 backdrop-blur-sm text-center opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                            <span class="text-[8px] font-bold text-white uppercase">Glissez pour ajuster</span>
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

                    <!-- Step 3: Special Notes -->
                    <div class="bg-white dark:bg-gray-900 rounded-[2rem] p-6 sm:p-10 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                        <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 ml-2">Instructions Spéciales (Optionnel)</label>
                        <textarea name="notes" rows="2" 
                                  class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-red-500 focus:bg-white dark:focus:bg-gray-700 rounded-2xl text-sm font-bold text-gray-900 dark:text-white transition-all outline-none resize-none"
                                  placeholder="Ex: Sans piment, code entrée 1234...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Order Summary (Right) -->
                <div class="lg:w-[400px] shrink-0">
                    <div class="sticky top-28 space-y-6">
                        <div class="bg-gray-900 dark:bg-gray-900 rounded-[2.5rem] p-10 text-white shadow-2xl border border-white/5 space-y-8 overflow-hidden relative">
                            <!-- Subtle Glow -->
                            <div class="absolute -top-10 -right-10 w-32 h-32 bg-red-500/20 blur-3xl rounded-full"></div>
                            
                            <h2 class="text-xl font-black uppercase tracking-widest border-b border-white/10 pb-6 mb-2">Mon Panier</h2>
                            
                            <!-- Items List (Compact) -->
                            <div class="space-y-4 max-h-[300px] overflow-y-auto no-scrollbar">
                                @foreach($cart as $item)
                                <div class="flex items-center gap-4 py-2 border-b border-white/5 last:border-0">
                                    <div class="w-10 h-10 rounded-xl bg-gray-800 overflow-hidden shrink-0 border border-white/10">
                                        <img src="{{ $item['image'] ? asset('storage/'.$item['image']) : asset('assets/default-plat.png') }}" class="w-full h-full object-cover" alt="">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[11px] font-black uppercase tracking-tight truncate">{{ $item['name'] }}</p>
                                        <p class="text-[9px] text-gray-400 font-bold">x{{ $item['quantity'] }} • {{ number_format($item['price'], 0, ',', ' ') }} FCFA</p>
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
                                <div id="delivery-fee-line" class="hidden justify-between text-white/50 text-[10px] font-black uppercase tracking-widest">
                                    <span>Livraison</span>
                                    <span class="text-white"><span id="summary-fee">0</span> FCFA</span>
                                </div>
                                <div class="flex justify-between items-end pt-4">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-red-500">Total à payer</p>
                                    <p class="text-4xl font-black tracking-tighter text-white">
                                        <span id="total-val">{{ number_format($total, 0, ',', ' ') }}</span>
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
                                <p class="text-[9px] text-gray-400 font-bold italic leading-relaxed text-center">
                                    Mobile Money & Carte bancaire bientôt disponibles.
                                </p>
                            </div>

                            <button type="submit" class="w-full py-6 bg-red-600 hover:bg-red-700 text-white rounded-[1.5rem] font-black text-xs uppercase tracking-[0.2em] transition-all shadow-xl shadow-red-600/20 active:scale-95 flex items-center justify-center gap-3">
                                Commander 
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </button>
                        </div>

                        <!-- Back Button Mobile -->
                        <a href="{{ route('cart.index') }}" class="lg:hidden w-full flex items-center justify-center gap-2 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-gray-900 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            Modifier ma commande
                        </a>
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

    // Check if we already have old input to show delivery details
    if (document.querySelector('input[name="type_recuperation"]:checked').value === 'livraison') {
        setTimeout(() => toggleDelivery(true), 100);
    }

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
            document.getElementById('distance-info').classList.add('hidden');
            updateTotal();
        }
    }

    function initDeliveryMap() {
        // Default center: Lomé or old coordinates
        const oldLat = document.getElementById('user-lat').value;
        const oldLng = document.getElementById('user-lng').value;
        const defaultLat = oldLat ? parseFloat(oldLat) : 6.1319;
        const defaultLng = oldLng ? parseFloat(oldLng) : 1.2227;

        deliveryMap = L.map('delivery-map', {
            zoomControl: false,
            tap: true
        }).setView([defaultLat, defaultLng], oldLat ? 16 : 13);

        L.tileLayer('https://{s}.tile.basemaps.cartocdn.com/voyager_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap',
            maxZoom: 19
        }).addTo(deliveryMap);

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
        calculateFee(lat, lng);
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
            document.getElementById('summary-fee').innerText = data.fee.toLocaleString();
            document.getElementById('delivery-fee-val').value = data.fee;
            updateTotal();
        });
    }

    function updateTotal() {
        let total = subTotal + currentFee;
        document.getElementById('total-val').innerText = total.toLocaleString();
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
