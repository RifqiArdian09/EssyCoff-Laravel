<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use App\Models\CafeTable;

class History extends Component
{
    use WithPagination;

    public int $perPage = 10;
    public string $search = '';
    public string $selectedMonth = '';
    public string $selectedTableId = '';
    public string $status = 'all'; // all | pending_payment | paid
    public bool $showPaymentModal = false;
    public $selectedOrder = null;
    public $uangDibayar = '';
    public string $paymentMethod = 'cash'; // cash, qris, card
    public string $paymentRef = '';
    public string $cardLast4 = '';
    public bool $showDeleteModal = false;
    public $orderToDelete = null;

    protected $paginationTheme = 'tailwind';

    /**
     * Reset pagination saat pencarian berubah
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Reset pagination saat filter bulan berubah
     */
    public function updatingSelectedMonth()
    {
        $this->resetPage();
    }

    public function updatingSelectedTableId()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    /**
     * Mount method untuk set default bulan
     */
    public function mount()
    {
        $this->selectedMonth = now()->format('Y-m');
    }

    /**
     * Buka modal konfirmasi pembayaran
     */
    public function confirmPayment($orderId)
    {
        $this->selectedOrder = Order::with('items.product', 'user')->find($orderId);

        if (!$this->selectedOrder) {
            session()->flash('message', 'Order tidak ditemukan.');
            return;
        }

        $this->uangDibayar = '';
        $this->paymentMethod = 'cash';
        $this->paymentRef = '';
        $this->cardLast4 = '';
        $this->showPaymentModal = true;
    }

    /**
     * Proses pembayaran dan cetak struk otomatis
     */
    public function processPayment()
    {
        // Validasi dinamis berdasarkan metode pembayaran
        $rules = [];
        $messages = [];
        $total = (float) $this->selectedOrder->total;

        if ($this->paymentMethod === 'cash') {
            $rules['uangDibayar'] = 'required|numeric|min:' . $total;
            $messages = [
                'uangDibayar.required' => 'Jumlah uang yang dibayar harus diisi',
                'uangDibayar.numeric' => 'Jumlah uang harus berupa angka',
                'uangDibayar.min' => 'Jumlah uang tidak boleh kurang dari total pesanan',
            ];
        } elseif ($this->paymentMethod === 'card') {
            $rules['cardLast4'] = 'required|digits:4';
            $messages = [
                'cardLast4.required' => 'Masukkan 4 digit terakhir kartu',
                'cardLast4.digits' => '4 digit terakhir kartu harus berisi 4 angka',
            ];
        } else {
            // qris: tidak butuh uangDibayar; paymentRef opsional
        }

        if (!empty($rules)) {
            $this->validate($rules, $messages);
        }

        $uangDibayar = $this->paymentMethod === 'cash' ? (float) ($this->uangDibayar ?: 0) : $total;
        $kembalian = $this->paymentMethod === 'cash' ? ($uangDibayar - $total) : 0;

        $this->selectedOrder->update([
            'uang_dibayar' => $uangDibayar,
            'kembalian' => $kembalian,
            'status' => 'paid',
            'user_id' => auth()->id(),
            'payment_method' => $this->paymentMethod,
            'payment_ref' => $this->paymentMethod === 'qris' ? ($this->paymentRef ?: null) : ($this->paymentMethod === 'card' ? ($this->paymentRef ?: null) : null),
            'card_last4' => $this->paymentMethod === 'card' ? $this->cardLast4 : null,
        ]);

        // Jika order terkait meja, set status meja menjadi unavailable
        if ($this->selectedOrder->table_id) {
            CafeTable::whereKey($this->selectedOrder->table_id)->update(['status' => 'unavailable']);
        }

        // Reset
        $this->showPaymentModal = false;
        $this->uangDibayar = '';
        $this->paymentMethod = 'cash';
        $this->paymentRef = '';
        $this->cardLast4 = '';
        $this->resetErrorBag();

        // Flash message dan trigger cetak
        session()->flash('message', 'Pembayaran berhasil! Struk akan dicetak.');
        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => 'Pembayaran berhasil! Struk akan dicetak.',
            'timeout' => 3000,
        ]);
        $this->dispatch('printReceipt');
    }

    /**
     * Tutup modal dan reset data
     */
    public function closeModal()
    {
        $this->showPaymentModal = false;
        $this->selectedOrder = null;
        $this->uangDibayar = '';
        $this->resetErrorBag();
    }

    /**
     * Buka modal konfirmasi hapus
     */
    public function confirmDelete($orderId)
    {
        $this->orderToDelete = Order::find($orderId);

        if (!$this->orderToDelete) {
            session()->flash('message', 'Order tidak ditemukan.');
            return;
        }

        $this->showDeleteModal = true;
    }

    /**
     * Tutup modal hapus
     */
    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->orderToDelete = null;
    }

    /**
     * Hapus order beserta item terkait
     */
    public function deleteOrder()
    {
        if (!$this->orderToDelete) {
            session()->flash('message', 'Tidak ada order yang dipilih.');
            return;
        }

        $order = Order::with('items')->find($this->orderToDelete->id);
        if (!$order) {
            session()->flash('message', 'Order tidak ditemukan.');
            $this->closeDeleteModal();
            return;
        }

        // Hapus semua item terlebih dahulu jika tidak ada cascade
        if (method_exists($order, 'items')) {
            $order->items()->delete();
        }

        $order->delete();

        $this->closeDeleteModal();

        // Refresh pagination/list
        $this->resetPage();

        session()->flash('message', 'Transaksi berhasil dihapus.');
        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => 'Transaksi berhasil dihapus.',
            'timeout' => 3000,
        ]);
    }

    /**
     * Get available months for filter
     */
    public function getAvailableMonths()
    {
        return Order::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month')
            ->distinct()
            ->orderByDesc('month')
            ->pluck('month')
            ->map(function ($month) {
                return [
                    'value' => $month,
                    'label' => \Carbon\Carbon::createFromFormat('Y-m', $month)->locale('id')->translatedFormat('F Y')
                ];
            });
    }

    public function getTables()
    {
        return CafeTable::orderBy('name')->get();
    }

    public function markTableAvailable(int $orderId)
    {
        $order = Order::find($orderId);
        if (!$order || !$order->table_id) return;
        CafeTable::whereKey($order->table_id)->update(['status' => 'available']);
        session()->flash('message', 'Meja telah ditandai Tersedia.');
        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => 'Meja telah ditandai Tersedia.',
            'timeout' => 3000,
        ]);
    }

    /**
     * Render view dengan data order
     */
    public function render()
    {
        $orders = Order::with('user', 'table')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('no_order', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedTableId !== '', function ($query) {
                $query->where('table_id', $this->selectedTableId);
            })
            ->when($this->selectedMonth, function ($query) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$this->selectedMonth]);
            })
            ->when($this->status !== 'all', function ($query) {
                if ($this->status === 'pending_payment') {
                    $query->where('status', 'pending_payment');
                } elseif ($this->status === 'paid') {
                    $query->where('status', 'paid');
                }
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        $availableMonths = $this->getAvailableMonths();
        $tables = $this->getTables();

        return view('livewire.pos.history', [
            'orders' => $orders,
            'availableMonths' => $availableMonths,
            'tables' => $tables,
        ]);
    }
}