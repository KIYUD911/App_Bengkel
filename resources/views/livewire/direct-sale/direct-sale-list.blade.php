<div>
<div class="page-header">
    <div>
        <h2 class="page-title">Riwayat Penjualan Langsung</h2>
        <p class="page-subtitle">Semua transaksi penjualan sparepart tanpa WO</p>
    </div>
    <a href="{{ route('direct-sales.create') }}" class="btn btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Penjualan Baru
    </a>
</div>

<div class="card" style="margin-bottom:1.25rem;">
    <div class="flex gap-3" style="flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:1;min-width:200px;">
            <label class="form-label">Cari</label>
            <input type="text" wire:model.live.debounce.300ms="search" class="form-input" placeholder="No DS / Nama Pelanggan..." />
        </div>
        <div style="min-width:145px;">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" wire:model.live="dateFrom" class="form-input" />
        </div>
        <div style="min-width:145px;">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" wire:model.live="dateTo" class="form-input" />
        </div>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No. DS</th>
                    <th>Pembeli</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Kasir</th>
                    <th>Tanggal</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->sales as $sale)
                    <tr>
                        <td><span style="font-family:monospace;font-weight:700;color:var(--primary);">{{ $sale->sale_number }}</span></td>
                        <td>
                            <div style="font-weight:500;">{{ $sale->buyer_name ?? 'Walk-in' }}</div>
                            @if($sale->customer?->is_vip)
                                <span class="badge" style="background:#FDF4FF;color:#7E22CE;font-size:.65rem;">⭐ VIP</span>
                            @endif
                        </td>
                        <td style="font-weight:600;">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                        <td><span class="badge badge-success">{{ ucfirst($sale->payment_method) }}</span></td>
                        <td style="font-size:var(--text-sm);">{{ $sale->user->name ?? '-' }}</td>
                        <td style="font-size:var(--text-xs);color:var(--text-muted);">
                            {{ $sale->paid_at?->format('d/m/Y H:i') ?? '-' }}
                        </td>
                        <td style="text-align:center;">
                            <button wire:click="downloadReceipt({{ $sale->id }})" class="btn btn-sm btn-secondary">
                                🖨️ Struk
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-state-icon">🛒</div>
                                <div class="empty-state-title">Belum ada penjualan</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($this->sales->hasPages())
        <div style="margin-top:1rem;display:flex;justify-content:center;">
            {{ $this->sales->links() }}
        </div>
    @endif
</div>
</div>
