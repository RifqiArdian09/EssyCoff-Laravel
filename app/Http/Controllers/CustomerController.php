<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');
        
        $products = Product::with('category')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhereHas('category', function ($cat) use ($search) {
                          $cat->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($category && $category !== 'all', function ($query) use ($category) {
                $query->whereHas('category', function ($cat) use ($category) {
                    $cat->where('name', 'like', "%{$category}%");
                });
            })
            ->latest()
            ->get();

        // Get favorite counts for each product
        $orders = Order::with(['items.product'])->get();
        $favoriteData = [];
        
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $productId = $item->product_id;
                if (!isset($favoriteData[$productId])) {
                    $favoriteData[$productId] = [
                        'total_ordered' => 0,
                        'order_count' => 0
                    ];
                }
                $favoriteData[$productId]['total_ordered'] += $item->qty;
                $favoriteData[$productId]['order_count']++;
            }
        }

        // Add favorite data to products
        foreach ($products as $product) {
            $product->favorite_data = $favoriteData[$product->id] ?? [
                'total_ordered' => 0,
                'order_count' => 0
            ];
        }
            
        $categories = Category::all();
        
        return view('customer', compact('products', 'categories', 'search', 'category'));
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0'
        ]);

        $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(Order::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

        // Create order with pending_payment status
        $order = Order::create([
            'no_order' => $orderNumber,
            'customer_name' => $request->customer_name,
            'total' => $request->total,
            'status' => 'pending_payment'
        ]);

        // Create order items
        foreach ($request->items as $item) {
            $product = Product::find($item['id']);
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'qty' => $item['quantity'],
                'harga' => $product->price,
                'subtotal' => $product->price * $item['quantity']
            ]);

            // Update product stock
            $product->decrement('stock', $item['quantity']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat dengan status Pending Payment',
            'order' => $order
        ]);
    }

    public function getOrderHistory(Request $request)
    {
        $orders = Order::with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Group items by product for favorites
        $favoriteItems = [];
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $productId = $item->product_id;
                if (!isset($favoriteItems[$productId])) {
                    $favoriteItems[$productId] = [
                        'product' => $item->product,
                        'total_ordered' => 0,
                        'last_ordered' => $order->created_at,
                        'order_count' => 0
                    ];
                }
                $favoriteItems[$productId]['total_ordered'] += $item->qty;
                $favoriteItems[$productId]['order_count']++;
                
                // Update last ordered date if this order is more recent
                if ($order->created_at > $favoriteItems[$productId]['last_ordered']) {
                    $favoriteItems[$productId]['last_ordered'] = $order->created_at;
                }
            }
        }

        // Sort by most ordered
        uasort($favoriteItems, function($a, $b) {
            return $b['total_ordered'] <=> $a['total_ordered'];
        });

        return response()->json([
            'success' => true,
            'orders' => $orders,
            'favorites' => array_values($favoriteItems)
        ]);
    }

}
