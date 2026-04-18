@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-4">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight leading-none uppercase">Vue d'ensemble</h1>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1.5 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                Opérations en temps réel
            </p>
        </div>
        <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-xl border border-gray-100 shadow-sm">
            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ now()->translatedFormat('l d F Y') }}</span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- Revenue -->
        <div class="bg-gray-900 p-4 rounded-2xl shadow-lg relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Volume Total</p>
                <p class="text-xl font-black text-white leading-none tracking-tight">{{ number_format($totalRevenue, 0, ',', ' ') }} <span class="text-[10px] text-gray-500 font-bold ml-1">F</span></p>
                <div class="mt-3 flex items-center gap-1.5">
                    <span class="text-[8px] font-black bg-emerald-500/20 text-emerald-400 px-1.5 py-0.5 rounded uppercase">+12.5%</span>
                    <span class="text-[8px] text-gray-500">vs mois dernier</span>
                </div>
            </div>
            <div class="absolute right-0 bottom-0 w-12 h-12 bg-white/5 rounded-tl-3xl group-hover:scale-150 transition-transform duration-700"></div>
        </div>

        <!-- Vendors -->
        <a href="{{ route('admin.vendors.index') }}" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:ring-2 hover:ring-blue-500/20 transition-all group">
            <p class="text-[8px] font-black text-blue-500 uppercase tracking-widest mb-1.5">Partenaires</p>
            <p class="text-xl font-black text-gray-900 leading-none">{{ $totalVendeurs }}</p>
            <div class="mt-3 text-[8px] font-black text-gray-300 uppercase italic group-hover:text-blue-500 transition-colors">Explorer la liste</div>
        </a>

        <!-- Pending -->
        <a href="{{ route('admin.vendors.index', ['status' => 'en_cours']) }}" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:ring-2 hover:ring-orange-500/20 transition-all group relative">
            @if($pendingVendeursCount > 0)
                <span class="absolute top-3 right-3 flex h-1.5 w-1.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-orange-500"></span>
                </span>
            @endif
            <p class="text-[8px] font-black text-orange-500 uppercase tracking-widest mb-1.5">Inscriptions</p>
            <p class="text-xl font-black text-gray-900 leading-none">{{ $pendingVendeursCount }}</p>
            <div class="mt-3 text-[8px] font-black text-gray-300 uppercase italic group-hover:text-orange-500 transition-colors">Validations en attente</div>
        </a>

        <!-- Success Rate -->
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-[8px] font-black text-indigo-500 uppercase tracking-widest mb-1.5">Performance</p>
            <div class="flex items-baseline gap-1">
                <p class="text-xl font-black text-gray-900 leading-none">94</p>
                <span class="text-[10px] font-black text-gray-300">%</span>
            </div>
            <div class="mt-2 w-full bg-gray-50 h-1 rounded-full overflow-hidden">
                <div class="bg-indigo-500 h-full w-[94%]"></div>
            </div>
        </div>
    </div>

    <!-- Main Grid Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        
        <!-- Recent Orders (Assuming simplified for dashboard) -->
        <div class="lg:col-span-2 space-y-3">
            <div class="flex items-center justify-between px-1">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Flux Opérationnel Récent</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-[8px] font-black text-blue-500 uppercase hover:underline">Voir tout</a>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <tbody class="divide-y divide-gray-50">
                            {{-- Placeholder for a few orders to keep it compact --}}
                            @php $recentOrders = \App\Models\Commande::latest('date_commande')->take(5)->get(); @endphp
                            @foreach($recentOrders as $order)
                            <tr class="group hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-2.5">
                                    <span class="text-[10px] font-black text-gray-900">#{{ $order->numero_commande }}</span>
                                    <p class="text-[7px] text-gray-300 font-bold uppercase">{{ $order->date_commande->diffForHumans() }}</p>
                                </td>
                                <td class="px-3 py-2.5">
                                    <p class="text-[10px] font-bold text-gray-800">{{ $order->client->name ?? 'Invité' }}</p>
                                    <p class="text-[7px] text-gray-400 font-bold uppercase truncate max-w-[100px]">{{ $order->vendeur->nom_commercial ?? 'Cabaa' }}</p>
                                </td>
                                <td class="px-3 py-2.5 text-right">
                                    <p class="text-[10px] font-black text-gray-900">{{ number_format($order->montant_total, 0, ',', ' ') }} F</p>
                                </td>
                                <td class="pl-3 pr-4 py-2.5 text-right">
                                    @php
                                        $color = match($order->statut) {
                                            'en_attente' => 'text-orange-500',
                                            'termine' => 'text-green-500',
                                            'annule' => 'text-red-500',
                                            default => 'text-blue-500'
                                        };
                                    @endphp
                                    <span class="text-[8px] font-black uppercase tracking-tighter {{ $color }}">{{ $order->statut }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Side: Inscriptions & Actions -->
        <div class="space-y-4">
            
            <!-- Inscriptions -->
            <div class="space-y-2">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Dernières Inscriptions</h3>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-1.5 space-y-1">
                    @foreach($latestVendeurs as $v)
                    <a href="{{ route('admin.vendors.index') }}" class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded-xl transition-all group">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($v->nom_commercial) }}&background=F1F5F9&color=64748B&bold=true" class="w-6 h-6 rounded-lg opacity-80 group-hover:opacity-100 transition-opacity">
                        <div class="flex-1 min-w-0">
                            <p class="text-[9px] font-black text-gray-900 truncate">{{ $v->nom_commercial }}</p>
                            <p class="text-[7px] text-gray-400 font-bold uppercase truncate">{{ $v->type_vendeur }}</p>
                        </div>
                        <span class="text-[7px] text-gray-300 font-black uppercase">{{ $v->date_inscription->format('d/m') }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            <!-- Fast Links -->
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('admin.finance.index') }}" class="p-3 bg-white border border-gray-100 rounded-xl hover:bg-emerald-50 hover:border-emerald-100 transition-all group text-center">
                    <div class="w-7 h-7 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-105 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Finance</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="p-3 bg-white border border-gray-100 rounded-xl hover:bg-purple-50 hover:border-purple-100 transition-all group text-center">
                    <div class="w-7 h-7 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-105 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Clients</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection