<div class="p-6 space-y-8 bg-zinc-800 min-h-screen text-white">
    <!-- Judul -->
    <div>
        <h1 class="text-2xl font-bold text-white">Report Transaksi</h1>
        <p class="text-zinc-300">Filter, cetak, atau export laporan transaksi POS</p>
    </div>

    <!-- Filter Tanggal -->
    <div class="bg-zinc-900 p-5 rounded-lg shadow-lg border border-zinc-700 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1">Dari Tanggal</label>
                <input 
                    type="date" 
                    wire:model.live="from"
                    class="w-full border-zinc-600 bg-zinc-800 text-white rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1">Sampai Tanggal</label>
                <input 
                    type="date" 
                    wire:model.live="to"
                    class="w-full border-zinc-600 bg-zinc-800 text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            <div class="flex gap-2">
                <flux:button icon="arrow-down-tray" wire:click="exportExcel">
                    Export Excel
                </flux:button>
                <flux:button icon="arrow-down-tray" wire:click="exportPDF">
                    Export PDF
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="bg-zinc-900 shadow rounded-lg overflow-hidden border border-zinc-700">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-zinc-200">
                <thead class="bg-zinc-700 text-zinc-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold">No</th>
                        <th class="px-4 py-3 font-semibold">No Order</th>
                        <th class="px-4 py-3 font-semibold">Tanggal</th>
                        <th class="px-4 py-3 font-semibold">Kasir</th>
                        <th class="px-4 py-3 font-semibold">Total</th>
                        <th class="px-4 py-3 font-semibold">Bayar</th>
                        <th class="px-4 py-3 font-semibold">Kembali</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700">
                    @forelse($orders as $order)
                        <tr class="hover:bg-zinc-750 transition duration-100">
                            <td class="px-4 py-3 font-medium">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3">{{ $order->no_order }}</td>
                            <td class="px-4 py-3 text-zinc-400">{{ $order->created_at->format('d/m H:i') }}</td>
                            <td class="px-4 py-3">{{ $order->user?->name ?? 'Sistem' }}</td>
                            <td class="px-4 py-3 font-semibold text-emerald-400">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-zinc-300">
                                Rp {{ number_format($order->uang_dibayar, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-blue-400">
                                Rp {{ number_format($order->kembalian, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-zinc-500">
                                Tidak ada transaksi ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 bg-zinc-800 border-t border-zinc-700">
            {{ $orders->links() }}
        </div>
    </div>

    <!-- Statistik Pendapatan -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Pendapatan Hari Ini -->
        <div class="bg-emerald-900 text-white p-5 rounded-lg shadow-lg border border-emerald-800">
            <h3 class="text-sm font-medium opacity-90">Pendapatan Hari Ini</h3>
            <p class="text-2xl font-bold mt-1">Rp {{ number_format($dailyTotal, 0, ',', '.') }}</p>
        </div>

        <!-- Pendapatan Bulan Ini -->
        <div class="bg-blue-900 text-white p-5 rounded-lg shadow-lg border border-blue-800">
            <h3 class="text-sm font-medium opacity-90">Pendapatan Bulan Ini</h3>
            <p class="text-2xl font-bold mt-1">Rp {{ number_format($monthlyTotal, 0, ',', '.') }}</p>
        </div>
    </div>
</div>