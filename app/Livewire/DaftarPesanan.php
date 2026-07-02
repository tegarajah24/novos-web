<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use App\Models\User;

class DaftarPesanan extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterDateFrom = '';
    public string $filterDateTo = '';

    protected $queryString = ['search', 'filterStatus', 'filterDateFrom', 'filterDateTo'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterDateFrom()
    {
        $this->resetPage();
    }

    public function updatingFilterDateTo()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset('search', 'filterStatus', 'filterDateFrom', 'filterDateTo');
        $this->resetPage();
    }

    public function assignOrder($orderId, $assigneeId)
    {
        $order = Order::where('order_number', $orderId)->first();
        if (!$order) {
            $this->dispatch('notify', type: 'error', message: 'Pesanan tidak ditemukan.');
            return;
        }

        $order->update(['assignee_id' => $assigneeId ?: null]);

        $this->dispatch('notify', type: 'success', message: 'Assignee berhasil diperbarui.');
    }

    public function getOrdersProperty()
    {
        $dbStatuses = ['menunggu_validasi', 'menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai', 'dibatalkan'];

        $statusMap = [
            'menunggu_verifikasi' => ['menunggu_validasi'],
            'menunggu_pembayaran' => ['menunggu_pembayaran'],
            'menunggu_acc' => ['dikonfirmasi'],
            'tahap_desain' => ['disetujui', 'di_design'],
            'tahap_produksi' => ['siap_cetak', 'diproduksi'],
            'selesai' => ['selesai'],
            'dibatalkan' => ['dibatalkan'],
        ];

        $query = Order::with(['user', 'orderItems', 'designRequest', 'assignee'])
            ->whereIn('status', $dbStatuses);

        if ($this->filterStatus && isset($statusMap[$this->filterStatus])) {
            $query->whereIn('status', $statusMap[$this->filterStatus]);
        }

        if ($this->filterDateFrom) {
            $query->whereDate('created_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->whereDate('created_at', '<=', $this->filterDateTo);
        }

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('designRequest', fn($d) => $d->where('team_name', 'like', "%{$search}%"));
            });
        }

        return $query->latest()->paginate(15);
    }

    public function getAssigneesProperty()
    {
        $colorKeys = ['purple', 'blue', 'orange', 'green', 'gray'];
        return User::with('role')
            ->whereHas('role', fn($q) => $q->where('name', 'Admin'))
            ->get()
            ->map(function ($user) use ($colorKeys) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'color' => $colorKeys[array_rand($colorKeys)],
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.daftar-pesanan');
    }
}
