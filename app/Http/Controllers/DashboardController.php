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

        $today = Carbon::today();

        // 📊 Statistik Hari Ini
        $totalOrdersToday = Order::whereDate('created_at', $today)->count();
        $totalRevenueToday = Order::whereDate('created_at', $today)->sum('total');

        // 🔢 Total produk terjual hari ini
        $totalProductsSold = OrderItem::whereHas('order', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->sum('qty');

        // 🏆 Produk terlaris (top 5)
        $topProducts = OrderItem::selectRaw('product_id, SUM(qty) as total_sold')
            ->whereHas('order', function ($query) use ($today) {
                $query->whereDate('created_at', $today);
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get(); // Ambil 5 produk terlaris

        // 📈 Grafik Omzet 7 Hari Terakhir
        $last7Days = collect(range(6, 0))->map(function ($day) {
            $date = Carbon::today()->subDays($day);
            return [
                'date' => $date->format('d M'),
                'total' => Order::whereDate('created_at', $date)->sum('total'),
            ];
        });

        // 📋 5 Transaksi Terakhir
        $recentOrders = Order::with('user') // Agar nama kasir muncul
            ->latest('created_at')
            ->take(5)
            ->get();

        // 📌 Status Counts (jika kamu masih butuh)
        $statusCounts = [
            'pending_payment' => Order::where('status', 'pending_payment')->count(),
            'paid'            => Order::where('status', 'paid')->count(),
        ];

        return view('dashboard', compact(
            'totalOrdersToday',
            'totalRevenueToday',
            'totalProductsSold',
            'topProducts',
            'last7Days',
            'recentOrders',
            'statusCounts'
        ));
    }
}