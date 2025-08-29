<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <!-- Header -->
    <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-primary mb-2">
            <i class="fas fa-user-plus mr-2"></i>
            Buat Akun Baru
        </h1>
        <p class="text-gray-600">Bergabunglah dengan komunitas pecinta kopi EssyCoff</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <div class="space-y-2">
            <label for="name" class="block text-sm font-medium text-dark">
                <i class="fas fa-user mr-2 text-primary"></i>
                Nama Lengkap
            </label>
            <input
                wire:model="name"
                id="name"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="Masukkan nama lengkap"
                class="w-full px-4 py-3 border border-secondary rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 bg-accent bg-opacity-30 hover:bg-white text-black"
            />
            @error('name')
                <p class="text-red-600 text-sm mt-1">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

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
            <label for="password" class="block text-sm font-medium text-dark">
                <i class="fas fa-lock mr-2 text-primary"></i>
                Password
            </label>
            <div class="relative">
                <input
                    wire:model="password"
                    id="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                    class="w-full px-4 py-3 border border-secondary rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 bg-accent bg-opacity-30 hover:bg-white pr-12"
                />
                <button type="button" 
                        onclick="togglePassword('password', 'toggleIcon1')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-secondary hover:text-primary transition-colors">
                    <i class="fas fa-eye" id="toggleIcon1"></i>
                </button>
            </div>
            @error('password')
                <p class="text-red-600 text-sm mt-1">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="space-y-2">
            <label for="password_confirmation" class="block text-sm font-medium text-dark">
                <i class="fas fa-lock mr-2 text-primary"></i>
                Konfirmasi Password
            </label>
            <div class="relative">
                <input
                    wire:model="password_confirmation"
                    id="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Ulangi password"
                    class="w-full px-4 py-3 border border-secondary rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 bg-accent bg-opacity-30 hover:bg-white pr-12"
                />
                <button type="button" 
                        onclick="togglePassword('password_confirmation', 'toggleIcon2')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-secondary hover:text-primary transition-colors">
                    <i class="fas fa-eye" id="toggleIcon2"></i>
                </button>
            </div>
            @error('password_confirmation')
                <p class="text-red-600 text-sm mt-1">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Terms Agreement -->
        <div class="flex items-start space-x-3 p-4 bg-accent bg-opacity-20 rounded-xl">
            <i class="fas fa-info-circle text-primary mt-0.5"></i>
            <div class="text-sm text-dark">
                <p>Dengan mendaftar, Anda menyetujui <a href="#" class="text-primary hover:text-dark font-medium">Syarat & Ketentuan</a> dan <a href="#" class="text-primary hover:text-dark font-medium">Kebijakan Privasi</a> EssyCoff.</p>
            </div>
        </div>

        <!-- Register Button -->
        <button
            type="submit"
            class="w-full bg-primary hover:bg-dark text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg flex items-center justify-center"
        >
            <i class="fas fa-user-plus mr-2"></i>
            Buat Akun
        </button>
    </form>

    <!-- Login Link -->
    <div class="text-center pt-4 border-t border-secondary">
        <p class="text-dark mb-3">Sudah punya akun?</p>
        <a href="{{ route('login') }}" 
           class="inline-flex items-center justify-center w-full bg-transparent border-2 border-primary text-primary font-medium py-3 px-6 rounded-xl hover:bg-primary hover:text-white transition-all duration-300"
           wire:navigate>
            <i class="fas fa-sign-in-alt mr-2"></i>
            Masuk Sekarang
        </a>
    </div>

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
function togglePassword(fieldId, iconId) {
    const passwordField = document.getElementById(fieldId);
    const toggleIcon = document.getElementById(iconId);
    
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
