<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

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
    public $showClearCartModal = false;
    public $showOutOfStockModal = false;
    public $outOfStockName = '';
    public $showReceiptModal = false;
    public $lastOrder = null;

    protected $paginationTheme = 'tailwind';
    protected $updatesQueryString = ['search'];

    public function mount()
    {
        // Inisialisasi cart dari session jika ada
        $this->cart = session()->get('pos_cart', []);
        $this->calculateTotal();
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
            return;
        }

        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['qty'] < $product->stock) {
                $this->cart[$productId]['qty']++;
                $this->dispatch('item-added', message: $product->name . ' ditambahkan ke keranjang');
            } else {
                $this->openOutOfStockModal($product->name);
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
            $this->dispatch('item-added', message: $product->name . ' ditambahkan ke keranjang');
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
        $this->uangCustomer = '';
        $this->customerName = '';
        $this->showClearCartModal = false;

        session()->flash('success', 'Keranjang berhasil dikosongkan!');
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
        if ($uangCustomer < $this->total) {
            session()->flash('error', 'Uang customer tidak cukup!');
            return;
        }

        if (empty($this->customerName)) {
            session()->flash('error', 'Nama customer harus diisi!');
            return;
        }

        try {
            $order = Order::create([
                'no_order' => 'ORD-' . date('YmdHis') . '-' . str_pad(Order::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT),
                'user_id' => Auth::id(),
                'customer_name' => $this->customerName,
                'total' => $this->total,
                'uang_dibayar' => $uangCustomer,
                'kembalian' => $this->kembalian,
                'status' => 'paid'
            ]);

            $orderId = $order->getKey();

            foreach ($this->cart as $productId => $item) {
                $product = Product::find($productId);

                OrderItem::create([
                    'order_id' => $orderId,
                    'product_id' => $productId,
                    'qty' => $item['qty'],
                    'harga' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty']
                ]);

                if ($product) {
                    $product->decrement('stock', $item['qty']);
                }
            }

            // Simpan order terakhir dan tampilkan modal struk
            $this->lastOrder = Order::with('items.product')->find($orderId);
            $this->showReceiptModal = true;

            // Reset cart setelah checkout berhasil
            $this->cart = [];
            session()->forget('pos_cart');
            $this->total = 0;
            $this->kembalian = 0;
            $this->customerName = '';
            $this->uangCustomer = '';

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
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

        return view('livewire.pos.cashier', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}