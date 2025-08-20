<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <!-- Summary Cards -->
        <div class="grid gap-4 md:grid-cols-3">
            <!-- Total Pendapatan -->
            <x-card class="flex items-center justify-between p-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pendapatan</h3>
                    <p class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">
                        Rp {{ number_format($totalRevenue,0,',','.') }}
                    </p>
                </div>
                <flux:icon icon="currency-dollar" class="w-8 h-8 text-green-600 dark:text-green-400" />
            </x-card>

            <!-- Total Transaksi -->
            <x-card class="flex items-center justify-between p-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Transaksi</h3>
                    <p class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ $totalOrders }}
                    </p>
                </div>
                <flux:icon icon="shopping-cart" class="w-8 h-8 text-blue-600 dark:text-blue-400" />
            </x-card>

            <!-- Total Produk -->
            <x-card class="flex items-center justify-between p-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Produk</h3>
                    <p class="mt-1 text-2xl font-bold text-purple-600 dark:text-purple-400">
                        {{ $totalProducts }}
                    </p>
                </div>
                <flux:icon icon="archive-box" class="w-8 h-8 text-purple-600 dark:text-purple-400" />
            </x-card>
        </div>

        <!-- Riwayat Transaksi Terbaru -->
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
            <h3 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-200">Riwayat Transaksi Terbaru</h3>
            <x-table>
                <x-slot:head>
                    <x-table.row>
                        <x-table.heading>#</x-table.heading>
                        <x-table.heading>No Order</x-table.heading>
                        <x-table.heading>Tanggal</x-table.heading>
                        <x-table.heading>Total</x-table.heading>
                        <x-table.heading>Kasir</x-table.heading>
                    </x-table.row>
                </x-slot:head>
                <x-slot:body>
                    @forelse($latestOrders as $index => $order)
                        <x-table.row>
                            <x-table.cell>{{ $index + 1 }}</x-table.cell>
                            <x-table.cell>{{ $order->no_order }}</x-table.cell>
                            <x-table.cell>{{ $order->created_at->format('d/m/Y H:i') }}</x-table.cell>
                            <x-table.cell>Rp {{ number_format($order->total,0,',','.') }}</x-table.cell>
                            <x-table.cell>{{ $order->user?->name ?? '-' }}</x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell colspan="5" class="text-center text-gray-500">Belum ada transaksi.</x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-slot:body>
            </x-table>
        </div>
    </div>
</x-layouts.app>
