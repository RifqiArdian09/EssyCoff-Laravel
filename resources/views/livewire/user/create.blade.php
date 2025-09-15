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

        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            required
            autocomplete="current-password"
            :placeholder="__('Password')"
            viewable
        />

        <flux:input
            wire:model="password_confirmation"
            :label="__('Konfirmasi Password')"
            type="password"
            required
            :placeholder="__('Konfirmasi Password')"
            viewable
        />

        <div class="flex gap-2">
            <flux:button type="submit" icon="check" variant="primary">Create</flux:button>
            <flux:button href="{{ route('users.index') }}" icon="x-mark" variant="ghost">Cancel</flux:button>
        </div>
    </x-form>
</section>
