<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;

class Order extends Component
{
    public string $search = '';
    public string $category = 'all';

    public function mount()
    {
        $this->search = request()->query('search', '');
        $this->category = request()->query('category', 'all');
    }

    public function render()
    {
        // Build favorite data from orders
        $orders = \App\Models\Order::with(['items.product'])->get();
        $favoriteData = [];
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $pid = $item->product_id;
                if (!isset($favoriteData[$pid])) {
                    $favoriteData[$pid] = ['total_ordered' => 0, 'order_count' => 0];
                }
                $favoriteData[$pid]['total_ordered'] += $item->qty;
                $favoriteData[$pid]['order_count']++;
            }
        }

        $products = Product::with('category')
            ->when($this->search, function ($query) {
                $search = $this->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhereHas('category', function ($cat) use ($search) {
                          $cat->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($this->category && $this->category !== 'all' && $this->category !== 'favorite', function ($query) {
                $category = $this->category;
                $query->whereHas('category', function ($cat) use ($category) {
                    $cat->where('name', 'like', "%{$category}%");
                });
            })
            ->latest()
            ->get();

        foreach ($products as $product) {
            $product->favorite_data = $favoriteData[$product->id] ?? [
                'total_ordered' => 0,
                'order_count' => 0,
            ];
        }

        if ($this->category === 'favorite') {
            $products = $products->filter(function ($product) {
                return $product->favorite_data['total_ordered'] > 0;
            })->sortByDesc(function ($product) {
                return $product->favorite_data['total_ordered'];
            })->values();
        }

        $categories = Category::all();

        return view('livewire.customer.order', [
            'products' => $products,
            'categories' => $categories,
            'search' => $this->search,
            'category' => $this->category,
        ])
        ->layout('components.layouts.app.customer', [
            'title' => 'EssyCoff - Order',
        ]);
    }
}
