@extends('layouts.customer')

@section('content')
<div class="h-[calc(100vh-4rem)] flex" x-data="chatApp()">
    {{-- Left Panel: Chat List --}}
    <div class="w-60 shrink-0 bg-white border-r border-gray-200 flex flex-col">
        <div class="p-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-900">Pesan</h2>
        </div>
        <div class="flex-1 overflow-y-auto">
            <template x-for="chat in chats" :key="chat.id">
                <button
                    @click="activeChat = chat.id; chat.unread = 0"
                    :class="activeChat === chat.id ? 'bg-blue-50 border-l-4 border-blue-900' : 'hover:bg-gray-50 border-l-4 border-transparent'"
                    class="w-full text-left p-4 transition-colors border-b border-gray-50"
                >
                    <div class="flex items-start gap-3">
                        <div class="relative shrink-0">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-900 font-bold text-sm">
                                <span x-text="chat.name.charAt(0)"></span>
                            </div>
                            <span x-show="chat.online" class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-sm text-gray-900 truncate" x-text="chat.name"></span>
                                <span class="text-xs text-gray-400 shrink-0" x-text="chat.time"></span>
                            </div>
                            <p class="text-xs text-gray-500 truncate mt-0.5" x-text="chat.lastMessage"></p>
                        </div>
                        <span x-show="chat.unread > 0" class="shrink-0 bg-blue-900 text-white text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center" x-text="chat.unread"></span>
                    </div>
                </button>
            </template>
        </div>
    </div>

    {{-- Right Panel: Chat Window --}}
    <div class="flex-1 flex flex-col bg-gray-50">
        {{-- No chat selected --}}
        <div x-show="!activeChat" class="flex-1 flex items-center justify-center">
            <div class="text-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-300 mb-3"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <p class="text-gray-500 font-medium">Pilih percakapan</p>
                <p class="text-gray-400 text-sm">Klik chat di samping untuk mulai</p>
            </div>
        </div>

        {{-- Active chat --}}
        <template x-if="activeChat">
            <div class="flex-1 flex flex-col">
                {{-- Chat Header --}}
                <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-900 font-bold text-sm shrink-0">
                        <span x-text="currentChat.name.charAt(0)"></span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900" x-text="currentChat.name"></p>
                        <p class="text-xs" :class="currentChat.online ? 'text-green-600' : 'text-gray-400'" x-text="currentChat.online ? 'Online' : 'Offline'"></p>
                    </div>
                </div>

                {{-- Messages --}}
                <div x-ref="messages" class="flex-1 overflow-y-auto px-6 py-4 space-y-4">
                    <template x-for="(msg, i) in currentChat.messages" :key="i">
                        <div class="flex" :class="msg.from === 'customer' ? 'justify-end' : 'justify-start'">
                            <div
                                :class="msg.from === 'customer' ? 'bg-blue-900 text-white rounded-br-none' : 'bg-white text-gray-900 border border-gray-200 rounded-bl-none'"
                                class="max-w-[70%] px-4 py-2.5 rounded-2xl shadow-sm"
                            >
                                <p class="text-sm leading-relaxed" x-text="msg.text"></p>
                                <p class="text-xs mt-1" :class="msg.from === 'customer' ? 'text-blue-200' : 'text-gray-400'" x-text="msg.time"></p>
                            </div>
                        </div>
                    </template>

                    {{-- Typing Indicator --}}
                    <div x-show="typing" class="flex justify-start">
                        <div class="bg-white border border-gray-200 rounded-2xl rounded-bl-none px-4 py-3 shadow-sm">
                            <div class="flex gap-1.5">
                                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Input --}}
                <div class="bg-white border-t border-gray-200 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <input
                            type="text"
                            x-model="message"
                            @keydown.enter="sendMessage"
                            placeholder="Tulis pesan..."
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-900 focus:border-blue-900 outline-none transition-shadow"
                        >
                        <button
                            @click="sendMessage"
                            :disabled="!message.trim()"
                            :class="message.trim() ? 'bg-blue-900 hover:bg-blue-800 cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                            class="text-white p-3 rounded-xl transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
</style>

<script>
function chatApp() {
    return {
        activeChat: null,
        message: '',
        typing: false,
        chats: [
            {
                id: 1,
                name: 'Admin Novos',
                lastMessage: 'Baik, akan kami proses',
                time: '10:32',
                unread: 2,
                online: true,
                messages: [
                    { from: 'admin', text: 'Halo Kak, ada yang bisa kami bantu?', time: '10:30' },
                    { from: 'customer', text: 'Halo, saya mau tanya status pesanan NVS-20240601-001', time: '10:31' },
                    { from: 'admin', text: 'Sebentar ya Kak, kami cek dulu', time: '10:31' },
                    { from: 'admin', text: 'Baik, akan kami proses', time: '10:32' }
                ]
            },
            {
                id: 2,
                name: 'Tim Design',
                lastMessage: 'Desain sedang dikerjakan',
                time: '09:15',
                unread: 0,
                online: false,
                messages: [
                    { from: 'admin', text: 'Halo Kak, desain jersey sedang kami kerjakan', time: '09:15' },
                    { from: 'admin', text: 'Estimasi selesai 2 hari lagi', time: '09:15' }
                ]
            }
        ],

        get currentChat() {
            return this.chats.find(c => c.id === this.activeChat);
        },

        sendMessage() {
            if (!this.message.trim()) return;

            const chat = this.currentChat;
            const now = new Date();
            const time = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');

            chat.messages.push({ from: 'customer', text: this.message.trim(), time });
            chat.lastMessage = this.message.trim();
            chat.time = time;
            this.message = '';

            this.$nextTick(() => this.scrollToBottom());

            // Simulate typing after 2 seconds
            setTimeout(() => {
                if (!this.activeChat) return;
                this.typing = true;
                this.$nextTick(() => this.scrollToBottom());

                // Reply after 1 more second
                setTimeout(() => {
                    if (!this.activeChat) return;
                    this.typing = false;
                    const replyTime = new Date();
                    const rt = String(replyTime.getHours()).padStart(2, '0') + ':' + String(replyTime.getMinutes()).padStart(2, '0');
                    const replies = [
                        'Baik Kak, akan kami tindak lanjuti',
                        'Noted Kak, terima kasih informasinya',
                        'Siap Kak, kami proses segera',
                        'Baik, akan kami sampaikan ke tim terkait'
                    ];
                    chat.messages.push({ from: 'admin', text: replies[Math.floor(Math.random() * replies.length)], time: rt });
                    chat.lastMessage = replies[0];
                    chat.time = rt;
                    this.$nextTick(() => this.scrollToBottom());
                }, 1000);
            }, 2000);
        },

        scrollToBottom() {
            const el = this.$refs.messages;
            if (el) el.scrollTop = el.scrollHeight;
        }
    }
}
</script>
@endsection
