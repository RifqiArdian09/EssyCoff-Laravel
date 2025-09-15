
<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = true;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $user = Auth::user();
        
        // Direct redirect based on role
        if ($user->role === 'cashier') {
            $this->redirect(route('pos.cashier'), navigate: true);
        } else {
            $this->redirect(route('dashboard'), navigate: true);
        }
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>


<div class="flex flex-col gap-8">
        <div class="flex flex-col items-center gap-4">
            <img src="{{ asset('images/tanpajudul.png') }}" alt="EssyCoff Logo" class="w-32 h-32 sm:w-36 sm:h-36">
            <h2 class="text-2xl font-bold text-primary">Selamat Datang Kembali</h2>
            <p class="text-gray-600 text-center">Silakan masuk ke akun Anda untuk melanjutkan</p>
        </div>

    <!-- Session Status -->
    <x-auth-session-status class="text-center mb-2" :status="session('status')" />

    <!-- Auth/Validation Alert -->
    @if ($errors->has('email') || $errors->has('password'))
        <div class="rounded-md bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            {{ $errors->first('email') ?: $errors->first('password') }}
        </div>
    @endif

    <form method="POST" wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-primary">{{ __('Email address') }}</label>
            <flux:input
                id="email"
                wire:model="email"
                :label="null"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
                color="primary"
                input-class="{{ $errors->has('email') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'focus:ring-primary focus:border-primary' }} text-dark placeholder:text-dark/60"
            />
            @error('email')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-primary">{{ __('Password') }}</label>
            <flux:input
                id="password"
                wire:model="password"
                :label="null"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Password')"
                viewable
                color="primary"
                input-class="{{ $errors->has('password') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'focus:ring-primary focus:border-primary' }} text-dark placeholder:text-dark/60"
            />
            @error('password')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        
        </div>

        <!-- Remember Me -->
        <div class="flex items-center gap-2">
            <flux:checkbox
                id="remember"
                wire:model="remember"
                :label="null"
                checked
                color="primary"
                input-class="accent-[#6f4e37] focus:ring-[#6f4e37] border-[#6f4e37]"
            />
            <label for="remember" class="text-primary select-none cursor-pointer">Ingatkan saya</label>
        </div>

        <!-- Login Button -->
        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" icon="arrow-right-start-on-rectangle" color="primary" class="w-full bg-primary text-white hover:bg-dark focus:ring-2 focus:ring-primary focus:outline-none inline-flex items-center justify-center gap-2">
                {{ __('Log in') }}
            </flux:button>
        </div>
    </form>
    

</div>