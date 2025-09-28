<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800" x-data="{ openMobileSidebar: false }">
    @php
        $user = auth()->user();
        $isManager = $user && $user->role === 'manager';
        $isCashier = $user && $user->role === 'cashier';
    @endphp

    <!-- Mobile-only Sidebar so the hamburger works -->
    <!-- Backdrop -->
    <div x-show="openMobileSidebar" x-transition.opacity class="fixed inset-0 z-40 bg-black/40 lg:hidden" @click="openMobileSidebar = false"></div>

    <flux:sidebar sticky stashable class="lg:hidden fixed left-0 top-0 h-screen z-50 w-72 max-w-[85vw] border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" @click="openMobileSidebar = false" />
        <a href="{{ $isCashier ? route('pos.cashier') : route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>
        <flux:navlist variant="outline" class="p-2">
            @if($isManager)
                <flux:navlist.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dasbor') }}
                </flux:navlist.item>
            @endif

            @if($isCashier || $isManager)
                <flux:navlist.item icon="shopping-cart" :href="route('pos.cashier')" :current="request()->routeIs('pos.cashier')" wire:navigate>
                    {{ __('Order') }}
                </flux:navlist.item>
                <flux:navlist.item icon="table-cells" :href="route('pos.tables')" :current="request()->routeIs('pos.tables')" wire:navigate>
                    {{ __('Meja') }}
                </flux:navlist.item>
                <flux:navlist.item icon="clock" :href="route('pos.history')" :current="request()->routeIs('pos.history')" wire:navigate>
                    {{ __('Riwayat Transaksi') }}
                </flux:navlist.item>
            @endif

            @if($isManager)
                <flux:navlist.item icon="document-text" :href="route('report.index')" :current="request()->routeIs('report.*')" wire:navigate>
                    {{ __('Laporan') }}
                </flux:navlist.item>
            @endif
        </flux:navlist>
    </flux:sidebar>
    <!-- Header -->
    <flux:header container class="relative z-30 border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <!-- Toggle sidebar untuk mobile -->
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" @click="openMobileSidebar = !openMobileSidebar" />

        <!-- Logo -->
        <a href="{{ ($user = auth()->user()) && $user->role === 'cashier' ? route('pos.cashier') : route('dashboard') }}" 
           class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" 
           wire:navigate>
            <x-app-logo />
        </a>

        <!-- Navbar -->
        <flux:navbar class="-mb-px max-lg:hidden"
            x-data="{
                pendingCount: {{ ($pendingCount ?? 0) }},
                init() {
                    window.addEventListener('pending-count', (e) => {
                        const c = Number((e && e.detail && e.detail.count) ?? 0);
                        this.pendingCount = isNaN(c) ? 0 : c;
                    });
                }
            }"
        >
            @php
            $user = auth()->user();
            $isManager = $user && $user->role === 'manager';
            $isCashier = $user && $user->role === 'cashier';

            $pendingCount = $pendingCount ?? 0;
            $lowStockCount = $lowStockCount ?? 0;
            $outOfStockCount = $outOfStockCount ?? 0;
            @endphp

            @if($isManager)
            <flux:navbar.item icon="layout-grid" 
                :href="route('dashboard')" 
                :current="request()->routeIs('dashboard')" 
                wire:navigate>
                {{ __('Dasbor') }}
            </flux:navbar.item>
            @endif

            @if($isManager)
            <!-- Produk -->
            <flux:navbar.item 
                icon="archive-box" 
                :href="route('products.index')" 
                :current="request()->routeIs('products.*')" 
                wire:navigate>
                {{ __('Produk') }}
            </flux:navbar.item>

            <!-- Pengguna -->
            <flux:navbar.item 
                icon="users" 
                :href="route('users.index')" 
                :current="request()->routeIs('users.*')" 
                wire:navigate>
                {{ __('Pengguna') }}
            </flux:navbar.item>

            <!-- Peringatan Stok -->
            @if(($lowStockCount > 0 || $outOfStockCount > 0))
            <flux:dropdown>
                <flux:navbar.item icon="exclamation-triangle">
                    {{ __('Peringatan Stok') }}
                </flux:navbar.item>

                <flux:menu class="w-[220px]">
                    @if($outOfStockCount > 0)
                    <flux:menu.item 
                        :href="route('products.index', ['filter' => 'out_of_stock'])" 
                        wire:navigate 
                        class="text-red-600 dark:text-red-400">
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                            {{ __('Stok Habis') }}
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
                            {{ __('Stok Menipis') }}
                            <span class="ml-auto text-yellow-500 font-bold">{{ $lowStockCount }}</span>
                        </span>
                    </flux:menu.item>
                    @endif
                </flux:menu>
            </flux:dropdown>
            @endif
            @endif

            @if($isCashier || $isManager)
            <!-- Order -->
            <flux:navbar.item icon="shopping-cart" 
                :href="route('pos.cashier')" 
                :current="request()->routeIs('pos.cashier')" 
                wire:navigate>
                {{ __('Order') }}
            </flux:navbar.item>

            <!-- Meja -->
            <flux:navbar.item icon="table-cells"
                :href="route('pos.tables')"
                :current="request()->routeIs('pos.tables')"
                wire:navigate>
                {{ __('Meja') }}
            </flux:navbar.item>

            <!-- Riwayat Transaksi -->
            <flux:navbar.item icon="clock" 
                :href="route('pos.history')" 
                :current="request()->routeIs('pos.history')" 
                wire:navigate>
                <span class="inline-flex items-center gap-2">
                    <span>{{ __('Riwayat Transaksi') }}</span>
                    <span x-show="pendingCount > 0" x-text="pendingCount" class="ml-2 text-xs px-2 py-0.5 rounded-full bg-emerald-600 text-white dark:bg-emerald-500" aria-live="polite"></span>
                </span>
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

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Keluar') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <!-- Main Content -->
    {{ $slot }}

    @php
        $user = auth()->user();
        $isCashier = $user && $user->role === 'cashier';
        $isManager = $user && $user->role === 'manager';
    @endphp

    @if($isCashier || $isManager)
        <!-- Live incoming order notifier: polls for new customer orders -->
        <livewire:pos.incoming-order-notifier />
    @endif

    @fluxScripts

    <!-- Global Toasts (Alpine) -->
    <div x-data="{
                toasts: [],
                playDing() {
                    try {
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
                        const o = ctx.createOscillator();
                        const g = ctx.createGain();
                        o.connect(g); g.connect(ctx.destination);
                        o.type = 'sine'; o.frequency.value = 880;
                        const now = ctx.currentTime;
                        g.gain.setValueAtTime(0.001, now);
                        g.gain.exponentialRampToValueAtTime(0.2, now + 0.01);
                        g.gain.exponentialRampToValueAtTime(0.0001, now + 0.3);
                        o.start(); o.stop(now + 0.35);
                    } catch (e) { /* ignore */ }
                },
                add(toast) {
                    const id = Date.now() + Math.random();
                    const item = { id, title: toast.title ?? null, message: toast.message ?? '', type: toast.type ?? 'default', timeout: toast.timeout ?? 3000, tid: null, href: toast.href ?? null };
                    this.toasts.push(item);
                    if (toast.playSound) { this.playDing(); }
                    item.tid = setTimeout(() => { this.remove(id); }, item.timeout);
                },
                remove(id) {
                    const t = this.toasts.find(tt => tt.id === id);
                    if (t && t.tid) clearTimeout(t.tid);
                    this.toasts = this.toasts.filter(t => t.id !== id);
                },
                open(t) {
                    if (t.href) { window.location.assign(t.href); }
                }
            }"
            x-on:toast.window="add($event.detail)"
            class="pointer-events-none fixed top-4 right-4 z-[80] space-y-2"
            aria-live="polite" aria-atomic="true"
        >
            <template x-for="t in toasts" :key="t.id">
                <div
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="translate-y-2 opacity-0"
                    x-transition:enter-end="translate-y-0 opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="pointer-events-auto min-w-[260px] max-w-sm rounded-md border p-3 pr-2 shadow-xl ring-1 ring-black/5 flex items-start gap-3 backdrop-blur-sm cursor-pointer"
                    :class="{
                        'bg-emerald-50 border-emerald-200 text-emerald-900 dark:bg-emerald-900/30 dark:border-emerald-700 dark:text-emerald-100': t.type === 'success',
                        'bg-red-50 border-red-200 text-red-900 dark:bg-red-900/30 dark:border-red-700 dark:text-red-100': t.type === 'error',
                        'bg-amber-50 border-amber-200 text-amber-900 dark:bg-amber-900/30 dark:border-amber-700 dark:text-amber-100': t.type === 'warning',
                        'bg-white border-gray-200 text-gray-800 dark:bg-zinc-800/95 dark:border-zinc-700 dark:text-zinc-100': !['success','error','warning'].includes(t.type),
                    }"
                    role="status"
                    @click="open(t)"
                    tabindex="0"
                >
                    <div class="shrink-0 mt-0.5">
                        <div class="h-6 w-6 rounded-full flex items-center justify-center"
                             :class="{
                            
                               'bg-red-100 text-red-700 dark:bg-red-800 dark:text-red-200': t.type === 'error',
                               'bg-amber-100 text-amber-700 dark:bg-amber-800 dark:text-amber-200': t.type === 'warning',
                               'bg-gray-100 text-gray-600 dark:bg-zinc-700 dark:text-zinc-200': !['success','error','warning'].includes(t.type),
                             }">
                            <svg x-show="t.type === 'success'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <svg x-show="t.type === 'error'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <svg x-show="t.type === 'warning'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5 19h14l-7-14-7 14z"/></svg>
                            <svg x-show="!['success','error','warning'].includes(t.type)" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/></svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p x-show="t.title" class="text-sm font-semibold leading-5" x-text="t.title"></p>
                        <p class="text-sm leading-5" x-text="t.message"></p>
                    </div>
                    <button class="shrink-0 opacity-70 hover:opacity-100 p-1 rounded-md hover:bg-black/5 dark:hover:bg-white/5" @click="remove(t.id)" aria-label="Close">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>
        </div>
</body>
</html>
