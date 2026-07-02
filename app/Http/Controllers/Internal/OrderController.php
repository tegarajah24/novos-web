<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
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
    public function index()
    {
        return view('internal.daftar-pesanan');
    }

    public function show(Order $order)
    {
        return view('internal.detail-pesanan', ['order' => $order]);
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

    public function assign(AssignOrderRequest $request, Order $order)
    {
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

        $uiStatusMap = [
            'menunggu_validasi' => 'menunggu_verifikasi',
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
}
