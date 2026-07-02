<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class DetailPesanan extends Component
{
    public string $orderNumber;

    public string $selectedStatus = '';
    public string $statusNote = '';
    public string $validationNote = '';

    public function mount($orderNumber)
    {
        $this->orderNumber = $orderNumber;
    }

    public function getOrderProperty()
    {
        return Order::where('order_number', $this->orderNumber)->firstOrFail();
    }

    public function getRawOrderProperty()
    {
        return $this->order;
    }

    public function getOrderDataProperty()
    {
        $order = $this->order;
        $order->load([
            'user',
            'orderItems',
            'designRequest',
            'payment',
            'statusHistories.changedBy',
            'productionTask.assignedTo',
        ]);

        $badgeStatusMap = [
            'menunggu_validasi' => ['label' => 'Menunggu Verifikasi', 'badge' => 'yellow'],
            'menunggu_pembayaran' => ['label' => 'Menunggu Pembayaran', 'badge' => 'orange'],
            'dikonfirmasi' => ['label' => 'Dikonfirmasi', 'badge' => 'blue'],
            'disetujui' => ['label' => 'Tahap Desain', 'badge' => 'blue'],
            'di_design' => ['label' => 'Tahap Desain', 'badge' => 'blue'],
            'siap_cetak' => ['label' => 'Produksi', 'badge' => 'purple'],
            'diproduksi' => ['label' => 'Produksi', 'badge' => 'purple'],
            'selesai' => ['label' => 'Selesai', 'badge' => 'green'],
            'dibatalkan' => ['label' => 'Dibatalkan', 'badge' => 'red'],
        ];

        $badgeStatusCodeMap = [
            'menunggu_validasi' => 'menunggu_verifikasi',
            'menunggu_pembayaran' => 'menunggu_pembayaran',
            'dikonfirmasi' => 'menunggu_acc',
            'disetujui' => 'tahap_desain',
            'di_design' => 'tahap_desain',
            'siap_cetak' => 'tahap_produksi',
            'diproduksi' => 'tahap_produksi',
            'selesai' => 'selesai',
            'dibatalkan' => 'dibatalkan',
        ];

        $badgeLabel = $badgeStatusMap[$order->status]['label'] ?? $order->status;
        $badgeType = $badgeStatusMap[$order->status]['badge'] ?? 'gray';

        $sizes = [];
        foreach ($order->orderItems as $item) {
            $sizes[$item->size] = $item->qty;
        }

        $designFiles = [];
        if ($order->designRequest) {
            $logoPath = $order->designRequest->logo;

            if ($order->designRequest->design_files) {
                foreach ($order->designRequest->design_files as $i => $file) {
                    $isFirstAndMatchesLogo = ($i === 0 && $logoPath && isset($file['path']) && $file['path'] === $logoPath);
                    $mime = $file['type'] ?? null;
                    if (!$mime && isset($file['path'])) {
                        $ext = strtolower(pathinfo($file['path'], PATHINFO_EXTENSION));
                        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];
                        $mime = in_array($ext, $imageExts) ? 'image/' . ($ext === 'jpg' ? 'jpeg' : $ext) : null;
                    }
                    $designFiles[] = [
                        'name' => $file['name'],
                        'url' => asset('storage/' . $file['path']),
                        'type' => $isFirstAndMatchesLogo ? 'logo' : 'design',
                        'size' => $file['size'] ?? null,
                        'mime' => $mime,
                    ];
                }
            }

            if ($logoPath) {
                $alreadyInDesignFiles = false;
                if ($order->designRequest->design_files) {
                    foreach ($order->designRequest->design_files as $f) {
                        if (isset($f['path']) && $f['path'] === $logoPath) {
                            $alreadyInDesignFiles = true;
                            break;
                        }
                    }
                }
                if (!$alreadyInDesignFiles) {
                    $logoMime = null;
                    $logoExt = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
                    $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];
                    if (in_array($logoExt, $imageExts)) {
                        $logoMime = 'image/' . ($logoExt === 'jpg' ? 'jpeg' : $logoExt);
                    }
                    array_unshift($designFiles, [
                        'name' => basename($logoPath),
                        'url' => asset('storage/' . $logoPath),
                        'type' => 'logo',
                        'mime' => $logoMime,
                    ]);
                }
            }
        }

        $historyNotes = [];
        foreach ($order->statusHistories as $h) {
            $historyNotes[] = [
                'date' => $h->created_at->format('j M Y, H:i'),
                'user' => $h->changedBy?->name ?? 'Sistem',
                'note' => $h->notes ?? 'Status berubah menjadi ' . ($badgeStatusMap[$h->status]['label'] ?? $h->status),
            ];
        }

        $statusHistory = [];
        foreach ($order->statusHistories as $h) {
            $statusHistory[] = [
                'date' => $h->created_at->format('j M Y, H:i'),
                'status' => $badgeStatusCodeMap[$h->status] ?? $h->status,
                'note' => $h->notes ?? '-',
            ];
        }

        $stepOrder = ['menunggu_validasi', 'menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai'];
        $stepLabels = [
            'menunggu_validasi' => 'Menunggu Validasi',
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'dikonfirmasi' => 'Dikonfirmasi',
            'disetujui' => 'Tahap Desain',
            'di_design' => 'Proses Desain',
            'siap_cetak' => 'Siap Cetak',
            'diproduksi' => 'Produksi',
            'selesai' => 'Selesai',
        ];

        $currentIdx = array_search($order->status, $stepOrder);

        if ($currentIdx === false && $order->status === 'dibatalkan') {
            $lastNonCancel = null;
            foreach ($order->statusHistories as $h) {
                if ($h->status !== 'dibatalkan') {
                    $lastNonCancel = $h;
                }
            }
            $currentIdx = $lastNonCancel
                ? array_search($lastNonCancel->status, $stepOrder)
                : 0;
            if ($currentIdx === false) $currentIdx = -1;
        } elseif ($currentIdx === false) {
            $currentIdx = -1;
        }

        $stepDates = [];
        foreach ($order->statusHistories as $h) {
            if (!isset($stepDates[$h->status])) {
                $stepDates[$h->status] = $h->created_at->format('j M Y');
            }
        }

        $steps = [];
        foreach ($stepOrder as $idx => $status) {
            $steps[] = [
                'label' => $stepLabels[$status],
                'date' => $stepDates[$status] ?? null,
                'done' => $currentIdx !== false && $idx < $currentIdx,
                'current' => $currentIdx !== false && $idx === $currentIdx,
            ];
        }

        return [
            'order_id' => $order->order_number,
            'last_update' => $order->updated_at->format('j M Y, H:i'),
            'customer' => [
                'name' => $order->user->name ?? '-',
                'email' => $order->user->email ?? '-',
                'phone' => $order->user->phone ?? '-',
            ],
            'product' => [
                'type' => $order->designRequest ? 'Jersey Custom' : 'Produk Katalog',
                'sport' => $order->designRequest?->motif ?? 'Umum',
                'notes' => $order->designRequest?->additional_notes ?? $order->notes ?? '-',
            ],
            'sizes' => $sizes,
            'design_files' => $designFiles,
            'history_notes' => $historyNotes,
            'status_history' => $statusHistory,
            'payment' => [
                'subtotal' => (float) ($order->orderItems->sum('subtotal')),
                'biaya_prioritas' => 0,
                'total' => (float) ($order->payment?->amount ?? $order->total_price ?? 0),
                'method' => $order->payment?->payment_method ?? '-',
                'status' => $order->payment?->status === 'success' ? 'lunas' : 'pending',
            ],
        ];
    }

    public function getRawStatusProperty()
    {
        return $this->order->status;
    }

    public function getBadgeTypeProperty()
    {
        $map = [
            'menunggu_validasi' => 'yellow',
            'menunggu_pembayaran' => 'orange',
            'dikonfirmasi' => 'blue',
            'disetujui' => 'blue',
            'di_design' => 'blue',
            'siap_cetak' => 'purple',
            'diproduksi' => 'purple',
            'selesai' => 'green',
            'dibatalkan' => 'red',
        ];
        return $map[$this->rawStatus] ?? 'gray';
    }

    public function getBadgeLabelProperty()
    {
        $map = [
            'menunggu_validasi' => 'Menunggu Verifikasi',
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'dikonfirmasi' => 'Dikonfirmasi',
            'disetujui' => 'Tahap Desain',
            'di_design' => 'Tahap Desain',
            'siap_cetak' => 'Produksi',
            'diproduksi' => 'Produksi',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];
        return $map[$this->rawStatus] ?? $this->rawStatus;
    }

    public function getStepsProperty()
    {
        return $this->order_data['steps'] ?? [];
    }

    private function getAllowedTransitions(string $currentStatus, string $roleName): array
    {
        $transitions = [
            'menunggu_validasi' => [
                'menunggu_pembayaran' => ['Admin', 'Manager', 'Super Admin'],
            ],
            'menunggu_pembayaran' => [
                'dibatalkan' => ['Admin', 'Manager', 'Super Admin'],
            ],
            'dikonfirmasi' => [
                'disetujui' => ['Admin', 'Manager', 'Super Admin'],
                'di_design' => ['Admin', 'Manager', 'Super Admin'],
                'dibatalkan' => ['Admin', 'Manager', 'Super Admin'],
            ],
            'disetujui' => [
                'di_design' => ['Admin', 'Manager', 'Super Admin'],
                'dibatalkan' => ['Admin', 'Manager', 'Super Admin'],
            ],
            'di_design' => [
                'siap_cetak' => ['Design', 'Admin', 'Manager', 'Super Admin'],
                'dibatalkan' => ['Admin', 'Manager', 'Super Admin'],
            ],
            'siap_cetak' => [
                'diproduksi' => ['Admin', 'Design', 'Manager', 'Super Admin'],
                'dibatalkan' => ['Admin', 'Manager', 'Super Admin'],
            ],
            'diproduksi' => [
                'selesai' => ['Produksi', 'Admin', 'Manager', 'Super Admin'],
                'dibatalkan' => ['Admin', 'Manager', 'Super Admin'],
            ],
        ];

        $roleName = auth()->user()->role->name ?? '';
        if ($currentStatus !== 'dibatalkan' && in_array($roleName, ['Admin', 'Manager', 'Super Admin'])) {
            $transitions[$currentStatus]['dibatalkan'] = ['Admin', 'Manager', 'Super Admin'];
        }

        $allowed = [];
        if (isset($transitions[$currentStatus])) {
            foreach ($transitions[$currentStatus] as $nextStatus => $roles) {
                if (in_array($roleName, $roles)) {
                    $allowed[] = $nextStatus;
                }
            }
        }

        return $allowed;
    }

    public function getAllowedStatusesProperty()
    {
        $dbTransitions = $this->getAllowedTransitions($this->rawStatus, auth()->user()->role->name ?? '');

        $uiStatusMap = [
            'menunggu_validasi' => 'menunggu_verifikasi',
            'dikonfirmasi' => 'menunggu_acc',
            'di_design' => 'tahap_desain',
            'siap_cetak' => 'tahap_produksi',
        ];

        $statusLabelMap = [
            'menunggu_validasi' => 'Menunggu Validasi',
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'dikonfirmasi' => 'Dikonfirmasi',
            'disetujui' => 'Disetujui',
            'di_design' => 'Di Design',
            'siap_cetak' => 'Siap Cetak',
            'diproduksi' => 'Diproduksi',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];

        $result = [];
        foreach ($dbTransitions as $dbStatus) {
            $result[] = [
                'value' => $uiStatusMap[$dbStatus] ?? $dbStatus,
                'label' => $statusLabelMap[$dbStatus] ?? $dbStatus,
                'db' => $dbStatus,
            ];
        }

        return $result;
    }

    public function validasiPesanan()
    {
        $order = $this->order;

        if ($order->status !== 'menunggu_validasi') {
            $this->dispatch('notify', type: 'error', message: 'Status pesanan tidak dapat divalidasi.');
            return;
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'menunggu_pembayaran']);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'menunggu_pembayaran',
                'changed_by' => auth()->id(),
                'notes' => $this->validationNote ?: 'Pesanan divalidasi oleh admin',
            ]);

            $chat = Chat::firstOrCreate([
                'order_id' => $order->id,
                'customer_id' => $order->user_id,
            ]);

            if (!$chat->admin_id) {
                $chat->update(['admin_id' => auth()->id()]);
            }

            ChatMessage::create([
                'chat_id' => $chat->id,
                'sender_id' => auth()->id(),
                'message' => 'Pesanan ' . $order->order_number . ' telah divalidasi. Silakan lakukan pembayaran.',
            ]);
        });

        $currentUser = auth()->user();
        Notification::sendToAllStaff(
            'order_validated',
            'Pesanan Divalidasi',
            "Pesanan <strong>{$order->order_number}</strong> telah divalidasi oleh <strong>{$currentUser->name}</strong> dan menunggu pembayaran customer.",
            [
                'initials' => collect(explode(' ', $currentUser->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                'role' => $currentUser->role->name,
                'role_initial' => substr($currentUser->role->name, 0, 1),
                'role_color' => '#1a237e',
                'order_number' => $order->order_number,
            ]
        );

        $this->dispatch('notify', type: 'success', message: 'Pesanan berhasil divalidasi. Customer sekarang dapat melanjutkan ke pembayaran.');
    }

    public function updateStatus()
    {
        $this->validate([
            'selectedStatus' => 'required',
        ]);

        $order = $this->order;
        $user = auth()->user();

        $uiStatusMap = [
            'menunggu_verifikasi' => 'menunggu_validasi',
            'menunggu_acc' => 'dikonfirmasi',
            'tahap_desain' => 'di_design',
            'tahap_produksi' => 'siap_cetak',
        ];

        $newDbStatus = $uiStatusMap[$this->selectedStatus] ?? $this->selectedStatus;
        $allowed = $this->getAllowedTransitions($order->status, $user->role->name ?? '');

        if (!in_array($newDbStatus, $allowed)) {
            $this->dispatch('notify', type: 'error', message: 'Transisi status tidak valid.');
            return;
        }

        $statusLabelMap = [
            'menunggu_validasi' => 'Menunggu Validasi',
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'dikonfirmasi' => 'Dikonfirmasi',
            'disetujui' => 'Disetujui',
            'di_design' => 'Di Design',
            'siap_cetak' => 'Siap Cetak',
            'diproduksi' => 'Diproduksi',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];

        DB::transaction(function () use ($order, $newDbStatus, $user, $statusLabelMap) {
            $order->update(['status' => $newDbStatus]);

            $order->statusHistories()->create([
                'status' => $newDbStatus,
                'changed_by' => $user->id,
                'notes' => $this->statusNote ?: 'Status berubah menjadi ' . ($statusLabelMap[$newDbStatus] ?? $newDbStatus),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $chat = Chat::firstOrCreate([
                'order_id' => $order->id,
                'customer_id' => $order->user_id,
            ]);

            if (!$chat->admin_id) {
                $chat->update(['admin_id' => $user->id]);
            }

            $chatMessage = match ($newDbStatus) {
                'menunggu_pembayaran' => 'Pesanan ' . $order->order_number . ' telah divalidasi. Silakan lakukan pembayaran.',
                'disetujui' => 'Pesanan ' . $order->order_number . ' telah disetujui dan akan dikerjakan oleh tim design.',
                'di_design' => 'Pesanan ' . $order->order_number . ' sedang dikerjakan oleh tim design.',
                'siap_cetak' => 'Desain pesanan ' . $order->order_number . ' telah selesai dan siap diproduksi.',
                'diproduksi' => 'Pesanan ' . $order->order_number . ' sedang dalam proses produksi.',
                'selesai' => 'Pesanan ' . $order->order_number . ' telah selesai! Terima kasih.',
                'dibatalkan' => 'Pesanan ' . $order->order_number . ' telah dibatalkan.',
                default => 'Status pesanan ' . $order->order_number . ' telah diperbarui.',
            };

            ChatMessage::create([
                'chat_id' => $chat->id,
                'sender_id' => $user->id,
                'message' => $chatMessage,
            ]);

            $customerTitle = match ($newDbStatus) {
                'menunggu_pembayaran' => 'Pesanan Divalidasi',
                'disetujui' => 'Pesanan Disetujui',
                'di_design' => 'Pesanan Masuk Tahap Desain',
                'siap_cetak' => 'Desain Selesai',
                'diproduksi' => 'Pesanan Diproduksi',
                'selesai' => 'Pesanan Selesai',
                'dibatalkan' => 'Pesanan Dibatalkan',
                default => 'Status Pesanan Diperbarui',
            };
            $customerMessage = match ($newDbStatus) {
                'menunggu_pembayaran' => 'Pesanan Anda telah divalidasi. Silakan lakukan pembayaran.',
                'disetujui' => 'Pesanan Anda telah disetujui dan akan dikerjakan oleh tim design.',
                'di_design' => 'Pesanan Anda sedang dikerjakan oleh tim design.',
                'siap_cetak' => 'Desain pesanan Anda telah selesai dan siap diproduksi.',
                'diproduksi' => 'Pesanan Anda sedang dalam proses produksi.',
                'selesai' => 'Pesanan Anda telah selesai! Terima kasih telah memesan di Novos.',
                'dibatalkan' => 'Pesanan Anda telah dibatalkan.',
                default => 'Status pesanan Anda telah diperbarui.',
            };
            Notification::sendToCustomer(
                $order->user_id,
                'order_status',
                $customerTitle,
                $customerMessage,
                [
                    'order_number' => $order->order_number,
                    'status' => $newDbStatus,
                ]
            );
        });

        Notification::sendToAllStaff(
            'status_update',
            'Status Diperbarui',
            "Status pesanan <strong>{$order->order_number}</strong> berubah menjadi <strong>{$statusLabelMap[$newDbStatus]}</strong> oleh <strong>{$user->name}</strong>.",
            [
                'initials' => collect(explode(' ', $user->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                'role' => $user->role->name,
                'role_initial' => substr($user->role->name, 0, 1),
                'role_color' => '#1a237e',
                'order_number' => $order->order_number,
            ]
        );

        $this->selectedStatus = '';
        $this->statusNote = '';

        $this->dispatch('notify', type: 'success', message: 'Status pesanan berhasil diperbarui ke "' . ($statusLabelMap[$newDbStatus] ?? $newDbStatus) . '".');
    }

    public function render()
    {
        return view('livewire.detail-pesanan');
    }
}
