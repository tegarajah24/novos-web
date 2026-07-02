<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class Produksi extends Component
{
    public $orders = [];
    public $activeTab = 'printing';
    public $selectedOrderId = null;
    public $isDetailOpen = false;

    public $updateStatus = '';
    public $productionNote = '';
    public $targetStage = 'jahit';

    public $qcJahitan = false;
    public $qcCacat = false;
    public $qcUkuran = false;
    public $qcDesain = false;
    public $qcPerluRevisi = false;

    public $submitting = false;

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
        $this->orders = Order::with(['user', 'designRequest', 'orderItems', 'statusHistories'])
            ->whereIn('status', ['siap_cetak', 'diproduksi'])
            ->latest()
            ->get()
            ->map(function ($order) {
                $dr = $order->designRequest;
                $sizes = [];
                foreach ($order->orderItems as $item) {
                    $sizes[$item->size] = (int) $item->qty;
                }
                $stage = $order->production_stage ?? 'printing';
                $priority = 'Normal';
                if ($order->admin_notes && preg_match('/Prioritas: (Express|Super Express)/', $order->admin_notes, $matches)) {
                    $priority = $matches[1];
                }
                $prodHistoryNotes = $order->statusHistories
                    ->whereIn('status', ['siap_cetak', 'diproduksi'])
                    ->filter(fn($h) => !empty($h->notes) && !str_starts_with($h->notes, 'Status berubah'))
                    ->map(fn($h) => '[' . $h->created_at->format('d M H:i') . '] ' . $h->notes)
                    ->values();
                $originalNotes = $dr?->additional_notes ?? $order->notes;
                $allNotes = $originalNotes
                    ? collect([$originalNotes])->merge($prodHistoryNotes)->implode("\n\n")
                    : ($prodHistoryNotes->isNotEmpty() ? $prodHistoryNotes->implode("\n\n") : 'Tidak ada catatan');
                return [
                    'id'               => $order->id,
                    'order_id'         => $order->order_number,
                    'customer'         => $order->user->name ?? '-',
                    'customer_contact' => $order->user->phone ?? '-',
                    'team_name'        => $dr?->team_name ?? 'Jersey Custom',
                    'status'           => $order->status,
                    'stage'            => $stage,
                    'deadline'         => $order->created_at->addDays(7)->format('d M Y'),
                    'priority'         => $priority,
                    'material'         => $dr?->material ?? '-',
                    'collar'           => $dr?->collar_style ?? '-',
                    'pattern'          => $dr?->motif ?? '-',
                    'notes'            => nl2br(e($allNotes)),
                    'total_qty'        => (int) $order->orderItems->sum('qty'),
                    'sizes'            => $sizes,
                    'reference_files'  => array_merge(
                        $dr?->logo ? [asset('storage/' . $dr->logo)] : [],
                        collect($dr?->design_files ?? [])->map(fn($f) => asset('storage/' . $f['path']))->values()->toArray(),
                    ),
                    'design_files'     => [],
                ];
            })
            ->values()
            ->toArray();
    }

    public function getFilteredOrdersProperty()
    {
        return collect($this->orders)->where('stage', $this->activeTab)->values()->all();
    }

    public function getQcProgressProperty()
    {
        $count = 0;
        if ($this->qcJahitan) $count++;
        if ($this->qcCacat) $count++;
        if ($this->qcUkuran) $count++;
        if ($this->qcDesain) $count++;
        return $count;
    }

    public function getSelectedOrderProperty()
    {
        return collect($this->orders)->firstWhere('id', $this->selectedOrderId);
    }

    public function openDetail($orderId)
    {
        $this->selectedOrderId = $orderId;
        $order = $this->selectedOrder;

        if ($order['stage'] === 'printing') {
            $this->updateStatus = 'selesai_printing';
        } elseif ($order['stage'] === 'jahit') {
            $this->updateStatus = 'selesai_jahit';
        } else {
            $this->updateStatus = 'selesai_qc';
        }

        $this->productionNote = '';
        $this->targetStage = 'jahit';
        $this->qcJahitan = false;
        $this->qcCacat = false;
        $this->qcUkuran = false;
        $this->qcDesain = false;
        $this->qcPerluRevisi = false;
        $this->isDetailOpen = true;
    }

    public function closeDetail()
    {
        $this->isDetailOpen = false;
        $this->selectedOrderId = null;
    }

    public function submitProduksi()
    {
        $order = Order::with('designRequest')->findOrFail($this->selectedOrderId);
        $user = auth()->user();

        $statusMap = [
            'proses_printing'  => ['stage' => 'printing', 'order_status' => 'siap_cetak'],
            'selesai_printing' => ['stage' => 'jahit',    'order_status' => 'diproduksi'],
            'proses_jahit'     => ['stage' => 'jahit',    'order_status' => 'diproduksi'],
            'selesai_jahit'    => ['stage' => 'qc',       'order_status' => 'diproduksi'],
            'proses_qc'        => ['stage' => 'qc',       'order_status' => 'diproduksi'],
            'selesai_qc'       => ['stage' => null,       'order_status' => 'selesai'],
            'revisi_qc'        => ['stage' => null,       'order_status' => 'diproduksi'],
        ];

        $mapping = $statusMap[$this->updateStatus];
        $newOrderStatus = $mapping['order_status'];
        $newStage = $mapping['stage'];

        DB::transaction(function () use ($order, $newOrderStatus, $newStage, $user) {
            $updateData = ['status' => $newOrderStatus];

            if ($this->updateStatus === 'revisi_qc') {
                $updateData['production_stage'] = $this->targetStage;
            } elseif ($newStage) {
                $updateData['production_stage'] = $newStage;
            } else {
                $updateData['production_stage'] = null;
            }

            $order->update($updateData);

            $order->statusHistories()->create([
                'status'     => $newOrderStatus,
                'changed_by' => $user->id,
                'notes'      => $this->productionNote ?: ('Produksi: ' . str_replace('_', ' ', $this->updateStatus)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        $notifType = str_starts_with($this->updateStatus, 'selesai') ? 'production_done' : 'production_update';

        Notification::sendToAllStaff(
            $notifType,
            $newOrderStatus === 'selesai' ? 'Pesanan Selesai' : 'Update Produksi',
            $newOrderStatus === 'selesai'
                ? "Pesanan <strong>{$order->order_number}</strong> telah selesai diproduksi."
                : "Produksi pesanan <strong>{$order->order_number}</strong> telah diupdate oleh <strong>{$user->name}</strong>.",
            [
                'initials' => collect(explode(' ', $user->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                'role' => $user->role->name,
                'role_initial' => substr($user->role->name, 0, 1),
                'role_color' => '#0284c7',
                'order_number' => $order->order_number,
            ]
        );

        if ($newOrderStatus === 'selesai') {
            Notification::sendToCustomer(
                $order->user_id,
                'production_done',
                'Pesanan Selesai',
                'Pesanan ' . $order->order_number . ' telah selesai diproduksi dan siap untuk dikirim/diambil.',
                ['order_number' => $order->order_number]
            );
        }

        $this->closeDetail();
        $this->loadOrders();

        $message = $newOrderStatus === 'selesai'
            ? 'Pesanan selesai diproduksi.'
            : 'Status produksi berhasil diperbarui.';
        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function render()
    {
        return view('livewire.produksi');
    }
}
