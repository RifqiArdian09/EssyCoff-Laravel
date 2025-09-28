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
                                        wire:click="confirmDelete({{ $category->id }})" 
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

    <!-- Modal Konfirmasi Hapus Kategori -->
    @if($confirmingCategoryDeletion)
    <div
        class="fixed inset-0 z-[60] overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-black/50" wire:click="$set('confirmingCategoryDeletion', false)"></div>

            <!-- Modal panel -->
            <div class="relative bg-white dark:bg-zinc-800 rounded-lg p-6 w-full max-w-sm z-[70]">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                        Hapus Kategori
                    </h3>
                    <p class="text-gray-500 dark:text-zinc-400 mb-6">
                        Apakah Anda yakin ingin menghapus kategori ini? Semua produk dalam kategori ini akan tetap ada tetapi tidak akan memiliki kategori.
                    </p>
                    <div class="flex justify-center gap-3">
                        <flux:button
                            wire:click="$set('confirmingCategoryDeletion', false)"
                            variant="primary"
                            class="px-4">
                            Batal
                        </flux:button>
                        <flux:button
                            wire:click="delete({{ $categoryIdToDelete }})"
                            variant="danger"
                            class="px-4">
                            Ya, Hapus
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>