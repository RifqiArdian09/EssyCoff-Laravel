<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use App\Models\Order;

class ReceiptPrint extends Component
{
    public $order;

    public function mount(Order $order)
    {
        $this->order = $order->load('items.product');
    }

    public function render()
    {
        // langsung pakai default layout (tanpa layouts.blank)
        return view('livewire.pos.receipt-print');
    }
}
