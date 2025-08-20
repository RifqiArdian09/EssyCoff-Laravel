<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;

class Edit extends Component
{
    public $category;
    public $name;

    protected $rules = [
        'name' => 'required|string|max:255|unique:categories,name',
    ];

    public function mount(Category $category)
    {
        $this->category = $category;
        $this->name = $category->name;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $this->category->id,
        ]);

        $this->category->update([
            'name' => $this->name,
        ]);

        session()->flash('success', 'Kategori berhasil diperbarui.');
        return redirect()->route('categories.index');
    }

    public function render()
    {
        return view('livewire.categories.edit');
    }
}
