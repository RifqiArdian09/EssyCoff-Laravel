<section id="struk" class="w-full max-w-md mx-auto mt-6 p-5 border rounded-lg bg-zinc-50 dark:bg-zinc-900/50 font-mono text-base">
    <div class="text-center mb-2">
        <h2 class="text-xl font-bold">EssyCoff</h2>
        <p class="text-xs text-gray-500">{{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <h3 class="text-sm font-semibold mb-1">Detail Transaksi</h3>
    <p class="text-xs mb-1">No. Order: {{ $order->no_order }}</p>
    <p class="text-xs mb-3">Customer: {{ $order->customer_name ?: '-' }}</p>

    <hr class="my-2 border-dashed">

    <div class="space-y-1.5 mb-3">
        @foreach($order->items as $item)
            <div class="line">
                <span class="truncate pr-2">{{ $item->product->name }} x {{ $item->qty }}</span>
                <span class="amount tabular-nums">Rp {{ number_format($item->subtotal,0,',','.') }}</span>
            </div>
        @endforeach
    </div>

    <hr class="my-2 border-dashed">

    <div class="space-y-1">
        <div class="line font-semibold">
            <span>Total</span>
            <span class="amount tabular-nums">Rp {{ number_format($order->total,0,',','.') }}</span>
        </div>
        <div class="line">
            <span>Bayar</span>
            <span class="amount tabular-nums">Rp {{ number_format($order->uang_dibayar,0,',','.') }}</span>
        </div>
        <div class="line">
            <span>Kembali</span>
            <span class="amount tabular-nums">Rp {{ number_format($order->kembalian,0,',','.') }}</span>
        </div>
    </div>

    <div class="thankyou text-center text-xs mt-3">
        <p>Terima Kasih 🙏</p>
        <p>~ EssyCoff ~</p>
    </div>

    <div class="w-full mt-3 flex gap-2 print:hidden">
        <button wire:click="backToPOS" class="w-1/2 bg-gray-300 text-black p-2 rounded text-xs">Kembali</button>
        <button wire:click="printReceipt" class="w-1/2 bg-blue-600 text-white p-2 rounded text-xs">Print</button>
    </div>

    <style>
        #struk .line { display: flex; justify-content: space-between; align-items: baseline; }
        #struk .amount { min-width: 110px; text-align: right; }
        .thankyou { display: none; }
        @media print {
            body { margin: 0; }
            body * { visibility: hidden; }
            #struk, #struk * { visibility: visible; }
            #struk { position: absolute; left: 0; right: 0; margin: 0 auto; top: 0; width: 90mm; }
            .print\:hidden { display: none !important; }
            .thankyou { display: block; }
        }
        @page { size: auto; margin: 6mm; }
    </style>

    <script>
        window.addEventListener('printReceipt', () => {
            window.print();
        });
    </script>
</section>
