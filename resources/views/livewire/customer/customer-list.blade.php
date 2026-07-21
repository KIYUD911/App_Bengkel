<div>
<div class="page-header">
    <div>
        <h2 class="page-title">Data Pelanggan (CRM)</h2>
        <p class="page-subtitle">Kelola data pelanggan bengkel</p>
    </div>
    <a href="{{ route('customers.create') }}" class="btn btn-primary">+ Pelanggan Baru</a>
</div>

<div class="card" style="margin-bottom:1.25rem;">
    <div class="flex gap-3" style="align-items:flex-end;">
        <div style="flex:1;">
            <label class="form-label">Cari Pelanggan</label>
            <input type="text" wire:model.live.debounce.300ms="search" class="form-input" placeholder="Nama atau nomor telepon..." />
        </div>
        <label style="display:flex;align-items:center;gap:.5rem;padding-bottom:.5rem;cursor:pointer;">
            <input type="checkbox" wire:model.live="vipOnly" style="accent-color:var(--primary);width:16px;height:16px;" />
            <span style="font-size:var(--text-sm);font-weight:500;">⭐ VIP Saja</span>
        </label>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Telepon</th>
                    <th>Total Kunjungan</th>
                    <th>Total Belanja</th>
                    <th>Status</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->customers as $c)
                    <tr>
                        <td>
                            <div style="font-weight:600;">{{ $c->name }}</div>
                            @if($c->is_vip)
                                <span class="badge" style="background:#FDF4FF;color:#7E22CE;border:1px solid #E9D5FF;font-size:.65rem;">⭐ VIP Member</span>
                            @endif
                        </td>
                        <td>{{ $c->phone ?? '-' }}</td>
                        <td style="text-align:center;">{{ $c->visit_count }}×</td>
                        <td style="font-weight:500;">Rp {{ number_format($c->total_spent, 0, ',', '.') }}</td>
                        <td>
                            @if($c->is_vip)
                                <span class="badge" style="background:#FDF4FF;color:#7E22CE;">⭐ VIP</span>
                            @else
                                <span class="badge badge-gray">Regular</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <div class="flex gap-2" style="justify-content:center;">
                                <a href="{{ route('customers.show', $c) }}" class="btn btn-sm btn-secondary">Detail</a>
                                <a href="{{ route('customers.edit', $c) }}" class="btn btn-sm btn-primary">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-state-icon">👤</div>
                                <div class="empty-state-title">Belum ada pelanggan</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($this->customers->hasPages())
        <div style="margin-top:1rem;display:flex;justify-content:center;">{{ $this->customers->links() }}</div>
    @endif
</div>
</div>
