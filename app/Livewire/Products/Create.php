<?php
namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;

class Create extends Component
{
    use WithFileUploads;
    
    public $name, $category_id, $price, $stock, $image;
    public $categories;
    
    public function mount()
    {
        $this->categories = Category::all();
    }
    
    public function updatedImage()
    {
        $this->validate([
            'image' => 'nullable|image|max:2048',
        ]);
    }
    
    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);
        
        $imagePath = $this->image ? $this->image->store('products', 'public') : null;
        
        Product::create([
            'name' => $this->name,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'stock' => $this->stock,
            'image' => $imagePath,
        ]);
        
        session()->flash('message', 'Product created successfully.');
        return redirect()->route('products.index');
    }
    
    public function render()
    {
        return view('livewire.products.create');
    }
}