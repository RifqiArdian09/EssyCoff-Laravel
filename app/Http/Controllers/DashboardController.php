<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = Order::sum('total');         // Total pendapatan
        $totalOrders = Order::count();               // Total transaksi
        $totalProducts = Product::count();           // Total produk
        $latestOrders = Order::with('user')          // 10 transaksi terbaru
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalProducts',
            'latestOrders'
        ));
    }
}
