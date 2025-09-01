<?php

namespace App\Livewire\Components;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\On;

class Notification extends Component
{
    public $orders;
    public $pendingCount = 0;

    public function mount()
    {
        $this->loadNotifications();
    }

    #[On('echo:orders,OrderCreated')]
    public function onOrderCreated($data)
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->orders = Order::with('items.product')
            ->where('status', 'pending_payment')
            ->latest()
            ->take(5)
            ->get();

        $this->pendingCount = Order::where('status', 'pending_payment')->count();
    }

    public function render()
    {
        return view('livewire.components.notification');
    }
}
