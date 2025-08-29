<section class="w-full p-4">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-200">Add User</h1>
    </div>

    <x-form wire:submit.prevent="save" class="space-y-6">
        <flux:input 
            wire:model.live="name" 
            label="Name" 
            required 
            class="dark:text-gray-200"
        />

        <flux:input 
            wire:model.live="email" 
            type="email"
            label="Email" 
            required 
            class="dark:text-gray-200"
        />

        <flux:select 
            wire:model.live="role" 
            label="Role" 
            required
        >
            <flux:select.option value="">Select role</flux:select.option>
            <flux:select.option value="manager">Manager</flux:select.option>
            <flux:select.option value="cashier">Cashier</flux:select.option>
        </flux:select>

        <div class="relative">
        <flux:input 
            wire:model.live="password" 
            type="{{ $showPassword ? 'text' : 'password' }}" 
            label="Password" 
            required 
            class="dark:text-gray-200"
            icon-end="eye-slash"
            wire:click="$toggle('showPassword')"
        />
    </div>

    <div class="relative">
        <flux:input 
            wire:model.live="password_confirmation" 
            type="{{ $showPasswordConfirmation ? 'text' : 'password' }}" 
            label="Konfirmasi Password" 
            required 
            class="dark:text-gray-200"
            icon-end="eye-slash"
            wire:click="$toggle('showPasswordConfirmation')"
        />
    </div>

        <div class="flex gap-2">
            <flux:button type="submit" icon="check" variant="primary">Create</flux:button>
            <flux:button href="{{ route('users.index') }}" icon="x-mark" variant="ghost">Cancel</flux:button>
        </div>
    </x-form>
</section>
