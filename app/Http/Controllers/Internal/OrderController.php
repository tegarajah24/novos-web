<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $dbStatuses = ['menunggu_validasi', 'menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai', 'dibatalkan'];

        $statusMap = [
            'menunggu_validasi'  => 'menunggu_verifikasi',
            'menunggu_pembayaran' => 'menunggu_pembayaran',
            'dikonfirmasi'       => 'menunggu_acc',
            'disetujui'          => 'tahap_desain',
            'di_design'          => 'tahap_desain',
            'siap_cetak'         => 'tahap_produksi',
            'diproduksi'         => 'tahap_produksi',
            'selesai'            => 'selesai',
            'dibatalkan'         => 'dibatalkan',
        ];

        $filterStatus = $request->query('status');
        $activeFilter = in_array($filterStatus, array_values($statusMap)) ? $filterStatus : null;

        $query = Order::with(['user', 'orderItems', 'designRequest', 'assignee'])
            ->whereIn('status', $dbStatuses);

        if ($activeFilter) {
            $filteredDbStatuses = array_keys(array_filter($statusMap, fn($v) => $v === $activeFilter));
            $query->whereIn('status', $filteredDbStatuses);
        }

        $activeDateFrom = $request->query('date_from');
        $activeDateTo = $request->query('date_to');

        if ($activeDateFrom && preg_match('/^\d{4}-\d{2}-\d{2}$/', $activeDateFrom)) {
            $query->whereDate('created_at', '>=', $activeDateFrom);
        }
        if ($activeDateTo && preg_match('/^\d{4}-\d{2}-\d{2}$/', $activeDateTo)) {
            $query->whereDate('created_at', '<=', $activeDateTo);
        }

        $orders = $query->latest()
            ->get()
            ->map(function ($order) use ($statusMap) {
                $produk = $order->designRequest
                    ? 'Jersey ' . $order->designRequest->team_name
                    : 'Jersey Custom';

                $assigneeId = $order->assignee_id;
                $assigneeName = $order->assignee ? $order->assignee->name : null;

                return [
                    'id'       => $order->id,
                    'order_id' => $order->order_number,
                    'customer' => $order->user->name ?? 'Unknown',
                    'produk'   => $produk,
                    'qty'      => $order->orderItems->sum('qty'),
                    'total'    => (float) ($order->total_price ?? 0),
                    'assignee_id' => $assigneeId,
                    'assignee' => $assigneeName,
                    'status'   => $statusMap[$order->status] ?? $order->status,
                ];
            })
            ->toArray();

        $colorKeys = ['purple', 'blue', 'orange', 'green', 'gray'];
        $assignees = User::with('role')
            ->whereHas('role', fn($q) => $q->where('name', 'Admin'))
            ->get()
            ->map(function ($user) use ($colorKeys) {
                return [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'color' => $colorKeys[array_rand($colorKeys)],
                ];
            })
            ->toArray();

        return view('internal.daftar-pesanan', compact('orders', 'assignees', 'activeFilter', 'activeDateFrom', 'activeDateTo'));
    }

    public function show(Order $order)
    {
        $rawStatus = $order->status;

        $order->load([
            'user',
            'orderItems',
            'designRequest',
            'payment',
            'statusHistories.changedBy',
            'productionTask.assignedTo',
        ]);

        // Status view mapping
        $badgeStatusMap = [
            'menunggu_validasi'  => ['label' => 'Menunggu Verifikasi', 'badge' => 'yellow'],
            'menunggu_pembayaran' => ['label' => 'Menunggu Pembayaran', 'badge' => 'orange'],
            'dikonfirmasi'       => ['label' => 'Dikonfirmasi',        'badge' => 'blue'],
            'disetujui'          => ['label' => 'Tahap Desain',        'badge' => 'blue'],
            'di_design'          => ['label' => 'Tahap Desain',        'badge' => 'blue'],
            'siap_cetak'         => ['label' => 'Produksi',            'badge' => 'purple'],
            'diproduksi'         => ['label' => 'Produksi',            'badge' => 'purple'],
            'selesai'            => ['label' => 'Selesai',             'badge' => 'green'],
            'dibatalkan'         => ['label' => 'Dibatalkan',          'badge' => 'red'],
        ];

        $badgeStatusCodeMap = [
            'menunggu_validasi'  => 'menunggu_verifikasi',
            'menunggu_pembayaran' => 'menunggu_pembayaran',
            'dikonfirmasi'       => 'menunggu_acc',
            'disetujui'          => 'tahap_desain',
            'di_design'          => 'tahap_desain',
            'siap_cetak'         => 'tahap_produksi',
            'diproduksi'         => 'tahap_produksi',
            'selesai'            => 'selesai',
            'dibatalkan'         => 'dibatalkan',
        ];

        $badgeLabel = $badgeStatusMap[$order->status]['label'] ?? $order->status;
        $badgeType  = $badgeStatusMap[$order->status]['badge'] ?? 'gray';

        // Sizes
        $sizes = [];
        foreach ($order->orderItems as $item) {
            $sizes[$item->size] = $item->qty;
        }

        // Design files
        $designFiles = [];
        if ($order->designRequest) {
            if ($order->designRequest->logo) {
                $designFiles[] = [
                    'name' => basename($order->designRequest->logo),
                    'url' => asset('storage/' . $order->designRequest->logo),
                    'type' => 'logo',
                ];
            }
            if ($order->designRequest->design_files) {
                foreach ($order->designRequest->design_files as $file) {
                    $designFiles[] = [
                        'name' => $file['name'],
                        'url' => asset('storage/' . $file['path']),
                        'type' => 'design',
                        'size' => $file['size'] ?? null,
                        'mime' => $file['type'] ?? null,
                    ];
                }
            }
        }

        // History notes
        $historyNotes = [];
        foreach ($order->statusHistories as $h) {
            $historyNotes[] = [
                'date' => $h->created_at->format('j M Y, H:i'),
                'user' => $h->changedBy?->name ?? 'Sistem',
                'note' => $h->notes ?? 'Status berubah menjadi ' . ($badgeStatusMap[$h->status]['label'] ?? $h->status),
            ];
        }

        // Status history table
        $statusHistory = [];
        foreach ($order->statusHistories as $h) {
            $statusHistory[] = [
                'date'   => $h->created_at->format('j M Y, H:i'),
                'status' => $badgeStatusCodeMap[$h->status] ?? $h->status,
                'note'   => $h->notes ?? '-',
            ];
        }

        // Stepper
        $stepOrder = ['menunggu_validasi', 'menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai'];
        $stepLabels = [
            'menunggu_validasi'  => 'Menunggu Validasi',
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'dikonfirmasi'       => 'Dikonfirmasi',
            'disetujui'          => 'Tahap Desain',
            'di_design'          => 'Proses Desain',
            'siap_cetak'         => 'Siap Cetak',
            'diproduksi'         => 'Produksi',
            'selesai'            => 'Selesai',
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
                'label'   => $stepLabels[$status],
                'date'    => $stepDates[$status] ?? null,
                'done'    => $currentIdx !== false && $idx < $currentIdx,
                'current' => $currentIdx !== false && $idx === $currentIdx,
            ];
        }

        $order = [
            'order_id'      => $order->order_number,
            'last_update'   => $order->updated_at->format('j M Y, H:i'),
            'customer'      => [
                'name'  => $order->user->name ?? '-',
                'email' => $order->user->email ?? '-',
                'phone' => $order->user->phone ?? '-',
            ],
            'product'       => [
                'type'  => $order->designRequest ? 'Jersey Custom' : 'Produk Katalog',
                'sport' => $order->designRequest?->motif ?? 'Umum',
                'notes' => $order->designRequest?->additional_notes ?? $order->notes ?? '-',
            ],
            'sizes'         => $sizes,
            'design_files'  => $designFiles,
            'history_notes' => $historyNotes,
            'status_history' => $statusHistory,
            'payment'       => [
                'subtotal'        => (float) ($order->orderItems->sum('subtotal')),
                'biaya_prioritas' => 0,
                'total'           => (float) ($order->payment?->amount ?? $order->total_price ?? 0),
                'method'          => $order->payment?->payment_method ?? '-',
                'status'          => $order->payment?->status === 'success' ? 'lunas' : 'pending',
            ],
        ];

        return view('internal.detail-pesanan', compact('order', 'badgeType', 'badgeLabel', 'steps') + ['rawStatus' => $rawStatus]);
    }

    public function validateOrder(Request $request, Order $order)
    {
        if ($order->status !== 'menunggu_validasi') {
            return response()->json([
                'success' => false,
                'message' => 'Status pesanan tidak dapat divalidasi.',
            ], 422);
        }

        DB::transaction(function () use ($order, $request) {
            $order->update(['status' => 'menunggu_pembayaran']);

            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => 'menunggu_pembayaran',
                'changed_by' => auth()->id(),
                'notes'      => $request->note ?? 'Pesanan divalidasi oleh admin',
            ]);

            $chat = Chat::firstOrCreate([
                'order_id'    => $order->id,
                'customer_id' => $order->user_id,
            ]);

            if (!$chat->admin_id) {
                $chat->update(['admin_id' => auth()->id()]);
            }

            ChatMessage::create([
                'chat_id'   => $chat->id,
                'sender_id' => auth()->id(),
                'message'   => 'Pesanan ' . $order->order_number . ' telah divalidasi. Silakan lakukan pembayaran.',
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

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil divalidasi.',
        ]);
    }

    public function assign(Request $request, Order $order)
    {
        $request->validate([
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        $order->update([
            'assignee_id' => $request->assignee_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Assignee berhasil diperbarui.',
        ]);
    }

    /**
     * Allowed status transitions per role.
     */
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
                'disetujui'  => ['Admin', 'Manager', 'Super Admin'],
                'di_design'  => ['Admin', 'Manager', 'Super Admin'],
                'dibatalkan' => ['Admin', 'Manager', 'Super Admin'],
            ],
            'disetujui' => [
                'di_design'  => ['Admin', 'Manager', 'Super Admin'],
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
                'selesai'    => ['Produksi', 'Admin', 'Manager', 'Super Admin'],
                'dibatalkan' => ['Admin', 'Manager', 'Super Admin'],
            ],
        ];

        // Dibatalkan dari status manapun — Admin / Super Admin
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

    /**
     * Map UI status codes to DB status codes.
     */
    private function toDbStatus(string $uiStatus): string
    {
        return match($uiStatus) {
            'menunggu_verifikasi' => 'menunggu_validasi',
            'menunggu_acc'        => 'dikonfirmasi',
            'tahap_desain'        => 'di_design',
            'tahap_produksi'      => 'siap_cetak',
            default               => $uiStatus,
        };
    }

    /**
     * Map DB status to label for history.
     */
    private function statusLabel(string $dbStatus): string
    {
        return match($dbStatus) {
            'menunggu_validasi'   => 'Menunggu Validasi',
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'dikonfirmasi'        => 'Dikonfirmasi',
            'disetujui'           => 'Disetujui',
            'di_design'           => 'Di Design',
            'siap_cetak'          => 'Siap Cetak',
            'diproduksi'          => 'Diproduksi',
            'selesai'             => 'Selesai',
            'dibatalkan'          => 'Dibatalkan',
            default               => $dbStatus,
        };
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|string',
            'notes'  => 'nullable|string|max:2000',
        ]);

        $user = auth()->user();
        $newDbStatus = $this->toDbStatus($data['status']);
        $allowed = $this->getAllowedTransitions($order->status, $user->role->name ?? '');

        if (!in_array($newDbStatus, $allowed)) {
            return response()->json([
                'success' => false,
                'message' => 'Transisi status tidak valid dari "' . $this->statusLabel($order->status) . '".',
            ], 422);
        }

        DB::transaction(function () use ($order, $newDbStatus, $data, $user) {
            $order->update(['status' => $newDbStatus]);

            $order->statusHistories()->create([
                'status'     => $newDbStatus,
                'changed_by' => $user->id,
                'notes'      => $data['notes'] ?? ('Status berubah menjadi ' . $this->statusLabel($newDbStatus)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Auto chat to customer
            $chat = \App\Models\Chat::firstOrCreate([
                'order_id'    => $order->id,
                'customer_id' => $order->user_id,
            ]);

            if (!$chat->admin_id) {
                $chat->update(['admin_id' => $user->id]);
            }

            $chatMessage = match($newDbStatus) {
                'menunggu_pembayaran' => 'Pesanan ' . $order->order_number . ' telah divalidasi. Silakan lakukan pembayaran.',
                'disetujui'           => 'Pesanan ' . $order->order_number . ' telah disetujui dan akan dikerjakan oleh tim design.',
                'di_design'           => 'Pesanan ' . $order->order_number . ' sedang dikerjakan oleh tim design.',
                'siap_cetak'          => 'Desain pesanan ' . $order->order_number . ' telah selesai dan siap diproduksi.',
                'diproduksi'          => 'Pesanan ' . $order->order_number . ' sedang dalam proses produksi.',
                'selesai'             => 'Pesanan ' . $order->order_number . ' telah selesai! Terima kasih.',
                'dibatalkan'          => 'Pesanan ' . $order->order_number . ' telah dibatalkan.',
                default               => 'Status pesanan ' . $order->order_number . ' telah diperbarui.',
            };

            \App\Models\ChatMessage::create([
                'chat_id'   => $chat->id,
                'sender_id' => $user->id,
                'message'   => $chatMessage,
            ]);

            // Send notification to customer
            $customerTitle = match($newDbStatus) {
                'menunggu_pembayaran' => 'Pesanan Divalidasi',
                'disetujui'           => 'Pesanan Disetujui',
                'di_design'           => 'Pesanan Masuk Tahap Desain',
                'siap_cetak'          => 'Desain Selesai',
                'diproduksi'          => 'Pesanan Diproduksi',
                'selesai'             => 'Pesanan Selesai',
                'dibatalkan'          => 'Pesanan Dibatalkan',
                default               => 'Status Pesanan Diperbarui',
            };
            $customerMessage = match($newDbStatus) {
                'menunggu_pembayaran' => 'Pesanan Anda telah divalidasi. Silakan lakukan pembayaran.',
                'disetujui'           => 'Pesanan Anda telah disetujui dan akan dikerjakan oleh tim design.',
                'di_design'           => 'Pesanan Anda sedang dikerjakan oleh tim design.',
                'siap_cetak'          => 'Desain pesanan Anda telah selesai dan siap diproduksi.',
                'diproduksi'          => 'Pesanan Anda sedang dalam proses produksi.',
                'selesai'             => 'Pesanan Anda telah selesai! Terima kasih telah memesan di Novos.',
                'dibatalkan'          => 'Pesanan Anda telah dibatalkan.',
                default               => 'Status pesanan Anda telah diperbarui.',
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
            "Status pesanan <strong>{$order->order_number}</strong> berubah menjadi <strong>{$this->statusLabel($newDbStatus)}</strong> oleh <strong>{$user->name}</strong>.",
            [
                'initials' => collect(explode(' ', $user->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                'role' => $user->role->name,
                'role_initial' => substr($user->role->name, 0, 1),
                'role_color' => '#1a237e',
                'order_number' => $order->order_number,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diperbarui ke "' . $this->statusLabel($newDbStatus) . '".',
            'new_status' => $newDbStatus,
        ]);
    }

    /**
     * Get allowed next statuses for a given order and current user.
     * Used by the detail view to populate the dropdown.
     */
    public function allowedStatuses(Order $order)
    {
        $user = auth()->user();
        $allowed = $this->getAllowedTransitions($order->status, $user->role->name ?? '');

        $result = [];
        foreach ($allowed as $dbStatus) {
            $result[] = [
                'value' => array_search($dbStatus, [
                    'menunggu_validasi' => 'menunggu_verifikasi',
                    'dikonfirmasi'      => 'menunggu_acc',
                    'di_design'         => 'tahap_desain',
                    'siap_cetak'        => 'tahap_produksi',
                ]) ?: $dbStatus,
                'label' => $this->statusLabel($dbStatus),
                'db'    => $dbStatus,
            ];
        }

        return response()->json(['statuses' => $result]);
    }
}
