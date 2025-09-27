<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\CafeTable;

class TableNotFound extends Component
{
    public string $tableCode = '';
    public $availableTables = [];

    public function mount($code = null)
    {
        $this->tableCode = $code ?? '';
        
        // Get some available tables as suggestions
        $this->availableTables = CafeTable::where('status', 'available')
            ->orderBy('name')
            ->limit(6)
            ->get();
    }

    public function render()
    {
        return view('livewire.customer.table-not-found')
            ->layout('components.layouts.app.customer', [
                'title' => 'Meja Tidak Ditemukan - EssyCoff',
            ]);
    }
}
