<?php

namespace App\Livewire\Actions;

use App\Models\Order;

class GetOrderHistory
{
    public function __invoke()
    {
        $orders = Order::with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        $favoriteItems = [];
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $productId = $item->product_id;
                if (!isset($favoriteItems[$productId])) {
                    $favoriteItems[$productId] = [
                        'product' => $item->product,
                        'total_ordered' => 0,
                        'last_ordered' => $order->created_at,
                        'order_count' => 0,
                    ];
                }
                $favoriteItems[$productId]['total_ordered'] += $item->qty;
                $favoriteItems[$productId]['order_count']++;
                if ($order->created_at > $favoriteItems[$productId]['last_ordered']) {
                    $favoriteItems[$productId]['last_ordered'] = $order->created_at;
                }
            }
        }

        uasort($favoriteItems, function ($a, $b) {
            return $b['total_ordered'] <=> $a['total_ordered'];
        });

        return response()->json([
            'success' => true,
            'orders' => $orders,
            'favorites' => array_values($favoriteItems),
        ]);
    }
}
