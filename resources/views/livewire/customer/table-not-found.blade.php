<div class="relative min-h-screen flex items-center justify-center p-6 bg-cover bg-center" style="background-image: url('{{ asset('images/coffee-shop-bg.jpg') }}')">
    <div class="absolute inset-0 bg-gradient-to-b from-black/40 to-black/60"></div>
    <div class="relative w-full max-w-md rounded-2xl p-6 shadow-xl ring-1 ring-black/10 bg-white/90 backdrop-blur-md dark:bg-zinc-900/80 dark:ring-white/10">
        <div class="mx-auto w-12 h-12 flex items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-300">
            <!-- icon meja -->
            <x-heroicon-o-table-cells class="h-6 w-6" />
        </div>

        <h1 class="mt-4 text-2xl font-bold text-center text-zinc-900 dark:text-white">Meja Tidak Ditemukan</h1>
        <p class="mt-1 text-center text-zinc-600 dark:text-zinc-300 text-sm">QR mungkin tidak valid atau meja sudah dihapus.</p>

        @if($tableCode)
            <p class="mt-3 text-center text-zinc-700 dark:text-zinc-200">
                Kode meja
                <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 font-mono dark:bg-red-900/40 dark:text-red-200">{{ $tableCode }}</span>
                tidak tersedia.
            </p>
        @endif

        @if($availableTables->count() > 0)
            <div class="mt-6">
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 mb-2">Meja yang Tersedia</h2>
                <ul class="space-y-2 text-sm">
                    @foreach($availableTables as $t)
                        <li>
                            <a href="{{ route('customer.table', ['code' => $t->code]) }}"
                               class="flex items-center justify-between rounded-lg border border-zinc-200 px-3 py-2 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-white/5 transition">
                                <div class="flex flex-col">
                                    <span class="text-zinc-900 dark:text-zinc-100 font-medium">{{ $t->name }}</span>
                                    <div class="mt-0.5 flex items-center gap-2">
                                        <span class="px-1.5 py-0.5 rounded bg-zinc-100 text-zinc-700 font-mono text-xs dark:bg-zinc-800 dark:text-zinc-300">{{ $t->code }}</span>
                                        @if($t->seats)
                                        <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-xs dark:bg-emerald-900/30 dark:text-emerald-300">{{ $t->seats }} kursi</span>
                                        @endif
                                    </div>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p class="mt-6 text-xs text-zinc-500 dark:text-zinc-400 text-center" aria-live="polite">
            Jika Anda yakin kode sudah benar, silakan hubungi kasir/manager.
        </p>
    </div>
</div>