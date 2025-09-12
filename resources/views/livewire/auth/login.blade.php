
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
    <x-auth-session-status class="text-center mb-4" :status="session('status')" />

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
 class="w-full px-6 py-4 text-lg border-2 border-secondary rounded-2xl transition-all duration-300 
           bg-accent bg-opacity-20 text-dark hover:bg-black focus:bg-black focus:text-white focus:ring-2 focus:ring-primary focus:border-primary shadow-sm hover:shadow-md">
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
            </div>
            <div class="relative">
                <input
                    wire:model="password"
                    id="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Masukkan password"
 class="w-full px-6 py-4 text-lg border-2 border-secondary rounded-2xl transition-all duration-300 
           bg-accent bg-opacity-20 text-dark hover:bg-black focus:bg-black focus:text-white focus:ring-2 focus:ring-primary focus:border-primary shadow-sm hover:shadow-md">
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
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input
                    wire:model="remember"
                    id="remember"
                    type="checkbox"
                    class="h-5 w-5 text-primary focus:ring-primary border-secondary rounded"
                />
                <label for="remember" class="ml-3 block text-sm font-medium text-dark">
                    Ingat saya
                </label>
            </div>
    
        </div>

        <!-- Login Button -->
        <button
            type="submit"
            class="w-full bg-primary hover:bg-dark text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl flex items-center justify-center text-lg"
        >
            <i class="fas fa-sign-in-alt mr-3"></i>
            Masuk ke Akun
        </button>
    </form>
    

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