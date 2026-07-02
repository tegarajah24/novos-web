<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class KelolaProduk extends Component
{
    use WithFileUploads;

    public $search = '';
    public $categoryFilter = '';

    public $showModal = false;
    public $editingProductId = null;

    public $name = '';
    public $category_id = '';
    public $price = '';
    public $description = '';
    public $image;
    public $existingImage = null;
    public $kerah = '';
    public $bahan = '';
    public $jenis_potongan = '';
    public $lengan_jahitan = '';

    public $submitting = false;

    protected function rules()
    {
        return [
            'name' => 'required|min:3',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'kerah' => 'nullable|string',
            'bahan' => 'nullable|string',
            'jenis_potongan' => 'nullable|string',
            'lengan_jahitan' => 'nullable|string',
        ];
    }

    protected function getListeners()
    {
        return ['notify'];
    }

    public function getCategoriesProperty()
    {
        return Category::select('id', 'name')->orderBy('name')->get()->toArray();
    }

    public function getProductsProperty()
    {
        return Product::with('category')
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->categoryFilter, fn($q) => $q->where('category_id', $this->categoryFilter))
            ->latest()
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'category_id' => $p->category_id,
                    'price' => (int) $p->price,
                    'description' => $p->description ?? '',
                    'image_depan' => $p->image ? asset('storage/' . $p->image) : null,
                    'image_belakang' => null,
                    'kerah' => $p->kerah,
                    'bahan' => $p->bahan,
                    'jenis_potongan' => $p->jenis_potongan,
                    'lengan_jahitan' => $p->lengan_jahitan,
                    'category_name' => $p->category?->name ?? '-',
                ];
            })
            ->values()
            ->toArray();
    }

    public function openCreate()
    {
        $this->editingProductId = null;
        $this->name = '';
        $this->category_id = '';
        $this->price = '';
        $this->description = '';
        $this->image = null;
        $this->existingImage = null;
        $this->kerah = '';
        $this->bahan = '';
        $this->jenis_potongan = '';
        $this->lengan_jahitan = '';
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $product = Product::findOrFail($id);
        $this->editingProductId = $product->id;
        $this->name = $product->name;
        $this->category_id = $product->category_id;
        $this->price = (int) $product->price;
        $this->description = $product->description ?? '';
        $this->image = null;
        $this->existingImage = $product->image ? asset('storage/' . $product->image) : null;
        $this->kerah = $product->kerah ?? '';
        $this->bahan = $product->bahan ?? '';
        $this->jenis_potongan = $product->jenis_potongan ?? '';
        $this->lengan_jahitan = $product->lengan_jahitan ?? '';
        $this->showModal = true;
    }

    public function closeForm()
    {
        $this->showModal = false;
        $this->editingProductId = null;
    }

    public function save()
    {
        $this->validate();

        $this->submitting = true;

        $data = [
            'name' => $this->name,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'description' => $this->description,
            'kerah' => $this->kerah,
            'bahan' => $this->bahan,
            'jenis_potongan' => $this->jenis_potongan,
            'lengan_jahitan' => $this->lengan_jahitan,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('products', 'public');
        }

        if ($this->editingProductId) {
            $product = Product::findOrFail($this->editingProductId);
            if ($this->image && $product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->update($data);
            $message = 'Produk berhasil diperbarui';
        } else {
            Product::create($data);
            $message = 'Produk berhasil ditambahkan';
        }

        $this->submitting = false;
        $this->closeForm();

        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        $this->dispatch('notify', type: 'success', message: 'Produk berhasil dihapus');
    }

    public function render()
    {
        return view('livewire.kelola-produk');
    }
}
