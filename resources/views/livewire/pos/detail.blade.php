<div class="p-6 space-y-8 bg-white dark:bg-zinc-800 min-h-screen text-gray-900 dark:text-white">
    <!-- Judul -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Transaksi: {{ $order->no_order }}</h1>
    </div>

    <!-- Informasi Transaksi -->
    <div class="bg-white dark:bg-zinc-900 p-5 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Transaksi</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-600 dark:text-zinc-400">Tanggal</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-zinc-400">Kasir</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $order->user?->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-zinc-400">Status</p>
                <p class="font-medium text-emerald-600 dark:text-emerald-400">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</p>
            </div>
        </div>
    </div>

    <!-- Daftar Produk -->
    <div class="bg-white dark:bg-zinc-900 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-zinc-700">
        <div class="p-5 border-b border-gray-200 dark:border-zinc-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Produk</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700 dark:text-zinc-200">
                <thead class="bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-4 py-3">Produk</th>
                        <th class="px-4 py-3 text-center">Qty</th>
                        <th class="px-4 py-3 text-right">Harga</th>
                        <th class="px-4 py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                    @foreach($order->items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition duration-100">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                {{ $item->product?->name ?? 'Produk dihapus' }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-zinc-300">{{ $item->qty }}</td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-zinc-300">
                                Rp {{ number_format($item->harga, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Ringkasan Pembayaran -->
    <div class="bg-white dark:bg-zinc-900 p-5 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ringkasan Pembayaran</h2>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-zinc-300">Total</span>
                <span class="text-xl font-bold text-gray-900 dark:text-white">
                    Rp {{ number_format($order->total, 0, ',', '.') }}
                </span>
            </div>

            @if($order->uang_dibayar)
                <div class="flex justify-between pt-1 border-t border-gray-200 dark:border-zinc-700">
                    <span class="text-gray-600 dark:text-zinc-300">Tunai</span>
                    <span class="font-semibold text-gray-900 dark:text-white">
                        Rp {{ number_format($order->uang_dibayar, 0, ',', '.') }}
                    </span>
                </div>
            @endif

            @if($order->kembalian !== null)
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-zinc-300">Kembalian</span>
                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                        Rp {{ number_format($order->kembalian, 0, ',', '.') }}
                    </span>
                </div>
            @endif
        </div>
    </div>

    <!-- Tombol Aksi -->
    <div class="flex flex-wrap gap-3">
        <flux:button 
            variant="outline" 
            icon="arrow-left"
            href="{{ route('pos.history') }}">
            Kembali
        </flux:button>

        <!-- Tombol Cetak Ulang Struk -->
        <flux:button 
            variant="primary" 
            icon="printer"
            wire:click="printReceipt">
            Cetak Ulang Struk
        </flux:button>
    </div>

    <!-- STRUK (Hanya muncul saat dicetak, tidak terlihat di layar) -->
    <div id="struk" class="print-only">
        <div class="receipt-content">
            <div class="text-center mb-3">
                <h2 class="text-xl font-bold">EssyCoff</h2>
                <p class="text-xs text-gray-600">Jl. Jati No.41, Padang Jati, Kota Bengkulu</p>
            </div>

            <hr class="my-2 border-dashed border-gray-400">

            <div class="space-y-1.5 mb-3 text-xs">
                <p><strong>No.:</strong> {{ $order->no_order }}</p>
                <p><strong>Kasir:</strong> {{ $order->user?->name ?? '-' }}</p>
                <p><strong>Tanggal:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <hr class="my-2 border-dashed border-gray-400">

            <div class="space-y-1">
                @foreach($order->items as $item)
                    <div class="flex justify-between">
                        <span>{{ Str::limit($item->product?->name, 15) }} × {{ $item->qty }}</span>
                        <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <hr class="my-2 border-dashed border-gray-400">

            <div class="space-y-1 font-semibold">
                <div class="flex justify-between">
                    <span>Total</span>
                    <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
                @if($order->uang_dibayar)
                <div class="flex justify-between">
                    <span>Tunai</span>
                    <span>Rp {{ number_format($order->uang_dibayar, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($order->kembalian !== null)
                <div class="flex justify-between">
                    <span>Kembali</span>
                    <span>Rp {{ number_format($order->kembalian, 0, ',', '.') }}</span>
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

    <!-- Script: Trigger print saat event -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('printReceipt', () => {
                window.print();
            });
        });
    </script>
</div>