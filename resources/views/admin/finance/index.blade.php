@extends('layouts.admin')

@section('title', 'Gestion Financière')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight leading-none uppercase">Cœur Financier</h1>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1.5 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                Revenus & Logistique Monétaire
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.finance.transactions') }}" class="px-4 py-2.5 bg-white border border-gray-100 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-gray-600">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Transactions
            </a>
            <a href="{{ route('admin.finance.payouts') }}" class="px-4 py-2.5 bg-gray-900 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-black transition shadow-lg flex items-center gap-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Retraits
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden group">
            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Volume total (GTV)</p>
            <p class="text-xl font-black text-gray-900 leading-none tracking-tight">{{ number_format($stats['total_volume'], 0, ',', ' ') }} F</p>
            <div class="absolute -right-2 -bottom-2 w-10 h-10 bg-gray-50 rounded-full group-hover:scale-150 transition-transform"></div>
        </div>

        <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden group">
            <p class="text-[8px] font-black text-blue-500 uppercase tracking-widest mb-1.5">Revenu Plateforme</p>
            <p class="text-xl font-black text-gray-900 leading-none tracking-tight">{{ number_format($stats['platform_revenue'], 0, ',', ' ') }} F</p>
            <div class="absolute -right-2 -bottom-2 w-10 h-10 bg-blue-50 rounded-full group-hover:scale-150 transition-transform"></div>
        </div>

        <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden group">
            <p class="text-[8px] font-black text-orange-500 uppercase tracking-widest mb-1.5">En attente</p>
            <p class="text-xl font-black text-gray-900 leading-none tracking-tight">{{ number_format($stats['pending_payouts'], 0, ',', ' ') }} F</p>
            <div class="absolute -right-2 -bottom-2 w-10 h-10 bg-orange-50 rounded-full group-hover:scale-150 transition-transform"></div>
        </div>

        <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden group">
            <p class="text-[8px] font-black text-emerald-500 uppercase tracking-widest mb-1.5">Total Reversé</p>
            <p class="text-xl font-black text-gray-900 leading-none tracking-tight">{{ number_format($stats['total_payouts'], 0, ',', ' ') }} F</p>
            <div class="absolute -right-2 -bottom-2 w-10 h-10 bg-emerald-50 rounded-full group-hover:scale-150 transition-transform"></div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-4">
        <!-- Pending Payouts Action List -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-[10px] font-black text-gray-900 uppercase tracking-widest">Retraits à Valider</h3>
                    <span class="px-2 py-0.5 bg-orange-100 text-orange-600 rounded text-[7px] font-black uppercase tracking-widest animate-pulse">Action Requise</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($pendingPayouts as $payout)
                    <div class="px-4 py-3 flex items-center justify-between gap-4 hover:bg-gray-50/50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-black text-gray-900 truncate">{{ $payout->vendeur->nom_commercial }}</p>
                                <p class="text-[8px] text-gray-400 font-bold uppercase truncate">{{ $payout->methode_paiement }} • {{ $payout->informations_paiement }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="text-[11px] font-black text-gray-900 leading-none">{{ number_format($payout->montant, 0, ',', ' ') }} F</p>
                                <p class="text-[7px] text-gray-300 font-black uppercase mt-1">{{ $payout->created_at->diffForHumans() }}</p>
                            </div>
                            <button onclick="openPayoutModal('{{ $payout->id_payout }}', '{{ $payout->montant }}', '{{ $payout->vendeur->nom_commercial }}')" class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white transition active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="py-12 text-center">
                        <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest italic">Aucune demande en attente</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Transactions Table -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden text-[10px]">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Flux de Trésorerie Récents</h3>
                    <a href="{{ route('admin.finance.transactions') }}" class="text-[7px] font-black text-blue-500 uppercase tracking-widest hover:underline">Voir Historique</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-4 py-2.5 font-black text-gray-400 uppercase tracking-widest">Date</th>
                                <th class="px-4 py-2.5 font-black text-gray-400 uppercase tracking-widest">Type</th>
                                <th class="px-4 py-2.5 font-black text-gray-400 uppercase tracking-widest">Bénéficiaire</th>
                                <th class="px-4 py-2.5 font-black text-gray-400 uppercase tracking-widest">Montant</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentTransactions as $trans)
                            <tr class="hover:bg-gray-50/30 transition">
                                <td class="px-4 py-2">
                                    <p class="font-black text-gray-900 leading-none">{{ $trans->date_transaction->format('d/m/Y') }}</p>
                                    <p class="text-[7px] text-gray-300 font-bold uppercase mt-0.5">{{ $trans->date_transaction->format('H:i') }}</p>
                                </td>
                                <td class="px-4 py-2">
                                    <span class="text-[8px] font-black uppercase text-gray-400">{{ str_replace('_', ' ', $trans->type_transaction) }}</span>
                                </td>
                                <td class="px-4 py-2 font-bold text-gray-900">
                                    {{ $trans->vendeur ? $trans->vendeur->nom_commercial : 'CabaaCabaa' }}
                                </td>
                                <td class="px-4 py-2">
                                    <p class="font-black {{ $trans->type_transaction == 'paiement_commande' ? 'text-emerald-500' : 'text-rose-500' }}">
                                        {{ $trans->type_transaction == 'paiement_commande' ? '+' : '-' }}{{ number_format($trans->montant, 0, ',', ' ') }} F
                                    </p>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-4">
            <div class="bg-gray-900 rounded-2xl p-5 text-white relative overflow-hidden shadow-xl">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-600/20 rounded-full blur-2xl"></div>
                <h4 class="text-[8px] font-black uppercase tracking-widest text-gray-500 mb-6">Résumé Commission</h4>
                <div class="space-y-4 relative z-10">
                    <div class="flex justify-between items-center">
                        <p class="text-[10px] text-gray-400 uppercase font-black">Total Ventes</p>
                        <p class="text-sm font-black">{{ number_format($stats['total_volume'], 0, ',', ' ') }} F</p>
                    </div>
                    <div class="flex justify-between items-center text-blue-400">
                        <p class="text-[10px] uppercase font-black">Part Plateforme</p>
                        <p class="text-sm font-black">{{ number_format($stats['platform_revenue'], 0, ',', ' ') }} F</p>
                    </div>
                    <div class="pt-4 border-t border-gray-800 flex justify-between items-center">
                        <p class="text-[10px] text-gray-500 uppercase font-black">À Reverser</p>
                        <p class="text-lg font-black text-emerald-400">{{ number_format($stats['total_volume'] - $stats['platform_revenue'], 0, ',', ' ') }} F</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                <h4 class="text-[8px] font-black uppercase tracking-widest text-gray-400 mb-4">Note Logistique</h4>
                <p class="text-[9px] text-gray-500 leading-relaxed font-bold uppercase">Tous les reversements sont traités dans un délai de 24h ouvrées après validation.</p>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div id="payoutModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden flex flex-col">
        <div class="px-5 py-4 bg-gray-900 text-white flex items-center justify-between">
            <h2 class="text-xs font-black uppercase tracking-widest">Valider Retrait</h2>
            <button onclick="closePayoutModal()" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <form id="payoutForm" method="POST" action="" class="p-6 space-y-4">
            @csrf @method('PATCH')
            <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-100">
                <p id="modalVendeur" class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1"></p>
                <p id="modalAmount" class="text-2xl font-black text-emerald-600 tracking-tight"></p>
            </div>

            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Statut Final</label>
                    <select name="statut" class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:ring-2 focus:ring-emerald-500/10" required 
                            onchange="this.value == 'rejete' ? document.getElementById('notes_admin_div').classList.remove('hidden') : document.getElementById('notes_admin_div').classList.add('hidden')">
                        <option value="complete">Terminé / Effectué</option>
                        <option value="rejete">Rejeter la demande</option>
                    </select>
                </div>

                <div id="notes_admin_div" class="hidden space-y-1.5">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Note explicative</label>
                    <textarea name="notes_admin" placeholder="Raison du rejet..." class="w-full px-3 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-900 focus:ring-2 focus:ring-rose-500/10 h-20"></textarea>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 py-3 bg-gray-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all shadow-lg">Confirmer</button>
                    <button type="button" onclick="closePayoutModal()" class="px-6 py-3 bg-gray-100 text-gray-400 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-200 transition-all">Retour</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function openPayoutModal(id, amount, vendor) {
        const form = document.getElementById('payoutForm');
        form.action = `/admin/finance/payouts/${id}`;
        document.getElementById('modalAmount').innerText = new Intl.NumberFormat('fr-FR').format(amount) + ' F';
        document.getElementById('modalVendeur').innerText = vendor;
        document.getElementById('payoutModal').classList.remove('hidden');
    }

    function closePayoutModal() {
        document.getElementById('payoutModal').classList.add('hidden');
    }
</script>
@endsection
