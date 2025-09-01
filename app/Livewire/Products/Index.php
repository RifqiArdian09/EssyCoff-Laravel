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
    public $isDarkMode = false;
    public $filter = ''; // Tambah filter property
    public $showAlerts = false; // Tambah property untuk show alerts

    protected $updatesQueryString = ['search', 'filter', 'showAlerts'];

    public function mount()
    {
        // Ambil filter dari query parameter
        $this->filter = request('filter', '');
        $this->showAlerts = request('show_alerts') === 'true';
    }

    // Reset halaman saat search atau filter berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    // Clear filter
    public function clearFilter()
    {
        $this->filter = '';
        $this->resetPage();
    }

    // Toggle theme
    public function toggleTheme()
    {
        $this->isDarkMode = !$this->isDarkMode;
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
        $lowStockThreshold = 10; // Batas stock sedikit

        $products = Product::with('category')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhereHas('category', function ($cat) {
                          $cat->where('name', 'like', "%{$this->search}%");
                      });
                });
            })
            ->when($this->filter === 'out_of_stock', function ($query) {
                $query->where('stock', 0);
            })
            ->when($this->filter === 'low_stock', function ($query) use ($lowStockThreshold) {
                $query->where('stock', '>', 0)
                      ->where('stock', '<=', $lowStockThreshold);
            })
            ->latest()
            ->paginate($this->perPage);

        // Hitung stock alerts untuk info
        $stockAlerts = [
            'out_of_stock' => Product::where('stock', 0)->count(),
            'low_stock' => Product::where('stock', '>', 0)
                ->where('stock', '<=', $lowStockThreshold)
                ->count(),
        ];

        return view('livewire.products.index', [
            'products' => $products,
            'stockAlerts' => $stockAlerts,
        ]);
    }
}