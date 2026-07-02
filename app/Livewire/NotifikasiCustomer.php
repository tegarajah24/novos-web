<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Notification;

class NotifikasiCustomer extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public function markRead($id)
    {
        $notif = Notification::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();

        if ($notif) {
            $notif->update(['is_read' => true]);
        }
    }

    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function render()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return view('livewire.notifikasi-customer', compact('notifications'));
    }
}
