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

class OrdersAllSheet implements FromCollection, WithHeadings, WithEvents, WithTitle, WithCustomStartCell
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'ALL';
    }

    public function collection()
    {
        $query = Order::query()->where('status', 'paid');

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
        return 'A2';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                $fromDate = $this->startDate ? \Carbon\Carbon::parse($this->startDate)->format('d/m/Y') : \Carbon\Carbon::now()->startOfMonth()->format('d/m/Y');
                $toDate = $this->endDate ? \Carbon\Carbon::parse($this->endDate)->format('d/m/Y') : \Carbon\Carbon::now()->endOfMonth()->format('d/m/Y');
                $title = "Laporan ALL (Paid) ({$fromDate} - {$toDate})";
                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', $title);
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $headerRange = 'A2:I2';
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

                // Money columns now G (Total), H (Uang Dibayar), I (Kembalian)
                foreach (['G', 'H', 'I'] as $col) {
                    $range = "{$col}3:{$col}" . ($lastRow - 1);
                    $sheet->getStyle($range)
                        ->getNumberFormat()
                        ->setFormatCode('_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"??_);_(@_)');
                }

                $totalRow = $lastRow;
                $sheet->mergeCells("A{$totalRow}:F{$totalRow}");
                $sheet->getStyle("A{$totalRow}:I{$totalRow}")
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
                $sheet->getStyle("G{$totalRow}")
                    ->getNumberFormat()
                    ->setFormatCode('_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"??_);_(@_)');

                $sheet->getStyle('A3:F' . ($lastRow - 1))
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('G3:I' . ($lastRow - 1))
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                foreach (range('A', 'I') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $sheet->freezePane('A3');
            }
        ];
    }
}
