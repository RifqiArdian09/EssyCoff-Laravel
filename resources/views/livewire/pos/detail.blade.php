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

    @php
    $width = $receiptWidth ?? '80mm'; // Default 80mm, bisa diubah jadi '58mm'
    $fontSize = $width === '58mm' ? '10px' : '12px';
    $padding = $width === '58mm' ? '8px' : '10px';
    @endphp

    <div
        id="receipt-content"
        class="hidden print:block bg-white text-black absolute left-0 top-0"
        style="width: {{ $width }}; padding: {{ $padding }}; font-family: 'Courier New', monospace; font-size: {{ $fontSize }}; line-height: 1.3;">
        <div class="receipt-layout space-y-1">
            <!-- Header -->
            <div class="text-center mb-2">
                <h2 class="font-bold text-lg" style="font-size: {{ $width === '58mm' ? '14px' : '16px' }}; margin-bottom: 4px;">
                    EssyCoff
                </h2>
                <p class="text-[9px] leading-tight">Jl. Jati No.41, Padang Jati, Kota Bengkulu</p>
                <p class="text-[9px]">Telp: (0736) 1234567</p>
            </div>

            <hr class="my-1 border-dashed border-black" style="border-top: 1px dashed #000; margin: 4px 0;">

            <!-- Order Info -->
            <div class="space-y-0.5 mb-2 text-[9px]">
                <div class="flex justify-between">
                    <span class="font-medium">No. order:</span>
                    <span>{{ $order->no_order }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Kasir:</span>
                    <span>{{ $order->user?->name ?? 'System' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Tanggal:</span>
                    <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <hr class="my-1 border-dashed border-black" style="border-top: 1px dashed #000; margin: 4px 0;">

            <!-- Items -->
            <div class="space-y-1 mb-2">
                @foreach($order->items as $item)
                <div class="flex justify-between text-[9px]" style="font-size: {{ $width === '58mm' ? '8px' : '10px' }};">
                    <div>
                        <span class="font-medium">{{ $item->product?->name ?? 'Produk dihapus' }}</span>
                        <div class="text-[8px] text-gray-600">
                            {{ $item->quantity ?? $item->qty }} × Rp {{ number_format($item->harga ?? $item->price, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="text-right">
                        <div>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                        @if($item->note)
                        <div class="text-[7px] italic">Catatan: {{ $item->note }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <hr class="my-1 border-dashed border-black" style="border-top: 1px dashed #000; margin: 4px 0;">

            <!-- Summary -->
            <div class="space-y-0.5 font-semibold text-[9px]">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
                @if($order->discount > 0)
                <div class="flex justify-between">
                    <span>Diskon</span>
                    <span class="text-red-600">- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($order->tax > 0)
                <div class="flex justify-between">
                    <span>Pajak ({{ $order->tax }}%)</span>
                    <span>Rp {{ number_format(($order->total * $order->tax) / 100, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($order->service_charge > 0)
                <div class="flex justify-between">
                    <span>Service Charge ({{ $order->service_charge }}%)</span>
                    <span>Rp {{ number_format(($order->total * $order->service_charge) / 100, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between font-bold pt-1 mt-1 border-t border-black" style="font-size: {{ $width === '58mm' ? '10px' : '12px' }};">
                    <span>Total</span>
                    <span>Rp {{ number_format($order->grand_total ?? $order->total, 0, ',', '.') }}</span>
                </div>

                @if($order->uang_dibayar !== null)
                <div class="flex justify-between pt-1 border-t border-black mt-1">
                    <span>Tunai</span>
                    <span>Rp {{ number_format($order->uang_dibayar, 0, ',', '.') }}</span>
                </div>
                @endif

                {{-- Selalu tampilkan kembalian, meskipun 0 --}}
                <div class="flex justify-between">
                    <span>Kembali</span>
                    <span>Rp {{ number_format($order->kembalian ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-3 text-[8px] text-gray-600">
                <p>Terima kasih atas kunjungan Anda</p>
                <p class="mt-0.5">~ EssyCoff ~</p>
                <p class="mt-1 text-[7px]">*Struk ini sebagai bukti pembayaran yang sah</p>
            </div>
        </div>
    </div>

    <!-- ✅ CSS Print untuk 58mm & 80mm -->
    <style>
        @media print {
            @page {
                margin: 0;
                padding: 0;
            }

            /* Sembunyikan semua elemen */
            body * {
                visibility: hidden;
            }

            /* Tampilkan hanya struk */
            #receipt-content,
            #receipt-content * {
                visibility: visible;
            }

            /* Atur ukuran dan gaya struk */
            #receipt-content {
                position: absolute !important;
                top: 0 !important;
                left: 50% !important;
                transform: translateX(-50%) !important;
                /* biar rata tengah */
                margin: 0 auto !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                background: white !important;
                color: black !important;
                page-break-after: always;
                width: 58mm !important;
                /* ubah jadi 58mm biar kecil */
                padding: 6px !important;
                /* padding lebih kecil */
                font-size: 10px !important;
                /* font ikut kecil */
            }


            /* Reset gaya dalam struk */
            #receipt-content * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
                border: none;
                background: transparent;
                color: black !important;
                text-decoration: none;
                float: none;
                page-break-inside: avoid;
            }

            /* Gaya khusus untuk print */
            .receipt-layout hr {
                border: none !important;
                border-top: 1px dashed #000 !important;
            }
        }
    </style>

    <!-- ✅ Script Print dengan Delay -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('printReceipt', () => {
                window.scrollTo(0, 0);
                setTimeout(() => {
                    window.print();
                }, 500); // Delay agar Livewire selesai render
            });
        });
    </script>
</div>