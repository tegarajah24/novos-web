<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Services\ImageService;
use Illuminate\Support\Facades\Storage;

class ChatCustomer extends Component
{
    use WithFileUploads;

    public array $chats = [];
    public ?int $activeChatId = null;
    public array $messages = [];
    public string $messageInput = '';
    public $file;
    public bool $sending = false;

    public function mount()
    {
        $this->loadChats();
    }

    public function loadChats()
    {
        $user = auth()->user();

        $this->chats = Chat::with(['messages.sender', 'admin'])
            ->where('customer_id', $user->id)
            ->latest()
            ->get()
            ->map(fn($chat) => [
                'id'          => $chat->id,
                'name'        => $chat->admin?->name ?? 'Admin Novos',
                'sender_avatar_url' => $chat->admin?->avatar ? Storage::url($chat->admin->avatar) : null,
                'lastMessage' => $chat->messages->last()?->message
                    ?? ($chat->messages->last()?->file_name
                        ? '📎 ' . $chat->messages->last()->file_name
                        : 'Mulai percakapan'),
                'time'   => $chat->messages->last()?->created_at?->format('H:i') ?? '',
                'unread' => $chat->messages
                    ->where('is_read', false)
                    ->where('sender_id', '!=', $user->id)
                    ->count(),
                'online'  => false,
            ])
            ->values()
            ->toArray();

        if ($this->activeChatId) {
            $this->loadMessages();
        } elseif (count($this->chats) > 0) {
            $this->activeChatId = $this->chats[0]['id'];
            $this->loadMessages();
        }
    }

    public function selectChat($chatId)
    {
        $this->activeChatId = (int) $chatId;
        $this->loadMessages();
        $this->markRead();
        $this->dispatch('messages-loaded');
    }

    public function loadMessages()
    {
        if (!$this->activeChatId) {
            $this->messages = [];
            return;
        }

        $chat = Chat::with('messages.sender')->find($this->activeChatId);
        if (!$chat || $chat->customer_id !== auth()->id()) {
            $this->messages = [];
            return;
        }

        $this->messages = $chat->messages->map(fn($msg) => [
            'from'               => $msg->sender_id === auth()->id() ? 'customer' : 'admin',
            'text'               => $msg->message,
            'time'               => $msg->created_at->format('H:i'),
            'file_url'           => $msg->file_url,
            'file_name'          => $msg->file_name,
            'file_size_formatted' => $msg->file_size_formatted,
            'is_image'           => $msg->is_image,
            'is_video'           => $msg->is_video,
            'sender_avatar_url' => $msg->sender_avatar_url,
        ])->toArray();

        $this->chats = collect($this->chats)->map(fn($c) => [
            ...$c,
            'unread' => $c['id'] == $this->activeChatId ? 0 : $c['unread'],
        ])->toArray();
    }

    public function markRead()
    {
        if (!$this->activeChatId) return;

        ChatMessage::where('chat_id', $this->activeChatId)
            ->where('sender_id', '!=', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $this->dispatch('cart-updated');
    }

    public function sendMessage()
    {
        $this->validate([
            'messageInput' => 'nullable|string|max:2000',
            'file'         => 'nullable|file|max:20480',
        ]);

        if (!$this->messageInput && !$this->file) return;

        $this->sending = true;
        $user = auth()->user();
        $chat = Chat::findOrFail($this->activeChatId);

        $filePath = null;
        $fileName = null;
        $fileSize = null;
        $fileType = null;

        if ($this->file) {
            $extension = strtolower($this->file->getClientOriginalExtension());
            $isImage = in_array($extension, ['jpg', 'jpeg', 'png']);
            $filePath = $isImage
                ? app(ImageService::class)->compressAndStore($this->file, 'chat-files')
                : $this->file->store('chat-files', 'public');
            $fileName = $this->file->getClientOriginalName();
            $fileSize = $isImage ? Storage::disk('public')->size($filePath) : $this->file->getSize();
            $fileType = $this->file->getMimeType();
        }

        $message = ChatMessage::create([
            'chat_id'   => $chat->id,
            'sender_id' => $user->id,
            'message'   => $this->messageInput ?: null,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'file_type' => $fileType,
        ]);

        $message->load('sender');

        $this->messages[] = [
            'from'               => 'customer',
            'text'               => $message->message,
            'time'               => $message->created_at->format('H:i'),
            'file_url'           => $message->file_url,
            'file_name'          => $message->file_name,
            'file_size_formatted' => $message->file_size_formatted,
            'is_image'           => $message->is_image,
            'is_video'           => $message->is_video,
            'sender_avatar_url' => $message->sender_avatar_url,
        ];

        $this->chats = collect($this->chats)->map(fn($c) => [
            ...$c,
            'lastMessage' => $c['id'] == $this->activeChatId
                ? ($message->message ?: '📎 ' . $message->file_name)
                : $c['lastMessage'],
            'time' => $c['id'] == $this->activeChatId ? $message->created_at->format('H:i') : $c['time'],
        ])->toArray();

        $this->messageInput = '';
        $this->file = null;
        $this->sending = false;

        $chat->load('order');
        \App\Models\Notification::sendToAllStaff(
            'chat',
            'Pesan Baru',
            "Pesan baru dari customer <strong>{$user->name}</strong>" . ($chat->order ? " untuk <strong>{$chat->order->order_number}</strong>" : '') . ($message->message ? ": {$message->message}" : ''),
            [
                'initials' => collect(explode(' ', $user->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                'role' => auth()->user()->role->name,
                'role_initial' => 'C',
                'role_color' => '#d53f8c',
            ]
        );

        $this->dispatch('messages-loaded');
    }

    public function getCurrentChatProperty(): ?array
    {
        return collect($this->chats)->firstWhere('id', $this->activeChatId);
    }

    public function render()
    {
        return view('livewire.chat-customer');
    }
}
