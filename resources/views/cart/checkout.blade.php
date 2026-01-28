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
                        <div id="delivery-details" class="md:col-span-2 pt-6 border-t border-gray-50 dark:border-gray-800 space-y-4 hidden">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xs font-black uppercase tracking-widest text-gray-900 dark:text-white">Adresse de livraison</h3>
                                <button type="button" onclick="detectLocation()" class="text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    Ma position actuelle
                                </button>
                            </div>
                            <input type="text" name="adresse_livraison" id="adresse-livraison"
                                   class="w-full px-8 py-5 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-orange-500 focus:bg-white dark:focus:bg-gray-700 rounded-xl text-sm font-bold text-gray-900 dark:text-white transition-all outline-none placeholder-gray-400 dark:placeholder-gray-600"
                                   placeholder="Quartier, Rue, Maison...">
                            
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

@section('scripts')
<script>
    let subTotal = {{ $total }};
    let currentFee = 0;

    function toggleDelivery(show) {
        const details = document.getElementById('delivery-details');
        const summaryLine = document.getElementById('delivery-fee-line');
        if (show) {
            details.classList.remove('hidden');
            summaryLine.classList.remove('hidden');
            detectLocation(); // Auto detect when delivery is selected
        } else {
            details.classList.add('hidden');
            summaryLine.classList.add('hidden');
            currentFee = 0;
            updateTotal();
        }
    }

    function detectLocation() {
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('user-lat').value = position.coords.latitude;
                document.getElementById('user-lng').value = position.coords.longitude;
                calculateFee(position.coords.latitude, position.coords.longitude);
            });
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
                window.showToast("Désolé, votre adresse est trop éloignée (" + data.distance + " km) pour une livraison. La distance maximale est de " + data.max_distance + " km.", 'error');
                document.querySelector('input[name="type_recuperation"][value="emporter"]').checked = true;
                toggleDelivery(false);
                return;
            }

            currentFee = data.fee;
            document.getElementById('distance-info').classList.remove('hidden');
            document.getElementById('distance-val').innerText = data.distance;
            document.getElementById('fee-val').innerText = data.fee.toLocaleString();
            
            // Show estimated time
            let infoBox = document.getElementById('distance-info');
            infoBox.innerHTML = `
                <div class="flex flex-col gap-2">
                    <p class="text-[10px] font-black text-orange-700 dark:text-orange-400 uppercase tracking-widest flex justify-between">
                        <span>Distance estimée : ${data.distance} km</span>
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
</script>
@endsection
