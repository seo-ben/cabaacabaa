@extends('layouts.vendor')

@section('title', 'Portefeuille & Paiements')
@section('page_title', 'Portefeuille & Paiements')

@section('content')
<div class="space-y-10">
    
    <!-- Wallet Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-2 bg-gray-900 dark:bg-gray-900/50 rounded-[2.5rem] flex flex-col md:flex-row items-center gap-10 p-10 text-white shadow-2xl shadow-gray-200 dark:shadow-none border border-transparent dark:border-gray-800 transition-colors">
            <div class="flex-1 space-y-4 text-center md:text-left">
                <p class="text-[11px] font-black uppercase tracking-[0.3em] opacity-40">Solde Disponible</p>
                <h3 class="text-5xl font-black tracking-tighter text-orange-400">{{ number_format($vendeur->wallet_balance, 0) }} <small class="text-xl">FCFA</small></h3>
                <p class="text-xs font-bold opacity-60 leading-relaxed max-w-md">Ce solde représente vos gains disponibles pour un retrait immédiat. Les transactions sont traitées sous 24h à 48h.</p>
            </div>
            <div class="w-full md:w-auto">
                <button onclick="document.getElementById('payoutModal').classList.remove('hidden')" class="w-full px-10 py-5 bg-orange-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-orange-700 transition-all shadow-xl shadow-orange-600/20 active:scale-95">
                    Demander un retrait
                </button>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] p-8 border border-gray-100 dark:border-gray-800 shadow-sm flex flex-col justify-center space-y-6 transition-colors">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center text-green-600 dark:text-green-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Total Retiré</p>
                    <p class="text-xl font-black text-gray-900 dark:text-white">{{ number_format($vendeur->payoutRequests()->where('statut', 'complete')->sum('montant'), 0) }} FCFA</p>
                </div>
            </div>
            <div class="flex items-center gap-4 border-t border-gray-50 dark:border-gray-800 pt-6">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">En attente</p>
                    <p class="text-xl font-black text-gray-900 dark:text-white">{{ number_format($vendeur->payoutRequests()->where('statut', 'en_attente')->sum('montant'), 0) }} FCFA</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payout Requests History -->
    <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] p-10 border border-gray-100 dark:border-gray-800 shadow-sm transition-colors">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h3 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white">Historique des Retraits</h3>
                <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-1">Gérez vos demandes de paiement</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Date</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Montant</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Méthode</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Statut</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @foreach($payouts as $payout)
                    <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-6 py-6 text-sm font-bold text-gray-900 dark:text-white">{{ $payout->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-6 text-sm font-black text-gray-900 dark:text-white">{{ number_format($payout->montant, 0) }} FCFA</td>
                        <td class="px-6 py-6">
                            <span class="text-[10px] font-black uppercase text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-3 py-1.5 rounded-lg">{{ $payout->methode_paiement }}</span>
                        </td>
                        <td class="px-6 py-6">
                            @php
                                $statusColors = [
                                    'en_attente' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400 border-yellow-100 dark:border-yellow-900/30',
                                    'approuve' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border-blue-100 dark:border-blue-900/30',
                                    'complete' => 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border-green-100 dark:border-green-900/30',
                                    'rejete' => 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border-red-100 dark:border-red-900/30',
                                ];
                            @endphp
                            <span class="px-3 py-1.5 rounded-xl border {{ $statusColors[$payout->statut] ?? 'bg-gray-50 dark:bg-gray-800 text-gray-400' }} text-[9px] font-black uppercase tracking-widest">
                                {{ str_replace('_', ' ', $payout->statut) }}
                            </span>
                        </td>
                        <td class="px-6 py-6 text-xs text-gray-500 dark:text-gray-400 font-medium">
                            {{ $payout->notes_admin ?: '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($payouts->isEmpty())
            <div class="py-20 text-center">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-6 text-gray-200 dark:text-gray-700">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-sm font-bold text-gray-400 dark:text-gray-500 italic">Aucune demande de retrait effectuée.</p>
            </div>
            @endif
        </div>
        
        <div class="mt-8">
            {{ $payouts->links() }}
        </div>
    </div>
</div>

<!-- Payout Modal -->
<div id="payoutModal" class="hidden fixed inset-0 z-[100] overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm" onclick="document.getElementById('payoutModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100 dark:border-gray-800">
            <div class="p-10">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Demander un Retrait</h3>
                        <p class="text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest mt-1">Fonds disponibles: {{ number_format($vendeur->wallet_balance, 0) }} FCFA</p>
                    </div>
                    <button onclick="document.getElementById('payoutModal').classList.add('hidden')" class="p-4 bg-gray-50 dark:bg-gray-800 text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 transition-colors rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ vendor_route('vendeur.slug.payouts.store') }}" method="POST" class="space-y-8">
                    @csrf
                    <div class="space-y-3">
                        <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 ml-2">Montant à retirer (FCFA)</label>
                        <input type="number" name="montant" min="5000" max="{{ $vendeur->wallet_balance }}" required
                               class="w-full px-8 py-5 bg-gray-50 dark:bg-gray-800 border border-transparent dark:border-gray-700 rounded-[1.5rem] text-sm font-black text-gray-900 dark:text-white outline-none focus:bg-white dark:focus:bg-gray-700 focus:ring-4 focus:ring-orange-500/10 transition-all">
                    </div>

                    <div class="space-y-3">
                        <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 ml-2">Méthode de Paiement</label>
                        <select name="methode_paiement" required
                                class="w-full px-8 py-5 bg-gray-50 dark:bg-gray-800 border border-transparent dark:border-gray-700 rounded-[1.5rem] text-sm font-black text-gray-900 dark:text-white outline-none focus:bg-white dark:focus:bg-gray-700 focus:ring-4 focus:ring-orange-500/10 transition-all appearance-none">
                            <option value="momo">MTN Mobile Money</option>
                            <option value="flooz">Moov Money (Flooz)</option>
                            <option value="banque">Virement Bancaire</option>
                            <option value="cheque">Chèque</option>
                        </select>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 ml-2">Informations de Paiement</label>
                        <textarea name="informations_paiement" rows="3" required placeholder="Ex: Numéro MoMo, Nom complet, Rib..."
                                  class="w-full px-8 py-5 bg-gray-50 dark:bg-gray-800 border border-transparent dark:border-gray-700 rounded-[1.5rem] text-sm font-bold text-gray-900 dark:text-white outline-none focus:bg-white dark:focus:bg-gray-700 focus:ring-4 focus:ring-orange-500/10 transition-all"></textarea>
                    </div>

                    <div class="flex items-center gap-4 bg-orange-50 dark:bg-orange-900/10 p-6 rounded-2xl border border-orange-100 dark:border-orange-900/30">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-[10px] font-bold text-orange-900 dark:text-orange-400/80 leading-relaxed uppercase tracking-widest">Le montant sera déduit de votre solde immédiatement après la demande.</p>
                    </div>

                    <button type="submit" class="w-full py-5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-orange-600 dark:hover:bg-orange-600 dark:hover:text-white transition-all shadow-2xl shadow-gray-900/10 active:scale-95">
                        Confirmer la demande
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
