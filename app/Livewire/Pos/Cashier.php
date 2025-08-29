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

    protected $paginationTheme = 'tailwind';
    protected $updatesQueryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function filterCategory($categoryId)
    {
        $this->categoryId = $categoryId;
        $this->resetPage();
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        if (!$product || $product->stock <= 0) return;

        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['qty'] < $product->stock) {
                $this->cart[$productId]['qty']++;
            }
        } else {
            $this->cart[$productId] = [
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image_url,
                'qty' => 1,
                'stock' => $product->stock
            ];
        }
        $this->calculateTotal();
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
        $this->calculateTotal();
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->total = 0;
        $this->kembalian = 0;
        $this->uangCustomer = '';
        $this->customerName = '';

        // Flash message sukses
        session()->flash('success', 'Keranjang berhasil dikosongkan!');
        
        // Dispatch event untuk menutup modal
        $this->dispatch('cart-cleared');
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
                'no_order' => 'ORD-' . date('Ymd') . '-' . str_pad(Order::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT),
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

            // Reset keranjang
            $this->reset(['cart', 'total', 'kembalian', 'customerName']);
            $this->uangCustomer = '';

            session()->flash('success', 'Transaksi berhasil dibuat!');

            // Redirect ke struk
            return redirect()->route('pos.receipt.index', $orderId);

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = Product::with('category')->where('name', 'like', '%' . $this->search . '%');
        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }

        $products = $query->paginate(12);
        $categories = Category::all();

        return view('livewire.pos.cashier', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}