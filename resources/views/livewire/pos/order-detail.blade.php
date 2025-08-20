<section class="w-full p-4">
    <x-card>
        <x-slot:title>Detail Transaksi: {{ $order->no_order }}</x-slot:title>
        <x-slot:description>Informasi lengkap transaksi POS</x-slot:description>

        <div class="mb-4">
            <p><strong>Tanggal:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Kasir:</strong> {{ $order->user ? $order->user->name : '-' }}</p>
            <p><strong>Sumber:</strong> {{ ucfirst($order->source) }}</p>
        </div>

        <x-table>
            <x-slot:head>
                <x-table.row>
                    <x-table.heading>Produk</x-table.heading>
                    <x-table.heading>Qty</x-table.heading>
                    <x-table.heading>Harga</x-table.heading>
                    <x-table.heading>Subtotal</x-table.heading>
                </x-table.row>
            </x-slot:head>

            <x-slot:body>
                @foreach($order->items as $item)
                    <x-table.row>
                        <x-table.cell>{{ $item->product->name }}</x-table.cell>
                        <x-table.cell>{{ $item->qty }}</x-table.cell>
                        <x-table.cell>Rp {{ number_format($item->harga,0,',','.') }}</x-table.cell>
                        <x-table.cell>Rp {{ number_format($item->subtotal,0,',','.') }}</x-table.cell>
                    </x-table.row>
                @endforeach
            </x-slot:body>

            <x-slot:footer>
                <x-table.row>
                    <x-table.cell colspan="3" class="text-right font-bold">Total</x-table.cell>
                    <x-table.cell class="font-bold">Rp {{ number_format($order->total,0,',','.') }}</x-table.cell>
                </x-table.row>
                <x-table.row>
                    <x-table.cell colspan="3" class="text-right font-bold">Uang Dibayar</x-table.cell>
                    <x-table.cell class="font-bold">Rp {{ number_format($order->uang_dibayar,0,',','.') }}</x-table.cell>
                </x-table.row>
                <x-table.row>
                    <x-table.cell colspan="3" class="text-right font-bold">Kembalian</x-table.cell>
                    <x-table.cell class="font-bold">Rp {{ number_format($order->kembalian,0,',','.') }}</x-table.cell>
                </x-table.row>
            </x-slot:footer>
        </x-table>

        <div class="mt-4">
            <a href="{{ route('pos.history') }}"
               class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
               Kembali ke Riwayat
            </a>
        </div>
    </x-card>
</section>
