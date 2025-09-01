<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
    <!-- Header -->
    <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <!-- Toggle sidebar untuk mobile -->
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <!-- Logo -->
        <a href="{{ route('dashboard') }}" 
           class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" 
           wire:navigate>
            <x-app-logo />
        </a>

        <!-- Navbar -->
        <flux:navbar class="-mb-px max-lg:hidden">
            <flux:navbar.item icon="layout-grid" 
                :href="route('dashboard')" 
                :current="request()->routeIs('dashboard')" 
                wire:navigate>
                {{ __('Dashboard') }}
            </flux:navbar.item>

            @php
            $user = auth()->user();
            $isManager = $user && $user->role === 'manager';
            $isCashier = $user && $user->role === 'cashier';

            $pendingCount = $pendingCount ?? 0;
            $lowStockCount = $lowStockCount ?? 0;
            $outOfStockCount = $outOfStockCount ?? 0;
            @endphp

            @if($isManager)
            <!-- Products -->
            <flux:navbar.item 
                icon="archive-box" 
                :href="route('products.index')" 
                :current="request()->routeIs('products.*')" 
                wire:navigate>
                {{ __('Products') }}
            </flux:navbar.item>

            <!-- Users -->
            <flux:navbar.item 
                icon="users" 
                :href="route('users.index')" 
                :current="request()->routeIs('users.*')" 
                wire:navigate>
                {{ __('Users') }}
            </flux:navbar.item>

            <!-- Stock Alerts -->
            @if(($lowStockCount > 0 || $outOfStockCount > 0))
            <flux:dropdown>
                <flux:navbar.item icon="exclamation-triangle">
                    {{ __('Stock Alerts') }}
                </flux:navbar.item>

                <flux:menu class="w-[220px]">
                    @if($outOfStockCount > 0)
                    <flux:menu.item 
                        :href="route('products.index', ['filter' => 'out_of_stock'])" 
                        wire:navigate 
                        class="text-red-600 dark:text-red-400">
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                            {{ __('Out of Stock') }}
                            <span class="ml-auto text-red-600 font-bold">{{ $outOfStockCount }}</span>
                        </span>
                    </flux:menu.item>
                    @endif

                    @if($lowStockCount > 0)
                    <flux:menu.item 
                        :href="route('products.index', ['filter' => 'low_stock'])" 
                        wire:navigate 
                        class="text-yellow-600 dark:text-yellow-400">
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                            {{ __('Low Stock') }}
                            <span class="ml-auto text-yellow-500 font-bold">{{ $lowStockCount }}</span>
                        </span>
                    </flux:menu.item>
                    @endif
                </flux:menu>
            </flux:dropdown>
            @endif
            @endif

            @if($isCashier || $isManager)
            <!-- POS -->
            <flux:navbar.item icon="shopping-cart" 
                :href="route('pos.cashier')" 
                :current="request()->routeIs('pos.cashier')" 
                wire:navigate>
                {{ __('POS') }}
            </flux:navbar.item>

            <!-- Transaction History -->
            <flux:navbar.item icon="clock" 
                :href="route('pos.history')" 
                :current="request()->routeIs('pos.history')" 
                wire:navigate>
                {{ __('Transaction History') }}
                @if($pendingCount > 0)
                    <span class="ml-2 text-xs px-2 py-0.5 rounded-full bg-red-500 text-white">
                        {{ $pendingCount }}
                    </span>
                @endif
            </flux:navbar.item>

            <!-- Report -->
            <flux:navbar.item icon="document-text" 
                :href="route('report.index')" 
                :current="request()->routeIs('report.*')" 
                wire:navigate>
                {{ __('Report') }}
            </flux:navbar.item>
            @endif
        </flux:navbar>

        <flux:spacer />

        <!-- User Menu -->
        <flux:dropdown position="top" align="end">
            <flux:profile class="cursor-pointer" :initials="auth()->user()->initials()" />
            <flux:menu>
                <div class="p-2 text-sm font-normal">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                            <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                {{ auth()->user()->initials() }}
                            </span>
                        </span>
                        <div class="flex-1 flex flex-col text-start text-sm leading-tight">
                            <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                            <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                </div>

                <flux:menu.separator />

                <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                    {{ __('Settings') }}
                </flux:menu.item>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <!-- Main Content -->
    {{ $slot }}

    @fluxScripts
</body>
</html>
