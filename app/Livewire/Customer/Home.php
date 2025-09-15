<?php

namespace App\Livewire\Customer;

use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        return view('livewire.customer.home')
            ->layout('components.layouts.app.customer', [
                'title' => 'EssyCoff - Home',
            ]);
    }
}
