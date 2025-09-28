<?php

namespace App\Livewire\Categories;

use Livewire\Component;
use App\Models\Category;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $confirmingCategoryDeletion = false;
    public $categoryIdToDelete = null;

    public function confirmDelete($categoryId)
    {
        $this->confirmingCategoryDeletion = true;
        $this->categoryIdToDelete = $categoryId;
    }

    public function delete($id = null)
    {
        $id = $id ?? $this->categoryIdToDelete;
        Category::findOrFail($id)->delete();
        session()->flash('message', 'Kategori berhasil dihapus.');
        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => 'Kategori berhasil dihapus.',
            'timeout' => 3000,
        ]);
        
        // Close the modal after successful deletion
        $this->confirmingCategoryDeletion = false;
        $this->categoryIdToDelete = null;
    }

    public function render()
    {
        return view('livewire.categories.index', [
            'categories' => Category::latest()->paginate(10), // Add pagination
        ]);
    }
}
