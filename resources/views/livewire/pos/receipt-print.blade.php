<div class="p-4 text-sm font-mono" onload="window.print()">
    <div class="text-center">
        <h2 class="text-lg font-bold">EssyCoff</h2>
        <p>No. Order: {{ $order->no_order }}</p>
        <p>{{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <hr class="my-2 border-dashed">

    @foreach($order->items as $item)
        <div class="flex justify-between">
            <span>{{ $item->product->name }} x{{ $item->qty }}</span>
            <span>Rp {{ number_format($item->subtotal,0,',','.') }}</span>
        </div>
    @endforeach

    <hr class="my-2 border-dashed">

    <div class="flex justify-between font-bold">
        <span>Total</span>
        <span>Rp {{ number_format($order->total,0,',','.') }}</span>
    </div>
    <div class="flex justify-between">
        <span>Bayar</span>
        <span>Rp {{ number_format($order->uang_dibayar,0,',','.') }}</span>
    </div>
    <div class="flex justify-between">
        <span>Kembali</span>
        <span>Rp {{ number_format($order->kembalian,0,',','.') }}</span>
    </div>

    <hr class="my-2 border-dashed">

    <div class="text-center">
        <p>Terima Kasih 🙏</p>
        <p>~ EssyCoff ~</p>
    </div>
</div>

<script>
    window.addEventListener('load', function() {
        window.print();
    });
</script>
