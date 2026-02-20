@extends('layouts.app')

@section('title', 'Carte des Chauffeurs - ' . config('app.name'))

@section('content')
<div class="relative w-full h-[calc(100vh-80px)]">
    <div id="map" class="w-full h-full z-0"></div>
    
    <!-- Status Indicator Overlay -->
    <div class="absolute top-4 right-4 z-[400] bg-white dark:bg-gray-800 p-4 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 max-w-xs transition-all">
        <div class="flex items-center justify-between gap-4 mb-2">
            <h3 class="text-sm font-black uppercase tracking-widest text-gray-900 dark:text-white">Statut Chauffeur</h3>
            <div id="status-indicator" class="w-3 h-3 rounded-full bg-gray-400 transition-colors duration-300"></div>
        </div>
        <p id="status-text" class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-4">Vous êtes actuellement hors ligne.</p>
        
        @if(auth()->check() && auth()->user()->isDriver())
            <button id="toggle-online-btn" class="w-full py-3 px-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-black dark:hover:bg-gray-200 transition-all shadow-lg active:scale-95">
                Passer en ligne
            </button>
        @else
            <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-100 dark:border-red-900/30">
                <p class="text-[10px] font-bold text-red-600 dark:text-red-400">Compte chauffeur requis pour diffuser la position.</p>
            </div>
        @endif
    </div>
</div>

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

        // Icône personnalisée (Fallback si l'image moto n'existe pas encore)
        // Vous devez ajouter 'moto-icon.png' dans public/assets/images/
        const motoIcon = L.icon({
            iconUrl: '{{ asset("assets/images/moto-icon.png") }}', 
            iconSize: [40, 40],
            iconAnchor: [20, 20],
            popupAnchor: [0, -20],
            // Fallback to standard marker if image fails to load is tricky in Leaflet directly without error handling on Image object
        });
        
        // Simple default icon
        const defaultIcon = new L.Icon.Default();

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
                const marker = L.marker(latlng) // Utiliser l'icone par défaut pour l'instant pour éviter les erreurs 404
                    .addTo(map)
                    .bindPopup(`
                        <div class="text-center">
                            <h4 class="font-bold text-sm">${driverName}</h4>
                            <p class="text-xs text-green-600 font-bold">En ligne</p>
                        </div>
                    `);
                
                driverMarkers[driverId] = marker;
            }
        }

        // 4. Charger les chauffeurs déjà en ligne au démarrage
        fetch('/api/drivers/online')
            .then(res => res.json())
            .then(drivers => {
                console.log('Online drivers loaded:', drivers.length);
                drivers.forEach(driver => updateDriverOnMap(driver));
            })
            .catch(err => console.error('Error fetching drivers:', err));

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
