<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;

class History extends Component
{
    use WithPagination;

    public int $perPage = 10;
    public ?string $search = ''; // Tambahkan properti search

    protected $paginationTheme = 'tailwind'; // Opsional: jika pakai tailwind bawaan Laravel

    public function render()
{
    $query = Order::with('user')->orderByDesc('created_at');

    if ($this->search) {
        $query->where(function ($q) {
            $q->where('no_order', 'LIKE', '%' . $this->search . '%')
              ->orWhere('customer_name', 'LIKE', '%' . $this->search . '%')
              ->orWhereHas('user', function ($userQuery) {
                  $userQuery->where('name', 'LIKE', '%' . $this->search . '%');
              });
        });
    }

   
    \Log::info('Search query:', [
        'search' => $this->search,
        'count' => $query->count(),
        'sql' => $query->toSql(),
        'bindings' => $query->getBindings()
    ]);

    $orders = $query->paginate($this->perPage);

    return view('livewire.pos.history', compact('orders'));
}

    // Opsional: agar pencarian langsung reaktif
    public function updatedSearch()
    {
        $this->resetPage(); // Reset ke halaman 1 saat pencarian berubah
    }
}