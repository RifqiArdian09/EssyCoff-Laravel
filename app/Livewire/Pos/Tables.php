<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CafeTable;
use Illuminate\Support\Str;

class Tables extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public bool $showModal = false;
    public bool $showQrModal = false;
    public ?CafeTable $editing = null;
    public string $qrCode = '';
    public string $qrName = '';
    public bool $confirmingDeletion = false;
    public ?int $tableIdToDelete = null;

    // Form fields
    public string $name = '';
    public string $code = '';
    public string $state = 'available'; // available | unavailable
    public ?int $seats = null;
    public string $note = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }

    public function mount()
    {
        $this->state = 'available';
    }

    public function openCreate()
    {
        $this->editing = null;
        $this->name = '';
        $this->code = $this->generateUniqueCode();
        $this->state = 'available';
        $this->seats = null;
        $this->note = '';
        $this->showModal = true;
    }

    public function openEdit(int $id)
    {
        $table = CafeTable::findOrFail($id);
        $this->editing = $table;
        $this->name = $table->name;
        $this->code = $table->code;
        $this->state = $table->status;
        $this->seats = $table->seats;
        $this->note = (string)($table->note ?? '');
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:100|unique:cafe_tables,code' . ($this->editing ? ',' . $this->editing->id : ''),
            'state' => 'required|in:available,unavailable',
            'seats' => 'nullable|integer|min:1|max:50',
            'note' => 'nullable|string|max:500',
        ]);

        if ($this->editing) {
            $this->editing->update([
                'name' => $this->name,
                'code' => $this->code,
                'status' => $this->state,
                'seats' => $this->seats,
                'note' => $this->note ?: null,
            ]);
            session()->flash('message', 'Meja berhasil diperbarui.');
        } else {
            CafeTable::create([
                'name' => $this->name,
                'code' => $this->code,
                'status' => $this->state,
                'seats' => $this->seats,
                'note' => $this->note ?: null,
            ]);
            session()->flash('message', 'Meja baru berhasil dibuat.');
        }

        $this->closeModal();
    }

    public function delete(int $id)
    {
        CafeTable::whereKey($id)->delete();
        session()->flash('message', 'Meja berhasil dihapus.');
        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => 'Meja berhasil dihapus.',
            'timeout' => 3000,
        ]);
    }

    public function toggleStatus(int $id)
    {
        $table = CafeTable::findOrFail($id);
        $table->update(['status' => $table->status === 'available' ? 'unavailable' : 'available']);
    }

    public function regenerateCode()
    {
        $this->code = $this->generateUniqueCode();
    }

     public function closeModal()
     {
         $this->showModal = false;
         $this->editing = null;
         $this->resetErrorBag();
     }

    public function openQr(string $code)
    {
        $this->qrCode = $code;
        $this->qrName = CafeTable::where('code', $code)->value('name') ?? '';
        $this->showQrModal = true;
    }

    public function closeQr()
    {
        $this->showQrModal = false;
        $this->qrCode = '';
        $this->qrName = '';
    }

    public function confirmDelete(int $id)
    {
        $this->tableIdToDelete = $id;
        $this->confirmingDeletion = true;
    }

    public function cancelDelete()
    {
        $this->confirmingDeletion = false;
        $this->tableIdToDelete = null;
    }

    public function deleteConfirmed()
    {
        if ($this->tableIdToDelete) {
            CafeTable::whereKey($this->tableIdToDelete)->delete();
            $this->confirmingDeletion = false;
            $this->tableIdToDelete = null;
            session()->flash('message', 'Meja berhasil dihapus.');
            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Meja berhasil dihapus.',
                'timeout' => 3000,
            ]);
        }
    }

    protected function generateUniqueCode(): string
    {
        do {
            $code = 'TBL-' . strtoupper(Str::random(5));
        } while (CafeTable::where('code', $code)->exists());
        return $code;
    }

    public function getQrUrl(string $code, int $size = 160, string $format = 'svg'): string
    {
        // Return RELATIVE URL to avoid APP_URL/domain mismatches affecting image loading
        return "/qr/table/{$code}.{$format}?size={$size}&margin=1";
    }

    public function render()
    {
        $tables = CafeTable::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('code', 'like', "%{$this->search}%"))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.pos.tables', [
            'tables' => $tables,
        ]);
    }
}
