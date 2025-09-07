<div class="p-6 space-y-8 bg-white dark:bg-zinc-800 min-h-screen text-gray-900 dark:text-white" x-data="{ loading: false }" x-on:item-added.window="loading = false">
    <div class="flex flex-col lg:flex-row gap-6">

        <!-- Left Panel - Products -->
        <section class="lg:w-3/5">
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700 space-y-6">

                <!-- Success Message -->
                @if(session()->has('success'))
                <div class="bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-2 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
                @endif

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
                        x-on:click="loading = true"
                        class="bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 p-3 rounded-lg shadow hover:shadow-lg cursor-pointer transition duration-200 flex flex-col items-center space-y-2 {{ $product->stock <= 0 ? 'opacity-50 cursor-not-allowed' : 'hover:scale-105' }}"
                        :class="loading ? 'opacity-50' : ''"
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
                    <flux:input
                        wire:model.live="customerName"
                        type="text"
                        label="Nama Customer"
                        placeholder="Masukkan nama customer"
                        required />

                    <flux:input
                        wire:model.live="uangCustomer"
                        type="number"
                        label="Uang Customer"
                        placeholder="Masukkan jumlah uang"
                        min="0"
                        step="1000"
                        required />

                    <!-- Quick Amount Buttons -->
                    @if($total > 0)
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

                    <div class="flex justify-between items-center text-lg font-semibold border-t border-gray-200 dark:border-zinc-700 pt-4">
                        <span class="text-gray-900 dark:text-white">Kembalian:</span>
                        <span class="{{ $kembalian > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-zinc-400' }}">
                            Rp {{ number_format($kembalian, 0, ',', '.') }}
                        </span>
                    </div>

                    <flux:button
                        wire:click="checkout"
                        variant="primary"
                        class="w-full"
                        icon="shopping-cart"
                        :disabled="count($cart) === 0 || $total <= 0 || ($uangCustomer === '' ? 0 : (float) $uangCustomer) < $total || empty($customerName)"
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

    <!-- Modal Struk -->
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
                    <p class="text-sm text-gray-500 dark:text-zinc-400">
                        {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
                    </p>
                </div>

                <!-- Struk Content -->
                <div class="bg-white dark:bg-zinc-800 p-4 rounded border border-gray-200 dark:border-zinc-700" id="receipt-content">
                    <div class="receipt-layout">
                        <div class="text-center mb-3">
                            <h2 class="text-xl font-bold">EssyCoff</h2>
                            <p class="text-xs text-gray-600">Jl. Jati No.41, Padang Jati, Kota Bengkulu</p>
                        </div>

                        <hr class="my-2 border-dashed border-gray-400">

                        <div class="space-y-1.5 mb-3 text-xs">
                            <p><strong>No.:</strong> {{ $lastOrder->no_order }}</p>
                            <p><strong>Kasir:</strong> {{ $lastOrder->user?->name ?? '-' }}</p>
                            <p><strong>Tanggal:</strong> {{ $lastOrder->created_at->format('d M, H:i') }}</p>
                        </div>

                        <hr class="my-2 border-dashed border-gray-400">

                        <div class="space-y-1 text-sm">
                            @foreach($lastOrder->items as $item)
                                <div class="flex justify-between">
                                    <span>{{ Str::limit($item->product?->name, 15) }} × {{ $item->qty }}</span>
                                    <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>

                        <hr class="my-2 border-dashed border-gray-400">

                        <div class="space-y-1 font-semibold text-sm">
                            <div class="flex justify-between">
                                <span>Total</span>
                                <span>Rp {{ number_format($lastOrder->total, 0, ',', '.') }}</span>
                            </div>
                            @if($lastOrder->uang_dibayar)
                            <div class="flex justify-between">
                                <span>Tunai</span>
                                <span>Rp {{ number_format($lastOrder->uang_dibayar, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            @if($lastOrder->kembalian !== null)
                            <div class="flex justify-between">
                                <span>Kembali</span>
                                <span>Rp {{ number_format($lastOrder->kembalian, 0, ',', '.') }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="text-center mt-4">
                            <p class="font-medium">Terima Kasih!</p>
                            <p class="text-gray-600">~ EssyCoff ~</p>
                        </div>
                    </div>
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
                        onclick="printReceipt()"
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

    <!-- CSS untuk cetak -->
    <style>
        @media print {
            /* Hide everything */
            body * {
                visibility: hidden;
            }
            
            /* Show only receipt */
            #receipt-content, #receipt-content * {
                visibility: visible;
            }
            
            #receipt-content {
                position: absolute !important;
                top: 0 !important;
                left: 50% !important;
                transform: translateX(-50%) !important;
                width: 80mm !important;
                max-width: 80mm !important;
                margin: 0 !important;
                padding: 10mm !important;
                background: white !important;
                color: black !important;
                font-family: 'Courier New', monospace !important;
                font-size: 12px !important;
                line-height: 1.4 !important;
                z-index: 9999 !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                break-after: avoid !important;
            }
            
            .receipt-layout .text-xl {
                font-size: 16px !important;
            }
            
            .receipt-layout .text-xs {
                font-size: 10px !important;
            }
            
            .receipt-layout hr {
                border: none !important;
                border-top: 1px dashed #000 !important;
                margin: 8px 0 !important;
            }
            
            .receipt-layout .flex {
                display: flex !important;
            }
            
            .receipt-layout .justify-between {
                justify-content: space-between !important;
            }
            
            .receipt-layout .text-center {
                text-align: center !important;
            }
            
            .receipt-layout .font-bold {
                font-weight: bold !important;
            }
            
            .receipt-layout .font-medium {
                font-weight: 500 !important;
            }
            
            .receipt-layout .font-semibold {
                font-weight: 600 !important;
            }
            
            /* Compact spacing */
            .receipt-layout .space-y-1 > * + * {
                margin-top: 2px !important;
            }
            
            .receipt-layout .space-y-1\.5 > * + * {
                margin-top: 3px !important;
            }
            
            .receipt-layout .mb-3 {
                margin-bottom: 6px !important;
            }
            
            .receipt-layout .mt-4 {
                margin-top: 8px !important;
            }
            
            .receipt-layout .my-2 {
                margin-top: 4px !important;
                margin-bottom: 4px !important;
            }
            
            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>

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

            @this.on('item-added', (e) => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: 'success',
                    title: e.message || 'Produk ditambahkan ke keranjang',
                    background: '#10b981',
                    color: '#ffffff',
                    iconColor: '#ffffff'
                });
            });
        });

        function printReceipt() {
            window.print();
        }
    </script>
</div>