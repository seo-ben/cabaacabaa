@extends('layouts.admin')

@section('title', 'Gestion des Retraits')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-400">
            <a href="{{ route('admin.finance.index') }}" class="hover:text-red-600 transition">Finance</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900">Demandes de Retrait</span>
        </nav>
        <div class="flex gap-2">
            <a href="{{ route('admin.finance.payouts') }}" class="px-4 py-2 {{ !$status ? 'bg-slate-900 text-white' : 'bg-white text-gray-600 border border-gray-200' }} rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm transition">Tous</a>
            <a href="{{ route('admin.finance.payouts', ['status' => 'en_attente']) }}" class="px-4 py-2 {{ $status == 'en_attente' ? 'bg-orange-500 text-white' : 'bg-white text-gray-600 border border-gray-200' }} rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm transition">En attente</a>
            <a href="{{ route('admin.finance.payouts', ['status' => 'complete']) }}" class="px-4 py-2 {{ $status == 'complete' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 border border-gray-200' }} rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm transition">Complétés</a>
        </div>
    </div>

    <!-- Payouts Table -->
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Vendeur</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Montant</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Méthode</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Date Demande</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Statut</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($payouts as $payout)
                    <tr class="hover:bg-gray-50/30 transition">
                        <td class="px-8 py-6">
                            <p class="text-sm font-black text-gray-900">{{ $payout->vendeur->nom_commercial }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $payout->vendeur->telephone_commercial }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-lg font-black text-red-600">{{ number_format($payout->montant, 0, ',', ' ') }} F</p>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-xs font-bold text-gray-900">{{ $payout->methode_paiement }}</p>
                            <p class="text-[10px] text-gray-500 font-medium truncate max-w-[150px]">{{ $payout->informations_paiement }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-xs font-bold text-gray-900">{{ $payout->created_at->format('d/m/Y') }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $payout->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-8 py-6">
                            @php
                                $statusClasses = [
                                    'en_attente' => 'bg-orange-50 text-orange-600 border-orange-100',
                                    'complete' => 'bg-green-50 text-green-600 border-green-100',
                                    'rejete' => 'bg-red-50 text-red-600 border-red-100',
                                ];
                            @endphp
                            <span class="px-3 py-1 text-[9px] font-black uppercase tracking-wider rounded-full border {{ $statusClasses[$payout->statut] ?? 'bg-gray-50' }}">
                                {{ $payout->statut }}
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            @if($payout->statut == 'en_attente')
                            <button onclick="openPayoutModal('{{ $payout->id_payout }}', '{{ $payout->montant }}', '{{ $payout->vendeur->nom_commercial }}')" class="px-4 py-2 bg-slate-900 text-white rounded-lg text-[9px] font-black uppercase tracking-widest shadow-lg hover:scale-105 active:scale-95 transition-all">
                                Traiter
                            </button>
                            @else
                                <span class="text-[9px] font-bold text-gray-400 italic">Déjà traité</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <p class="text-gray-400 font-medium italic">Aucune demande de retrait.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-8 py-6 border-t border-gray-50">
            {{ $payouts->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Approval Modal (Same as Index for Consistency) -->
<div id="payoutModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closePayoutModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-[2.5rem] shadow-2xl p-10 overflow-hidden">
        <div class="relative z-10">
            <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-2">Traiter le Retrait</h3>
            <p id="modalVendeur" class="text-sm text-gray-500 mb-8 font-medium"></p>
            
            <form id="payoutForm" method="POST" action="">
                @csrf @method('PATCH')
                <div class="bg-gray-50 rounded-2xl p-6 mb-8 text-center italic">
                    <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px] mb-2">Montant du virement</p>
                    <p id="modalAmount" class="text-4xl font-black text-red-600 tracking-tighter"></p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Action</label>
                        <select name="statut" class="w-full bg-gray-50 border-none rounded-xl text-sm font-bold px-4 py-3" required onchange="this.value == 'rejete' ? document.getElementById('notes_admin_div').classList.remove('hidden') : document.getElementById('notes_admin_div').classList.add('hidden')">
                            <option value="complete">Valider / Viré</option>
                            <option value="rejete">Rejeter</option>
                        </select>
                    </div>

                    <div id="notes_admin_div" class="hidden">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Motif (si rejet)</label>
                        <textarea name="notes_admin" placeholder="Indiquez pourquoi la demande de retrait est refusée..." class="w-full bg-gray-50 border-none rounded-xl text-xs font-medium px-4 py-3"></textarea>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 py-4 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl hover:scale-105 active:scale-95 transition-all">
                            Appliquer
                        </button>
                        <button type="button" onclick="closePayoutModal()" class="flex-1 py-4 bg-gray-100 text-gray-600 rounded-xl text-[10px] font-black uppercase tracking-widest">
                            Fermer
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
