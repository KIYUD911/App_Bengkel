<div>
<div class="page-header">
    <div>
        <h2 class="page-title">Restock Sparepart</h2>
        <p class="page-subtitle">Tambah stok: <strong>{{ $sparePart->name }}</strong></p>
    </div>
    <a href="{{ route('spare-parts.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

@if($successMessage)
    <div class="alert alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,5000)" x-transition>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ $successMessage }}
    </div>
@endif
@if($errorMessage)
    <div class="alert alert-danger">{{ $errorMessage }}</div>
@endif

<div class="grid grid-2" style="max-width:800px;align-items:start;">

    {{-- Info Part --}}
    <div class="card">
        <div class="card-header"><span class="card-title">📦 Info Sparepart</span></div>
        <table style="width:100%;">
            <tbody>
                <tr><td style="padding:.5rem 0;color:var(--text-muted);font-size:var(--text-sm);">Kode</td><td><code>{{ $sparePart->part_code }}</code></td></tr>
                <tr><td style="padding:.5rem 0;color:var(--text-muted);font-size:var(--text-sm);">Nama</td><td style="font-weight:600;">{{ $sparePart->name }}</td></tr>
                <tr><td style="padding:.5rem 0;color:var(--text-muted);font-size:var(--text-sm);">Kategori</td><td>{{ $sparePart->category ?? '-' }}</td></tr>
                <tr><td style="padding:.5rem 0;color:var(--text-muted);font-size:var(--text-sm);">Harga Beli</td><td>Rp {{ number_format($sparePart->purchase_price, 0, ',', '.') }}</td></tr>
            </tbody>
        </table>

        {{-- Stock Preview --}}
        <div style="margin-top:1rem;display:flex;align-items:center;gap:1rem;background:var(--surface);border-radius:var(--radius);padding:1rem;border:1px solid var(--border);">
            <div style="text-align:center;flex:1;">
                <div style="font-size:var(--text-xs);color:var(--text-muted);margin-bottom:.25rem;">Stok Saat Ini</div>
                <div style="font-size:2rem;font-weight:700;color:{{ $sparePart->quantity_available <= 5 ? 'var(--danger)' : 'var(--text)' }};">
                    {{ $sparePart->quantity_available }}
                </div>
                <div style="font-size:var(--text-xs);color:var(--text-muted);">{{ $sparePart->unit }}</div>
            </div>

            <div style="font-size:1.5rem;">→</div>

            <div style="text-align:center;flex:1;">
                <div style="font-size:var(--text-xs);color:var(--text-muted);margin-bottom:.25rem;">Setelah Restock</div>
                <div style="font-size:2rem;font-weight:700;color:var(--success);">
                    {{ $this->stockAfter }}
                </div>
                <div style="font-size:var(--text-xs);color:var(--text-muted);">{{ $sparePart->unit }}</div>
            </div>
        </div>
    </div>

    {{-- Form Restock --}}
    <div class="card">
        <div class="card-header"><span class="card-title">📥 Tambah Stok</span></div>

        <div class="form-group">
            <label class="form-label">Jumlah Tambah <span class="required">*</span></label>
            <div style="display:flex;align-items:center;gap:.75rem;">
                <button wire:click="$set('qty', {{ max(1, $qty-1) }})"
                    style="width:40px;height:40px;border-radius:50%;border:1.5px solid var(--border);background:white;font-size:1.25rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">−</button>
                <input type="number" wire:model.live="qty" class="form-input" min="1" max="9999"
                    style="width:100px;text-align:center;font-size:var(--text-xl);font-weight:700;" />
                <button wire:click="$set('qty', {{ $qty+1 }})"
                    style="width:40px;height:40px;border-radius:50%;border:1.5px solid var(--border);background:white;font-size:1.25rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">+</button>
            </div>
            @error('qty') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Alasan Restock <span class="required">*</span></label>
            <select wire:model="reason" class="form-select">
                <option value="purchase">Pembelian dari Supplier</option>
                <option value="return_from_wo">Pengembalian dari WO</option>
                <option value="correction">Koreksi Stok</option>
                <option value="other">Lainnya</option>
            </select>
        </div>

        <button wire:click="save" class="btn btn-success" style="width:100%;" wire:loading.attr="disabled">
            <span wire:loading.remove>📥 Tambah {{ $qty }} {{ $sparePart->unit }}</span>
            <span wire:loading>Memproses...</span>
        </button>

        <div style="margin-top:.75rem;">
            <a href="{{ route('stock-movements.index') }}" class="btn btn-secondary" style="width:100%;display:block;text-align:center;">
                📊 Lihat Riwayat Mutasi Stok
            </a>
        </div>
    </div>
</div>
</div>
