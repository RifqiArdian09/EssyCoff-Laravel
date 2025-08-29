<section class="w-full p-4">
    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-200">Riwayat Transaksi</h1>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-800 text-green-100 border border-green-700">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('message') }}
            </div>
        </div>
    @endif

     <!-- Search Bar -->
     <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari transaksi..." class="mb-4" />

    <!-- Orders Table -->
    <x-table>
        <x-slot:head>
            <x-table.row>
                <x-table.heading class="w-16">#</x-table.heading>
                <x-table.heading>No Order</x-table.heading>
                <x-table.heading>Customer</x-table.heading>
                <x-table.heading>Tanggal</x-table.heading>
                <x-table.heading>Kasir</x-table.heading>
                <x-table.heading>Total</x-table.heading>
                <x-table.heading>Status</x-table.heading>
                <x-table.heading class="w-40">Actions</x-table.heading>
            </x-table.row>
        </x-slot:head>

        <x-slot:body>
            @forelse ($orders as $index => $order)
                <x-table.row>
                    <x-table.cell class="font-medium text-gray-400">
                        {{ ($orders->currentPage() - 1) * $orders->perPage() + $index + 1 }}
                    </x-table.cell>

                    <x-table.cell>
                        <div class="font-medium text-gray-200">{{ $order->no_order }}</div>
                    </x-table.cell>

                    <x-table.cell>
                        <div class="font-medium text-gray-200">{{ $order->customer_name ?? '-' }}</div>
                    </x-table.cell>

                    <x-table.cell>
                        <div class="text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                    </x-table.cell>

                    <x-table.cell>
                        <div class="text-gray-400">{{ $order->user ? $order->user->name : '-' }}</div>
                    </x-table.cell>

                    <x-table.cell class="font-medium text-green-400">
                        Rp {{ number_format($order->total,0,',','.') }}
                    </x-table.cell>

                    <x-table.cell>
                        @if($order->status === 'pending_payment')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                Pending Payment
                            </span>
                        @else
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                Paid
                            </span>
                        @endif
                    </x-table.cell>

                    <x-table.cell class="flex gap-2">
                        @if($order->status === 'pending_payment')
                            <button wire:click="confirmPayment({{ $order->id }})"
                                    class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs">
                                Konfirmasi Bayar
                            </button>
                        @endif
                        <a href="{{ route('pos.transaction-detail', $order) }}"
                           class="px-2 py-1 bg-white text-gray-800 rounded hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600 text-xs">
                            Detail
                        </a>
                    </x-table.cell>
                </x-table.row>
            @empty
                <x-table.row>
                    <x-table.cell colspan="10" class="text-center py-12 text-gray-400">
                        Belum ada transaksi.
                    </x-table.cell>
                </x-table.row>
            @endforelse
        </x-slot:body>
    </x-table>

    @if($orders->hasPages())
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif

    <!-- Payment Confirmation Modal -->
    @if($showPaymentModal && $selectedOrder)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">
                    Konfirmasi Pembayaran
                </h3>
                
                <div class="space-y-4">
                    <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                        <div class="flex justify-between mb-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">No. Order:</span>
                            <span class="font-medium">{{ $selectedOrder->no_order }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Customer:</span>
                            <span class="font-medium">{{ $selectedOrder->customer_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total:</span>
                            <span class="font-bold text-green-600">Rp {{ number_format($selectedOrder->total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Uang yang Dibayar
                        </label>
                        <input type="number" 
                               wire:model.live="uangDibayar" 
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               placeholder="Masukkan jumlah uang"
                               min="{{ $selectedOrder->total }}">
                        @error('uangDibayar')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($uangDibayar && $uangDibayar >= $selectedOrder->total)
                        <div class="bg-blue-100 dark:bg-blue-900 p-4 rounded-lg">
                            <div class="flex justify-between">
                                <span class="text-sm text-blue-600 dark:text-blue-300">Kembalian:</span>
                                <span class="font-bold text-blue-600 dark:text-blue-300">
                                    Rp {{ number_format($uangDibayar - $selectedOrder->total, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex space-x-3 mt-6">
                    <button wire:click="closeModal" 
                            class="flex-1 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                        Batal
                    </button>
                    <button wire:click="processPayment" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                            @if(!$uangDibayar || $uangDibayar < $selectedOrder->total) disabled @endif>
                        Konfirmasi
                    </button>
                </div>
            </div>
        </div>
    @endif
</section>