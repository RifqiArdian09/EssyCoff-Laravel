<?php

namespace App\Livewire\Report;

use Livewire\Component;
use App\Models\Order;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdersExport;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class Index extends Component
{
    public $from;
    public $to;
    public $perPage = 10;
    public $page = 1;
    
    protected $queryString = [
        'from' => ['except' => ''],
        'to' => ['except' => ''],
        'page' => ['except' => 1, 'as' => 'p'],
        'perPage' => ['except' => 10]
    ];
    
    protected $listeners = [
        'refreshComponent' => '$refresh'
    ];
    
    protected $paginationTheme = 'bootstrap';

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
        $totalFiltered = Order::whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ])
            ->where('status', 'paid')
            ->sum('total');

        $pdf = PDF::loadView('livewire.report.pdf', compact('orders', 'fromDate', 'toDate', 'totalFiltered'));
        
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'laporan_transaksi_'.now()->format('Y-m-d').'.pdf');
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
            ->paginate($this->perPage)
            ->withQueryString(); // This ensures pagination works with query strings

        // Hitung pendapatan sesuai filter
        $dailyTotal = Order::where('status', 'paid')
                           ->whereDate('created_at', today())
                           ->sum('total');
        $monthlyTotal = Order::where('status', 'paid')
                             ->whereMonth('created_at', now()->month)
                             ->whereYear('created_at', now()->year)
                             ->sum('total');

        return view('livewire.report.index', [
            'orders' => $orders,
            'dailyTotal' => $dailyTotal,
            'monthlyTotal' => $monthlyTotal,
        ]);
    }
}