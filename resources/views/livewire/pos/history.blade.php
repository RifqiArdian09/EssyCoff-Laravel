<div class="p-6 space-y-8 bg-white dark:bg-zinc-800 min-h-screen text-gray-900 dark:text-white">
    <!-- Judul -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Riwayat Transaksi</h1>
            <p class="text-gray-600 dark:text-zinc-300">Lihat dan kelola semua transaksi penjualan</p>
        </div>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
    <div class="px-4 py-3 rounded-lg bg-emerald-50 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-100 border border-emerald-200 dark:border-emerald-700 flex items-center gap-2 shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span>{{ session('message') }}</span>
    </div>
    @endif

    <!-- Search Bar -->
    <div class="bg-white dark:bg-zinc-800 p-5 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700 space-y-4 transition-colors duration-200">
        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Cari Transaksi</label>
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Cari no. order, customer..."
            class="w-full"
            icon="magnifying-glass" />
    </div>

    <!-- Tabel Transaksi -->
    <div class="bg-white dark:bg-zinc-800 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-zinc-700 transition-colors duration-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700 dark:text-zinc-200">
                <thead class="bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">No Order</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Kasir</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse ($orders as $index => $order)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition duration-150 ease-in-out">
                        <td class="px-4 py-3 font-medium text-gray-500 dark:text-zinc-400">
                            {{ ($orders->currentPage() - 1) * $orders->perPage() + $index + 1 }}
                        </td>
                        <td class="px-4 py-3 font-mono text-sm text-gray-900 dark:text-white">
                            {{ $order->no_order }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-gray-900 dark:text-white">
                                {{ $order->customer_name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-zinc-400">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-zinc-400">
                            {{ $order->user?->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-emerald-600 dark:text-emerald-400 text-right">
                            Rp {{ number_format((float)$order->total, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($order->status === 'pending_payment')
                            <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 rounded-full text-xs font-medium">
                                Pending Payment
                            </span>
                            @else
                            <span class="px-2 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 rounded-full text-xs font-medium">
                                Paid
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex gap-2 justify-end">
                                @if($order->status === 'pending_payment')
                                <flux:button
                                    variant="primary"
                                    size="sm"
                                    icon="credit-card"
                                    wire:click="confirmPayment({{ $order->id }})">
                                    Bayar
                                </flux:button>
                                @endif
                                <flux:button
                                    variant="primary"
                                    size="sm"
                                    icon="document-text"
                                    href="{{ route('pos.detail', $order) }}">
                                    Detail
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-zinc-500">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Belum ada transaksi.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

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

    <!-- ✅ Modal: Konfirmasi Pembayaran -->
    @if($showPaymentModal && $selectedOrder)
    <div class="fixed inset-0 z-50 flex items-center justify-center" wire:click.self="closeModal">
        <div class="relative bg-white dark:bg-zinc-800 rounded-lg shadow-xl max-w-md w-full mx-4 p-6 border border-gray-200 dark:border-zinc-700">

            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Konfirmasi Pembayaran</h3>

            <div class="space-y-4">
                <div class="bg-gray-50 dark:bg-zinc-700/60 p-4 rounded-lg space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-zinc-300">No. Order:</span>
                        <span class="font-medium">{{ $selectedOrder->no_order }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-zinc-300">Customer:</span>
                        <span class="font-medium">{{ $selectedOrder->customer_name ?? 'Umum' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-zinc-300">Total:</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">
                            Rp {{ number_format((float)$selectedOrder->total, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">
                        Uang yang Dibayar
                    </label>
                    @php
                    $minUang = (float) $selectedOrder->total;
                    @endphp

                    <flux:input
                        wire:model.live="uangDibayar"
                        type="number"
                        placeholder="15000"
                        :min="$minUang"
                        step="1"
                        class="w-full" />
                    @error('uangDibayar')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror

                    <!-- Quick Amount Buttons -->
                    @php
                    $total = (float)$selectedOrder->total;
                    $rounded50k = ceil($total / 50000) * 50000;
                    $rounded100k = ceil($total / 100000) * 100000;
                    @endphp

                    <div class="grid grid-cols-3 gap-2 mt-2">
                        <button
                            type="button"
                            wire:click="$set('uangDibayar', {{ $total }})"
                            class="px-3 py-2 text-xs bg-gray-100 dark:bg-zinc-700 rounded hover:bg-gray-200 dark:hover:bg-zinc-600 transition text-gray-800 dark:text-zinc-300 font-medium">
                            Pas
                        </button>
                        <button
                            type="button"
                            wire:click="$set('uangDibayar', {{ $rounded50k }})"
                            class="px-3 py-2 text-xs bg-blue-100 dark:bg-blue-900/40 rounded hover:bg-blue-200 dark:hover:bg-blue-800 transition text-blue-800 dark:text-blue-300 font-medium">
                            Rp {{ number_format($rounded50k, 0, ',', '.') }}
                        </button>
                        <button
                            type="button"
                            wire:click="$set('uangDibayar', {{ $rounded100k }})"
                            class="px-3 py-2 text-xs bg-emerald-100 dark:bg-emerald-900/40 rounded hover:bg-emerald-200 dark:hover:bg-emerald-800 transition text-emerald-800 dark:text-emerald-300 font-medium">
                            Rp {{ number_format($rounded100k, 0, ',', '.') }}
                        </button>
                    </div>
                </div>

                @php
                $uangDibayarFloat = (float)($uangDibayar ?? 0);
                $kembalian = $uangDibayarFloat - $total;
                @endphp

                @if($uangDibayarFloat >= $total)
                <div class="bg-blue-50 dark:bg-blue-900/30 p-3 rounded-lg">
                    <div class="flex justify-between text-sm">
                        <span class="text-blue-700 dark:text-blue-300">Kembalian:</span>
                        <span class="font-bold text-blue-700 dark:text-blue-300">
                            Rp {{ number_format($kembalian, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                @endif
            </div>

            <div class="flex gap-3 mt-6">
                <flux:button variant="ghost" wire:click="closeModal" class="flex-1">
                    Batal
                </flux:button>
                <flux:button
                    variant="primary"
                    wire:click="processPayment"
                    :disabled="!$uangDibayar || $uangDibayar < $total"
                    class="flex-1">
                    Konfirmasi & Cetak
                </flux:button>
            </div>
        </div>
    </div>
    @endif

    <!-- STRUK (Hanya muncul saat dicetak) -->
    <div id="struk" class="print-only">
        <div class="receipt-content">
            <div class="text-center mb-3">
                <h2 class="text-xl font-bold">EssyCoff</h2>
                <p class="text-xs text-gray-600">Jl. Jati No.41, Padang Jati, Kota Bengkulu</p>
            </div>

            <hr class="my-2 border-dashed border-gray-400">

            <div class="space-y-1.5 mb-3 text-xs text-center"> <!-- 👈 text-center -->
                <p><strong>No.:</strong> {{ $selectedOrder?->no_order }}</p>
                <p><strong>Kasir:</strong> {{ $selectedOrder?->user?->name ?? '-' }}</p>
                <p><strong>Tanggal:</strong> {{ $selectedOrder?->created_at?->format('d M, H:i') }}</p>
            </div>

            <hr class="my-2 border-dashed border-gray-400">

            <!-- Detail Item: tetap kiri-kanan, tapi container di tengah -->
            <div class="space-y-1 font-mono text-xs">
                @foreach($selectedOrder?->items ?? [] as $item)
                <div class="flex justify-between">
                    <span class="flex-1 text-left">{{ Str::limit($item->product?->name, 15) }} × {{ $item->qty }}</span>
                    <span class="flex-1 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>

            <hr class="my-2 border-dashed border-gray-400">

            <div class="space-y-1 font-semibold text-center"> <!-- 👈 text-center -->
                <div class="flex justify-between">
                    <span class="text-left">Total</span>
                    <span class="text-right">Rp {{ number_format($selectedOrder?->total, 0, ',', '.') }}</span>
                </div>
                @if($selectedOrder?->uang_dibayar)
                <div class="flex justify-between">
                    <span class="text-left">Tunai</span>
                    <span class="text-right">Rp {{ number_format($selectedOrder?->uang_dibayar, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($selectedOrder?->kembalian !== null)
                <div class="flex justify-between">
                    <span class="text-left">Kembali</span>
                    <span class="text-right">Rp {{ number_format($selectedOrder?->kembalian, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>

            <div class="text-center mt-4">
                <p class="font-medium">Terima Kasih!</p>
                <p class="text-gray-600">~ EssyCoff ~</p>
            </div>
        </div>
    </div>

    <!-- CSS untuk cetak -->
    <style>
        .print-only {
            display: none;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            .print-only,
            .print-only * {
                visibility: visible;
            }

            .print-only {
                display: block !important;
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                background: white;
                color: black;
                z-index: 9999;
                box-sizing: border-box;
                font-family: 'Courier New', monospace;
            }

            .receipt-content {
                width: 80mm;
                max-width: 80mm;
                margin: 10px auto;
                /* 👈 Pusatkan di tengah halaman */
                padding: 5mm;
                background: white;
                color: black;
                font-size: 12px;
                line-height: 1.6;
                box-shadow: none;
            }

            .receipt-content hr {
                border: none;
                border-top: 1px dashed #000;
                margin: 8px 0;
            }

            .receipt-content .flex {
                display: flex;
            }

            .receipt-content .justify-between {
                justify-content: space-between;
            }

            .receipt-content .text-center {
                text-align: center;
            }

            .receipt-content .font-bold {
                font-weight: bold;
            }

            .receipt-content .font-semibold {
                font-weight: 600;
            }

            @page {
                size: 80mm auto;
                margin: 0;
                /* Optional: atur potongan kertas */
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>

    <!-- Script: Trigger print saat event -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('printReceipt', () => {
                setTimeout(() => {
                    window.print();
                }, 300);
            });
        });
    </script>
</div>