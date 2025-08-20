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

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        $this->from = now()->startOfMonth()->format('Y-m-d');
        $this->to = now()->endOfMonth()->format('Y-m-d');
    }

    public function exportExcel()
    {
        return Excel::download(new OrdersExport($this->from, $this->to), 'report.xlsx');
    }

    public function exportPDF()
    {
        $orders = Order::with('items.product', 'user')
            ->whereBetween('created_at', [$this->from, $this->to])
            ->get();

        $pdf = \PDF::loadView('livewire.report.pdf', compact('orders'));
        return response()->streamDownload(fn() => print($pdf->output()), 'report.pdf');
    }

    public function render()
    {
        $orders = Order::with('items.product', 'user')
            ->whereBetween('created_at', [$this->from, $this->to])
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        $dailyTotal = Order::whereDate('created_at', now())->sum('total');
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
