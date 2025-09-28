
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
use App\Models\User;

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

        // Validate credentials without logging in yet
        $credentials = ['email' => $this->email, 'password' => $this->password];
        if (! Auth::validate($credentials)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Fetch user and enforce single active session
        $user = User::where('email', $this->email)->first();
        if ($user && !empty($user->active_session_id) && $user->active_session_id !== Session::getId()) {
            // Another device is already using this account
            throw ValidationException::withMessages([
                'email' => 'Akun ini sedang aktif di perangkat lain. Silakan keluar dari perangkat tersebut terlebih dahulu.',
            ]);
        }

        // Proceed with normal login
        if (! Auth::attempt($credentials, $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        // Store the new session ID as the active session for the user
        $user = Auth::user();
        try {
            $user->active_session_id = Session::getId();
            $user->save();
        } catch (\Throwable $e) {
            Log::error('Failed to set active_session_id for user ID '.$user->id.': '.$e->getMessage());
        }
        
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


<div class="mx-auto w-full max-w-sm px-4 py-6 sm:max-w-md sm:px-6 sm:py-8 flex flex-col gap-6 sm:gap-8">
        <div class="flex flex-col items-center gap-3 sm:gap-4">
            <img src="{{ asset('images/tanpajudul.png') }}" alt="EssyCoff Logo" class="w-20 h-20 sm:w-36 sm:h-36">
            <h2 class="text-xl sm:text-2xl font-bold text-primary">Selamat Datang Kembali</h2>
            <p class="hidden sm:block text-gray-600 text-center">Silakan masuk ke akun Anda untuk melanjutkan</p>
        </div>

    <!-- Session Status (minimal) -->
    @if (session('status'))
        <p class="text-center text-sm text-primary mb-2">{{ session('status') }}</p>
    @endif

    <!-- Global error bubble removed; errors shown per field below -->

    <form method="POST" wire:submit="login" class="flex flex-col gap-4 sm:gap-6">
        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="hidden sm:block text-sm font-medium text-primary">{{ __('Email address') }}</label>
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
                input-class="{{ $errors->has('email') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'focus:ring-primary focus:border-primary' }} h-11 sm:h-10 text-base sm:text-sm placeholder:text-dark/60"
            />
            @error('email')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <label for="password" class="hidden sm:block text-sm font-medium text-primary">{{ __('Password') }}</label>
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
                input-class="{{ $errors->has('password') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'focus:ring-primary focus:border-primary' }} h-11 sm:h-10 text-base sm:text-sm placeholder:text-dark/60"
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
            <label for="remember" class="text-sm text-primary select-none cursor-pointer">Ingatkan saya</label>
        </div>

        <!-- Login Button -->
        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" icon="arrow-right-start-on-rectangle" color="primary" class="w-full h-11 sm:h-10 text-base sm:text-sm bg-primary text-white hover:bg-dark focus:ring-2 focus:ring-primary focus:outline-none inline-flex items-center justify-center gap-2">
                {{ __('Log in') }}
            </flux:button>
        </div>
    </form>
    

</div>