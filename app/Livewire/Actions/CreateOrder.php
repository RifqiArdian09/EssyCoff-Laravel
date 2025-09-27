<?php

namespace App\Livewire\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CafeTable;

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
            'table' => 'nullable|string|max:100', // table code from URL/QR
        ]);

        $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(Order::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

        // Resolve table by code if provided and available
        $tableId = null;
        if ($request->filled('table')) {
            $tableCode = $request->input('table');
            $table = CafeTable::where('code', $tableCode)->first();
            if (!$table) {
                // Log the attempt for monitoring
                Log::warning('Customer attempted to order with invalid table code', [
                    'table_code' => $tableCode,
                    'customer_name' => $request->customer_name,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Meja dengan kode "' . $tableCode . '" tidak ditemukan. Silakan hubungi staff untuk bantuan.',
                    'error_type' => 'table_not_found'
                ], 404);
            }
            $tableId = $table->id;
            
            // Log successful table validation
            Log::info('Order created with valid table', [
                'table_code' => $tableCode,
                'table_name' => $table->name,
                'customer_name' => $request->customer_name
            ]);
        }

        $order = Order::create([
            'no_order' => $orderNumber,
            'table_id' => $tableId,
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

        $message = 'Pesanan berhasil dibuat dengan status Pending Payment';
        if ($tableId) {
            $tableName = CafeTable::find($tableId)->name ?? 'Unknown';
            $message = 'Pesanan berhasil dibuat untuk ' . $tableName . ' dengan status Pending Payment';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'order' => $order,
            'table_info' => $tableId ? [
                'id' => $tableId,
                'name' => $tableName ?? null,
                'code' => $request->input('table')
            ] : null,
        ]);
    }
}
