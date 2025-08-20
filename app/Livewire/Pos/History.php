<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;

class History extends Component
{
    use WithPagination;

    public $search = ''; // jika mau fitur search

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $orders = Order::with('items.product', 'user')
            ->when($this->search, function($query) {
                $query->where('no_order', 'like', '%'.$this->search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livewire.pos.history', [
            'orders' => $orders
        ]);
    }
}
