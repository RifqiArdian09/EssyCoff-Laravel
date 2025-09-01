<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $pendingCount = 0;
            $lowStockCount = 0;
            $outOfStockCount = 0;
            
            if (Auth::check()) {
                // Pending orders count
                $pendingCount = Order::where('status', 'pending_payment')->count();
                
                // Stock alerts - definisikan threshold
                $lowStockThreshold = 10; // Batas stock sedikit
                
                // Produk yang hampir habis (stock <= 10 tapi > 0)
                $lowStockCount = Product::where('stock', '>', 0)
                    ->where('stock', '<=', $lowStockThreshold)
                    ->count();
                
                // Produk yang habis (stock = 0)
                $outOfStockCount = Product::where('stock', 0)->count();
            }
            
            // Total stock alert count
            $totalStockAlerts = $lowStockCount + $outOfStockCount;
            
            $view->with([
                'pendingCount' => $pendingCount,
                'lowStockCount' => $lowStockCount,
                'outOfStockCount' => $outOfStockCount,
                'totalStockAlerts' => $totalStockAlerts,
            ]);
        });
    }
}