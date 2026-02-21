<div class="inline-flex gap-2">
    <!-- Chat Button -->
    <button @click="$dispatch('open-chat-modal', { orderId: {{ $order->id_commande }} })" 
            class="p-2 bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 rounded-lg hover:bg-orange-600 hover:text-white transition-all border border-orange-100 dark:border-orange-900/30 relative" 
            title="Discuter avec le client">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        @if($order->unread_messages_count > 0)
            <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-600 text-white text-[8px] font-black rounded-full flex items-center justify-center animate-pulse">
                {{ $order->unread_messages_count }}
            </span>
        @endif
    </button>

    <!-- View Details Button -->
    <button @click="$dispatch('open-order-modal', { order: {{ Js::from([
        'numero' => $order->numero_commande,
        'date' => $order->date_commande->format('d/m/Y H:i'),
        'client' => $order->client->name ?? $order->nom_complet_client ?? 'Anonyme',
        'telephone' => $order->telephone_client ?? $order->client->telephone ?? '---',
        'adresse' => $order->adresse_livraison ?? 'Retrait sur place',
        'total' => $order->montant_total,
        'paiement' => $order->mode_paiement_prevu,
        'statut' => $order->statut,
        'lignes' => $order->lignes->map(fn($l) => [
            'nom' => $l->nom_plat_snapshot,
            'quantite' => $l->quantite,
            'prix' => $l->prix_unitaire,
            'options' => $l->options
        ])
    ]) }} })" 
            class="p-2 bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-900 dark:hover:bg-white hover:text-white dark:hover:text-gray-900 transition-all border border-gray-200 dark:border-gray-700" 
            title="Voir détails">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
    </button>
    
    <form action="{{ vendor_route('vendeur.slug.orders.status', $order->id_commande) }}" method="POST" class="inline-flex gap-2">
        @csrf
        @method('PATCH')
        
        @if($order->statut === 'en_attente')
            <button type="submit" name="statut" value="en_preparation" class="p-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Accepter">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </button>
        @elseif($order->statut === 'en_preparation')
            <button type="submit" name="statut" value="pret" class="p-2 bg-blue-600 text-white rounded-lg hover:bg-orange-500 transition-all shadow-sm" title="Prête">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </button>
        @elseif($order->statut === 'pret')
            <button type="submit" name="statut" value="termine" class="p-2 bg-orange-500 text-white rounded-lg hover:bg-green-600 transition-all shadow-sm" title="Livre">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
            </button>
        @endif

        @if(!in_array($order->statut, ['termine', 'annule']))
            <button type="submit" name="statut" value="annule" onclick="return confirm('Annuler cette commande ?')" class="p-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-600 hover:text-white transition-all border border-red-100 dark:border-red-900/30" title="Annuler">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        @endif
    </form>

    <!-- Assign Driver Button -->
    @if($order->type_recuperation === 'livraison' && !in_array($order->statut, ['termine', 'annule']))
        <button @click="$dispatch('open-assign-modal', { orderId: {{ $order->id_commande }}, currentDriver: {{ $order->id_livreur ?? 'null' }} })"
                class="p-2 {{ $order->id_livreur ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border-green-100 dark:border-green-900/30' : 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 border-indigo-100 dark:border-indigo-900/30' }} rounded-lg hover:bg-indigo-600 hover:text-white transition-all border"
                title="{{ $order->id_livreur ? 'Changer de livreur' : 'Assigner un livreur' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </button>
    @endif
</div>
