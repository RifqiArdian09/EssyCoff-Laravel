<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Check if user is a cashier and redirect to POS
        if (auth()->user()->role === 'cashier') {
            return redirect()->route('pos.cashier');
        }

        // Hari ini
        $today = Carbon::today();

        $totalOrdersToday = Order::whereDate('created_at', $today)->count();
        $totalRevenueToday = Order::whereDate('created_at', $today)->sum('total');

        $statusCounts = [
            'pending'   => Order::where('status', 'pending')->count(),
            'diproses'  => Order::where('status', 'diproses')->count(),
            'selesai'   => Order::where('status', 'selesai')->count(),
        ];

        // Grafik omzet 7 hari terakhir
        $last7Days = collect(range(6, 0))->map(function ($day) {
            $date = Carbon::today()->subDays($day);
            return [
                'date' => $date->format('d M'),
                'total' => Order::whereDate('created_at', $date)->sum('total')
            ];
        });

        // Produk terlaris (Top 5)
        $topProducts = OrderItem::selectRaw('product_id, SUM(qty) as total_sold')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalOrdersToday',
            'totalRevenueToday',
            'statusCounts',
            'last7Days',
            'topProducts'
        ));
    }
}
