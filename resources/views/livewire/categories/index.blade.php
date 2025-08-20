<section class="w-full">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-200">Categories</h1>
        <a href="{{ route('categories.create') }}" wire:navigate
           class="px-4 py-2 bg-white text-gray-800 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600">
            Add Category
        </a>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 px-4 py-2 rounded bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
            {{ session('message') }}
        </div>
    @endif

    <x-table>
        <x-slot:head>
            <x-table.row>
                <x-table.heading>#</x-table.heading>
                <x-table.heading>Name</x-table.heading>
                <x-table.heading>Actions</x-table.heading>
            </x-table.row>
        </x-slot:head>

        <x-slot:body>
            @forelse ($categories as $index => $category)
                <x-table.row>
                    <x-table.cell>{{ $index + 1 }}</x-table.cell>
                    <x-table.cell>{{ $category->name }}</x-table.cell>
                    <x-table.cell class="flex gap-2">
                        <a href="{{ route('categories.edit', $category) }}" 
                           class="px-2 py-1 bg-white text-gray-800 rounded hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600">
                            Edit
                        </a>
                        <button wire:click="delete({{ $category->id }})"
                                class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700">
                            Delete
                        </button>
                    </x-table.cell>
                </x-table.row>
            @empty
                <x-table.row>
                    <x-table.cell colspan="3" class="text-center text-gray-500 dark:text-gray-400">
                        No categories found.
                    </x-table.cell>
                </x-table.row>
            @endforelse
        </x-slot:body>
    </x-table>
</section>
