@extends('layouts.app')

@section('content')
@section('content')
<div class="min-h-screen bg-[#FDFCFB] dark:bg-gray-950 pt-20 pb-32 transition-colors duration-300 overflow-x-hidden w-full">
    <div class="max-w-[1920px] mx-auto px-3 sm:px-10 lg:px-14 w-full">
        
        <!-- Header avec actions -->
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
            
            <!-- Articles Section - 8 colonnes -->
            <div class="lg:col-span-8 space-y-4 lg:space-y-6 w-full">
                @foreach($cart as $id => $item)
                <div id="cart-item-{{ $id }}" class="group relative bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-xl hover:shadow-gray-200/40 dark:hover:shadow-black/20 transition-all overflow-hidden w-full">
                    
                    <!-- Bouton supprimer (Position absolue pour mobile) -->
                    <button class="cart-remove absolute top-1 right-1 sm:top-2 sm:right-2 p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all z-20" data-id="{{ $id }}">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>

                    <div class="p-4 sm:p-6">
                        <div class="flex gap-3 sm:gap-6">
                            <!-- Image -->
                            <div class="w-20 h-20 sm:w-32 sm:h-32 rounded-xl overflow-hidden bg-gray-50 dark:bg-gray-800 flex-shrink-0">
                                <img src="{{ isset($item['image']) && $item['image'] ? asset('storage/' . $item['image']) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=200' }}" 
                                     class="w-full h-full object-cover">
                            </div>

                            <!-- Informations principales -->
                            <div class="flex-1 flex flex-col justify-center min-w-0">
                                <!-- Nom et vendeur -->
                                <div class="mb-2 pr-6 sm:pr-0">
                                    <h3 class="text-sm sm:text-xl font-black text-gray-900 dark:text-white leading-snug mb-1 truncate sm:whitespace-normal">{{ $item['name'] }}</h3>
                                    <p class="text-[9px] sm:text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest truncate sm:whitespace-normal">{{ $item['vendor_name'] ?? 'Établissement' }}</p>
                                </div>
                                
                                <!-- Options -->
                                @if(isset($item['options']) && !empty($item['options']))
                                <div class="mb-3 flex flex-wrap items-center gap-x-2 gap-y-2">
                                    @foreach($item['options'] as $optionGroup)
                                        <div class="flex flex-wrap items-center gap-1 text-[9px] sm:text-xs bg-gray-50 dark:bg-gray-800/50 px-2 py-1 rounded-lg max-w-full">
                                            <span class="font-bold text-gray-500 dark:text-gray-400">{{ $optionGroup['groupe'] }}:</span>
                                            @foreach($optionGroup['variantes'] as $variant)
                                                <span class="text-gray-700 dark:text-gray-300 font-semibold truncate max-w-[80px]">
                                                    {{ $variant['nom'] }}
                                                    @if(isset($variant['quantite']) && $variant['quantite'] > 1)
                                                        <span class="text-gray-400">x{{ $variant['quantite'] }}</span>
                                                    @endif
                                                </span>
                                                @if(!$loop->last)<span class="text-gray-300">,</span>@endif
                                            @endforeach
                                        </div>
                                    @endforeach
                                    
                                    <button class="edit-options-btn ml-auto text-[9px] font-bold text-indigo-500 hover:text-indigo-600 uppercase tracking-wider bg-indigo-50 dark:bg-indigo-900/20 px-2 py-1 rounded"
                                            data-id="{{ $id }}"
                                            data-plat-id="{{ $item['id'] }}">
                                        Modifier
                                    </button>
                                </div>
                                @endif

                                <!-- Contrôles Mobile / Desktop -->
                                <div class="mt-auto pt-3 border-t border-gray-50 dark:border-gray-800">
                                    <div class="flex flex-wrap items-end justify-between gap-y-3">
                                        
                                        <!-- Prix Unitaire & Total Mobile -->
                                        <div class="flex flex-col">
                                            <div class="text-[9px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-0.5 sm:hidden">Total</div>
                                            <p class="text-base sm:text-xl font-black text-orange-600 dark:text-orange-400 leading-none">
                                                <span id="item-total-{{ $id }}">{{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }}</span> <small class="text-[9px] font-bold">FCFA</small>
                                            </p>
                                            <p class="hidden sm:block text-[10px] text-gray-400 mt-1">{{ number_format($item['price'], 0) }} FCFA / unité</p>
                                        </div>

                                        <!-- Quantité -->
                                        <div class="flex items-center gap-2 sm:gap-3 bg-gray-50 dark:bg-gray-800 px-2 py-1 sm:px-3 sm:py-1.5 rounded-xl ml-auto sm:ml-0">
                                            <button class="qty-decrease w-6 h-6 sm:w-7 sm:h-7 flex items-center justify-center text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors hover:bg-white dark:hover:bg-gray-700 rounded-lg" data-id="{{ $id }}">
                                                <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                            </button>
                                            <span class="cart-quantity w-5 sm:w-6 text-center text-xs sm:text-sm font-black text-gray-900 dark:text-white" data-id="{{ $id }}">{{ $item['quantity'] }}</span>
                                            <button class="qty-increase w-6 h-6 sm:w-7 sm:h-7 flex items-center justify-center text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors hover:bg-white dark:hover:bg-gray-700 rounded-lg" data-id="{{ $id }}">
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

            <!-- Summary Section - 4 colonnes sticky -->
            <div class="lg:col-span-4 w-full">
                <div class="sticky top-8 space-y-6">
                    
                    <!-- Récapitulatif principal -->
                    <div class="bg-gray-900 dark:bg-gray-800 rounded-2xl p-5 sm:p-10 text-white space-y-6 sm:space-y-8 shadow-2xl shadow-gray-200 dark:shadow-none border border-transparent dark:border-gray-700 overflow-hidden w-full">
                        <h3 class="text-lg sm:text-xl font-black border-b border-white/10 pb-4 sm:pb-6 uppercase tracking-widest break-words">Récapitulatif</h3>
                        
                        <div class="space-y-4">
                            <div class="flex flex-wrap justify-between items-center text-white/60 gap-2">
                                <span class="text-[10px] sm:text-xs font-black uppercase tracking-widest">Sous-total</span>
                                <span class="font-bold whitespace-nowrap"><span class="cart-total-val">{{ number_format($total, 0, ',', ' ') }}</span> FCFA</span>
                            </div>
                            
                            @if(session()->has('coupon'))
                            <div class="flex justify-between text-green-400">
                                <span class="text-xs font-black uppercase tracking-widest">Réduction</span>
                                @php
                                    $discount = session('coupon')['type'] === 'percentage' 
                                        ? ($total * (session('coupon')['valeur'] / 100)) 
                                        : session('coupon')['valeur'];
                                @endphp
                                <span class="font-bold">- {{ number_format($discount, 0, ',', ' ') }} FCFA</span>
                            </div>
                            @else
                            <div id="additional-discount-row" class="hidden flex justify-between text-green-400">
                                <span class="text-xs font-black uppercase tracking-widest">Réduction</span>
                                <span id="additional-discount-val" class="font-bold">0 FCFA</span>
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

                        <a href="{{ route('checkout.index') }}" class="block w-full text-center py-5 bg-orange-600 text-white rounded-xl font-black text-sm uppercase tracking-widest hover:bg-orange-700 transition-all shadow-xl shadow-orange-600/20 dark:shadow-none active:scale-95">
                            Passer la commande
                        </a>
                    </div>

                    <!-- Coupon Section -->
                    <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 border border-gray-100 dark:border-gray-800 space-y-6 shadow-sm">
                        <h4 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-4">Code Promo</h4>
                        
                        @if(session()->has('coupon'))
                        <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-900/30 rounded-xl">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-green-500 rounded-lg flex items-center justify-center text-white">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-[10px] font-black text-green-700 dark:text-green-400 uppercase tracking-widest">{{ session('coupon')['code'] }} appliqué</span>
                            </div>
                        </div>
                        @else
                        <div class="flex gap-2">
                            <input type="text" id="coupon_code" placeholder="Votre code..." 
                                   class="flex-1 px-5 py-4 bg-gray-50 dark:bg-gray-800 border border-transparent rounded-xl text-[13px] font-bold text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-orange-500 transition-all outline-none">
                            <button id="apply_coupon" class="px-6 bg-gray-900 dark:bg-gray-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-orange-600 hover:text-white transition-all active:scale-95 disabled:opacity-50">
                                Appliquer
                            </button>
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
        <!-- État vide -->
        <div class="py-32 flex flex-col items-center text-center space-y-8 animate-in fade-in zoom-in duration-700">
            <div class="w-40 h-40 bg-gray-50 dark:bg-gray-900 rounded-2xl flex items-center justify-center text-gray-200 dark:text-gray-800">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <div>
                <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-2">Votre panier est vide</h2>
                <p class="text-gray-500 dark:text-gray-400 font-medium">Laissez-vous tenter par nos articles disponibles !</p>
            </div>
            <a href="{{ route('explore.plats') }}" class="px-10 py-5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white rounded-xl font-black uppercase tracking-widest text-[11px] hover:bg-orange-600 dark:hover:bg-gray-200 shadow-xl transition-all active:scale-95">Explorer les articles</a>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    // Fonction pour mettre à jour la quantité
    function updateCart(id, quantity) {
        if(quantity < 1) return;

        $.ajax({
            url: '{{ route('cart.update') }}',
            method: "PATCH",
            data: {
                _token: '{{ csrf_token() }}', 
                id: id, 
                quantity: quantity
            },
            success: function (response) {
                $("#item-total-" + id).text(response.item_total);
                $(".cart-total-val").text(response.cart_total);
                $("#final-total-val").text(response.final_total || response.cart_total);
                $("#cart-item-count-badge").text(response.cart_count + " Articles");
                
                // Dispatch event for AlpineJS to update cart count
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: response.cart_count } }));
            }
        });
    }

    // Augmenter la quantité
    $(".qty-increase").click(function() {
        var id = $(this).data("id");
        var qtyDisplay = $(".cart-quantity[data-id='" + id + "']");
        var newQty = parseInt(qtyDisplay.text()) + 1;
        qtyDisplay.text(newQty);
        updateCart(id, newQty);
    });

    // Diminuer la quantité
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

    // Supprimer un article
    $(".cart-remove").click(function (e) {
        e.preventDefault();
        var ele = $(this);
        var id = ele.attr("data-id");

        if(confirm("Retirer cet article ?")) {
            $.ajax({
                url: '{{ route('cart.remove') }}',
                method: "DELETE",
                data: {
                    _token: '{{ csrf_token() }}', 
                    id: id
                },
                success: function (response) {
                    $("#cart-item-" + id).fadeOut(300, function() {
                        $(this).remove();
                        $(".cart-total-val").text(response.cart_total);
                        $("#final-total-val").text(response.final_total || response.cart_total);
                        $("#cart-item-count-badge").text(response.cart_count + " Articles");
                        
                        // Dispatch event for AlpineJS to update cart count
                        window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: response.cart_count } }));

                        if(response.cart_count == 0) {
                            window.location.reload();
                        }
                    });
                }
            });
        }
    });

    // Appliquer un coupon
    $("#apply_coupon").click(function() {
        var code = $("#coupon_code").val();
        if(!code) return;

        var btn = $(this);
        btn.prop('disabled', true).text('...');

        $.ajax({
            url: '{{ route('cart.coupon') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                code: code
            },
            success: function(response) {
                var message = $("#coupon-message");
                message.removeClass('hidden text-red-500 text-green-500');
                
                if(response.success) {
                    message.addClass('text-green-500').text(response.message).show();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    message.addClass('text-red-500').text(response.error).show();
                    btn.prop('disabled', false).text('Appliquer');
                }
            },
            error: function() {
                $("#coupon-message").removeClass('hidden').addClass('text-red-500').text('Une erreur est survenue.').show();
                btn.prop('disabled', false).text('Appliquer');
            }
        });
    });

    // Modifier les options d'un article
    $(".edit-options-btn").click(function() {
        var platId = $(this).data("plat-id");
        var cartId = $(this).data("id");
        
        // Sauvegarder l'ID du panier dans sessionStorage pour le retrouver après modification
        sessionStorage.setItem('editingCartItem', cartId);
        
        // Rediriger vers la page des plats avec un paramètre pour ouvrir le modal
        window.location.href = '{{ route("explore.plats") }}?edit_cart=' + cartId + '&plat_id=' + platId;
    });
</script>
@endsection