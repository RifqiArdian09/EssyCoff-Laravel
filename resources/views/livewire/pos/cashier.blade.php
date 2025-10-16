<div class="p-6 space-y-8 bg-white dark:bg-zinc-800 min-h-screen text-gray-900 dark:text-white" x-data="{ loading: false }" x-on:toast.window="loading = false">
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Left Panel - Products -->
        <section class="lg:w-3/5">
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700 space-y-6">

                <!-- Modal Produk Habis -->
                @if($showOutOfStockModal)
                <div
                    class="fixed inset-0 z-[60] overflow-y-auto"
                    aria-labelledby="outofstock-title"
                    role="dialog"
                    aria-modal="true">
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <!-- Background overlay -->
                        <div class="fixed inset-0 bg-black/50"></div>
                        <!-- Modal panel -->
                        <div class="relative bg-white dark:bg-zinc-800 rounded-lg p-6 w-full max-w-sm z-[70]">
                            <div class="text-center">
                                <h3 id="outofstock-title" class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    Produk Habis
                                </h3>
                                <p class="text-gray-500 dark:text-zinc-400 mb-6">
                                    Stok {{ $outOfStockName ?: 'produk' }} habis.
                                </p>
                                <div class="flex justify-center gap-3">
                                    <button
                                        wire:click="closeOutOfStockModal"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <!-- Search Input -->
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari produk..."
                    icon="magnifying-glass" />
                <!-- Category Filter -->
                <div class="flex gap-2 pb-1 overflow-x-auto lg:overflow-visible">
                    <flux:button
                        variant="{{ $categoryId === null ? 'filled' : 'outline' }}"
                        size="sm"
                        wire:click="filterCategory(null)"
                        class="{{ $categoryId === null 
                            ? 'bg-red-600 hover:bg-red-700 text-white dark:bg-red-500 dark:hover:bg-red-600' 
                            : 'text-gray-700 dark:text-zinc-300 hover:text-gray-900 dark:hover:text-white' }}">
                        Semua
                    </flux:button>
                    @foreach($categories as $category)
                    <flux:button
                        variant="{{ $categoryId == $category->id ? 'filled' : 'outline' }}"
                        size="sm"
                        wire:click="filterCategory({{ $category->id }})"
                        class="{{ $categoryId == $category->id 
                                ? 'bg-red-600 hover:bg-red-700 text-white dark:bg-red-500 dark:hover:bg-red-600' 
                                : 'text-gray-700 dark:text-zinc-300 hover:text-gray-900 dark:hover:text-white' }}">
                        {{ $category->name }}
                    </flux:button>
                    @endforeach
                </div>
                <!-- Products Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @forelse($products as $product)
                    <div
                        wire:click="addToCart({{ $product->id }})"
                        class="bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 p-3 rounded-lg shadow hover:shadow-lg cursor-pointer transition duration-200 flex flex-col items-center space-y-2 {{ $product->stock <= 0 ? 'opacity-50 cursor-not-allowed' : 'hover:scale-105' }}"
                        {{ $product->stock <= 0 ? 'wire:click="openOutOfStockModal(\'' . addslashes($product->name) . '\')"' : '' }}>
                        @if($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded-full">
                        @else
                        <div class="w-16 h-16 bg-gray-200 dark:bg-zinc-700 flex items-center justify-center rounded-full">
                            <span class="text-gray-500 dark:text-zinc-400 text-xs">No Image</span>
                        </div>
                        @endif
                        <div class="text-center space-y-1">
                            <h3 class="font-medium text-sm text-gray-900 dark:text-white truncate">
                                {{ $product->name }}
                            </h3>
                            <p class="text-blue-600 dark:text-blue-400 font-bold text-sm">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                            @if($product->stock > 0)
                            <span class="text-xs {{ $product->stock <= 5 ? 'text-red-500' : 'text-emerald-600 dark:text-emerald-400' }}">
                                Stok: {{ $product->stock }}
                            </span>
                            @else
                            <span class="text-xs text-red-500">Habis</span>
                            @endif
                            @if($product->category)
                            <span class="text-xs text-gray-500 dark:text-zinc-400 block">
                                {{ $product->category->name }}
                            </span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-8 text-gray-500 dark:text-zinc-400">
                        <p class="font-medium">Tidak ada produk ditemukan</p>
                        <p class="mt-1 text-sm">Coba ubah kata kunci pencarian</p>
                    </div>
                    @endforelse
                </div>
                <div class="mt-4">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-3">
                        <p class="text-sm text-gray-600 dark:text-zinc-400">
                            Menampilkan
                            <span class="font-medium text-gray-900 dark:text-white">{{ $products->firstItem() }}</span>
                            –
                            <span class="font-medium text-gray-900 dark:text-white">{{ $products->lastItem() }}</span>
                            dari
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $products->total() }}</span>
                            data
                        </p>
                        <div class="[&>nav]:flex [&>nav]:items-center [&>nav]:gap-1">
                            {{ $products->links('components.pagination.simple-arrows') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Right Panel - Cart & Payment -->
        <section class="lg:w-2/5 space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0L17 21"></path>
                        </svg>
                        Keranjang
                        @if(count($cart) > 0)
                        <span class="text-xs bg-red-500 text-white px-2 py-1 rounded-full ml-2">{{ count($cart) }}</span>
                        @endif
                    </h2>
                    @if(!empty($cart))
                    <flux:button
                        wire:click="clearCartWithConfirm"
                        variant="danger"
                        size="sm"
                        icon="trash"
                        class="flex items-center gap-1">
                        Kosongkan
                    </flux:button>
                    @endif
                </div>
                <!-- Error Message -->
                @if(session()->has('error'))
                <div class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-2 rounded-lg text-sm mb-4">
                    {{ session('error') }}
                </div>
                @endif
                <div class="max-h-80 overflow-y-auto pr-2">
                    @if(empty($cart))
                    <div class="text-center py-8 text-gray-500 dark:text-zinc-400">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-zinc-700 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <p class="font-medium">Keranjang masih kosong</p>
                        <p class="mt-1 text-sm">Klik produk untuk menambah ke keranjang</p>
                    </div>
                    @else
                    <div class="space-y-3">
                        @foreach($cart as $id => $item)
                        <div class="bg-gray-50 dark:bg-zinc-800 p-4 rounded-lg border border-gray-200 dark:border-zinc-700 transition-all duration-200 hover:shadow-md">
                            <div class="flex justify-between items-start">
                                <!-- Gambar + Detail -->
                                <div class="flex items-start space-x-3 flex-1">
                                    <div class="relative">
                                        @if($item['image'])
                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-14 h-14 object-cover rounded-lg bg-gray-100 dark:bg-zinc-700">
                                        @else
                                        <div class="w-14 h-14 bg-gray-200 dark:bg-zinc-700 flex items-center justify-center rounded-lg">
                                            <span class="text-gray-500 dark:text-zinc-400 text-xs">No Image</span>
                                        </div>
                                        @endif
                                        <div class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                                            {{ $item['qty'] }}
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-medium text-gray-900 dark:text-white truncate">
                                            {{ $item['name'] }}
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-zinc-300 mt-1">
                                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                                        </p>
                                        <div class="flex items-center mt-2">
                                            <button
                                                wire:click="updateQuantity({{ $id }}, {{ $item['qty'] - 1 }})"
                                                class="p-1 bg-gray-200 dark:bg-zinc-700 rounded-lg hover:bg-gray-300 dark:hover:bg-zinc-600 transition text-sm w-6 h-6 flex items-center justify-center"
                                                {{ $item['qty'] <= 1 ? 'disabled' : '' }}>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                            </button>
                                            <span class="px-2 text-sm font-medium text-gray-900 dark:text-white min-w-[30px] text-center">
                                                {{ $item['qty'] }}
                                            </span>
                                            <button
                                                wire:click="updateQuantity({{ $id }}, {{ $item['qty'] + 1 }})"
                                                class="p-1 bg-gray-200 dark:bg-zinc-700 rounded-lg hover:bg-gray-300 dark:hover:bg-zinc-600 transition text-sm w-6 h-6 flex items-center justify-center"
                                                {{ $item['qty'] >= $item['stock'] ? 'disabled' : '' }}>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </button>
                                            @if($item['qty'] >= $item['stock'])
                                            <span class="text-xs text-red-500 ml-2">Stok terbatas</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <!-- Harga Total & Hapus -->
                                <div class="flex flex-col items-end justify-between h-full ml-4">
                                    <button
                                        wire:click="removeFromCart({{ $id }})"
                                        class="text-red-500 hover:text-red-700 dark:hover:text-red-400 transition p-1 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-md mb-2"
                                        title="Hapus item">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    <span class="font-bold text-blue-600 dark:text-blue-400 text-sm">
                                        Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                <!-- Cart Summary -->
                @if(!empty($cart))
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                    <div class="flex justify-between items-center text-sm text-gray-600 dark:text-zinc-400 mb-1">
                        <span>Total Item:</span>
                        <span>{{ array_sum(array_column($cart, 'qty')) }} item</span>
                    </div>
                    <div class="flex justify-between items-center font-semibold">
                        <span class="text-gray-900 dark:text-white">Subtotal:</span>
                        <span class="text-blue-600 dark:text-blue-400">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                @endif
            </div>
            <!-- Payment -->
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pembayaran</h2>
                <div class="space-y-4">
                    <!-- Pilih Meja (hanya meja available ditampilkan) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Meja</label>
                        <flux:select wire:model.live="selectedTableId" class="w-full">
                            <option value="">Tanpa Meja</option>
                            @foreach(($tables ?? collect()) as $t)
                                @if(($t->status ?? 'available') === 'available')
                                    <option value="{{ $t->id }}">
                                        {{ $t->name }} ({{ $t->code }}) — {{ $t->seats ? $t->seats.' kursi' : 'kursi ?' }}
                                    </option>
                                @endif
                            @endforeach
                        </flux:select>
                        @if($selectedTableId)
                        <p class="mt-1 text-xs text-gray-500 dark:text-zinc-400">Meja akan otomatis diset <span class="font-semibold">Tidak Tersedia</span> setelah pembayaran.</p>
                        @endif
                    </div>
                    <!-- Metode Pembayaran -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Metode</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button
                                type="button"
                                wire:click="$set('paymentMethod','cash')"
                                class="px-3 py-2 rounded border text-sm transition
                                    {{ $paymentMethod === 'cash' 
                                        ? 'bg-emerald-600 text-white border-emerald-700 hover:bg-emerald-700' 
                                        : 'bg-gray-50 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border-gray-200 dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700' }}">
                                Cash
                            </button>
                            <button
                                type="button"
                                wire:click="$set('paymentMethod','qris')"
                                class="px-3 py-2 rounded border text-sm transition
                                    {{ $paymentMethod === 'qris' 
                                        ? 'bg-blue-600 text-white border-blue-700 hover:bg-blue-700' 
                                        : 'bg-gray-50 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border-gray-200 dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700' }}">
                                QRIS
                            </button>
                            <button
                                type="button"
                                wire:click="$set('paymentMethod','card')"
                                class="px-3 py-2 rounded border text-sm transition
                                    {{ $paymentMethod === 'card' 
                                        ? 'bg-purple-600 text-white border-purple-700 hover:bg-purple-700' 
                                        : 'bg-gray-50 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border-gray-200 dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700' }}">
                                Card
                            </button>
                        </div>
                    </div>
                    <flux:input
                        wire:model.live="customerName"
                        type="text"
                        label="Nama Customer"
                        placeholder="Masukkan nama customer"
                        required />
                    @if($paymentMethod === 'cash')
                    <flux:input
                        wire:model.live="uangCustomer"
                        type="number"
                        label="Uang Customer"
                        placeholder="Masukkan jumlah uang"
                        min="0"
                        step="1000"
                        required />
                    @endif
                    @if($paymentMethod === 'qris')
                    <flux:input
                        wire:model.live="paymentRef"
                        type="text"
                        label="No. Referensi QRIS (opsional)"
                        placeholder="Masukkan nomor referensi / approval code" />
                    @endif
                    @if($paymentMethod === 'card')
                    <div class="grid grid-cols-2 gap-3">
                        <flux:input
                            wire:model.live="cardLast4"
                            type="text"
                            label="Last 4 Digit Kartu"
                            placeholder="1234"
                            maxlength="4" />
                        <flux:input
                            wire:model.live="paymentRef"
                            type="text"
                            label="No. Referensi (opsional)"
                            placeholder="Auth code / STAN" />
                    </div>
                    @endif
                    <!-- Quick Amount Buttons -->
                    @if($total > 0 && $paymentMethod === 'cash')
                    <div class="grid grid-cols-3 gap-2">
                        <button
                            wire:click="$set('uangCustomer', {{ $total }})"
                            class="px-3 py-2 text-xs bg-gray-100 dark:bg-zinc-700 rounded hover:bg-gray-200 dark:hover:bg-zinc-600 transition">
                            Pas
                        </button>
                        <button
                            wire:click="$set('uangCustomer', {{ ceil($total / 50000) * 50000 }})"
                            class="px-3 py-2 text-xs bg-gray-100 dark:bg-zinc-700 rounded hover:bg-gray-200 dark:hover:bg-zinc-600 transition">
                            Rp {{ number_format(ceil($total / 50000) * 50000, 0, ',', '.') }}
                        </button>
                        <button
                            wire:click="$set('uangCustomer', {{ ceil($total / 100000) * 100000 }})"
                            class="px-3 py-2 text-xs bg-gray-100 dark:bg-zinc-700 rounded hover:bg-gray-200 dark:hover:bg-zinc-600 transition">
                            Rp {{ number_format(ceil($total / 100000) * 100000, 0, ',', '.') }}
                        </button>
                    </div>
                    @endif
                    @if($paymentMethod === 'cash')
                    <div class="flex justify-between items-center text-lg font-semibold border-t border-gray-200 dark:border-zinc-700 pt-4">
                        <span class="text-gray-900 dark:text-white">Kembalian:</span>
                        <span class="{{ $kembalian > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-zinc-400' }}">
                            Rp {{ number_format($kembalian, 0, ',', '.') }}
                        </span>
                    </div>
                    @endif
                    <flux:button
                        wire:click="checkout"
                        variant="primary"
                        class="w-full"
                        icon="shopping-cart"
                        :disabled="count($cart) === 0 || $total <= 0 || empty($customerName) || ($paymentMethod === 'cash' && (($uangCustomer === '' ? 0 : (float) $uangCustomer) < $total)) || ($paymentMethod === 'card' && (strlen((string) $cardLast4) !== 4))"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>Buat Transaksi</span>
                        <span wire:loading>Memproses...</span>
                    </flux:button>
                </div>
            </div>
        </section>
    </div>
    <!-- Modal Konfirmasi Hapus -->
    @if($showClearCartModal)
    <div
        class="fixed inset-0 z-[60] overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-black/50" wire:click="closeClearCartModal"></div>
            <!-- Modal panel -->
            <div class="relative bg-white dark:bg-zinc-800 rounded-xl p-6 w-full max-w-sm z-[70]">
                <div class="text-center">
                    <!-- Warning Icon -->
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                        <svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        Kosongkan Keranjang
                    </h3>
                    <p class="text-gray-500 dark:text-zinc-400 mb-6 text-sm">
                        Apakah Anda yakin ingin mengosongkan keranjang? Tindakan ini tidak dapat dibatalkan.
                    </p>
                    <div class="flex justify-center gap-3">
                        <flux:button
                            wire:click="closeClearCartModal"
                            variant="outline"
                            class="px-4">
                            Batal
                        </flux:button>
                        <flux:button
                            wire:click="clearCart"
                            variant="danger"
                            class="px-4 flex items-center gap-1">
                            Ya, Kosongkan
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- Modal Sukses Sederhana (Tanpa Struk) -->
    @if($showReceiptModal)
    <div
        class="fixed inset-0 z-[70] overflow-y-auto"
        aria-labelledby="receipt-modal-title"
        role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-black/50"></div>
            <!-- Modal panel -->
            <div class="relative bg-white dark:bg-zinc-800 rounded-xl p-6 w-full max-w-sm z-[80]">
                <div class="text-center mb-6">
                    <!-- Success Icon -->
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 mb-3">
                        <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 id="receipt-modal-title" class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                        Pembayaran Berhasil
                    </h3>
                    @if($lastOrder)
                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400 mb-1">
                        No. Order: {{ $lastOrder->no_order }}
                    </p>
                    @endif
                    <p class="text-sm text-gray-500 dark:text-zinc-400">
                        {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div class="flex justify-center gap-3 mt-4">
                    <flux:button
                        wire:click="closeReceiptModal"
                        variant="outline"
                        icon="arrow-left"
                        class="flex-1 text-sm">
                        Tutup
                    </flux:button>
                    <flux:button
                        wire:click="preparePrintReceipt"
                        variant="primary"
                        icon="printer"
                        class="flex-1 text-sm">
                        Cetak
                    </flux:button>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- Modal Struk untuk Dicetak (Struktur seperti history.blade.php) -->
    @if($showPrintReceiptModal && $lastOrder)
    @php
    $width = $receiptWidth ?? '80mm'; // Default 80mm, bisa diubah jadi '58mm'
    $fontSize = $width === '58mm' ? '10px' : '12px';
    $padding = $width === '58mm' ? '8px' : '10px';
    @endphp

    <div
        id="receipt-content"
        class="hidden print:block bg-white text-black absolute left-0 top-0"
        style="width: {{ $width }}; padding: {{ $padding }}; font-family: 'Courier New', monospace; font-size: {{ $fontSize }}; line-height: 1.3;">
        <div class="receipt-layout space-y-1">
            <!-- Header -->
            <div class="text-center mb-2">
                <h2 class="font-bold text-lg" style="font-size: {{ $width === '58mm' ? '14px' : '16px' }}; margin-bottom: 4px;">
                    EssyCoff
                </h2>
                <p class="text-[9px] leading-tight">Jl. Jati No.41, Padang Jati, Kota Bengkulu</p>
                <p class="text-[9px]">Telp: (0736) 1234567</p>
            </div>

            <hr class="my-1 border-dashed border-black" style="border-top: 1px dashed #000; margin: 4px 0;">

            <!-- Order Info -->
            <div class="space-y-0.5 mb-2 text-[9px]">
                <div class="flex justify-between">
                    <span class="font-medium">No. order:</span>
                    <span>{{ $lastOrder->no_order }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Kasir:</span>
                    <span>{{ $lastOrder->user?->name ?? 'System' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Tanggal:</span>
                    <span>{{ $lastOrder->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if($lastOrder->customer_name)
                <div class="flex justify-between">
                    <span class="font-medium">Customer:</span>
                    <span>{{ $lastOrder->customer_name }}</span>
                </div>
                @endif
            </div>

            <hr class="my-1 border-dashed border-black" style="border-top: 1px dashed #000; margin: 4px 0;">

            <!-- Items -->
            <div class="space-y-1 mb-2">
                @foreach($lastOrder->items as $item)
                <div class="flex justify-between text-[9px]" style="font-size: {{ $width === '58mm' ? '8px' : '10px' }};">
                    <div>
                        <span class="font-medium">{{ $item->product?->name ?? 'Produk dihapus' }}</span>
                        <div class="text-[8px] text-gray-600">
                            {{ $item->quantity ?? $item->qty }} × Rp {{ number_format($item->harga ?? $item->price, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="text-right">
                        <div>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                        @if($item->note)
                        <div class="text-[7px] italic">Catatan: {{ $item->note }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <hr class="my-1 border-dashed border-black" style="border-top: 1px dashed #000; margin: 4px 0;">

            <!-- Summary -->
            <div class="space-y-0.5 font-semibold text-[9px]">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($lastOrder->total, 0, ',', '.') }}</span>
                </div>
                @if($lastOrder->discount > 0)
                <div class="flex justify-between">
                    <span>Diskon</span>
                    <span class="text-red-600">- Rp {{ number_format($lastOrder->discount, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($lastOrder->tax > 0)
                <div class="flex justify-between">
                    <span>Pajak ({{ $lastOrder->tax }}%)</span>
                    <span>Rp {{ number_format(($lastOrder->total * $lastOrder->tax) / 100, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($lastOrder->service_charge > 0)
                <div class="flex justify-between">
                    <span>Service Charge ({{ $lastOrder->service_charge }}%)</span>
                    <span>Rp {{ number_format(($lastOrder->total * $lastOrder->service_charge) / 100, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between font-bold pt-1 mt-1 border-t border-black" style="font-size: {{ $width === '58mm' ? '10px' : '12px' }};">
                    <span>Total</span>
                    <span>Rp {{ number_format($lastOrder->grand_total ?? $lastOrder->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between pt-1 border-t border-black mt-1">
                    <span>Metode</span>
                    <span>{{ strtoupper($lastOrder->payment_method ?? 'CASH') }}</span>
                </div>
                @if($lastOrder->payment_method === 'qris' && $lastOrder->payment_ref)
                <div class="flex justify-between">
                    <span>Referensi</span>
                    <span>{{ $lastOrder->payment_ref }}</span>
                </div>
                @endif
                @if($lastOrder->payment_method === 'card')
                <div class="flex justify-between">
                    <span>Kartu</span>
                    <span>**** **** **** {{ $lastOrder->card_last4 }}</span>
                </div>
                @if($lastOrder->payment_ref)
                <div class="flex justify-between">
                    <span>Referensi</span>
                    <span>{{ $lastOrder->payment_ref }}</span>
                </div>
                @endif
                @endif
                @if($lastOrder->uang_dibayar !== null)
                <div class="flex justify-between">
                    <span>{{ $lastOrder->payment_method === 'cash' ? 'Tunai' : 'Dibayar' }}</span>
                    <span>Rp {{ number_format($lastOrder->uang_dibayar, 0, ',', '.') }}</span>
                </div>
                @endif
                {{-- Selalu tampilkan kembalian, meskipun 0 --}}
                <div class="flex justify-between">
                    <span>Kembali</span>
                    <span>Rp {{ number_format($lastOrder->kembalian ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-3 text-[8px] text-gray-600">
                <p>Terima kasih atas kunjungan Anda</p>
                <p class="mt-0.5">~ EssyCoff ~</p>
                <p class="mt-1 text-[7px]">*Struk ini sebagai bukti pembayaran yang sah</p>
            </div>
        </div>
    </div>
    @endif
    <!-- ✅ CSS Print untuk 58mm & 80mm -->
    <style>
        @media print {
            @page {
                margin: 0;
                padding: 0;
            }

            /* Sembunyikan semua elemen */
            body * {
                visibility: hidden;
            }

            /* Tampilkan hanya struk */
            #receipt-content,
            #receipt-content * {
                visibility: visible;
            }

            /* Atur ukuran dan gaya struk */
            #receipt-content {
                position: absolute !important;
                top: 0 !important;
                left: 50% !important;
                transform: translateX(-50%) !important;
                /* biar rata tengah */
                margin: 0 auto !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                background: white !important;
                color: black !important;
                page-break-after: always;
                width: 58mm !important;
                /* ubah jadi 58mm biar kecil */
                padding: 6px !important;
                /* padding lebih kecil */
                font-size: 10px !important;
                /* font ikut kecil */
            }

            /* Reset gaya dalam struk */
            #receipt-content * {
                box-sizing: border-box;
            }

            /* Gaya khusus untuk print */
            .receipt-layout hr {
                border: none !important;
                border-top: 1px dashed #000 !important;
            }
        }
    </style>
    <!-- ✅ Script Print dengan Delay -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('printReceipt', () => {
                window.scrollTo(0, 0);
                setTimeout(() => {
                    window.print();
                }, 500); // Delay agar Livewire selesai render
            });
        });
    </script>

    <!-- SweetAlert2 Script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('swal:confirm', (e) => {
                Swal.fire({
                    title: e.title,
                    text: e.text,
                    icon: e.icon,
                    showCancelButton: true,
                    confirmButtonText: e.accept.label,
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-lg shadow-xl',
                        confirmButton: 'bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded',
                        cancelButton: 'bg-gray-500 text-white px-4 py-2 rounded'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call(e.accept.method);
                    }
                });
            });
        });
    </script>
</div>