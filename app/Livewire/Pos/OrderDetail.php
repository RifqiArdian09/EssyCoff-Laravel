<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use App\Models\Order;
use App\Models\CafeTable;

class OrderDetail extends Component
{
    public $order;
    public bool $showPaymentModal = false;
    public string $paymentMethod = 'cash'; // cash, qris, card
    public $uangDibayar = '';
    public string $paymentRef = '';
    public string $cardLast4 = '';

    public function mount(Order $order)
    {
        $this->order = $order->load('items.product', 'user', 'table');
    }

    public function printReceipt()
    {
        $this->dispatch('printReceipt');
    }

    public function openPaymentModal()
    {
        if (!$this->order) return;
        $this->paymentMethod = 'cash';
        $this->uangDibayar = '';
        $this->paymentRef = '';
        $this->cardLast4 = '';
        $this->resetErrorBag();
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->resetErrorBag();
    }

    public function processPayment()
    {
        if (!$this->order) return;

        $total = (float) $this->order->total;
        $rules = [];
        $messages = [];
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
        }

        if (!empty($rules)) {
            $this->validate($rules, $messages);
        }

        $uangDibayar = $this->paymentMethod === 'cash' ? (float) ($this->uangDibayar ?: 0) : $total;
        $kembalian = $this->paymentMethod === 'cash' ? max(0, $uangDibayar - $total) : 0;

        $this->order->update([
            'uang_dibayar' => $uangDibayar,
            'kembalian' => $kembalian,
            'status' => 'paid',
            'user_id' => auth()->id(),
            'payment_method' => $this->paymentMethod,
            'payment_ref' => $this->paymentMethod === 'qris' ? ($this->paymentRef ?: null) : ($this->paymentMethod === 'card' ? ($this->paymentRef ?: null) : null),
            'card_last4' => $this->paymentMethod === 'card' ? $this->cardLast4 : null,
        ]);

        if ($this->order->table_id) {
            CafeTable::whereKey($this->order->table_id)->update(['status' => 'unavailable']);
        }

        // Refresh order data and close modal
        $this->order->refresh()->load('items.product', 'user', 'table');
        $this->showPaymentModal = false;
        $this->resetErrorBag();

        session()->flash('message', 'Pembayaran berhasil! Struk akan dicetak.');
        $this->dispatch('printReceipt');
    }

    public function markTableAvailable()
    {
        if ($this->order && $this->order->table_id) {
            CafeTable::whereKey($this->order->table_id)->update(['status' => 'available']);
            // Refresh relation
            $this->order->load('table');
            session()->flash('message', 'Meja telah ditandai Tersedia.');
        }
    }

    public function render()
    {
        return view('livewire.pos.detail');
    }
}
