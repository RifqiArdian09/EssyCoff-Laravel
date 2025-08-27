<section class="w-full p-4">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-200">Users</h1>
        <a href="{{ route('users.create') }}" wire:navigate
           class="px-3 py-2 rounded-lg text-sm bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700">
            Add User
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
    <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari user..." class="mb-4" />

    <!-- Users Table -->
    <x-table>
        <x-slot:head>
            <x-table.row>
                <x-table.heading>No</x-table.heading>
                <x-table.heading>Name</x-table.heading>
                <x-table.heading>Role</x-table.heading>
                <x-table.heading>Actions</x-table.heading>
            </x-table.row>
        </x-slot:head>

        <x-slot:body>
            @forelse ($users as $index => $user)
                <x-table.row>
                    <x-table.cell class="font-medium text-gray-400">
                        {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}
                    </x-table.cell>


                    <x-table.cell>
                        <div class="font-medium text-gray-200">{{ $user->name }}</div>
                    </x-table.cell>


                    <x-table.cell>
                        <span class="px-2 py-1 bg-gray-700 text-gray-300 rounded-full text-xs">
                            {{ ucfirst($user->role) }}
                        </span>
                    </x-table.cell>

                    <x-table.cell class="flex gap-2 justify-end">
                        <a href="{{ route('users.edit', $user) }}"
                           class="px-2 py-1 text-sm rounded bg-white text-gray-800 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600">
                            Edit
                        </a>
                        <button type="button" aria-label="Delete user {{ $user->name }}" wire:click="delete({{ $user->id }})"
                                class="px-2 py-1 text-sm rounded bg-red-500 text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-red-600 dark:hover:bg-red-700">
                            Delete
                        </button>
                    </x-table.cell>
                </x-table.row>

            @empty
                <x-table.row>
                    <x-table.cell colspan="6" class="text-center py-12 text-gray-400">
                        No users found. Try adjusting your search or add a new user.
                    </x-table.cell>
                </x-table.row>
            @endforelse
        </x-slot:body>
    </x-table>

    @if($users->hasPages())
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    @endif
</section>
