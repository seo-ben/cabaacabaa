@extends('layouts.app')

@section('content')

{{-- ================= MOBILE VIEW (< lg) ================= --}}
<main class="block lg:hidden bg-gray-50 dark:bg-slate-950 min-h-screen font-sans pb-24">

    {{-- Sticky Header --}}
    <div class="sticky top-0 z-30 bg-white/95 dark:bg-slate-950/95 backdrop-blur-xl border-b border-gray-100 dark:border-slate-800/50">
        <div class="flex items-center justify-between px-4 py-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Mon Panier</h1>
                <span id="cart-item-count-badge-mobile" class="text-[10px] font-black text-orange-500 uppercase tracking-widest">{{ count($cart) }} article{{ count($cart) > 1 ? 's' : '' }}</span>
            </div>
            @if(count($cart) > 0)
            <form action="{{ route('cart.clear') }}" method="POST">
                @csrf
                <button type="submit" onclick="return confirm('Vider tout le panier ?')" class="text-[10px] font-black text-red-400 uppercase tracking-widest px-3 py-2 bg-red-50 dark:bg-red-900/20 rounded-xl active:scale-95 transition-transform">
                    Vider
                </button>
            </form>
            @endif
        </div>
    </div>

    @if(count($cart) > 0)

    {{-- Cart Items --}}
    <section class="px-4 pt-4 space-y-3">
        @foreach($cart as $id => $item)
        <div id="cart-item-mobile-{{ $id }}" class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden border border-gray-100 dark:border-slate-800 shadow-sm">
            <div class="flex gap-3 p-3">
                {{-- Image --}}
                <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-50 dark:bg-slate-800 flex-shrink-0">
                    <img src="{{ isset($item['image']) && $item['image'] ? asset('storage/' . $item['image']) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=200' }}" class="w-full h-full object-cover">
                </div>
                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2 mb-1">
                        <div class="min-w-0">
                            <h3 class="text-sm font-black text-gray-900 dark:text-white truncate">{{ $item['name'] }}</h3>
                            <p class="text-[9px] font-black text-orange-500 uppercase tracking-wider truncate">{{ $item['vendor_name'] ?? 'Établissement' }}</p>
                        </div>
                        <button class="cart-remove-mobile shrink-0 w-7 h-7 bg-red-50 dark:bg-red-900/20 text-red-400 rounded-lg flex items-center justify-center active:scale-90 transition-transform" data-id="{{ $id }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    {{-- Options --}}
                    @if(isset($item['options']) && !empty($item['options']))
                    <div class="flex flex-wrap gap-1 mb-2">
                        @foreach($item['options'] as $optionGroup)
                            @foreach($optionGroup['variantes'] as $variant)
                            <span class="text-[8px] font-bold bg-gray-100 dark:bg-slate-800 text-gray-500 px-2 py-0.5 rounded-md">{{ $variant['nom'] }}@if(isset($variant['quantite']) && $variant['quantite'] > 1) x{{ $variant['quantite'] }}@endif</span>
                            @endforeach
                        @endforeach
                    </div>
                    @endif
                    {{-- Price & Qty --}}
                    <div class="flex items-center justify-between">
                        <p class="text-base font-black text-orange-600 dark:text-orange-400">
                            <span id="item-total-{{ $id }}">{{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }}</span> <small class="text-[9px] font-bold">FCFA</small>
                        </p>
                        <div class="flex items-center gap-2 bg-gray-100 dark:bg-slate-800 px-2 py-1 rounded-xl">
                            <button class="qty-decrease w-6 h-6 flex items-center justify-center text-gray-500 hover:text-red-600 transition-colors" data-id="{{ $id }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
                            </button>
                            <span class="cart-quantity w-5 text-center text-xs font-black text-gray-900 dark:text-white" data-id="{{ $id }}">{{ $item['quantity'] }}</span>
                            <button class="qty-increase w-6 h-6 flex items-center justify-center text-gray-500 hover:text-red-600 transition-colors" data-id="{{ $id }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </section>

    {{-- Promo Code Section --}}
    <section class="px-4 mt-5">
        @if(session()->has('coupon'))
        <div class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-900/30 rounded-2xl">
            <div class="w-8 h-8 bg-green-500 rounded-xl flex items-center justify-center text-white shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            </div>
            <span class="text-[10px] font-black text-green-700 dark:text-green-400 uppercase tracking-widest">{{ session('coupon')['code'] }} appliqué !</span>
        </div>
        @else
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-4">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Code Promo</p>
            <div class="flex gap-2">
                <input type="text" id="coupon_code_mobile" placeholder="Votre code..." class="flex-1 px-4 py-3 bg-gray-50 dark:bg-slate-800 border-none rounded-xl text-sm font-bold text-gray-900 dark:text-white placeholder-gray-400 outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                <button id="apply_coupon_mobile" class="px-4 bg-gray-900 dark:bg-slate-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl active:scale-95 transition-transform">OK</button>
            </div>
            <div id="coupon-message-mobile" class="hidden mt-2 text-[10px] font-black uppercase tracking-widest"></div>
        </div>
        @endif
    </section>

    {{-- Order Summary - Fixed Bottom Card --}}
    <section class="fixed bottom-16 left-0 right-0 z-40 px-4 pb-2">
        <div class="bg-gray-900 dark:bg-slate-800 rounded-2xl shadow-2xl shadow-black/20 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 border-b border-white/10">
                <span class="text-[10px] font-black text-white/60 uppercase tracking-widest">Sous-total</span>
                <span class="text-sm font-black text-white"><span class="cart-total-val">{{ number_format($total, 0, ',', ' ') }}</span> FCFA</span>
            </div>
            @if(session()->has('coupon'))
            @php $discount = session('coupon')['type'] === 'percentage' ? ($total * (session('coupon')['valeur'] / 100)) : session('coupon')['valeur']; @endphp
            <div class="flex items-center justify-between px-5 py-2 border-b border-white/10">
                <span class="text-[10px] font-black text-green-400 uppercase tracking-widest">Réduction</span>
                <span class="text-sm font-black text-green-400">- {{ number_format($discount, 0, ',', ' ') }} FCFA</span>
            </div>
            @endif
            <div class="flex items-center justify-between px-5 py-4">
                <div>
                    <p class="text-[9px] font-black text-white/50 uppercase tracking-widest">Total</p>
                    <p class="text-xl font-black text-orange-400 tracking-tighter"><span id="final-total-val">{{ number_format(session()->has('coupon') ? $total - $discount : $total, 0, ',', ' ') }}</span> <small class="text-[9px] font-bold">FCFA</small></p>
                </div>
                <a href="{{ route('checkout.index') }}" class="px-6 py-3 bg-orange-600 text-white rounded-xl font-black text-[11px] uppercase tracking-widest active:scale-95 transition-transform shadow-lg shadow-orange-600/20">
                    Commander →
                </a>
            </div>
        </div>
    </section>

    @else
    {{-- Empty State --}}
    <div class="flex flex-col items-center justify-center min-h-[60vh] px-8 text-center">
        <div class="w-24 h-24 bg-gray-100 dark:bg-slate-900 rounded-3xl flex items-center justify-center text-gray-300 dark:text-slate-700 mb-6">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
        </div>
        <h2 class="text-xl font-black text-gray-900 dark:text-white mb-2">Votre panier est vide</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-8 font-medium">Laissez-vous tenter par nos articles !</p>
        <a href="{{ route('explore.plats') }}" class="px-8 py-4 bg-gray-900 dark:bg-white dark:text-gray-900 text-white rounded-2xl font-black uppercase tracking-widest text-[11px] shadow-xl active:scale-95 transition-transform">Explorer les articles</a>
    </div>
    @endif

