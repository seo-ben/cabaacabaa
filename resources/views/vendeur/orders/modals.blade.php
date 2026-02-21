{{-- Order Details Modal --}}
<div x-data="{ open: false, order: null }" 
     @open-order-modal.window="open = true; order = $event.detail.order"
     x-show="open" 
     class="fixed inset-0 z-[60] overflow-y-auto" 
     style="display: none;">
    <div class="flex items-center justify-center min-h-screen p-4">
        {{-- Overlay --}}
        <div class="fixed inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm transition-opacity" @click="open = false"></div>
        
        {{-- Modal Content --}}
        <div class="relative bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden border border-gray-100 dark:border-gray-800 animate-in zoom-in-95 duration-200">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white leading-tight" x-text="'Commande #' + (order?.numero || '')"></h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1" x-text="order?.date"></p>
                    </div>
                    <button @click="open = false" class="w-10 h-10 flex items-center justify-center bg-gray-50 dark:bg-gray-800 text-gray-400 rounded-2xl hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-6">
                    {{-- Client Info --}}
                    <div class="flex items-center gap-4 p-5 bg-gray-50 dark:bg-gray-800/40 rounded-3xl">
                        <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center text-sm font-black text-gray-400 shadow-sm" x-text="order?.client?.charAt(0)"></div>
                        <div>
                            <p class="text-xs font-black text-gray-900 dark:text-white" x-text="order?.client"></p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase mt-0.5" x-text="order?.telephone"></p>
                        </div>
                    </div>

                    {{-- Localisation --}}
                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block px-1">Adresse de livraison</label>
                        <div class="p-5 bg-gray-50 dark:bg-gray-800/40 rounded-3xl flex items-start gap-3">
                            <svg class="w-4 h-4 text-orange-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300 leading-relaxed" x-text="order?.adresse"></span>
                        </div>
                    </div>

                    {{-- Order Lines --}}
                    <div class="space-y-4 pt-4 border-t border-gray-50 dark:border-gray-800">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block px-1">Détail des articles</label>
                        <div class="space-y-3 max-h-48 overflow-y-auto pr-2 no-scrollbar">
                            <template x-for="item in (order?.lignes || [])" :key="item.id">
                                <div class="flex items-center justify-between group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg flex items-center justify-center text-[10px] font-black text-gray-500" x-text="item.quantite + 'x'"></div>
                                        <div>
                                            <p class="text-xs font-black text-gray-900 dark:text-white" x-text="item.nom"></p>
                                            <p class="text-[9px] font-bold text-gray-400 uppercase mt-0.5" x-text="item.options"></p>
                                        </div>
                                    </div>
                                    <p class="text-[11px] font-black text-gray-900 dark:text-white" x-text="(item.prix * item.quantite) + ' F'"></p>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Footer Modal --}}
                    <div class="pt-6 border-t border-gray-50 dark:border-gray-800 flex items-center justify-between">
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Total à payer</p>
                            <p class="text-2xl font-black text-gray-900 dark:text-white" x-text="(order?.total || 0) + ' FCFA'"></p>
                        </div>
                        <div class="text-right">
                             <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Paiement</p>
                             <p class="text-xs font-black text-orange-600 uppercase" x-text="order?.paiement"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Other modals (Chat, Assign, etc.) could be here too --}}
<div x-data="{ open: false, orderId: null }" 
     @open-chat-modal.window="open = true; orderId = $event.detail.orderId"
     x-show="open" 
     class="fixed inset-0 z-[60] flex items-center justify-center p-4"
     style="display: none;">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="open = false"></div>
    <div class="relative bg-white dark:bg-gray-900 rounded-[2.5rem] w-full max-w-sm p-10 text-center">
         <div class="w-20 h-20 bg-blue-50 dark:bg-blue-900/20 rounded-3xl flex items-center justify-center mx-auto mb-6 text-blue-600">
             <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
         </div>
         <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Messagerie Client</h3>
         <p class="text-gray-400 text-sm mb-6">La messagerie instantanée avec le client est en cours d'initialisation...</p>
         <button @click="open = false" class="w-full py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl font-black text-[10px] uppercase tracking-widest active:scale-95">Compris</button>
    </div>
</div>

<div x-data="{ open: false, orderId: null, currentDriver: null }" 
     @open-assign-modal.window="open = true; orderId = $event.detail.orderId; currentDriver = $event.detail.currentDriver"
     x-show="open" 
     class="fixed inset-0 z-[60] flex items-center justify-center p-4"
     style="display: none;">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="open = false"></div>
    <div class="relative bg-white dark:bg-gray-900 rounded-[2.5rem] w-full max-w-md p-8 animate-in slide-in-from-bottom-5">
         <h3 class="text-xl font-black text-gray-900 dark:text-white mb-6">Assigner un livreur</h3>
         <form :action="'/vendeur/orders/' + orderId + '/assign'" method="POST" class="space-y-4">
             @csrf
             <div class="space-y-2">
                 @foreach($vendeur->livreurs ?? [] as $livreur)
                     <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-2xl cursor-pointer hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all border border-transparent has-[:checked]:border-indigo-500">
                         <div class="flex items-center gap-3">
                             <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-xs font-black text-indigo-600">{{ substr($livreur->name, 0, 1) }}</div>
                             <span class="text-xs font-black text-gray-700 dark:text-gray-200">{{ $livreur->name }}</span>
                         </div>
                         <input type="radio" name="id_livreur" value="{{ $livreur->id }}" class="w-4 h-4 text-indigo-600 focus:ring-0" :checked="currentDriver == {{ $livreur->id }}">
                     </label>
                 @endforeach
                 @if(!count($vendeur->livreurs ?? []))
                    <p class="text-center text-gray-400 text-xs py-10 font-bold italic">Aucun livreur configuré.</p>
                 @endif
             </div>
             <div class="flex gap-3 mt-8">
                 <button type="button" @click="open = false" class="flex-1 py-4 bg-gray-50 dark:bg-gray-800 text-gray-400 rounded-2xl font-black text-[10px] uppercase tracking-widest">Annuler</button>
                 <button type="submit" class="flex-1 py-4 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-indigo-600/20 active:scale-95">Confirmer</button>
             </div>
         </form>
    </div>
</div>
