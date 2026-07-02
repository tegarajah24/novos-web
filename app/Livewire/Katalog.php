<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;
use Livewire\Component;

class Katalog extends Component
{
    public string $search = '';
    public array $selectedCats = [];
    public int $currentPage = 1;
    public int $perPage = 12;

    public function mount()
    {
        $slug = request('kategori');
        if ($slug) {
            $found = collect($this->getCategoriesProperty())->firstWhere('slug', $slug);
            if ($found) {
                $this->selectedCats = [$found['name']];
            }
        }
    }

    public function updatingSearch()
    {
        $this->currentPage = 1;
    }

    public function getProductsProperty(): array
    {
        return Product::with('category')
            ->where('is_active', true)
            ->latest()
            ->get()
            ->map(fn($p) => [
                'id'             => $p->id,
                'name'           => $p->name,
                'category'       => $p->category?->name ?? 'Katalog',
                'price'          => $p->price ? (int) $p->price : null,
                'badge'          => null,
                'image'          => $p->image ? asset('storage/' . $p->image) : null,
                'kerah'          => $p->kerah,
                'bahan'          => $p->bahan,
                'jenis_potongan' => $p->jenis_potongan,
                'lengan_jahitan' => $p->lengan_jahitan,
            ])
            ->toArray();
    }

    public function getCategoriesProperty(): array
    {
        return Category::orderBy('name')->get()->map(fn($c) => [
            'slug' => Str::slug($c->name),
            'name' => $c->name,
        ])->toArray();
    }

    public function getFilteredProductsProperty(): \Illuminate\Support\Collection
    {
        return collect($this->products)
            ->when($this->selectedCats, fn($col) => $col->whereIn('category', $this->selectedCats))
            ->when($this->search, fn($col) => $col->filter(fn($p) => str_contains(strtolower($p['name']), strtolower($this->search))))
            ->values();
    }

    public function getPagedProductsProperty(): \Illuminate\Support\Collection
    {
        return $this->filteredProducts->forPage($this->currentPage, $this->perPage);
    }

    public function getTotalPagesProperty(): int
    {
        return max(1, (int) ceil($this->filteredProducts->count() / $this->perPage));
    }

    public function getPageNumbersProperty(): array
    {
        return range(1, $this->totalPages);
    }

    public function goPage($page): void
    {
        $page = (int) $page;
        if ($page >= 1 && $page <= $this->totalPages) {
            $this->currentPage = $page;
        }
    }

    public function toggleCategory($name): void
    {
        if (in_array($name, $this->selectedCats)) {
            $this->selectedCats = array_values(array_filter($this->selectedCats, fn($c) => $c !== $name));
        } else {
            $this->selectedCats[] = $name;
        }
        $this->currentPage = 1;
    }

    public function resetFilter(): void
    {
        $this->search = '';
        $this->selectedCats = [];
        $this->currentPage = 1;
    }

    public function addToCart($productId): void
    {
        if (!auth()->check()) {
            return;
        }

        Cart::create([
            'user_id'    => auth()->id(),
            'product_id' => $productId,
            'size'       => 'M',
            'qty'        => 1,
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
