@extends((($user->role ?? 'client') === 'admin') ? 'layouts.admin' : 'layouts.app')

@section('content')

{{-- ==================== MOBILE VIEW (< lg) ==================== --}}
<div class="block lg:hidden bg-gray-50 dark:bg-slate-950 min-h-screen pb-28">

    {{-- Mobile Hero Header --}}
    <div class="relative bg-gradient-to-br from-gray-900 via-slate-800 to-gray-900 overflow-hidden pt-10 pb-16 px-5">
        {{-- Decorative blobs --}}
        <div class="absolute -top-8 -right-8 w-36 h-36 bg-red-500/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-28 h-28 bg-orange-500/15 rounded-full blur-2xl"></div>

        <div class="relative z-10 flex items-center justify-between mb-6">
            {{-- Avatar + Info --}}
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-orange-500 rounded-2xl flex items-center justify-center text-white text-2xl font-black shadow-xl shadow-red-900/30 shrink-0">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Bonjour 👋</p>
                    <h1 class="text-lg font-black text-white leading-tight">{{ $user->name }}</h1>
                </div>
            </div>
            {{-- Quick action --}}
            <a href="{{ route('home') }}"
               class="px-4 py-2.5 bg-red-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest active:scale-95 transition-transform shadow-lg shadow-red-900/30">
                Commander
            </a>
        </div>

        {{-- Last login --}}
        <p class="relative z-10 text-[10px] font-bold text-gray-500 uppercase tracking-widest">
            Dernière connexion :
            <span class="text-gray-400">{{ $user->date_derniere_connexion ? $user->date_derniere_connexion->format('d/m/Y H:i') : 'Bienvenue !' }}</span>
        </p>
    </div>

    {{-- Stats Cards (overlap) --}}
    <div class="px-4 -mt-8 relative z-10">
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-gray-100 dark:border-slate-800 shadow-lg shadow-black/5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center">
                        <svg class="w-4.5 h-4.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <span class="w-2 h-2 rounded-full bg-red-400 animate-pulse"></span>
                </div>
                <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $user->commandes()->count() }}</div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-0.5">Commandes</div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-gray-100 dark:border-slate-800 shadow-lg shadow-black/5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 bg-orange-50 dark:bg-orange-900/20 rounded-xl flex items-center justify-center">
                        <svg class="w-4.5 h-4.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                </div>
                <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $user->favoris()->count() }}</div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-0.5">Favoris</div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="px-4 mt-4">
        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 pl-1">Accès rapide</p>
        <div class="grid grid-cols-3 gap-2">
            <a href="{{ route('orders.index') }}"
               class="bg-white dark:bg-slate-900 rounded-2xl p-3.5 border border-gray-100 dark:border-slate-800 shadow-sm flex flex-col items-center gap-2 active:scale-95 transition-transform">
                <div class="w-10 h-10 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"/></svg>
                </div>
                <span class="text-[9px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-wider text-center leading-tight">Commandes</span>
            </a>

            <a href="{{ route('explore') }}"
               class="bg-white dark:bg-slate-900 rounded-2xl p-3.5 border border-gray-100 dark:border-slate-800 shadow-sm flex flex-col items-center gap-2 active:scale-95 transition-transform">
                <div class="w-10 h-10 bg-orange-50 dark:bg-orange-900/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <span class="text-[9px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-wider text-center leading-tight">Explorer</span>
            </a>

            <a href="{{ route('cart.index') }}"
               class="bg-white dark:bg-slate-900 rounded-2xl p-3.5 border border-gray-100 dark:border-slate-800 shadow-sm flex flex-col items-center gap-2 active:scale-95 transition-transform">
                <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-[9px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-wider text-center leading-tight">Panier</span>
            </a>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="px-4 mt-5">
        <div class="flex items-center justify-between mb-3 pl-1">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Dernières commandes</p>
            <a href="{{ route('orders.index') }}" class="text-[9px] font-black text-red-500 uppercase tracking-widest">Voir tout →</a>
        </div>

        <div class="space-y-2.5">
            @forelse($user->commandes->take(5) as $commande)
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="flex items-center gap-3 px-4 py-3.5">
                        {{-- Icon --}}
                        <div class="w-10 h-10 bg-gray-50 dark:bg-slate-800 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-black text-gray-900 dark:text-white truncate">#{{ $commande->numero_commande }}</span>
                                <span class="text-[9px] font-bold text-gray-400 shrink-0">{{ $commande->date_commande->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-2 mt-0.5">
                                <span class="text-[10px] font-bold text-gray-400 truncate">{{ $commande->lignes ? $commande->lignes->count() : 0 }} art • {{ number_format($commande->montant_total, 0) }} FCFA</span>
                                <span class="shrink-0 px-2 py-0.5 rounded-lg text-[8px] font-black uppercase tracking-wider
                                    @if(in_array($commande->statut, ['livree', 'livré'])) bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400
                                    @elseif(in_array($commande->statut, ['annulee', 'annulé'])) bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400
                                    @else bg-orange-50 text-orange-600 dark:bg-orange-900/20 dark:text-orange-400 @endif">
                                    {{ $commande->statut }}
                                </span>
                            </div>
                        </div>
                        {{-- Arrow --}}
                        <a href="{{ route('orders.track', ['code' => $commande->numero_commande]) }}" class="shrink-0 w-7 h-7 bg-gray-50 dark:bg-slate-800 rounded-lg flex items-center justify-center active:bg-red-50 transition-colors">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-8 text-center">
                    <div class="w-14 h-14 bg-gray-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <p class="text-sm font-black text-gray-900 dark:text-white mb-1">Aucune commande encore</p>
                    <p class="text-xs text-gray-400 font-medium mb-4">Découvrez nos vendeurs et passez votre première commande !</p>
                    <a href="{{ route('home') }}"
                       class="inline-block px-5 py-3 bg-gradient-to-r from-red-600 to-orange-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-red-500/25 active:scale-95 transition-all">
                        Explorer maintenant →
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Livreur CTA (if eligible) --}}
    @if($user->canApplyAsVendor())
    <div class="px-4 mt-5">
        <div class="relative bg-gradient-to-br from-gray-800 to-gray-900 dark:from-slate-800 dark:to-slate-900 rounded-2xl p-5 overflow-hidden border border-white/5">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-orange-500/10 rounded-full blur-2xl"></div>
            <div class="relative z-10 flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-black text-white">Ouvrez votre boutique</p>
                    <p class="text-[10px] text-gray-400 font-medium mt-0.5">Vendez vos produits et développez votre activité.</p>
                </div>
                <a href="{{ route('vendor.apply') }}"
                   class="px-3 py-2 bg-orange-500 text-white rounded-lg text-[9px] font-black uppercase tracking-widest active:scale-95 transition-transform shrink-0">
                    Postuler
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
{{-- ==================== END MOBILE VIEW ==================== --}}


{{-- ==================== DESKTOP VIEW (>= lg) ==================== --}}
<div class="hidden lg:block max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14 py-12">
    <div class="space-y-12">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white dark:bg-gray-900 rounded-[2.5rem] p-10 border border-gray-100 dark:border-gray-800 shadow-sm transition-colors">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-gradient-to-br from-red-600 to-orange-500 rounded-[1.5rem] flex items-center justify-center text-white text-3xl font-black shadow-xl shadow-red-200 dark:shadow-red-900/20">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Bonjour, {{ $user->name }} !</h1>
                    <p class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-1">
                        Dernière connexion : {{ $user->date_derniere_connexion ? $user->date_derniere_connexion->format('d/m/Y H:i') : 'Première fois ? Bienvenue !' }}
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('home') }}" class="px-8 py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-red-600 dark:hover:bg-red-600 dark:hover:text-white transition-all active:scale-95 shadow-lg">
                    Acheter maintenant
                </a>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] p-8 border border-gray-100 dark:border-gray-800 shadow-sm transition-colors group">
                <p class="text-[11px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-2">Mes Commandes</p>
                <div class="flex items-center justify-between">
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white">{{ $user->commandes()->count() }}</h3>
                    <div class="w-12 h-12 bg-red-50 dark:bg-red-900/20 rounded-2xl flex items-center justify-center text-red-600 dark:text-red-400 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] p-8 border border-gray-100 dark:border-gray-800 shadow-sm transition-colors group">
                <p class="text-[11px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-2">Mes Favoris</p>
                <div class="flex items-center justify-between">
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white">{{ $user->favoris()->count() }}</h3>
                    <div class="w-12 h-12 bg-orange-50 dark:bg-orange-900/20 rounded-2xl flex items-center justify-center text-orange-600 dark:text-orange-400 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div class="bg-white dark:bg-gray-900 rounded-[3rem] p-10 border border-gray-100 dark:border-gray-800 shadow-sm transition-colors">
                <div class="flex items-center justify-between mb-10">
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Dernières Commandes</h2>
                    <a href="{{ route('orders.index') }}" class="text-[11px] font-black uppercase tracking-widest text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors">Voir l'historique</a>
                </div>
                <div class="space-y-6">
                    @forelse($user->commandes->take(4) as $commande)
                        <div class="flex items-center gap-6 p-6 rounded-3xl bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all group">
                            <div class="w-16 h-16 bg-white dark:bg-gray-700 rounded-2xl flex items-center justify-center text-red-600 dark:text-red-400 shadow-sm border border-gray-100 dark:border-gray-600 group-hover:scale-105 transition-transform">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="text-sm font-black text-gray-900 dark:text-white truncate">Commande #{{ $commande->numero_commande }}</h4>
                                    <span class="text-[10px] font-black uppercase text-gray-400 dark:text-gray-500">{{ $commande->date_commande->diffForHumans() }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-bold text-gray-400 dark:text-gray-500 truncate">{{ $commande->lignes ? $commande->lignes->count() : 0 }} articles • {{ number_format($commande->montant_total, 0) }} FCFA</p>
                                    <span class="px-3 py-1 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-lg text-[9px] font-black uppercase tracking-widest">{{ $commande->statut }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-20 text-center">
                            <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-6 text-gray-200 dark:text-gray-700">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </div>
                            <p class="text-sm font-bold text-gray-400 dark:text-gray-500 italic">Vous n'avez pas encore passé de commande.</p>
                            <a href="{{ route('home') }}" class="mt-8 inline-block px-8 py-4 bg-red-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-red-700 transition-all shadow-xl shadow-red-600/20 active:scale-95">Explorer les articles</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
{{-- ==================== END DESKTOP VIEW ==================== --}}

<script>
    function copyReferralCode(code) {
        navigator.clipboard.writeText(code).then(() => {
            window.showToast('Code promo copié !', 'success');
        });
    }
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            window.showToast('Lien de parrainage copié !', 'success');
        });
    }
</script>
@endsection
