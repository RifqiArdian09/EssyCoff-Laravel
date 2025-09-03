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

            <!-- Cart -->
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Keranjang
                        @if(count($cart) > 0)
                        <span class="text-sm bg-red-500 text-white px-2 py-1 rounded-full ml-2">{{ count($cart) }}</span>
                        @endif
                    </h2>

                    <!-- Tombol dengan SweetAlert Confirmation -->
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

                <div class="max-h-80 overflow-y-auto">
                    @if(empty($cart))
                    <div class="text-center py-8 text-gray-500 dark:text-zinc-400">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-200 dark:bg-zinc-700 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0L17 21"></path>
                            </svg>
                        </div>
                        <p class="font-medium">Keranjang masih kosong</p>
                        <p class="mt-1 text-sm">Klik produk untuk menambah ke keranjang</p>
                    </div>
                    @else
                    <div class="space-y-3">
                        @foreach($cart as $id => $item)
                        <div class="bg-gray-50 dark:bg-zinc-800 p-3 rounded-lg border border-gray-200 dark:border-zinc-700">
                            <div class="flex justify-between items-start">
                                <!-- Gambar + Detail -->
                                <div class="flex items-start space-x-3 flex-1">
                                    @if($item['image'])
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-12 h-12 object-cover rounded-full bg-gray-100 dark:bg-zinc-700">
                                    @else
                                    <div class="w-12 h-12 bg-gray-200 dark:bg-zinc-700 flex items-center justify-center rounded-full">
                                        <span class="text-gray-500 dark:text-zinc-400 text-xs">No</span>
                                    </div>
                                    @endif

                                    <div>
                                        <h3 class="font-medium text-gray-900 dark:text-white truncate max-w-[140px]">
                                            {{ $item['name'] }}
                                        </h3>

                                        <div class="flex items-center space-x-2 mt-2">
                                            <button
                                                wire:click="updateQuantity({{ $id }}, {{ $item['qty'] - 1 }})"
                                                class="px-2 py-1 bg-gray-200 dark:bg-zinc-700 rounded hover:bg-gray-300 dark:hover:bg-zinc-600 transition text-sm"
                                                {{ $item['qty'] <= 1 ? 'disabled' : '' }}>-</button>

                                            <span class="px-2 text-sm font-medium text-gray-900 dark:text-white min-w-[30px] text-center">
                                                {{ $item['qty'] }}
                                            </span>

                                            <button
                                                wire:click="updateQuantity({{ $id }}, {{ $item['qty'] + 1 }})"
                                                class="px-2 py-1 bg-gray-200 dark:bg-zinc-700 rounded hover:bg-gray-300 dark:hover:bg-zinc-600 transition text-sm"
                                                {{ $item['qty'] >= $item['stock'] ? 'disabled' : '' }}>+</button>
                                        </div>

                                        <p class="text-sm text-gray-600 dark:text-zinc-300 mt-1">
                                            Rp {{ number_format($item['price'], 0, ',', '.') }} x {{ $item['qty'] }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Harga Total & Hapus -->
                                <div class="flex items-center space-x-2 ml-4">
                                    <span class="font-bold text-blue-600 dark:text-blue-400 text-right">
                                        Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                                    </span>
                                    <button
                                        wire:click="removeFromCart({{ $id }})"
                                        class="text-red-500 hover:text-red-700 dark:hover:text-red-400 text-lg transition p-1 hover:bg-red-100 dark:hover:bg-red-900/30 rounded"
                                        title="Hapus item">×</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment -->
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pembayaran</h2>

                <div class="space-y-4">
                    <!-- Total Items -->
                    @if(count($cart) > 0)
                    <div class="flex justify-between items-center text-sm text-gray-600 dark:text-zinc-400 border-b border-gray-200 dark:border-zinc-700 pb-2">
                        <span>Total Item:</span>
                        <span>{{ array_sum(array_column($cart, 'qty')) }} item</span>
                    </div>
                    @endif

                    <div class="flex justify-between items-center text-lg font-semibold">
                        <span class="text-gray-900 dark:text-white">Total:</span>
                        <span class="text-blue-600 dark:text-blue-400">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </span>
                    </div>

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
            <div class="fixed inset-0 bg-black/50"></div>

            <!-- Modal panel -->
            <div class="relative bg-white dark:bg-zinc-800 rounded-lg p-6 w-full max-w-sm z-[70]">
                <div class="text-center">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Konfirmasi Hapus
                    </h3>
                    <p class="text-gray-500 dark:text-zinc-400 mb-6">
                        Apakah Anda yakin ingin mengosongkan keranjang?
                    </p>
                    <div class="flex justify-center gap-3">
                        <flux:button
                            wire:click="closeClearCartModal"
                            variant="primary"
                            class="px-4">
                            Batal
                        </flux:button>
                        <flux:button
                            wire:click="clearCart"
                            variant="danger"
                            class="px-4">
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
            <div class="relative bg-white dark:bg-zinc-800 rounded-lg p-6 w-full max-w-md z-[80]">
                <div class="text-center mb-4">
                    <h3 id="receipt-modal-title" class="text-lg font-bold text-gray-900 dark:text-white">
                        Struk Pembayaran
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-zinc-400">
                        {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
                    </p>
                </div>

                <!-- Struk Content -->
                <div class="bg-white dark:bg-zinc-800 p-4 rounded border border-gray-200 dark:border-zinc-700" id="receipt-content">
                    <div class="text-center mb-4">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">EasyCoff</h2>
                        <p class="text-sm text-gray-600 dark:text-zinc-400">Jl. Contoh No. 123</p>
                        <p class="text-sm text-gray-600 dark:text-zinc-400">Telp: (021) 123-4567</p>
                    </div>

                    <div class="border-t border-b border-gray-300 py-2 mb-3">
                        <p class="text-sm">
                            <strong>No. Order:</strong> {{ $lastOrder->no_order }}<br>
                            <strong>Kasir:</strong> {{ Auth::user()->name }}<br>
                            <strong>Customer:</strong> {{ $lastOrder->customer_name }}<br>
                            <strong>Tanggal:</strong> {{ $lastOrder->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <div class="mb-3">
                        <table class="w-full text-sm text-gray-900 dark:text-white">
                            <thead>
                                <tr class="border-b border-gray-300 dark:border-zinc-600">
                                    <th class="text-left pb-1 text-gray-700 dark:text-zinc-300">Item</th>
                                    <th class="text-center pb-1 text-gray-700 dark:text-zinc-300">Qty</th>
                                    <th class="text-right pb-1 text-gray-700 dark:text-zinc-300">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lastOrder->items as $item)
                                <tr class="border-b border-gray-200 dark:border-zinc-700">
                                    <td class="py-1">{{ $item->product->name }}</td>
                                    <td class="text-center py-1">{{ $item->qty }}</td>
                                    <td class="text-right py-1">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-b border-gray-300 dark:border-zinc-600 py-2 mb-3">
                        <div class="flex justify-between text-sm text-gray-700 dark:text-zinc-300">
                            <span>Subtotal:</span>
                            <span>Rp {{ number_format($lastOrder->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-700 dark:text-zinc-300">
                            <span>Pembayaran:</span>
                            <span>Rp {{ number_format($lastOrder->uang_dibayar, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm font-bold text-gray-900 dark:text-white">
                            <span>Kembalian:</span>
                            <span>Rp {{ number_format($lastOrder->kembalian, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="text-center text-xs text-gray-500 dark:text-zinc-400 mt-4">
                        <p>Terima kasih atas kunjungan Anda</p>
                        <p class="mt-1">*** Barang yang sudah dibeli tidak dapat ditukar ***</p>
                    </div>
                </div>

                <div class="flex justify-center gap-3 mt-6">
                    <flux:button
                        wire:click="closeReceiptModal"
                        variant="outline"
                        icon="arrow-left"
                        class="w-1/2 text-xs">
                        Tutup
                        </flux:button>


                   <flux:button
                        onclick="window.print(); return false;"
                        variant="primary"
                        icon="printer"
                        class="w-1/2 text-xs">
                        Cetak struk
                        </flux:button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- SweetAlert2 Script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @media print {
            @page {
                size: 80mm auto;
                /* Fixed width of 80mm, auto height */
                margin: 0;
                /* Remove default margins */
                padding: 0;
            }

            body {
                margin: 0;
                padding: 0;
                width: 80mm;
                font-family: 'Courier New', monospace;
            }

            body * {
                visibility: hidden;
                margin: 0;
                padding: 0;
            }

            #receipt-content,
            #receipt-content * {
                visibility: visible;
            }

            #receipt-content {
                width: 100%;
                max-width: 80mm;
                margin: 0;
                padding: 3mm;
                box-sizing: border-box;
                font-size: 10px;
                line-height: 1.2;
            }

            #receipt-content .text-sm {
                font-size: 9px !important;
                line-height: 1.1;
            }

            #receipt-content table {
                width: 100%;
                border-collapse: collapse;
                margin: 2mm 0;
            }

            #receipt-content th,
            #receipt-content td {
                padding: 1mm 0;
            }

            #receipt-content .text-center {
                text-align: center;
            }

            #receipt-content .text-xs {
                font-size: 9px !important;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
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