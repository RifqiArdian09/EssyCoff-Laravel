<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;

class Create extends Component
{
    public $name;

    protected $rules = [
        'name' => 'required|string|max:255|unique:categories,name',
    ];

    public function save()
    {
        $this->validate();

        Category::create([
            'name' => $this->name,
        ]);

        session()->flash('success', 'Kategori berhasil ditambahkan.');
        return redirect()->route('categories.index');
    }

    public function render()
    {
        return view('livewire.categories.create');
    }
}
