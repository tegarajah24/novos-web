<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\OrderItemDetail;
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
        $query = $this->buildFilteredQuery($request);

        $orders = $query->latest()
            ->get()
            ->map(function ($order) {
                $statusMap = $this->getStatusMap();
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
                    'priority' => $order->designRequest?->priority ?? 'normal',
                    'created_at' => $order->created_at->format('d/m/Y'),
                ];
            })
            ->toArray();

        $activeFilter = $request->query('status');
        $activeDateFrom = $request->query('date_from');
        $activeDateTo = $request->query('date_to');
        $activeAssignee = $request->query('assignee');
        $activePriority = $request->query('priority');

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

        return view('internal.daftar-pesanan', compact('orders', 'assignees', 'activeFilter', 'activeDateFrom', 'activeDateTo', 'activeAssignee', 'activePriority'));
    }

    private function getStatusMap(): array
    {
        return [
            'menunggu_pembayaran' => 'menunggu_pembayaran',
            'dikonfirmasi'       => 'menunggu_acc',
            'disetujui'          => 'tahap_desain',
            'di_design'          => 'tahap_desain',
            'siap_cetak'         => 'tahap_produksi',
            'diproduksi'         => 'tahap_produksi',
            'selesai'            => 'selesai',
            'dibatalkan'         => 'dibatalkan',
        ];
    }

    private function buildFilteredQuery(Request $request)
    {
        $dbStatuses = ['menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai', 'dibatalkan'];
        $statusMap = $this->getStatusMap();

        $query = Order::with(['user', 'orderItems', 'designRequest', 'assignee'])
            ->whereIn('status', $dbStatuses);

        // Status filter
        $filterStatus = $request->query('status');
        if ($filterStatus && in_array($filterStatus, array_values($statusMap))) {
            $filteredDbStatuses = array_keys(array_filter($statusMap, fn($v) => $v === $filterStatus));
            $query->whereIn('status', $filteredDbStatuses);
        }

        // Assignee filter
        $filterAssignee = $request->query('assignee');
        if ($filterAssignee === 'unassigned') {
            $query->whereNull('assignee_id');
        } elseif ($filterAssignee) {
            $query->where('assignee_id', $filterAssignee);
        }

        // Priority filter (from design_requests)
        $filterPriority = $request->query('priority');
        if ($filterPriority) {
            $query->whereHas('designRequest', fn($q) => $q->where('priority', $filterPriority));
        }

        // Date range filter
        $activeDateFrom = $request->query('date_from');
        $activeDateTo = $request->query('date_to');

        if ($activeDateFrom && preg_match('/^\d{4}-\d{2}-\d{2}$/', $activeDateFrom)) {
            $query->whereDate('created_at', '>=', $activeDateFrom);
        }
        if ($activeDateTo && preg_match('/^\d{4}-\d{2}-\d{2}$/', $activeDateTo)) {
            $query->whereDate('created_at', '<=', $activeDateTo);
        }

        return $query;
    }

    public function show(Order $order)
    {
        $rawStatus = $order->status;

        $order->load([
            'user',
            'orderItems',
            'itemDetails',
            'designRequest',
            'payment',
            'statusHistories.changedBy',
            'productionTask.assignedTo',
        ]);

        // Status view mapping
        $badgeStatusMap = [
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

            // For existing orders where logo is NOT in design_files
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

        // History notes — only real (non-auto-generated) notes
        $autoGeneratedPrefixes = [
            'Status berubah menjadi',
            'Produksi:',
            'Design selesai',
            'Pembayaran divalidasi',
            'Desain disetujui',
        ];
        $historyNotes = [];
        foreach ($order->statusHistories as $h) {
            $note = $h->notes;
            if (empty($note)) continue;

            $isAuto = false;
            foreach ($autoGeneratedPrefixes as $prefix) {
                if (str_starts_with($note, $prefix)) { $isAuto = true; break; }
            }
            if ($isAuto) continue;

            $roleName = $h->changedBy?->role?->name;
            $origin = match ($roleName) {
                'Design'   => 'Design',
                'Customer' => 'Customer',
                'Produksi' => str_contains($note, 'printing') ? 'Produksi (Printing)'
                            : (str_contains($note, 'jahit')    ? 'Produksi (Jahit)'
                            : (str_contains($note, 'qc') || str_contains($note, 'QC') ? 'Produksi (QC)' : 'Produksi')),
                null       => 'Sistem',
                default    => $roleName,
            };

            $historyNotes[] = [
                'date'   => $h->created_at->format('j M Y, H:i'),
                'user'   => $h->changedBy?->name ?? 'Sistem',
                'origin' => $origin,
                'note'   => $note,
            ];
        }

        // Status history table
        $statusHistory = [];
        foreach ($order->statusHistories as $h) {
            $note = $h->notes;
            if (!empty($note)) {
                foreach ($autoGeneratedPrefixes as $prefix) {
                    if (str_starts_with($note, $prefix)) { $note = '-'; break; }
                }
            } else {
                $note = '-';
            }
            $statusHistory[] = [
                'date'   => $h->created_at->format('j M Y, H:i'),
                'status' => $badgeStatusCodeMap[$h->status] ?? $h->status,
                'note'   => $note,
            ];
        }

        // Stepper
        $stepOrder = ['menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai'];
        $stepLabels = [
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

        $itemDetails = [];
        if ($order->itemDetails && $order->itemDetails->isNotEmpty()) {
            foreach ($order->itemDetails as $detail) {
                $itemDetails[] = [
                    'no_punggung'  => $detail->no_punggung,
                    'nama_punggung' => $detail->nama_punggung,
                    'model_lengan' => $detail->model_lengan,
                    'size'         => $detail->size,
                    'keterangan'   => $detail->keterangan,
                ];
            }
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
                'notes' => $order->designRequest?->additional_notes ?? $order->notes ?? '-',
                'team_name'      => $order->designRequest?->team_name ?? '-',
                'nama_artikel'   => $order->designRequest?->nama_artikel ?? '-',
                'nama_pemesan'   => $order->designRequest?->nama_pemesan ?? '-',
                'detail_sponsor' => $order->designRequest?->detail_sponsor ?? '-',
                'material'       => $order->designRequest?->material ?? '-',
                'collar_style'   => $order->designRequest?->collar_style ?? '-',
                'jenis_potongan' => $order->designRequest?->jenis_potongan ?? '-',
                'lengan_jahitan' => $order->designRequest?->lengan_jahitan ?? '-',
                'priority'       => $order->designRequest?->priority ?? 'normal',
            ],
            'sizes'         => $sizes,
            'item_details'  => $itemDetails,
            'design_files'  => $designFiles,
            'history_notes' => $historyNotes,
            'status_history' => $statusHistory,
            'payment'       => [
                'subtotal'          => (float) ($order->orderItems->sum('subtotal')),
                'biaya_prioritas'   => 0,
                'total'             => (float) ($order->payment?->amount ?? $order->total_price ?? 0),
                'method'            => $order->payment?->payment_method ?? '-',
                'status'            => $order->payment?->status === 'success' ? 'lunas' : 'pending',
                'payment_proof'     => $order->payment?->payment_proof ? asset('storage/' . $order->payment->payment_proof) : null,
                'payment_proof_name' => $order->payment?->payment_proof_name ?? null,
            ],
        ];

        return view('internal.detail-pesanan', compact('order', 'badgeType', 'badgeLabel', 'steps') + ['rawStatus' => $rawStatus]);
    }

    public function assign(AssignOrderRequest $request, Order $order)
    {
        if ($order->assignee_id !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Assignee sudah ditetapkan dan tidak dapat diubah.',
            ], 422);
        }

        if ($request->assignee_id) {
            $assignee = User::find($request->assignee_id);
            if (!$assignee || !$assignee->role || !in_array($assignee->role->name, ['Admin', 'Super Admin', 'Manager'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya admin yang dapat ditetapkan sebagai assignee.',
                ], 422);
            }
        }

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
            'menunggu_pembayaran' => [
                'dikonfirmasi' => ['Admin', 'Manager', 'Super Admin'],
                'dibatalkan'   => ['Admin', 'Manager', 'Super Admin'],
            ],
            'dikonfirmasi' => [
                'disetujui'  => ['Admin', 'Manager', 'Super Admin'],
                'dibatalkan' => ['Admin', 'Manager', 'Super Admin'],
            ],
            'disetujui' => [
                'di_design'  => ['Design'],
                'dibatalkan' => ['Admin', 'Manager', 'Super Admin'],
            ],
            'di_design' => [
                'siap_cetak' => ['Design'],
                'dibatalkan' => ['Admin', 'Manager', 'Super Admin'],
            ],
            'siap_cetak' => [
                'diproduksi' => ['Admin', 'Manager', 'Super Admin'],
                'dibatalkan' => ['Admin', 'Manager', 'Super Admin'],
            ],
            'diproduksi' => [
                'selesai'    => ['Produksi'],
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

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $data = $request->validated();

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

            if ($order->assignee_id === null && $user->isAdmin()) {
                $order->update(['assignee_id' => $user->id]);
            }

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

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:success,rejected',
            'notes'  => 'nullable|string|max:2000',
        ]);

        $newStatus = $request->status;
        $payment = $order->payment;

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada data pembayaran.',
            ], 422);
        }

        if ($payment->status === 'success') {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran sudah lunas.',
            ], 422);
        }

        DB::transaction(function () use ($payment, $newStatus, $request, $order) {
            $payment->update([
                'status' => $newStatus,
                'paid_at' => $newStatus === 'success' ? now() : $payment->paid_at,
            ]);

            $order->statusHistories()->create([
                'status'     => $order->status,
                'changed_by' => auth()->id(),
                'notes'      => $newStatus === 'success'
                    ? 'Pembayaran divalidasi (Lunas)'
                    : 'Pembayaran ditolak: ' . ($request->notes ?? '-'),
            ]);

            $chat = Chat::firstOrCreate([
                'order_id'    => $order->id,
                'customer_id' => $order->user_id,
            ]);

            $msg = $newStatus === 'success'
                ? 'Pembayaran untuk ' . $order->order_number . ' telah divalidasi. Terima kasih!'
                : 'Pembayaran untuk ' . $order->order_number . ' perlu diperbaiki. ' . ($request->notes ?? 'Silakan hubungi admin.');

            ChatMessage::create([
                'chat_id'   => $chat->id,
                'sender_id' => auth()->id(),
                'message'   => $msg,
            ]);
        });

        $title = $newStatus === 'success' ? 'Pembayaran Divalidasi' : 'Pembayaran Ditolak';
        $msg = $newStatus === 'success'
            ? 'Pembayaran pesanan ' . $order->order_number . ' telah divalidasi.'
            : 'Pembayaran pesanan ' . $order->order_number . ' ditolak. ' . ($request->notes ?? '');

        Notification::sendToCustomer(
            $order->user_id,
            'payment_status',
            $title,
            $msg,
            ['order_number' => $order->order_number]
        );

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran berhasil diperbarui.',
        ]);
    }

    public function updateDesignRequest(Request $request, Order $order)
    {
        $request->validate([
            'team_name'      => 'nullable|string|max:255',
            'nama_artikel'   => 'nullable|string|max:255',
            'nama_pemesan'   => 'nullable|string|max:255',
            'detail_sponsor' => 'nullable|string|max:500',
            'material'       => 'nullable|string|max:255',
            'collar_style'   => 'nullable|string|max:255',
            'jenis_potongan' => 'nullable|string|max:255',
            'lengan_jahitan' => 'nullable|string|max:255',
            'additional_notes' => 'nullable|string|max:5000',
            'priority'       => 'nullable|string|in:normal,express,super_express',
        ]);

        if (!$order->designRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini tidak memiliki data desain.',
            ], 422);
        }

        $order->designRequest->update($request->only([
            'team_name', 'nama_artikel', 'nama_pemesan', 'detail_sponsor',
            'material', 'collar_style', 'jenis_potongan', 'lengan_jahitan',
            'additional_notes', 'priority',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Data produk berhasil diperbarui.',
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

        $uiStatusMap = [
            'dikonfirmasi'      => 'menunggu_acc',
            'di_design'         => 'tahap_desain',
            'siap_cetak'        => 'tahap_produksi',
        ];

        $result = [];
        foreach ($allowed as $dbStatus) {
            $result[] = [
                'value' => $uiStatusMap[$dbStatus] ?? $dbStatus,
                'label' => $this->statusLabel($dbStatus),
                'db'    => $dbStatus,
            ];
        }

        return response()->json(['statuses' => $result]);
    }

    public function exportDaftarPesanan(Request $request)
    {
        $query = $this->buildFilteredQuery($request);
        $orders = $query->latest()->get();
        $statusMap = $this->getStatusMap();

        $filename = 'daftar-pesanan-' . now()->format('Ymd-His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($orders, $statusMap) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['Order ID', 'Customer', 'Produk', 'Qty', 'Total', 'Assignee', 'Prioritas', 'Status', 'Tanggal']);

            foreach ($orders as $order) {
                $produk = $order->designRequest
                    ? 'Jersey ' . $order->designRequest->team_name
                    : 'Jersey Custom';

                $assigneeName = $order->assignee ? $order->assignee->name : '-';

                $priorityLabel = match ($order->designRequest?->priority) {
                    'express'       => 'Express',
                    'super_express' => 'Super Express',
                    default         => 'Normal',
                };

                $statusLabelMap = [
                    'menunggu_pembayaran' => 'Menunggu Pembayaran',
                    'dikonfirmasi'       => 'Menunggu ACC',
                    'disetujui'          => 'Tahap Desain',
                    'di_design'          => 'Tahap Desain',
                    'siap_cetak'         => 'Produksi',
                    'diproduksi'         => 'Produksi',
                    'selesai'            => 'Selesai',
                    'dibatalkan'         => 'Dibatalkan',
                ];

                fputcsv($handle, [
                    $order->order_number,
                    $order->user->name ?? 'Unknown',
                    $produk,
                    $order->orderItems->sum('qty'),
                    (float) ($order->total_price ?? 0),
                    $assigneeName,
                    $priorityLabel,
                    $statusLabelMap[$order->status] ?? $order->status,
                    $order->created_at->format('d/m/Y'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCsv(Order $order)
    {
        $order->load('itemDetails');

        $filename = $order->order_number . '-detail-items.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($order) {
            $handle = fopen('php://output', 'w');

            // BOM for UTF-8
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header
            fputcsv($handle, ['No Punggung', 'Nama Punggung', 'Model Lengan', 'Size', 'Keterangan']);

            // Data
            if ($order->itemDetails && $order->itemDetails->isNotEmpty()) {
                foreach ($order->itemDetails as $detail) {
                    fputcsv($handle, [
                        $detail->no_punggung ?? '',
                        $detail->nama_punggung ?? '',
                        $detail->model_lengan ?? '',
                        $detail->size ?? '',
                        $detail->keterangan ?? '',
                    ]);
                }
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
