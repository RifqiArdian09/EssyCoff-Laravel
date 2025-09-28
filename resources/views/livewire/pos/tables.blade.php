<div class="p-6 space-y-8 bg-white dark:bg-zinc-800 min-h-screen text-gray-900 dark:text-white">
    @once
    <script>
        // Define once globally so it survives Livewire updates
        window.printQr = function(size){
            const card = document.getElementById('qr-card');
            if(!card) return;

            // Explicit sizes in mm so it fits 1 page
            const qrImg = card.querySelector('img[alt^="QR "]');
            if(size === 'A5'){
                card.style.setProperty('width', '148mm', 'important');
                card.style.setProperty('padding', '8mm', 'important');
                if(qrImg){
                    qrImg.style.setProperty('width', '88mm', 'important');
                    qrImg.style.setProperty('height', '88mm', 'important');
                }
            } else {
                // A6 default
                card.style.setProperty('width', '105mm', 'important');
                card.style.setProperty('padding', '6mm', 'important');
                if(qrImg){
                    qrImg.style.setProperty('width', '54mm', 'important');
                    qrImg.style.setProperty('height', '54mm', 'important');
                }
            }

            // Inject a temporary @page rule to enforce paper size and zero margins
            const style = document.createElement('style');
            style.setAttribute('id', 'tmp-print-size');
            style.media = 'print';
            style.innerHTML = `@page { size: ${size}; margin: 0; }`;
            document.head.appendChild(style);

            // Trigger print
            window.print();

            // Cleanup the temporary style after a short delay
            setTimeout(() => {
                const s = document.getElementById('tmp-print-size');
                if (s) s.remove();
            }, 500);
        }
    </script>
    @endonce
    <!-- Judul -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Meja</h1>
            <p class="text-gray-600 dark:text-zinc-300">Kelola semua meja dan QR pelanggan</p>
        </div>
        <div class="flex items-center gap-2">
            <flux:button
                variant="primary"
                color="sky"
                icon="plus"
                wire:click="openCreate"
                size="sm">
                Tambah Meja
            </flux:button>
        </div>
    </div>

    

    <!-- Pencarian & Filter -->
    <div class="bg-white dark:bg-zinc-800 p-5 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700 space-y-4 transition-colors duration-200">
        <!-- Quick Filter Chips -->
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-sm text-gray-600 dark:text-zinc-400 me-2">Filter cepat:</span>
            <button
                class="px-3 py-1 rounded-full text-xs border transition
                    {{ $status === '' ? 'bg-gray-900 text-white dark:bg-white dark:text-black' : 'bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-200 border-gray-200 dark:border-zinc-600' }}"
                wire:click="$set('status','')">
                Semua
            </button>
            <button
                class="px-3 py-1 rounded-full text-xs border transition
                    {{ $status === 'available' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700' }}"
                wire:click="$set('status','available')">
                Tersedia
            </button>
            <button
                class="px-3 py-1 rounded-full text-xs border transition
                    {{ $status === 'unavailable' ? 'bg-red-600 text-white border-red-600' : 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 border-red-200 dark:border-red-700' }}"
                wire:click="$set('status','unavailable')">
                Tidak Tersedia
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Cari Meja</label>
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama atau kode meja..."
                    class="w-full"
                    icon="magnifying-glass" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Status</label>
                <flux:select wire:model.live="status" class="w-full">
                    <option value="">Semua Status</option>
                    <option value="available">Tersedia</option>
                    <option value="unavailable">Tidak Tersedia</option>
                </flux:select>
            </div>
        </div>
    </div>

    <!-- Tabel Meja -->
    <div class="bg-white dark:bg-zinc-800 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-zinc-700 transition-colors duration-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700 dark:text-zinc-200">
                <thead class="bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3 text-center">Kursi</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">QR</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse ($tables as $index => $t)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition duration-150 ease-in-out">
                        <td class="px-4 py-3 font-medium text-gray-500 dark:text-zinc-400">
                            {{ ($tables->currentPage() - 1) * $tables->perPage() + $index + 1 }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $t->name }}</div>
                            @if($t->note)
                            <div class="text-xs text-gray-600 dark:text-zinc-400 truncate max-w-xs">{{ $t->note }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-mono">
                            <div class="flex items-center gap-2">
                                <span class="bg-gray-100 dark:bg-zinc-700 px-2 py-1 rounded text-sm">{{ $t->code }}</span>
                                <button type="button" class="px-2 py-1 text-[10px] rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 hover:bg-blue-200 dark:hover:bg-blue-800/40 transition-colors"
                                    onclick="navigator.clipboard.writeText('{{ $t->code }}'); this.innerText='✓'; setTimeout(()=>this.innerText='Copy',1500);">Copy</button>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($t->seats)
                            <span class="px-2 py-1 rounded-full text-xs bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 font-medium">
                                {{ $t->seats }} kursi
                            </span>
                            @else
                            <span class="px-2 py-1 rounded-full text-xs bg-gray-100 dark:bg-zinc-700 text-gray-500 dark:text-zinc-400">
                                -
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($t->status === 'available')
                                <span class="px-2 py-1 bg-emerald-100 dark:bg-emerald-800/40 text-emerald-800 dark:text-emerald-300 rounded-full text-xs font-medium">Tersedia</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 dark:bg-red-800/40 text-red-800 dark:text-red-300 rounded-full text-xs font-medium">Tidak Tersedia</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <button type="button" class="group" wire:click="openQr('{{ $t->code }}')">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 rounded-lg border border-blue-200 dark:border-blue-700 flex items-center justify-center group-hover:scale-105 transition-all duration-200 shadow-sm">
                                        <img src="{{ $this->getQrUrl($t->code) }}" alt="QR {{ $t->code }}" class="w-12 h-12 rounded" />
                                    </div>
                                    <div class="text-[10px] text-blue-600 dark:text-blue-400 mt-1 font-medium">Lihat QR</div>
                                </div>
                            </button>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-1 justify-end">
                                <flux:button
                                    variant="primary"
                                    icon="pencil-square"
                                    wire:click="openEdit({{ $t->id }})"
                                    size="xs">
                                    Edit
                                </flux:button>
                                <flux:button
                                    variant="outline"
                                    icon="arrows-right-left"
                                    wire:click="toggleStatus({{ $t->id }})"
                                    size="xs"
                                    class="{{ $t->status === 'available' ? 'text-red-600 hover:text-red-700' : 'text-emerald-600 hover:text-emerald-700' }}">
                                    {{ $t->status === 'available' ? 'Tutup' : 'Buka' }}
                                </flux:button>
                                <flux:button
                                    variant="danger"
                                    icon="trash"
                                    wire:click="confirmDelete({{ $t->id }})"
                                    size="xs">
                                    Hapus
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-zinc-500">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                <span>Belum ada meja. Cobalah menambahkan meja baru.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($tables->hasPages())
        <div class="p-4 bg-gray-50 dark:bg-zinc-800 border-t border-gray-200 dark:border-zinc-700 transition-colors duration-200">
            <div class="flex flex-col md:flex-row items-center justify-between gap-3">
                <p class="text-sm text-gray-600 dark:text-zinc-400">
                    Menampilkan
                    <span class="font-medium text-gray-900 dark:text-white">{{ $tables->firstItem() }}</span>
                    –
                    <span class="font-medium text-gray-900 dark:text-white">{{ $tables->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $tables->total() }}</span>
                    data
                </p>
                <div class="[&>nav]:flex [&>nav]:items-center [&>nav]:gap-1">
                    {{ $tables->links('components.pagination.simple-arrows') }}
                </div>
            </div>
        </div>
        @endif
    </div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center" wire:click.self="$set('showModal', false)">
        <div class="relative bg-white dark:bg-zinc-800 rounded-lg shadow-xl max-w-md w-full mx-4 p-6 border border-gray-200 dark:border-zinc-700">
            <h3 class="text-lg font-bold mb-4">{{ $editing ? 'Edit Meja' : 'Tambah Meja' }}</h3>
            <div class="space-y-3">
                <flux:input label="Nama Meja" wire:model.live="name" placeholder="Meja A1" />
                <div class="grid grid-cols-2 gap-3">
                    <flux:input label="Kode" wire:model.live="code" placeholder="TBL-XXXXX" />
                    <flux:select label="Status" wire:model.live="state">
                        <option value="available">Tersedia</option>
                        <option value="unavailable">Tidak Tersedia</option>
                    </flux:select>
                </div>
                <flux:input label="Jumlah Kursi" type="number" min="1" wire:model.live="seats" placeholder="4" />
                <flux:input label="Catatan" wire:model.live="note" placeholder="Dekat jendela" />
                <div class="flex items-center gap-2">
                    <flux:button variant="outline" icon="arrow-path" wire:click="regenerateCode">Generate Ulang Kode</flux:button>
                    @if($code)
                    <img src="{{ $this->getQrUrl($code) }}" alt="QR Preview" class="w-16 h-16 rounded bg-white p-1">
                    @endif
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <flux:button variant="ghost" wire:click="$set('showModal', false)" class="flex-1">Batal</flux:button>
                <flux:button variant="primary" wire:click="save" class="flex-1">Simpan</flux:button>
            </div>
        </div>
    </div>
    @endif

    @if($showQrModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center" wire:click.self="closeQr">
        <div class="relative bg-white dark:bg-zinc-800 rounded-lg shadow-xl max-w-md w-full mx-4 p-6 border border-gray-200 dark:border-zinc-700 text-center">
            <h3 class="text-lg font-bold mb-4">QR Meja</h3>
            <div class="flex flex-col items-center gap-3">
                <!-- Printable Card -->
                <div id="qr-card" class="bg-white text-black rounded-lg shadow-lg border-2 border-gray-200 p-6 w-[105mm] hidden print:block print:shadow-none print:border-0">
                    
                    
                    <!-- Konten Utama -->
                    <div class="text-center space-y-3">
                        <div class="text-xl font-bold text-gray-800 mb-2">Scan untuk Order</div>
                        @if($qrName)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-2 mb-3">
                            <div class="text-sm text-blue-700"><span class="font-bold text-blue-800">{{ $qrName }}</span></div>
                        </div>
                        @endif
                        
                        <!-- QR Code dengan border yang lebih menarik -->
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-4 rounded-xl border-2 border-gray-300 mx-auto inline-block">
                            <img src="{{ $this->getQrUrl($qrCode, 360) }}" alt="QR {{ $qrCode }}" class="mx-auto bg-white p-2 rounded-lg shadow-sm" style="width: 65mm; height: 65mm;" />
                        </div>
                        
                        <!-- Info Meja -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mt-4">
                            <div class="text-sm font-medium text-gray-700">Kode Meja</div>
                            <div class="text-lg font-bold text-gray-900 font-mono">{{ $qrCode }}</div>
                        </div>
                    </div>
                </div>

                <!-- Screen Preview -->
                <img src="{{ $this->getQrUrl($qrCode, 300) }}" alt="QR {{ $qrCode }}" class="w-[300px] h-[300px] bg-white p-2 rounded shadow print:hidden" />
                <div class="flex flex-wrap justify-center gap-2 print:hidden">
                    <a href="{{ $this->getQrUrl($qrCode, 600, 'png') }}" download="QR-{{ $qrCode }}.png" class="inline-flex items-center px-3 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700 transition">
                        Download PNG
                    </a>
                    <a href="{{ $this->getQrUrl($qrCode, 600, 'svg') }}" download="QR-{{ $qrCode }}.svg" class="inline-flex items-center px-3 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition">
                        Download SVG
                    </a>
                    <button type="button" onclick="window.printQr('A6')" class="inline-flex items-center px-3 py-2 rounded bg-gray-800 text-white hover:bg-black transition">
                        Cetak Kartu A6
                    </button>
                    <button type="button" onclick="window.printQr('A5')" class="inline-flex items-center px-3 py-2 rounded bg-gray-600 text-white hover:bg-gray-700 transition">
                        Cetak Kartu A5
                    </button>
                </div>
                <p class="text-xs text-gray-500 dark:text-zinc-400 print:hidden">Tautan: {{ route('customer.table', ['code' => $qrCode]) }}</p>
            </div>
            <div class="mt-6">
                <flux:button variant="ghost" wire:click="closeQr" class="w-full">Tutup</flux:button>
            </div>
            <style>
                @media print {
                    @page {
                        size: auto; /* Will be overridden by tmp @page from printQr */
                        margin: 10mm;
                    }
                    /* Show only the printable card */
                    body * { visibility: hidden !important; }
                    #qr-card, #qr-card * { visibility: visible !important; }
                    /* Center the card without absolute positioning to prevent split */
                    #qr-card {
                        position: static !important;
                        margin: 0 auto !important;
                        transform: none !important;
                        box-shadow: none !important;
                        border: none !important;
                        page-break-inside: avoid !important;
                        line-height: 1.2 !important;
                    }
                    /* Shrink QR wrapper padding when printing */
                    #qr-card .qr-box { padding: 2mm !important; border-width: 1px !important; }
                }
            </style>
        </div>
    </div>
    @endif

    <!-- Modal Konfirmasi Hapus -->
    @if($confirmingDeletion)
    <div class="fixed inset-0 z-50 flex items-center justify-center" wire:click.self="cancelDelete">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="relative bg-white dark:bg-zinc-800 rounded-lg shadow-xl max-w-md w-full mx-4 p-6 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 dark:bg-red-900/30 rounded-full">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-center mb-2 text-gray-900 dark:text-white">Konfirmasi Hapus</h3>
            <p class="text-center text-gray-600 dark:text-zinc-300 mb-6">
                Apakah Anda yakin ingin menghapus meja ini? Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="flex gap-3">
                <flux:button variant="ghost" wire:click="cancelDelete" class="flex-1">
                    Batal
                </flux:button>
                <flux:button variant="danger" wire:click="deleteConfirmed" class="flex-1">
                    Ya, Hapus
                </flux:button>
            </div>
        </div>
    </div>
    @endif
</div>
