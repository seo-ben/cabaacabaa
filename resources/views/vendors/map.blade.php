@extends('layouts.app')

@section('title', 'Vendeurs Proches - Localisez vos restaurants')

@section('head')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Reset and base map styles */
    #map-container {
        position: relative;
        height: calc(100vh - 80px); /* Ajusté pour le header standard */
        width: 100%;
        overflow: hidden;
        background: #f3f4f6;
    }

    #map {
        height: 100%;
        width: 100%;
        z-index: 1;
    }

    /* Floating controls - Premium Look */
    .floating-ui {
        position: absolute;
        z-index: 1000;
        pointer-events: none;
        width: 100%;
        padding: 16px;
    }

    .top-ui {
        top: 0;
        display: flex;
        flex-direction: column;
        align-items: center; /* Centré au milieu en haut */
        gap: 12px;
    }

    .bottom-ui {
        bottom: 20px;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 12px;
    }

    /* Style des Cartes vitrées (Glassmorphism) */
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        pointer-events: auto;
        transition: all 0.3s ease;
    }

    /* Menu de sélection de rayon */
    .radius-selector {
        display: flex;
        padding: 4px;
        border-radius: 16px;
        overflow-x: auto;
        max-width: calc(100vw - 32px);
        scrollbar-width: none;
    }
    
    .radius-selector::-webkit-scrollbar { display: none; }

    .radius-item {
        padding: 10px 18px;
        border-radius: 12px;
        font-weight: 800;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.3s;
        color: #6b7280;
    }

    .radius-item:hover { color: #1f2937; }

    .radius-item.active {
        background: #f97316;
        color: white;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
    }

    /* Badge compteur de vendeurs */
    #stats-pill {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 50px; /* Forme pilule */
    }

    .pill-label {
        font-[Outfit] font-black text-[10px] uppercase tracking-[0.1em] text-gray-500;
    }

    .pill-value {
        font-bold text-orange-600 text-sm;
    }

    /* Boutons d'action (Position, Refresh) */
    .map-action-btn {
        width: 52px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #1f2937;
    }

    .map-action-btn:hover {
        background: #fff;
        transform: scale(1.05);
    }
    
    .map-action-btn:active { transform: scale(0.95); }

    .map-action-btn svg {
        width: 24px;
        height: 24px;
    }

    /* Style des Popups (Vendeurs sur la carte) */
    .leaflet-popup-content-wrapper { border-radius: 20px; padding: 0; overflow: hidden; }
    .leaflet-popup-content { margin: 0 !important; width: 280px !important; }
    .popup-card { padding: 0; }
    .popup-img { width: 100%; height: 140px; object-fit: cover; }
    .popup-body { padding: 15px; }
    .popup-title { font-weight: 800; font-size: 16px; margin-bottom: 4px; color: #111827; }
    .popup-meta { font-size: 12px; color: #6b7280; display: flex; align-items: center; gap: 4px; margin-bottom: 12px; }
    .popup-btn { display: block; width: 100%; padding: 10px; background: #f97316; color: white; text-align: center; border-radius: 10px; font-weight: 700; text-decoration: none; font-size: 13px; }

    /* Marqueurs */
    .custom-marker { background: #f97316; border: 3px solid white; border-radius: 50%; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; shadow-lg; font-size: 20px; color: white; }
    .user-marker { width: 24px; height: 24px; background: #3b82f6; border: 4px solid white; border-radius: 50%; shadow-lg; position: relative; }
    .user-marker::after {
        content: ''; position: absolute; inset: -8px; border-radius: 50%; background: rgba(59, 130, 246, 0.2); animation: pulse-ring 2s infinite;
    }
    @keyframes pulse-ring { 0% { transform: scale(0.7); opacity: 0.8; } 100% { transform: scale(1.5); opacity: 0; } }

    /* Loader Overlay */
    .loader-overlay { position: fixed; inset: 0; background: rgba(255, 255, 255, 0.8); backdrop-blur-sm; z-index: 2000; display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .spinner { width: 50px; height: 50px; border: 5px solid #f3f4f6; border-top: 5px solid #f97316; border-radius: 50%; animation: spin 1s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    @media (max-width: 640px) {
        #map-container { height: calc(100vh - 60px); }
        .top-ui { padding: 10px; }
        .radius-selector { border-radius: 10px; }
    }
</style>
@endsection

@section('content')
<div id="map-container">
    <!-- UI Layer -->
    <div class="floating-ui top-ui">
        <div class="radius-selector glass-card">
            <div class="radius-item active" data-radius="0.5">500m</div>
            <div class="radius-item" data-radius="1">1km</div>
            <div class="radius-item" data-radius="2">2km</div>
            <div class="radius-item" data-radius="5">5km</div>
            <div class="radius-item" data-radius="10">10km</div>
        </div>
        
        <div id="stats-pill" class="glass-card hidden">
            <span class="pill-label">Vendeurs à proximité</span>
            <span id="vendor-count" class="pill-value">0</span>
        </div>
    </div>

    <div class="floating-ui bottom-ui">
        <div class="glass-card map-action-btn" onclick="recenterMap()" title="Ma position">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="glass-card map-action-btn" onclick="requestLocation()" title="Rafraîchir">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </div>
    </div>

    <!-- Map div -->
    <div id="map"></div>

    <!-- Permissions Overlay -->
    <div id="permission-card" class="loader-overlay hidden">
        <div class="glass-card p-8 text-center max-w-sm mx-4">
            <div class="text-5xl mb-4">📍</div>
            <h2 class="text-XL font-black mb-2">Localisation requise</h2>
            <p class="text-gray-600 text-sm mb-6">Pour voir les vendeurs autour de vous, nous avons besoin de votre position GPS.</p>
            <button onclick="requestLocation()" class="w-full py-3 bg-orange-600 text-white rounded-xl font-bold shadow-lg shadow-orange-200">
                Autoriser l'accès
            </button>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loader" class="loader-overlay hidden">
        <div class="spinner mb-4"></div>
        <p class="text-xs font-black uppercase tracking-widest text-gray-500">Recherche de vendeurs...</p>
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
                    loadVendors();
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

    async function loadVendors() {
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
                pill.classList.remove('hidden');

                data.vendors.forEach(v => {
                    const icon = L.divIcon({
                        className: 'custom-marker-wrapper',
                        html: `<div class="custom-marker">🍽️</div>`,
                        iconSize: [44, 44],
                        iconAnchor: [22, 22]
                    });

                    const popupHtml = `
                        <div class="popup-card">
                            <img src="${v.image}" class="popup-img" onerror="this.src='/images/default-vendor.jpg'">
                            <div class="popup-body">
                                <div class="popup-title">${v.nom}</div>
                                <div class="popup-meta">
                                    <span>📍 ${v.distance_text}</span>
                                    <span>•</span>
                                    <span class="text-orange-600 font-bold">${v.category}</span>
                                </div>
                                <a href="${v.url}" class="popup-btn">Commander</a>
                            </div>
                        </div>
                    `;

                    const marker = L.marker([v.latitude, v.longitude], { icon: icon })
                        .addTo(map)
                        .bindPopup(popupHtml);
                    
                    vendorMarkers.push(marker);
                });

                if (data.count > 0 && isInitialLoad) {
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
