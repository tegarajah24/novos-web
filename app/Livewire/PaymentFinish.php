<?php

namespace App\Livewire;

use Livewire\Component;

class PaymentFinish extends Component
{
    public $orderNumber;

    public function mount($orderNumber = null)
    {
        $this->orderNumber = $orderNumber;
    }

    public function render()
    {
        return view('livewire.payment-finish');
    }
}
