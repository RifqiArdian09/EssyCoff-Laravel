<?php

namespace App\Livewire\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CafeTable;

class CreateOrder
{
    public function __invoke(Request $request)
    {
        // Device identification via persistent cookie
        $deviceId = $request->cookie('device_id');
        if (!$deviceId) {
            $deviceId = (string) Str::uuid();
            // Persist for 1 year
            Cookie::queue(cookie('device_id', $deviceId, 60 * 24 * 365));
        }

        // Rate limit: max 3 orders per 3 minutes per device
        $rateKey = 'orders:device:' . $deviceId;
        $maxAttempts = 3; // max 3 pesan
        $decaySeconds = 180; // 3 menit

        if (RateLimiter::tooManyAttempts($rateKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($rateKey);
            return response()->json([
                'success' => false,
                'message' => 'Terlalu sering memesan. Silakan coba lagi dalam ' . $seconds . ' detik.',
                'error_type' => 'rate_limited',
                'retry_after' => $seconds,
            ], 429);
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0',
            'table' => 'nullable|string|max:100', // table code from URL/QR
        ]);

        // Generate unique order number resiliently to avoid duplicates under concurrency
        $order = null;
        $orderNumber = null;
        $attempts = 0;

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
            
            Log::info('Order created with valid table', [
                'table_code' => $tableCode,
                'table_name' => $table->name,
                'customer_name' => $request->customer_name
            ]);
        }

        // Serialize number generation using MySQL advisory lock to avoid race conditions
        $today = now();
        $prefix = 'ORD-' . $today->format('Ymd') . '-';
        $lockKey = 'orders_seq_' . $today->format('Ymd');
        $lockAcquired = false;

        try {
            $lockAcquired = (bool) collect(DB::select('SELECT GET_LOCK(?, 5) as l', [$lockKey]))->first()->l;
            if (!$lockAcquired) {
                Log::warning('Could not acquire order number lock');
            }

            // Retry in case of a very rare duplicate even under lock
            do {
                $attempts++;
                $lastNo = Order::where('no_order', 'like', $prefix . '%')
                    ->orderBy('no_order', 'desc')
                    ->value('no_order');

                $seq = 0;
                if ($lastNo && preg_match('/^ORD-\\d{8}-(\\d{4})$/', $lastNo, $m)) {
                    $seq = (int) $m[1];
                }
                $seq++;
                $orderNumber = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);

                try {
                    $order = Order::create([
                        'no_order' => $orderNumber,
                        'table_id' => $tableId,
                        'customer_name' => $request->customer_name,
                        'total' => $request->total,
                        'status' => 'pending_payment',
                    ]);
                } catch (QueryException $e) {
                    // Detect duplicate key reliably (MySQL error code 1062)
                    $isDuplicate = ($e->getCode() === '23000') || (isset($e->errorInfo[1]) && (int)$e->errorInfo[1] === 1062);
                    if ($isDuplicate) {
                        usleep(50000);
                        $order = null;
                    } else {
                        throw $e;
                    }
                }
            } while (!$order && $attempts < 10);
        } finally {
            if ($lockAcquired) {
                DB::select('SELECT RELEASE_LOCK(?)', [$lockKey]);
            }
        }

        if (!$order) {
            Log::error('Failed to generate unique order number after retries');
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat nomor pesanan unik. Silakan coba lagi.',
                'error_type' => 'order_number_conflict'
            ], 409);
        }

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

        // Count only successful orders towards the rate limit window
        RateLimiter::hit($rateKey, $decaySeconds);

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

    // Helper to parse sequence (kept simple within this class)
    private function parseSeq(?string $no): int
    {
        if ($no && preg_match('/^ORD-\\d{8}-(\\d{4})$/', $no, $m)) {
            return (int) $m[1];
        }
        return 0;
    }
}

