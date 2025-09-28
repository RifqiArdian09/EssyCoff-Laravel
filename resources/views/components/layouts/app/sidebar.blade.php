<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">

    @if(auth()->check() && !request()->routeIs('home'))
    <!-- Sidebar -->
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        @php
        $user = auth()->user();
        $isManager = $user && $user->role === 'manager';
        $isCashier = $user && $user->role === 'cashier';
        @endphp

        <a href="{{ $isCashier ? route('pos.cashier') : route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>
        

        <flux:navlist variant="outline"
            x-data="{
                openMgmt: {{ (request()->routeIs('dashboard') || request()->routeIs('categories.*') || request()->routeIs('products.*') || request()->routeIs('users.*')) ? 'true' : 'false' }},
                openReport: {{ (request()->routeIs('report.*')) ? 'true' : 'false' }},
                openPos: {{ (request()->routeIs('pos.*')) ? 'true' : 'false' }},
                pendingCount: {{ $pendingBadge ?? 0 }},
                init() {
                    const key = (name) => `sidebar:${name}`;
                    const getBool = (k, def) => {
                        try { const v = localStorage.getItem(k); return v === null ? def : (v === 'true'); } catch { return def; }
                    };
                    this.openMgmt = getBool(key('mgmt'), this.openMgmt);
                    this.openReport = getBool(key('report'), this.openReport);
                    this.openPos = getBool(key('pos'), this.openPos);
                    this.$watch('openMgmt', v => { try { localStorage.setItem(key('mgmt'), String(v)); } catch {} });
                    this.$watch('openReport', v => { try { localStorage.setItem(key('report'), String(v)); } catch {} });
                    this.$watch('openPos', v => { try { localStorage.setItem(key('pos'), String(v)); } catch {} });
                    // Live update pending transaction count
                    window.addEventListener('pending-count', (e) => {
                        const c = Number((e && e.detail && e.detail.count) ?? 0);
                        this.pendingCount = isNaN(c) ? 0 : c;
                    });
                }
            }"
        >
            @php
            // Ambil data dari AppServiceProvider
            $pendingCount = $pendingCount ?? 0;
            $lowStockCount = $lowStockCount ?? 0;
            $outOfStockCount = $outOfStockCount ?? 0;
            
            // Badge counts untuk menu
            $pendingBadge = $pendingCount > 0 ? $pendingCount : null;
            $stockBadge = ($lowStockCount + $outOfStockCount) > 0 ? ($lowStockCount + $outOfStockCount) : null;
            @endphp

            @if($isManager)
            <div class="px-3 pt-4 pb-2 text-xs font-semibold uppercase tracking-wider flex items-center justify-between cursor-pointer select-none transition-colors duration-200 hover:text-emerald-600 dark:hover:text-emerald-400"
                 :class="openMgmt ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-500 dark:text-zinc-400'"
                 @click="openMgmt = !openMgmt">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 opacity-70" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 3H3v7h7V3ZM21 3h-7v7h7V3ZM10 14H3v7h7v-7ZM21 14h-7v7h7v-7Z"/>
                    </svg>
                    <span>Manajemen</span>
                </div>
                <svg class="h-3.5 w-3.5 transition-transform duration-300 opacity-70" :class="openMgmt ? 'rotate-90' : ''" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M7 5l6 5-6 5V5z"/></svg>
            </div>
            <div x-show="openMgmt" x-transition.duration.300ms>
            <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </flux:navlist.item>
            
                <flux:navlist.item icon="squares-2x2" :href="route('categories.index')" :current="request()->routeIs('categories.*')" wire:navigate>
                    {{ __('Kategori') }}
                </flux:navlist.item>
                
                <!-- Products dengan stock alert -->
                @if($stockBadge)
                <flux:navlist.item 
                    icon="cube" 
                    :href="route('products.index')" 
                    :current="request()->routeIs('products.*')" 
                    wire:navigate
                    class="relative">
                    {{ __('Produk') }}
                </flux:navlist.item>
                @else
                <flux:navlist.item icon="cube" :href="route('products.index')" :current="request()->routeIs('products.*')" wire:navigate>
                    {{ __('Produk') }}
                </flux:navlist.item>
            @endif
                
                <flux:navlist.item icon="users" :href="route('users.index')" :current="request()->routeIs('users.*')" wire:navigate>
                    {{ __('Pengguna') }}
                </flux:navlist.item>
            

            <!-- Stock Alerts Section (Khusus Manager) -->
            @if(($lowStockCount > 0 || $outOfStockCount > 0))
            
                @if($outOfStockCount > 0)
                <flux:navlist.item 
                    icon="exclamation-triangle" 
                    :href="route('products.index', ['filter' => 'out_of_stock'])" 
                    wire:navigate
                    badge="{{ $outOfStockCount }}"
                    class="text-red-600 dark:text-red-400">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                        {{ __('Stok Habis') }}
                    </span>
                </flux:navlist.item>
                @endif
                
                @if($lowStockCount > 0)
                <flux:navlist.item 
                    icon="exclamation-circle" 
                    :href="route('products.index', ['filter' => 'low_stock'])" 
                    wire:navigate
                    badge="{{ $lowStockCount }}"
                    class="text-yellow-600 dark:text-yellow-400">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                        {{ __('Stok Menipis') }}
                    </span>
                </flux:navlist.item>
                @endif
            
            @endif
            </div>
            <div class="mx-3 my-2 h-px bg-zinc-200 dark:bg-zinc-700/60"></div>
            <div class="px-3 pt-4 pb-2 text-xs font-semibold uppercase tracking-wider flex items-center justify-between cursor-pointer select-none transition-colors duration-200 hover:text-emerald-600 dark:hover:text-emerald-400"
                 :class="openReport ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-500 dark:text-zinc-400'"
                 @click="openReport = !openReport">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 opacity-70" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19.5 3h-15A1.5 1.5 0 0 0 3 4.5v15A1.5 1.5 0 0 0 4.5 21h15a1.5 1.5 0 0 0 1.5-1.5v-15A1.5 1.5 0 0 0 19.5 3Zm-3 12h-9v-1.5h9V15Zm0-3h-9V9.5h9V12Zm0-3h-9V6.5h9V9Z"/>
                    </svg>
                    <span>Laporan</span>
                </div>
                <svg class="h-3.5 w-3.5 transition-transform duration-300 opacity-70" :class="openReport ? 'rotate-90' : ''" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M7 5l6 5-6 5V5z"/></svg>
            </div>
            <div x-show="openReport" x-transition.duration.300ms>
            <flux:navlist.item icon="document-text" :href="route('report.index')" :current="request()->routeIs('report.*')" wire:navigate>
                {{ __('Laporan') }}
            </flux:navlist.item>
            </div>
            @endif

            @if($isCashier || $isManager)
                <div class="mx-3 my-2 h-px bg-zinc-200 dark:bg-zinc-700/60"></div>
                <div class="px-3 pt-4 pb-2 text-xs font-semibold uppercase tracking-wider flex items-center justify-between cursor-pointer select-none transition-colors duration-200 hover:text-emerald-600 dark:hover:text-emerald-400"
                     :class="openPos ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-500 dark:text-zinc-400'"
                     @click="openPos = !openPos">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 opacity-70" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7 4h10a1 1 0 0 1 .96.73l2.5 8.5A2 2 0 0 1 18.54 16H8.1l.57 3H18v2H8a2 2 0 0 1-1.97-1.64L4.1 4H2V2h3a1 1 0 0 1 .98.8L6.3 6H20v2H6.86l.8 3H18.54l-1.5-5H7V4Z"/>
                        </svg>
                        <span>POS</span>
                    </div>
                    <span x-show="pendingCount > 0" x-text="pendingCount"
                          class="mr-2 inline-flex min-w-[1.25rem] h-4 items-center justify-center rounded-full bg-emerald-600 px-1 text-[10px] font-semibold text-white dark:bg-emerald-500"
                          @click.stop="window.location.assign('{{ route('pos.history') }}')"
                          title="Riwayat Transaksi"></span>
                    <svg class="h-3.5 w-3.5 transition-transform duration-300 opacity-70" :class="openPos ? 'rotate-90' : ''" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M7 5l6 5-6 5V5z"/></svg>
                </div>
                <div x-show="openPos" x-transition.duration.300ms>
                <flux:navlist.item icon="shopping-cart" :href="route('pos.cashier')" :current="request()->routeIs('pos.cashier')" wire:navigate>
                    {{ __('Order') }}
                </flux:navlist.item>

                <!-- Tables Management -->
                <flux:navlist.item
                    icon="table-cells"
                    :href="route('pos.tables')"
                    :current="request()->routeIs('pos.tables')"
                    wire:navigate>
                    {{ __('Meja') }}
                </flux:navlist.item>

                <!-- Transaction History dengan badge live (Alpine) -->
                <flux:navlist.item
                    icon="clock"
                    :href="route('pos.history')"
                    :current="request()->routeIs('pos.history')"
                    wire:navigate>
                    <span class="inline-flex items-center gap-2 w-full">
                        <span>{{ __('Riwayat Transaksi') }}</span>
                        <span x-show="pendingCount > 0" x-text="pendingCount"
                              class="ml-auto inline-flex min-w-[1.5rem] h-5 items-center justify-center rounded-full bg-emerald-600 px-1 text-xs font-semibold text-white dark:bg-emerald-500"
                              aria-live="polite"></span>
                    </span>
                </flux:navlist.item>

                
                </div>
            @endif
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
                    <flux:menu.item as="button" type="submit" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>
    @endif

    @if(auth()->check())
    <!-- Mobile Header -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
        <div class="ml-2" x-data="{ pending: {{ $pendingBadge ?? 0 }} }" x-init="window.addEventListener('pending-count', e => { const c = Number((e && e.detail && e.detail.count) ?? 0); pending = isNaN(c) ? 0 : c; })">
            <a href="{{ route('pos.history') }}" class="relative inline-flex items-center justify-center rounded-md p-2 text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white" wire:navigate aria-label="Riwayat Transaksi">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 8v5l3 3 1.5-1.5L14 12.75V8h-2Zm0-6a10 10 0 1 1 0 20 10 10 0 0 1 0-20Z"/></svg>
                <span x-show="pending > 0" x-text="pending" class="absolute -top-1.5 -right-1.5 inline-flex min-w-[1.25rem] h-5 items-center justify-center rounded-full bg-emerald-600 px-1 text-[11px] font-semibold text-white ring-2 ring-zinc-50 dark:bg-emerald-500 dark:ring-zinc-900" aria-live="polite"></span>
            </a>
        </div>
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

                <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                    {{ __('Settings') }}
                </flux:menu.item>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>
    @endif

    <!-- Main Content -->
    {{ $slot }}

    @php
        $user = auth()->user();
        $isManager = $user && $user->role === 'manager';
        $isCashier = $user && $user->role === 'cashier';
    @endphp

    @if($isManager || $isCashier)
        <!-- Live incoming order notifier: polls for new customer orders -->
        <livewire:pos.incoming-order-notifier />
    @endif

    @fluxScripts

    @if($isManager || $isCashier)
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
                    // Normalize Livewire payloads: sometimes detail can be an array of args
                    const payload = Array.isArray(toast) ? (toast[0] || {}) : (toast || {});
                    const typeRaw = ((payload && payload.type) ? String(payload.type) : 'success').toLowerCase();
                    const allowed = ['success','error','warning'];
                    const normType = allowed.includes(typeRaw) ? typeRaw : 'success';
                    const item = { id, title: payload.title ?? null, message: payload.message ?? '', type: normType, timeout: payload.timeout ?? 3000, tid: null, href: payload.href ?? null };
                    this.toasts.push(item);
                    if (payload.playSound) { this.playDing(); }
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
                               'bg-emerald-100 text-emerald-700 dark:bg-emerald-800 dark:text-emerald-200': t.type === 'success',
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
                    <button class="shrink-0 opacity-70 hover:opacity-100 p-1 rounded-md hover:bg-black/5 dark:hover:bg-white/5" @click.stop="remove(t.id)" aria-label="Close">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>
        </div>
    @endif

    @if(session()->has('message'))
    <div x-data x-init="setTimeout(() => { try { window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', title: 'Berhasil', message: $el.dataset.msg, timeout: 3000, playSound: false } })); } catch(e){} }, 10)" data-msg='@json(session('message'))'></div>
    @endif
    @if(session()->has('success'))
    <div x-data x-init="setTimeout(() => { try { window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', title: 'Berhasil', message: $el.dataset.msg, timeout: 3000, playSound: false } })); } catch(e){} }, 10)" data-msg='@json(session('success'))'></div>
    @endif
    @if(session()->has('error'))
    <div x-data x-init="setTimeout(() => { try { window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', title: 'Gagal', message: $el.dataset.msg, timeout: 4000, playSound: false } })); } catch(e){} }, 10)" data-msg='@json(session('error'))'></div>
    @endif
    @if(session()->has('warning'))
    <div x-data x-init="setTimeout(() => { try { window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'warning', title: 'Perhatian', message: $el.dataset.msg, timeout: 3500, playSound: false } })); } catch(e){} }, 10)" data-msg='@json(session('warning'))'></div>
    @endif
</body>

</html>