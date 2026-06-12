@extends('layouts.internal')

@section('title', 'Chat')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Chat Customer</h1>
    <p class="text-sm text-gray-500 mt-0.5">Percakapan dengan customer</p>
@endsection

@section('internal-content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" style="height: calc(100vh - 10rem);"
     x-data="internalChatApp()">
    <div class="flex h-full">

        {{-- Left: Chat List --}}
        <div class="w-64 shrink-0 border-r border-gray-200 flex flex-col">
            <div class="p-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-900 text-sm">Percakapan</h2>
            </div>
            <div class="flex-1 overflow-y-auto">
                <template x-for="chat in chats" :key="chat.id">
                    <button
                        @click="activeChat = chat.id; chat.unread = 0"
                        :class="activeChat === chat.id ? 'bg-blue-50 border-l-4 border-[#1a237e]' : 'hover:bg-gray-50 border-l-4 border-transparent'"
                        class="w-full text-left p-4 transition-colors border-b border-gray-50"
                    >
                        <div class="flex items-start gap-3">
                            <div class="relative shrink-0">
                                <div class="w-9 h-9 rounded-full bg-[#1a237e]/10 flex items-center justify-center text-[#1a237e] font-bold text-sm">
                                    <span x-text="chat.name.charAt(0)"></span>
                                </div>
                                <span x-show="chat.online" class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-xs text-gray-900 truncate" x-text="chat.name"></span>
                                    <span class="text-xs text-gray-400 shrink-0" x-text="chat.time"></span>
                                </div>
                                <p class="text-xs text-gray-500 truncate mt-0.5" x-text="chat.lastMessage"></p>
                            </div>
                            <span x-show="chat.unread > 0" class="shrink-0 bg-[#1a237e] text-white text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center" x-text="chat.unread"></span>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        {{-- Right: Chat Window --}}
        <div class="flex-1 flex flex-col bg-gray-50">
            {{-- No chat selected --}}
            <div x-show="!activeChat" class="flex-1 flex items-center justify-center">
                <div class="text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p class="text-gray-500 font-medium text-sm">Pilih percakapan</p>
                    <p class="text-gray-400 text-xs mt-1">Klik chat di kiri untuk mulai</p>
                </div>
            </div>

            {{-- Active chat --}}
            <template x-if="activeChat">
                <div class="flex-1 flex flex-col">
                    {{-- Header --}}
                    <div class="bg-white border-b border-gray-200 px-6 py-3.5 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-[#1a237e]/10 flex items-center justify-center text-[#1a237e] font-bold text-sm shrink-0">
                            <span x-text="currentChat.name.charAt(0)"></span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm" x-text="currentChat.name"></p>
                            <p class="text-xs" :class="currentChat.online ? 'text-green-600' : 'text-gray-400'" x-text="currentChat.online ? 'Online' : 'Offline'"></p>
                        </div>
                    </div>

                    {{-- Messages --}}
                    <div x-ref="messages" class="flex-1 overflow-y-auto px-6 py-4 space-y-3">
                        <template x-for="(msg, i) in currentChat.messages" :key="i">
                            <div class="flex" :class="msg.from === 'admin' ? 'justify-end' : 'justify-start'">
                                <div
                                    :class="msg.from === 'admin' ? 'bg-[#1a237e] text-white rounded-br-none' : 'bg-white text-gray-900 border border-gray-200 rounded-bl-none'"
                                    class="max-w-[70%] px-4 py-2.5 rounded-2xl shadow-sm space-y-1"
                                >
                                    <p class="text-sm leading-relaxed" x-text="msg.text"></p>
                                    <p class="text-xs" :class="msg.from === 'admin' ? 'text-blue-200' : 'text-gray-400'" x-text="msg.time"></p>
                                </div>
                            </div>
                        </template>
                        {{-- Typing --}}
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
                                placeholder="Tulis pesan ke customer..."
                                class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30 focus:border-[#1a237e]/50 transition-shadow"
                            >
                            <button
                                @click="sendMessage"
                                :disabled="!message.trim()"
                                :class="message.trim() ? 'bg-[#1a237e] hover:bg-[#1a237e]/90 cursor-pointer' : 'bg-gray-200 cursor-not-allowed'"
                                class="text-white p-2.5 rounded-xl transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

    </div>
</div>

<script>
function internalChatApp() {
    return {
        activeChat: null,
        message: '',
        typing: false,
        chats: [],

        get currentChat() {
            return this.chats.find(c => c.id === this.activeChat);
        },

        sendMessage() {
            if (!this.message.trim()) return;
            const chat = this.currentChat;
            const now = new Date();
            const t = String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0');
            chat.messages.push({ from: 'admin', text: this.message.trim(), time: t });
            chat.lastMessage = this.message.trim();
            chat.time = t;
            this.message = '';
            this.$nextTick(() => {
                const el = this.$refs.messages;
                if (el) el.scrollTop = el.scrollHeight;
            });
        }
    }
}
</script>
@endsection
