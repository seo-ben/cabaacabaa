@extends('layouts.app')

@section('content')

{{-- MOBILE --}}
<div class="block lg:hidden bg-gray-50 dark:bg-slate-950 min-h-screen pb-28">

    {{-- Header --}}
    <div class="relative bg-gradient-to-br from-gray-900 via-slate-800 to-gray-900 overflow-hidden px-5 pt-10 pb-14">
        <div class="absolute -top-8 -right-8 w-32 h-32 bg-pink-500/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-red-500/10 rounded-full blur-2xl"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-red-500 rounded-2xl flex items-center justify-center shadow-xl shrink-0">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                </div>
                <div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Ma liste</p>
                    <h1 class="text-xl font-black text-white leading-tight">Mes Favoris</h1>
                </div>
            </div>
            <div class="text-right">
                <div class="text-2xl font-black text-white">{{ $favoris->count() }}</div>
                <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest">boutique{{ $favoris->count() > 1 ? 's' : '' }}</div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="px-4 -mt-4 space-y-3">

        @if($favoris->count() === 0)
            {{-- Empty state --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-10 text-center border border-gray-100 dark:border-slate-800 shadow-sm mt-4">
                <div class="w-16 h-16 bg-pink-50 dark:bg-pink-900/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-pink-300 dark:text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <h3 class="text-lg font-black text-gray-900 dark:text-white mb-2">Aucun favori encore</h3>
                <p class="text-xs text-gray-400 font-medium leading-relaxed mb-5">Explorez nos vendeurs et ajoutez vos préférés en cliquant sur le ❤️</p>
                <a href="{{ route('explore') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-pink-600 to-red-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-pink-500/25 active:scale-95 transition-all">
                    Découvrir les vendeurs →
                </a>
            </div>

        @else
            @foreach($favoris as $favori)
                @if($favori->vendeur)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden fav-card" data-id="{{ $favori->vendeur->id_vendeur }}">

                    {{-- Cover image --}}
                    <div class="relative h-32 bg-gray-100 dark:bg-slate-800 overflow-hidden">
                        @if($favori->vendeur->photo_couverture)
                            <img src="{{ asset('storage/' . $favori->vendeur->photo_couverture) }}"
                                 alt="{{ $favori->vendeur->nom_boutique }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-pink-400 to-red-500"></div>
                        @endif
                        {{-- Gradient overlay --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>

                        {{-- Status badge --}}
                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest
                            {{ $favori->vendeur->est_ouvert ? 'bg-green-500 text-white' : 'bg-gray-700/80 text-gray-300' }}">
                            {{ $favori->vendeur->est_ouvert ? 'Ouvert' : 'Fermé' }}
                        </span>

                        {{-- Remove fav button --}}
                        <button onclick="rmFav({{ $favori->vendeur->id_vendeur }}, this)"
                                class="absolute top-3 right-3 w-8 h-8 bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg active:scale-90 transition-transform text-pink-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        </button>

                        {{-- Logo --}}
                        <div class="absolute -bottom-5 left-4">
                            <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-xl shadow-lg border-2 border-white dark:border-slate-900 overflow-hidden">
                                @if($favori->vendeur->logo)
                                    <img src="{{ asset('storage/' . $favori->vendeur->logo) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-pink-500 to-red-500 flex items-center justify-center text-white font-black text-sm">{{ substr($favori->vendeur->nom_boutique ?? 'V', 0, 1) }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="px-4 pt-8 pb-4">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h3 class="text-sm font-black text-gray-900 dark:text-white leading-tight">{{ $favori->vendeur->nom_boutique }}</h3>
                            @if($favori->vendeur->note_moyenne)
                                <div class="flex items-center gap-1 bg-yellow-50 dark:bg-yellow-900/20 px-2 py-0.5 rounded-lg shrink-0">
                                    <svg class="w-3 h-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span class="text-[10px] font-black text-yellow-600 dark:text-yellow-400">{{ number_format($favori->vendeur->note_moyenne, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        @if($favori->vendeur->categorie)
                            <p class="text-[10px] font-bold text-gray-400 mb-2">{{ $favori->vendeur->categorie->nom_categorie ?? '' }}</p>
                        @endif
                        @if($favori->vendeur->adresse)
                            <div class="flex items-center gap-1.5 text-gray-400 mb-3">
                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span class="text-[10px] font-medium truncate">{{ $favori->vendeur->adresse }}</span>
                            </div>
                        @endif
                        <a href="{{ route('vendor.show', ['id' => $favori->vendeur->id_vendeur, 'slug' => $favori->vendeur->slug]) }}"
                           class="block w-full py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl text-[10px] font-black uppercase tracking-widest text-center active:scale-[0.97] transition-all">
                            Voir la boutique →
                        </a>
                    </div>
                </div>
                @endif
            @endforeach

            {{-- CTA discover more --}}
            <a href="{{ route('explore') }}"
               class="flex items-center justify-center gap-2 py-4 bg-white dark:bg-slate-900 rounded-2xl border border-dashed border-gray-200 dark:border-slate-700 text-[10px] font-black text-gray-400 uppercase tracking-widest active:scale-[0.98] transition-all mt-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Découvrir plus de vendeurs
            </a>
        @endif
    </div>
</div>
{{-- END MOBILE --}}


{{-- DESKTOP --}}
<div class="hidden lg:block max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-14 py-12">
    <div class="space-y-12">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white dark:bg-gray-900 rounded-[2.5rem] p-10 border border-gray-100 dark:border-gray-800 shadow-sm transition-colors">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-gradient-to-br from-pink-600 to-red-500 rounded-[1.5rem] flex items-center justify-center text-white text-3xl shadow-xl shadow-pink-200 dark:shadow-pink-900/20">
                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Mes Favoris</h1>
                    <p class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-1">{{ $favoris->count() }} vendeur{{ $favoris->count() > 1 ? 's' : '' }} en favoris</p>
                </div>
            </div>
            <a href="{{ route('explore') }}" class="px-8 py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-pink-600 dark:hover:bg-pink-600 dark:hover:text-white transition-all active:scale-95 shadow-lg">Découvrir plus de vendeurs</a>
        </div>

        @if($favoris->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach($favoris as $favori)
                @if($favori->vendeur)
                <div class="group bg-white dark:bg-gray-900 rounded-[2rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden transition-all hover:shadow-xl hover:-translate-y-1 fav-card" data-id="{{ $favori->vendeur->id_vendeur }}">
                    <div class="relative h-48 bg-gray-100 dark:bg-gray-800 overflow-hidden">
                        @if($favori->vendeur->photo_couverture)
                            <img src="{{ asset('storage/' . $favori->vendeur->photo_couverture) }}" alt="{{ $favori->vendeur->nom_boutique }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-pink-500 to-red-500 flex items-center justify-center"><svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
                        @endif
                        @if($favori->vendeur->est_ouvert)
                            <span class="absolute top-4 left-4 px-3 py-1.5 bg-green-500 text-white rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg">Ouvert</span>
                        @else
                            <span class="absolute top-4 left-4 px-3 py-1.5 bg-gray-500 text-white rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg">Fermé</span>
                        @endif
                        <button onclick="rmFav({{ $favori->vendeur->id_vendeur }}, this)" class="absolute top-4 right-4 w-10 h-10 bg-white dark:bg-gray-900 rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform text-pink-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        </button>
                        <div class="absolute -bottom-8 left-6">
                            <div class="w-16 h-16 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border-4 border-white dark:border-gray-900 overflow-hidden">
                                @if($favori->vendeur->logo)<img src="{{ asset('storage/' . $favori->vendeur->logo) }}" class="w-full h-full object-cover">
                                @else<div class="w-full h-full bg-gradient-to-br from-pink-500 to-red-500 flex items-center justify-center text-white text-xl font-black">{{ substr($favori->vendeur->nom_boutique ?? 'V', 0, 1) }}</div>@endif
                            </div>
                        </div>
                    </div>
                    <div class="p-6 pt-12">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-black text-gray-900 dark:text-white leading-tight">{{ $favori->vendeur->nom_boutique }}</h3>
                                @if($favori->vendeur->categorie)<p class="text-xs font-bold text-gray-400 dark:text-gray-500 mt-1">{{ $favori->vendeur->categorie->nom_categorie ?? '' }}</p>@endif
                            </div>
                            @if($favori->vendeur->note_moyenne)
                            <div class="flex items-center gap-1 bg-yellow-50 dark:bg-yellow-900/20 px-2 py-1 rounded-lg">
                                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span class="text-xs font-black text-yellow-600 dark:text-yellow-400">{{ number_format($favori->vendeur->note_moyenne, 1) }}</span>
                            </div>
                            @endif
                        </div>
                        @if($favori->vendeur->adresse)
                        <div class="flex items-center gap-2 text-gray-400 dark:text-gray-500 mb-4"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span class="text-xs font-medium truncate">{{ $favori->vendeur->adresse }}</span></div>
                        @endif
                        <a href="{{ route('vendor.show', ['id' => $favori->vendeur->id_vendeur, 'slug' => $favori->vendeur->slug]) }}" class="block w-full py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl text-center text-[11px] font-black uppercase tracking-widest hover:bg-pink-600 dark:hover:bg-pink-600 dark:hover:text-white transition-all active:scale-95">Voir la boutique</a>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
        @else
        <div class="bg-white dark:bg-gray-900 rounded-[3rem] p-20 border border-gray-100 dark:border-gray-800 shadow-sm text-center">
            <div class="w-24 h-24 bg-pink-50 dark:bg-pink-900/20 rounded-3xl flex items-center justify-center mx-auto mb-8"><svg class="w-12 h-12 text-pink-300 dark:text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></div>
            <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-4">Aucun favori pour le moment</h2>
            <p class="text-gray-400 dark:text-gray-500 font-medium mb-8 max-w-md mx-auto">Explorez nos vendeurs et ajoutez vos préférés en cliquant sur le cœur.</p>
            <a href="{{ route('explore') }}" class="inline-block px-10 py-5 bg-pink-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-pink-700 transition-all shadow-xl shadow-pink-600/20 active:scale-95">Découvrir les vendeurs</a>
        </div>
        @endif
    </div>
</div>
{{-- END DESKTOP --}}

<script>
function rmFav(vendorId, btn) {
    fetch(`/favoris/toggle/${vendorId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
    })
    .then(r => r.json())
    .then(data => {
        if (data.action === 'removed') {
            const card = btn.closest('.fav-card');
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.9)';
            setTimeout(() => { card.remove(); if (!document.querySelector('.fav-card')) location.reload(); }, 300);
            if (window.showToast) window.showToast('Retiré des favoris', 'success');
        }
    })
    .catch(() => { if (window.showToast) window.showToast('Une erreur est survenue', 'error'); });
}
</script>
@endsection
