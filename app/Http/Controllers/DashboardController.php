<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Redirect kasir langsung ke POS
        if (auth()->user()->role === 'cashier') {
            return redirect()->route('pos.cashier');
        }

        $today = Carbon::today();

        // Ringkasan hari ini
        $totalOrdersToday = Order::whereDate('created_at', $today)->count();
        $totalRevenueToday = Order::whereDate('created_at', $today)->sum('total');

        // Grafik omzet 7 hari terakhir
        $last7Days = collect(range(6, 0))->map(function ($day) {
            $date = Carbon::today()->subDays($day);
            return [
                'date'  => $date->format('d M'),
                'total' => Order::whereDate('created_at', $date)->sum('total'),
            ];
        });

        // Produk terlaris (Top 5)
        $topProducts = OrderItem::selectRaw('product_id, SUM(qty) as total_sold')
            ->whereHas('product') // hanya produk yang masih ada
            ->with('product:id,name,image')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalOrdersToday',
            'totalRevenueToday',
            'last7Days',
            'topProducts'
        ));
    }
}