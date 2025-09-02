<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
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

    public bool $remember = false;

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
        $defaultRoute = ($user && $user->role === 'cashier')
            ? route('pos.cashier', absolute: false)
            : route('dashboard', absolute: false);

        $this->redirectIntended(default: $defaultRoute, navigate: true);
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

<div class="flex flex-col gap-6">
    <!-- Header -->
    <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-primary mb-2">
            <i class="fas fa-sign-in-alt mr-2"></i>
            Masuk ke Akun Anda
        </h1>
        <p class="text-gray-600">Masukkan email dan password untuk melanjutkan</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-dark">
                <i class="fas fa-envelope mr-2 text-primary"></i>
                Alamat Email
            </label>
            <input
                wire:model="email"
                id="email"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="contoh@email.com"
                class="w-full px-4 py-3 border border-secondary rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 bg-accent bg-opacity-30 hover:bg-white"
            />
            @error('email')
                <p class="text-red-600 text-sm mt-1">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <div class="flex justify-between items-center">
                <label for="password" class="block text-sm font-medium text-dark">
                    <i class="fas fa-lock mr-2 text-primary"></i>
                    Password
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" 
                       class="text-sm text-primary hover:text-dark transition-colors duration-200"
                       wire:navigate>
                        Lupa password?
                    </a>
                @endif
            </div>
            <div class="relative">
                <input
                    wire:model="password"
                    id="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Masukkan password"
                    class="w-full px-4 py-3 border border-secondary rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 bg-accent bg-opacity-30 hover:bg-white pr-12"
                />
                <button type="button" 
                        onclick="togglePassword()"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-secondary hover:text-primary transition-colors">
                    <i class="fas fa-eye" id="toggleIcon"></i>
                </button>
            </div>
            @error('password')
                <p class="text-red-600 text-sm mt-1">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input
                wire:model="remember"
                id="remember"
                type="checkbox"
                class="h-4 w-4 text-primary focus:ring-primary border-secondary rounded"
            />
            <label for="remember" class="ml-2 block text-sm text-dark">
                Ingat saya
            </label>
        </div>

        <!-- Login Button -->
        <button
            type="submit"
            class="w-full bg-primary hover:bg-dark text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg flex items-center justify-center"
        >
            <i class="fas fa-sign-in-alt mr-2"></i>
            Masuk
        </button>
    </form>

    <!-- Register Link -->
    @if (Route::has('register'))
        <div class="text-center pt-4 border-t border-secondary">
            <p class="text-dark mb-3">Belum punya akun?</p>
            <a href="{{ route('register') }}" 
               class="inline-flex items-center justify-center w-full bg-transparent border-2 border-primary text-primary font-medium py-3 px-6 rounded-xl hover:bg-primary hover:text-white transition-all duration-300"
               wire:navigate>
                <i class="fas fa-user-plus mr-2"></i>
                Daftar Sekarang
            </a>
        </div>
    @endif

    <!-- Back to Home -->
    <div class="text-center">
        <a href="{{ url('/') }}" 
           class="text-secondary hover:text-primary text-sm transition-colors duration-200"
           wire:navigate>
            <i class="fas fa-arrow-left mr-1"></i>
            Kembali ke Beranda
        </a>
    </div>
</div>

<script>
function togglePassword() {
    const passwordField = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>
