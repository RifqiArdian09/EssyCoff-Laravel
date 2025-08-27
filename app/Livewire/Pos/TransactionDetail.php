<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use App\Models\Order;

class TransactionDetail extends Component
{
    public $order;

    public function mount(Order $order)
    {
        $this->order = $order->load('items.product');
    }

    public function printReceipt()
{
    // Tetap di halaman detail; trigger print dari browser/JS jika diperlukan
    $this->dispatch('printReceipt');
    return null;
}


    public function backToPOS()
    {
        return redirect()->route('pos.cashier');
    }

    public function render()
    {
        return view('livewire.pos.transaction-detail');
    }
}
