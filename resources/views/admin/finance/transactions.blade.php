@extends('layouts.admin')

@section('title', 'Historique des Transactions')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-400">
            <a href="{{ route('admin.finance.index') }}" class="hover:text-red-600 transition">Finance</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900">Journal des Transactions</span>
        </nav>
        <h1 class="hidden md:block text-2xl font-black text-gray-900 tracking-tight">Journal Financier</h1>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row gap-4">
        <form action="{{ route('admin.finance.transactions') }}" method="GET" class="flex-1 flex gap-4">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher par référence, restaurant..." 
                       class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm">
                <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button type="submit" class="px-6 py-2 bg-slate-900 text-white rounded-xl font-bold text-sm">Filtrer</button>
            @if($search)
                <a href="{{ route('admin.finance.transactions') }}" class="px-4 py-2 text-gray-500 hover:text-red-600 font-bold text-sm flex items-center">Effacer</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">ID / REF</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Date</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Type</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Vendeur</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Montant</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Commande</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transactions as $trans)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-8 py-6">
                            <span class="text-xs font-black text-gray-900 uppercase">#{{ $trans->id_transaction }}</span>
                            <p class="text-[9px] text-gray-400 font-bold mt-1 leading-none">{{ $trans->reference_paiement ?? 'NO-REF' }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-xs font-bold text-gray-900">{{ $trans->date_transaction->format('d M Y') }}</p>
                            <p class="text-[9px] text-gray-400 font-bold mt-1 uppercase">{{ $trans->date_transaction->format('H:i:s') }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">{{ str_replace('_', ' ', $trans->type_transaction) }}</span>
                        </td>
                        <td class="px-8 py-6">
                            @if($trans->vendeur)
                                <p class="text-sm font-bold text-gray-900">{{ $trans->vendeur->nom_commercial }}</p>
                            @else
                                <span class="text-[10px] font-bold text-gray-400 italic">N/A</span>
                            @endif
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-sm font-black {{ $trans->type_transaction == 'paiement_commande' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $trans->type_transaction == 'paiement_commande' ? '+' : '-' }}{{ number_format($trans->montant, 0, ',', ' ') }} F
                            </p>
                        </td>
                        <td class="px-8 py-6">
                            @if($trans->commande)
                                <a href="{{ route('admin.orders.show', $trans->id_commande) }}" class="text-blue-600 hover:underline text-xs font-black uppercase tracking-widest">
                                    #{{ $trans->commande->numero_commande }}
                                </a>
                            @else
                                <span class="text-[10px] text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right lg:text-left">
                            <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[9px] font-black uppercase tracking-wider border border-green-100">
                                {{ $trans->statut }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-8 py-20 text-center">
                            <p class="text-gray-400 font-medium italic">Aucune transaction trouvée.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-8 py-6 border-t border-gray-50">
            {{ $transactions->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
