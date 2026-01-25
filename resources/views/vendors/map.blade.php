@extends('layouts.app')

@section('title', 'Vendeurs Proches - Trouvez les restaurants autour de vous')

@section('head')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: calc(100vh - 120px);
        width: 100%;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    }

    .vendor-popup {
        min-width: 280px;
    }

    .vendor-popup-image {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 12px;
    }

    .vendor-popup-title {
        font-size: 16px;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 6px;
    }

    .vendor-popup-specialty {
        font-size: 12px;
        color: #f97316;
        font-weight: 700;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .vendor-popup-distance {
        font-size: 11px;
        color: #6b7280;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .vendor-popup-rating {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        color: #fbbf24;
        margin-bottom: 12px;
    }

    .vendor-popup-btn {
        display: inline-block;
        width: 100%;
        padding: 10px 16px;
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white;
        text-align: center;
        border-radius: 8px;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-decoration: none;
        transition: all 0.3s;
    }

    .vendor-popup-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(249, 115, 22, 0.3);
    }

    .leaflet-popup-content-wrapper {
        border-radius: 12px;
        padding: 0;
    }

    .leaflet-popup-content {
        margin: 16px;
    }

    /* Custom marker icon */
    .custom-marker {
        background: #f97316;
        border: 3px solid white;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4);
        font-size: 20px;
    }

    .user-marker {
        background: #3b82f6;
        border: 3px solid white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        box-shadow: 0 0 0 8px rgba(59, 130, 246, 0.2);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 0 0 8px rgba(59, 130, 246, 0.2);
        }
        50% {
            box-shadow: 0 0 0 15px rgba(59, 130, 246, 0.1);
        }
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.95);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(10px);
    }

    .loading-spinner {
        width: 60px;
        height: 60px;
        border: 4px solid #f3f4f6;
        border-top: 4px solid #f97316;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }

    .radius-control {
        display: flex;
        gap: 10px;
        margin-top: 16px;
    }

    .radius-btn {
        flex: 1;
        padding: 10px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        background: white;
        font-weight: 700;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .radius-btn.active {
        border-color: #f97316;
        background: #fff7ed;
        color: #f97316;
    }

    .radius-btn:hover {
        border-color: #f97316;
    }

    /* Tooltip styling */
    .leaflet-tooltip-top:before {
        border-top-color: #fff;
    }
    .vendor-label {
        background: transparent;
        border: none;
        box-shadow: none;
        padding: 0;
    }
    .custom-marker {
        transition: transform 0.3s;
    }
    .custom-marker:hover {
        transform: scale(1.2) rotate(10deg);
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-orange-50 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950 py-24 transition-colors duration-300">
    <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14">
        
        <!-- Header -->
        <div class="mb-10 text-center">
            <h1 class="text-5xl font-black text-gray-900 dark:text-white tracking-tighter mb-4">
                📍 Vendeurs Proches
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 font-medium max-w-2xl mx-auto">
                Découvrez les restaurants et boutiques autour de vous grâce à la géolocalisation
            </p>
        </div>

        <!-- Stats Card -->
        <div class="stats-card" id="stats-card" style="display: none;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-widest text-gray-400 mb-2">Vendeurs trouvés</p>
                    <p class="text-4xl font-black text-gray-900 dark:text-white" id="vendor-count">0</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-black uppercase tracking-widest text-gray-400 mb-2">Rayon de recherche</p>
                    <p class="text-2xl font-black text-orange-600" id="radius-display">200 m</p>
                </div>
            </div>

            <!-- Radius Control -->
            <div class="radius-control">
                <button class="radius-btn active" data-radius="0.2" data-text="200 m">200 m</button>
                <button class="radius-btn" data-radius="0.5" data-text="500 m">500 m</button>
                <button class="radius-btn" data-radius="1" data-text="1 km">1 km</button>
                <button class="radius-btn" data-radius="2" data-text="2 km">2 km</button>
                <button class="radius-btn" data-radius="5" data-text="5 km">5 km</button>
            </div>
        </div>

        <!-- Map Container -->
        <div id="map"></div>

        <!-- No Location Permission -->
        <div id="no-location" class="text-center py-20 bg-white dark:bg-gray-900 rounded-2xl shadow-lg" style="display: none;">
            <div class="max-w-md mx-auto">
                <div class="text-6xl mb-6">📍</div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-4">Géolocalisation requise</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Veuillez autoriser l'accès à votre position pour voir les vendeurs proches de vous.
                </p>
                <button onclick="requestLocation()" class="px-8 py-4 bg-orange-600 text-white rounded-xl font-black text-sm uppercase tracking-widest hover:bg-orange-700 transition-all shadow-xl">
                    Activer la géolocalisation
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="loading-overlay">
    <div class="text-center">
        <div class="loading-spinner mx-auto mb-4"></div>
        <p class="text-sm font-black uppercase tracking-widest text-gray-600">Recherche en cours...</p>
    </div>
</div>
@endsection

@section('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let map;
let userMarker;
let vendorMarkers = [];
let currentRadius = 0.2; // 200 mètres par défaut
let userLat, userLng;

// Initialiser la carte
function initMap(lat, lng) {
    // Créer la carte centrée sur la position de l'utilisateur
    map = L.map('map').setView([lat, lng], 16);

    // Ajouter les tuiles OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);

    // Ajouter le marqueur de l'utilisateur
    const userIcon = L.divIcon({
        className: 'user-marker',
        iconSize: [20, 20],
    });

    userMarker = L.marker([lat, lng], { icon: userIcon })
        .addTo(map)
        .bindPopup('<div class="text-center font-bold">📍 Vous êtes ici</div>');

    // Ajouter un cercle pour visualiser le rayon
    L.circle([lat, lng], {
        color: '#f97316',
        fillColor: '#f97316',
        fillOpacity: 0.1,
        radius: currentRadius * 1000 // Convertir km en mètres
    }).addTo(map);
}

// Charger les vendeurs proches
async function loadNearbyVendors(lat, lng, radius = 0.2) {
    try {
        const response = await fetch('{{ route('vendors.nearby') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                lat: lat,
                lng: lng,
                radius: radius
            })
        });

        const data = await response.json();

        if (data.success) {
            // Mettre à jour les stats
            document.getElementById('vendor-count').textContent = data.count;
            document.getElementById('radius-display').textContent = data.radius_text;
            document.getElementById('stats-card').style.display = 'block';

            // Supprimer les anciens marqueurs de vendeurs
            vendorMarkers.forEach(marker => map.removeLayer(marker));
            vendorMarkers = [];

            // Ajouter les nouveaux marqueurs
            data.vendors.forEach(vendor => {
                const vendorIcon = L.divIcon({
                    className: 'custom-marker',
                    html: '🍽️',
                    iconSize: [40, 40],
                });

                const popupContent = `
                    <div class="vendor-popup">
                        <img src="${vendor.image}" alt="${vendor.nom}" class="vendor-popup-image" onerror="this.src='/images/default-vendor.jpg'">
                        <div class="vendor-popup-title">${vendor.nom}</div>
                        <div class="vendor-popup-specialty">🍕 ${vendor.specialties_text}</div>
                        <div class="vendor-popup-distance">📍 ${vendor.distance_text} de vous</div>
                        ${vendor.note_moyenne > 0 ? `
                            <div class="vendor-popup-rating">
                                ⭐ ${vendor.note_moyenne.toFixed(1)} (${vendor.nombre_avis} avis)
                            </div>
                        ` : ''}
                        <a href="${vendor.url}" class="vendor-popup-btn">
                            Voir le menu →
                        </a>
                    </div>
                `;

                const marker = L.marker([vendor.latitude, vendor.longitude], { icon: vendorIcon })
                    .addTo(map)
                    .bindPopup(popupContent, {
                        maxWidth: 300,
                        className: 'vendor-popup-container'
                    })
                    .bindTooltip(`<div class="font-black text-[10px] uppercase tracking-tighter text-orange-600 bg-white px-2 py-1 rounded-lg shadow-sm border border-orange-100">${vendor.specialties_text}</div>`, {
                        permanent: true,
                        direction: 'top',
                        className: 'vendor-label',
                        offset: [0, -20]
                    });

                vendorMarkers.push(marker);
            });

            // Ajuster la vue pour inclure tous les marqueurs
            if (data.count > 0) {
                const bounds = L.latLngBounds(
                    data.vendors.map(v => [v.latitude, v.longitude])
                );
                bounds.extend([lat, lng]); // Inclure la position de l'utilisateur
                map.fitBounds(bounds, { padding: [50, 50] });
            }

            // Afficher un message si aucun vendeur trouvé
            if (data.count === 0) {
                window.showToast('Aucun vendeur trouvé dans ce rayon. Essayez d\'augmenter la distance.', 'info');
            }
        }
    } catch (error) {
        console.error('Erreur lors du chargement des vendeurs:', error);
        window.showToast('Erreur lors du chargement des vendeurs proches.', 'error');
    } finally {
        document.getElementById('loading-overlay').style.display = 'none';
    }
}

// Demander la géolocalisation
function requestLocation() {
    if ("geolocation" in navigator) {
        document.getElementById('loading-overlay').style.display = 'flex';
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                userLat = position.coords.latitude;
                userLng = position.coords.longitude;

                // Masquer le message "no location"
                document.getElementById('no-location').style.display = 'none';
                document.getElementById('map').style.display = 'block';

                // Initialiser la carte
                initMap(userLat, userLng);

                // Charger les vendeurs proches
                loadNearbyVendors(userLat, userLng, currentRadius);
            },
            function(error) {
                document.getElementById('loading-overlay').style.display = 'none';
                document.getElementById('no-location').style.display = 'block';
                document.getElementById('map').style.display = 'none';
                
                let errorMessage = 'Impossible d\'obtenir votre position.';
                if (error.code === error.PERMISSION_DENIED) {
                    errorMessage = 'Vous avez refusé l\'accès à votre position. Veuillez l\'autoriser dans les paramètres de votre navigateur.';
                }
                window.showToast(errorMessage, 'error');
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    } else {
        window.showToast('La géolocalisation n\'est pas supportée par votre navigateur.', 'error');
        document.getElementById('no-location').style.display = 'block';
        document.getElementById('loading-overlay').style.display = 'none';
    }
}

// Gestion des boutons de rayon
document.querySelectorAll('.radius-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Retirer la classe active de tous les boutons
        document.querySelectorAll('.radius-btn').forEach(b => b.classList.remove('active'));
        
        // Ajouter la classe active au bouton cliqué
        this.classList.add('active');
        
        // Mettre à jour le rayon
        currentRadius = parseFloat(this.dataset.radius);
        
        // Recharger les vendeurs avec le nouveau rayon
        if (userLat && userLng) {
            document.getElementById('loading-overlay').style.display = 'flex';
            
            // Supprimer l'ancien cercle et en créer un nouveau
            map.eachLayer(layer => {
                if (layer instanceof L.Circle) {
                    map.removeLayer(layer);
                }
            });
            
            L.circle([userLat, userLng], {
                color: '#f97316',
                fillColor: '#f97316',
                fillOpacity: 0.1,
                radius: currentRadius * 1000
            }).addTo(map);
            
            loadNearbyVendors(userLat, userLng, currentRadius);
        }
    });
});

// Démarrer automatiquement au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    requestLocation();
});
</script>
@endsection
