<section class="w-full">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-200">Riwayat Transaksi</h1>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 px-4 py-2 rounded bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
            {{ session('message') }}
        </div>
    @endif

    <x-table>
        <x-slot:head>
            <x-table.row>
                <x-table.heading>#</x-table.heading>
                <x-table.heading>No Order</x-table.heading>
                <x-table.heading>Tanggal</x-table.heading>
                <x-table.heading>Kasir</x-table.heading>
                <x-table.heading>Total</x-table.heading>
                <x-table.heading>Uang Dibayar</x-table.heading>
                <x-table.heading>Kembalian</x-table.heading>
                <x-table.heading>Sumber</x-table.heading>
                <x-table.heading>Detail</x-table.heading>
            </x-table.row>
        </x-slot:head>

        <x-slot:body>
            @forelse ($orders as $index => $order)
                <x-table.row>
                    <x-table.cell>{{ $index + 1 }}</x-table.cell>
                    <x-table.cell>{{ $order->no_order }}</x-table.cell>
                    <x-table.cell>{{ $order->created_at->format('d/m/Y H:i') }}</x-table.cell>
                    <x-table.cell>{{ $order->user ? $order->user->name : '-' }}</x-table.cell>
                    <x-table.cell>Rp {{ number_format($order->total,0,',','.') }}</x-table.cell>
                    <x-table.cell>Rp {{ number_format($order->uang_dibayar,0,',','.') }}</x-table.cell>
                    <x-table.cell>Rp {{ number_format($order->kembalian,0,',','.') }}</x-table.cell>
                    <x-table.cell>{{ ucfirst($order->source) }}</x-table.cell>
                    <x-table.cell>
                        <a href="{{ route('pos.order.detail', $order->id) }}"
                           class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-sm">
                            Detail
                        </a>
                    </x-table.cell>
                </x-table.row>
            @empty
                <x-table.row>
                    <x-table.cell colspan="9" class="text-center text-gray-500 dark:text-gray-400">
                        Belum ada transaksi.
                    </x-table.cell>
                </x-table.row>
            @endforelse
        </x-slot:body>
    </x-table>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</section>
