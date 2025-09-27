@php
    $user = auth()->user();
    $role = $user->role ?? null;
@endphp

@if($role === 'cashier')
    <x-layouts.app.header :title="$title ?? null">
        <flux:main>
            {{ $slot }}
        </flux:main>
    </x-layouts.app.header>
@else
    <x-layouts.app.sidebar :title="$title ?? null">
        <flux:main>
            {{ $slot }}
        </flux:main>
    </x-layouts.app.sidebar>
@endif