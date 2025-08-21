<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    protected $updatesQueryString = ['search'];

    // Reset halaman saat search berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Delete product
    public function delete($id)
    {
        $product = Product::findOrFail($id);
        
        if ($product->image && \Storage::disk('public')->exists($product->image)) {
            \Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();
        session()->flash('message', 'Product deleted successfully.');
    }

    public function render()
    {
        $products = Product::with('category')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhereHas('category', function ($cat) {
                          $cat->where('name', 'like', "%{$this->search}%");
                      });
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.products.index', [
            'products' => $products,
        ]);
    }
}
