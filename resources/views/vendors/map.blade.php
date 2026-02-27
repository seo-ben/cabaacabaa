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

    @media (max-width: 1023px) {
        #map-container { 
            height: calc(100vh - 145px); /* Header + Bottom Nav Safe area */
            margin-bottom: 65px;
        }
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
        <button onclick="recenterMap()" class="map-btn" title="Ma position">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3c-.49-4.39-4.04-7.94-8.43-8.42V1c0-.55-.45-1-1-1s-1 .45-1 1v1.58C6.11 3.06 2.56 6.61 2.07 11H.5c-.55 0-1 .45-1 1s.45 1 1 1h1.57c.49 4.39 4.04 7.94 8.43 8.42V23c0 .55.45 1 1 1s1-.45 1-1v-1.58c4.39-.48 7.94-4.03 8.43-8.42H23.5c.55 0 1-.45 1-1s-.45-1-1-1h-1.56z"/></svg>
        </button>
    </div>

    <!-- Moving Loader to a small indicator -->
    <div id="loader" class="absolute top-24 left-1/2 -translate-x-1/2 z-[2000] hidden">
        <div class="bg-gray-900/80 backdrop-blur-md px-6 py-3 rounded-2xl flex items-center gap-3 shadow-2xl border border-white/10">
            <div class="spinner w-4 h-4 border-2 border-white/20 border-top-white animate-spin rounded-full"></div>
            <p class="text-[10px] font-black uppercase tracking-widest text-white">Recherche...</p>
        </div>
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

    // Fallback toast function
    const showToast = (message, type = 'info') => {
        if (window.showToast) {
            window.showToast(message, type);
        } else {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-24 left-1/2 -translate-x-1/2 z-[5000] px-6 py-3 rounded-2xl text-white text-[10px] font-black uppercase tracking-widest shadow-2xl animate-in fade-in slide-in-from-bottom-4 ${type === 'error' ? 'bg-red-600' : 'bg-slate-900'}`;
            toast.innerText = message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('animate-out', 'fade-out', 'slide-out-to-bottom-4');
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }
    };

    function initMap(lat, lng, zoom = 17) {
        if (!map) {
            map = L.map('map', {
                zoomControl: false,
                tap: false
            }).setView([lat, lng], zoom);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap &copy; CartoDB',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);

            L.control.zoom({ position: 'bottomleft' }).addTo(map);
            
            // Fix for map size
            setTimeout(() => map.invalidateSize(), 100);
        } else {
            map.setView([lat, lng], zoom);
        }

        updateUserMarker(lat, lng);
        updateRadiusCircle(lat, lng);
    }


    function updateUserMarker(lat, lng) {
        const userIcon = L.divIcon({
            className: 'user-marker-container',
            html: '<div class="w-6 h-6 bg-blue-600 border-4 border-white rounded-full shadow-xl"></div>',
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
            color: '#dc2626',
            fillColor: '#dc2626',
            fillOpacity: 0.08,
            weight: 2,
            dashArray: '5, 10',
            radius: currentRadius * 1000
        }).addTo(map);
    }

    function requestLocation() {
        const loader = document.getElementById('loader');
        
        loader.classList.remove('hidden');

        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    userLat = pos.coords.latitude;
                    userLng = pos.coords.longitude;
                    
                    if (!map) {
                        initMap(userLat, userLng);
                    } else {
                        map.setView([userLat, userLng], 17);
                        updateUserMarker(userLat, userLng);
                        updateRadiusCircle(userLat, userLng);
                    }
                    
                    loadVendors(true).finally(() => {
                        loader.classList.add('hidden');
                    });
                },
                (err) => {
                    loader.classList.add('hidden');
                    console.warn("Geolocation error:", err);
                    
                    // Already initialized if we follow the new DOMContentLoaded logic
                    // Just fallback to default search if needed
                    if (!userLat) {
                        userLat = 6.1319; // Lomé
                        userLng = 1.2227;
                    }
                    loadVendors(false);
                    showToast("Utilisation de la position par défaut.", "info");
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 30000 }
            );
        } else {
            loader.classList.add('hidden');
            showToast("Géolocalisation non supportée.", "error");
            if (!userLat) {
                userLat = 6.1319;
                userLng = 1.2227;
            }
            loadVendors(false);
        }
    }

    async function loadVendors(shouldFitBounds = false) {
        const countDisplay = document.getElementById('vendor-count');
        if (!userLat || !userLng) return;

        try {
            const response = await fetch('{{ route('vendors.nearby') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ lat: userLat, lng: userLng, radius: currentRadius })
            });

            if (!response.ok) throw new Error("Server error");

            const data = await response.json();
            if (data.success) {
                vendorMarkers.forEach(m => map.removeLayer(m));
                vendorMarkers = [];
                countDisplay.textContent = data.count;

                data.vendors.forEach(v => {
                    const icon = L.divIcon({
                        className: 'vendor-marker-wrapper',
                        html: `
                            <div class="relative group">
                                <div class="w-10 h-10 bg-red-600 border-2 border-white rounded-xl shadow-xl flex items-center justify-center transform group-hover:scale-110 transition-all">
                                    <span class="text-xl">🛍️</span>
                                </div>
                                <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-4 h-1.5 bg-black/20 blur-[1px] rounded-full"></div>
                            </div>
                        `,
                        iconSize: [40, 40],
                        iconAnchor: [20, 20],
                        popupAnchor: [0, -20]
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

                if (data.count > 0 && shouldFitBounds && map) {
                    const group = new L.featureGroup([...vendorMarkers, userMarker]);
                    map.fitBounds(group.getBounds().pad(0.2));
                }
            }
        } catch (e) { 
            console.error(e);
            showToast("Erreur lors de la récupération des boutiques.", "error");
        }
    }

    function recenterMap() {
        if (userLat && userLng) map.flyTo([userLat, userLng], 16); 
        requestLocation();
    }

    document.querySelectorAll('.radius-item').forEach(item => {
        item.addEventListener('click', function() {
            document.querySelectorAll('.radius-item').forEach(i => i.classList.remove('active', 'bg-red-600', 'text-white'));
            this.classList.add('active', 'bg-red-600', 'text-white');
            currentRadius = parseFloat(this.dataset.radius);
            if (userLat) { updateRadiusCircle(userLat, userLng); loadVendors(); }
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        // Initialiser avec Lomé par défaut immédiatement
        userLat = 6.1319;
        userLng = 1.2227;
        initMap(userLat, userLng, 13);
        
        // Puis tenter la géolocalisation
        requestLocation();
    });
</script>
@endsection
