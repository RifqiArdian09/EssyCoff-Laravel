<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class OrdersExport implements WithMultipleSheets
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        return [
            new OrdersAllSheet($this->startDate, $this->endDate),
            new OrdersMethodSheet($this->startDate, $this->endDate, 'cash'),
            new OrdersMethodSheet($this->startDate, $this->endDate, 'qris'),
            new OrdersMethodSheet($this->startDate, $this->endDate, 'card'),
        ];
    }
}