</main>

{{-- ================= DESKTOP VIEW (>= lg) ================= --}}
<div class="hidden lg:block min-h-screen bg-[#FDFCFB] dark:bg-gray-950 pt-20 pb-32 transition-colors duration-300 overflow-x-hidden w-full">
    <div class="max-w-[1920px] mx-auto px-3 sm:px-10 lg:px-14 w-full">
        
        <!-- Header -->
        <div class="mb-6 sm:mb-12">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
                <h1 class="text-2xl sm:text-4xl font-black text-gray-900 dark:text-white tracking-tighter">Votre Panier</h1>
                @if(count($cart) > 0)
                <form action="{{ route('cart.clear') }}" method="POST" class="self-start sm:self-auto">
                    @csrf
                    <button type="submit" onclick="return confirm('Vider tout le panier ?')" class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest hover:text-red-600 dark:hover:text-red-400 transition-colors">
                        Vider le panier
                    </button>
                </form>
                @endif
            </div>
            <div class="flex items-center gap-4">
                <span id="cart-item-count-badge" class="px-3 py-1.5 sm:px-4 sm:py-2 bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-widest border border-orange-100 dark:border-orange-900/50">
                    {{ count($cart) }} Articles
                </span>
            </div>
        </div>

        @if(count($cart) > 0)
        <div class="flex flex-col lg:grid lg:grid-cols-12 gap-6 lg:gap-8 cursor-default w-full">
            
            <!-- Articles Section -->
            <div class="lg:col-span-8 space-y-4 lg:space-y-6 w-full">
                @foreach($cart as $id => $item)
                <div id="cart-item-{{ $id }}" class="group relative bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-xl hover:shadow-gray-200/40 dark:hover:shadow-black/20 transition-all overflow-hidden w-full">
                    <button class="cart-remove absolute top-1 right-1 sm:top-2 sm:right-2 p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all z-20" data-id="{{ $id }}">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <div class="p-4 sm:p-6">
                        <div class="flex gap-3 sm:gap-6">
                            <div class="w-20 h-20 sm:w-32 sm:h-32 rounded-xl overflow-hidden bg-gray-50 dark:bg-gray-800 flex-shrink-0">
                                <img src="{{ isset($item['image']) && $item['image'] ? asset('storage/' . $item['image']) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=200' }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 flex flex-col justify-center min-w-0">
                                <div class="mb-2 pr-6 sm:pr-0">
                                    <h3 class="text-sm sm:text-xl font-black text-gray-900 dark:text-white leading-snug mb-1">{{ $item['name'] }}</h3>
                                    <p class="text-[9px] sm:text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest">{{ $item['vendor_name'] ?? 'Établissement' }}</p>
                                </div>
                                @if(isset($item['options']) && !empty($item['options']))
                                <div class="mb-3 flex flex-wrap items-center gap-x-2 gap-y-2">
                                    @foreach($item['options'] as $optionGroup)
                                        <div class="flex flex-wrap items-center gap-1 text-[9px] sm:text-xs bg-gray-50 dark:bg-gray-800/50 px-2 py-1 rounded-lg max-w-full">
                                            <span class="font-bold text-gray-500 dark:text-gray-400">{{ $optionGroup['groupe'] }}:</span>
                                            @foreach($optionGroup['variantes'] as $variant)
                                                <span class="text-gray-700 dark:text-gray-300 font-semibold truncate max-w-[80px]">{{ $variant['nom'] }}@if(isset($variant['quantite']) && $variant['quantite'] > 1)<span class="text-gray-400">x{{ $variant['quantite'] }}</span>@endif</span>
                                                @if(!$loop->last)<span class="text-gray-300">,</span>@endif
                                            @endforeach
                                        </div>
                                    @endforeach
                                    <button class="edit-options-btn ml-auto text-[9px] font-bold text-indigo-500 hover:text-indigo-600 uppercase tracking-wider bg-indigo-50 dark:bg-indigo-900/20 px-2 py-1 rounded" data-id="{{ $id }}" data-plat-id="{{ $item['id'] }}">Modifier</button>
                                </div>
                                @endif
                                <div class="mt-auto pt-3 border-t border-gray-50 dark:border-gray-800">
                                    <div class="flex flex-wrap items-end justify-between gap-y-3">
                                        <div class="flex flex-col">
                                            <p class="text-base sm:text-xl font-black text-orange-600 dark:text-orange-400 leading-none">
                                                <span id="item-total-{{ $id }}">{{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }}</span> <small class="text-[9px] font-bold">FCFA</small>
                                            </p>
                                            <p class="hidden sm:block text-[10px] text-gray-400 mt-1">{{ number_format($item['price'], 0) }} FCFA / unité</p>
                                        </div>
                                        <div class="flex items-center gap-2 sm:gap-3 bg-gray-50 dark:bg-gray-800 px-2 py-1 sm:px-3 sm:py-1.5 rounded-xl ml-auto sm:ml-0">
                                            <button class="qty-decrease w-6 h-6 sm:w-7 sm:h-7 flex items-center justify-center text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" data-id="{{ $id }}">
                                                <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                            </button>
                                            <span class="cart-quantity w-5 sm:w-6 text-center text-xs sm:text-sm font-black text-gray-900 dark:text-white" data-id="{{ $id }}">{{ $item['quantity'] }}</span>
                                            <button class="qty-increase w-6 h-6 sm:w-7 sm:h-7 flex items-center justify-center text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" data-id="{{ $id }}">
                                                <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Summary Section -->
            <div class="lg:col-span-4 w-full">
                <div class="sticky top-8 space-y-6">
                    <div class="bg-gray-900 dark:bg-gray-800 rounded-2xl p-5 sm:p-10 text-white space-y-6 sm:space-y-8 shadow-2xl border border-transparent dark:border-gray-700 overflow-hidden w-full">
                        <h3 class="text-lg sm:text-xl font-black border-b border-white/10 pb-4 sm:pb-6 uppercase tracking-widest">Récapitulatif</h3>
                        <div class="space-y-4">
                            <div class="flex flex-wrap justify-between items-center text-white/60 gap-2">
                                <span class="text-[10px] sm:text-xs font-black uppercase tracking-widest">Sous-total</span>
                                <span class="font-bold"><span class="cart-total-val">{{ number_format($total, 0, ',', ' ') }}</span> FCFA</span>
                            </div>
                            @if(session()->has('coupon'))
                            <div class="flex justify-between text-green-400">
                                <span class="text-xs font-black uppercase tracking-widest">Réduction</span>
                                @php $discount = session('coupon')['type'] === 'percentage' ? ($total * (session('coupon')['valeur'] / 100)) : session('coupon')['valeur']; @endphp
                                <span class="font-bold">- {{ number_format($discount, 0, ',', ' ') }} FCFA</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-white/60">
                                <span class="text-xs font-black uppercase tracking-widest">Livraison</span>
                                <span class="font-bold">Calculé après</span>
                            </div>
                            <div class="pt-4 border-t border-white/10 flex justify-between items-end">
                                <span class="text-xs font-black uppercase tracking-widest">Total</span>
                                <span class="text-3xl font-black text-orange-400 tracking-tighter"><span id="final-total-val">{{ number_format(session()->has('coupon') ? $total - $discount : $total, 0, ',', ' ') }}</span> <small class="text-[10px]">FCFA</small></span>
                            </div>
                        </div>
                        <a href="{{ route('checkout.index') }}" class="block w-full text-center py-5 bg-orange-600 text-white rounded-xl font-black text-sm uppercase tracking-widest hover:bg-orange-700 transition-all shadow-xl shadow-orange-600/20 active:scale-95">Passer la commande</a>
                    </div>

                    <!-- Coupon -->
                    <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 border border-gray-100 dark:border-gray-800 space-y-6 shadow-sm">
                        <h4 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Code Promo</h4>
                        @if(session()->has('coupon'))
                        <div class="flex items-center gap-2 p-3 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-900/30 rounded-xl">
                            <div class="w-6 h-6 bg-green-500 rounded-lg flex items-center justify-center text-white">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="text-[10px] font-black text-green-700 dark:text-green-400 uppercase tracking-widest">{{ session('coupon')['code'] }} appliqué</span>
                        </div>
                        @else
                        <div class="flex gap-2">
                            <input type="text" id="coupon_code" placeholder="Votre code..." class="flex-1 px-5 py-4 bg-gray-50 dark:bg-gray-800 border border-transparent rounded-xl text-[13px] font-bold text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-orange-500 transition-all outline-none">
                            <button id="apply_coupon" class="px-6 bg-gray-900 dark:bg-gray-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-orange-600 transition-all active:scale-95">Appliquer</button>
                        </div>
                        <div id="coupon-message" class="hidden text-[10px] font-black uppercase tracking-widest"></div>
                        @endif
                    </div>

                    <!-- Sécurité -->
                    <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 border border-gray-100 dark:border-gray-800 flex items-center gap-6">
                        <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-2xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest leading-relaxed">Paiement sécurisé au retrait chez le vendeur.</p>
                    </div>
                </div>
            </div>
        </div>

        @else
        <!-- État vide desktop -->
        <div class="py-32 flex flex-col items-center text-center space-y-8">
            <div class="w-40 h-40 bg-gray-50 dark:bg-gray-900 rounded-2xl flex items-center justify-center text-gray-200 dark:text-gray-800">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <div>
                <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-2">Votre panier est vide</h2>
                <p class="text-gray-500 dark:text-gray-400 font-medium">Laissez-vous tenter par nos articles disponibles !</p>
            </div>
            <a href="{{ route('explore.plats') }}" class="px-10 py-5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white rounded-xl font-black uppercase tracking-widest text-[11px] hover:bg-orange-600 shadow-xl transition-all active:scale-95">Explorer les articles</a>
        </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
    function updateCart(id, quantity) {
        if(quantity < 1) return;
        $.ajax({
            url: '{{ route('cart.update') }}',
            method: "PATCH",
            data: { _token: '{{ csrf_token() }}', id: id, quantity: quantity },
            success: function (response) {
                $("#item-total-" + id).text(response.item_total);
                $(".cart-total-val").text(response.cart_total);
                $("#final-total-val").text(response.final_total || response.cart_total);
                $("#cart-item-count-badge").text(response.cart_count + " Articles");
                $("#cart-item-count-badge-mobile").text(response.cart_count + " article" + (response.cart_count > 1 ? "s" : ""));
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: response.cart_count } }));
            }
        });
    }

    $(".qty-increase").click(function() {
        var id = $(this).data("id");
        var qtyDisplay = $(".cart-quantity[data-id='" + id + "']");
        var newQty = parseInt(qtyDisplay.text()) + 1;
        qtyDisplay.text(newQty);
        updateCart(id, newQty);
    });

    $(".qty-decrease").click(function() {
        var id = $(this).data("id");
        var qtyDisplay = $(".cart-quantity[data-id='" + id + "']");
        var currentQty = parseInt(qtyDisplay.text());
        if(currentQty > 1) {
            var newQty = currentQty - 1;
            qtyDisplay.text(newQty);
            updateCart(id, newQty);
        }
    });

    function removeCartItem(id) {
        $.ajax({
            url: '{{ route('cart.remove') }}',
            method: "DELETE",
            data: { _token: '{{ csrf_token() }}', id: id },
            success: function (response) {
                $("#cart-item-" + id).fadeOut(300, function() { $(this).remove(); });
                $("#cart-item-mobile-" + id).fadeOut(300, function() { $(this).remove(); });
                $(".cart-total-val").text(response.cart_total);
                $("#final-total-val").text(response.final_total || response.cart_total);
                $("#cart-item-count-badge").text(response.cart_count + " Articles");
                $("#cart-item-count-badge-mobile").text(response.cart_count + " article" + (response.cart_count > 1 ? "s" : ""));
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: response.cart_count } }));
                if(response.cart_count == 0) { window.location.reload(); }
            }
        });
    }

    $(".cart-remove").click(function(e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        if(confirm("Retirer cet article ?")) { removeCartItem(id); }
    });

    $(".cart-remove-mobile").click(function(e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        removeCartItem(id);
    });

    function applyCoupon(codeInputId, messageId, btnId) {
        var code = $("#" + codeInputId).val();
        if(!code) return;
        var btn = $("#" + btnId);
        btn.prop('disabled', true).text('...');
        $.ajax({
            url: '{{ route('cart.coupon') }}',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', code: code },
            success: function(response) {
                var message = $("#" + messageId);
                message.removeClass('hidden text-red-500 text-green-500');
                if(response.success) {
                    message.addClass('text-green-500').text(response.message).show();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    message.addClass('text-red-500').text(response.error).show();
                    btn.prop('disabled', false).text('Appliquer');
                }
            }
        });
    }

    $("#apply_coupon").click(function() { applyCoupon('coupon_code', 'coupon-message', 'apply_coupon'); });
    $("#apply_coupon_mobile").click(function() { applyCoupon('coupon_code_mobile', 'coupon-message-mobile', 'apply_coupon_mobile'); });

    $(".edit-options-btn").click(function() {
        var platId = $(this).data("plat-id");
        var cartId = $(this).data("id");
        window.location.href = '{{ route("explore.plats") }}?edit_cart=' + cartId + '&plat_id=' + platId;
    });
</script>
@endsection