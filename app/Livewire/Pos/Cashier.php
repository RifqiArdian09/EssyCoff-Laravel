<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class Cashier extends Component
{
    use WithPagination;

    public $search = '';
    public $cart = [];
    public $total = 0;
    public $uangCustomer = '';  // Changed to empty string instead of 0
    public $kembalian = 0;

    protected $paginationTheme = 'tailwind';
    protected $updatesQueryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        if (!$product || $product->stock <= 0) return;

        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['qty'] < $product->stock) {
                $this->cart[$productId]['qty']++;
            } else {
                session()->flash('error', $product->name . ' stok tidak cukup!');
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
        // Handle empty string or null values
        $this->uangCustomer = $value;
        $this->calculateKembalian();
    }

    public function calculateKembalian()
    {
        // Convert to float, handle empty string as 0
        $uang = $this->uangCustomer === '' ? 0 : (float) $this->uangCustomer;
        $total = (float) $this->total;
        
        // Only calculate kembalian if customer money is sufficient and total > 0
        $this->kembalian = ($uang >= $total && $total > 0) ? $uang - $total : 0;
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang masih kosong!');
            return;
        }

        if ($this->total <= 0) {
            session()->flash('error', 'Total tidak valid!');
            return;
        }

        $uangCustomer = $this->uangCustomer === '' ? 0 : (float) $this->uangCustomer;
        
        if ($uangCustomer < $this->total) {
            session()->flash('error', 'Uang customer tidak mencukupi!');
            return;
        }

        try {
            $order = Order::create([
                'no_order' => 'ORD-' . date('YmdHis') . rand(100, 999),
                'user_id' => auth()->id(),
                'total' => $this->total,
                'uang_dibayar' => $uangCustomer,
                'kembalian' => $this->kembalian,
                'source' => 'kasir',
                'status' => 'paid'
            ]);

            foreach ($this->cart as $productId => $item) {
                $product = Product::find($productId);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'qty' => $item['qty'],
                    'harga' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty']
                ]);

                // Kurangi stok
                if ($product) {
                    $product->stock -= $item['qty'];
                    $product->save();
                }
            }

            // Reset all values including uangCustomer to empty string
            $this->reset(['cart', 'total', 'kembalian']);
            $this->uangCustomer = '';  // Explicitly set to empty string
            
            session()->flash('message', 'Transaksi berhasil! No. Order: ' . $order->no_order);

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function clearCart()
    {
        $this->reset(['cart', 'total', 'kembalian']);
        $this->uangCustomer = '';  // Keep input empty when clearing cart
        session()->flash('message', 'Keranjang berhasil dikosongkan!');
    }

    public function render()
    {
        $products = Product::with('category')
            ->where('name', 'like', '%'.$this->search.'%')
            ->paginate(12);

        return view('livewire.pos.cashier', [
            'products' => $products
        ]);
    }
}