<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class Edit extends Component
{
    use WithFileUploads;

    public $product, $name, $category_id, $price, $stock, $image, $currentImage;
    public $categories;

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->categories = Category::all();

        $this->name = $product->name;
        $this->category_id = $product->category_id;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->currentImage = $product->image;
    }

    public function updatedImage()
    {
        $this->validate([
            'image' => 'nullable|image|max:2048',
        ]);
    }

    public function removeImage()
    {
        $this->image = null;
        $this->currentImage = null;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        // Handle image upload logic
        $imagePath = $this->currentImage;
        
        if ($this->image && method_exists($this->image, 'store')) {
            // New image uploaded, delete old one if exists
            if ($this->product->image && Storage::disk('public')->exists($this->product->image)) {
                Storage::disk('public')->delete($this->product->image);
            }
            $imagePath = $this->image->store('products', 'public');
        } elseif ($this->currentImage === null && $this->product->image) {
            // Image was removed, delete the old one
            if (Storage::disk('public')->exists($this->product->image)) {
                Storage::disk('public')->delete($this->product->image);
            }
            $imagePath = null;
        }

        // Update the product
        $this->product->update([
            'name' => $this->name,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'stock' => $this->stock,
            'image' => $imagePath,
        ]);

        session()->flash('message', 'Product updated successfully.');
        return redirect()->route('products.index');
    }

    public function render()
    {
        return view('livewire.products.edit');
    }
}