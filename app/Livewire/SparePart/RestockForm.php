<?php

namespace App\Livewire\SparePart;

use App\Models\SparePart;
use App\Services\InventoryService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;

#[Layout('layouts.app')]
class RestockForm extends Component
{
    #[Locked]
    public SparePart $sparePart;

    public int    $qty    = 1;
    public string $reason = 'purchase';

    public ?string $successMessage = null;
    public ?string $errorMessage   = null;

    public function mount(SparePart $sparePart): void
    {
        $this->sparePart = $sparePart;
    }

    public function getStockAfterProperty(): int
    {
        return $this->sparePart->quantity_available + max(0, $this->qty);
    }

    public function save(InventoryService $inventory): void
    {
        $this->validate([
            'qty'    => 'required|integer|min:1|max:9999',
            'reason' => 'required|string',
        ]);

        try {
            $inventory->addStock(
                part: $this->sparePart,
                qty: $this->qty,
                reason: $this->reason,
                user: auth()->user(),
            );

            $this->sparePart->refresh();
            $this->successMessage = "Stok berhasil ditambah {$this->qty} unit. Stok sekarang: {$this->sparePart->quantity_available} {$this->sparePart->unit}.";
            $this->qty = 1;

            // Kirim toast notification
            $this->dispatch('notify',
                type: 'success',
                message: "✅ Stok {$this->sparePart->name} bertambah {$this->qty} unit. Total: {$this->sparePart->quantity_available} {$this->sparePart->unit}."
            );

            // Kirim event ke SparePartList (jika ada di halaman yang sama)
            $this->dispatch('stock-updated');

        } catch (\Throwable $e) {
            $this->errorMessage = 'Gagal restock: ' . $e->getMessage();
            $this->dispatch('notify', type: 'error', message: 'Gagal restock: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.spare-part.restock-form')
            ->title('Restock: ' . $this->sparePart->name);
    }
}
