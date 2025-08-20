<?php

namespace App\Livewire\Categories;

use Livewire\Component;
use App\Models\Category;

class Index extends Component
{
    public function delete($id)
    {
        Category::findOrFail($id)->delete();
        session()->flash('message', 'Category deleted successfully.');
    }

    public function render()
    {
        return view('livewire.categories.index', [
            'categories' => Category::latest()->get(), // ambil semua categories
        ]);
    }
}
