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
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $previousDay = Carbon::yesterday();

        // Calculate previous values first
        $previousRevenue = Order::where('status', 'paid')
            ->whereDate('created_at', $previousDay)
            ->sum('total') ?: 1; // Avoid division by zero
        $previousOrders = Order::whereDate('created_at', $previousDay)->count() ?: 1;
        $previousProducts = OrderItem::whereHas('order', function ($query) use ($previousDay) {
            $query->whereDate('created_at', $previousDay);
        })->sum('qty') ?: 1;

        // Get today's stats
        $totalRevenueToday = Order::where('status', 'paid')
            ->whereDate('created_at', $today)
            ->sum('total');
        $totalOrdersToday = Order::whereDate('created_at', $today)->count();
        $totalProductsSold = OrderItem::whereHas('order', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->sum('qty');

        // Calculate growth percentages
        $revenueGrowth = (($totalRevenueToday - $previousRevenue) / $previousRevenue) * 100;
        $ordersGrowth = (($totalOrdersToday - $previousOrders) / $previousOrders) * 100;
        $productsGrowth = (($totalProductsSold - $previousProducts) / $previousProducts) * 100;

        // 🏆 Produk terlaris bulan ini (top 5)
        $topProducts = OrderItem::selectRaw('product_id, SUM(qty) as total_sold')
            ->whereHas('order', function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // 📈 Grafik Omzet 7 Hari Terakhir
        $last7Days = collect(range(6, 0))->map(function ($day) {
            $date = Carbon::today()->subDays($day);
            return [
                'date' => $date->format('d M'),
                'total' => Order::where('status', 'paid')
                    ->whereDate('created_at', $date)
                    ->sum('total'),
            ];
        });

        // 📈 Data pendapatan harian bulan ini
        $currentMonthDays = collect();
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $currentMonthDays->push([
                'date' => $date->format('d M'),
                'total' => Order::where('status', 'paid')
                    ->whereDate('created_at', $date)
                    ->sum('total')
            ]);
        }

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
            'revenueGrowth',
            'ordersGrowth',
            'productsGrowth',
            'topProducts',
            'currentMonthDays',
            'last7Days',
            'recentOrders',
            'statusCounts'
        ));
    }
}