<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi POS</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .info { margin-bottom: 15px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        h1 { color: #333; margin: 0; }
        .summary { margin-top: 30px; font-size: 16px; }
        .summary p { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Report Transaksi POS</h1>
        <div class="info">
            <strong>Periode:</strong> 
            {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} - 
            {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Order</th>
                <th>Tanggal</th>
                <th>Kasir</th>
                <th>Total</th>
                <th>Bayar</th>
                <th>Kembali</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $order->no_order }}</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $order->user?->name ?? 'Sistem' }}</td>
                <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($order->uang_dibayar, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($order->kembalian, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tambahkan ringkasan -->
    <div class="summary">
        <p><strong>Total Pendapatan (Periode Ini):</strong> Rp {{ number_format($totalFiltered ?? 0, 0, ',', '.') }}</p>
    </div>

</body>
</html>