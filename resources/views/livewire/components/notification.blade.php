<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative p-2 text-gray-500 rounded-full hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none">
        <x-heroicon-o-bell class="w-6 h-6" />
        @if($pendingCount > 0)
            <span class="absolute top-0 right-0 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">{{ $pendingCount }}</span>
        @endif
    </button>

    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow-lg z-50" x-cloak>
        <div class="p-4 border-b border-gray-200 dark:border-zinc-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Notifications</h3>
        </div>
        <div class="divide-y divide-gray-200 dark:divide-zinc-700 max-h-96 overflow-y-auto">
            @forelse($orders as $order)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-zinc-700">
                    <div class="flex justify-between items-center">
                    <span class="font-semibold text-gray-800 dark:text-gray-200">
    {{ $order->no_order ?? 'Order #'.$order->id }}
</span>

                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">A new order has been placed.</p>
                    <div class="mt-2">
                        @foreach($order->items as $item)
                            <div class="text-xs text-gray-500 dark:text-gray-400">- {{ $item->qty }}x {{ $item->product->name }}</div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    No new notifications
                </div>
            @endforelse
        </div>
        <div class="p-2 border-t border-gray-200 dark:border-zinc-700">
            <a href="{{ route('pos.history') }}" wire:navigate class="block text-center text-sm text-blue-500 hover:underline">View all orders</a>
        </div>
    </div>
</div>
