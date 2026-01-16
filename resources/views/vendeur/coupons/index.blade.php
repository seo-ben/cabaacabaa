@extends('layouts.vendor')

@section('title', 'Gestion des Coupons')

@section('content')
<div class="space-y-8" x-data="{ showModal: false }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-gray-900 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm">
        <div>
            <h1 class="text-3xl font-display font-black text-gray-900 dark:text-white tracking-tighter">Coupons & Promos</h1>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Créez des offres pour booster vos ventes</p>
        </div>
        <button @click="showModal = true" class="inline-flex items-center gap-2 px-8 py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[11px] font-black uppercase tracking-widest rounded-2xl hover:bg-red-600 dark:hover:bg-red-600 hover:text-white transition-all shadow-xl shadow-gray-200 dark:shadow-none">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Créer un coupon
        </button>
    </div>

    <!-- Coupons Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($coupons as $coupon)
        <div class="bg-white dark:bg-gray-900 rounded-[2rem] border border-gray-100 dark:border-gray-800 p-8 relative overflow-hidden group transition-all hover:border-red-100 dark:hover:border-red-900/30 shadow-sm">
            <!-- Background Accent -->
            <div class="absolute -top-12 -right-12 w-24 h-24 bg-red-50 dark:bg-red-900/10 rounded-full blur-2xl group-hover:bg-red-100 dark:group-hover:bg-red-900/20 transition-all"></div>
            
            <div class="relative z-10 flex flex-col h-full">
                <div class="flex justify-between items-start mb-6">
                    <div class="px-4 py-1.5 bg-gray-100 dark:bg-gray-800 rounded-full">
                        <span class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-widest">{{ $coupon->code }}</span>
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ vendor_route('vendeur.slug.coupons.toggle', $coupon) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="p-2 {{ $coupon->actif ? 'text-green-500 bg-green-50 dark:bg-green-900/20' : 'text-gray-400 bg-gray-50 dark:bg-gray-800' }} rounded-lg hover:scale-110 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </form>
                        <form action="{{ vendor_route('vendeur.slug.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Supprimer ce coupon ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg hover:scale-110 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-4xl font-display font-black text-gray-900 dark:text-white mb-2">
                        @if($coupon->type === 'percentage')
                            {{ $coupon->valeur }}<span class="text-xl">%</span>
                        @else
                            {{ number_format($coupon->valeur, 0, ',', ' ') }}<span class="text-xl">F</span>
                        @endif
                    </h3>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">De réduction immédiate</p>
                </div>

                <div class="space-y-3 mt-auto pt-6 border-t border-gray-50 dark:border-gray-800">
                    <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest">
                        <span class="text-gray-400">Min. Achat</span>
                        <span class="text-gray-900 dark:text-white">{{ number_format($coupon->montant_minimal_achat, 0) }} FCFA</span>
                    </div>
                    <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest">
                        <span class="text-gray-400">Utilisation</span>
                        <span class="text-gray-900 dark:text-white">{{ $coupon->nombre_utilisations }} / {{ $coupon->limite_utilisation ?? '∞' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest">
                        <span class="text-gray-400">Expire le</span>
                        <span class="text-gray-900 dark:text-white">{{ $coupon->expire_at ? $coupon->expire_at->format('d/m/Y') : 'Jamais' }}</span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white dark:bg-gray-900 rounded-[2.5rem] p-24 text-center border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="w-24 h-24 bg-gray-50 dark:bg-gray-800 rounded-3xl flex items-center justify-center mx-auto mb-8 border border-gray-100 dark:border-gray-700">
                <svg class="w-12 h-12 text-gray-200 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
            </div>
            <h2 class="text-2xl font-display font-black text-gray-900 dark:text-white mb-2 tracking-tight">Aucun coupon actif</h2>
            <p class="text-gray-400 dark:text-gray-500 font-bold max-w-sm mx-auto mb-10 uppercase text-[10px] tracking-widest leading-loose">Créez votre premier code promotionnel pour attirer plus de clients fidèles.</p>
        </div>
        @endforelse
    </div>

    <!-- Create Coupon Modal -->
    <div x-show="showModal" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-gray-900/75 dark:bg-black/80 backdrop-blur-sm" @click="showModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative inline-block align-bottom bg-white dark:bg-gray-900 rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100 dark:border-gray-800 p-10">
                <div class="flex justify-between items-center mb-10">
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter">Nouveau Coupon</h3>
                    <button @click="showModal = false" class="p-2 bg-gray-50 dark:bg-gray-800 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ vendor_route('vendeur.slug.coupons.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Code du Coupon (ex: PROMOJuin)</label>
                        <input type="text" name="code" required class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-red-500 rounded-2xl outline-none transition-all text-sm font-bold text-gray-900 dark:text-white uppercase tracking-widest" placeholder="PROMO20">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Type</label>
                            <select name="type" required class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-red-500 rounded-2xl outline-none transition-all text-sm font-bold text-gray-900 dark:text-white">
                                <option value="percentage">Pourcentage (%)</option>
                                <option value="fixed">Montant Fixe (F)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Valeur</label>
                            <input type="number" name="valeur" required class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-red-500 rounded-2xl outline-none transition-all text-sm font-bold text-gray-900 dark:text-white" placeholder="20">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Achat Minimum (FCFA)</label>
                        <input type="number" name="montant_minimal_achat" required class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-red-500 rounded-2xl outline-none transition-all text-sm font-bold text-gray-900 dark:text-white" placeholder="2000">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Limite Utilisation</label>
                            <input type="number" name="limite_utilisation" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-red-500 rounded-2xl outline-none transition-all text-sm font-bold text-gray-900 dark:text-white" placeholder="100">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Date d'Expiration</label>
                            <input type="date" name="expire_at" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 border-2 border-transparent focus:border-red-500 rounded-2xl outline-none transition-all text-sm font-bold text-gray-900 dark:text-white">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-5 bg-red-600 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-red-700 transition-all shadow-xl shadow-red-200 dark:shadow-none mt-4">
                        Créer le Coupon
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
