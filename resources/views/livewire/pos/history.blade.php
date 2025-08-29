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
            icon="magnifying-glass" 
        />
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
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- ✅ Modal: Background luar TETAP TRANSPARAN -->
    @if($showPaymentModal && $selectedOrder)
        <div class="fixed inset-0 z-50 flex items-center justify-center" wire:click.self="closeModal">
            <!-- ❌ Tidak ada overlay hitam -->
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
                        <flux:input 
                            wire:model.live="uangDibayar" 
                            type="number" 
                            placeholder="15000" 
                            :min="(float)$selectedOrder->total"
                            step="1"
                            class="w-full" 
                        />
                        @error('uangDibayar')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    @php
                        $uangDibayarFloat = (float)($uangDibayar ?? 0);
                        $total = (float)$selectedOrder->total;
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

    <!-- ✅ STRUK: Hanya muncul saat print, termasuk di PDF -->
    @if($selectedOrder)
        <div id="print-receipt" class="print-only">
            <div class="receipt-content">
                <!-- Header -->
                <div class="text-center mb-2">
                    <h2 class="text-xl font-bold">EssyCoff</h2>
                    <p class="text-xs text-gray-600">Jl. Jati No.41, Padang Jati, Kota Bengkulu</p>
                </div>

                <hr class="my-2 border-dashed border-gray-400">

                <!-- Info Transaksi -->
                <div class="text-xs space-y-1 mb-2">
                    <p><strong>No.:</strong> {{ $selectedOrder->no_order }}</p>
                    <p><strong>Kasir:</strong> {{ $selectedOrder->user?->name ?? '-' }}</p>
                    <p><strong>Tanggal:</strong> {{ $selectedOrder->created_at->format('d/m/Y H:i') }}</p>
                    @if($selectedOrder->customer_name)
                        <p><strong>Customer:</strong> {{ $selectedOrder->customer_name }}</p>
                    @endif
                </div>

                <hr class="my-2 border-dashed border-gray-400">

                <!-- Daftar Produk -->
                <div class="space-y-1 mb-2">
                    @foreach ($selectedOrder->items as $item)
                        <div class="flex justify-between">
                            <span class="truncate max-w-[140px]">
                                {{ Str::limit($item->product?->name, 18) }} × {{ $item->qty }}
                            </span>
                            <span>Rp {{ number_format((float)$item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <hr class="my-2 border-dashed border-gray-400">

                <!-- Ringkasan -->
                <div class="space-y-1 font-semibold">
                    <div class="flex justify-between">
                        <span>Total</span>
                        <span>Rp {{ number_format((float)$selectedOrder->total, 0, ',', '.') }}</span>
                    </div>
                    @php
                        $uangDibayarFloat = (float)($uangDibayar ?? 0);
                        $total = (float)$selectedOrder->total;
                        $kembalian = $uangDibayarFloat - $total;
                    @endphp
                    @if($uangDibayarFloat > 0)
                    <div class="flex justify-between">
                        <span>Tunai</span>
                        <span>Rp {{ number_format($uangDibayarFloat, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($kembalian >= 0 && $uangDibayarFloat > 0)
                    <div class="flex justify-between">
                        <span>Kembali</span>
                        <span>Rp {{ number_format($kembalian, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="text-center mt-4">
                    <p class="font-medium">Terima Kasih!</p>
                    <p class="text-gray-600 text-xs">~ EssyCoff ~</p>
                </div>
            </div>
        </div>
    @endif

    <!-- ✅ CSS UNTUK CETAK (termasuk PDF) -->
    <style>
        .print-only {
            display: none;
        }

        @media print {
            /* Hide everything */
            body * {
                visibility: hidden;
            }
            
            /* Show only receipt */
            .print-only, .print-only * {
                visibility: visible;
            }
            
            .print-only {
                display: block !important;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: white;
                z-index: 9999;
            }
            
            .receipt-content {
                width: 80mm;
                max-width: 80mm;
                margin: 0 auto;
                padding: 10mm;
                font-family: 'Courier New', monospace;
                font-size: 12px;
                line-height: 1.4;
                color: black;
                background: white;
            }
            
            .receipt-content .text-xl {
                font-size: 16px;
            }
            
            .receipt-content .text-xs {
                font-size: 10px;
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
            
            .receipt-content .font-medium {
                font-weight: 500;
            }
            
            .receipt-content .font-semibold {
                font-weight: 600;
            }
            
            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>

    <!-- ✅ JS: Cetak otomatis setelah pembayaran -->
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