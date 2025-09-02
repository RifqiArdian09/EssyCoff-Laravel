<div class="p-6 space-y-8 bg-white dark:bg-zinc-800 min-h-screen text-gray-900 dark:text-white">
    <!-- Judul -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laporan Transaksi</h1>
        <p class="text-gray-600 dark:text-zinc-300">Filter, cetak, atau ekspor laporan transaksi POS</p>
    </div>

    <!-- Filter Tanggal -->
    <div class="bg-white dark:bg-zinc-800 p-5 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700 space-y-4 transition-colors duration-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Dari Tanggal</label>
                <input 
                    type="date" 
                    wire:model.live="from"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Sampai Tanggal</label>
                <input 
                    type="date" 
                    wire:model.live="to"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600"
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

    <!-- Statistik Pendapatan -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Pendapatan Hari Ini -->
        <div class="bg-emerald-50 dark:bg-emerald-900/30 p-5 rounded-lg shadow border border-emerald-100 dark:border-emerald-800">
            <h3 class="text-sm font-medium text-emerald-700 dark:text-emerald-300">Pendapatan Hari Ini</h3>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                Rp {{ number_format($dailyTotal ?? 0, 0, ',', '.') }}
            </p>
        </div>

        <!-- Pendapatan Bulan Ini -->
        <div class="bg-blue-50 dark:bg-blue-900/30 p-5 rounded-lg shadow border border-blue-100 dark:border-blue-800">
            <h3 class="text-sm font-medium text-blue-700 dark:text-blue-300">Pendapatan Bulan Ini</h3>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                Rp {{ number_format($monthlyTotal ?? 0, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="bg-white dark:bg-zinc-800 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-zinc-700 transition-colors duration-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700 dark:text-zinc-200">
                <thead class="bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">No Order</th>
                        <th class="px-4 py-3">TanggaI</th>
                        <th class="px-4 py-3">Kasir</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-right">Bayar</th>
                        <th class="px-4 py-3 text-right">Kembali</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition duration-150 ease-in-out">
                            <td class="px-4 py-3 font-medium text-gray-500 dark:text-zinc-400">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-4 py-3 font-mono text-sm text-gray-900 dark:text-white">
                                {{ $order->no_order }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-zinc-400">
                                {{ $order->created_at->format('d M, H:i') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                {{ $order->user?->name ?? 'Sistem' }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-emerald-600 dark:text-emerald-400 text-right">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-zinc-300 text-right">
                                Rp {{ number_format($order->uang_dibayar, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-blue-600 dark:text-blue-400 text-right">
                                Rp {{ number_format($order->kembalian, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-zinc-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span>Tidak ada transaksi ditemukan.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="p-4 bg-gray-50 dark:bg-zinc-800 border-t border-gray-200 dark:border-zinc-700 transition-colors duration-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>