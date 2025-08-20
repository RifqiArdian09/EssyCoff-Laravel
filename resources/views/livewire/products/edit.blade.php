<section class="w-full">
    <x-page-heading>
        <x-slot:title>Edit Product</x-slot:title>
    </x-page-heading>
    
    <x-form wire:submit.prevent="update" class="space-y-6">
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

            {{-- Show current image or new uploaded image --}}
            @if($image)
                {{-- New image uploaded --}}
                <div class="mt-3">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">New Image Preview:</p>
                    <div class="relative inline-block">
                        <img src="{{ $image->temporaryUrl() }}" 
                            class="w-32 h-32 object-cover rounded-lg border border-gray-200 dark:border-gray-700" 
                            alt="New Product Image">
                        <button type="button" 
                                wire:click="$set('image', null)" 
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm hover:bg-red-600">
                            ×
                        </button>
                    </div>
                </div>
            @elseif($currentImage)
                {{-- Current existing image --}}
                <div class="mt-3">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Current Image:</p>
                    <div class="relative inline-block">
                        <img src="{{ asset('storage/'.$currentImage) }}" 
                            class="w-32 h-32 object-cover rounded-lg border border-gray-200 dark:border-gray-700" 
                            alt="Current Product Image"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <div class="w-32 h-32 bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center" style="display:none;">
                            <span class="text-gray-400 text-xs">Image not found</span>
                        </div>
                        <button type="button" 
                                wire:click="removeImage" 
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm hover:bg-red-600">
                            ×
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Path: storage/{{ $currentImage }}</p>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">No image selected</p>
            @endif
        </div>

        <div class="flex gap-2 pt-4">
            <flux:button type="submit" variant="primary">Update Product</flux:button>
            <flux:button href="{{ route('products.index') }}" variant="ghost">Cancel</flux:button>
        </div>
    </x-form>
</section>