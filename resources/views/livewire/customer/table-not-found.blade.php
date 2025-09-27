<div class="min-h-screen flex items-center justify-center p-6 bg-white">
    <div class="w-full max-w-md bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <div class="mx-auto w-12 h-12 flex items-center justify-center rounded-full bg-red-100 text-red-600">
            <!-- icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01M4.93 4.93l14.14 14.14M9 3h6a2 2 0 012 2v1H7V5a2 2 0 012-2zm10 5H5l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12z"/>
            </svg>
        </div>

        <h1 class="mt-4 text-xl font-bold text-gray-900 text-center">
            Meja Tidak Ditemukan
        </h1>
        <p class="mt-1 text-center text-gray-500 text-sm">QR mungkin tidak valid atau meja sudah dihapus.</p>

        @if($tableCode)
            <p class="mt-2 text-center text-gray-600">
                Kode meja
                <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 font-mono">{{ $tableCode }}</span>
                tidak tersedia.
            </p>
        @else
            <p class="mt-2 text-center text-gray-600">
                Meja yang Anda cari tidak tersedia.
            </p>
        @endif

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-2">
            <a href="{{ route('customer') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                Lihat Menu
            </a>
            <a href="{{ route('home') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Beranda
            </a>
        </div>

        @if($availableTables->count() > 0)
            <div class="mt-6">
                <h2 class="text-sm font-semibold text-gray-900 mb-2">Meja yang Tersedia</h2>
                <ul class="space-y-2 text-sm">
                    @foreach($availableTables as $t)
                        <li>
                            <a href="{{ route('customer.table', ['code' => $t->code]) }}"
                               class="flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2 hover:bg-gray-50 transition">
                                <div class="flex flex-col">
                                    <span class="text-gray-900 font-medium">{{ $t->name }}</span>
                                    <div class="mt-0.5 flex items-center gap-2">
                                        <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 font-mono text-xs">{{ $t->code }}</span>
                                        @if($t->seats)
                                        <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-xs">{{ $t->seats }} kursi</span>
                                        @endif
                                    </div>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p class="mt-6 text-xs text-gray-500 text-center">
            Jika Anda yakin kode sudah benar, silakan hubungi staff.
        </p>
    </div>
</div>