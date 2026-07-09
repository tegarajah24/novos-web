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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            'menunggu_spk'       => 'menunggu_spk',
            'diproduksi'         => 'tahap_produksi',
            'selesai'            => 'selesai',
            'dibatalkan'         => 'dibatalkan',
        ];
    }

    private function buildFilteredQuery(Request $request)
    {
        $dbStatuses = ['menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'menunggu_spk', 'diproduksi', 'selesai', 'dibatalkan'];
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
            'siap_cetak'         => ['label' => 'Siap Cetak',          'badge' => 'indigo'],
            'menunggu_spk'       => ['label' => 'Menunggu SPK',        'badge' => 'yellow'],
            'diproduksi'         => ['label' => 'Produksi',            'badge' => 'purple'],
            'selesai'            => ['label' => 'Selesai',             'badge' => 'green'],
            'dibatalkan'         => ['label' => 'Dibatalkan',          'badge' => 'red'],
        ];

        $badgeStatusCodeMap = [
            'menunggu_pembayaran' => 'menunggu_pembayaran',
            'dikonfirmasi'       => 'menunggu_acc',
            'disetujui'          => 'tahap_desain',
            'di_design'          => 'tahap_desain',
            'siap_cetak'         => 'siap_cetak',
            'menunggu_spk'       => 'menunggu_spk',
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
                    $role = $file['role'] ?? null;
                    if ($role) {
                        $isLogo = ($role === 'logo');
                    } else {
                        // Fallback logic for old orders
                        $isLogo = ($i === 0 && $logoPath && isset($file['path']) && $file['path'] === $logoPath);
                    }

                    $mime = $file['type'] ?? null;
                    if (!$mime && isset($file['path'])) {
                        $ext = strtolower(pathinfo($file['path'], PATHINFO_EXTENSION));
                        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];
                        $mime = in_array($ext, $imageExts) ? 'image/' . ($ext === 'jpg' ? 'jpeg' : $ext) : null;
                    }

                    $designFiles[] = [
                        'name' => $file['name'],
                        'url' => asset('storage/' . $file['path']),
                        'path' => $file['path'],
                        'role' => $role,
                        'type' => $isLogo ? 'logo' : 'design',
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
        $stepOrder = ['menunggu_pembayaran', 'dikonfirmasi', 'di_design', 'menunggu_spk', 'siap_cetak', 'diproduksi', 'selesai'];
        $stepLabels = [
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'dikonfirmasi'       => 'Dikonfirmasi',
            'di_design'          => 'Tahap Desain',
            'menunggu_spk'       => 'Menunggu SPK',
            'siap_cetak'         => 'Siap Cetak',
            'diproduksi'         => 'Produksi',
            'selesai'            => 'Selesai',
        ];

        $statusToStepMap = [
            'menunggu_pembayaran' => 'menunggu_pembayaran',
            'dikonfirmasi'        => 'dikonfirmasi',
            'disetujui'           => 'dikonfirmasi',
            'di_design'           => 'di_design',
            'menunggu_spk'        => 'menunggu_spk',
            'siap_cetak'          => 'siap_cetak',
            'diproduksi'          => 'diproduksi',
            'selesai'             => 'selesai',
        ];

        $currentStepKey = $statusToStepMap[$order->status] ?? $order->status;
        $currentIdx = array_search($currentStepKey, $stepOrder);

        if ($currentIdx === false && $order->status === 'dibatalkan') {
            $lastNonCancel = null;
            foreach ($order->statusHistories as $h) {
                if ($h->status !== 'dibatalkan') {
                    $lastNonCancel = $h;
                }
            }
            $lastStatusKey = $lastNonCancel ? ($statusToStepMap[$lastNonCancel->status] ?? $lastNonCancel->status) : 'menunggu_pembayaran';
            $currentIdx = array_search($lastStatusKey, $stepOrder);
            if ($currentIdx === false) $currentIdx = -1;
        } elseif ($currentIdx === false) {
            $currentIdx = -1;
        }

        $stepDates = [];
        foreach ($order->statusHistories as $h) {
            $mappedStatus = $statusToStepMap[$h->status] ?? $h->status;
            if (!isset($stepDates[$mappedStatus])) {
                $stepDates[$mappedStatus] = $h->created_at->format('j M Y');
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

        $deadlineDays = match ($order->designRequest?->priority) {
            'super_express' => 2,
            'express'       => 6,
            default         => 14,
        };

        $order = [
            'order_id'      => $order->order_number,
            'last_update'   => $order->updated_at->format('j M Y, H:i'),
            'tanggal_masuk' => $order->created_at->format('j M Y'),
            'deadline'      => $order->created_at->copy()->addDays($deadlineDays)->format('j M Y'),
            'total_qty'     => $order->orderItems->sum('qty'),
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
                'menunggu_spk' => ['Design', 'Admin', 'Manager', 'Super Admin'],
                'dibatalkan'   => ['Admin', 'Manager', 'Super Admin'],
            ],
            'menunggu_spk' => [
                'siap_cetak' => ['Admin', 'Manager', 'Super Admin'],
                'dibatalkan' => ['Admin', 'Manager', 'Super Admin'],
            ],
            'siap_cetak' => [
                'dibatalkan'   => ['Admin', 'Manager', 'Super Admin'],
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
            'menunggu_spk'        => 'Menunggu SPK',
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
                'siap_cetak'          => 'Pesanan ' . $order->order_number . ' sudah siap diproduksi.',
                'menunggu_spk'        => 'SPK untuk pesanan ' . $order->order_number . ' sedang disiapkan.',
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
                'siap_cetak'          => 'Siap Diproduksi',
                'menunggu_spk'        => 'SPK Sedang Disiapkan',
                'diproduksi'          => 'Pesanan Diproduksi',
                'selesai'             => 'Pesanan Selesai',
                'dibatalkan'          => 'Pesanan Dibatalkan',
                default               => 'Status Pesanan Diperbarui',
            };
            $customerMessage = match($newDbStatus) {
                'menunggu_pembayaran' => 'Pesanan Anda telah divalidasi. Silakan lakukan pembayaran.',
                'disetujui'           => 'Pesanan Anda telah disetujui dan akan dikerjakan oleh tim design.',
                'di_design'           => 'Pesanan Anda sedang dikerjakan oleh tim design.',
                'siap_cetak'          => 'Pesanan Anda sudah siap diproduksi.',
                'menunggu_spk'        => 'SPK untuk pesanan Anda sedang disiapkan. Akan segera masuk produksi.',
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
            $payment = $order->payment()->create([
                'amount'         => $order->total_price ?? 0,
                'status'         => 'pending',
                'payment_method' => 'manual_transfer',
                'notes'          => 'Pembayaran dicatat manual oleh admin.',
            ]);
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
                    'siap_cetak'         => 'Siap Cetak',
                    'menunggu_spk'       => 'Menunggu SPK',
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

    public function exportExcel(Order $order)
    {
        $order->load([
            'user',
            'orderItems',
            'itemDetails',
            'designRequest',
            'payment',
        ]);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('SPK - Detail Produk');

        // Enable default grid lines
        $sheet->setShowGridLines(true);

        // Styling helpers using fully qualified class names
        $styleHeader = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
                'name' => 'Segoe UI',
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1A237E'], // Navy
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $styleSubHeader = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '1A237E'],
                'size' => 10,
                'name' => 'Segoe UI',
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8EAF6'], // Soft Blue
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $styleLabel = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '1A237E'],
                'size' => 9,
                'name' => 'Segoe UI',
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F5F5F7'], // Soft Gray
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $styleValue = [
            'font' => [
                'size' => 9,
                'name' => 'Segoe UI',
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $styleCenter = [
            'font' => [
                'size' => 9,
                'name' => 'Segoe UI',
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $borderThin = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'], // Light Gray
                ],
            ],
        ];

        $borderBox = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '1A237E'],
                ],
            ],
        ];

        // ── SEKTOR KIRI: TITLE SPK ──
        $sheet->setCellValue('A1', 'SURAT PERINTAH KERJA (SPK)');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1:J1')->applyFromArray($styleHeader);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->setCellValue('A2', 'No. Pesanan: ' . $order->order_number);
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2:J2')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
                'name' => 'Segoe UI',
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '283593'], // Darker Blue
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Specs Fields mapping
        $deadlineDays = match ($order->designRequest?->priority) {
            'super_express' => 2,
            'express'       => 6,
            default         => 14,
        };

        $leftFields = [
            ['label1' => 'Jenis',            'val1' => $order->designRequest ? 'Jersey Custom' : 'Produk Katalog', 'label2' => 'Bahan',            'val2' => $order->designRequest?->material ?? '-'],
            ['label1' => 'Nama Tim',         'val1' => $order->designRequest?->team_name ?? '-',                   'label2' => 'Kerah',            'val2' => $order->designRequest?->collar_style ?? '-'],
            ['label1' => 'Nama Artikel',     'val1' => $order->designRequest?->nama_artikel ?? '-',                'label2' => 'Jenis Potongan',   'val2' => $order->designRequest?->jenis_potongan ?? '-'],
            ['label1' => 'Nama Pemesan',     'val1' => $order->designRequest?->nama_pemesan ?? '-',                'label2' => 'Lengan & Jahitan', 'val2' => $order->designRequest?->lengan_jahitan ?? '-'],
            ['label1' => 'Detail Sponsor',   'val1' => $order->designRequest?->detail_sponsor ?? '-',              'label2' => 'Prioritas',        'val2' => $order->designRequest?->priority === 'express' ? 'Express' : ($order->designRequest?->priority === 'super_express' ? 'Super Express' : 'Normal')],
            ['label1' => 'Tanggal Masuk',    'val1' => $order->created_at->format('j M Y'),                        'label2' => 'Deadline',         'val2' => $order->created_at->copy()->addDays($deadlineDays)->format('j M Y')],
            ['label1' => 'Total Qty',        'val1' => $order->orderItems->sum('qty') . ' pcs',                    'label2' => '',                 'val2' => ''],
        ];

        // Render left fields
        // Layout:
        // Col A (Label 1), Col B:E (Value 1), Col F:G (Label 2), Col H:J (Value 2)
        $currentRow = 4;
        foreach ($leftFields as $rowPair) {
            // Field 1
            $sheet->setCellValue('A' . $currentRow, $rowPair['label1']);
            $sheet->getStyle('A' . $currentRow)->applyFromArray($styleLabel);

            $sheet->setCellValue('B' . $currentRow, $rowPair['val1']);
            $sheet->getStyle('B' . $currentRow)->applyFromArray($styleValue);

            // Field 2
            if ($rowPair['label2']) {
                $sheet->mergeCells('B' . $currentRow . ':E' . $currentRow);

                $sheet->setCellValue('F' . $currentRow, $rowPair['label2']);
                $sheet->mergeCells('F' . $currentRow . ':G' . $currentRow);
                $sheet->getStyle('F' . $currentRow . ':G' . $currentRow)->applyFromArray($styleLabel);

                $sheet->setCellValue('H' . $currentRow, $rowPair['val2']);
                $sheet->mergeCells('H' . $currentRow . ':J' . $currentRow);
                $sheet->getStyle('H' . $currentRow . ':J' . $currentRow)->applyFromArray($styleValue);
            } else {
                $sheet->mergeCells('B' . $currentRow . ':J' . $currentRow);
            }
            $sheet->getStyle('A' . $currentRow . ':J' . $currentRow)->applyFromArray($borderThin);
            $sheet->getRowDimension($currentRow)->setRowHeight(20);
            $currentRow++;
        }
        $sheet->getStyle('A4:J' . ($currentRow - 1))->applyFromArray($borderBox);

        // ── SIZING GRID SECTION ──
        $currentRow += 2;
        $sizingStartRow = $currentRow;
        $sheet->setCellValue('A' . $currentRow, 'RINCIAN TOTAL UKURAN');
        $sheet->mergeCells('A' . $currentRow . ':J' . $currentRow);
        $sheet->getStyle('A' . $currentRow . ':J' . $currentRow)->applyFromArray($styleHeader);
        $sheet->getRowDimension($currentRow)->setRowHeight(22);
        $currentRow++;

        // Parse Sizes from itemDetails
        $dewasaSizes = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '6XL'];
        $anakSizes   = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL'];

        $sizingGrid = [
            'dewasa' => [
                'short' => array_fill_keys($dewasaSizes, 0),
                'long'  => array_fill_keys($dewasaSizes, 0),
            ],
            'anak' => [
                'short' => array_fill_keys($anakSizes, 0),
                'long'  => array_fill_keys($anakSizes, 0),
            ]
        ];

        foreach ($order->itemDetails as $detail) {
            $sizeStr = strtoupper(trim($detail->size ?? ''));
            $modelLengan = strtolower(trim($detail->model_lengan ?? ''));
            $sleeveType = (str_contains($modelLengan, 'long')) ? 'long' : 'short';

            if (str_contains(strtolower($sizeStr), 'anak')) {
                $cleanSize = trim(str_replace('ANAK', '', $sizeStr));
                if ($cleanSize === 'XXL') $cleanSize = '2XL';
                if ($cleanSize === 'XXXL') $cleanSize = '3XL';

                if (array_key_exists($cleanSize, $sizingGrid['anak'][$sleeveType])) {
                    $sizingGrid['anak'][$sleeveType][$cleanSize]++;
                }
            } else {
                $cleanSize = $sizeStr;
                if ($cleanSize === 'XXL') $cleanSize = '2XL';
                if ($cleanSize === 'XXXL') $cleanSize = '3XL';

                if (array_key_exists($cleanSize, $sizingGrid['dewasa'][$sleeveType])) {
                    $sizingGrid['dewasa'][$sleeveType][$cleanSize]++;
                }
            }
        }

        // DEWASA
        $sheet->setCellValue('A' . $currentRow, 'DEWASA');
        $sheet->getStyle('A' . $currentRow)->applyFromArray($styleSubHeader);
        $colIdx = 'B';
        foreach ($dewasaSizes as $sz) {
            $sheet->setCellValue($colIdx . $currentRow, $sz);
            $sheet->getStyle($colIdx . $currentRow)->applyFromArray($styleSubHeader);
            $colIdx++;
        }
        $sheet->getRowDimension($currentRow)->setRowHeight(20);
        $currentRow++;

        $sheet->setCellValue('A' . $currentRow, 'Lengan Pendek');
        $sheet->getStyle('A' . $currentRow)->applyFromArray($styleLabel);
        $colIdx = 'B';
        foreach ($dewasaSizes as $sz) {
            $sheet->setCellValue($colIdx . $currentRow, $sizingGrid['dewasa']['short'][$sz]);
            $sheet->getStyle($colIdx . $currentRow)->applyFromArray($styleCenter);
            $colIdx++;
        }
        $sheet->getStyle('A' . $currentRow . ':J' . $currentRow)->applyFromArray($borderThin);
        $sheet->getRowDimension($currentRow)->setRowHeight(20);
        $currentRow++;

        $sheet->setCellValue('A' . $currentRow, 'Lengan Panjang');
        $sheet->getStyle('A' . $currentRow)->applyFromArray($styleLabel);
        $colIdx = 'B';
        foreach ($dewasaSizes as $sz) {
            $sheet->setCellValue($colIdx . $currentRow, $sizingGrid['dewasa']['long'][$sz]);
            $sheet->getStyle($colIdx . $currentRow)->applyFromArray($styleCenter);
            $colIdx++;
        }
        $sheet->getStyle('A' . $currentRow . ':J' . $currentRow)->applyFromArray($borderThin);
        $sheet->getRowDimension($currentRow)->setRowHeight(20);
        $currentRow++;

        // ANAK
        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, 'ANAK');
        $sheet->getStyle('A' . $currentRow)->applyFromArray($styleSubHeader);
        $colIdx = 'B';
        foreach ($anakSizes as $sz) {
            $sheet->setCellValue($colIdx . $currentRow, $sz);
            $sheet->getStyle($colIdx . $currentRow)->applyFromArray($styleSubHeader);
            $colIdx++;
        }
        // Blank cells at the end of Anak sizes header (Columns I & J)
        $sheet->getStyle('I' . $currentRow . ':J' . $currentRow)->applyFromArray($styleSubHeader);
        $sheet->getRowDimension($currentRow)->setRowHeight(20);
        $currentRow++;

        $sheet->setCellValue('A' . $currentRow, 'Lengan Pendek');
        $sheet->getStyle('A' . $currentRow)->applyFromArray($styleLabel);
        $colIdx = 'B';
        foreach ($anakSizes as $sz) {
            $sheet->setCellValue($colIdx . $currentRow, $sizingGrid['anak']['short'][$sz]);
            $sheet->getStyle($colIdx . $currentRow)->applyFromArray($styleCenter);
            $colIdx++;
        }
        $sheet->getStyle('A' . $currentRow . ':J' . $currentRow)->applyFromArray($borderThin);
        $sheet->getRowDimension($currentRow)->setRowHeight(20);
        $currentRow++;

        $sheet->setCellValue('A' . $currentRow, 'Lengan Panjang');
        $sheet->getStyle('A' . $currentRow)->applyFromArray($styleLabel);
        $colIdx = 'B';
        foreach ($anakSizes as $sz) {
            $sheet->setCellValue($colIdx . $currentRow, $sizingGrid['anak']['long'][$sz]);
            $sheet->getStyle($colIdx . $currentRow)->applyFromArray($styleCenter);
            $colIdx++;
        }
        $sheet->getStyle('A' . $currentRow . ':J' . $currentRow)->applyFromArray($borderThin);
        $sheet->getRowDimension($currentRow)->setRowHeight(20);
        $currentRow++;
        $sheet->getStyle('A' . $sizingStartRow . ':J' . ($currentRow - 1))->applyFromArray($borderBox);


        // ── IMAGES BOX SECTION ──
        $logoPath = $order->designRequest?->logo;
        $designFiles = $order->designRequest?->design_files ?? [];
 
        $logoPaths = [];
        $refPaths = [];
 
        if ($designFiles) {
            foreach ($designFiles as $f) {
                $path = $f['path'] ?? null;
                if (!$path) continue;
 
                $role = $f['role'] ?? null;
                if ($role) {
                    if ($role === 'logo') {
                        $logoPaths[] = $path;
                    } else {
                        $refPaths[] = $path;
                    }
                } else {
                    // Fallback logic for old orders
                    if ($path === $logoPath) {
                        $logoPaths[] = $path;
                    } else {
                        $refPaths[] = $path;
                    }
                }
            }
        }
 
        // Fallback: If logoPaths is empty but logoPath is set, add it
        if (empty($logoPaths) && $logoPath) {
            $logoPaths[] = $logoPath;
        }
 
        if (!empty($logoPaths) || !empty($refPaths)) {
            $currentRow += 2;
            $sheet->setCellValue('A' . $currentRow, 'REFERENSI DESAIN & LOGO');
            $sheet->mergeCells('A' . $currentRow . ':J' . $currentRow);
            $sheet->getStyle('A' . $currentRow . ':J' . $currentRow)->applyFromArray($styleHeader);
            $sheet->getRowDimension($currentRow)->setRowHeight(22);
            $currentRow++;
 
            // Labels Row
            $labelRow = $currentRow;
            $sheet->setCellValue('A' . $labelRow, 'Logo Tim');
            $sheet->mergeCells('A' . $labelRow . ':D' . $labelRow);
            $sheet->getStyle('A' . $labelRow . ':D' . $labelRow)->applyFromArray($styleSubHeader);
 
            $sheet->setCellValue('F' . $labelRow, 'Referensi Desain');
            $sheet->mergeCells('F' . $labelRow . ':J' . $labelRow);
            $sheet->getStyle('F' . $labelRow . ':J' . $labelRow)->applyFromArray($styleSubHeader);
            $sheet->getRowDimension($labelRow)->setRowHeight(20);
            $currentRow++;
 
            // Image Box Row (11 rows height)
            $imageStartRow = $currentRow;
            $sheet->mergeCells('A' . $imageStartRow . ':D' . ($imageStartRow + 10));
            $sheet->getStyle('A' . $imageStartRow . ':D' . ($imageStartRow + 10))->applyFromArray($borderBox);
 
            $sheet->mergeCells('F' . $imageStartRow . ':J' . ($imageStartRow + 10));
            $sheet->getStyle('F' . $imageStartRow . ':J' . ($imageStartRow + 10))->applyFromArray($borderBox);
 
            for ($r = $imageStartRow; $r <= $imageStartRow + 10; $r++) {
                $sheet->getRowDimension($r)->setRowHeight(18);
            }
 
            // Draw Logo Tim inside A:D box
            $numLogos = count($logoPaths);
            if ($numLogos === 1) {
                $fullPath = storage_path('app/public/' . $logoPaths[0]);
                if (file_exists($fullPath)) {
                    try {
                        $drawing = new Drawing();
                        $drawing->setPath($fullPath);
                        $drawing->setHeight(160);
                        $drawing->setCoordinates('B' . ($imageStartRow + 1)); // center in A:D
                        $drawing->setWorksheet($sheet);
                    } catch (\Exception $e) {}
                }
            } elseif ($numLogos === 2) {
                $coords = ['A', 'C'];
                foreach ($logoPaths as $idx => $path) {
                    if ($idx >= 2) break;
                    $fullPath = storage_path('app/public/' . $path);
                    if (file_exists($fullPath)) {
                        try {
                            $drawing = new Drawing();
                            $drawing->setPath($fullPath);
                            $drawing->setHeight(150);
                            $drawing->setCoordinates($coords[$idx] . ($imageStartRow + 1));
                            $drawing->setWorksheet($sheet);
                        } catch (\Exception $e) {}
                    }
                }
            } elseif ($numLogos >= 3) {
                $coords = ['A', 'B', 'C'];
                $maxHeight = ($numLogos === 3) ? 120 : 100;
                foreach ($logoPaths as $idx => $path) {
                    if ($idx >= 3) break;
                    $fullPath = storage_path('app/public/' . $path);
                    if (file_exists($fullPath)) {
                        try {
                            $drawing = new Drawing();
                            $drawing->setPath($fullPath);
                            $drawing->setHeight($maxHeight);
                            $drawing->setCoordinates($coords[$idx] . ($imageStartRow + 2));
                            $drawing->setWorksheet($sheet);
                        } catch (\Exception $e) {}
                    }
                }
            }
 
            // Draw Referensi Desain inside F:J box
            $numRefs = count($refPaths);
            if ($numRefs === 1) {
                $fullPath = storage_path('app/public/' . $refPaths[0]);
                if (file_exists($fullPath)) {
                    try {
                        $drawing = new Drawing();
                        $drawing->setPath($fullPath);
                        $drawing->setHeight(160);
                        $drawing->setCoordinates('H' . ($imageStartRow + 1)); // center in F:J (column H)
                        $drawing->setWorksheet($sheet);
                    } catch (\Exception $e) {}
                }
            } elseif ($numRefs === 2) {
                $coords = ['F', 'I'];
                foreach ($refPaths as $idx => $path) {
                    if ($idx >= 2) break;
                    $fullPath = storage_path('app/public/' . $path);
                    if (file_exists($fullPath)) {
                        try {
                            $drawing = new Drawing();
                            $drawing->setPath($fullPath);
                            $drawing->setHeight(150);
                            $drawing->setCoordinates($coords[$idx] . ($imageStartRow + 1));
                            $drawing->setWorksheet($sheet);
                        } catch (\Exception $e) {}
                    }
                }
            } elseif ($numRefs >= 3) {
                $coords = ['F', 'H', 'J'];
                $maxHeight = ($numRefs === 3) ? 120 : 100;
                foreach ($refPaths as $idx => $path) {
                    if ($idx >= 3) break;
                    $fullPath = storage_path('app/public/' . $path);
                    if (file_exists($fullPath)) {
                        try {
                            $drawing = new Drawing();
                            $drawing->setPath($fullPath);
                            $drawing->setHeight($maxHeight);
                            $drawing->setCoordinates($coords[$idx] . ($imageStartRow + 2));
                            $drawing->setWorksheet($sheet);
                        } catch (\Exception $e) {}
                    }
                }
            }
 
            $currentRow = $imageStartRow + 11;
        }
        $leftMaxRow = $currentRow;

        // ── SEKTOR KANAN: DETAIL DATA PESANAN (DAFTAR PEMAIN) ──
        $sheet->setCellValue('L1', 'DETAIL DATA PESANAN');
        $sheet->mergeCells('L1:P1');
        $sheet->getStyle('L1:P1')->applyFromArray($styleHeader);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->setCellValue('L2', 'Tabel Rincian Jersey & Nama Punggung');
        $sheet->mergeCells('L2:P2');
        $sheet->getStyle('L2:P2')->applyFromArray([
            'font' => [
                'italic' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 9,
                'name' => 'Segoe UI',
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '283593'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Player table headers
        $sheet->setCellValue('L4', 'No');
        $sheet->setCellValue('M4', 'Nama Punggung');
        $sheet->setCellValue('N4', 'No Punggung');
        $sheet->setCellValue('O4', 'Size');
        $sheet->setCellValue('P4', 'Keterangan');
        $sheet->getStyle('L4:P4')->applyFromArray($styleSubHeader);
        $sheet->getStyle('L4:P4')->applyFromArray($borderThin);
        $sheet->getRowDimension(4)->setRowHeight(20);

        $rightRow = 5;
        if ($order->itemDetails && $order->itemDetails->isNotEmpty()) {
            foreach ($order->itemDetails as $index => $detail) {
                $sheet->setCellValue('L' . $rightRow, $index + 1);
                $sheet->getStyle('L' . $rightRow)->applyFromArray($styleCenter);

                $sheet->setCellValue('M' . $rightRow, $detail->nama_punggung ?? '-');
                $sheet->getStyle('M' . $rightRow)->applyFromArray($styleValue);

                $sheet->setCellValue('N' . $rightRow, $detail->no_punggung ?? '-');
                $sheet->getStyle('N' . $rightRow)->applyFromArray($styleCenter);

                $sheet->setCellValue('O' . $rightRow, $detail->size ?? '-');
                $sheet->getStyle('O' . $rightRow)->applyFromArray($styleCenter);

                $sheet->setCellValue('P' . $rightRow, $detail->keterangan ?? '-');
                $sheet->getStyle('P' . $rightRow)->applyFromArray($styleValue);

                $sheet->getStyle('L' . $rightRow . ':P' . $rightRow)->applyFromArray($borderThin);
                $sheet->getRowDimension($rightRow)->setRowHeight(18);
                $rightRow++;
            }
        } else {
            $sheet->setCellValue('L' . $rightRow, 'Tidak ada data item pesanan.');
            $sheet->mergeCells('L' . $rightRow . ':P' . $rightRow);
            $sheet->getStyle('L' . $rightRow)->applyFromArray($styleValue);
            $sheet->getStyle('L' . $rightRow . ':P' . $rightRow)->applyFromArray($borderThin);
            $sheet->getRowDimension($rightRow)->setRowHeight(20);
            $rightRow++;
        }
        $sheet->getStyle('L4:P' . ($rightRow - 1))->applyFromArray($borderBox);

        // ── VALIDASI PRODUKSI SECTION ──
        $maxRow = max($leftMaxRow, $rightRow) + 3;

        $sheet->setCellValue('A' . $maxRow, 'DESAINER');
        $sheet->mergeCells('A' . $maxRow . ':C' . $maxRow);
        $sheet->getStyle('A' . $maxRow . ':C' . $maxRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'name' => 'Segoe UI', 'color' => ['rgb' => '1A237E']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->setCellValue('E' . $maxRow, 'DIVISI I (POTONG/PRINT)');
        $sheet->mergeCells('E' . $maxRow . ':G' . $maxRow);
        $sheet->getStyle('E' . $maxRow . ':G' . $maxRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'name' => 'Segoe UI', 'color' => ['rgb' => '1A237E']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->setCellValue('H' . $maxRow, 'DIVISI II (SEWING/QC)');
        $sheet->mergeCells('H' . $maxRow . ':J' . $maxRow);
        $sheet->getStyle('H' . $maxRow . ':J' . $maxRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'name' => 'Segoe UI', 'color' => ['rgb' => '1A237E']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        $maxRow4 = $maxRow + 4;
        $sheet->setCellValue('A' . $maxRow4, '(...................................)');
        $sheet->mergeCells('A' . $maxRow4 . ':C' . $maxRow4);
        $sheet->getStyle('A' . $maxRow4 . ':C' . $maxRow4)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('E' . $maxRow4, '(...................................)');
        $sheet->mergeCells('E' . $maxRow4 . ':G' . $maxRow4);
        $sheet->getStyle('E' . $maxRow4 . ':G' . $maxRow4)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('H' . $maxRow4, '(...................................)');
        $sheet->mergeCells('H' . $maxRow4 . ':J' . $maxRow4);
        $sheet->getStyle('H' . $maxRow4 . ':J' . $maxRow4)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // ── Column Widths Configuration ──
        $sheet->getColumnDimension('A')->setWidth(18); // fits "Lengan Panjang/Pendek"
        foreach (range('B', 'J') as $col) {
            $sheet->getColumnDimension($col)->setWidth(8); // compact sizing grid columns
        }
        $sheet->getColumnDimension('K')->setWidth(4); // separator column

        // Auto-fit player table columns on the right (L to P)
        foreach (['L', 'M', 'N', 'O', 'P'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = $order->order_number . '-detail-pesanan.xlsx';

        $tempFile = tempnam(sys_get_temp_dir(), 'export');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function exportSpk(Order $order)
    {
        $order->load([
            'user',
            'orderItems',
            'designRequest',
        ]);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // ── Styling helpers ──
        $navy       = '1A237E';
        $darkerNavy = '283593';
        $labelBg    = 'F5F5F7';
        $sectionBg  = 'DAE3F3';
        $gridBg     = 'F2F2F2';

        $fontSegoe  = 'Segoe UI';
        $fontCalibri = 'Calibri';

        $styleHdr = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 14, 'name' => $fontSegoe],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $navy]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ];
        $styleSub = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12, 'name' => $fontSegoe],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $darkerNavy]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ];
        $styleLbl = [
            'font'      => ['bold' => true, 'color' => ['rgb' => $navy], 'size' => 10, 'name' => $fontSegoe],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $labelBg]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ];
        $styleVal = [
            'font'      => ['size' => 10, 'name' => $fontSegoe],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ];
        $styleValLeft = [
            'font'      => ['size' => 10, 'name' => $fontSegoe],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ];

        // Pre-combined border arrays (top+bottom+left sides as needed)
        $BORDER_MEDIUM = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM;
        $BORDER_THIN   = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN;
        $BLACK = ['rgb' => '000000'];

        $bdrFirstT     = ['borders' => ['top'   => ['borderStyle' => $BORDER_MEDIUM, 'color' => $BLACK]]];
        $bdrFirstTL    = ['borders' => ['top'   => ['borderStyle' => $BORDER_MEDIUM, 'color' => $BLACK],
                                        'left'  => ['borderStyle' => $BORDER_MEDIUM, 'color' => $BLACK]]];
        $bdrRowT       = ['borders' => ['top'   => ['borderStyle' => $BORDER_THIN,   'color' => $BLACK]]];
        $bdrRowTL      = ['borders' => ['top'   => ['borderStyle' => $BORDER_THIN,   'color' => $BLACK],
                                        'left'  => ['borderStyle' => $BORDER_MEDIUM, 'color' => $BLACK]]];
        $bdrLastTB     = ['borders' => ['top'   => ['borderStyle' => $BORDER_THIN,   'color' => $BLACK],
                                        'bottom'=> ['borderStyle' => $BORDER_MEDIUM, 'color' => $BLACK]]];
        $bdrLastTLB    = ['borders' => ['top'   => ['borderStyle' => $BORDER_THIN,   'color' => $BLACK],
                                        'bottom'=> ['borderStyle' => $BORDER_MEDIUM, 'color' => $BLACK],
                                        'left'  => ['borderStyle' => $BORDER_MEDIUM, 'color' => $BLACK]]];
        $bdrLeftOnly   = ['borders' => ['left'  => ['borderStyle' => $BORDER_MEDIUM, 'color' => $BLACK]]];
        $bdrTopBottomM = ['borders' => ['top'   => ['borderStyle' => $BORDER_MEDIUM, 'color' => $BLACK],
                                        'bottom'=> ['borderStyle' => $BORDER_MEDIUM, 'color' => $BLACK]]];
        $bdrTBMLeft    = ['borders' => ['top'   => ['borderStyle' => $BORDER_MEDIUM, 'color' => $BLACK],
                                        'bottom'=> ['borderStyle' => $BORDER_MEDIUM, 'color' => $BLACK],
                                        'left'  => ['borderStyle' => $BORDER_MEDIUM, 'color' => $BLACK]]];
        $bdrThinTop    = ['borders' => ['top'   => ['borderStyle' => $BORDER_THIN,   'color' => $BLACK]]];
        $bdrThinTopL   = ['borders' => ['top'   => ['borderStyle' => $BORDER_THIN,   'color' => $BLACK],
                                        'left'  => ['borderStyle' => $BORDER_MEDIUM, 'color' => $BLACK]]];
        $bdrBottomM    = ['borders' => ['bottom'=> ['borderStyle' => $BORDER_MEDIUM, 'color' => $BLACK]]];
        $bdrOutline    = ['borders' => ['outline'=> ['borderStyle' => $BORDER_MEDIUM, 'color' => $BLACK]]];
        $bdrThinAll    = ['borders' => ['allBorders' => ['borderStyle' => $BORDER_THIN,  'color' => $BLACK]]];

        $designFiles = $order->designRequest?->design_files ?? [];
        $deadlineDays = match ($order->designRequest?->priority) {
            'super_express' => 2,
            'express'       => 6,
            default         => 14,
        };

        // ═══════════════════════════════════════════════════════════════
        // SHEET 1 — "depan"  (Portrait A4)
        // ═══════════════════════════════════════════════════════════════
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('depan');
        $sheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->setShowGridLines(true);
        $sheet->getColumnDimension('A')->setWidth(9.22);
        // Thin border grid di semua sel sebagai base
        $sheet->getStyle('A1:J44')->applyFromArray($bdrThinAll);

        // ── Row 1: Title ──
        $sheet->setCellValue('A1', 'SURAT PERINTAH KERJA (SPK)');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1:J1')->applyFromArray($styleHdr);
        $sheet->getRowDimension(1)->setRowHeight(18);

        // ── Row 2: Order number ──
        $sheet->setCellValue('A2', 'No. Pesanan: ' . $order->order_number);
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2:J2')->applyFromArray($styleSub);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // ── Rows 4-9: Specs grid ──
        $specs = [
            ['Jenis',            'Jersey Custom',                                                'Bahan',            $order->designRequest?->material ?? '-'],
            ['Nama Tim',         $order->designRequest?->team_name ?? '-',                        'Kerah',            $order->designRequest?->collar_style ?? '-'],
            ['Nama Artikel',     $order->designRequest?->nama_artikel ?? '-',                     'Jenis Potongan',   $order->designRequest?->jenis_potongan ?? '-'],
            ['Nama Pemesan',     $order->designRequest?->nama_pemesan ?? $order->user?->name ?? '-', 'Lengan & Jahitan', $order->designRequest?->lengan_jahitan ?? '-'],
            ['Detail Sponsor',   $order->designRequest?->detail_sponsor ?? '-',                   'Prioritas',        ucfirst($order->designRequest?->priority ?? 'Normal')],
            ['Tanggal Masuk',    $order->created_at->format('d M Y'),                              'Deadline',         $order->created_at->addDays($deadlineDays)->format('d M Y')],
        ];

        for ($i = 0; $i < count($specs); $i++) {
            $r = 4 + $i;
            list($lbl1, $val1, $lbl2, $val2) = $specs[$i];

            // Left label (A:B)
            $sheet->setCellValue('A' . $r, $lbl1);
            $sheet->mergeCells('A' . $r . ':B' . $r);
            $sheet->getStyle('A' . $r . ':B' . $r)->applyFromArray($styleLbl);

            // Left value (C:E)
            $sheet->setCellValue('C' . $r, $val1);
            $sheet->mergeCells('C' . $r . ':E' . $r);
            $sheet->getStyle('C' . $r . ':E' . $r)->applyFromArray($i < 4 ? $styleVal : $styleValLeft);

            // Right label (F:G)
            $sheet->setCellValue('F' . $r, $lbl2);
            $sheet->mergeCells('F' . $r . ':G' . $r);
            $sheet->getStyle('F' . $r . ':G' . $r)->applyFromArray($styleLbl);

            // Right value (H:J)
            $sheet->setCellValue('H' . $r, $val2);
            $sheet->mergeCells('H' . $r . ':J' . $r);
            $sheet->getStyle('H' . $r . ':J' . $r)->applyFromArray($styleValLeft);

            // Borders
            if ($i === 0) {
                $sheet->getStyle('A' . $r . ':B' . $r)->applyFromArray($bdrFirstTL);
                $sheet->getStyle('C' . $r . ':E' . $r)->applyFromArray($bdrFirstT);
                $sheet->getStyle('F' . $r . ':G' . $r)->applyFromArray($bdrFirstT);
                $sheet->getStyle('H' . $r . ':J' . $r)->applyFromArray($bdrFirstT);
            } else {
                $sheet->getStyle('A' . $r . ':B' . $r)->applyFromArray($bdrRowTL);
                $sheet->getStyle('C' . $r . ':E' . $r)->applyFromArray($bdrRowT);
                $sheet->getStyle('F' . $r . ':G' . $r)->applyFromArray($bdrRowT);
                $sheet->getStyle('H' . $r . ':J' . $r)->applyFromArray($bdrRowT);
            }

            $sheet->getRowDimension($r)->setRowHeight(18);
        }

        // ── Row 10: Total Qty ──
        $totalQty = $order->orderItems->sum('qty') . ' pcs';
        $sheet->setCellValue('A10', 'Total Qty');
        $sheet->mergeCells('A10:B10');
        $sheet->getStyle('A10:B10')->applyFromArray($styleLbl + $bdrLastTLB);

        $sheet->setCellValue('C10', $totalQty);
        $sheet->mergeCells('C10:J10');
        $sheet->getStyle('C10:J10')->applyFromArray($styleValLeft + $bdrLastTB);
        $sheet->getRowDimension(10)->setRowHeight(18);

        // ── Row 11: Keterangan ──
        $sheet->setCellValue('A11', 'Keterangan');
        $sheet->mergeCells('A11:J11');
        $sheet->getStyle('A11:J11')->applyFromArray($styleSub + $bdrThinTopL);
        $sheet->getRowDimension(11)->setRowHeight(18);

        // ── Rows 12-21: Image / notes area ──
        $sheet->mergeCells('A12:J21');
        $sheet->getStyle('A12:J21')->applyFromArray($bdrLeftOnly);
        for ($r = 12; $r <= 21; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(18);
        }

        // ── Row 22: spacer ──
        $sheet->mergeCells('A22:J22');
        $sheet->getStyle('A22:J22')->applyFromArray($bdrLeftOnly);
        $sheet->getRowDimension(22)->setRowHeight(18);

        // ── Row 23: RINCIAN TOTAL UKURAN ──
        $sheet->setCellValue('A23', 'RINCIAN TOTAL UKURAN');
        $sheet->mergeCells('A23:J23');
        $sheet->getStyle('A23:J23')->applyFromArray($styleHdr + $bdrTBMLeft);
        $sheet->getRowDimension(23)->setRowHeight(18);

        // ── Build sizing data ──
        $dewasaSizes = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '6XL'];
        $anakSizes   = ['KIDS_S', 'KIDS_M', 'KIDS_L', 'KIDS_XL', 'KIDS_XXL', 'KIDS_XXXL'];

        $grid = ['dewasa' => ['short' => [], 'long' => []], 'anak' => ['short' => [], 'long' => []]];
        foreach (['dewasa', 'anak'] as $g) {
            $list = ($g === 'dewasa') ? $dewasaSizes : $anakSizes;
            foreach ($list as $sz) { $grid[$g]['short'][$sz] = 0; $grid[$g]['long'][$sz] = 0; }
        }
        foreach ($order->orderItems as $item) {
            $sz = strtoupper(trim($item->size));
            $isLong = str_contains($sz, 'PANJANG');
            if ($isLong) $sz = trim(str_replace(['LENGAN PANJANG', 'PANJANG'], '', $sz));
            $model = $isLong ? 'long' : 'short';
            if (in_array($sz, $dewasaSizes)) {
                $grid['dewasa'][$model][$sz] += $item->qty;
            } elseif (in_array($sz, $anakSizes)) {
                $grid['anak'][$model][$sz] += $item->qty;
            } elseif (in_array('KIDS_' . $sz, $anakSizes)) {
                $grid['anak'][$model]['KIDS_' . $sz] += $item->qty;
            } elseif ($sz === 'XXL') {
                $grid['dewasa'][$model]['2XL'] += $item->qty;
            } elseif ($sz === 'XXXL') {
                $grid['dewasa'][$model]['3XL'] += $item->qty;
            }
        }

        // ── Row 24: DEWASA | ANAK headers ──
        $styleSectionHdr = [
            'font'      => ['bold' => true, 'size' => 11, 'name' => $fontCalibri],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $sectionBg]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM],
        ];
        $styleGridHdr = [
            'font'      => ['bold' => true, 'size' => 11, 'name' => $fontCalibri],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $gridBg]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ];
        $styleSizeLbl = [
            'font'      => ['bold' => true, 'color' => ['rgb' => $navy], 'size' => 10, 'name' => $fontSegoe],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ];
        $styleGridVal = [
            'font'      => ['size' => 10, 'name' => $fontSegoe],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ];

        // Row 24
        $sheet->setCellValue('A24', 'DEWASA');
        $sheet->mergeCells('A24:E24');
        $sheet->getStyle('A24:E24')->applyFromArray($styleSectionHdr + $bdrTBMLeft);
        $sheet->setCellValue('F24', 'ANAK');
        $sheet->mergeCells('F24:J24');
        $sheet->getStyle('F24:J24')->applyFromArray($styleSectionHdr + $bdrTopBottomM);
        $sheet->getRowDimension(24)->setRowHeight(18);

        // Row 25: column headers
        $colHeaders = ['SIZE', 'PENDEK', 'PANJANG', 'KET'];
        $dewasaCols = ['A', 'B', 'C', 'D'];
        $anakCols   = ['F', 'G', 'H', 'I'];

        // Dewasa headers (A-D)
        foreach ($colHeaders as $ci => $ch) {
            $col = $dewasaCols[$ci];
            $sheet->setCellValue($col . '25', $ch);
            if ($ci === 3) { // KET merges D:E
                $sheet->mergeCells('D25:E25');
                $sheet->getStyle('D25:E25')->applyFromArray($styleGridHdr + $bdrTopBottomM);
            } else {
                $sheet->getStyle($col . '25')->applyFromArray($styleGridHdr);
            }
        }
        // Anak headers (F-I)
        foreach ($colHeaders as $ci => $ch) {
            $col = $anakCols[$ci];
            $sheet->setCellValue($col . '25', $ch);
            if ($ci === 3) { // KET merges I:J
                $sheet->mergeCells('I25:J25');
                $sheet->getStyle('I25:J25')->applyFromArray($styleGridHdr);
            } else {
                $sheet->getStyle($col . '25')->applyFromArray($styleGridHdr);
            }
        }
        // Borders for header row
        $sheet->getStyle('A25:B25')->applyFromArray($bdrTBMLeft);
        $sheet->getStyle('C25:E25')->applyFromArray($bdrTopBottomM);
        $sheet->getStyle('F25:G25')->applyFromArray($bdrTopBottomM);
        $sheet->getStyle('H25:J25')->applyFromArray($bdrTopBottomM);
        $sheet->getRowDimension(25)->setRowHeight(18);

        // ── Rows 26-34: Size data ──
        $dewasaRowMap = [
            26 => ['XS', 'XS'],
            27 => ['S', 'S'],
            28 => ['M', 'M'],
            29 => ['L', 'L'],
            30 => ['XL', 'XL'],
            31 => ['2XL', '2XL'],
            32 => ['3XL', '3XL'],
            33 => ['4XL', '4XL'],
            34 => ['6XL', '6XL'],
        ];
        $anakRowMap   = [
            26 => ['S',   'KIDS_S'],
            27 => ['M',   'KIDS_M'],
            28 => ['L',   'KIDS_L'],
            29 => ['XL',  'KIDS_XL'],
            30 => ['2XL', 'KIDS_XXL'],
            31 => ['3XL', 'KIDS_XXXL'],
        ];

        foreach ($dewasaRowMap as $r => [$label, $key]) {
            $isAlt = $r % 2 === 1;
            $bg = $isAlt ? ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $gridBg]]] : [];

            $sheet->setCellValue('A' . $r, $label);
            $sheet->getStyle('A' . $r)->applyFromArray($styleSizeLbl + $bg + $bdrThinTopL);

            $sheet->setCellValue('B' . $r, $grid['dewasa']['short'][$key] ?: '');
            $sheet->getStyle('B' . $r)->applyFromArray($styleGridVal + $bg + $bdrThinTop);

            $sheet->setCellValue('C' . $r, $grid['dewasa']['long'][$key] ?: '');
            $sheet->getStyle('C' . $r)->applyFromArray($styleGridVal + $bg + $bdrThinTop);

            $sheet->mergeCells('D' . $r . ':E' . $r);
            $sheet->getStyle('D' . $r . ':E' . $r)->applyFromArray($styleGridVal + $bg + $bdrThinTop);

            $sheet->getRowDimension($r)->setRowHeight(18);

            if ($r === 34) {
                $sheet->getStyle('A' . $r)->applyFromArray($bdrBottomM);
                $sheet->getStyle('B' . $r . ':E' . $r)->applyFromArray($bdrBottomM);
            }
        }

        foreach ($anakRowMap as $r => [$label, $key]) {
            $isAlt = $r % 2 === 1;
            $bg = $isAlt ? ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $gridBg]]] : [];

            $sheet->setCellValue('F' . $r, $label);
            $sheet->getStyle('F' . $r)->applyFromArray($styleSizeLbl + $bg + $bdrThinTop);

            $sheet->setCellValue('G' . $r, $grid['anak']['short'][$key] ?? '');
            $sheet->getStyle('G' . $r)->applyFromArray($styleGridVal + $bg + $bdrThinTop);

            $sheet->setCellValue('H' . $r, $grid['anak']['long'][$key] ?? '');
            $sheet->getStyle('H' . $r)->applyFromArray($styleGridVal + $bg + $bdrThinTop);

            $sheet->mergeCells('I' . $r . ':J' . $r);
            $sheet->getStyle('I' . $r . ':J' . $r)->applyFromArray($styleGridVal + $bg + $bdrThinTop);
        }
        // Bottom medium on last anak row (31)
        $sheet->getStyle('F31:J31')->applyFromArray($bdrBottomM);

        // ── Rows 35-37: spacer ──
        for ($r = 35; $r <= 37; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(18);
        }

        // ── Rows 38-41: Signatures ──
        $styleSigLbl = [
            'font'      => ['bold' => true, 'color' => ['rgb' => $navy], 'size' => 10, 'name' => $fontSegoe],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM],
        ];
        $styleSigDot = [
            'font'      => ['bold' => true, 'color' => ['rgb' => $navy], 'size' => 10, 'name' => $fontSegoe],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM],
        ];

        $sheet->setCellValue('B38', 'DESAINER');
        $sheet->getStyle('B38')->applyFromArray($styleSigLbl);
        $sheet->setCellValue('E38', 'DIVISI I (PRINT)');
        $sheet->getStyle('E38')->applyFromArray($styleSigLbl);
        $sheet->setCellValue('H38', 'DIVISI II (PRESS)');
        $sheet->getStyle('H38')->applyFromArray($styleSigLbl);
        for ($r = 38; $r <= 40; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(18);
        }
        $sheet->setCellValue('B41', '…..');
        $sheet->getStyle('B41')->applyFromArray($styleSigDot);
        $sheet->setCellValue('E41', '…..');
        $sheet->getStyle('E41')->applyFromArray($styleSigDot);
        $sheet->setCellValue('H41', '…..');
        $sheet->getStyle('H41')->applyFromArray($styleSigDot);
        for ($r = 41; $r <= 44; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(18);
        }

        // ── Set print area & outer border ──
        $sheet->getPageSetup()->setPrintArea('A1:J44');
        $sheet->getStyle('A1:J44')->applyFromArray($bdrOutline);

        // ═══════════════════════════════════════════════════════════════
        // SHEET 2 — "belakang" (Landscape A4)
        // ═══════════════════════════════════════════════════════════════
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('belakang');
        $sheet2->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet2->getPageMargins()->setTop(0.75)->setBottom(0.75)->setLeft(0.25)->setRight(0.25);
        $sheet2->setShowGridLines(true);
        // Thin border grid di semua sel sebagai base
        $sheet2->getStyle('A1:N33')->applyFromArray($bdrThinAll);

        $mockupDepan    = null;
        $mockupBelakang = null;
        $detailDepan    = null;
        $detailBelakang = null;
        $sponsorPaths   = [];
        foreach ($designFiles as $f) {
            $role = $f['role'] ?? null;
            if ($role === 'mockup_depan')       $mockupDepan    = $f['path'];
            elseif ($role === 'mockup_belakang')  $mockupBelakang = $f['path'];
            elseif ($role === 'detail_depan')     $detailDepan    = $f['path'];
            elseif ($role === 'detail_belakang') $detailBelakang = $f['path'];
            elseif ($role === 'sponsor')         $sponsorPaths[] = $f['path'];
        }

        // ── Row 1: RINCIAN header ──
        $styleRincianHdr = [
            'font'      => ['bold' => true, 'size' => 11, 'name' => $fontCalibri],
            'borders'   => ['top' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']],
                            'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']],
                            'left' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ];
        $sheet2->setCellValue('A1', 'RINCIAN');
        $sheet2->mergeCells('A1:N1');
        $sheet2->getStyle('A1:N1')->applyFromArray($styleRincianHdr);
        $sheet2->getRowDimension(1)->setRowHeight(15);

        // ── Rows 2-22: Image area ──
        $sheet2->mergeCells('A2:N22');
        $sheet2->getStyle('A2:N22')->applyFromArray(['borders' => ['left' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]]]);
        $sheet2->getRowDimension(22)->setRowHeight(15);

        // Embed mockup & detail images in the large area
        $bigImgRow = 4;
        if ($mockupDepan) {
            $p = storage_path('app/public/' . $mockupDepan);
            if (file_exists($p)) {
                try {
                    $d = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $d->setPath($p)->setHeight(250)->setCoordinates('B' . $bigImgRow)->setWorksheet($sheet2);
                } catch (\Exception $e) {}
            }
        }
        if ($mockupBelakang) {
            $p = storage_path('app/public/' . $mockupBelakang);
            if (file_exists($p)) {
                try {
                    $d = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $d->setPath($p)->setHeight(250)->setCoordinates('H' . $bigImgRow)->setWorksheet($sheet2);
                } catch (\Exception $e) {}
            }
        }

        // ── Row 23: Separator ──
        $sheet2->mergeCells('A23:N23');
        $sheet2->getStyle('A23:N23')->applyFromArray(['borders' => ['left' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]]]);
        $sheet2->getRowDimension(23)->setRowHeight(15);

        // ── Row 24: Section titles ──
        $styleSecHdr = [
            'font'      => ['bold' => true, 'size' => 11, 'name' => $fontCalibri],
            'borders'   => ['top'    => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']],
                            'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']],
                            'left'   => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM],
        ];
        $sheet2->setCellValue('A24', 'DETAIL DEPAN');
        $sheet2->mergeCells('A24:D24');
        $sheet2->getStyle('A24:D24')->applyFromArray($styleSecHdr);

        $sheet2->setCellValue('E24', 'NAMA & NO PUNGGUNG');
        $sheet2->mergeCells('E24:H24');
        $sheet2->getStyle('E24:H24')->applyFromArray($styleSecHdr);

        $sheet2->setCellValue('I24', 'DETAIL SPONSOR');
        $sheet2->mergeCells('I24:N24');
        $sheet2->getStyle('I24:N24')->applyFromArray($styleSecHdr);
        $sheet2->getRowDimension(24)->setRowHeight(15);

        // ── Rows 25-33: Image boxes ──
        $styleImgBox = [
            'borders' => ['left' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]],
        ];

        $sheet2->mergeCells('A25:D33');
        $sheet2->getStyle('A25:D33')->applyFromArray($styleImgBox);

        $sheet2->mergeCells('E25:H33');
        $sheet2->getStyle('E25:H33')->applyFromArray($styleImgBox);

        $sheet2->mergeCells('I25:N33');
        $sheet2->getStyle('I25:N33')->applyFromArray($styleImgBox);

        for ($r = 25; $r <= 33; $r++) {
            $sheet2->getRowDimension($r)->setRowHeight(15);
        }

        // Detail depan image in A25:D33
        if ($detailDepan) {
            $p = storage_path('app/public/' . $detailDepan);
            if (file_exists($p)) {
                try {
                    $d = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $d->setPath($p)->setHeight(80)->setCoordinates('B' . 26)->setWorksheet($sheet2);
                } catch (\Exception $e) {}
            }
        }

        // Detail belakang image in E25:H33
        if ($detailBelakang) {
            $p = storage_path('app/public/' . $detailBelakang);
            if (file_exists($p)) {
                try {
                    $d = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $d->setPath($p)->setHeight(80)->setCoordinates('F' . 26)->setWorksheet($sheet2);
                } catch (\Exception $e) {}
            }
        }

        // Sponsor images in I25:N33
        $sponsorImgsPerRow = 2;
        for ($idx = 0; $idx < min(count($sponsorPaths), 4); $idx++) {
            $p = storage_path('app/public/' . $sponsorPaths[$idx]);
            if (!file_exists($p)) continue;
            $col = ($idx % 2 === 0) ? 'J' : 'L';
            $row = 26 + intdiv($idx, 2) * 3;
            try {
                $d = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $d->setPath($p)->setHeight(60)->setCoordinates($col . $row)->setWorksheet($sheet2);
            } catch (\Exception $e) {}
        }

        // ── Outer border for sheet 2 ──
        $sheet2->getStyle('A1:N33')->applyFromArray($bdrOutline);

        // ═══════════════════════════════════════════════════════════════
        // ── WRITE FILE ──
        // ═══════════════════════════════════════════════════════════════
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = $order->order_number . '-spk.xlsx';

        $tempFile = tempnam(sys_get_temp_dir(), 'export');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function uploadSpkFile(Request $request, Order $order)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,zip,rar,ai,eps,psd|max:20480',
            'role' => 'required|string|in:mockup_depan,mockup_belakang,detail_depan,detail_belakang,sponsor,pola',
        ]);

        $file = $request->file('file');
        $role = $request->input('role');

        $path = $file->store('design-files/' . $order->order_number, 'public');

        if (!$order->designRequest) {
            return response()->json(['success' => false, 'message' => 'Design request tidak ditemukan.'], 404);
        }

        $designFiles = $order->designRequest->design_files ?? [];

        // Single-use roles: remove any existing file with that role
        if (in_array($role, ['mockup_depan', 'mockup_belakang', 'detail_depan', 'detail_belakang'])) {
            $designFiles = array_values(array_filter($designFiles, fn($f) => ($f['role'] ?? null) !== $role));
        }

        $designFiles[] = [
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize(),
            'type' => $file->getMimeType(),
            'role' => $role,
        ];

        $order->designRequest->update([
            'design_files' => $designFiles
        ]);

        return response()->json([
            'success' => true,
            'message' => 'File berhasil diunggah ke slot SPK.',
            'file' => [
                'name' => $file->getClientOriginalName(),
                'url' => asset('storage/' . $path),
                'path' => $path,
                'role' => $role,
            ]
        ]);
    }

    public function deleteSpkFile(Request $request, Order $order)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $path = $request->input('path');

        if (!$order->designRequest) {
            return response()->json(['success' => false, 'message' => 'Design request tidak ditemukan.'], 404);
        }

        $designFiles = $order->designRequest->design_files ?? [];
        $found = false;

        $newDesignFiles = [];
        foreach ($designFiles as $f) {
            if (($f['path'] ?? null) === $path) {
                $found = true;
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                }
            } else {
                $newDesignFiles[] = $f;
            }
        }

        if ($found) {
            $order->designRequest->update([
                'design_files' => $newDesignFiles
            ]);
            return response()->json(['success' => true, 'message' => 'File berhasil dihapus dari SPK.']);
        }

        return response()->json(['success' => false, 'message' => 'File tidak ditemukan.'], 404);
    }

    public function updateSpkNotes(Request $request, Order $order)
    {
        $request->validate([
            'notes' => 'nullable|string|max:5000',
        ]);

        if (!$order->designRequest) {
            return response()->json(['success' => false, 'message' => 'Design request tidak ditemukan.'], 404);
        }

        $order->designRequest->update([
            'additional_notes' => $request->input('notes')
        ]);

        return response()->json(['success' => true, 'message' => 'Catatan SPK berhasil diperbarui.']);
    }
}
