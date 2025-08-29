<?php

namespace App\Livewire\Report;

use Livewire\Component;
use App\Models\Order;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdersExport;
use Carbon\Carbon;

class Index extends Component
{
    public $from;
    public $to;
    public $perPage = 10;

    public function mount()
    {
        $this->from = now()->startOfMonth()->format('Y-m-d');
        $this->to = now()->endOfMonth()->format('Y-m-d');
    }

    public function exportExcel()
    {
        return Excel::download(new OrdersExport($this->from, $this->to), 'report_transaksi_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPDF()
    {
        $fromDate = $this->from ?: now()->startOfMonth()->format('Y-m-d');
        $toDate = $this->to ?: now()->endOfMonth()->format('Y-m-d');

        $orders = Order::with('items.product', 'user')
            ->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ])
            ->orderByDesc('created_at')
            ->get();

        // Hitung total berdasarkan filter
        $totalFiltered = $orders->sum('total');

        $pdf = \PDF::loadView('livewire.report.pdf', compact('orders', 'fromDate', 'toDate', 'totalFiltered'));

        return response()->streamDownload(fn() => print($pdf->output()), 'report_transaksi_' . now()->format('Y-m-d') . '.pdf');
    }

    public function render()
    {
        $fromDate = $this->from ?: now()->startOfMonth()->format('Y-m-d');
        $toDate = $this->to ?: now()->endOfMonth()->format('Y-m-d');

        $orders = Order::with('items.product', 'user')
            ->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ])
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        // Hitung pendapatan sesuai filter
        $dailyTotal = Order::whereDate('created_at', today())->sum('total');
        $monthlyTotal = Order::whereMonth('created_at', now()->month)
                             ->whereYear('created_at', now()->year)
                             ->sum('total');

        return view('livewire.report.index', [
            'orders' => $orders,
            'dailyTotal' => $dailyTotal,
            'monthlyTotal' => $monthlyTotal,
        ]);
    }
}