<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;

class History extends Component
{
    use WithPagination;

    public int $perPage = 10;

    protected $paginationTheme = 'tailwind';

    public function render()
    {
        $orders = Order::with('user')
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.pos.history', [
            'orders' => $orders,
        ]);
    }
}


