@extends('layouts.app')

@section('title', 'Carte des Chauffeurs - ' . config('app.name'))

@section('content')
<div class="relative w-full h-[calc(100vh-80px)]" id="map-parent">
    <div id="map" class="w-full h-full z-0"></div>
    
    <!-- Status Indicator Overlay -->
    <div class="absolute top-4 right-4 left-4 sm:left-auto z-[400] bg-white/90 dark:bg-gray-800/90 backdrop-blur-md p-4 rounded-2xl shadow-xl border border-white/20 dark:border-gray-700 sm:max-w-xs transition-all animate-in slide-in-from-top-4 duration-500">
        <div class="flex items-center justify-between gap-4 mb-3">
            <div class="flex flex-col">
                <h3 class="text-[10px] font-black uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400">Statut Chauffeur</h3>
                <div id="status-text" class="text-xs font-black text-gray-900 dark:text-white mt-0.5">Hors ligne</div>
            </div>
            <div id="status-indicator" class="w-2.5 h-2.5 rounded-full bg-gray-400 shadow-sm transition-all duration-300"></div>
        </div>
        
        @if(auth()->check() && auth()->user()->isDriver())
            <button id="toggle-online-btn" class="w-full py-3 px-6 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-black/10 active:scale-95 transition-all">
                Passer en ligne
            </button>
        @else
            <div class="px-3 py-2 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-100 dark:border-red-900/30 flex items-center gap-2">
                <svg class="w-3 h-3 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <p class="text-[9px] font-black uppercase tracking-tight text-red-600 dark:text-red-400">Diffusion réservée aux livreurs</p>
            </div>
        @endif
    </div>
</div>

