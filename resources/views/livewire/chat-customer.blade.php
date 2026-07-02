@php
    $currentChat = $this->currentChat;
@endphp

<div class="max-w-6xl mx-auto px-4 py-8" x-data="{
    mobileList: true,
    init() {
        this.$watch('$wire.activeChatId', () => { this.scrollToBottom(); });
        Livewire.on('messages-loaded', () => { this.scrollToBottom(); });
    },
    scrollToBottom() {
        this.$nextTick(() => {
            const el = document.querySelector('.messages-scroll');
            if (el) el.scrollTop = el.scrollHeight;
        });
    }
}">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden h-[calc(100vh-8rem)] md:flex">
        {{-- Left Panel --}}
        <div x-show="mobileList || window.innerWidth >= 768" class="w-full md:w-60 md:shrink-0 bg-white md:border-r border-gray-200 flex flex-col">
            <div class="p-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-900">Pesan</h2>
            </div>
            <div class="flex-1 overflow-y-auto">
                @forelse($chats as $chat)
                    <button
                        wire:click="selectChat({{ $chat['id'] }})"
                        @click="mobileList = false"
                        wire:key="chat-{{ $chat['id'] }}"
                        class="w-full text-left p-4 transition-colors border-b border-gray-50
                               {{ $activeChatId === $chat['id'] ? 'bg-blue-50 border-l-4 border-[#1a237e]' : 'hover:bg-gray-50 border-l-4 border-transparent' }}"
                    >
                        <div class="flex items-start gap-3">
                            <div class="relative shrink-0">
                                @if($chat['sender_avatar_url'])
                                    <img src="{{ $chat['sender_avatar_url'] }}" class="w-10 h-10 rounded-full object-cover" alt="Avatar">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-900 font-bold text-sm">
                                        <span>{{ substr($chat['name'], 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-sm text-gray-900 truncate">{{ $chat['name'] }}</span>
                                    <span class="text-xs text-gray-400 shrink-0">{{ $chat['time'] }}</span>
                                </div>
                                <p class="text-xs text-gray-500 truncate mt-0.5">{{ $chat['lastMessage'] }}</p>
                            </div>
                            @if($chat['unread'] > 0)
                                <span class="shrink-0 bg-[#1a237e] text-white text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">{{ $chat['unread'] }}</span>
                            @endif
                        </div>
                    </button>
                @empty
                    <div class="p-4 text-center text-gray-400 text-sm">Belum ada percakapan</div>
                @endforelse
            </div>
        </div>

        {{-- Right Panel --}}
        <div x-show="!mobileList || window.innerWidth >= 768" class="flex-1 flex flex-col bg-gray-50">
            @if(!$currentChat)
                <div class="flex-1 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        <p class="text-gray-500 font-medium">Pilih percakapan</p>
                        <p class="text-gray-400 text-sm">Klik chat di samping untuk mulai</p>
                    </div>
                </div>
            @else
                <div class="flex-1 flex flex-col min-h-0">
                    {{-- Header --}}
                    <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-3">
                        <button @click="mobileList = true" class="md:hidden p-1.5 -ml-1.5 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors" title="Kembali">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><polyline points="12 19 5 12 12 5"/></svg>
                        </button>
                        <div class="w-10 h-10 rounded-full shrink-0">
                            @if($currentChat['sender_avatar_url'])
                                <img src="{{ $currentChat['sender_avatar_url'] }}" class="w-10 h-10 rounded-full object-cover" alt="Avatar">
                            @else
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-900 font-bold text-sm">
                                    <span>{{ substr($currentChat['name'], 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $currentChat['name'] }}</p>
                            <p class="text-xs text-gray-400">Offline</p>
                        </div>
                    </div>

                    {{-- Messages --}}
                    <div class="flex-1 overflow-y-auto min-h-0 px-6 py-4 space-y-4 messages-scroll" wire:poll.10s="loadChats">
                        @forelse($messages as $msg)
                            <div class="flex {{ $msg['from'] === 'customer' ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[70%] px-4 py-2.5 rounded-2xl shadow-sm space-y-1.5
                                    {{ $msg['from'] === 'customer' ? 'bg-[#1a237e] text-white rounded-br-none' : 'bg-white text-gray-900 border border-gray-200 rounded-bl-none' }}">
                                    @if($msg['file_url'])
                                        <div>
                                            @if($msg['is_image'])
                                                <a href="{{ $msg['file_url'] }}" target="_blank" class="block -mx-1 -mt-1">
                                                    <img src="{{ $msg['file_url'] }}" alt="{{ $msg['file_name'] }}" class="max-w-full rounded-xl max-h-60 object-cover">
                                                </a>
                                            @elseif($msg['is_video'])
                                                <video src="{{ $msg['file_url'] }}" controls class="max-w-full rounded-xl max-h-60"></video>
                                            @else
                                                <a href="{{ $msg['file_url'] }}" target="_blank"
                                                    class="flex items-center gap-3 p-3 rounded-xl transition-colors
                                                           {{ $msg['from'] === 'customer' ? 'bg-[#1a237e] hover:bg-[#283593]' : 'bg-gray-100 hover:bg-gray-200' }}">
                                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 {{ $msg['from'] === 'customer' ? 'bg-[#283593]' : 'bg-blue-100' }}">
                                                        <svg class="w-5 h-5 {{ $msg['from'] === 'customer' ? 'text-white' : 'text-blue-900' }}" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-sm font-medium truncate {{ $msg['from'] === 'customer' ? 'text-blue-100' : 'text-gray-900' }}">{{ $msg['file_name'] }}</p>
                                                        <p class="text-xs {{ $msg['from'] === 'customer' ? 'text-blue-200' : 'text-gray-400' }}">{{ $msg['file_size_formatted'] }}</p>
                                                    </div>
                                                    <svg class="w-4 h-4 shrink-0 {{ $msg['from'] === 'customer' ? 'text-blue-200' : 'text-gray-400' }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                    @if($msg['text'])
                                        <p class="text-sm leading-relaxed">{{ $msg['text'] }}</p>
                                    @endif
                                    <p class="text-xs {{ $msg['from'] === 'customer' ? 'text-blue-200' : 'text-gray-400' }}">{{ $msg['time'] }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="flex justify-center py-12">
                                <p class="text-gray-400 text-sm">Belum ada pesan. Kirim pesan untuk memulai percakapan.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Input --}}
                    <div class="bg-white border-t border-gray-200 px-6 py-4">
                        @if($file)
                            <div class="flex items-center gap-3 mb-3 p-3 bg-gray-50 rounded-xl border border-gray-200">
                                @if(in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $file->temporaryUrl() }}" class="w-12 h-12 rounded-lg object-cover shrink-0">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-blue-900" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $file->getClientOriginalName() }}</p>
                                    <p class="text-xs text-gray-500">{{ number_format($file->getSize() / 1024, 1) }} KB</p>
                                </div>
                                <button wire:click="$set('file', null)" class="text-gray-400 hover:text-red-500 transition-colors p-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>
                        @endif

                        <form wire:submit="sendMessage" class="flex items-center gap-3">
                            <label class="cursor-pointer p-2 text-gray-400 hover:text-[#1a237e] transition-colors rounded-lg hover:bg-gray-100">
                                <input type="file" wire:model="file" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar" class="hidden">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18.84 5.6l-8.11 8.11a2 2 0 1 1-2.83-2.83l8.49-8.49"/></svg>
                            </label>

                            <input
                                type="text"
                                wire:model="messageInput"
                                wire:keydown.enter="sendMessage"
                                placeholder="Tulis pesan..."
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1a237e] focus:border-[#1a237e] outline-none transition-shadow"
                            >

                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                @disabled(!$messageInput && !$file)
                                class="text-white p-3 rounded-xl transition-colors
                                       {{ ($messageInput || $file) && !$sending ? 'bg-[#1a237e] hover:bg-[#283593] cursor-pointer' : 'bg-gray-300 cursor-not-allowed' }}"
                            >
                                @if($sending)
                                    <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
