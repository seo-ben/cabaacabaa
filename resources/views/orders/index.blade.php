@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#FDFCFB] dark:bg-gray-950 py-24 transition-colors duration-300">
    <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
            <div class="space-y-4">
                <h1 class="text-5xl font-black text-gray-900 dark:text-white tracking-tighter">Mes commandes</h1>
                <p class="text-gray-500 dark:text-gray-400 font-medium">Suivez l'historique et le statut de vos articles.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="px-6 py-3 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm text-sm font-bold text-gray-900 dark:text-white">
                    <span class="text-orange-500 mr-2">{{ $commandes->total() }}</span> Commandes au total
                </div>
            </div>
        </div>

        @if($commandes->isEmpty())
            <div class="bg-white dark:bg-gray-900 rounded-[4rem] p-20 text-center border border-gray-100 dark:border-gray-800 shadow-xl dark:shadow-none">
                <div class="w-24 h-24 bg-orange-50 dark:bg-orange-900/20 rounded-[2rem] flex items-center justify-center text-orange-500 mx-auto mb-8">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-4">Vous n'avez pas encore commandé</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-10 max-w-sm mx-auto font-medium">Nos articles n'attendent que vous. Découvrez les meilleures boutiques autour de vous.</p>
                <a href="{{ route('home') }}" class="inline-block px-10 py-5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white rounded-[2rem] font-black uppercase tracking-widest text-[11px] hover:bg-orange-600 dark:hover:bg-gray-200 transition-all shadow-xl active:scale-95">Explorer le catalogue</a>
            </div>
        @else
            <div class="space-y-8">
                @foreach($commandes as $commande)
                    <div class="group bg-white dark:bg-gray-900 rounded-[3.5rem] border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-2xl hover:shadow-gray-200/50 dark:hover:shadow-black/20 transition-all duration-500 overflow-hidden">
                        <div class="flex flex-col lg:flex-row">
                            <!-- Info Side -->
                            <div class="flex-1 p-10 lg:p-12 space-y-8">
                                <div class="flex flex-wrap items-center justify-between gap-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center text-gray-400 group-hover:bg-orange-50 dark:group-hover:bg-orange-900/20 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Commande #{{ $commande->numero_commande }}</p>
                                            <p class="text-sm font-black text-gray-900 dark:text-white">{{ $commande->date_commande->format('d M Y à H:i') }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-3">
                                        <span class="px-5 py-2.5 
                                            @if($commande->statut == 'en_attente') bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border-orange-100 dark:border-orange-900/30
                                            @elseif($commande->statut == 'confirmee' || $commande->statut == 'terminee') bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border-green-100 dark:border-green-900/30
                                            @elseif($commande->statut == 'annulee') bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border-red-100 dark:border-red-900/30
                                            @else bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border-blue-100 dark:border-blue-900/30 @endif
                                            rounded-full text-[10px] font-black uppercase tracking-widest border border-current">
                                            {{ ucfirst(str_replace('_', ' ', $commande->statut)) }}
                                        </span>
                                        @if($commande->paiement_effectue)
                                            <span class="px-5 py-2.5 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border border-current rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                Payé
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 pt-8 border-t border-gray-50 dark:border-gray-800">
                                    <div>
                                        <h4 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3">Établissement</h4>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-gray-50 dark:bg-gray-800 rounded-lg flex items-center justify-center text-orange-600 dark:text-orange-400 font-black italic text-xs">
                                                {{ substr($commande->vendeur->nom_commercial, 0, 1) }}
                                            </div>
                                            <span class="text-sm font-black text-gray-900 dark:text-white">{{ $commande->vendeur->nom_commercial }}</span>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3">Mode</h4>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-gray-50 dark:bg-gray-800 rounded-lg flex items-center justify-center text-gray-900 dark:text-white">
                                                @if($commande->type_recuperation == 'livraison')
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                                @endif
                                            </div>
                                            <span class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest text-[11px]">{{ $commande->type_recuperation }}</span>
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3">Total</h4>
                                        <span class="text-xl font-black text-orange-600 dark:text-orange-400 tracking-tighter">{{ number_format($commande->montant_total, 0) }} <small class="text-[10px] uppercase">FCFA</small></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Side -->
                            <div class="bg-gray-50 dark:bg-gray-800 lg:w-72 p-10 flex flex-col justify-center gap-4 border-l dark:border-gray-800">
                                <a href="{{ route('order.confirmation', $commande->id_commande) }}" class="w-full py-4 bg-white dark:bg-gray-700 border-2 border-transparent hover:border-orange-500 dark:hover:border-orange-500 hover:text-orange-600 dark:hover:text-orange-400 text-gray-900 dark:text-white text-[10px] font-black uppercase tracking-widest rounded-2xl text-center transition-all shadow-sm">Suivre la commande</a>
                                @if($commande->statut == 'en_attente')
                                    <a href="{{ route('orders.cancel', $commande->id_commande) }}" 
                                       onclick="return confirm('Voulez-vous vraiment annuler cette commande ?')"
                                       class="w-full py-4 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-[10px] font-black uppercase tracking-widest rounded-2xl text-center hover:bg-red-600 dark:hover:bg-red-600 hover:text-white dark:hover:text-white transition-all">Annuler</a>
                                @endif
                                <a href="{{ route('orders.reorder', $commande->id_commande) }}" class="w-full py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[10px] font-black uppercase tracking-widest rounded-2xl text-center hover:bg-orange-600 dark:hover:bg-gray-200 transition-all shadow-xl dark:shadow-none active:scale-95">Commander à nouveau</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-16">
                {{ $commandes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
