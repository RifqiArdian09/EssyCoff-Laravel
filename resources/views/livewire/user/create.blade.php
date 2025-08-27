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
                id="password"
                wire:model.live="password" 
                type="password"
                label="Password" 
                required 
                class="dark:text-gray-200 pr-10"/>
            <button type="button" 
                    class="absolute right-2 bottom-2.5 p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
                    aria-label="Toggle password visibility"
                    onclick="togglePasswordWithIcon('password', this)">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path data-eye="on" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path data-eye="on" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    <path data-eye="off" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.362-3.993M6.18 6.18A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.966 9.966 0 01-4.043 5.197M15 12a3 3 0 00-3-3m0 0a3 3 0 013 3m-3-3L3 21m9-12l9 9"/>
                </svg>
            </button>
        </div>

        <div class="relative">
            <flux:input 
                id="password_confirmation"
                wire:model.live="password_confirmation" 
                type="password"
                label="Confirm Password" 
                required 
                class="dark:text-gray-200 pr-10"
            />
            <button type="button" 
                    class="absolute right-2 bottom-2.5 p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
                    aria-label="Toggle confirm password visibility"
                    onclick="togglePasswordWithIcon('password_confirmation', this)">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path data-eye="on" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path data-eye="on" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    <path data-eye="off" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.362-3.993M6.18 6.18A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.966 9.966 0 01-4.043 5.197M15 12a3 3 0 00-3-3m0 0a3 3 0 013 3m-3-3L3 21m9-12l9 9"/>
                </svg>
            </button>
        </div>

        <div class="flex gap-2">
            <flux:button type="submit" variant="primary">Create</flux:button>
            <flux:button href="{{ route('users.index') }}">Cancel</flux:button>
        </div>
    </x-form>

    <script>
        function togglePasswordWithIcon(id, btn) {
            var input = document.getElementById(id);
            if (!input) return;
            var isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';

            // toggle icons
            var eyesOn = btn.querySelectorAll('[data-eye="on"]');
            var eyesOff = btn.querySelectorAll('[data-eye="off"]');
            eyesOn.forEach(function(node){ node.classList.toggle('hidden', !isHidden); });
            eyesOff.forEach(function(node){ node.classList.toggle('hidden', isHidden); });
        }
    </script>
</section>
