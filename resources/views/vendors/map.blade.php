@extends('layouts.app')

@section('title', 'Boutiques Proches - Localisez vos restaurants')

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
        background: rgba(255, 255, 255, 0.95);
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
        background: #dc2626; /* Red-600 */
        color: white;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
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
        transition: all 0.2s;
        pointer-events: auto;
        border: 1px solid #f1f5f9;
        color: #475569;
    }

    .map-btn:active { transform: scale(0.9); }
    .map-btn.active { background: #dc2626; color: white; border-color: #dc2626; }

    /* Custom Popup Styles */
    .leaflet-popup-content-wrapper {
        border-radius: 24px;
        padding: 0;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .leaflet-popup-content { margin: 0; width: 280px !important; }
    .leaflet-popup-tip-container { display: none; }

    /* Micro-animations */
    .loader-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(4px);
        z-index: 2000;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f4f6;
        border-top-color: #dc2626;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    /* RTL / LTR fix for popup close button */
    .leaflet-container a.leaflet-popup-close-button {
        padding: 12px;
        color: #94a3b8;
        font-size: 20px;
    }
</style>
@endsection

@section('content')
<div id="map-container">
    <div id="map"></div>

    <!-- Status & Filters Overlay -->
    <div class="status-overlay">
        <div class="glass-panel text-slate-900 border-none shadow-2xl">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-xs font-black text-gray-900 uppercase tracking-widest">Boutiques à proximité</h1>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter mt-0.5">
                        <span id="vendor-count" class="text-red-600 font-black">0</span> Établissements trouvés
                    </p>
                </div>
                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-red-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Rayon de recherche</label>
                <div class="radius-selector">
                    <div class="radius-item active" data-radius="0.5">500m</div>
                    <div class="radius-item" data-radius="1">1km</div>
                    <div class="radius-item" data-radius="2.5">2.5km</div>
                    <div class="radius-item" data-radius="5">5km</div>
                    <div class="radius-item" data-radius="10">10km</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-group">
        <button id="satellite-btn" onclick="toggleSatellite()" class="map-btn" title="Mode Satellite">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2 2 2 0 012 2v.656c0 .53.21 1.039.586 1.414l.344.344m-7.377-12.81c.563-.544 1.348-.87 2.21-.87 1.768 0 3.2 1.432 3.2 3.2 0 .862-.326 1.647-.87 2.21l-4.54 4.54a2.21 2.21 0 01-3.126 0 2.21 2.21 0 010-3.126l4.54-4.54z"/></svg>
        </button>
        <button onclick="recenterMap()" class="map-btn" title="Ma position">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3c-.49-4.39-4.04-7.94-8.43-8.42V1c0-.55-.45-1-1-1s-1 .45-1 1v1.58C6.11 3.06 2.56 6.61 2.07 11H.5c-.55 0-1 .45-1 1s.45 1 1 1h1.57c.49 4.39 4.04 7.94 8.43 8.42V23c0 .55.45 1 1 1s1-.45 1-1v-1.58c4.39-.48 7.94-4.03 8.43-8.42H23.5c.55 0 1-.45 1-1s-.45-1-1-1h-1.56z"/></svg>
        </button>
    </div>

    <!-- Permission Panel -->
    <div id="permission-card" class="loader-overlay hidden">
        <div class="glass-panel text-center max-w-xs mx-4">
            <div class="w-16 h-16 bg-red-50 rounded-3xl flex items-center justify-center text-red-600 mx-auto mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <h2 class="text-xl font-black mb-2">Localisation requise</h2>
            <p class="text-gray-600 text-sm mb-6 font-medium leading-relaxed">Pour voir les boutiques autour de vous, nous avons besoin de votre position GPS.</p>
            <button onclick="requestLocation()" class="w-full py-4 bg-red-600 text-white rounded-2xl font-black uppercase tracking-widest text-[11px] shadow-xl shadow-red-200 active:scale-95 transition-all">
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
    let roadLayer, satelliteLayer;
    let isSatellite = false;

    function initMap(lat, lng) {
        if (!map) {
            map = L.map('map', {
                zoomControl: false,
                tap: false
            }).setView([lat, lng], 17);

            // Layer Routes - CartoDB Voyager (Much clearer and shows houses/spaces better)
            roadLayer = L.tileLayer('https://{s}.tile.basemaps.cartocdn.com/voyager_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap',
                maxZoom: 19
            });

            // Layer Satellite
            satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri',
                maxZoom: 19
            });

            roadLayer.addTo(map);
            L.control.zoom({ position: 'bottomleft' }).addTo(map);
        } else {
            map.setView([lat, lng]);
        }

        updateUserMarker(lat, lng);
        updateRadiusCircle(lat, lng);
    }

    function toggleSatellite() {
        if (!map) return;
        isSatellite = !isSatellite;
        const btn = document.getElementById('satellite-btn');
        
        if (isSatellite) {
            map.removeLayer(roadLayer);
            satelliteLayer.addTo(map);
            btn.classList.add('bg-red-600', 'text-white');
            if (window.showToast) showToast("Mode satellite activé", "info");
        } else {
            map.removeLayer(satelliteLayer);
            roadLayer.addTo(map);
            btn.classList.remove('bg-red-600', 'text-white');
            if (window.showToast) showToast("Mode plan activé", "info");
        }
    }

    function updateUserMarker(lat, lng) {
        const userIcon = L.divIcon({
            className: 'user-marker-container',
            html: '<div class="w-5 h-5 bg-red-600 border-2 border-white rounded-full shadow-lg"></div>',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
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
            color: '#ef4444',
            fillColor: '#ef4444',
            fillOpacity: 0.05,
            weight: 1,
            dashArray: '5, 5',
            radius: currentRadius * 1000
        }).addTo(map);
    }

    function requestLocation() {
        const loader = document.getElementById('loader');
        const permissionCard = document.getElementById('permission-card');
        
        loader.classList.remove('hidden');
        permissionCard.classList.add('hidden');

        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    userLat = pos.coords.latitude;
                    userLng = pos.coords.longitude;
                    initMap(userLat, userLng);
                    loadVendors(true);
                    loader.classList.add('hidden');
                },
                (err) => {
                    loader.classList.add('hidden');
                    if (isInitialLoad) permissionCard.classList.remove('hidden');
                    if (window.showToast) showToast("Localisation impossible.", "error");
                },
                { enableHighAccuracy: true, timeout: 8000, maximumAge: 30000 }
            );
        }
        isInitialLoad = false;
    }

    async function loadVendors(shouldFitBounds = false) {
        const countDisplay = document.getElementById('vendor-count');
        try {
            const response = await fetch('{{ route('vendors.nearby') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ lat: userLat, lng: userLng, radius: currentRadius })
            });

            const data = await response.json();
            if (data.success) {
                vendorMarkers.forEach(m => map.removeLayer(m));
                vendorMarkers = [];
                countDisplay.textContent = data.count;

                data.vendors.forEach(v => {
                    const icon = L.divIcon({
                        className: 'vendor-marker-wrapper',
                        html: `<div class="bg-red-600 w-9 h-9 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white text-lg">📍</div>`,
                        iconSize: [36, 36],
                        iconAnchor: [18, 36]
                    });

                    const products = v.products ? v.products.slice(0, 6) : [];
                    const productsHtml = products.map(p => `<span class="px-2 py-0.5 bg-gray-50 dark:bg-gray-800 text-[8px] font-bold text-gray-500 rounded uppercase tracking-tighter">${p}</span>`).join('');

                    const popupHtml = `
                        <div class="p-4 w-[280px]">
                            <a href="${v.url}" class="flex gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[9px] font-black text-red-600 uppercase tracking-widest">${v.category}</span>
                                        <span class="text-[9px] font-bold text-gray-400">📍 ${v.distance_text}</span>
                                    </div>
                                    <h4 class="text-sm font-black text-gray-900 truncate mb-2 uppercase">${v.nom}</h4>
                                    <div class="flex flex-wrap gap-1 mb-4">
                                        ${productsHtml}
                                    </div>
                                    <span class="text-[10px] font-black text-red-600 uppercase tracking-widest border-b-2 border-red-600 pb-0.5">Visiter la boutique</span>
                                </div>
                                <div class="w-16 h-16 rounded-2xl overflow-hidden bg-gray-100 shrink-0 border border-gray-100 shadow-sm">
                                    <img src="${v.image_full || '/images/default-vendor.jpg'}" class="w-full h-full object-cover">
                                </div>
                            </a>
                        </div>
                    `;

                    const marker = L.marker([v.latitude, v.longitude], { icon: icon }).addTo(map).bindPopup(popupHtml);
                    vendorMarkers.push(marker);
                });

                if (data.count > 0 && shouldFitBounds) {
                    const group = new L.featureGroup([...vendorMarkers, userMarker]);
                    map.fitBounds(group.getBounds().pad(0.2));
                }
            }
        } catch (e) { console.error(e); }
    }

    function recenterMap() {
        if (userLat && userLng) map.flyTo([userLat, userLng], 16); else requestLocation();
    }

    document.querySelectorAll('.radius-item').forEach(item => {
        item.addEventListener('click', function() {
            document.querySelectorAll('.radius-item').forEach(i => i.classList.remove('active', 'bg-red-600', 'text-white'));
            this.classList.add('active', 'bg-red-600', 'text-white');
            currentRadius = parseFloat(this.dataset.radius);
            if (userLat) { updateRadiusCircle(userLat, userLng); loadVendors(); }
        });
    });

    document.addEventListener('DOMContentLoaded', requestLocation);
</script>
@endsection
