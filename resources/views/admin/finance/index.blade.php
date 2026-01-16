@extends('layouts.admin')

@section('title', 'Gestion Financière')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Cœur Financier</h1>
            <p class="text-gray-500 font-medium">Suivi des revenus, commissions et reversements vendeurs.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.finance.transactions') }}" class="px-6 py-3 bg-white border border-gray-200 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Toutes les transactions
            </a>
            <a href="{{ route('admin.finance.payouts') }}" class="px-6 py-3 bg-slate-900 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-slate-800 transition shadow-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Gérer les retraits
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-red-600/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Volume total (GTV)</p>
            <p class="text-3xl font-black text-gray-900 tracking-tight">{{ number_format($stats['total_volume'], 0, ',', ' ') }} F</p>
            <p class="text-[10px] text-green-500 font-bold mt-2 uppercase tracking-wide">Flux total traité</p>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-blue-600/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Revenu Plateforme</p>
            <p class="text-3xl font-black text-blue-600 tracking-tight">{{ number_format($stats['platform_revenue'], 0, ',', ' ') }} F</p>
            <p class="text-[10px] text-gray-400 font-bold mt-2 uppercase tracking-wide">Commissions & Frais</p>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-orange-600/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Retraits en attente</p>
            <p class="text-3xl font-black text-orange-500 tracking-tight">{{ number_format($stats['pending_payouts'], 0, ',', ' ') }} F</p>
            <p class="text-[10px] text-gray-400 font-bold mt-2 uppercase tracking-wide">Dû aux vendeurs</p>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-green-600/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Total Reversé</p>
            <p class="text-3xl font-black text-green-600 tracking-tight">{{ number_format($stats['total_payouts'], 0, ',', ' ') }} F</p>
            <p class="text-[10px] text-gray-400 font-bold mt-2 uppercase tracking-wide">Reversements complétés</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Pending Payouts Action List -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                    <h3 class="text-lg font-black text-gray-900 tracking-tight">Retraits à Valider</h3>
                    <span class="px-4 py-1.5 bg-orange-100 text-orange-600 rounded-xl text-[10px] font-black uppercase tracking-widest">
                        Urgent
                    </span>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($pendingPayouts as $payout)
                    <div class="p-8 flex items-center justify-between gap-6 hover:bg-gray-50 transition">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-black text-gray-900">{{ $payout->vendeur->nom_commercial }}</p>
                                <p class="text-xs text-gray-500">{{ $payout->methode_paiement }} • {{ $payout->informations_paiement }}</p>
                            </div>
                        </div>
                        <div class="text-right flex items-center gap-6">
                            <div>
                                <p class="text-base font-black text-gray-900">{{ number_format($payout->montant, 0, ',', ' ') }} F</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ $payout->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="openPayoutModal('{{ $payout->id_payout }}', '{{ $payout->montant }}', '{{ $payout->vendeur->nom_commercial }}')" class="p-3 bg-red-600 text-white rounded-xl shadow-lg hover:scale-105 transition active:scale-95">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <p class="text-sm text-gray-400 font-medium italic">Aucune demande en attente.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Transactions Table -->
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between">
                    <h3 class="text-lg font-black text-gray-900 tracking-tight">Flux de Trésorerie Récents</h3>
                    <a href="{{ route('admin.finance.transactions') }}" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline">Tout voir</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Date</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Type</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Bénéficiaire</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Montant</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentTransactions as $trans)
                            <tr class="hover:bg-gray-50/30 transition">
                                <td class="px-8 py-5">
                                    <p class="text-xs font-bold text-gray-900">{{ $trans->date_transaction->format('d/m/Y') }}</p>
                                    <p class="text-[10px] text-gray-400 font-medium">{{ $trans->date_transaction->format('H:i') }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-gray-500">{{ str_replace('_', ' ', $trans->type_transaction) }}</span>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-xs font-bold text-gray-900">{{ $trans->vendeur ? $trans->vendeur->nom_commercial : 'Plateforme' }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-sm font-black {{ $trans->type_transaction == 'paiement_commande' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $trans->type_transaction == 'paiement_commande' ? '+' : '-' }}{{ number_format($trans->montant, 0, ',', ' ') }} F
                                    </p>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[9px] font-black uppercase tracking-wider border border-green-100">
                                        {{ $trans->statut }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/10 rounded-full blur-2xl"></div>
                <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-6">Résumé de Commission</h4>
                <div class="space-y-6 relative z-10">
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-slate-400">Total Ventes</p>
                        <p class="text-lg font-black">{{ number_format($stats['total_volume'], 0, ',', ' ') }} F</p>
                    </div>
                    <div class="flex justify-between items-center text-blue-400">
                        <p class="text-sm">Part Plateforme</p>
                        <p class="text-lg font-black">{{ number_format($stats['platform_revenue'], 0, ',', ' ') }} F</p>
                    </div>
                    <div class="pt-6 border-t border-slate-800 flex justify-between items-center">
                        <p class="text-sm text-slate-400">À Reverser</p>
                        <p class="text-2xl font-black">{{ number_format($stats['total_volume'] - $stats['platform_revenue'], 0, ',', ' ') }} F</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] border border-gray-100 p-8 shadow-sm">
                <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-6">Comptes Vendeurs</h4>
                <div class="space-y-6">
                    <p class="text-sm text-gray-500 leading-relaxed font-medium">Assurez-vous de vérifier les informations de paiement avant de valider un retrait manuel.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div id="payoutModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closePayoutModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-[2.5rem] shadow-2xl p-10 overflow-hidden">
        <div class="relative z-10">
            <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-2">Valider le Retrait</h3>
            <p id="modalVendeur" class="text-sm text-gray-500 mb-8 font-medium"></p>
            
            <form id="payoutForm" method="POST" action="">
                @csrf @method('PATCH')
                <div class="bg-gray-50 rounded-2xl p-6 mb-8 text-center italic">
                    <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px] mb-2">Montant à reverser</p>
                    <p id="modalAmount" class="text-4xl font-black text-red-600 tracking-tighter"></p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Action</label>
                        <select name="statut" class="w-full bg-gray-50 border-none rounded-xl text-sm font-bold px-4 py-3" required onchange="this.value == 'rejete' ? document.getElementById('notes_admin_div').classList.remove('hidden') : document.getElementById('notes_admin_div').classList.add('hidden')">
                            <option value="complete">Marquer comme Terminé</option>
                            <option value="rejete">Rejeter la demande</option>
                        </select>
                    </div>

                    <div id="notes_admin_div" class="hidden">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Notes d'administration</label>
                        <textarea name="notes_admin" placeholder="Raison du rejet ou informations complémentaires..." class="w-full bg-gray-50 border-none rounded-xl text-xs font-medium px-4 py-3"></textarea>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 py-4 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl hover:scale-105 active:scale-95 transition-all">
                            Confirmer
                        </button>
                        <button type="button" onclick="closePayoutModal()" class="flex-1 py-4 bg-gray-100 text-gray-600 rounded-xl text-[10px] font-black uppercase tracking-widest">
                            Annuler
                        </button>
                    </div>
                </div>
            </form>
        </div>
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
