<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\User as UserModel;
use Illuminate\Validation\Rule;

class Edit extends Component
{
    public UserModel $user;

    public string $name = '';
    public string $email = '';
    public string $role = '';

    public function mount(UserModel $user): void
    {
        $this->user = $user;
        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->role = $user->role ?? '';
    }

    public function render()
    {
        return view('livewire.user.edit');
    }

    public function update(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($this->user->getKey()),
            ],
            'role' => ['required', 'string', 'max:255'],
        ]);

        $this->user->update($validated);

        session()->flash('message', 'User updated successfully.');

        $this->redirectRoute('users.index');
    }
}
