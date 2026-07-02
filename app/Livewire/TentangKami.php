<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Role;

class TentangKami extends Component
{
    public $tim = [];

    public function mount()
    {
        $this->tim = User::with('role')
            ->whereHas('role', fn($q) => $q->whereIn('name', Role::internalNames()))
            ->orderBy('created_at')->get()->toArray();
    }

    public function render()
    {
        return view('livewire.tentang-kami');
    }
}
