<section class="w-full">
    <x-page-heading>
        <x-slot:title>Add Product</x-slot:title>
    </x-page-heading>
    
    <x-form wire:submit.prevent="save" class="space-y-6">
        <flux:input wire:model.live="name" label="Name" required class="dark:text-gray-200" />
        
        <flux:select wire:model.live="category_id" label="Category" required>
            <flux:select.option value="">Select Category</flux:select.option>
            @foreach($categories as $category)
                <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
            @endforeach
        </flux:select>
        
        <flux:input wire:model.live="price" type="number" step="0.01" label="Price" required class="dark:text-gray-200" />
        
        <flux:input wire:model.live="stock" type="number" label="Stock" required class="dark:text-gray-200" />
        
        <div>
            <flux:input type="file" wire:model="image" label="Product Image" accept="image/*" />
            
            @error('image') 
                <span class="text-red-500 text-sm">{{ $message }}</span> 
            @enderror
            
            @if($image)
                <div class="mt-3">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Image Preview:</p>
                    <img src="{{ $image->temporaryUrl() }}" 
                        class="w-32 h-32 object-cover rounded-lg border border-gray-200 dark:border-gray-700" 
                        alt="Product Image Preview">
                </div>
            @endif
        </div>
        
        <div class="flex gap-2 pt-4">
            <flux:button type="submit" variant="primary">Save Product</flux:button>
            <flux:button href="{{ route('products.index') }}" variant="ghost">Cancel</flux:button>
        </div>
    </x-form>
</section>