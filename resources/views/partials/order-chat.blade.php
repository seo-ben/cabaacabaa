<div x-data="orderChat({{ $orderId }})" 
     x-init="init()" 
     class="flex flex-col bg-white dark:bg-gray-900 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-2xl shadow-gray-200/50 dark:shadow-none overflow-hidden h-full max-h-[600px] border-b-8 border-b-orange-600"
     x-cloak>
    
    <!-- Chat Header -->
    <div class="px-8 py-6 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white dark:bg-gray-700 rounded-2xl flex items-center justify-center text-orange-600 dark:text-orange-400 shadow-sm border border-gray-100 dark:border-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Discussion Directe</h3>
                <div class="flex items-center gap-2 mt-1">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                        Support en temps réel
                    </p>
                </div>
            </div>
        </div>
        <div x-show="unreadCount > 0" 
             class="px-3 py-1 bg-orange-600 text-white rounded-full text-[10px] font-black animate-bounce shadow-lg shadow-orange-600/20">
            <span x-text="unreadCount"></span> NOUVEAU
        </div>
    </div>

    <!-- Messages Container -->
    <div class="flex-1 overflow-y-auto p-8 space-y-6 custom-scrollbar bg-[radial-gradient(#f3f4f6_1.5px,transparent_1.5px)] dark:bg-[radial-gradient(#1f2937_1.5px,transparent_1.5px)] bg-size-[24px_24px]" 
         x-ref="messagesContainer">
        
        <template x-if="loading && messages.length === 0">
            <div class="flex flex-col items-center justify-center h-full space-y-4">
                <div class="w-12 h-12 border-4 border-orange-100 border-t-orange-600 rounded-full animate-spin"></div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Chargement...</p>
            </div>
        </template>

        <template x-if="!loading && messages.length === 0">
            <div class="flex flex-col items-center justify-center h-full text-center space-y-6 opacity-60">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-3xl flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-200 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter">Aucun message</p>
                    <p class="text-[11px] font-bold text-gray-400 mt-2">Démarrer la discussion maintenant</p>
                </div>
            </div>
        </template>

        <template x-for="message in messages" :key="message.id">
            <div :class="message.id_user == currentUserId ? 'flex justify-end pr-2' : 'flex justify-start pl-2'"
                 class="animate-in slide-in-from-bottom-2 duration-300">
                <div class="max-w-[85%] group">
                    <div :class="message.id_user == currentUserId 
                        ? 'bg-orange-600 text-white rounded-t-2xl rounded-bl-2xl shadow-lg shadow-orange-600/10' 
                        : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-t-2xl rounded-br-2xl border border-gray-100 dark:border-gray-700 shadow-sm'" 
                         class="px-5 py-3 relative">
                        <!-- Message Text -->
                        <p class="text-[13px] font-bold leading-relaxed whitespace-pre-wrap" x-text="message.message"></p>
                    </div>
                    <!-- Metadata below bubble -->
                    <div :class="message.id_user == currentUserId ? 'text-right' : 'text-left'" class="mt-2 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <span class="text-[9px] font-black uppercase text-gray-400 dark:text-gray-600 tracking-tighter" 
                              x-text="message.id_user == currentUserId ? 'Vous' : (message.user ? message.user.name : 'Client')"></span>
                        <span class="w-1 h-1 bg-gray-200 dark:bg-gray-700 rounded-full"></span>
                        <span class="text-[9px] font-black uppercase text-gray-400 dark:text-gray-600 tracking-tighter" x-text="formatTime(message.created_at)"></span>
                    </div>
                </div>
            </div>
        </template>
        
        <div x-show="sending" class="flex justify-end pr-2 animate-pulse">
            <div class="bg-orange-600/50 text-white rounded-2xl px-5 py-3 text-[13px] font-bold">
                Envoi en cours...
            </div>
        </div>
    </div>

    <!-- Message Input -->
    <div class="p-6 bg-gray-50/50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
        <form @submit.prevent="sendMessage()" class="relative">
            <input 
                type="text" 
                x-model="newMessage"
                @keydown.enter.prevent="sendMessage()"
                placeholder="Message..."
                class="w-full pl-6 pr-16 py-5 bg-white dark:bg-gray-900 border-none rounded-2xl text-[13px] font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 shadow-sm transition-all placeholder-gray-400"
                maxlength="1000"
            >
            <button 
                type="submit"
                :disabled="!newMessage.trim() || sending"
                class="absolute right-3 top-1/2 -translate-y-1/2 w-12 h-12 bg-orange-600 text-white rounded-xl flex items-center justify-center hover:bg-orange-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-orange-600/20 active:scale-95">
                <svg x-show="!sending" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                <svg x-show="sending" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </form>
    </div>
</div>

@once
<script>
function orderChat(orderId) {
    return {
        orderId: orderId,
        messages: [],
        newMessage: '',
        unreadCount: 0,
        currentUserId: {{ Auth::id() ?? 'null' }},
        polling: null,
        loading: false,
        sending: false,

        init() {
            this.loading = true;
            this.loadMessages().finally(() => {
                this.loading = false;
            });
            this.startPolling();
        },

        async loadMessages() {
            try {
                const response = await fetch(`/api/orders/${this.orderId}/messages`);
                if (response.ok) {
                    const data = await response.json();
                    
                    // Only update and scroll if new messages arrived
                    if (data.length > this.messages.length) {
                        this.messages = data;
                        this.$nextTick(() => this.scrollToBottom());
                    } else if (data.length < this.messages.length) {
                        // Handle case where messages might have been deleted or reset
                        this.messages = data;
                    }
                    
                    this.unreadCount = 0;
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim() || this.sending) return;

            const msg = this.newMessage;
            this.newMessage = '';
            this.sending = true;

            try {
                const response = await fetch(`/api/orders/${this.orderId}/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: msg })
                });

                if (response.ok) {
                    const message = await response.json();
                    this.messages.push(message);
                    this.$nextTick(() => this.scrollToBottom());
                } else {
                    // Restore message on error
                    this.newMessage = msg;
                }
            } catch (error) {
                console.error('Error sending message:', error);
                this.newMessage = msg;
            } finally {
                this.sending = false;
            }
        },

        startPolling() {
            if (this.polling) clearInterval(this.polling);
            this.polling = setInterval(() => {
                this.loadMessages();
            }, 4000); // Slightly slower polling for performance
        },

        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTo({
                    top: container.scrollHeight,
                    behavior: 'smooth'
                });
            }
            
            // Clean unread count on server if we scrolled to bottom of new messages
            this.unreadCount = 0;
        },

        formatTime(timestamp) {
            if (!timestamp) return '';
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;
            
            if (diff < 60000) return 'À l\'instant';
            if (diff < 3600000) return Math.floor(diff / 60000) + ' min';
            
            // If same day
            if (date.toDateString() === now.toDateString()) {
                return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
            }
            
            return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
        }
    }
}
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; }
</style>
@endonce

