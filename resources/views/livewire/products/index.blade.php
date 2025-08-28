<section class="w-full p-4">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-200">Products</h1>
        <a href="{{ route('products.create') }}" wire:navigate
        class="px-4 py-2 bg-white text-gray-800 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600">
            Add Product
        </a>
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
    <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari produk..." class="mb-4" />

    <!-- Products Table -->
    <x-table>
        <x-slot:head>
            <x-table.row>
                <x-table.heading class="w-16">#</x-table.heading>
                <x-table.heading class="w-24">Image</x-table.heading>
                <x-table.heading>Name</x-table.heading>
                <x-table.heading>Category</x-table.heading>
                <x-table.heading class="text-right">Price</x-table.heading>
                <x-table.heading class="w-20 text-center">Stock</x-table.heading>
                <x-table.heading class="w-32 text-right">Actions</x-table.heading>
            </x-table.row>
        </x-slot:head>

        <x-slot:body>
            @forelse ($products as $index => $product)
                <x-table.row>
                    <x-table.cell class="font-medium text-gray-400">
                        {{ ($products->currentPage() - 1) * $products->perPage() + $index + 1 }}
                    </x-table.cell>

                    <!-- Circular Product Image -->
                    <x-table.cell>
                        <div class="flex justify-center">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-12 h-12 object-cover rounded-full border-2 border-gray-600"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-12 h-12 bg-gray-700 rounded-full flex items-center justify-center border-2 border-gray-600" style="display:none;">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="w-12 h-12 bg-gray-700 rounded-full flex items-center justify-center border-2 border-gray-600">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </x-table.cell>

                    <x-table.cell>
                        <div class="font-medium text-gray-200">{{ $product->name }}</div>
                        @if($product->description)
                            <div class="text-sm text-gray-400 truncate max-w-xs">{{ $product->description }}</div>
                        @endif
                    </x-table.cell>

                    <x-table.cell>
                        @if($product->category)
                            <span class="px-2 py-1 bg-gray-700 text-gray-300 rounded-full text-xs">
                                {{ $product->category->name }}
                            </span>
                        @else
                            <span class="text-gray-500 italic">No Category</span>
                        @endif
                    </x-table.cell>

                    <x-table.cell class="font-medium text-green-400 text-right">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </x-table.cell>

                    <x-table.cell class="text-center">
                        <span class="px-2 py-1 rounded-full text-xs {{ $product->stock > 10 ? 'bg-green-800 text-green-200' : ($product->stock > 0 ? 'bg-yellow-800 text-yellow-200' : 'bg-red-800 text-red-200') }}">
                            {{ $product->stock }}
                        </span>
                    </x-table.cell>

                    <x-table.cell class="flex gap-2 justify-end">
                        <a href="{{ route('products.edit', $product) }}"
                           class="px-2 py-1 text-sm rounded bg-white text-gray-800 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600">
                            Edit
                        </a>
                        <button type="button" aria-label="Delete product {{ $product->name }}" wire:click="delete({{ $product->id }})"
                                class="px-2 py-1 text-sm rounded bg-red-500 text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-red-600 dark:hover:bg-red-700">
                            Delete
                        </button>
                    </x-table.cell>
                </x-table.row>

            @empty
                <x-table.row>
                    <x-table.cell colspan="7" class="text-center py-12 text-gray-400">
                        No products found. Try adjusting your search or add a new product.
                    </x-table.cell>
                </x-table.row>
            @endforelse
        </x-slot:body>
    </x-table>

    @if($products->hasPages())
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @endif
</section>
