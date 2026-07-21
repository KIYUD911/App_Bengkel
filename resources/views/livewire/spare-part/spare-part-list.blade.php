<div>
<div class="page-header">
    <div>
        <h2 class="page-title">Manajemen Sparepart</h2>
        <p class="page-subtitle">Katalog dan stok sparepart bengkel</p>
    </div>
    <a href="{{ route('spare-parts.create') }}" class="btn btn-primary">+ Tambah Sparepart</a>
</div>

<div class="card" style="margin-bottom:1.25rem;">
    <div class="flex gap-3" style="flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:1;min-width:200px;">
            <label class="form-label">Cari</label>
            <input type="text" wire:model.live.debounce.300ms="search" class="form-input" placeholder="Nama atau kode part..." />
        </div>
        <div style="min-width:160px;">
            <label class="form-label">Kategori</label>
            <select wire:model.live="category" class="form-select">
                <option value="">Semua Kategori</option>
                @foreach($this->categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <label style="display:flex;align-items:center;gap:.5rem;padding-bottom:.5rem;cursor:pointer;">
            <input type="checkbox" wire:model.live="lowStockOnly" style="accent-color:var(--danger);width:16px;height:16px;" />
            <span style="font-size:var(--text-sm);font-weight:500;color:var(--danger);">⚠️ Stok Kritis Saja</span>
        </label>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Sparepart</th>
                    <th>Kategori</th>
                    <th style="text-align:center;">Stok</th>
                    <th>Satuan</th>
                    <th style="text-align:right;">Harga Beli</th>
                    <th style="text-align:right;">Harga Jual</th>
                    <th>Garansi</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->spareParts as $part)
                    <tr style="{{ $part->quantity_available <= 5 ? 'background: #FEF2F2;' : '' }}">
                        <td><code style="font-size:var(--text-xs);background:var(--surface);padding:.2rem .4rem;border-radius:4px;">{{ $part->part_code }}</code></td>
                        <td>
                            <div style="font-weight:500;">{{ $part->name }}</div>
                            @if($part->quantity_available <= 5)
                                <span class="badge badge-danger" style="font-size:.625rem;">⚠️ Stok Kritis</span>
                            @endif
                        </td>
                        <td><span class="badge badge-gray">{{ $part->category ?? '-' }}</span></td>
                        <td style="text-align:center;font-weight:700;font-size:var(--text-lg);color:{{ $part->quantity_available <= 5 ? 'var(--danger)' : 'var(--success)' }};">
                            {{ $part->quantity_available }}
                        </td>
                        <td style="font-size:var(--text-sm);">{{ $part->unit }}</td>
                        <td style="text-align:right;font-size:var(--text-sm);">Rp {{ number_format($part->purchase_price, 0, ',', '.') }}</td>
                        <td style="text-align:right;font-weight:600;">Rp {{ number_format($part->selling_price, 0, ',', '.') }}</td>
                        <td style="font-size:var(--text-sm);">{{ $part->warranty_days > 0 ? $part->warranty_days.' hari' : '-' }}</td>
                        <td>
                            <div class="flex gap-2" style="justify-content:center;">
                                <a href="{{ route('spare-parts.edit', $part) }}" class="btn btn-sm btn-secondary">Edit</a>
                                <a href="{{ route('spare-parts.restock', $part) }}" class="btn btn-sm btn-success">Restock</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <div class="empty-state-icon">📦</div>
                                <div class="empty-state-title">Tidak ada sparepart</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($this->spareParts->hasPages())
        <div style="margin-top:1rem;display:flex;justify-content:center;">{{ $this->spareParts->links() }}</div>
    @endif
</div>
</div>
