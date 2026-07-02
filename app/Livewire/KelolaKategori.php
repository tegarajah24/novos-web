<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;

class KelolaKategori extends Component
{
    public $categories = [];
    public $name = '';
    public $editId = null;
    public $modalOpen = false;
    public $submitting = false;

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = Category::withCount('products')
            ->orderBy('name')
            ->get()
            ->map(fn($cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
                'products_count' => $cat->products_count,
            ])
            ->toArray();
    }

    public function openModal($id = null)
    {
        if ($id) {
            $cat = Category::findOrFail($id);
            $this->editId = $cat->id;
            $this->name = $cat->name;
        } else {
            $this->editId = null;
            $this->name = '';
        }
        $this->modalOpen = true;
    }

    public function simpan()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        $this->submitting = true;

        if ($this->editId) {
            $category = Category::findOrFail($this->editId);
            $category->update(['name' => $this->name]);
            $this->dispatch('notify', type: 'success', message: 'Kategori berhasil diperbarui');
        } else {
            Category::create(['name' => $this->name]);
            $this->dispatch('notify', type: 'success', message: 'Kategori berhasil ditambahkan');
        }

        $this->modalOpen = false;
        $this->name = '';
        $this->editId = null;
        $this->submitting = false;
        $this->loadCategories();
    }

    public function hapus($id)
    {
        $category = Category::findOrFail($id);

        if ($category->products()->exists()) {
            $this->dispatch('notify', type: 'error', message: 'Kategori tidak bisa dihapus karena masih memiliki produk terkait.');
            return;
        }

        $category->delete();
        $this->dispatch('notify', type: 'success', message: 'Kategori berhasil dihapus');
        $this->loadCategories();
    }

    public function render()
    {
        return view('livewire.kelola-kategori');
    }
}
