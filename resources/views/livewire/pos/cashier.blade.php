<section class="w-full">
    <div class="flex flex-col lg:flex-row gap-6" style="min-height: calc(100vh - 120px);">

        <!-- Left Panel - Products -->
        <section class="lg:w-3/5">
            <div class="space-y-6 border p-4 rounded-lg dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50 transition-colors duration-200">

                <!-- Search Input -->
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari produk..." />

                <!-- Category Filter Buttons -->
                <div class="flex flex-wrap gap-2 mt-4 mb-2">
                    <button wire:click="filterCategory(null)" class="px-3 py-1 rounded {{ $categoryId === null ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200' }}">Semua</button>

                    @foreach($categories as $category)
                        <button wire:click="filterCategory({{ $category->id }})" class="px-3 py-1 rounded {{ $categoryId === $category->id ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mt-4">
                    @foreach($products as $product)
                        <div wire:click="addToCart({{ $product->id }})" class="space-y-2 border p-2 rounded-lg dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50 shadow hover:shadow-lg cursor-pointer flex flex-col items-center">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-20 h-20 object-cover rounded-full">
                            @else
                                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 flex items-center justify-center rounded-full">
                                    <span class="text-gray-500 dark:text-gray-400 text-xs">No Image</span>
                                </div>
                            @endif

                            <div class="text-center">
                                <h3 class="font-medium text-xs truncate">{{ $product->name }}</h3>
                                <p class="text-blue-600 dark:text-blue-400 font-bold text-xs mt-1">Rp {{ number_format($product->price,0,',','.') }}</p>

                                @if($product->stock > 0)
                                    <span class="text-xs {{ $product->stock <= 5 ? 'text-red-500' : 'text-green-600 dark:text-green-400' }}">
                                        Stok: {{ $product->stock }}
                                    </span>
                                @else
                                    <span class="text-xs text-red-500">Habis</span>
                                @endif

                                @if($product->category)
                                    <span class="text-xs text-gray-400 dark:text-gray-300 block">{{ $product->category->name }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </section>

        <!-- Right Panel - Cart & Payment -->
        <section class="lg:w-2/5 flex flex-col gap-4">
           
            <!-- Cart Section -->
            <div class="space-y-2 border p-2 rounded-lg dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50 transition-colors duration-200">
                <div class="flex justify-between items-center mb-2">
                    <h2 class="text-sm font-semibold">Keranjang</h2>
                    @if(!empty($cart))
                        <button wire:click="clearCart" class="text-xs text-red-500 hover:text-red-700 dark:hover:text-red-400">Kosongkan</button>
                    @endif
                </div>

                <!-- Scrollable Cart Content dengan max height untuk 4 items -->
                <div class="max-h-80 overflow-y-auto">
                    @if(empty($cart))
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400 text-xs">
                            <p class="font-medium">Keranjang masih kosong</p>
                            <p class="mt-1">Klik produk untuk menambah ke keranjang</p>
                        </div>
                    @else
                        <div class="space-y-1 pr-2">
                            @foreach($cart as $id => $item)
                                <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-700/50 p-1 rounded-lg text-xs">
                                    <div class="flex flex-col">
                                        <h3 class="font-medium truncate">{{ $item['name'] }}</h3>
                                        <div class="flex items-center space-x-1 mt-1">
                                            <button wire:click="updateQuantity({{ $id }}, {{ $item['qty'] - 1 }})" class="px-1 py-0.5 bg-gray-200 dark:bg-gray-600 rounded hover:bg-gray-300 dark:hover:bg-gray-500">-</button>
                                            <span class="px-1">{{ $item['qty'] }}</span>
                                            <button wire:click="updateQuantity({{ $id }}, {{ $item['qty'] + 1 }})" class="px-1 py-0.5 bg-gray-200 dark:bg-gray-600 rounded hover:bg-gray-300 dark:hover:bg-gray-500">+</button>
                                        </div>
                                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Rp {{ number_format($item['price'],0,',','.') }} x {{ $item['qty'] }}</p>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <span class="font-bold text-blue-600 dark:text-blue-400 text-xs">Rp {{ number_format($item['price']*$item['qty'],0,',','.') }}</span>
                                        <button wire:click="removeFromCart({{ $id }})" class="text-red-500 hover:text-red-700 dark:hover:text-red-400 text-xs">×</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Section -->
            <div class="space-y-2 border p-2 rounded-lg dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50 transition-colors duration-200">
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between font-semibold">
                        <span>Total:</span>
                        <span>Rp {{ number_format($total,0,',','.') }}</span>
                    </div>

                    <flux:input 
                        wire:model.live="customerName" 
                        type="text" 
                        label="Nama Customer" 
                        placeholder="Masukkan nama customer"
                        class="text-xs"
                    />

                    <flux:input 
                        wire:model.live="uangCustomer" 
                        type="number" 
                        label="Uang Customer" 
                        placeholder="Masukkan jumlah uang"
                        min="0" 
                        step="1000" 
                        class="text-xs"
                    />

                    <div class="flex justify-between font-semibold mt-1">
                        <span>Kembalian:</span>
                        <span class="{{ $kembalian > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}">
                            Rp {{ number_format($kembalian,0,',','.') }}
                        </span>
                    </div>

                    <flux:button 
                        wire:click="checkout" 
                        variant="primary" 
                        class="w-full mt-2 text-xs"
                        :disabled="empty($cart) || $total <= 0 || ($uangCustomer === '' ? 0 : (float) $uangCustomer) < $total"
                    >
                        Buat Transaksi
                    </flux:button>
                </div>
            </div>

        </section>
    </div>
</section>