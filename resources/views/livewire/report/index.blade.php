<section class="w-full">
    <x-page-heading>
        <x-slot:title>Report Transaksi</x-slot:title>
        <x-slot:description>Filter, cetak, atau export laporan transaksi POS</x-slot:description>
    </x-page-heading>

    <x-card>
        <x-slot:title>Data Transaksi</x-slot:title>
        <x-slot:description>Berisi laporan transaksi POS sesuai filter</x-slot:description>

        <div class="flex flex-wrap gap-4 mb-4">
            <flux:input wire:model="startDate" type="date" label="Dari Tanggal" />
            <flux:input wire:model="endDate" type="date" label="Sampai Tanggal" />

            <flux:button wire:click="$refresh" variant="primary">Filter</flux:button>
            <flux:button wire:click="exportExcel" variant="ghost">Export Excel</flux:button>
            <flux:button wire:click="exportPDF" variant="ghost">Export PDF</flux:button>
            <flux:button onclick="window.print()" variant="ghost">Print</flux:button>
        </div>

        <div class="flex gap-4 mb-4">
            <div class="font-bold text-green-600 dark:text-green-400">
                Total Hari Ini: Rp {{ number_format($dailyTotal,0,',','.') }}
            </div>
            <div class="font-bold text-blue-600 dark:text-blue-400">
                Total Bulan Ini: Rp {{ number_format($monthlyTotal,0,',','.') }}
            </div>
        </div>

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
                </x-table.row>
            </x-slot:head>

            <x-slot:body>
                @forelse($orders as $index => $order)
                    <x-table.row>
                        <x-table.cell>{{ $index + 1 }}</x-table.cell>
                        <x-table.cell>{{ $order->no_order }}</x-table.cell>
                        <x-table.cell>{{ $order->created_at->format('d/m/Y H:i') }}</x-table.cell>
                        <x-table.cell>{{ $order->user?->name ?? '-' }}</x-table.cell>
                        <x-table.cell>Rp {{ number_format($order->total,0,',','.') }}</x-table.cell>
                        <x-table.cell>Rp {{ number_format($order->uang_dibayar,0,',','.') }}</x-table.cell>
                        <x-table.cell>Rp {{ number_format($order->kembalian,0,',','.') }}</x-table.cell>
                        <x-table.cell>{{ ucfirst($order->source) }}</x-table.cell>
                    </x-table.row>
                @empty
                    <x-table.row>
                        <x-table.cell colspan="8" class="text-center text-gray-500">Belum ada transaksi.</x-table.cell>
                    </x-table.row>
                @endforelse
            </x-slot:body>
        </x-table>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </x-card>
</section>
