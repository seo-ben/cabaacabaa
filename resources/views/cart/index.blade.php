@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-white dark:bg-gray-950 pt-16 lg:pt-32 pb-32 transition-colors duration-300">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6 mb-12 border-b border-gray-100 dark:border-gray-800 pb-8">
            <div class="space-y-2">
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Mon Panier</h1>
                <p class="text-xs font-black uppercase tracking-[0.3em] text-red-600 dark:text-red-400">
                    {{ count($cart) }} Article{{ count($cart) > 1 ? 's' : '' }} au total
                </p>
            </div>
            @if(count($cart) > 0)
            <form action="{{ route('cart.clear') }}" method="POST">
                @csrf
                <button type="submit" onclick="return confirm('Vider tout le panier ?')" 
                        class="text-[10px] font-black text-gray-400 hover:text-red-500 uppercase tracking-widest transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Vider le panier
                </button>
            </form>
            @endif
        </div>

        @if(count($cart) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <!-- Items List -->
            <div class="lg:col-span-8">
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($cart as $id => $item)
                    <div id="cart-item-{{ $id }}" class="py-8 first:pt-0 group">
                        <div class="flex gap-6">
                            <!-- Image -->
                            <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-3xl overflow-hidden bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shrink-0">
                                <img src="{{ isset($item['image']) && $item['image'] ? asset('storage/' . $item['image']) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=200' }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            </div>
                            
                            <!-- Info -->
                            <div class="flex-1 flex flex-col min-w-0">
                                <div class="flex justify-between items-start gap-4">
                                    <div class="min-w-0">
                                        <h3 class="text-sm sm:text-lg font-black text-gray-900 dark:text-white truncate mb-1">{{ $item['name'] }}</h3>
                                        <div class="flex items-center gap-3">
                                            <span class="text-[9px] font-black text-red-500 uppercase tracking-widest">{{ $item['vendor_name'] ?? 'Établissement' }}</span>
                                            <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-700"></span>
                                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ number_format($item['price'], 0) }} FCFA / u</span>
                                        </div>
                                    </div>
                                    <button class="cart-remove p-2 text-gray-300 hover:text-red-500 bg-gray-50 dark:bg-gray-900 rounded-xl transition-all active:scale-90" data-id="{{ $id }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>

                                <!-- Options List (Very Compact) -->
                                @if(isset($item['options']) && !empty($item['options']))
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach($item['options'] as $optionGroup)
                                        @foreach($optionGroup['variantes'] as $variant)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-50 dark:bg-gray-800 text-[8px] font-bold text-gray-500 uppercase tracking-tighter">
                                            {{ $variant['nom'] }}@if(isset($variant['quantite']) && $variant['quantite'] > 1) (x{{ $variant['quantite'] }})@endif
                                        </span>
                                        @endforeach
                                    @endforeach
                                    <button class="edit-options-btn text-[8px] font-black text-blue-500 underline uppercase tracking-widest ml-1" 
                                            data-id="{{ $id }}" data-plat-id="{{ $item['id'] }}">Modifier</button>
                                </div>
                                @endif

                                <!-- Price & Controls -->
                                <div class="mt-auto pt-4 flex items-center justify-between">
                                    <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-900 p-1 rounded-2xl border border-gray-100 dark:border-gray-800">
                                        <button class="qty-decrease w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-500 transition-colors" data-id="{{ $id }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
                                        </button>
                                        <span class="cart-quantity w-8 text-center text-sm font-black text-gray-900 dark:text-white" data-id="{{ $id }}">{{ $item['quantity'] }}</span>
                                        <button class="qty-increase w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-500 transition-colors" data-id="{{ $id }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                        </button>
                                    </div>
                                    <p class="text-xl font-black text-gray-900 dark:text-white tracking-tighter">
                                        <span id="item-total-{{ $id }}">{{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }}</span>
                                        <small class="text-[9px] text-gray-400 uppercase tracking-widest ml-1">FCFA</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div class="lg:col-span-4">
                <div class="sticky top-32 space-y-6">
                    <div class="bg-gray-900 dark:bg-gray-900 rounded-[2.5rem] p-8 sm:p-10 text-white shadow-2xl relative overflow-hidden">
                        <!-- Abstract Decoration -->
                        <div class="absolute -top-12 -right-12 w-40 h-40 bg-red-600/20 blur-[80px] rounded-full"></div>
                        
                        <h3 class="text-lg font-black uppercase tracking-widest border-b border-white/10 pb-6 mb-8">Détails</h3>
                        
                        <div class="space-y-6">
                            <div class="flex justify-between text-white/50 text-[10px] font-black uppercase tracking-widest">
                                <span>Sous-total</span>
                                <span class="text-white"><span class="cart-total-val">{{ number_format($total, 0, ',', ' ') }}</span> FCFA</span>
                            </div>
                            
                            @if(session()->has('coupon'))
                            @php $discount = session('coupon')['type'] === 'percentage' ? ($total * (session('coupon')['valeur'] / 100)) : session('coupon')['valeur']; @endphp
                            <div class="flex justify-between text-green-400 text-[10px] font-black uppercase tracking-widest">
                                <span>Réduction ({{ session('coupon')['code'] }})</span>
                                <span>- {{ number_format($discount, 0, ',', ' ') }} FCFA</span>
                            </div>
                            @endif

                            <div class="pt-8 border-t border-white/10">
                                <p class="text-[10px] font-black uppercase tracking-widest text-red-500 mb-1">Total à payer</p>
                                <p class="text-5xl font-black tracking-tighter text-white">
                                    <span id="final-total-val">{{ number_format(session()->has('coupon') ? $total - $discount : $total, 0, ',', ' ') }}</span>
                                    <span class="text-sm font-bold text-gray-500 ml-1">FCFA</span>
                                </p>
                            </div>

                            <a href="{{ route('checkout.index') }}" class="block w-full py-6 bg-red-600 hover:bg-red-700 text-white rounded-3xl font-black text-xs uppercase tracking-[0.2em] transition-all shadow-xl shadow-red-600/20 active:scale-95 text-center mt-8">
                                Passer à la caisse
                            </a>
                        </div>
                    </div>

                    <!-- Coupon Input -->
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-3xl p-8 border border-gray-100 dark:border-gray-800">
                        <h4 class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4">Avez-vous un code promo ?</h4>
                        @if(session()->has('coupon'))
                        <div class="flex items-center gap-3 text-green-600 dark:text-green-400 text-xs font-black uppercase tracking-widest">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            Code appliqué !
                        </div>
                        @else
                        <div class="flex gap-2">
                            <input type="text" id="coupon_code" placeholder="Entrez le code..." 
                                   class="flex-1 px-5 py-3 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl text-[13px] font-bold text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-red-500 outline-none transition-all">
                            <button id="apply_coupon" class="px-5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white text-[9px] font-black uppercase tracking-widest rounded-2xl active:scale-95">OK</button>
                        </div>
                        <div id="coupon-message" class="hidden mt-3 text-[9px] font-black uppercase tracking-widest"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center py-32 text-center">
            <div class="w-24 h-24 bg-gray-50 dark:bg-gray-900 rounded-[2rem] flex items-center justify-center text-gray-200 dark:text-gray-700 mb-8 border border-gray-100 dark:border-gray-800">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-4">Votre panier est vide</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium max-w-xs mx-auto mb-10">Il semble que vous n'ayez pas encore ajouté de délices à votre sélection.</p>
            <a href="{{ route('explore.plats') }}" class="px-10 py-5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white rounded-2xl font-black uppercase tracking-[.2em] text-[10px] shadow-2xl active:scale-95 transition-all">
                Voir les menus
            </a>
        </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
    let isQtyUpdating = false;

    $(document).off('click', '.qty-increase').on('click', '.qty-increase', function(e) {
        e.preventDefault();
        if (isQtyUpdating) return;
        
        var id = $(this).data("id");
        var qtyDisplay = $(".cart-quantity[data-id='" + id + "']");
        var currentQty = parseInt(qtyDisplay.text()) || 0;
        var newQty = currentQty + 1;
        
        qtyDisplay.text(newQty);
        updateCart(id, newQty);
    });

    $(document).off('click', '.qty-decrease').on('click', '.qty-decrease', function(e) {
        e.preventDefault();
        if (isQtyUpdating) return;
        
        var id = $(this).data("id");
        var qtyDisplay = $(".cart-quantity[data-id='" + id + "']");
        var currentQty = parseInt(qtyDisplay.text()) || 0;
        
        if (currentQty > 1) {
            var newQty = currentQty - 1;
            qtyDisplay.text(newQty);
            updateCart(id, newQty);
        }
    });

    function updateCart(id, quantity) {
        if (quantity < 1) return;
        isQtyUpdating = true;
        
        $.ajax({
            url: '{{ route('cart.update') }}',
            method: "PATCH",
            data: { _token: '{{ csrf_token() }}', id: id, quantity: quantity },
            success: function (response) {
                $("#item-total-" + id).text(response.item_total);
                $(".cart-total-val").text(response.cart_total);
                $("#final-total-val").text(response.final_total || response.cart_total);
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: response.cart_count } }));
            },
            complete: function() {
                isQtyUpdating = false;
            }
        });
    }

    function removeCartItem(id) {
        $.ajax({
            url: '{{ route('cart.remove') }}',
            method: "DELETE",
            data: { _token: '{{ csrf_token() }}', id: id },
            success: function (response) {
                $("#cart-item-" + id).fadeOut(300, function() { $(this).remove(); if(response.cart_count == 0) { window.location.reload(); } });
                $(".cart-total-val").text(response.cart_total);
                $("#final-total-val").text(response.final_total || response.cart_total);
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: response.cart_count } }));
            }
        });
    }

    $(".cart-remove").click(function(e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        if(confirm("Désouhaitez-vous retirer cet article ?")) { removeCartItem(id); }
    });

    $("#apply_coupon").click(function() {
        var code = $("#coupon_code").val();
        if(!code) return;
        var btn = $(this);
        btn.prop('disabled', true).text('...');
        $.ajax({
            url: '{{ route('cart.coupon') }}',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', code: code },
            success: function(response) {
                var message = $("#coupon-message");
                message.removeClass('hidden text-red-500 text-green-500');
                if(response.success) {
                    message.addClass('text-green-500').text("Succès !").show();
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    message.addClass('text-red-500').text(response.error).show();
                    btn.prop('disabled', false).text('OK');
                }
            }
        });
    });

    $(".edit-options-btn").click(function() {
        var platId = $(this).data("plat-id");
        var cartId = $(this).data("id");
        window.location.href = '{{ route("explore.plats") }}?edit_cart=' + cartId + '&plat_id=' + platId;
    });
</script>
@endsection