<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi POS</title>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 30px;
            color: #333;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #1e3a8a;
            font-size: 24px;
            margin: 0;
            font-weight: 700;
        }

        .info {
            font-size: 14px;
            color: #4b5563;
            margin: 10px 0;
        }

        .logo-placeholder {
            width: 80px;
            height: 80px;
            background-color: #e5e7eb;
            border-radius: 50%;
            display: inline-block;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 12px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 10px;
            text-align: left;
            border: 1px solid #e5e7eb;
        }

        thead th {
            background-color: #2563eb;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 11px;
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        tbody tr:hover {
            background-color: #eff6ff;
        }

        .summary-box {
            background-color: #dbeafe;
            border-left: 4px solid #2563eb;
            padding: 15px;
            margin-top: 30px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            color: #1e40af;
        }

        .footer {
            margin-top: 50px;
            text-align: right;
            font-style: italic;
            color: #6b7280;
            font-size: 11px;
        }

        @media print {
            body {
                margin: 15px;
            }
        }
    </style>
</head>
<body>

    <div class="header">
       
        <h1>LAPORAN TRANSAKSI ESSYCOFF</h1>
        <div class="info">
            <strong>Periode:</strong> 
            {{ \Carbon\Carbon::parse($fromDate)->translatedFormat('d F Y') }} - 
            {{ \Carbon\Carbon::parse($toDate)->translatedFormat('d F Y') }}
        </div>
    </div>

    @php
        // Filter hanya yang status paid
        $paidOrders = collect($orders ?? [])->filter(function($o){ return ($o->status ?? null) === 'paid'; });
        // Normalisasi nilai numerik
        $paidOrders = $paidOrders->map(function($o){
            $o->total = (float) ($o->total ?? 0);
            $o->uang_dibayar = (float) ($o->uang_dibayar ?? 0);
            $o->kembalian = (float) ($o->kembalian ?? 0);
            $o->payment_method = strtoupper($o->payment_method ?? '');
            return $o;
        });
        $totalPaid = $paidOrders->sum('total');
        $byMethod = $paidOrders->groupBy('payment_method')->map->sum('total');
    @endphp

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 14%;">No Order</th>
                <th style="width: 16%;">Tanggal</th>
                <th style="width: 16%;">Kasir</th>
                <th style="width: 16%;">Customer</th>
                <th style="width: 9%; text-align: left;">Metode</th>
                <th style="width: 12%; text-align: right;">Total</th>
                <th style="width: 6%; text-align: right;">Bayar</th>
                <th style="width: 6%; text-align: right;">Kembali</th>
            </tr>
        </thead>
        <tbody>
            @forelse($paidOrders as $order)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $order->no_order }}</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $order->user?->name ?? 'Sistem' }}</td>
                <td>{{ $order->customer_name ?? '-' }}</td>
                <td style="text-transform: uppercase;">{{ $order->payment_method }}</td>
                <td style="text-align: right;">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                <td style="text-align: right;">Rp {{ number_format($order->uang_dibayar, 0, ',', '.') }}</td>
                <td style="text-align: right;">Rp {{ number_format($order->kembalian, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center; color:#6b7280;">Tidak ada transaksi paid pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Ringkasan Total -->
    <div class="summary-box">
        <p>📊 <strong>TOTAL PENDAPATAN (PAID):</strong> Rp {{ number_format($totalPaid ?? 0, 0, ',', '.') }}</p>
        <div style="margin-top:6px; font-weight:500;">
            <span>• CASH:</span> Rp {{ number_format((float)($byMethod['CASH'] ?? 0), 0, ',', '.') }}
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <span>• QRIS:</span> Rp {{ number_format((float)($byMethod['QRIS'] ?? 0), 0, ',', '.') }}
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <span>• CARD:</span> Rp {{ number_format((float)($byMethod['CARD'] ?? 0), 0, ',', '.') }}
        </div>
    </div>

    <div class="footer">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }} • Sistem POS v1.0
    </div>

</body>
</html>