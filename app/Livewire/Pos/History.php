<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;

class History extends Component
{
    use WithPagination;

    public int $perPage = 10;
    public string $search = '';
    public string $selectedMonth = '';
    public bool $showPaymentModal = false;
    public $selectedOrder = null;
    public $uangDibayar = '';

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
        $this->showPaymentModal = true;
    }

    /**
     * Proses pembayaran dan cetak struk otomatis
     */
    public function processPayment()
    {
        $this->validate([
            'uangDibayar' => 'required|numeric|min:' . $this->selectedOrder->total,
        ], [
            'uangDibayar.required' => 'Jumlah uang yang dibayar harus diisi',
            'uangDibayar.numeric' => 'Jumlah uang harus berupa angka',
            'uangDibayar.min' => 'Jumlah uang tidak boleh kurang dari total pesanan',
        ]);

        $uangDibayar = (float) $this->uangDibayar;
        $total = (float) $this->selectedOrder->total;

        $this->selectedOrder->update([
            'uang_dibayar' => $uangDibayar,
            'kembalian' => $uangDibayar - $total,
            'status' => 'paid',
            'user_id' => auth()->id(),
        ]);

        // Reset
        $this->showPaymentModal = false;
        $this->uangDibayar = 0;
        $this->resetErrorBag();

        // Flash message dan trigger cetak
        session()->flash('message', 'Pembayaran berhasil! Struk akan dicetak.');
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

    /**
     * Render view dengan data order
     */
    public function render()
    {
        $orders = Order::with('user')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('no_order', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedMonth, function ($query) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$this->selectedMonth]);
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        $availableMonths = $this->getAvailableMonths();

        return view('livewire.pos.history', [
            'orders' => $orders,
            'availableMonths' => $availableMonths,
        ]);
    }
}