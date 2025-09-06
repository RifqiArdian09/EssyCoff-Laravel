<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

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
        $fromDate = $this->startDate ?: now()->startOfMonth();
        $toDate = $this->endDate ?: now()->endOfMonth();

        $query = Order::query();

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        $orders = $query->with('user')->get()->map(function ($order) {
            return [
                'No Order' => $order->no_order,
                'Tanggal' => $order->created_at->format('d/m/Y H:i'),
                'Kasir' => $order->user?->name ?? 'Sistem',
                'Total' => $order->total,
                'Uang Dibayar' => $order->uang_dibayar,
                'Kembalian' => $order->kembalian ?? 0, // ← INI PERUBAHANNYA!
            ];
        });

        $totalFiltered = $query->sum('total');

        // Tambahkan baris kosong sebagai pemisah
        $orders->push([
            'No Order' => '',
            'Tanggal' => '',
            'Kasir' => '',
            'Total' => '',
            'Uang Dibayar' => '',
            'Kembalian' => '',
        ]);

        // Baris ringkasan total
        $orders->push([
            'No Order' => 'TOTAL PENDAPATAN',
            'Tanggal' => '',
            'Kasir' => '',
            'Total' => $totalFiltered,
            'Uang Dibayar' => '',
            'Kembalian' => '',
        ]);

        return $orders;
    }

    public function headings(): array
    {
        return ['No Order', 'Tanggal', 'Kasir', 'Total', 'Uang Dibayar', 'Kembalian'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // === STYLING HEADER ===
                $headerRange = 'A1:F1';
                $sheet->getStyle($headerRange)
                    ->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '2563EB'], // Biru modern
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                    ]);

                // === STYLING KOLOM UANG ===
                foreach (['D', 'E', 'F'] as $col) {
                    $range = "{$col}2:{$col}" . ($lastRow - 1); // sampai baris sebelum ringkasan
                    $sheet->getStyle($range)
                        ->getNumberFormat()
                        ->setFormatCode('_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"??_);_(@_)');
                }

                // === STYLING BARIS TOTAL ===
                $totalRow = $lastRow;
                $sheet->mergeCells("A{$totalRow}:C{$totalRow}");
                $sheet->getStyle("A{$totalRow}:F{$totalRow}")
                    ->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F3F4F6'],
                        ],
                        'borders' => [
                            'top' => [
                                'borderStyle' => Border::BORDER_MEDIUM,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                    ]);
                $sheet->getStyle("D{$totalRow}")
                    ->getNumberFormat()
                    ->setFormatCode('_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"??_);_(@_)');

                // === ALIGNMENT ===
                $sheet->getStyle('A2:C' . ($lastRow - 1))
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('D2:F' . ($lastRow - 1))
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->getStyle("A{$totalRow}:C{$totalRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("D{$totalRow}:F{$totalRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // === AUTO WIDTH ===
                foreach (range('A', 'F') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // === FREEZE HEADER ===
                $sheet->freezePane('A2');
            }
        ];
    }
}
