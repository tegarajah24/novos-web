<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Order;

class Beranda extends Component
{
    public $bestSellers = [];
    public $latestProducts = [];
    public $totalOrders = 0;
    public $totalProducts = 0;

    public function mount()
    {
        $this->bestSellers = Product::where('is_active', true)
            ->with('category')->inRandomOrder()->take(8)->get();
        $this->latestProducts = Product::where('is_active', true)
            ->with('category')->latest()->take(8)->get();
        $this->totalOrders = Order::where('status', 'selesai')->count();
        $this->totalProducts = Product::where('is_active', true)->count();
    }

    public function render()
    {
        return view('livewire.beranda');
    }
}
