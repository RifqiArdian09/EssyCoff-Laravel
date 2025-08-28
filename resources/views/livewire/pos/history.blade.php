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
                <x-table.heading class="w-32">Actions</x-table.heading>
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

                    <x-table.cell class="flex gap-2">
                        <a href="{{ route('pos.detail', $order) }}"
                           class="px-2 py-1 bg-white text-gray-800 rounded hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600">
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
</section>