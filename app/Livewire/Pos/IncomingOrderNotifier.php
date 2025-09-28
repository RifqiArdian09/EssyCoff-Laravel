<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use App\Models\Order;
use Carbon\Carbon;

class IncomingOrderNotifier extends Component
{
    public ?string $lastCheckedAt = null;

    public function mount(): void
    {
        // Start checking from now to avoid notifying old orders
        $this->lastCheckedAt = now()->toDateTimeString();
    }

    public function check(): void
    {
        $since = $this->lastCheckedAt ? Carbon::parse($this->lastCheckedAt) : now()->subMinute();

        $newOrders = Order::with('table')
            ->where('status', 'pending_payment')
            ->where('created_at', '>', $since)
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        // Update checkpoint first to avoid duplicate toasts on slow clients
        $this->lastCheckedAt = now()->toDateTimeString();

        foreach ($newOrders as $order) {
            $tableLabel = $order->table?->name
                ? ($order->table->name . ($order->table->code ? ' ('.$order->table->code.')' : ''))
                : 'Tanpa Meja';
            $customer = $order->customer_name ?: 'Customer';
            $message = "Order {$order->no_order} dari {$customer} • {$tableLabel}";

            // Dispatch Alpine global toast
            $href = route('pos.detail', ['order' => $order->getKey()]);
            $payload = [
                'title' => 'Pesanan Masuk',
                'message' => $message,
                'type' => 'warning',
                'timeout' => 6000,
                'href' => $href,
                'playSound' => true,
            ];
            $json = json_encode($payload);
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: $json }))");
        }

        // Always broadcast current pending count so UI badges can update in real-time
        $pendingCount = Order::where('status', 'pending_payment')->count();
        $this->js("window.dispatchEvent(new CustomEvent('pending-count', { detail: { count: $pendingCount } }))");
    }

    public function render()
    {
        return view('livewire.pos.incoming-order-notifier');
    }
}
