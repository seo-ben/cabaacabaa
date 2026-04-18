@extends('layouts.admin')

@section('title', 'Détails Commande #' . $order->numero_commande)

@section('content')
<div class="space-y-3">
    <!-- Breadcrumbs / Top Bar -->
    <div class="flex items-center justify-between">
        <nav class="flex items-center gap-2 text-[7px] font-black uppercase tracking-[0.2em] text-gray-400">
            <a href="{{ route('admin.orders.index') }}" class="hover:text-red-600 transition">Commandes</a>
            <svg class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900">#{{ $order->numero_commande }}</span>
        </nav>
        
        <div class="flex items-center gap-2">
            @php
                $statusClasses = [
                    'en_attente' => 'bg-orange-50 text-orange-600 border-orange-100',
                    'en_preparation' => 'bg-blue-50 text-blue-600 border-blue-100',
                    'pret' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                    'termine' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                    'annule' => 'bg-rose-50 text-rose-600 border-rose-100',
                ];
                $statusLabels = [
                    'en_attente' => 'Reçue',
                    'en_preparation' => 'En Prep.',
                    'pret' => 'Prête',
                    'termine' => 'Livrée',
                    'annule' => 'Annul.',
                ];
            @endphp
            <span class="px-2 py-1 rounded-lg border text-[8px] font-black uppercase tracking-widest {{ $statusClasses[$order->statut] ?? 'bg-gray-50' }}">
                {{ $statusLabels[$order->statut] ?? str_replace('_', ' ', $order->statut) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
        <!-- Main Actions & Identity (Full width row) -->
        <div class="lg:col-span-12">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-3">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-red-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-gray-900 tracking-tight leading-none uppercase">Commande #{{ $order->numero_commande }}</h2>
                            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mt-1.5 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                Reçue le {{ $order->date_commande->format('d/m/Y à H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <form action="{{ route('admin.orders.status', $order->id_commande) }}" method="POST" class="flex items-center gap-2">
                            @csrf @method('PATCH')
                            <select name="statut" class="bg-gray-50 border-gray-200 rounded-xl text-[8px] font-black uppercase tracking-widest px-3 py-2 focus:ring-2 focus:ring-red-500 transition-all outline-none">
                                <option value="en_attente" {{ $order->statut == 'en_attente' ? 'selected' : '' }}>Statut: Reçue</option>
                                <option value="en_preparation" {{ $order->statut == 'en_preparation' ? 'selected' : '' }}>Statut: Prépar.</option>
                                <option value="pret" {{ $order->statut == 'pret' ? 'selected' : '' }}>Statut: Prête</option>
                                <option value="termine" {{ $order->statut == 'termine' ? 'selected' : '' }}>Statut: Livrée</option>
                                <option value="annule" {{ $order->statut == 'annule' ? 'selected' : '' }}>Action: Annuler</option>
                            </select>
                            <button type="submit" class="px-3 py-2 bg-gray-900 text-white rounded-xl text-[8px] font-black uppercase tracking-widest hover:bg-black transition-all active:scale-95 shadow-lg">
                                MAJ
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items (Left) -->
        <div class="lg:col-span-8 space-y-3">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden text-[9px]">
                <div class="px-3 py-2 border-b border-gray-100 flex items-center justify-between bg-gray-50/30">
                    <h3 class="font-black text-gray-900 uppercase tracking-widest">Articles ({{ $order->lignes->count() }})</h3>
                    <div class="flex items-center gap-2">
                        <span class="font-black text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded uppercase tracking-widest">Payé: {{ $order->mode_paiement }}</span>
                    </div>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($order->lignes as $ligne)
                    <div class="px-3 py-2 flex items-center gap-3 hover:bg-gray-50/50 transition-colors">
                        <div class="w-10 h-10 rounded-lg overflow-hidden shrink-0 bg-gray-50 border border-gray-100 shadow-inner">
                            @if($ligne->plat->image_principale)
                                <img src="{{ asset('storage/' . $ligne->plat->image_principale) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-[10px] font-black text-gray-900 uppercase truncate leading-tight">{{ $ligne->plat->nom_plat }}</h4>
                            <p class="text-[7.5px] font-black text-gray-400 uppercase tracking-widest mt-0.5">{{ $ligne->quantite }} Unite(s) • {{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} F</p>
                            @if($ligne->options)
                                <div class="mt-1 flex flex-wrap gap-1">
                                    @php $options = is_array($ligne->options) ? $ligne->options : json_decode($ligne->options, true); @endphp
                                    @foreach($options as $group => $sel)
                                        @foreach($sel as $varName => $price)
                                            <span class="px-1 py-0.5 bg-gray-100 border border-gray-200 rounded text-[6px] font-black text-gray-400 uppercase tracking-widest">{{ $varName }}</span>
                                        @endforeach
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-gray-900">{{ number_format($ligne->prix_total, 0, ',', ' ') }} F</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Financial Summary -->
                <div class="bg-gray-50/30 p-3 space-y-1.5 border-t border-gray-100">
                    <div class="flex justify-between text-[8px] font-black uppercase tracking-widest leading-none">
                        <span class="text-gray-400">Total articles</span>
                        <span class="text-gray-900">{{ number_format($order->montant_plats, 0, ',', ' ') }} F</span>
                    </div>
                    @if($order->frais_service > 0)
                    <div class="flex justify-between text-[8px] font-black uppercase tracking-widest leading-none">
                        <span class="text-gray-400">Frais logistiques</span>
                        <span class="text-gray-900">{{ number_format($order->frais_service, 0, ',', ' ') }} F</span>
                    </div>
                    @endif
                    <div class="pt-2 mt-1 border-t border-gray-200 flex justify-between items-center">
                        <span class="text-[9px] font-black text-gray-900 uppercase tracking-tight">Total Final Net</span>
                        <span class="text-lg font-black text-red-600 tracking-tight leading-none">{{ number_format($order->montant_total, 0, ',', ' ') }} F</span>
                    </div>
                </div>
            </div>

            @if($order->instructions_speciales)
            <div class="p-3 bg-red-50/50 rounded-2xl border border-red-100/50">
                <p class="text-[7px] font-black uppercase text-red-600 tracking-widest mb-1.5 flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Notes du Client
                </p>
                <p class="text-gray-700 text-[9px] font-bold leading-relaxed uppercase">{{ $order->instructions_speciales }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar (Right) -->
        <div class="lg:col-span-4 space-y-3">
            <!-- Timeline (Vertical) -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-3">
                <p class="text-[8px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Chronologie</p>
                <div class="space-y-4 relative">
                    <div class="absolute left-3 top-2 bottom-2 w-px bg-gray-50"></div>
                    @php
                        $steps = [
                            ['label' => 'REÇUE', 'time' => $order->date_commande, 'icon' => 'clock'],
                            ['label' => 'PRÉPARATION', 'time' => $order->heure_preparation_debut, 'icon' => 'flame'],
                            ['label' => 'PRÊTE', 'time' => $order->heure_prete, 'icon' => 'bell'],
                            ['label' => 'LIVRÉE', 'time' => $order->statut == 'termine' ? $order->heure_recuperation_effective : null, 'icon' => 'check']
                        ];
                    @endphp
                    @foreach($steps as $step)
                        <div class="flex items-start gap-3 relative z-10">
                            <div class="w-6 h-6 rounded-lg {{ $step['time'] ? 'bg-gray-900 text-white shadow-md shadow-gray-200' : 'bg-gray-50 text-gray-300 border border-gray-100' }} flex items-center justify-center transition-all shrink-0">
                                <span class="text-[10px]">
                                    @if($step['icon'] == 'clock')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @elseif($step['icon'] == 'flame')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    @elseif($step['icon'] == 'bell')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    @else
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[8px] font-black uppercase tracking-widest {{ $step['time'] ? 'text-gray-900' : 'text-gray-300' }} mb-0.5 leading-none">{{ $step['label'] }}</p>
                                <p class="text-[7.5px] font-bold text-gray-400 uppercase leading-none">{{ $step['time'] ? $step['time']->format('d M — H:i') : '--:--' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Client & Vendor & Delivery Maps -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50 overflow-hidden">
                <!-- Vendeur -->
                <div class="p-3">
                    <p class="text-[7px] font-black uppercase tracking-widest text-gray-400 mb-2">Vendeur Partenaire</p>
                    @if($order->vendeur)
                    <div class="flex items-center gap-3">
                        <img src="{{ $order->vendeur->image_principale ? asset('storage/' . $order->vendeur->image_principale) : 'https://ui-avatars.com/api/?name='.urlencode($order->vendeur->nom_commercial).'&background=F1F5F9&color=64748B' }}" class="w-8 h-8 rounded-xl shadow-inner object-cover border border-gray-100">
                        <div class="min-w-0">
                            <p class="text-[9px] font-black text-gray-900 uppercase truncate leading-none mb-1">{{ $order->vendeur->nom_commercial }}</p>
                            <a href="{{ route('admin.vendors.edit', $order->id_vendeur) }}" class="text-[7px] font-black text-red-600 hover:text-red-700 uppercase tracking-widest">Voir Profil →</a>
                        </div>
                    </div>
                    @endif
                </div>
                <!-- Client -->
                <div class="p-3">
                    <p class="text-[7px] font-black uppercase tracking-widest text-gray-400 mb-2">Coordonnées Client</p>
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($order->client->name ?? $order->nom_complet ?? 'User') }}&background=F1F5F9&color=64748B&bold=true" class="w-8 h-8 rounded-xl border border-gray-100">
                        <div class="min-w-0 flex-1">
                            <p class="text-[9px] font-black text-gray-900 uppercase truncate leading-none mb-1">{{ $order->client->name ?? $order->nom_complet }}</p>
                            <p class="text-[7.5px] text-gray-400 font-bold uppercase truncate">{{ $order->client->phone ?? $order->phone ?? 'Indisponible' }}</p>
                        </div>
                        <a href="tel:{{ $order->client->phone ?? $order->phone }}" class="w-7 h-7 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </a>
                    </div>
                </div>
                <!-- Destination -->
                <div class="p-3">
                    <p class="text-[7px] font-black uppercase tracking-widest text-gray-400 mb-2">Adresse Livraison</p>
                    <div class="flex items-start gap-2">
                        <div class="w-6 h-6 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center shrink-0">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        </div>
                        <p class="text-[9px] font-bold text-gray-600 uppercase leading-snug tracking-tight">
                            {{ $order->adresse_livraison ?? 'Adresse non spécifiée' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Communication -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col h-[400px]">
                <div class="px-3 py-2 border-b border-gray-100 bg-gray-50/30">
                    <p class="text-[7px] font-black uppercase tracking-widest text-gray-400">Communication Client</p>
                </div>
                <div class="flex-1 overflow-hidden">
                    @include('partials.order-chat', ['orderId' => $order->id_commande])
                </div>
            </div>

            <!-- Case Avis -->
            @if($order->avis)
            <div class="bg-gray-900 rounded-2xl p-4 shadow-xl border border-white/5 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-24 h-24 bg-yellow-500/10 rounded-full blur-2xl"></div>
                <p class="text-[8px] font-black uppercase tracking-widest text-gray-500 mb-3">Feedback Client</p>
                <div class="flex items-center gap-1 text-yellow-400 mb-3">
                    @for($i=0; $i<$order->avis->note; $i++) 
                        <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M10 1l2.6 6.3h6.4l-5.2 4 2 6.7-5.8-4.3-5.8 4.3 2-6.7-5.2-4h6.4z"/></svg> 
                    @endfor
                </div>
                <p class="text-[9px] text-gray-300 font-bold leading-relaxed uppercase italic">"{{ $order->avis->commentaire }}"</p>
            </div>
            @endif
        </div>
    </div>
</div>
<script>
    window.orderCode = "{{ $order->numero_commande }}";
</script>
@endsection
