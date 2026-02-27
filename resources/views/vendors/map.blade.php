@extends('layouts.app')

@section('title', 'Vendeurs Proches - Localisez vos restaurants')

@section('head')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Reset and base map styles */
    #map-container {
        position: relative;
        height: calc(100vh - 80px);
        width: 100%;
        overflow: hidden;
        background: #f3f4f6;
    }

    #map {
        height: 100%;
        width: 100%;
        z-index: 0;
    }

    /* Floating UI - Matching Driver Map Style */
    .status-overlay {
        position: absolute; 
        top: 20px; 
        right: 20px; 
        left: 20px;
        z-index: 1000;
        pointer-events: none;
    }

    @media (min-width: 640px) {
        .status-overlay { left: auto; width: 320px; }
    }

    .glass-panel {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 24px;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
        pointer-events: auto;
        padding: 20px;
    }

    /* Radius Selection */
    .radius-selector {
        display: flex;
        gap: 8px;
        margin-top: 12px;
        overflow-x: auto;
        padding-bottom: 4px;
        scrollbar-width: none;
    }
    .radius-selector::-webkit-scrollbar { display: none; }

    .radius-item {
        padding: 8px 14px;
        border-radius: 12px;
        font-weight: 900;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.3s;
        background: #f3f4f6;
        color: #6b7280;
    }

    .radius-item.active {
        background: #f97316;
        color: white;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);
    }

    /* Action Buttons */
    .action-group {
        position: absolute;
        bottom: 30px;
        right: 20px;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .map-btn {
        width: 50px;
        height: 50px;
        background: white;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: all 0.3s;
        pointer-events: auto;
    }

    .map-btn:hover { transform: scale(1.05); }
    .map-btn svg { width: 22px; height: 22px; color: #1f2937; }

    /* Custom Markers */
    .vendor-marker { 
        background: #f97316; 
        border: 2px solid white; 
        border-radius: 20px 20px 20px 0;
        width: 38px; 
        height: 38px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4); 
        font-size: 16px; 
        transform: rotate(-45deg);
        margin-top: -19px;
    }
    .vendor-marker-inner {
        transform: rotate(45deg);
    }
    
    .user-marker { 
        width: 20px; height: 20px; background: #3b82f6; border: 3px solid white; border-radius: 50%; shadow: 0 0 15px rgba(59,130,246,0.5); position: relative;
    }
    .user-marker::after { content: ''; position: absolute; inset: -6px; border-radius: 50%; background: rgba(59, 130, 246, 0.2); animation: pulse 2s infinite; }
    @keyframes pulse { 0% { transform: scale(0.8); opacity: 0.8; } 100% { transform: scale(2); opacity: 0; } }

    /* Popup Style */
    .leaflet-popup-content-wrapper { border-radius: 24px; padding: 0; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.15); }
    .leaflet-popup-content { margin: 0 !important; width: 300px !important; }
    .popup-info { padding: 0; }
    .popup-btn { display: inline-block; padding: 6px 12px; background: #111827; color: white; text-align: center; border-radius: 10px; font-weight: 900; font-size: 9px; text-transform: uppercase; letter-spacing: 0.1em; margin-top: 8px; }
    .dark .popup-btn { background: white; color: #111827; }

    /* Desktop Adjustments for Bottom Nav */
    @media (max-width: 1023px) {
        #map-container { height: calc(100vh - 145px); margin-bottom: 65px; }
        .action-group { bottom: 85px; }
    }

    /* Missing Loader & Spinner Styles */
    .loader-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(8px);
        z-index: 2000;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .loader-overlay.hidden { display: none !important; }
    
    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #f97316;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    .user-marker-container { overflow: visible !important; }
    .vendor-marker-wrapper { overflow: visible !important; }

    /* Specialty tags in popup */
    .specialty-tag {
        display: inline-block;
        padding: 2px 8px;
        background: #fdf2f8;
        color: #db2777;
        border-radius: 6px;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        margin-right: 4px;
        margin-bottom: 4px;
    }
    .dark .specialty-tag {
        background: rgba(219, 39, 119, 0.1);
        color: #f472b6;
    }
</style>
@endsection

@section('content')
<div id="map-container">
    <!-- UI Layer: Status Overlay -->
    <div class="status-overlay">
        <div class="glass-panel">
            <div class="flex items-center justify-between mb-2">
                <div class="flex flex-col" id="stats-pill">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.15em] text-gray-500">Rayon de recherche</h3>
                    <div id="stats-display" class="text-xs font-black text-gray-900 mt-0.5">
                        <span id="vendor-count">0</span> boutiques trouvées
                    </div>
                </div>
                <div class="w-2.5 h-2.5 rounded-full bg-orange-500 animate-pulse shadow-sm shadow-orange-200"></div>
            </div>

            <div class="radius-selector">
                <div class="radius-item active" data-radius="0.5">500m</div>
                <div class="radius-item" data-radius="1">1km</div>
                <div class="radius-item" data-radius="2">2km</div>
                <div class="radius-item" data-radius="5">5km</div>
                <div class="radius-item" data-radius="10">10km</div>
            </div>
        </div>
    </div>

    <!-- Map div -->
    <div id="map"></div>

    <!-- Action Group -->
    <div class="action-group">
        <div class="map-btn" onclick="recenterMap()" title="Ma position">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div class="map-btn" onclick="requestLocation()" title="Rafraîchir">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </div>
    </div>

    <!-- Permissions Overlay -->
    <div id="permission-card" class="loader-overlay hidden">
        <div class="glass-panel p-8 text-center max-w-sm mx-4 pointer-events-auto">
            <div class="text-5xl mb-4">📍</div>
            <h2 class="text-xl font-black mb-2">Localisation requise</h2>
            <p class="text-gray-600 text-sm mb-6 font-medium leading-relaxed">Pour voir les boutiques autour de vous, nous avons besoin de votre position GPS.</p>
            <button onclick="requestLocation()" class="w-full py-4 bg-orange-600 text-white rounded-2xl font-black uppercase tracking-widest text-[11px] shadow-xl shadow-orange-200 active:scale-95 transition-all">
                Autoriser l'accès
            </button>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loader" class="loader-overlay hidden">
        <div class="spinner mb-4"></div>
        <p class="text-[10px] font-black uppercase tracking-widest text-gray-500">Recherche...</p>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let map, userMarker, radiusCircle;
    let vendorMarkers = [];
    let currentRadius = 0.5;
    let userLat, userLng;
    let isInitialLoad = true;

    // Configuration de la carte
    function initMap(lat, lng) {
        if (!map) {
            map = L.map('map', {
                zoomControl: false,
                tap: false // Aide pour mobile
            }).setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
                maxZoom: 19
            }).addTo(map);

            // Ajouter le bouton de zoom en bas à gauche pour ne pas gêner
            L.control.zoom({ position: 'bottomleft' }).addTo(map);
        } else {
            map.setView([lat, lng]);
        }

        updateUserMarker(lat, lng);
        updateRadiusCircle(lat, lng);
    }

    function updateUserMarker(lat, lng) {
        const userIcon = L.divIcon({
            className: 'user-marker-container',
            html: '<div class="user-marker"></div>',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });

        if (userMarker) {
            userMarker.setLatLng([lat, lng]);
        } else {
            userMarker = L.marker([lat, lng], { icon: userIcon, zIndexOffset: 1000 }).addTo(map);
        }
    }

    function updateRadiusCircle(lat, lng) {
        if (radiusCircle) map.removeLayer(radiusCircle);
        
        radiusCircle = L.circle([lat, lng], {
            color: '#3b82f6',
            fillColor: '#3b82f6',
            fillOpacity: 0.05,
            weight: 1,
            dashArray: '5, 5',
            radius: currentRadius * 1000
        }).addTo(map);
    }

    // Demande de géolocalisation optimisée
    function requestLocation() {
        const loader = document.getElementById('loader');
        const permissionCard = document.getElementById('permission-card');
        
        loader.classList.remove('hidden');
        permissionCard.classList.add('hidden');

        const geoOptions = {
            enableHighAccuracy: false, // Plus rapide et moins bloqué que "true"
            timeout: 8000,
            maximumAge: 30000 // Cache de 30 secondes
        };

        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    userLat = pos.coords.latitude;
                    userLng = pos.coords.longitude;
                    
                    initMap(userLat, userLng);
                    loadVendors(true); // Fit bounds on first load
                    loader.classList.add('hidden');
                },
                (err) => {
                    console.error("Geo Error:", err);
                    loader.classList.add('hidden');
                    if (isInitialLoad) permissionCard.classList.remove('hidden');
                    
                    // Fallback IP si possible ou message d'erreur
                    window.showToast("Localisation impossible. Vérifiez vos paramètres.", "error");
                },
                geoOptions
            );
        } else {
            window.showToast("Votre navigateur ne supporte pas la géolocalisation.", "error");
        }
        isInitialLoad = false;
    }

    async function loadVendors(shouldFitBounds = false) {
        const pill = document.getElementById('stats-pill');
        const countDisplay = document.getElementById('vendor-count');
        
        try {
            const response = await fetch('{{ route('vendors.nearby') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    lat: userLat,
                    lng: userLng,
                    radius: currentRadius
                })
            });

            const data = await response.json();

            if (data.success) {
                // Nettoyage
                vendorMarkers.forEach(m => map.removeLayer(m));
                vendorMarkers = [];

                // Affichage count
                countDisplay.textContent = data.count;
                if (pill) pill.classList.remove('hidden');

                data.vendors.forEach(v => {
                    const icon = L.divIcon({
                        className: 'vendor-marker-wrapper',
                        html: `<div class="vendor-marker"><div class="vendor-marker-inner">🛍️</div></div>`,
                        iconSize: [38, 38],
                        iconAnchor: [19, 38]
                    });

                    // Prendre les 6 premières spécialités
                    const specialties = v.specialties ? v.specialties.slice(0, 6) : [];
                    const specialtiesHtml = specialties.map(s => `<span class="specialty-tag">${s}</span>`).join('');

                    const popupHtml = `
                        <div class="leaflet-popup-card group p-3">
                            <a href="${v.url}" class="flex items-center gap-4 no-underline">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <div class="text-[10px] font-black text-orange-600 bg-orange-50 px-1.5 py-0.5 rounded-md">★ ${v.note_moyenne || '5.0'}</div>
                                        <div class="text-[9px] font-bold text-gray-400">📍 ${v.distance_text}</div>
                                    </div>
                                    
                                    <h4 class="text-sm font-black text-gray-900 group-hover:text-orange-600 transition-colors uppercase truncate mb-2">${v.nom}</h4>
                                    
                                    <div class="flex flex-wrap gap-1 mb-2">
                                        ${specialtiesHtml}
                                    </div>

                                    <div class="popup-btn">Voir la boutique</div>
                                </div>

                                <div class="shrink-0 w-24 h-24 rounded-2xl overflow-hidden shadow-lg shadow-black/5 border border-gray-100">
                                    ${v.image_full 
                                        ? `<img src="${v.image_full}" class="w-full h-full object-cover" onerror="this.src='/images/default-vendor.jpg'">`
                                        : `<div class="w-full h-full bg-orange-50 flex items-center justify-center text-2xl">🛍️</div>`
                                    }
                                </div>
                            </a>
                        </div>
                    `;

                    const marker = L.marker([v.latitude, v.longitude], { icon: icon })
                        .addTo(map)
                        .bindPopup(popupHtml);
                    
                    vendorMarkers.push(marker);
                });

                if (data.count > 0 && shouldFitBounds) {
                    const group = new L.featureGroup([...vendorMarkers, userMarker]);
                    map.fitBounds(group.getBounds().pad(0.1));
                }
            }
        } catch (e) {
            console.error(e);
        }
    }

    function recenterMap() {
        if (userLat && userLng) {
            map.flyTo([userLat, userLng], 16);
        } else {
            requestLocation();
        }
    }

    // Event Listeners
    document.querySelectorAll('.radius-item').forEach(item => {
        item.addEventListener('click', function() {
            document.querySelectorAll('.radius-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            currentRadius = parseFloat(this.dataset.radius);
            
            if (userLat) {
                updateRadiusCircle(userLat, userLng);
                loadVendors();
            }
        });
    });

    document.addEventListener('DOMContentLoaded', requestLocation);
</script>
@endsection
