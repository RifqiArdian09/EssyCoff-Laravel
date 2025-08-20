<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">

    <!-- Sidebar -->
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

    <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
        <x-app-logo />
    </a>

    <flux:navlist variant="outline">
        <flux:navlist.group :heading="__('Platform')">
            <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </flux:navlist.item>
        </flux:navlist.group>

        <flux:navlist.group :heading="__('Master Data')">
            <flux:navlist.item icon="folder" :href="route('categories.index')" :current="request()->routeIs('categories.*')" wire:navigate>
                {{ __('Categories') }}
            </flux:navlist.item>
            <flux:navlist.item icon="archive-box" :href="route('products.index')" :current="request()->routeIs('products.*')" wire:navigate>
                {{ __('Products') }}
            </flux:navlist.item>
        </flux:navlist.group>

        <flux:navlist.group :heading="__('Transaksi')">
            <flux:navlist.item icon="shopping-cart" :href="route('pos.cashier')" :current="request()->routeIs('pos.cashier')" wire:navigate>
                {{ __('POS') }}
            </flux:navlist.item>
            <flux:navlist.item icon="clock" :href="route('pos.history')" :current="request()->routeIs('pos.history')" wire:navigate>
                {{ __('Transaction History') }}
            </flux:navlist.item>
            <flux:navlist.item icon="document-text" :href="route('report.index')" :current="request()->routeIs('report.*')" wire:navigate>
                {{ __('Report') }}
            </flux:navlist.item>
        </flux:navlist.group>
    </flux:navlist>

    <flux:spacer />

    <flux:dropdown class="hidden lg:block" position="bottom" align="start">
        <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()" icon-trailing="chevrons-up-down" />

        <flux:menu class="w-[220px]">
            <div class="p-2">
                <span class="font-semibold">{{ auth()->user()->name }}</span><br>
                <span class="text-xs text-gray-500">{{ auth()->user()->email }}</span>
            </div>

            <flux:menu.separator />

            <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                {{ __('Settings') }}
            </flux:menu.item>

            <flux:menu.separator />

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:menu.item as="button" type="submit"  class="w-full">
                    {{ __('Log Out') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:sidebar>


    <!-- Mobile Header -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu class="w-[220px]">
                <!-- User Info -->
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

                <!-- Settings -->
                <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                    {{ __('Settings') }}
                </flux:menu.item>

                <flux:menu.separator />

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit"  class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <!-- Main Content Slot -->
    {{ $slot }}

    @fluxScripts
</body>
</html>
