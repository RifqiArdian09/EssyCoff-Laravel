<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OrdersMethodSheet implements FromCollection, WithHeadings, WithEvents, WithTitle, WithCustomStartCell
{
    protected $startDate;
    protected $endDate;
    protected string $method; // cash | qris | card

    public function __construct($startDate = null, $endDate = null, string $method = 'cash')
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->method = $method;
    }

    public function title(): string
    {
        return strtoupper($this->method);
    }

    public function collection()
    {
        $query = Order::query()
            ->where('status', 'paid')
            ->where('payment_method', $this->method);

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        $orders = $query->with('user','table')->get()->map(function ($order) {
            return [
                'No Order' => $order->no_order,
                'Tanggal' => $order->created_at->format('d/m/Y H:i'),
                'Kasir' => $order->user?->name ?? 'Sistem',
                'Customer' => $order->customer_name ?? '-',
                'Meja' => $order->table ? ($order->table->name . ' (' . $order->table->code . ')') : '-',
                'Metode' => strtoupper($order->payment_method ?? ''),
                'Total' => $order->total,
                'Uang Dibayar' => $order->uang_dibayar,
                'Kembalian' => $order->kembalian ?? 0,
            ];
        });

        $totalFiltered = $query->sum('total');

        // Separator row
        $orders->push([
            'No Order' => '',
            'Tanggal' => '',
            'Kasir' => '',
            'Customer' => '',
            'Meja' => '',
            'Metode' => '',
            'Total' => '',
            'Uang Dibayar' => '',
            'Kembalian' => '',
        ]);

        // Total row
        $orders->push([
            'No Order' => 'TOTAL PENDAPATAN',
            'Tanggal' => '',
            'Kasir' => '',
            'Customer' => '',
            'Meja' => '',
            'Metode' => '',
            'Total' => $totalFiltered,
            'Uang Dibayar' => '',
            'Kembalian' => '',
        ]);

        return $orders;
    }

    public function headings(): array
    {
        return ['No Order', 'Tanggal', 'Kasir', 'Customer', 'Meja', 'Metode', 'Total', 'Uang Dibayar', 'Kembalian'];
    }

    public function startCell(): string
    {
        // Put headings at row 2 so row 1 can be used for the title
        return 'A2';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Title row
                $fromDate = $this->startDate ? \Carbon\Carbon::parse($this->startDate)->format('d/m/Y') : \Carbon\Carbon::now()->startOfMonth()->format('d/m/Y');
                $toDate = $this->endDate ? \Carbon\Carbon::parse($this->endDate)->format('d/m/Y') : \Carbon\Carbon::now()->endOfMonth()->format('d/m/Y');
                $title = "Laporan {$this->title()} (Paid) ({$fromDate} - {$toDate})";
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', $title);
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Header style (row 2)
                $headerRange = 'A2:H2';
                $sheet->getStyle($headerRange)
                    ->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '2563EB'],
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

                // Money columns F-H
                foreach (['F', 'G', 'H'] as $col) {
                    $range = "{$col}3:{$col}" . ($lastRow - 1);
                    $sheet->getStyle($range)
                        ->getNumberFormat()
                        ->setFormatCode('_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"??_);_(@_)');
                }

                // Total row styling
                $totalRow = $lastRow;
                $sheet->mergeCells("A{$totalRow}:E{$totalRow}");
                $sheet->getStyle("A{$totalRow}:H{$totalRow}")
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
                $sheet->getStyle("F{$totalRow}")
                    ->getNumberFormat()
                    ->setFormatCode('_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"??_);_(@_)');

                // Alignment
                $sheet->getStyle('A3:E' . ($lastRow - 1))
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('F3:H' . ($lastRow - 1))
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("A{$totalRow}:E{$totalRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("F{$totalRow}:H{$totalRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Auto width
                foreach (range('A', 'H') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Freeze header
                $sheet->freezePane('A3');
            }
        ];
    }
}
