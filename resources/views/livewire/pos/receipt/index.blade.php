<section id="struk" class="w-full max-w-md mx-auto mt-6 p-6 border rounded-xl bg-zinc-50 dark:bg-zinc-900/60 font-mono text-sm shadow-md">
   <!-- Header -->
    <div class="text-center mb-3">
        <h2 class="text-2xl font-extrabold tracking-wide">EssyCoff</h2>
        <p class="text-xs">Jl. Jati No.41, Padang Jati, Kec. Ratu Samban, Kota Bengkulu, Bengkulu</p>
    </div>

    <!-- Info Transaksi -->
    <div class="mb-3 text-xs space-y-0.5">
        <p>No.: <span class="font-semibold">{{ $order->no_order }}</span></p>
        <p>Kasir: <span class="font-semibold">{{ $order->user->name ?? '-' }}</span></p>
        <p>Tanggal: {{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <hr class="my-2 border-dashed">

    <!-- Items -->
    <div class="space-y-1.5 mb-3">
        @foreach($order->items as $item)
            <div class="line">
                <span class="truncate pr-2">{{ $item->product->name }} × {{ $item->qty }}</span>
                <span class="amount tabular-nums">Rp {{ number_format($item->subtotal,0,',','.') }}</span>
            </div>
        @endforeach
    </div>

    <hr class="my-2 border-dashed">

    <!-- Summary -->
    <div class="space-y-1">
        <div class="line font-bold text-gray-900 dark:text-white">
            <span>Total</span>
            <span class="amount tabular-nums">Rp {{ number_format($order->total,0,',','.') }}</span>
        </div>
        <div class="line">
            <span>Tunai</span>
            <span class="amount tabular-nums">Rp {{ number_format($order->uang_dibayar,0,',','.') }}</span>
        </div>
        <div class="line">
            <span>Kembali</span>
            <span class="amount tabular-nums">Rp {{ number_format($order->kembalian,0,',','.') }}</span>
        </div>
    </div>

    <!-- Footer -->
    <div class="thankyou text-center text-xs mt-4 hidden">
        <p>Terima Kasih</p>
        <p>~ EssyCoff ~</p>
    </div>

    <!-- Buttons -->
    <div class="w-full mt-4 flex gap-2 print:hidden">
        <flux:button 
            wire:click="backToPOS" 
            variant="outline" 
            icon="arrow-left"
            class="w-1/2 text-xs">
            Kembali
        </flux:button>

        <flux:button 
            wire:click="printReceipt" 
            variant="primary" 
            icon="printer"
            class="w-1/2 text-xs">
            Print
        </flux:button>
    </div>

    <style>
        #struk .line { display: flex; justify-content: space-between; align-items: baseline; }
        #struk .amount { min-width: 110px; text-align: right; }
        @media print {
            body { margin: 0; }
            body * { visibility: hidden; }
            #struk, #struk * { visibility: visible; }
            #struk { position: absolute; left: 0; right: 0; margin: 0 auto; top: 0; width: 80mm; }
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