<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Commande #{{ $commande->numero_commande }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .receipt-container { border: none; shadow: none; }
        }
        @font-face {
            font-family: 'Receipt';
            src: local('Courier New'), local('Courier'), monospace;
        }
        .receipt-font { font-family: 'Receipt', monospace; }
    </style>
</head>
<body class="bg-gray-100 p-4 sm:p-10">
    <div class="max-w-xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden receipt-container border border-gray-100">
        <!-- Receipt Header -->
        <div class="bg-gray-900 px-8 py-10 text-white text-center">
            <div class="mb-4">
                <span class="text-3xl font-black tracking-tighter uppercase">Cabaa</span>
                <span class="text-orange-500 font-black">Cabaa</span>
            </div>
            <h1 class="text-lg font-bold opacity-80 uppercase tracking-widest">Reçu de Livraison</h1>
            <p class="text-xs mt-2 opacity-50">{{ $commande->date_commande->format('d/m/Y à H:i') }}</p>
        </div>

        <!-- Order Content -->
        <div class="p-8 space-y-8">
            <!-- Vendor & Client -->
            <div class="grid grid-cols-2 gap-8 pb-8 border-b border-gray-100 italic">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Fournisseur</p>
                    <p class="text-sm font-bold text-gray-900">{{ $commande->vendeur->nom_commercial }}</p>
                    <p class="text-[10px] text-gray-500">{{ $commande->vendeur->adresse_complete }}</p>
                    <p class="text-[10px] text-gray-500">{{ $commande->vendeur->telephone_commercial }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Client</p>
                    <p class="text-sm font-bold text-gray-900">{{ $commande->nom_complet_client }}</p>
                    <p class="text-[10px] text-gray-500">{{ $commande->telephone_client }}</p>
                    <p class="text-[10px] text-gray-500">{{ $commande->adresse_livraison }}</p>
                </div>
            </div>

            <!-- Items -->
            <div class="space-y-4">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                            <th class="py-3 text-left">Article</th>
                            <th class="py-3 text-center">Qté</th>
                            <th class="py-3 text-right">Prix</th>
                            <th class="py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($commande->lignes as $ligne)
                        <tr>
                            <td class="py-4">
                                <p class="font-bold text-gray-900">{{ $ligne->nom_plat_snapshot }}</p>
                                @if($ligne->options)
                                    <p class="text-[10px] text-gray-500 mt-1">
                                        @foreach($ligne->options as $group)
                                            {{ $group['groupe'] }}: 
                                            @foreach($group['variantes'] as $v)
                                                {{ $v['nom'] }}{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                            {{ !$loop->last ? ' | ' : '' }}
                                        @endforeach
                                    </p>
                                @endif
                            </td>
                            <td class="py-4 text-center font-bold">{{ $ligne->quantite }}</td>
                            <td class="py-4 text-right text-gray-500">{{ number_format($ligne->prix_unitaire, 0) }}</td>
                            <td class="py-4 text-right font-black">{{ number_format($ligne->sous_total, 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="space-y-3 pt-8 border-t border-gray-100">
                <div class="flex justify-between text-sm text-gray-500 italic">
                    <span class="font-medium">Sous-total</span>
                    <span class="font-bold text-gray-900">{{ number_format($commande->montant_plats, 0) }} FCFA</span>
                </div>
                @if($commande->frais_livraison > 0)
                <div class="flex justify-between text-sm text-gray-500 italic">
                    <span class="font-medium">Frais de livraison</span>
                    <span class="font-bold text-gray-900">+ {{ number_format($commande->frais_livraison, 0) }} FCFA</span>
                </div>
                @endif
                <div class="flex justify-between items-center bg-gray-50 p-4 rounded-2xl">
                    <span class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">Total payé</span>
                    <span class="text-2xl font-black text-gray-900">{{ number_format($commande->montant_total, 0) }} FCFA</span>
                </div>
            </div>

            <!-- QR Code Placeholder or Transaction Ref -->
            <div class="pt-8 text-center space-y-4">
                <div class="inline-block p-4 border border-gray-100 rounded-2xl">
                   <p class="text-[8px] font-black text-gray-400 uppercase mb-2 tracking-[0.3em]">Référence Commande</p>
                   <p class="text-lg font-black tracking-widest text-gray-900">#{{ $commande->numero_commande }}</p>
                </div>
                <p class="text-[10px] text-gray-400 italic">Merci pour votre confiance sur CabaaCabaa !</p>
            </div>
        </div>

        <!-- Footer / Print Action -->
        <div class="px-8 py-6 bg-gray-50 border-t border-gray-100 flex items-center justify-between no-print">
            <a href="{{ route('orders.track', ['code' => $commande->numero_commande]) }}" class="text-xs font-black text-gray-400 uppercase tracking-widest hover:text-gray-900 transition-colors">
                &larr; Retour au suivi
            </a>
            <button onclick="window.print()" class="px-6 py-3 bg-red-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-red-700 transition-all shadow-lg hover:scale-105 active:scale-95">
                Imprimer le reçu
            </button>
        </div>
    </div>

    <!-- Micro-animation script -->
    <script>
        window.onload = function() {
            // Optional: trigger print automatically if param found
            if (window.location.search.includes('print=1')) {
                setTimeout(() => window.print(), 1000);
            }
        }
    </script>
</body>
</html>
