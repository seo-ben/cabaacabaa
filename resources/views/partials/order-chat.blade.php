<div x-data="orderChat({{ $orderId }})" x-init="init()" class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-xl dark:shadow-none flex flex-col h-[600px] sticky top-24">
    <!-- Chat Header -->
    <div class="p-6 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-black text-gray-900 dark:text-white">Discussion</h3>
                <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500">Contactez le vendeur</p>
            </div>
            <div x-show="unreadCount > 0" class="w-6 h-6 bg-orange-600 text-white rounded-full flex items-center justify-center text-[10px] font-black">
                <span x-text="unreadCount"></span>
            </div>
        </div>
    </div>

    <!-- Messages Container -->
    <div class="flex-1 overflow-y-auto p-6 space-y-4" x-ref="messagesContainer">
        <template x-if="messages.length === 0">
            <div class="flex flex-col items-center justify-center h-full text-center space-y-3">
                <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <p class="text-sm font-bold text-gray-400 dark:text-gray-500">Aucun message</p>
                <p class="text-xs text-gray-400 dark:text-gray-600">Commencez la conversation</p>
            </div>
        </template>

        <template x-for="message in messages" :key="message.id">
            <div :class="message.user.id_user == currentUserId ? 'flex justify-end' : 'flex justify-start'">
                <div :class="message.user.id_user == currentUserId ? 'bg-orange-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white'" 
                     class="max-w-[80%] rounded-2xl px-4 py-3 space-y-1">
                    <p class="text-[10px] font-black uppercase tracking-wider opacity-70" x-text="message.user.name"></p>
                    <p class="text-sm font-medium" x-text="message.message"></p>
                    <p class="text-[9px] font-bold opacity-60" x-text="formatTime(message.created_at)"></p>
                </div>
            </div>
        </template>
    </div>

    <!-- Message Input -->
    <div class="p-4 border-t border-gray-100 dark:border-gray-800">
        <form @submit.prevent="sendMessage()" class="flex gap-2">
            <input 
                type="text" 
                x-model="newMessage"
                placeholder="Écrivez votre message..."
                class="flex-1 px-4 py-3 bg-gray-50 dark:bg-gray-800 border-none rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none placeholder-gray-400 dark:placeholder-gray-600"
                maxlength="1000"
            >
            <button 
                type="submit"
                :disabled="!newMessage.trim()"
                class="px-6 py-3 bg-orange-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-orange-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-orange-600/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </button>
        </form>
    </div>
</div>

<script>
function orderChat(orderId) {
    return {
        orderId: orderId,
        messages: [],
        newMessage: '',
        unreadCount: 0,
        currentUserId: {{ Auth::id() }},
        polling: null,

        init() {
            this.loadMessages();
            this.startPolling();
        },

        async loadMessages() {
            try {
                const response = await fetch(`/api/orders/${this.orderId}/messages`);
                if (response.ok) {
                    this.messages = await response.json();
                    this.$nextTick(() => this.scrollToBottom());
                    this.unreadCount = 0;
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim()) return;

            try {
                const response = await fetch(`/api/orders/${this.orderId}/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message: this.newMessage })
                });

                if (response.ok) {
                    const message = await response.json();
                    this.messages.push(message);
                    this.newMessage = '';
                    this.$nextTick(() => this.scrollToBottom());
                }
            } catch (error) {
                console.error('Error sending message:', error);
            }
        },

        startPolling() {
            this.polling = setInterval(() => {
                this.loadMessages();
            }, 3000); // Poll every 3 seconds
        },

        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },

        formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;
            
            if (diff < 60000) return 'À l\'instant';
            if (diff < 3600000) return Math.floor(diff / 60000) + ' min';
            if (diff < 86400000) return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
            return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
        }
    }
}
</script>
