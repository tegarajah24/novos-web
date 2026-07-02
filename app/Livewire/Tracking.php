<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Notification;
use App\Models\Role;
use Illuminate\Support\Str;

class Tracking extends Component
{
    public $state = 'empty';
    public $searchQuery = '';
    public $errorMessage = '';
    public $order = ['id' => '', 'date' => '', 'status' => 'menunggu_validasi', 'design_files' => [], 'team_name' => ''];
    public $shareUrl = null;
    public $shared = false;
    public $revisionNote = '';

    protected function getListeners()
    {
        return ['notify'];
    }

    public function mount($orderData = null, $shared = false)
    {
        $this->shared = $shared;
        if ($orderData) {
            $this->order = $orderData;
            $this->shareUrl = $orderData['share_url'] ?? null;
            $this->state = 'result';
        }
    }

    public function search()
    {
        $query = trim($this->searchQuery);
        if (!$query) {
            $this->errorMessage = 'Masukkan nomor pesanan';
            $this->state = 'error';
            return;
        }

        $this->state = 'loading';
        $this->errorMessage = '';

        $order = Order::with(['designRequest', 'orderItems'])
            ->where('order_number', $query)
            ->where('user_id', auth()->id())
            ->first();

        if (!$order) {
            $this->errorMessage = 'Pesanan dengan nomor "' . e($query) . '" tidak ditemukan';
            $this->state = 'error';
            return;
        }

        $this->setOrderData($order);
        $this->state = 'result';
    }

    private function setOrderData($order)
    {
        $designFiles = [];
        if ($order->designRequest && $order->designRequest->design_files) {
            $designFiles = collect($order->designRequest->design_files)->map(fn($f) => [
                'name' => $f['name'],
                'url'  => asset('storage/' . $f['path']),
            ])->values()->toArray();
        }

        $this->order = [
            'id'           => $order->order_number,
            'date'         => $order->created_at->format('j F Y'),
            'status'       => $order->status,
            'design_files' => $designFiles,
            'team_name'    => $order->designRequest?->team_name,
        ];

        $this->shareUrl = $order->share_token
            ? route('tracking.shared', $order->share_token)
            : null;
    }

    public function accDesign()
    {
        $order = Order::where('order_number', $this->order['id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!in_array($order->status, ['di_design', 'siap_cetak'])) {
            $this->dispatch('notify', type: 'error', message: 'Status pesanan tidak memungkinkan untuk ACC');
            return;
        }

        $nextStatus = match ($order->status) {
            'di_design'  => 'siap_cetak',
            'siap_cetak' => 'diproduksi',
        };

        $order->update(['status' => $nextStatus]);

        OrderStatusHistory::create([
            'order_id'   => $order->id,
            'status'     => $nextStatus,
            'changed_by' => auth()->id(),
            'notes'      => 'Desain disetujui oleh customer',
        ]);

        $currentUser = auth()->user();
        Notification::sendToAllStaff(
            'design_acc',
            'Desain Disetujui',
            "Customer <strong>{$currentUser->name}</strong> menyetujui desain untuk <strong>{$order->order_number}</strong> — status berubah ke <strong>{$nextStatus}</strong>.",
            [
                'initials' => collect(explode(' ', $currentUser->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                'role' => $currentUser->role->name,
                'role_initial' => 'C',
                'role_color' => '#6b46c1',
                'order_number' => $order->order_number,
            ]
        );

        $this->order['status'] = $nextStatus;
        $this->dispatch('notify', type: 'success', title: 'Desain Disetujui!', message: 'Desain telah Anda setujui. Pesanan akan dilanjutkan ke tahap berikutnya.');
    }

    public function sendRevision()
    {
        $note = trim($this->revisionNote);
        if (!$note) {
            $this->dispatch('notify', type: 'warning', title: 'Catatan kosong', message: 'Silakan tulis catatan revisi terlebih dahulu.');
            return;
        }

        $order = Order::where('order_number', $this->order['id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!in_array($order->status, ['di_design', 'siap_cetak'])) {
            $this->dispatch('notify', type: 'error', message: 'Tidak dapat mengirim revisi pada status ini');
            return;
        }

        if ($order->status === 'siap_cetak') {
            $order->update(['status' => 'di_design']);
            $this->order['status'] = 'di_design';
        }

        OrderStatusHistory::create([
            'order_id'   => $order->id,
            'status'     => $order->status,
            'changed_by' => auth()->id(),
            'notes'      => 'Revisi: ' . $note,
        ]);

        $currentUser = auth()->user();
        Notification::sendToAllStaff(
            'design_revision',
            'Revisi Desain',
            "Customer <strong>{$currentUser->name}</strong> meminta revisi untuk <strong>{$order->order_number}</strong>: {$note}",
            [
                'initials' => collect(explode(' ', $currentUser->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                'role' => $currentUser->role->name,
                'role_initial' => 'C',
                'role_color' => '#d97706',
                'order_number' => $order->order_number,
            ]
        );

        $this->revisionNote = '';
        $this->dispatch('notify', type: 'success', title: 'Revisi Dikirim!', message: 'Catatan revisi Anda telah dikirim ke tim desain.');
        $this->dispatch('revisionSent');
    }

    public function generateShareToken()
    {
        $order = Order::where('order_number', $this->order['id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!$order->share_token) {
            $order->update(['share_token' => Str::random(32)]);
        }

        $this->shareUrl = route('tracking.shared', $order->share_token);
        $this->dispatch('notify', type: 'success', title: 'Link Disalin!', message: 'Link tracking berhasil dibuat.');
        $this->dispatch('shareUrlReady', url: $this->shareUrl);
    }

    public function render()
    {
        return view('livewire.tracking');
    }
}
