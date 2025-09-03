<div class="p-6 space-y-8 bg-white dark:bg-zinc-800 min-h-screen text-gray-900 dark:text-white">
    <!-- Judul -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kategori</h1>
            <p class="text-gray-600 dark:text-zinc-300">Kelola semua kategori produk dalam sistem</p>
        </div>
        <flux:button 
            variant="primary" 
            color="sky" 
            icon="plus" 
            href="{{ route('categories.create') }}" 
            size="sm">
            Tambah Kategori
        </flux:button>
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


    <!-- Tabel Kategori -->
    <div class="bg-white dark:bg-zinc-800 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-zinc-700 transition-colors duration-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700 dark:text-zinc-200">
                <thead class="bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse ($categories as $index => $category)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition duration-150 ease-in-out">
                            <td class="px-4 py-3 font-medium text-gray-500 dark:text-zinc-400">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                {{ $category->name }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex gap-2 justify-end">
                                    <flux:button 
                                        variant="primary" 
                                        icon="pencil-square" 
                                        href="{{ route('categories.edit', $category) }}" 
                                        size="sm">
                                        Edit
                                    </flux:button>

                                    <flux:button 
                                        variant="danger" 
                                        icon="trash" 
                                        wire:click="delete({{ $category->id }})" 
                                        wire:confirm="Anda yakin ingin menghapus kategori ini?" 
                                        size="sm">
                                        Hapus
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-zinc-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                    </svg>
                                    <span>Belum ada kategori. Cobalah menambahkan kategori baru.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination (jika menggunakan paginate) -->
        @if(method_exists($categories, 'hasPages') && $categories->hasPages())
            <div class="p-4 bg-gray-50 dark:bg-zinc-800 border-t border-gray-200 dark:border-zinc-700 transition-colors duration-200">
                <div class="flex flex-col md:flex-row items-center justify-between gap-3">
                    <p class="text-sm text-gray-600 dark:text-zinc-400">
                        Menampilkan
                        <span class="font-medium text-gray-900 dark:text-white">{{ $categories->firstItem() }}</span>
                        –
                        <span class="font-medium text-gray-900 dark:text-white">{{ $categories->lastItem() }}</span>
                        dari
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $categories->total() }}</span>
                        data
                    </p>

                    <div class="[&>nav]:flex [&>nav]:items-center [&>nav]:gap-1">
                        {{ $categories->links('components.pagination.simple-arrows') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>