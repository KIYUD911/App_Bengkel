<?php

namespace App\Livewire\SparePart;

use App\Models\SparePart;
use App\Services\AuditTrailService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class SparePartForm extends Component
{
    public ?int $partId = null;

    public string $partCode    = '';
    public string $name        = '';
    public string $category    = '';
    public string $purchasePrice = '';
    public string $sellingPrice  = '';
    public int    $qty          = 0;
    public string $unit         = 'pcs';
    public int    $warrantyDays = 0;

    public ?string $successMessage = null;
    public ?string $errorMessage   = null;

    public function mount(?SparePart $sparePart = null): void
    {
        if ($sparePart && $sparePart->exists) {
            $this->partId       = $sparePart->id;
            $this->partCode     = $sparePart->part_code;
            $this->name         = $sparePart->name;
            $this->category     = $sparePart->category ?? '';
            $this->purchasePrice = (string) $sparePart->purchase_price;
            $this->sellingPrice  = (string) $sparePart->selling_price;
            $this->qty          = $sparePart->quantity_available;
            $this->unit         = $sparePart->unit;
            $this->warrantyDays = $sparePart->warranty_days;
        }
    }

    public function save(AuditTrailService $audit): void
    {
        $rules = [
            'partCode'    => 'required|string|max:50|unique:spare_parts,part_code' . ($this->partId ? ",{$this->partId}" : ''),
            'name'        => 'required|string|max:255',
            'purchasePrice' => 'required|numeric|min:0',
            'sellingPrice'  => 'required|numeric|min:0',
            'unit'        => 'required|string|max:20',
            'warrantyDays' => 'integer|min:0',
        ];
        $this->validate($rules);

        $data = [
            'part_code'          => $this->partCode,
            'name'               => $this->name,
            'category'           => $this->category ?: null,
            'purchase_price'     => (float) $this->purchasePrice,
            'selling_price'      => (float) $this->sellingPrice,
            'unit'               => $this->unit,
            'warranty_days'      => $this->warrantyDays,
        ];

        try {
            if ($this->partId) {
                $part     = SparePart::findOrFail($this->partId);
                $oldValues = $part->toArray();
                $part->update($data);
                $audit->logUpdate('spare_parts', $part->id, $oldValues, $part->fresh()->toArray(), auth()->user());
                $this->successMessage = 'Sparepart berhasil diperbarui.';
            } else {
                $data['quantity_available'] = $this->qty;
                $part = SparePart::create($data);
                $audit->logCreate('spare_parts', $part->id, $part->toArray(), auth()->user());
                $this->redirect(route('spare-parts.index'), navigate: true);
            }
        } catch (\Throwable $e) {
            $this->errorMessage = 'Gagal menyimpan: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.spare-part.spare-part-form')
            ->title($this->partId ? 'Edit Sparepart' : 'Tambah Sparepart');
    }
}
