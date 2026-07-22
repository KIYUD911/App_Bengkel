<div>
<div class="page-header">
    <div>
        <h2 class="page-title">📦 Laporan Inventori</h2>
        <p class="page-subtitle">Stok sparepart & produk terlaris</p>
    </div>
    <button wire:click="exportExcel" class="btn btn-success">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export Excel
    </button>
</div>

{{-- Top 10 Terlaris --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-header"><span class="card-title">🏆 Top 10 Sparepart Terlaris</span></div>
    @php $top = $this->topSelling; $maxSold = $top->max('total_sold') ?: 1; @endphp
    <div style="display:flex;flex-direction:column;gap:.5rem;">
        @forelse($top as $i => $part)
        <div style="display:flex;align-items:center;gap:.75rem;">
            <div style="width:24px;height:24px;border-radius:50%;background:{{ $i < 3 ? 'var(--warning)' : 'var(--border)' }};display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;flex-shrink:0;color:{{ $i < 3 ? 'white' : 'var(--text-muted)' }};">{{ $i+1 }}</div>
            <div style="min-width:180px;font-size:var(--text-sm);font-weight:500;">{{ $part['name'] }}</div>
            <div style="flex:1;height:12px;background:var(--border);border-radius:6px;overflow:hidden;">
                <div style="height:100%;width:{{ round(($part['total_sold']/$maxSold)*100) }}%;background:{{ $i === 0 ? '#F59E0B' : ($i < 3 ? '#16A34A' : 'var(--primary)') }};border-radius:6px;transition:width .5s;"></div>
            </div>
            <div style="font-size:var(--text-sm);font-weight:700;min-width:60px;text-align:right;">{{ $part['total_sold'] }} unit</div>
            <div style="font-size:var(--text-xs);color:var(--text-muted);min-width:55px;text-align:right;">Stok: {{ $part['stock'] }}</div>
        </div>
        @empty
        <div class="empty-state" style="padding:2rem;"><div class="empty-state-icon">📦</div><div class="empty-state-title">Belum ada data penjualan</div></div>
        @endforelse
    </div>
</div>

{{-- Filter & Tabel Semua Sparepart --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">📋 Semua Sparepart</span>
        <div class="flex gap-2" style="align-items:center;">
            <select wire:model.live="category" class="form-select" style="width:180px;">
                <option value="">Semua Kategori</option>
                @foreach($this->categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>
            <span class="badge badge-gray">{{ $this->allParts->count() }} item</span>
        </div>
    </div>

    {{-- Legend --}}
    <div style="margin-bottom:.75rem;font-size:var(--text-xs);color:var(--text-muted);display:flex;align-items:center;gap:.5rem;">
        <span style="background:#FEE2E2;display:inline-block;width:12px;height:12px;border-radius:2px;"></span>
        Stok kritis (qty &lt; 5)
    </div>

    <div class="table-wrap" wire:loading.class="opacity-50">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Sparepart</th>
                    <th>Kategori</th>
                    <th style="text-align:right;">Stok</th>
                    <th style="text-align:right;">Total Terjual</th>
                    <th style="text-align:right;">Harga Beli</th>
                    <th style="text-align:right;">Nilai Stok</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->allParts as $part)
                @php $critical = $part->quantity_available < 5; @endphp
                <tr style="{{ $critical ? 'background:#FEF2F2;' : '' }}">
                    <td>
                        <code style="font-size:var(--text-xs);">{{ $part->part_code }}</code>
                    </td>
                    <td>
                        <div style="font-weight:500;{{ $critical ? 'color:var(--danger);' : '' }}">{{ $part->name }}</div>
                        @if($critical)
                            <span style="font-size:.65rem;font-weight:700;color:var(--danger);">⚠ STOK KRITIS</span>
                        @endif
                    </td>
                    <td style="font-size:var(--text-sm);color:var(--text-muted);">{{ $part->category ?? '-' }}</td>
                    <td style="text-align:right;font-weight:700;color:{{ $critical ? 'var(--danger)' : 'var(--text)' }};">
                        {{ $part->quantity_available }} {{ $part->unit }}
                    </td>
                    <td style="text-align:right;font-size:var(--text-sm);">{{ $part->total_sold }} unit</td>
                    <td style="text-align:right;font-size:var(--text-sm);">Rp {{ number_format($part->purchase_price, 0, ',', '.') }}</td>
                    <td style="text-align:right;font-weight:600;color:var(--primary);">Rp {{ number_format($part->stock_value, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="7">
                    <div class="empty-state"><div class="empty-state-icon">📦</div><div class="empty-state-title">Tidak ada data</div></div>
                </td></tr>
                @endforelse
            </tbody>
            @if($this->allParts->count() > 0)
            <tfoot>
                <tr style="background:var(--success-light);">
                    <td colspan="6" style="font-weight:700;padding:.875rem 1rem;font-size:var(--text-sm);">Total Nilai Inventori</td>
                    <td style="text-align:right;font-weight:700;color:var(--success);font-size:var(--text-base);">
                        Rp {{ number_format($this->allParts->sum('stock_value'), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
</div>
