<?php

namespace App\Livewire\Actions;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class CreateOrder
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0',
        ]);

        $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(Order::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

        $order = Order::create([
            'no_order' => $orderNumber,
            'customer_name' => $request->customer_name,
            'total' => $request->total,
            'status' => 'pending_payment',
        ]);

        foreach ($request->items as $item) {
            $product = Product::find($item['id']);
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'qty' => $item['quantity'],
                'harga' => $product->price,
                'subtotal' => $product->price * $item['quantity'],
            ]);

            $product->decrement('stock', $item['quantity']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat dengan status Pending Payment',
            'order' => $order,
        ]);
    }
}
