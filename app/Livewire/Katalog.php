<?php

namespace App\Livewire;

use App\Models\Cart;
use Livewire\Component;

class Katalog extends Component
{
    public $products = [];
    public $categories = [];
    public $search = '';
    public $selectedCats = [];
    public int $currentPage = 1;
    public int $perPage = 12;

    public function mount($products, $categories)
    {
        $this->products = $products;
        $this->categories = $categories;

        $slug = request('kategori');
        if ($slug) {
            $found = collect($categories)->firstWhere('slug', $slug);
            if ($found) {
                $this->selectedCats = [$found['name']];
            }
        }
    }

    public function updatingSearch()
    {
        $this->currentPage = 1;
    }

    public function getFilteredProductsProperty()
    {
        return collect($this->products)
            ->when($this->selectedCats, fn($col) => $col->whereIn('category', $this->selectedCats))
            ->when($this->search, fn($col) => $col->filter(fn($p) => str_contains(strtolower($p['name']), strtolower($this->search))))
            ->values();
    }

    public function getPagedProductsProperty()
    {
        return $this->filteredProducts->forPage($this->currentPage, $this->perPage);
    }

    public function getTotalPagesProperty()
    {
        return max(1, (int) ceil($this->filteredProducts->count() / $this->perPage));
    }

    public function getPageNumbersProperty()
    {
        return range(1, $this->totalPages);
    }

    public function goPage($page)
    {
        $page = (int) $page;
        if ($page >= 1 && $page <= $this->totalPages) {
            $this->currentPage = $page;
        }
    }

    public function toggleCategory($name)
    {
        if (in_array($name, $this->selectedCats)) {
            $this->selectedCats = array_values(array_filter($this->selectedCats, fn($c) => $c !== $name));
        } else {
            $this->selectedCats[] = $name;
        }
        $this->currentPage = 1;
    }

    public function resetFilter()
    {
        $this->search = '';
        $this->selectedCats = [];
        $this->currentPage = 1;
    }

    public function addToCart($productId)
    {
        if (!auth()->check()) {
            return;
        }

        Cart::create([
            'user_id' => auth()->id(),
            'product_id' => $productId,
            'size' => 'M',
            'qty' => 1,
        ]);

        $this->dispatch('cart-updated');
    }

    public function formatRupiah($val): string
    {
        return 'Rp ' . number_format((int) $val, 0, ',', '.');
    }

    public function render()
    {
        return view('livewire.katalog');
    }
}
