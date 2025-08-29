<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class OrdersExport implements FromCollection, WithHeadings, WithEvents
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $fromDate = $this->startDate ? $this->startDate : now()->startOfMonth();
        $toDate = $this->endDate ? $this->endDate : now()->endOfMonth();

        $query = Order::query();

        if ($this->startDate) $query->whereDate('created_at', '>=', $this->startDate);
        if ($this->endDate) $query->whereDate('created_at', '<=', $this->endDate);

        $orders = $query->with('user')->get()->map(function($order) {
            return [
                'No Order' => $order->no_order,
                'Tanggal' => $order->created_at->format('d/m/Y H:i'),
                'Kasir' => $order->user?->name ?? 'Sistem',
                'Total' => $order->total,
                'Uang Dibayar' => $order->uang_dibayar,
                'Kembalian' => $order->kembalian,
                'Sumber' => ucfirst($order->source),
            ];
        });

        // Hitung total berdasarkan filter
        $totalFiltered = $query->sum('total');

        // Tambahkan baris ringkasan
        $summary = [
            'No Order' => '',
            'Tanggal' => '',
            'Kasir' => '',
            'Total' => "Total Pendapatan: Rp " . number_format($totalFiltered, 0, ',', '.'),
            'Uang Dibayar' => '',
            'Kembalian' => '',
            'Sumber' => '',
        ];

        return $orders->push((object)$summary);
    }

    public function headings(): array
    {
        return ['No Order', 'Tanggal', 'Kasir', 'Total', 'Uang Dibayar', 'Kembalian', 'Sumber'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Gaya baris ringkasan
                $sheet->getStyle("A{$lastRow}:G{$lastRow}")
                      ->getFont()->setBold(true);
                $sheet->getStyle("A{$lastRow}:G{$lastRow}")
                      ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->mergeCells("A{$lastRow}:C{$lastRow}");
            }
        ];
    }
}