<?php

namespace App\Livewire\Pos\Receipt;

use Livewire\Component;
use App\Models\Order;

class Index extends Component
{
    public $order;

    public function mount(Order $order)
    {
        $this->order = $order->load('items.product');
    }

    public function printReceipt()
    {
        // Trigger print dari browser (ditangani lewat JS listener di Blade)
        $this->dispatch('printReceipt');
    }

    public function backToPOS()
    {
        return redirect()->route('pos.cashier');
    }

    public function render()
    {
        return view('livewire.pos.receipt.index');
    }
}
