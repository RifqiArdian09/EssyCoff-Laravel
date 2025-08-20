<section class="w-full">
    <x-page-heading>
        <x-slot:title>Edit Category</x-slot:title>
    </x-page-heading>

    <x-form wire:submit.prevent="update" class="space-y-6">
        <flux:input 
            wire:model.live="name" 
            label="Name" 
            required 
            class="dark:text-gray-200"
        />
        <div class="flex gap-2">
            <flux:button type="submit" variant="primary">Update</flux:button>
            <flux:button href="{{ route('categories.index') }}">Cancel</flux:button>
        </div>
    </x-form>
</section>
