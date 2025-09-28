<?php
namespace App\Livewire\Pos;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use App\Models\CafeTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
class Cashier extends Component
{
    use WithPagination;
    public $search = '';
    public $cart = [];
    public $total = 0;
    public $uangCustomer = '';
    public $kembalian = 0;
    public $categoryId = null;
    public $customerName = '';
    public $paymentMethod = 'cash'; // cash, qris, card
    public $paymentRef = '';
    public $cardLast4 = '';
    public $selectedTableId = null; // dropdown meja
    public $showClearCartModal = false;
    public $showOutOfStockModal = false;
    public $outOfStockName = '';
    public $showReceiptModal = false; // Modal sukses sederhana
    public $showPrintReceiptModal = false; // Modal struk untuk dicetak
    public $lastOrder = null;
    protected $paginationTheme = 'tailwind';
    protected $updatesQueryString = ['search'];
    public function mount()
    {
        // Inisialisasi cart dari session jika ada
        $this->cart = session()->get('pos_cart', []);
        $this->validateCartItems();
        $this->calculateTotal();
    }

    public function validateCartItems()
    {
        $validCart = [];
        $removedItems = [];
        
        foreach ($this->cart as $productId => $item) {
            $product = Product::find($productId);
            
            if (!$product) {
                $removedItems[] = $item['name'];
                continue;
            }
            
            // Update stock info and adjust quantity if needed
            if ($item['qty'] > $product->stock) {
                if ($product->stock > 0) {
                    $item['qty'] = $product->stock;
                    $item['stock'] = $product->stock;
                    $validCart[$productId] = $item;
                } else {
                    $removedItems[] = $item['name'];
                }
            } else {
                $item['stock'] = $product->stock;
                $validCart[$productId] = $item;
            }
        }
        
        $this->cart = $validCart;
        session()->put('pos_cart', $this->cart);
        
        if (!empty($removedItems)) {
            session()->flash('error', 'Beberapa produk dihapus dari keranjang: ' . implode(', ', $removedItems));
        }
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function filterCategory($categoryId)
    {
        if ($this->categoryId == $categoryId) {
            // Jika kategori yang sama diklik, reset filter
            $this->categoryId = null;
        } else {
            $this->categoryId = $categoryId;
        }
        $this->resetPage();
    }
    public function addToCart($productId)
    {
        $product = Product::find($productId);
        if (!$product || $product->stock <= 0) {
            $this->openOutOfStockModal($product->name ?? '');
            // Toast warning: out of stock
            $this->dispatch('toast', type: 'warning', title: 'Stok Habis', message: ($product->name ?? 'Produk') . ' tidak tersedia.');
            return;
        }
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['qty'] < $product->stock) {
                $this->cart[$productId]['qty']++;
                // Emit lightweight event for front-end to show toast (Alpine listener)
                $this->dispatch('item-added', message: $product->name . ' ditambahkan ke keranjang');
                // Global toast success
                $this->dispatch('toast', type: 'success', title: 'Ditambahkan', message: $product->name . ' ditambahkan ke keranjang');
            } else {
                $this->openOutOfStockModal($product->name);
                $this->dispatch('toast', type: 'warning', title: 'Stok Terbatas', message: 'Stok ' . $product->name . ' tidak mencukupi.');
                return;
            }
        } else {
            $this->cart[$productId] = [
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image_url,
                'qty' => 1,
                'stock' => $product->stock
            ];
            // Emit lightweight event for front-end to show toast (Alpine listener)
            $this->dispatch('item-added', message: $product->name . ' ditambahkan ke keranjang');
            $this->dispatch('toast', type: 'success', title: 'Ditambahkan', message: $product->name . ' ditambahkan ke keranjang');
        }
        // Simpan cart ke session
        session()->put('pos_cart', $this->cart);
        $this->calculateTotal();
    }
    public function removeFromCart($productId)
    {
        if (isset($this->cart[$productId])) {
            unset($this->cart[$productId]);
            // Update session
            session()->put('pos_cart', $this->cart);
            $this->calculateTotal();
            // Toast info
            $this->dispatch('toast', type: 'success', title: 'Dihapus', message: 'Item dihapus dari keranjang');
        }
    }
    public function openClearCartModal()
    {
        $this->showClearCartModal = true;
    }
    public function closeClearCartModal()
    {
        $this->showClearCartModal = false;
    }
    public function clearCartWithConfirm()
    {
        $this->showClearCartModal = true;
    }
    public function clearCart()
    {
        $this->cart = [];
        session()->forget('pos_cart');
        $this->total = 0;
        $this->kembalian = 0;
        $this->customerName = '';
        $this->uangCustomer = '';
        $this->showClearCartModal = false;
        session()->flash('success', 'Keranjang berhasil dikosongkan!');
        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Keranjang berhasil dikosongkan!');
    }
    public function openOutOfStockModal($name = '')
    {
        $this->outOfStockName = $name;
        $this->showOutOfStockModal = true;
    }
    public function closeOutOfStockModal()
    {
        $this->showOutOfStockModal = false;
        $this->outOfStockName = '';
    }
    public function updateQuantity($productId, $qty)
    {
        $product = Product::find($productId);
        if (!$product) return;
        $qty = max(0, min($qty, $product->stock));
        if ($qty === 0) {
            $this->removeFromCart($productId);
            return;
        }
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['qty'] = $qty;
            // Update session
            session()->put('pos_cart', $this->cart);
            $this->calculateTotal();
        }
    }
    public function calculateTotal()
    {
        $this->total = 0;
        foreach ($this->cart as $item) {
            $this->total += $item['price'] * $item['qty'];
        }
        $this->calculateKembalian();
    }
    public function updatedUangCustomer($value)
    {
        // Hanya terima angka
        $value = preg_replace('/[^0-9]/', '', $value);
        $this->uangCustomer = $value;
        $this->calculateKembalian();
    }
    public function calculateKembalian()
    {
        // Kembalian hanya relevan untuk metode cash
        if ($this->paymentMethod !== 'cash') {
            $this->kembalian = 0;
            return;
        }
        $uang = $this->uangCustomer === '' ? 0 : (float) $this->uangCustomer;
        $total = (float) $this->total;
        $this->kembalian = ($uang >= $total && $total > 0) ? $uang - $total : 0;
    }
    public function checkout()
    {
        if (count($this->cart) === 0) {
            session()->flash('error', 'Keranjang masih kosong!');
            return;
        }
        if ($this->total <= 0) {
            session()->flash('error', 'Total tidak valid!');
            return;
        }
        $uangCustomer = $this->uangCustomer === '' ? 0 : (float) $this->uangCustomer;
        if ($this->paymentMethod === 'cash') {
            if ($uangCustomer < $this->total) {
                session()->flash('error', 'Uang customer tidak cukup!');
                return;
            }
        } elseif ($this->paymentMethod === 'card') {
            // Validasi minimal 4 digit terakhir
            if (empty($this->cardLast4) || !preg_match('/^\d{4}$/', $this->cardLast4)) {
                session()->flash('error', 'Masukkan 4 digit terakhir kartu.');
                return;
            }
        }
        if (empty($this->customerName)) {
            session()->flash('error', 'Nama customer harus diisi!');
            return;
        }
        try {
            // Tentukan nilai pembayaran berdasarkan metode
            $uangDibayar = $this->paymentMethod === 'cash' ? $uangCustomer : $this->total;
            $kembalian = $this->paymentMethod === 'cash' ? $this->kembalian : 0;

            // Generate unique no_order: ORD-YYYYMMDD-#### using advisory lock to avoid race
            $today = now();
            $prefix = 'ORD-' . $today->format('Ymd') . '-';
            $lockKey = 'orders_seq_' . $today->format('Ymd');
            $lockAcquired = false;
            $attempts = 0;
            $order = null;
            try {
                $lockAcquired = (bool) collect(DB::select('SELECT GET_LOCK(?, 5) as l', [$lockKey]))->first()->l;
                // Retry create with fresh sequence on duplicate
                do {
                    $attempts++;
                    $lastNo = Order::where('no_order', 'like', $prefix . '%')
                        ->orderBy('no_order', 'desc')
                        ->value('no_order');
                    $seq = 0;
                    if ($lastNo && preg_match('/^ORD-\d{8}-(\d{4})$/', $lastNo, $m)) {
                        $seq = (int) $m[1];
                    }
                    $seq++;
                    $noOrder = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);

                    try {
                        $order = Order::create([
                            'no_order' => $noOrder,
                            'user_id' => Auth::id(),
                            'table_id' => $this->selectedTableId,
                            'customer_name' => $this->customerName,
                            'total' => $this->total,
                            'uang_dibayar' => $uangDibayar,
                            'kembalian' => $kembalian,
                            'status' => 'paid',
                            'payment_method' => $this->paymentMethod,
                            'payment_ref' => $this->paymentMethod === 'qris' ? ($this->paymentRef ?: null) : null,
                            'card_last4' => $this->paymentMethod === 'card' ? $this->cardLast4 : null,
                        ]);
                    } catch (QueryException $e) {
                        $isDuplicate = ($e->getCode() === '23000') || (isset($e->errorInfo[1]) && (int)$e->errorInfo[1] === 1062);
                        if ($isDuplicate) {
                            usleep(50000);
                            $order = null;
                        } else {
                            throw $e;
                        }
                    }
                } while (!$order && $attempts < 10);
            } finally {
                if ($lockAcquired) {
                    DB::select('SELECT RELEASE_LOCK(?)', [$lockKey]);
                }
            }
            if (!$order) {
                throw new \RuntimeException('Gagal membuat nomor pesanan unik. Silakan coba lagi.');
            }
            $orderId = $order->getKey();
            foreach ($this->cart as $productId => $item) {
                $product = Product::find($productId);
                
                // Skip if product doesn't exist (was deleted)
                if (!$product) {
                    session()->flash('error', "Produk '{$item['name']}' tidak ditemukan. Mungkin telah dihapus.");
                    continue;
                }
                
                // Check if sufficient stock is available
                if ($product->stock < $item['qty']) {
                    session()->flash('error', "Stok '{$product->name}' tidak mencukupi. Tersisa {$product->stock} item.");
                    continue;
                }
                
                OrderItem::create([
                    'order_id' => $orderId,
                    'product_id' => $productId,
                    'qty' => $item['qty'],
                    'harga' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty']
                ]);
                
                $product->decrement('stock', $item['qty']);
            }
            // Set lastOrder untuk ditampilkan di modal sukses
            $this->lastOrder = $order->load('items.product', 'user');
            // Toast success with order number
            $this->dispatch('toast', type: 'success', title: 'Transaksi Berhasil', message: 'No. Order: ' . $order->no_order, playSound: true);
            // Jika ada meja dipilih, set status meja menjadi unavailable (otomatis setelah bayar)
            if ($this->selectedTableId) {
                CafeTable::whereKey($this->selectedTableId)->update(['status' => 'unavailable']);
            }
            // Hanya tampilkan modal sukses. JANGAN tampilkan struk di sini.
            $this->showReceiptModal = true;
            // Reset cart setelah checkout berhasil
            $this->cart = [];
            session()->forget('pos_cart');
            $this->total = 0;
            $this->kembalian = 0;
            $this->customerName = '';
            $this->uangCustomer = '';
            $this->paymentMethod = 'cash';
            $this->paymentRef = '';
            $this->cardLast4 = '';
            $this->selectedTableId = null;
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            $this->dispatch('toast', type: 'error', title: 'Gagal', message: $e->getMessage());
        }
    }
    public function preparePrintReceipt()
    {
        // Tutup modal sukses
        $this->showReceiptModal = false;
        // Ambil data order terakhir
        $this->lastOrder = Order::with('items.product')->latest()->first();
        // Tampilkan modal struk untuk dicetak
        $this->showPrintReceiptModal = true;
        // Panggil print setelah modal struk muncul
        $this->js('setTimeout(() => window.print(), 500);');
    }
    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
        $this->showPrintReceiptModal = false;
        $this->lastOrder = null;
    }
    public function printReceipt()
    {
        $this->js('window.print()');
    }
    public function render()
    {
        $query = Product::with('category')
            ->where('name', 'like', '%' . $this->search . '%');
        if (!empty($this->categoryId)) {
            $query->where('category_id', $this->categoryId);
        }
        $products = $query->orderBy('name')->paginate(12);
        $categories = Category::orderBy('name')->get();
        $tables = CafeTable::orderBy('name')->get();
        return view('livewire.pos.cashier', [
            'products' => $products,
            'categories' => $categories,
            'tables' => $tables,
        ]);
    }
}