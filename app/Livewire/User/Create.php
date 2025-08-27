<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\User as UserModel;
use Illuminate\Validation\Rule;

class Create extends Component
{
    public string $name = '';
    public string $email = '';
    public string $role = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function render()
    {
        return view('livewire.user.create');
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'role' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        UserModel::create($validated);

        session()->flash('message', 'User created successfully.');

        $this->redirectRoute('users.index');
    }
}
