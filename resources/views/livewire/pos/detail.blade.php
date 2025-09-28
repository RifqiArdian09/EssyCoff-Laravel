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
                <p class="font-medium {{ $order->status === 'pending_payment' ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</p>
            </div>
            <div class="md:col-span-3">
                <p class="text-sm text-gray-600 dark:text-zinc-400">Meja</p>
                @if($order->table)
                <p class="font-medium text-gray-900 dark:text-white">{{ $order->table->name }} <span class="text-xs text-gray-500 dark:text-zinc-400">({{ $order->table->code }})</span></p>
                @else
                <p class="font-medium text-gray-400">-</p>
                @endif
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

            <div class="flex justify-between pt-1 border-t border-gray-200 dark:border-zinc-700">
                <span class="text-gray-600 dark:text-zinc-300">Metode</span>
                <span class="font-medium text-gray-900 dark:text-white">
                    {{ $order->status === 'pending_payment' ? '' : strtoupper($order->payment_method ?? '') }}
                </span>
            </div>

            @if(($order->payment_method === 'qris') && $order->payment_ref)
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-zinc-300">Referensi</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ $order->payment_ref }}</span>
            </div>
            @endif

            @if($order->payment_method === 'card' && $order->status !== 'pending_payment')
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-zinc-300">Kartu</span>
                <span class="font-medium text-gray-900 dark:text-white">**** **** **** {{ $order->card_last4 }}</span>
            </div>
            @if($order->payment_ref)
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-zinc-300">Referensi</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ $order->payment_ref }}</span>
            </div>
            @endif
            @endif

            @if($order->uang_dibayar !== null && $order->status !== 'pending_payment')
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-zinc-300">{{ $order->payment_method === 'cash' ? 'Tunai' : 'Dibayar' }}</span>
                <span class="font-semibold text-gray-900 dark:text-white">
                    Rp {{ number_format($order->uang_dibayar, 0, ',', '.') }}
                </span>
            </div>
            @endif

            @if($order->kembalian !== null && $order->status !== 'pending_payment')
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
        
        @if($order->status === 'pending_payment')
        <flux:button 
            variant="primary" 
            icon="credit-card"
            wire:click="openPaymentModal">
            Bayar
        </flux:button>
        @endif

        @if($order->status === 'paid')
        <!-- Tombol Cetak Ulang Struk -->
        <flux:button 
            variant="primary" 
            icon="printer"
            wire:click="printReceipt">
            Cetak Ulang Struk
        </flux:button>
        @endif

        @if($order->table && $order->table->status === 'unavailable')
        <flux:button 
            variant="outline" 
            icon="check"
            wire:click="markTableAvailable">
            Tandai Meja Tersedia
        </flux:button>
        @endif
    </div>

    <!-- Modal Pembayaran -->
    @if($showPaymentModal)
    <div class="fixed inset-0 z-[70] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" wire:click="closePaymentModal"></div>
        <div class="relative bg-white dark:bg-zinc-800 rounded-lg p-6 w-full max-w-md z-[71] border border-gray-200 dark:border-zinc-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pembayaran</h3>
            <!-- Info ringkas: No. Order, Customer, Total -->
            <div class="mb-4 rounded-md border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-900 p-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-zinc-300">No. Order</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $order->no_order }}</span>
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-gray-600 dark:text-zinc-300">Customer</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $order->customer_name ?? '-' }}</span>
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-gray-600 dark:text-zinc-300">Total</span>
                    <span class="font-bold text-gray-900 dark:text-white">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="space-y-4">
                @php
                $total = (float) ($order->total ?? 0);
                $rounded50k = ceil($total / 50000) * 50000;
                $rounded100k = ceil($total / 100000) * 100000;
                @endphp

                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-zinc-300">Total</span>
                    <span class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>

                <!-- Metode Pembayaran -->
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Metode</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" wire:click="$set('paymentMethod','cash')"
                            class="px-3 py-2 rounded border text-sm transition
                                {{ $paymentMethod === 'cash'
                                    ? 'bg-emerald-600 text-white border-emerald-700 hover:bg-emerald-700'
                                    : 'bg-gray-50 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border-gray-200 dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700' }}">
                            Cash
                        </button>
                        <button type="button" wire:click="$set('paymentMethod','qris')"
                            class="px-3 py-2 rounded border text-sm transition
                                {{ $paymentMethod === 'qris'
                                    ? 'bg-blue-600 text-white border-blue-700 hover:bg-blue-700'
                                    : 'bg-gray-50 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border-gray-200 dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700' }}">
                            QRIS
                        </button>
                        <button type="button" wire:click="$set('paymentMethod','card')"
                            class="px-3 py-2 rounded border text-sm transition
                                {{ $paymentMethod === 'card'
                                    ? 'bg-purple-600 text-white border-purple-700 hover:bg-purple-700'
                                    : 'bg-gray-50 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border-gray-200 dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700' }}">
                            Card
                        </button>
                    </div>
                </div>

                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">
                    @if($paymentMethod === 'cash')
                        Uang yang Dibayar
                    @elseif($paymentMethod === 'qris')
                        Referensi QRIS (opsional)
                    @else
                        Detail Kartu
                    @endif
                </label>

                @php $minUang = (float) $order->total; @endphp

                @if($paymentMethod === 'cash')
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
                @elseif($paymentMethod === 'qris')
                    <flux:input
                        wire:model.live="paymentRef"
                        type="text"
                        placeholder="No. Referensi (opsional)"
                        class="w-full" />
                @else
                    <div class="grid grid-cols-2 gap-3">
                        <flux:input
                            wire:model.live="cardLast4"
                            type="text"
                            placeholder="Last 4 Digit"
                            maxlength="4"
                            class="w-full" />
                        <flux:input
                            wire:model.live="paymentRef"
                            type="text"
                            placeholder="No. Referensi (opsional)"
                            class="w-full" />
                    </div>
                    @error('cardLast4')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                @endif

                @php
                $uangDibayarFloat = (float)($uangDibayar ?? 0);
                $kembalian = $uangDibayarFloat - $total;
                @endphp

                @if($paymentMethod === 'cash' && $uangDibayarFloat >= $total)
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

            <div class="flex justify-end gap-2 mt-6">
                <flux:button variant="outline" wire:click="closePaymentModal">Batal</flux:button>
                <flux:button variant="primary" icon="check" wire:click="processPayment">Proses Pembayaran</flux:button>
            </div>
        </div>
    </div>
    @endif

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
                @if($order->table)
                <div class="flex justify-between">
                    <span class="font-medium">Meja:</span>
                    <span>{{ $order->table->name }} ({{ $order->table->code }})</span>
                </div>
                @endif
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

                <div class="flex justify-between pt-1 border-t border-black mt-1">
                    <span>Metode</span>
                    <span>{{ strtoupper($order->payment_method ?? 'CASH') }}</span>
                </div>
                @if($order->payment_method === 'qris' && $order->payment_ref)
                <div class="flex justify-between">
                    <span>Referensi</span>
                    <span>{{ $order->payment_ref }}</span>
                </div>
                @endif
                @if($order->payment_method === 'card')
                <div class="flex justify-between">
                    <span>Kartu</span>
                    <span>**** **** **** {{ $order->card_last4 }}</span>
                </div>
                @if($order->payment_ref)
                <div class="flex justify-between">
                    <span>Referensi</span>
                    <span>{{ $order->payment_ref }}</span>
                </div>
                @endif
                @endif
                @if($order->uang_dibayar !== null)
                <div class="flex justify-between">
                    <span>{{ $order->payment_method === 'cash' ? 'Tunai' : 'Dibayar' }}</span>
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