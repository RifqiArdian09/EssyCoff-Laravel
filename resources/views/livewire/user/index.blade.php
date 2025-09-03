<div class="p-6 space-y-8 bg-white dark:bg-zinc-800 min-h-screen text-gray-900 dark:text-white">
    <!-- Judul -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pengguna</h1>
            <p class="text-gray-600 dark:text-zinc-300">Kelola semua pengguna dalam sistem</p>
        </div>
        <flux:button 
            variant="primary" 
            color="sky" 
            icon="plus" 
            href="{{ route('users.create') }}" 
            size="sm">
            Tambah Pengguna
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

    <!-- Search Bar -->
    <div class="bg-white dark:bg-zinc-800 p-5 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700 space-y-4 transition-colors duration-200">
        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Cari Pengguna</label>
        <flux:input 
            wire:model.live.debounce.300ms="search" 
            placeholder="Cari pengguna berdasarkan nama atau email..." 
            class="w-full" 
            icon="magnifying-glass" 
        />
    </div>

    <!-- Tabel Pengguna -->
    <div class="bg-white dark:bg-zinc-800 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-zinc-700 transition-colors duration-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700 dark:text-zinc-200">
                <thead class="bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse ($users as $index => $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition duration-150 ease-in-out">
                            <td class="px-4 py-3 font-medium text-gray-500 dark:text-zinc-400">
                                {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                {{ $user->name }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $role = ucfirst($user->role);
                                    $badgeClass = match($user->role) {
                                        'manager' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200',
                                        'cashier' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200',
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs {{ $badgeClass }}">
                                    {{ $role }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex gap-2 justify-end">
                                    <flux:button 
                                        variant="primary" 
                                        icon="pencil-square" 
                                        href="{{ route('users.edit', $user) }}" 
                                        size="sm">
                                        Edit
                                    </flux:button>

                                    <flux:button 
                                        variant="danger" 
                                        icon="trash" 
                                        wire:click="delete({{ $user->id }})" 
                                        wire:confirm="Anda yakin ingin menghapus pengguna ini?" 
                                        size="sm">
                                        Hapus
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-zinc-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    <span>Belum ada pengguna. Cobalah menambahkan pengguna baru.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="p-4 bg-gray-50 dark:bg-zinc-800 border-t border-gray-200 dark:border-zinc-700 transition-colors duration-200">
                <div class="flex flex-col md:flex-row items-center justify-between gap-3">
                    <p class="text-sm text-gray-600 dark:text-zinc-400">
                        Menampilkan
                        <span class="font-medium text-gray-900 dark:text-white">{{ $users->firstItem() }}</span>
                        –
                        <span class="font-medium text-gray-900 dark:text-white">{{ $users->lastItem() }}</span>
                        dari
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $users->total() }}</span>
                        data
                    </p>

                    <div class="[&>nav]:flex [&>nav]:items-center [&>nav]:gap-1">
                        {{ $users->links('components.pagination.simple-arrows') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>