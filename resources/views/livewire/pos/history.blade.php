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

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-zinc-800 p-5 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700 space-y-4 transition-colors duration-200">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Search Input -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Cari Transaksi</label>
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari no. order, customer..."
                    class="w-full"
                    icon="magnifying-glass" />
            </div>

            <!-- Month Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Filter Bulan</label>
                <flux:select wire:model.live="selectedMonth" class="w-full">
                    <option value="">Semua Bulan</option>
                    @foreach($availableMonths as $month)
                    <option value="{{ $month['value'] }}">{{ $month['label'] }}</option>
                    @endforeach
                </flux:select>
            </div>
            <!-- Table Filter -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Filter Meja</label>
                <flux:select wire:model.live="selectedTableId" class="w-full">
                    <option value="">Semua Meja</option>
                    @foreach(($tables ?? collect()) as $t)
                        <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->code }})</option>
                    @endforeach
                </flux:select>
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
                    Menampilkan transaksi bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->locale('id')->translatedFormat('F Y') }}
                </span>
            </div>
        </div>
        @endif
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
                        <th class="px-4 py-3">Meja</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Kasir</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3">Metode</th>
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
                        <td class="px-4 py-3">
                            @if($order->table)
                                <div class="flex flex-col">
                                    <span class="text-gray-900 dark:text-white font-medium">{{ $order->table->name }}</span>
                                    <span class="text-xs text-gray-500 dark:text-zinc-400 font-mono">({{ $order->table->code }})</span>
                                </div>
                            @else
                                <span class="text-gray-400 dark:text-zinc-500">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-zinc-400">
                            {{ $order->created_at->format('d M, H:i') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-zinc-400">
                            {{ $order->user?->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-emerald-600 dark:text-emerald-400 text-right">
                            Rp {{ number_format((float)$order->total, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">
                            @php $method = $order->payment_method; @endphp
                            @if($order->status === 'pending_payment' || !$method)
                                <span class="px-2 py-1 bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 rounded-full text-xs font-medium">-</span>
                            @elseif($method === 'cash')
                                <span class="px-2 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 rounded-full text-xs font-medium">Cash</span>
                            @elseif($method === 'qris')
                                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-full text-xs font-medium">QRIS</span>
                            @elseif($method === 'card')
                                <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 rounded-full text-xs font-medium">Card</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 rounded-full text-xs font-medium">{{ strtoupper($method) }}</span>
                            @endif
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
                                    color="emerald"
                                    size="sm"
                                    icon="credit-card"
                                    wire:click="confirmPayment({{ $order->id }})"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white border-emerald-600">
                                    Bayar
                                </flux:button>
                                @else
                                <flux:button
                                    variant="outline"
                                    color="blue"
                                    size="sm"
                                    icon="document-text"
                                    href="{{ route('pos.detail', $order) }}"
                                    class="text-blue-600 border-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:border-blue-400 dark:hover:bg-blue-900/20">
                                    Detail
                                </flux:button>
                                @if($order->table && $order->table->status === 'unavailable')
                                <flux:button
                                    variant="outline"
                                    size="sm"
                                    icon="check"
                                    wire:click="markTableAvailable({{ $order->id }})">
                                    Free Table
                                </flux:button>
                                @endif
                                @endif
                                
                                <flux:button
                                    variant="danger"
                                    icon="trash"
                                    size="sm"
                                    wire:click="confirmDelete({{ $order->id }})">
                                    Hapus
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-500 dark:text-zinc-500">
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
                    @php
                    // Pastikan variabel total tersedia untuk semua metode
                    $total = (float) ($selectedOrder->total ?? 0);
                    $rounded50k = ceil($total / 50000) * 50000;
                    $rounded100k = ceil($total / 100000) * 100000;
                    @endphp
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
                    @php $minUang = (float) $selectedOrder->total; @endphp

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
                </div>

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

            <div class="flex gap-3 mt-6">
                <flux:button variant="ghost" wire:click="closeModal" class="flex-1">
                    Batal
                </flux:button>
                <flux:button
                    variant="primary"
                    wire:click="processPayment"
                    :disabled="($paymentMethod === 'cash' && ((!$uangDibayar) || ($uangDibayar < $total))) || ($paymentMethod === 'card' && (strlen((string) $cardLast4) !== 4))"
                    class="flex-1">
                    Konfirmasi & Cetak
                </flux:button>
            </div>
        </div>
    </div>
    @endif

    <!-- ✅ Modal: Konfirmasi Hapus -->
    @if($showDeleteModal && $orderToDelete)
    <div class="fixed inset-0 z-50 flex items-center justify-center" wire:click.self="closeDeleteModal">
        <div class="relative bg-white dark:bg-zinc-800 rounded-lg shadow-xl max-w-md w-full mx-4 p-6 border border-gray-200 dark:border-zinc-700">

            <div class="flex items-start gap-3">
                <div class="shrink-0 mt-0.5 text-red-600 dark:text-red-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M4.93 4.93l14.14 14.14M9 3h6a2 2 0 012 2v1H7V5a2 2 0 012-2zm10 5H5l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Hapus Transaksi?</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-zinc-300">Tindakan ini tidak dapat dibatalkan. Anda akan menghapus transaksi dengan No. Order <span class="font-semibold">{{ $orderToDelete->no_order }}</span>.</p>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <flux:button variant="ghost" wire:click="closeDeleteModal" class="flex-1">Batal</flux:button>
                <flux:button variant="primary" class="flex-1 bg-red-600 hover:bg-red-700 text-white border-red-600" icon="trash" wire:click="deleteOrder">Hapus</flux:button>
            </div>
        </div>
    </div>
    @endif

    @if($selectedOrder)
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
                    <span>{{ $selectedOrder->no_order }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Kasir:</span>
                    <span>{{ $selectedOrder->user?->name ?? 'System' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Tanggal:</span>
                    <span>{{ $selectedOrder->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if($selectedOrder->table)
                <div class="flex justify-between">
                    <span class="font-medium">Meja:</span>
                    <span>{{ $selectedOrder->table->name }} ({{ $selectedOrder->table->code }})</span>
                </div>
                @endif
                @if($selectedOrder->customer_name)
                <div class="flex justify-between">
                    <span class="font-medium">Customer:</span>
                    <span>{{ $selectedOrder->customer_name }}</span>
                </div>
                @endif
            </div>

            <hr class="my-1 border-dashed border-black" style="border-top: 1px dashed #000; margin: 4px 0;">

            <!-- Items -->
            <div class="space-y-1 mb-2">
                @foreach($selectedOrder->items as $item)
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
                    <span>Rp {{ number_format($selectedOrder->total, 0, ',', '.') }}</span>
                </div>
                @if($selectedOrder->discount > 0)
                <div class="flex justify-between">
                    <span>Diskon</span>
                    <span class="text-red-600">- Rp {{ number_format($selectedOrder->discount, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($selectedOrder->tax > 0)
                <div class="flex justify-between">
                    <span>Pajak ({{ $selectedOrder->tax }}%)</span>
                    <span>Rp {{ number_format(($selectedOrder->total * $selectedOrder->tax) / 100, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($selectedOrder->service_charge > 0)
                <div class="flex justify-between">
                    <span>Service Charge ({{ $selectedOrder->service_charge }}%)</span>
                    <span>Rp {{ number_format(($selectedOrder->total * $selectedOrder->service_charge) / 100, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between font-bold pt-1 mt-1 border-t border-black" style="font-size: {{ $width === '58mm' ? '10px' : '12px' }};">
                    <span>Total</span>
                    <span>Rp {{ number_format($selectedOrder->grand_total ?? $selectedOrder->total, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between pt-1 border-t border-black mt-1">
                    <span>Metode</span>
                    <span>{{ strtoupper($selectedOrder->payment_method ?? 'CASH') }}</span>
                </div>
                @if($selectedOrder->payment_method === 'qris' && $selectedOrder->payment_ref)
                <div class="flex justify-between">
                    <span>Referensi</span>
                    <span>{{ $selectedOrder->payment_ref }}</span>
                </div>
                @endif
                @if($selectedOrder->payment_method === 'card')
                <div class="flex justify-between">
                    <span>Kartu</span>
                    <span>**** **** **** {{ $selectedOrder->card_last4 }}</span>
                </div>
                @if($selectedOrder->payment_ref)
                <div class="flex justify-between">
                    <span>Referensi</span>
                    <span>{{ $selectedOrder->payment_ref }}</span>
                </div>
                @endif
                @endif
                @if($selectedOrder->uang_dibayar !== null)
                <div class="flex justify-between">
                    <span>{{ $selectedOrder->payment_method === 'cash' ? 'Tunai' : 'Dibayar' }}</span>
                    <span>Rp {{ number_format($selectedOrder->uang_dibayar, 0, ',', '.') }}</span>
                </div>
                @endif
                {{-- Selalu tampilkan kembalian, meskipun 0 --}}
                <div class="flex justify-between">
                    <span>Kembali</span>
                    <span>Rp {{ number_format($selectedOrder->kembalian ?? 0, 0, ',', '.') }}</span>
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
    @endif

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