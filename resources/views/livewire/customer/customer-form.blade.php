<div>
<div class="page-header">
    <div>
        <h2 class="page-title">{{ $customerId ? 'Edit Pelanggan' : 'Tambah Pelanggan' }}</h2>
    </div>
    <a href="{{ route('customers.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

@if($successMessage)
    <div class="alert alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,3000)" x-transition>{{ $successMessage }}</div>
@endif
@if($errorMessage)
    <div class="alert alert-danger">{{ $errorMessage }}</div>
@endif

<div class="card" style="max-width:600px;">
    <div class="form-group">
        <label class="form-label">Nama Lengkap <span class="required">*</span></label>
        <input type="text" wire:model.live="name" class="form-input @error('name') is-invalid @enderror" />
        @error('name') <span class="form-error">{{ $message }}</span> @enderror
    </div>
    <div class="form-group">
        <label class="form-label">Nomor Telepon</label>
        <input type="text" wire:model.live="phone" class="form-input" placeholder="08xx..." />
        @error('phone') <span class="form-error">{{ $message }}</span> @enderror
    </div>
    <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" wire:model.live="email" class="form-input" placeholder="email@domain.com" />
        @error('email') <span class="form-error">{{ $message }}</span> @enderror
    </div>
    <div class="form-group">
        <label class="form-label">Alamat</label>
        <textarea wire:model="address" class="form-textarea" rows="3" placeholder="Alamat lengkap..."></textarea>
        @error('address') <span class="form-error">{{ $message }}</span> @enderror
    </div>
    <div class="flex gap-2">
        <button wire:click="save" class="btn btn-primary" wire:loading.attr="disabled">
            <span wire:loading.remove>💾 {{ $customerId ? 'Perbarui' : 'Simpan' }}</span>
            <span wire:loading>Menyimpan...</span>
        </button>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Batal</a>
    </div>
</div>
</div>