<style>
    @media (max-width: 1023px) {
        #map-parent { 
            height: calc(100vh - 145px); /* Header + Bottom Nav Safe area */
            margin-bottom: 65px;
        }
    }
    .leaflet-control-zoom { border: none !important; border-radius: 12px !important; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; }
    .leaflet-control-zoom-in, .leaflet-control-zoom-out { background-color: white !important; color: #111827 !important; font-weight: bold !important; border-bottom: 1px solid #f3f4f6 !important; }
</style>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Initialiser la carte sur Lomé (Togo) ou la position actuelle si possible
        const map = L.map('map').setView([6.1375, 1.2123], 13);

        // Tiles OpenStreetMap (gratuit)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // 2. Stocker les markers des chauffeurs
        const driverMarkers = {};

        // Icônes personnalisées
        const driverIcon = L.divIcon({
            className: 'custom-driver-marker',
            html: `
                <div class="relative group">
                    <div class="w-10 h-10 bg-gray-900 border-2 border-white rounded-xl shadow-lg flex items-center justify-center transform group-hover:scale-110 transition-all">
                        <span class="text-xl">🏍️</span>
                    </div>
                    <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-4 h-1.5 bg-black/20 blur-[1px] rounded-full"></div>
                </div>
            `,
            iconSize: [40, 40],
            iconAnchor: [20, 20],
            popupAnchor: [0, -20]
        });

        const vendorIcon = L.divIcon({
            className: 'custom-vendor-marker',
            html: `
                <div class="relative group">
                    <div class="w-10 h-10 bg-red-600 border-2 border-white rounded-xl shadow-lg flex items-center justify-center transform group-hover:scale-110 transition-all">
                        <span class="text-xl">🏢</span>
                    </div>
                </div>
            `,
            iconSize: [40, 40],
            iconAnchor: [20, 20],
            popupAnchor: [0, -20]
        });

        // 3. Écouter les positions en temps réel via WebSocket (Echo)
        if (window.Echo) {
            console.log('Echo initialized, listening for drivers...');
            window.Echo.channel('drivers-map')
                .listen('.location.updated', (data) => {
                    console.log('📍 Driver Update:', data);
                    updateDriverOnMap(data);
                });
        } else {
            console.error('Laravel Echo is not initialized. Make sure app.js is loaded.');
        }

        function updateDriverOnMap(data) {
            const { driverId, latitude, longitude, driverName } = data;
            
            // Validate coordinates
            if (!latitude || !longitude) return;
            
            const latlng = [parseFloat(latitude), parseFloat(longitude)];

            if (driverMarkers[driverId]) {
                // Déplacer le marker existant avec animation (si supporté par plugins, sinon setLatLng)
                driverMarkers[driverId].setLatLng(latlng);
                driverMarkers[driverId].getPopup().setContent(`
                    <div class="text-center">
                        <h4 class="font-bold text-sm">${driverName}</h4>
                        <p class="text-xs text-gray-500">À l'instant</p>
                    </div>
                `);
            } else {
                // Créer un nouveau marker
                const marker = L.marker(latlng, { icon: driverIcon })
                    .addTo(map)
                    .bindPopup(`
                        <div class="text-center p-2">
                            <h4 class="font-black text-sm text-gray-900 mb-1">${driverName}</h4>
                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase tracking-widest">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                En ligne
                            </div>
                        </div>
                    `);
                
                driverMarkers[driverId] = marker;
            }
        }

        // 3b. Fonction pour afficher les boutiques
        function addVendorToMap(vendor) {
            const latlng = [parseFloat(vendor.latitude), parseFloat(vendor.longitude)];
            
            L.marker(latlng, { icon: vendorIcon })
                .addTo(map)
                .bindPopup(`
                    <div class="text-center p-2">
                        @{{#if vendor.image}}
                            <img src="${vendor.image}" class="w-16 h-16 rounded-lg object-cover mx-auto mb-2 border border-gray-100 shadow-sm">
                        @{{/if}}
                        <h4 class="font-black text-sm text-gray-900 mb-1">${vendor.name}</h4>
                        <a href="${vendor.url}" class="text-[10px] font-black text-red-600 hover:text-red-700 uppercase tracking-widest">Voir la boutique</a>
                    </div>
                `);
        }

        // 4. Charger les données au démarrage
        // Chauffeurs
        fetch('/api/drivers/online')
            .then(res => res.json())
            .then(drivers => {
                drivers.forEach(driver => updateDriverOnMap(driver));
            });

        // Boutiques
        fetch('/api/vendors/active')
            .then(res => res.json())
            .then(vendors => {
                vendors.forEach(vendor => addVendorToMap(vendor));
            })
            .catch(err => console.error('Error fetching vendors:', err));

        // 5. Logic pour le chauffeur actuel
        @if(auth()->check() && auth()->user()->isDriver())
            const statusIndicator = document.getElementById('status-indicator');
            const statusText = document.getElementById('status-text');
            const toggleBtn = document.getElementById('toggle-online-btn');
            
            let isOnline = false;
            let watchId = null;

            toggleBtn.addEventListener('click', () => {
                if (!isOnline) {
                    startTracking();
                } else {
                    stopTracking();
                }
            });

            function startTracking() {
                if (!navigator.geolocation) {
                    alert("La géolocalisation n'est pas supportée par votre navigateur.");
                    return;
                }

                isOnline = true;
                updateUI(true);

                // Initial position update
                navigator.geolocation.getCurrentPosition(sendLocation, handleError);

                // Continuous tracking
                watchId = navigator.geolocation.watchPosition(
                    sendLocation,
                    handleError,
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            }

            function sendLocation(position) {
                const { latitude, longitude } = position.coords;
                // console.log('My position:', latitude, longitude);

                // Center map on me initially or follow me
                // map.setView([latitude, longitude], 15); 

                fetch('/api/driver/location', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ latitude, longitude })
                }).catch(e => console.error('Send location error:', e));
            }

            function handleError(error) {
                console.error('Geolocation error:', error);
                // Don't stop tracking immediately on minor temporary errors, but warn if persistent
                if (error.code === 1) { // PERMISSION_DENIED
                    alert('Permission refusée. Impossible de vous localiser.');
                    stopTracking();
                }
            }

            function stopTracking() {
                isOnline = false;
                updateUI(false);
                if (watchId !== null) {
                    navigator.geolocation.clearWatch(watchId);
                    watchId = null;
                }
            }

            function updateUI(online) {
                if (online) {
                    statusIndicator.classList.remove('bg-gray-400');
                    statusIndicator.classList.add('bg-green-500', 'animate-pulse'); // Pulse Red for "Live/Recording" feel or Green for "Online"
                    statusText.textContent = 'En ligne (Votre position est diffusée)';
                    statusText.classList.add('text-green-600', 'dark:text-green-400');
                    statusText.classList.remove('text-gray-500', 'dark:text-gray-400');
                    
                    toggleBtn.textContent = 'Passer hors ligne';
                    toggleBtn.className = "w-full py-3 px-4 bg-red-600 text-white rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-red-700 transition-all shadow-lg shadow-red-200 active:scale-95";
                } else {
                    statusIndicator.classList.remove('bg-green-500', 'animate-pulse');
                    statusIndicator.classList.add('bg-gray-400');
                    statusText.textContent = 'Vous êtes hors ligne.';
                    statusText.classList.remove('text-green-600', 'dark:text-green-400');
                    statusText.classList.add('text-gray-500', 'dark:text-gray-400');

                    toggleBtn.textContent = 'Passer en ligne';
                    toggleBtn.className = "w-full py-3 px-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-black dark:hover:bg-gray-200 transition-all shadow-lg active:scale-95";
                }
            }
        @endif
    });
</script>
@endsection
