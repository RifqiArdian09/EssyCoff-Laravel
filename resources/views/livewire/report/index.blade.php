<div class="p-6 space-y-8 bg-white dark:bg-zinc-800 min-h-screen text-gray-900 dark:text-white">
    <!-- Judul -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laporan Transaksi</h1>
        <p class="text-gray-600 dark:text-zinc-300">Filter, cetak, atau ekspor laporan transaksi POS</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white dark:bg-zinc-800 p-6 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700 space-y-6 transition-colors duration-200">
        <!-- Filter Inputs -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Month Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Filter Bulan</label>
                <flux:select wire:model.live="selectedMonth" class="w-full">
                    <option value="">Pilih Bulan</option>
                    @foreach($availableMonths as $month)
                        <option value="{{ $month['value'] }}">{{ $month['label'] }}</option>
                    @endforeach
                </flux:select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Dari Tanggal</label>
                <input
                    type="date"
                    wire:model.live="from"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Sampai Tanggal</label>
                <input
                    type="date"
                    wire:model.live="to"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600">
            </div>
        </div>

        <!-- Export Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200 dark:border-zinc-700">
            <div class="flex gap-3">
                <flux:button icon="arrow-down-tray" wire:click="exportExcel" variant="primary" class="flex-1 sm:flex-none">
                    Export Excel
                </flux:button>
                <flux:button icon="arrow-down-tray" wire:click="exportPDF" variant="outline" class="flex-1 sm:flex-none">
                    Export PDF
                </flux:button>
            </div>
        </div>
        
        <!-- Summary Info -->
        @if($selectedMonth)
        <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-700">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="text-sm font-medium text-blue-700 dark:text-blue-300">
                    Menampilkan laporan bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->locale('id')->translatedFormat('F Y') }}
                </span>
            </div>
        </div>
        @elseif($from || $to)
        <div class="mt-4 p-3 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg border border-emerald-200 dark:border-emerald-700">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="text-sm font-medium text-emerald-700 dark:text-emerald-300">
                    Menampilkan laporan dari 
                    @if($from && $to)
                        {{ \Carbon\Carbon::parse($from)->locale('id')->translatedFormat('d F Y') }} 
                        sampai {{ \Carbon\Carbon::parse($to)->locale('id')->translatedFormat('d F Y') }}
                    @elseif($from)
                        {{ \Carbon\Carbon::parse($from)->locale('id')->translatedFormat('d F Y') }} 
                        sampai {{ now()->format('d F Y') }}
                    @else
                        {{ now()->startOfMonth()->locale('id')->translatedFormat('d F Y') }} 
                        sampai {{ \Carbon\Carbon::parse($to)->locale('id')->translatedFormat('d F Y') }}
                    @endif
                </span>
            </div>
        </div>
        @endif
    </div>

    <!-- Statistik Pendapatan -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Pendapatan Hari Ini -->
        <div class="bg-emerald-50 dark:bg-emerald-900/30 p-5 rounded-lg shadow border border-emerald-100 dark:border-emerald-800">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-sm font-medium text-emerald-700 dark:text-emerald-300">Pendapatan Hari Ini</h3>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                        Rp {{ number_format($dailyTotal ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-emerald-100 dark:bg-emerald-800/50 rounded-lg p-3">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pendapatan Bulan Ini -->
        <div class="bg-blue-50 dark:bg-blue-900/30 p-5 rounded-lg shadow border border-blue-100 dark:border-blue-800">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-sm font-medium text-blue-700 dark:text-blue-300">Pendapatan Bulan Ini</h3>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                        Rp {{ number_format($monthlyTotal ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-800/50 rounded-lg p-3">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
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
                        <th class="px-4 py-3">Tanggal</th>
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
                            {{ $orders->firstItem() + $loop->index }}
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
            <div class="flex flex-col md:flex-row items-center justify-between gap-3">
                <p class="text-sm text-gray-600 dark:text-zinc-400">
                    Menampilkan
                    <span class="font-medium text-gray-900 dark:text-white">{{ $orders->firstItem() }}</span>
                    –
                    <span class="font-medium text-gray-900 dark:text-white">{{ $orders->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $orders->total() }}</span>
                    data
                </p>

                <div class="[&>nav]:flex [&>nav]:items-center [&>nav]:gap-1">
                    {{ $orders->links('components.pagination.simple-arrows') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>