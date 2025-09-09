<?php

namespace App\Livewire\Report;

use Livewire\Component;
use App\Models\Order;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdersExport;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    
    public $from;
    public $to;
    public $selectedMonth = '';
    public $perPage = 10;
    
    protected $queryString = [
        'from' => ['except' => ''],
        'to' => ['except' => ''],
        'selectedMonth' => ['except' => ''],
        'perPage' => ['except' => 10]
    ];
    
    public function mount()
    {
        // Set default values jika tidak ada di query string
        if (!$this->from && !$this->to && !$this->selectedMonth) {
            $this->selectedMonth = now()->format('Y-m');
        }
    }

    /**
     * Reset pagination dan filter lainnya ketika mengubah bulan
     */
    public function updatedSelectedMonth($value)
    {
        if ($value) {
            // Reset filter tanggal ketika memilih bulan
            $this->from = null;
            $this->to = null;
        }
        $this->resetPage();
    }

    /**
     * Reset pagination dan filter bulan ketika mengubah tanggal
     */
    public function updatedFrom()
    {
        $this->selectedMonth = '';
        $this->resetPage();
    }

    public function updatedTo()
    {
        $this->selectedMonth = '';
        $this->resetPage();
    }

    /**
     * Get available months for filter
     */
    public function getAvailableMonths()
    {
        return Order::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month')
            ->distinct()
            ->orderByDesc('month')
            ->pluck('month')
            ->map(function ($month) {
                return [
                    'value' => $month,
                    'label' => \Carbon\Carbon::createFromFormat('Y-m', $month)->locale('id')->translatedFormat('F Y')
                ];
            });
    }

    public function exportExcel()
    {
        list($fromDate, $toDate) = $this->getFilterDates();
        
        return Excel::download(
            new OrdersExport($fromDate, $toDate), 
            'report_transaksi_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportPDF()
    {
        list($fromDate, $toDate) = $this->getFilterDates();

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

    /**
     * Helper method untuk mendapatkan tanggal filter yang konsisten
     */
    private function getFilterDates()
    {
        if ($this->selectedMonth) {
            $fromDate = Carbon::createFromFormat('Y-m', $this->selectedMonth)->startOfMonth()->format('Y-m-d');
            $toDate = Carbon::createFromFormat('Y-m', $this->selectedMonth)->endOfMonth()->format('Y-m-d');
        } else {
            $fromDate = $this->from ?: now()->startOfMonth()->format('Y-m-d');
            $toDate = $this->to ?: now()->endOfMonth()->format('Y-m-d');
        }
        
        return [$fromDate, $toDate];
    }

    public function render()
    {
        $query = Order::with('items.product', 'user')->where('status', 'paid');

        // Terapkan filter berdasarkan pilihan pengguna
        if ($this->selectedMonth) {
            // Filter by bulan terpilih
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$this->selectedMonth]);
        } elseif ($this->from || $this->to) {
            // Filter by tanggal
            $fromDate = $this->from ?: now()->startOfMonth()->format('Y-m-d');
            $toDate = $this->to ?: now()->endOfMonth()->format('Y-m-d');
            
            $query->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        } else {
            // Default ke bulan ini jika tidak ada filter
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        }

        $orders = $query->orderByDesc('created_at')
            ->paginate($this->perPage);

        // Hitung pendapatan sesuai filter
        list($fromDate, $toDate) = $this->getFilterDates();
        
        $dailyTotal = Order::where('status', 'paid')
                           ->whereDate('created_at', today())
                           ->sum('total');
                           
        $monthlyTotal = Order::where('status', 'paid')
            ->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ])
            ->sum('total');

        $availableMonths = $this->getAvailableMonths();

        return view('livewire.report.index', [
            'orders' => $orders,
            'dailyTotal' => $dailyTotal,
            'monthlyTotal' => $monthlyTotal,
            'availableMonths' => $availableMonths,
        ]);
    }
}