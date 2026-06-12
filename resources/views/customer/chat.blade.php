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
                                class="max-w-[70%] px-4 py-2.5 rounded-2xl shadow-sm space-y-1.5"
                            >
                                {{-- File attachment --}}
                                <template x-if="msg.file_url">
                                    <div>
                                        {{-- Image --}}
                                        <template x-if="msg.is_image">
                                            <a :href="msg.file_url" target="_blank" class="block -mx-1 -mt-1">
                                                <img :src="msg.file_url" :alt="msg.file_name" class="max-w-full rounded-xl max-h-60 object-cover">
                                            </a>
                                        </template>
                                        {{-- Video --}}
                                        <template x-if="msg.is_video">
                                            <video :src="msg.file_url" controls class="max-w-full rounded-xl max-h-60" @click.stop></video>
                                        </template>
                                        {{-- Other file --}}
                                        <template x-if="!msg.is_image && !msg.is_video">
                                            <a :href="msg.file_url" target="_blank"
                                                :class="msg.from === 'customer' ? 'bg-blue-800 hover:bg-blue-700' : 'bg-gray-100 hover:bg-gray-200'"
                                                class="flex items-center gap-3 p-3 rounded-xl transition-colors"
                                            >
                                                <div :class="msg.from === 'customer' ? 'bg-blue-700' : 'bg-blue-100'" class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :class="msg.from === 'customer' ? 'text-white' : 'text-blue-900'"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-medium truncate" :class="msg.from === 'customer' ? 'text-blue-100' : 'text-gray-900'" x-text="msg.file_name"></p>
                                                    <p class="text-xs" :class="msg.from === 'customer' ? 'text-blue-200' : 'text-gray-400'" x-text="msg.file_size_formatted"></p>
                                                </div>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" :class="msg.from === 'customer' ? 'text-blue-200' : 'text-gray-400'"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                            </a>
                                        </template>
                                    </div>
                                </template>
                                {{-- Text message --}}
                                <template x-if="msg.text">
                                    <p class="text-sm leading-relaxed" x-text="msg.text"></p>
                                </template>
                                <p class="text-xs" :class="msg.from === 'customer' ? 'text-blue-200' : 'text-gray-400'" x-text="msg.time"></p>
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
                    {{-- File preview --}}
                    <template x-if="selectedFile">
                        <div class="flex items-center gap-3 mb-3 p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <template x-if="selectedFileIsImage">
                                <img :src="selectedFilePreview" class="w-12 h-12 rounded-lg object-cover shrink-0">
                            </template>
                            <template x-if="!selectedFileIsImage">
                                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-900"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </div>
                            </template>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="selectedFile.name"></p>
                                <p class="text-xs text-gray-500" x-text="selectedFileSizeFormatted"></p>
                            </div>
                            <button @click="removeSelectedFile" class="text-gray-400 hover:text-red-500 transition-colors p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                    </template>
                    <div class="flex items-center gap-3">
                        <label class="cursor-pointer p-2 text-gray-400 hover:text-blue-900 transition-colors rounded-lg hover:bg-gray-100">
                            <input type="file" @change="handleFileSelect" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar" class="hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18.84 5.6l-8.11 8.11a2 2 0 1 1-2.83-2.83l8.49-8.49"/></svg>
                        </label>
                        <input
                            type="text"
                            x-model="message"
                            @keydown.enter="sendMessage"
                            placeholder="Tulis pesan..."
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-900 focus:border-blue-900 outline-none transition-shadow"
                        >
                        <button
                            @click="sendMessage"
                            :disabled="(!message.trim() && !selectedFile) || sending"
                            :class="(message.trim() || selectedFile) && !sending ? 'bg-blue-900 hover:bg-blue-800 cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                            class="text-white p-3 rounded-xl transition-colors"
                        >
                            <template x-if="!sending">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            </template>
                            <template x-if="sending">
                                <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                            </template>
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
        sending: false,
        selectedFile: null,
        selectedFilePreview: null,
        selectedFileIsImage: false,
        chats: @json($chats),

        get currentChat() {
            return this.chats.find(c => c.id === this.activeChat);
        },

        get selectedFileSizeFormatted() {
            if (!this.selectedFile) return '';
            const bytes = this.selectedFile.size;
            const units = ['B', 'KB', 'MB'];
            let size = bytes;
            let unit = 0;
            while (size >= 1024 && unit < units.length - 1) {
                size /= 1024;
                unit++;
            }
            return size.toFixed(1) + ' ' + units[unit];
        },

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            const maxSize = 20 * 1024 * 1024;
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'File terlalu besar',
                    text: 'Ukuran file maksimal 20 MB',
                });
                event.target.value = '';
                return;
            }

            this.selectedFile = file;
            this.selectedFileIsImage = file.type.startsWith('image/');

            if (this.selectedFileIsImage) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.selectedFilePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                this.selectedFilePreview = null;
            }

            event.target.value = '';
        },

        removeSelectedFile() {
            this.selectedFile = null;
            this.selectedFilePreview = null;
            this.selectedFileIsImage = false;
        },

        async sendMessage() {
            if ((!this.message.trim() && !this.selectedFile) || this.sending) return;

            const chat = this.currentChat;
            this.sending = true;

            const formData = new FormData();
            formData.append('chat_id', chat.id);
            if (this.message.trim()) {
                formData.append('message', this.message.trim());
            }
            if (this.selectedFile) {
                formData.append('file', this.selectedFile);
            }

            try {
                const response = await fetch('{{ route("chat.send") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                if (!response.ok) {
                    const err = await response.json();
                    throw new Error(err.message || 'Gagal mengirim pesan');
                }

                const result = await response.json();
                const msg = result.message;

                chat.messages.push({
                    from: 'customer',
                    text: msg.message || '',
                    time: msg.created_at,
                    file_url: msg.file_url,
                    file_name: msg.file_name,
                    file_size_formatted: msg.file_size_formatted,
                    is_image: msg.is_image,
                    is_video: msg.is_video,
                });

                if (msg.message) {
                    chat.lastMessage = msg.message;
                } else if (msg.file_name) {
                    chat.lastMessage = '📎 ' + msg.file_name;
                }
                chat.time = msg.created_at;

                this.message = '';
                this.removeSelectedFile();

                this.$nextTick(() => this.scrollToBottom());

            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal mengirim',
                    text: error.message || 'Terjadi kesalahan saat mengirim pesan',
                });
            } finally {
                this.sending = false;
            }
        },

        scrollToBottom() {
            const el = this.$refs.messages;
            if (el) el.scrollTop = el.scrollHeight;
        }
    }
}
</script>
@endsection
