<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use App\Models\Order;
use App\Models\CafeTable;

class OrderDetail extends Component
{
    public $order;

    public function mount(Order $order)
    {
        $this->order = $order->load('items.product', 'user', 'table');
    }

    public function printReceipt()
    {
        $this->dispatch('printReceipt');
    }

    public function markTableAvailable()
    {
        if ($this->order && $this->order->table_id) {
            CafeTable::whereKey($this->order->table_id)->update(['status' => 'available']);
            // Refresh relation
            $this->order->load('table');
            session()->flash('message', 'Meja telah ditandai Tersedia.');
        }
    }

    public function render()
    {
        return view('livewire.pos.detail');
    }
}
