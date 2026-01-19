@extends((($user->role ?? 'client') === 'admin') ? 'layouts.admin' : 'layouts.app')

@section('content')
<div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14 py-12">
    <div class="space-y-12">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white dark:bg-gray-900 rounded-[2.5rem] p-10 border border-gray-100 dark:border-gray-800 shadow-sm transition-colors">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-gradient-to-br from-red-600 to-orange-500 rounded-[2rem] flex items-center justify-center text-white text-3xl font-black shadow-xl shadow-red-200 dark:shadow-red-900/20">
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
                @if($user->canApplyAsVendor())
                <a href="{{ route('vendor.apply') }}" class="px-8 py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-700 transition-all active:scale-95 shadow-sm">
                    Devenir Vendeur
                </a>
                @endif
            </div>
        </div>

        <!-- Stats Grid -->
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
            @if(false)
            <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] p-8 border border-gray-100 dark:border-gray-800 shadow-sm transition-colors group">
                <p class="text-[11px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-2">Wallet Parrainage</p>
                <div class="flex items-center justify-between">
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white">{{ number_format($user->referral_balance ?? 0, 0) }} <small class="text-xs">FCFA</small></h3>
                    <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-2xl flex items-center justify-center text-green-600 dark:text-green-400 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Referral & Activities Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Referral Program Card (Hidden for now) -->
            @if(false)
            <div class="bg-gray-900 dark:bg-gray-900/50 rounded-[3rem] p-10 text-white relative overflow-hidden group shadow-2xl shadow-gray-200 dark:shadow-none border border-transparent dark:border-gray-800 transition-colors">
                <div class="absolute -right-20 -top-20 w-60 h-60 bg-orange-600/10 rounded-full group-hover:scale-110 transition duration-1000"></div>
                
                <div class="relative z-10 space-y-8">
                    <div>
                        <span class="px-4 py-2 bg-orange-600 text-white rounded-full text-[10px] font-black uppercase tracking-widest">🎁 Programme Ambassadeur</span>
                        <h2 class="text-3xl font-black mt-6 tracking-tight leading-tight">Gagnez 500 FCFA pour chaque ami invité !</h2>
                        <p class="text-gray-400 font-medium mt-4 leading-relaxed">Partagez votre code avec vos proches. Dès leur première commande, vous recevez un bonus de parrainage dans votre portefeuille.</p>
                    </div>

                    <div class="bg-white/5 backdrop-blur-md rounded-3xl p-8 border border-white/10 space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Votre Code de Parrainage</label>
                            <div class="flex gap-3">
                                <div class="flex-1 bg-black/40 px-6 py-4 rounded-2xl font-black text-2xl tracking-widest text-orange-400 select-all border border-white/5">
                                    {{ $user->referral_code ?? '------' }}
                                </div>
                                <button onclick="copyReferralCode('{{ $user->referral_code }}')" class="px-6 bg-orange-600 hover:bg-orange-700 text-white rounded-2xl transition-all active:scale-95 shadow-lg shadow-orange-600/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012-2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Lien de Parrainage Rapide</label>
                            <div class="flex bg-black/20 rounded-2xl p-2 pl-6 items-center justify-between border border-white/5">
                                <span class="text-xs font-bold text-gray-400 truncate mr-4">{{ route('register', ['ref' => $user->referral_code]) }}</span>
                                <button onclick="copyToClipboard('{{ route('register', ['ref' => $user->referral_code]) }}')" class="p-3 bg-white/10 hover:bg-white/20 rounded-xl transition-all">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-6">
                        <div class="flex -space-x-3">
                            @foreach($user->referrals->take(5) as $ref)
                                <div class="w-10 h-10 rounded-full border-4 border-gray-900 bg-orange-600 flex items-center justify-center text-[10px] font-black ring-2 ring-transparent group-hover:ring-orange-600/20 transition-all">
                                    {{ substr($ref->name, 0, 1) }}
                                </div>
                            @endforeach
                            @if($user->referrals->count() > 5)
                                <div class="w-10 h-10 rounded-full border-4 border-gray-900 bg-gray-800 flex items-center justify-center text-[10px] font-black text-gray-400">
                                    +{{ $user->referrals->count() - 5 }}
                                </div>
                            @endif
                        </div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-500">
                            {{ $user->referrals->count() }} Amis parrainés
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Orders/History List -->
            <div class="bg-white dark:bg-gray-900 rounded-[3rem] p-10 border border-gray-100 dark:border-gray-800 shadow-sm transition-colors">
                <div class="flex items-center justify-between mb-10">
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Dernières Commandes</h2>
                    <a href="#" class="text-[11px] font-black uppercase tracking-widest text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors">Voir l'historique</a>
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

<script>
    function copyReferralCode(code) {
        navigator.clipboard.writeText(code).then(() => {
            alert('Code promo copié !');
        });
    }
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Lien de parrainage copié !');
        });
    }
</script>
@endsection

