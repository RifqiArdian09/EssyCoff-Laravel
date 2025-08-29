<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use App\Models\Order;

class OrderDetail extends Component
{
    public $order;

    public function mount(Order $order)
    {
        $this->order = $order->load('items.product', 'user');
    }

    public function printReceipt()
    {
        $this->dispatch('printReceipt');
    }

    public function render()
    {
        return view('livewire.pos.detail');
    }
}
