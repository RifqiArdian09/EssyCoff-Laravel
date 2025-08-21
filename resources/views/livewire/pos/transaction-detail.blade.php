<section class="w-full max-w-md mx-auto mt-6 p-4 border rounded-lg bg-zinc-50 dark:bg-zinc-900/50">
    <h2 class="text-lg font-bold mb-2">Detail Transaksi</h2>
    <p class="text-xs mb-2">No. Order: {{ $order->no_order }}</p>
    <p class="text-xs mb-4">Customer: {{ $order->customer_name ?: '-' }}</p>

    <div class="space-y-2 mb-4">
        @foreach($order->items as $item)
            <div class="flex justify-between text-xs">
                <span>{{ $item->product->name }} x {{ $item->qty }}</span>
                <span>Rp {{ number_format($item->subtotal,0,',','.') }}</span>
            </div>
        @endforeach
    </div>

    <div class="border-t pt-2 text-xs mb-4">
        <div class="flex justify-between font-semibold">
            <span>Total:</span>
            <span>Rp {{ number_format($order->total,0,',','.') }}</span>
        </div>
        <div class="flex justify-between">
            <span>Bayar:</span>
            <span>Rp {{ number_format($order->uang_dibayar,0,',','.') }}</span>
        </div>
        <div class="flex justify-between">
            <span>Kembalian:</span>
            <span>Rp {{ number_format($order->kembalian,0,',','.') }}</span>
        </div>
    </div>

    <div class="flex gap-2">
        <button wire:click="printReceipt" class="w-1/2 bg-blue-600 text-white p-2 rounded text-xs">Cetak Struk</button>
        <button wire:click="backToPOS" class="w-1/2 bg-gray-300 text-black p-2 rounded text-xs">Kembali</button>
    </div>
</section>

<script>
    window.addEventListener('printReceipt', () => {
        window.print();
    });
</script>
