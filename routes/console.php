<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Order;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automatically delete pending payments older than 1 hour (and restore stock)
Schedule::call(function () {
    // Process in chunks to avoid memory issues
    Order::where('status', 'pending_payment')
        ->where('created_at', '<=', now()->subHour())
        ->orderBy('id')
        ->chunkById(200, function ($orders) {
            foreach ($orders as $order) {
                // Eager load related items and products
                $order->load(['items.product']);
                // Restore product stock for each item
                foreach ($order->items as $item) {
                    if ($item->product) {
                        // qty column name is `qty` in order_items
                        $item->product->increment('stock', (int) $item->qty);
                    }
                }
                // Delete the order (order_items removed by cascade)
                $order->delete();
            }
        });
})->everyTenMinutes()->name('cleanup-pending-orders-older-than-1h');
