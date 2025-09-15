<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class Index extends Component
{
    public int $totalOrdersToday = 0;
    public int $totalProductsSold = 0;
    public float $totalRevenueToday = 0;

    public float $revenueGrowth = 0.0;
    public float $ordersGrowth = 0.0;
    public float $productsGrowth = 0.0;

    public $topProducts = [];
    public $currentMonthDays = [];
    public $last7Days = [];
    public $recentOrders = [];
    public array $statusCounts = [];

    public function mount(): void
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $previousDay = Carbon::yesterday();

        $previousRevenue = Order::where('status', 'paid')
            ->whereDate('created_at', $previousDay)
            ->sum('total') ?: 1; // avoid div by zero
        $previousOrders = Order::whereDate('created_at', $previousDay)->count() ?: 1;
        $previousProducts = OrderItem::whereHas('order', function ($query) use ($previousDay) {
            $query->whereDate('created_at', $previousDay);
        })->sum('qty') ?: 1;

        $this->totalRevenueToday = (float) Order::where('status', 'paid')
            ->whereDate('created_at', $today)
            ->sum('total');
        $this->totalOrdersToday = (int) Order::whereDate('created_at', $today)->count();
        $this->totalProductsSold = (int) OrderItem::whereHas('order', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->sum('qty');

        $this->revenueGrowth = (($this->totalRevenueToday - $previousRevenue) / $previousRevenue) * 100;
        $this->ordersGrowth = (($this->totalOrdersToday - $previousOrders) / $previousOrders) * 100;
        $this->productsGrowth = (($this->totalProductsSold - $previousProducts) / $previousProducts) * 100;

        $this->topProducts = OrderItem::selectRaw('product_id, SUM(qty) as total_sold')
            ->whereHas('order', function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        $this->last7Days = collect(range(6, 0))->map(function ($day) {
            $date = Carbon::today()->subDays($day);
            return [
                'date' => $date->format('d M'),
                'total' => Order::where('status', 'paid')
                    ->whereDate('created_at', $date)
                    ->sum('total'),
            ];
        });

        $currentMonthDays = collect();
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $currentMonthDays->push([
                'date' => $date->format('d M'),
                'total' => Order::where('status', 'paid')
                    ->whereDate('created_at', $date)
                    ->sum('total')
            ]);
        }
        $this->currentMonthDays = $currentMonthDays;

        $this->recentOrders = Order::with('user')
            ->latest('created_at')
            ->take(5)
            ->get();

        $this->statusCounts = [
            'pending_payment' => Order::where('status', 'pending_payment')->count(),
            'paid' => Order::where('status', 'paid')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.index', [
            'totalOrdersToday' => $this->totalOrdersToday,
            'totalRevenueToday' => $this->totalRevenueToday,
            'totalProductsSold' => $this->totalProductsSold,
            'revenueGrowth' => $this->revenueGrowth,
            'ordersGrowth' => $this->ordersGrowth,
            'productsGrowth' => $this->productsGrowth,
            'topProducts' => $this->topProducts,
            'currentMonthDays' => collect($this->currentMonthDays),
            'last7Days' => collect($this->last7Days),
            'recentOrders' => $this->recentOrders,
            'statusCounts' => $this->statusCounts,
        ]);
    }
}
