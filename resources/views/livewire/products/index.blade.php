<div class="p-6 space-y-8 bg-white dark:bg-zinc-800 min-h-screen text-gray-900 dark:text-white">
    <!-- Judul -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Produk</h1>
            <p class="text-gray-600 dark:text-zinc-300">Kelola semua produk dalam sistem</p>
        </div>
        @if(!in_array($filter, ['out_of_stock', 'low_stock']))
        <flux:button
            variant="primary"
            color="sky"
            icon="plus"
            href="{{ route('products.create') }}"
            size="sm">
            Tambah Produk
        </flux:button>
        @endif
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


    <div class="bg-white dark:bg-zinc-800 p-5 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700 space-y-4 transition-colors duration-200">
        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Cari Produk</label>
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Cari produk, kategori, atau nama produk..."
            class="w-full"
            icon="magnifying-glass" />
    </div>

    <!-- Tabel Produk -->
    <div class="bg-white dark:bg-zinc-800 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-zinc-700 transition-colors duration-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700 dark:text-zinc-200">
                <thead class="bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Gambar</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3 text-right">Harga</th>
                        <th class="px-4 py-3 text-center">Stok</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse ($products as $index => $product)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition duration-150 ease-in-out cursor-pointer">
                        <td class="px-4 py-3 font-medium text-gray-500 dark:text-zinc-400">
                            {{ ($products->currentPage() - 1) * $products->perPage() + $index + 1 }}
                        </td>

                        <!-- Gambar Produk (Bulat) -->
                        <td class="px-4 py-3">
                            <div class="flex justify-center">
                                @if($product->image)
                                <img
                                    src="{{ asset('storage/' . $product->image) }}"
                                    alt="{{ $product->name }}"
                                    class="w-12 h-12 object-cover rounded-full border-2 border-gray-300 dark:border-gray-600"
                                    onerror="this.style.display='none'; this.parentNode.querySelector('.fallback').style.display='flex';">
                                <!-- Fallback jika gambar error -->
                                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center border-2 border-gray-300 dark:border-gray-600 fallback" style="display:none;">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                @else
                                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center border-2 border-gray-300 dark:border-gray-600">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                @endif
                            </div>
                        </td>

                        <!-- Nama & Deskripsi -->
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $product->name }}</div>
                            @if($product->description)
                            <div class="text-sm text-gray-600 dark:text-zinc-400 truncate max-w-xs">
                                {{ $product->description }}
                            </div>
                            @endif
                        </td>

                        <!-- Kategori -->
                        <td class="px-4 py-3">
                            @if($product->category)
                            <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded-full text-xs font-medium">
                                {{ $product->category->name }}
                            </span>
                            @else
                            <span class="text-gray-500 dark:text-zinc-500 italic text-sm">Tanpa Kategori</span>
                            @endif
                        </td>

                        <!-- Harga -->
                        <td class="px-4 py-3 font-semibold text-emerald-600 dark:text-emerald-400 text-right">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </td>

                        <!-- Stok (dengan warna status) -->
                        <td class="px-4 py-3 text-center">
                            @php
                            $stock = $product->stock;
                            if ($stock > 10) {
                            $badgeClass = 'bg-emerald-100 dark:bg-emerald-800/40 text-emerald-800 dark:text-emerald-300';
                            } elseif ($stock > 0) {
                            $badgeClass = 'bg-yellow-100 dark:bg-yellow-800/40 text-yellow-800 dark:text-yellow-300';
                            } else {
                            $badgeClass = 'bg-red-100 dark:bg-red-800/40 text-red-800 dark:text-red-300';
                            }
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs {{ $badgeClass }}">
                                {{ $stock }}
                            </span>
                        </td>

                        <!-- Aksi -->
                        <td class="px-4 py-3">
                            <div class="flex gap-2 justify-end">
                                <flux:button
                                    variant="primary"
                                    icon="pencil-square"
                                    href="{{ route('products.edit', $product) }}"
                                    size="sm">
                                    Edit
                                </flux:button>

                                <flux:button
                                    variant="danger"
                                    icon="trash"
                                    wire:click="delete({{ $product->id }})"
                                    wire:confirm="Anda yakin ingin menghapus produk ini?"
                                    size="sm">
                                    Hapus
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-zinc-500">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                <span>Belum ada produk. Cobalah menambahkan produk baru.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
        <div class="p-4 bg-gray-50 dark:bg-zinc-800 border-t border-gray-200 dark:border-zinc-700 transition-colors duration-200">
            <div class="flex flex-col md:flex-row items-center justify-between gap-3">
                <p class="text-sm text-gray-600 dark:text-zinc-400">
                    Menampilkan
                    <span class="font-medium text-gray-900 dark:text-white">{{ $products->firstItem() }}</span>
                    –
                    <span class="font-medium text-gray-900 dark:text-white">{{ $products->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $products->total() }}</span>
                    data
                </p>

                <div class="[&>nav]:flex [&>nav]:items-center [&>nav]:gap-1">
                    {{ $products->links('components.pagination.simple-arrows') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>