<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $confirmingUserDeletion = false;
    public $userIdToDelete = null;

    protected $updatesQueryString = ['search'];

    // Reset halaman saat search berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Show delete confirmation modal
    public function confirmDelete($userId)
    {
        $this->confirmingUserDeletion = true;
        $this->userIdToDelete = $userId;
    }

    // Delete user
    public function delete($id = null)
    {
        $id = $id ?? $this->userIdToDelete;
        $user = User::findOrFail($id);
        
        // Prevent deleting the currently logged in user
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Tidak dapat menghapus akun yang sedang digunakan.');
            $this->confirmingUserDeletion = false;
            return;
        }
        
        $user->delete();
        session()->flash('message', 'Pengguna berhasil dihapus.');
        
        // Close the modal after successful deletion
        $this->confirmingUserDeletion = false;
        $this->userIdToDelete = null;
    }

    public function render()
    {
        $users = User::when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('role', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.user.index', [
            'users' => $users,
        ]);
    }
    
}
