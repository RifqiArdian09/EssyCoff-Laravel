<h1>Report Transaksi POS</h1>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>No Order</th>
            <th>Tanggal</th>
            <th>Kasir</th>
            <th>Total</th>
            <th>Uang Dibayar</th>
            <th>Kembalian</th>
            <th>Sumber</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $index => $order)
        <tr>
            <td>{{ $index+1 }}</td>
            <td>{{ $order->no_order }}</td>
            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ $order->user?->name ?? '-' }}</td>
            <td>{{ $order->total }}</td>
            <td>{{ $order->uang_dibayar }}</td>
            <td>{{ $order->kembalian }}</td>
         
        </tr>
        @endforeach
    </tbody>
</table>
