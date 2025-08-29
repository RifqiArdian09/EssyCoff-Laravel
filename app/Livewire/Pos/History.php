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
    public bool $showPaymentModal = false;
    public $selectedOrder = null;
    public $uangDibayar = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmPayment($orderId)
    {
        $this->selectedOrder = Order::find($orderId);
        $this->uangDibayar = '';
        $this->showPaymentModal = true;
    }

    public function processPayment()
    {
        $this->validate([
            'uangDibayar' => 'required|numeric|min:' . $this->selectedOrder->total,
        ], [
            'uangDibayar.required' => 'Jumlah uang yang dibayar harus diisi',
            'uangDibayar.numeric' => 'Jumlah uang harus berupa angka',
            'uangDibayar.min' => 'Jumlah uang tidak boleh kurang dari total pesanan',
        ]);

        $kembalian = $this->uangDibayar - $this->selectedOrder->total;

        $this->selectedOrder->update([
            'uang_dibayar' => $this->uangDibayar,
            'kembalian' => $kembalian,
            'status' => 'paid',
            'user_id' => auth()->id(), // Set kasir yang konfirmasi
        ]);

        $this->showPaymentModal = false;
        $this->selectedOrder = null;
        $this->uangDibayar = '';

        session()->flash('message', 'Pembayaran berhasil dikonfirmasi!');
        
        // Refresh the page data to show updated status
        $this->resetPage();
    }

    public function closeModal()
    {
        $this->showPaymentModal = false;
        $this->selectedOrder = null;
        $this->uangDibayar = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        $orders = Order::with('user')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('no_order', 'like', '%' . $this->search . '%')
                      ->orWhere('customer_name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.pos.history', [
            'orders' => $orders,
        ]);
    }
}


