<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class Design extends Component
{
    use WithFileUploads;

    public $orders = [];
    public $selectedOrderId = null;
    public $isDetailOpen = false;
    public $uploadedFiles = [];
    public $updateStatus = '';
    public $uploading = false;

    protected function getListeners()
    {
        return ['notify'];
    }

    public function mount()
    {
        $this->loadOrders();
    }

    public function loadOrders()
    {
        $this->orders = Order::with(['user', 'designRequest', 'statusHistories' => function ($q) {
                $q->where('notes', 'like', 'Revisi:%')->latest()->take(1);
            }])
            ->whereIn('status', ['disetujui', 'di_design'])
            ->latest()
            ->get()
            ->map(function ($order) {
                $dr = $order->designRequest;
                $revision = $order->statusHistories->first();
                $priority = 'Normal';
                if ($order->admin_notes && preg_match('/Prioritas: (Express|Super Express)/', $order->admin_notes, $matches)) {
                    $priority = $matches[1];
                }
                return [
                    'id' => $order->id,
                    'order_id' => $order->order_number,
                    'customer' => $order->user->name ?? '-',
                    'customer_contact' => $order->user->phone ?? '-',
                    'team_name' => $dr?->team_name ?? 'Jersey Custom',
                    'deadline' => $order->created_at->addDays(7)->format('d M Y'),
                    'priority' => $priority,
                    'revision_note' => $revision?->notes ? str_replace('Revisi: ', '', $revision->notes) : null,
                    'material' => $dr?->material ?? '-',
                    'collar' => $dr?->collar_style ?? '-',
                    'pattern' => $dr?->motif ?? '-',
                    'notes' => nl2br(e($dr?->additional_notes ?? $order->notes ?? 'Tidak ada catatan')),
                    'reference_files' => array_merge(
                        $dr?->logo ? [asset('storage/' . $dr->logo)] : [],
                        collect($dr?->design_files ?? [])->map(fn($f) => asset('storage/' . $f['path']))->values()->toArray(),
                    ),
                ];
            })
            ->values()
            ->toArray();
    }

    public function openDetail($orderId)
    {
        $this->selectedOrderId = $orderId;
        $this->uploadedFiles = [];
        $this->updateStatus = '';
        $this->isDetailOpen = true;
    }

    public function closeDetail()
    {
        $this->isDetailOpen = false;
        $this->selectedOrderId = null;
    }

    public function removeFile($index)
    {
        array_splice($this->uploadedFiles, $index, 1);
    }

    public function submitDesign()
    {
        $this->validate([
            'updateStatus' => 'required',
            'uploadedFiles' => 'required|array|min:1',
        ]);

        $this->uploading = true;

        $order = Order::with('designRequest')->findOrFail($this->selectedOrderId);
        $user = auth()->user();

        $uploadedPaths = [];
        foreach ($this->uploadedFiles as $file) {
            $path = $file->store('design-files/' . $order->order_number, 'public');
            $uploadedPaths[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'type' => $file->getMimeType(),
            ];
        }

        DB::transaction(function () use ($order, $user, $uploadedPaths) {
            $order->update(['status' => 'siap_cetak']);

            if (!empty($uploadedPaths) && $order->designRequest) {
                $existingFiles = $order->designRequest->design_files ?? [];
                $order->designRequest->update([
                    'design_files' => array_merge($existingFiles, $uploadedPaths),
                ]);
            }

            $notes = 'Design selesai dikerjakan';
            if (!empty($uploadedPaths)) {
                $notes .= '. File: ' . implode(', ', array_column($uploadedPaths, 'name'));
            }

            $order->statusHistories()->create([
                'status' => 'siap_cetak',
                'changed_by' => $user->id,
                'notes' => $notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        Notification::sendToAllStaff(
            'design_upload',
            'Desain Siap Cetak',
            "Desain untuk <strong>{$order->order_number}</strong> telah selesai dan siap diproduksi.",
            [
                'initials' => collect(explode(' ', $user->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                'role' => $user->role->name,
                'role_initial' => substr($user->role->name, 0, 1),
                'role_color' => '#6b46c1',
                'order_number' => $order->order_number,
            ]
        );

        Notification::sendToCustomer(
            $order->user_id,
            'design_ready',
            'Desain Selesai',
            'Desain untuk pesanan ' . $order->order_number . ' telah selesai dan siap untuk tahap produksi.',
            ['order_number' => $order->order_number]
        );

        $this->uploading = false;
        $this->closeDetail();
        $this->loadOrders();

        $this->dispatch('notify', type: 'success', message: 'Desain berhasil diselesaikan dan diteruskan ke produksi.');
    }

    public function render()
    {
        return view('livewire.design');
    }
}
