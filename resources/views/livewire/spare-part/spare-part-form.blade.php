<div>
<div class="page-header">
    <div><h2 class="page-title">{{ $partId ? 'Edit Sparepart' : 'Tambah Sparepart' }}</h2></div>
    <a href="{{ route('spare-parts.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

@if($successMessage)
    <div class="alert alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,3000)" x-transition>{{ $successMessage }}</div>
@endif
@if($errorMessage)
    <div class="alert alert-danger">{{ $errorMessage }}</div>
@endif

<div class="card" style="max-width:680px;">
    <div class="grid grid-2">
        <div class="form-group">
            <label class="form-label">Kode Part <span class="required">*</span></label>
            <input type="text" wire:model.live="partCode" class="form-input @error('partCode') is-invalid @enderror" placeholder="e.g. SP-001" {{ $partId ? 'disabled' : '' }} />
            @error('partCode') <span class="form-error">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Nama Sparepart <span class="required">*</span></label>
            <input type="text" wire:model.live="name" class="form-input @error('name') is-invalid @enderror" />
            @error('name') <span class="form-error">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Kategori</label>
            <input type="text" wire:model="category" class="form-input" placeholder="Oli, Filter, Rem..." />
        </div>
        <div class="form-group">
            <label class="form-label">Satuan <span class="required">*</span></label>
            <select wire:model="unit" class="form-select">
                <option>pcs</option><option>liter</option><option>set</option><option>meter</option><option>kg</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Harga Beli (Rp) <span class="required">*</span></label>
            <input type="number" wire:model="purchasePrice" class="form-input" min="0" placeholder="0" />
            @error('purchasePrice') <span class="form-error">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Harga Jual (Rp) <span class="required">*</span></label>
            <input type="number" wire:model="sellingPrice" class="form-input" min="0" placeholder="0" />
            @error('sellingPrice') <span class="form-error">{{ $message }}</span> @enderror
        </div>
        @if(!$partId)
        <div class="form-group">
            <label class="form-label">Stok Awal</label>
            <input type="number" wire:model="qty" class="form-input" min="0" placeholder="0" />
        </div>
        @endif
        <div class="form-group">
            <label class="form-label">Garansi (hari)</label>
            <input type="number" wire:model="warrantyDays" class="form-input" min="0" placeholder="0 = tanpa garansi" />
            <span class="form-hint">0 = tidak ada garansi</span>
        </div>
    </div>

    {{-- Preview margin --}}
    @if($purchasePrice && $sellingPrice && (float)$sellingPrice > 0)
        <div style="padding:.75rem;background:var(--success-light);border-radius:var(--radius-sm);margin-bottom:1rem;">
            <span style="font-size:var(--text-sm);color:var(--success);">
                Margin: Rp {{ number_format((float)$sellingPrice - (float)$purchasePrice, 0, ',', '.') }}
                ({{ round(((float)$sellingPrice - (float)$purchasePrice) / max((float)$sellingPrice, 1) * 100, 1) }}%)
            </span>
        </div>
    @endif

    <div class="flex gap-2">
        <button wire:click="save" class="btn btn-primary" wire:loading.attr="disabled">
            <span wire:loading.remove>💾 {{ $partId ? 'Perbarui' : 'Simpan' }}</span>
            <span wire:loading>Menyimpan...</span>
        </button>
        <a href="{{ route('spare-parts.index') }}" class="btn btn-secondary">Batal</a>
    </div>
</div>
</div>
