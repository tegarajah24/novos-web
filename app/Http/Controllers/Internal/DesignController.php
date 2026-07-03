<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDesignStatusRequest;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DesignController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'designRequest', 'statusHistories' => function ($q) {
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
                    'id'                => $order->id,
                    'order_id'          => $order->order_number,
                    'customer'          => $order->user->name ?? '-',
                    'customer_contact'  => $order->user->phone ?? '-',
                    'team_name'         => $dr?->team_name ?? 'Jersey Custom',
                    'deadline'          => $order->created_at->addDays(7)->format('d M Y'),
                    'priority'          => $priority,
                    'revision_note'     => $revision?->notes
                        ? str_replace('Revisi: ', '', $revision->notes)
                        : null,
                    'material'          => $dr?->material ?? '-',
                    'collar'            => $dr?->collar_style ?? '-',
                    'pattern'           => $dr?->motif ?? '-',
                    'notes'             => nl2br(e($dr?->additional_notes ?? $order->notes ?? 'Tidak ada catatan')),
                    'reference_files'   => array_merge(
                        $dr?->logo ? [asset('storage/' . $dr->logo)] : [],
                        collect($dr?->design_files ?? [])->map(fn($f) => asset('storage/' . $f['path']))->values()->toArray(),
                    ),
                ];
            })
            ->values()
            ->toArray();

        return view('internal.design', compact('orders'));
    }

    public function updateStatus(UpdateDesignStatusRequest $request, Order $order)
    {
        $data = $request->validated();

        $user = auth()->user();

        if (!in_array($user->role->name, ['Design', 'Super Admin', 'Manager'])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk memperbarui status desain.',
            ], 403);
        }

        // Simpan file upload
        $uploadedFiles = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('design-files/' . $order->order_number, 'public');
                $uploadedFiles[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
        }

        DB::transaction(function () use ($order, $data, $user, $uploadedFiles) {
            $order->update(['status' => $data['status']]);

            if (!empty($uploadedFiles) && $order->designRequest) {
                $existingFiles = $order->designRequest->design_files ?? [];
                $order->designRequest->update([
                    'design_files' => array_merge($existingFiles, $uploadedFiles),
                ]);
            }

            $notes = 'Design selesai dikerjakan';
            if (!empty($uploadedFiles)) {
                $notes .= '. File: ' . implode(', ', array_column($uploadedFiles, 'name'));
            }

            $order->statusHistories()->create([
                'status'     => $data['status'],
                'changed_by' => $user->id,
                'notes'      => $notes,
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
            [
                'order_number' => $order->order_number,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Desain berhasil diselesaikan dan diteruskan ke produksi.',
        ]);
    }
}
