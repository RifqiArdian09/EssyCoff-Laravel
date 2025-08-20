<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
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
        $query = Order::with('user');

        if ($this->startDate) $query->whereDate('created_at', '>=', $this->startDate);
        if ($this->endDate) $query->whereDate('created_at', '<=', $this->endDate);

        return $query->get()->map(function($order) {
            return [
                'No Order' => $order->no_order,
                'Tanggal' => $order->created_at->format('d/m/Y H:i'),
                'Kasir' => $order->user?->name,
                'Total' => $order->total,
                'Uang Dibayar' => $order->uang_dibayar,
                'Kembalian' => $order->kembalian,
                'Sumber' => ucfirst($order->source),
            ];
        });
    }

    public function headings(): array
    {
        return ['No Order','Tanggal','Kasir','Total','Uang Dibayar','Kembalian','Sumber'];
    }
}
