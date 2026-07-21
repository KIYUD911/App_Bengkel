<div>
<div class="page-header">
    <div>
        <h2 class="page-title">🛡️ Manajemen Garansi</h2>
        <p class="page-subtitle">Daftar garansi sparepart seluruh pelanggan</p>
    </div>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div class="flex gap-3" style="flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:1;min-width:200px;">
            <label class="form-label">Cari</label>
            <input type="text" wire:model.live.debounce.300ms="search" class="form-input" placeholder="Nama pelanggan atau sparepart..." />
        </div>
        <div style="min-width:180px;">
            <label class="form-label">Status Garansi</label>
            <div style="display:flex;border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden;">
                @foreach(['active'=>'✅ Aktif','all'=>'📋 Semua','expired'=>'❌ Kadaluarsa'] as $val => $lab)
                <button wire:click="$set('filterStatus','{{ $val }}')"
                    style="flex:1;padding:.5rem .4rem;font-size:var(--text-xs);font-weight:500;border:none;cursor:pointer;white-space:nowrap;background:{{ $filterStatus===$val ? 'var(--primary)' : 'white' }};color:{{ $filterStatus===$val ? 'white' : 'var(--text-secondary)' }};">
                    {{ $lab }}
                </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Pelanggan</th>
                    <th>No. WO</th>
                    <th>Nama Sparepart</th>
                    <th>Tgl Beli</th>
                    <th>Tgl Kadaluarsa</th>
                    <th>Sisa Hari</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->warranties as $item)
                    @php
                        $endDate    = \Carbon\Carbon::parse($item->warranty_end_date);
                        $sisaHari   = now()->startOfDay()->diffInDays($endDate, false);
                        $isExpired  = $sisaHari < 0;

                        if ($isExpired) {
                            $color = 'var(--danger)';
                            $bg    = 'var(--danger-light)';
                            $label = 'Kadaluarsa';
                            $badge = 'badge-danger';
                        } elseif ($sisaHari <= 7) {
                            $color = 'var(--danger)';
                            $bg    = 'var(--danger-light)';
                            $label = 'Segera Kadaluarsa';
                            $badge = 'badge-danger';
                        } elseif ($sisaHari <= 30) {
                            $color = 'var(--warning)';
                            $bg    = 'var(--warning-light)';
                            $label = 'Hampir Kadaluarsa';
                            $badge = 'badge-warning';
                        } else {
                            $color = 'var(--success)';
                            $bg    = 'var(--success-light)';
                            $label = 'Aktif';
                            $badge = 'badge-success';
                        }
                    @endphp
                    <tr style="{{ $isExpired ? 'opacity:.7;' : '' }}">
                        <td>
                            <div style="font-weight:500;">{{ $item->workOrder?->customer?->name ?? '-' }}</div>
                            @if($item->workOrder?->customer?->is_vip)
                                <span class="badge" style="background:#FDF4FF;color:#7E22CE;font-size:.6rem;">⭐ VIP</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('work-orders.show', $item->work_order_id) }}"
                                style="font-family:monospace;font-weight:700;color:var(--primary);">
                                {{ $item->workOrder?->wo_number ?? '-' }}
                            </a>
                        </td>
                        <td style="font-weight:500;">{{ $item->sparePart?->name ?? '-' }}</td>
                        <td style="font-size:var(--text-sm);">{{ $item->created_at?->format('d/m/Y') ?? '-' }}</td>
                        <td style="font-size:var(--text-sm);">{{ $endDate->format('d/m/Y') }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:.5rem;">
                                {{-- Progress bar visual --}}
                                @php
                                    $totalDays  = max(1, $item->warranty_days);
                                    $pct        = max(0, min(100, ($sisaHari / $totalDays) * 100));
                                @endphp
                                <div style="width:60px;height:6px;background:var(--border);border-radius:3px;overflow:hidden;flex-shrink:0;">
                                    <div style="height:100%;width:{{ $pct }}%;background:{{ $color }};border-radius:3px;transition:width .3s;"></div>
                                </div>
                                <span style="font-weight:700;color:{{ $color }};font-size:var(--text-sm);">
                                    {{ $isExpired ? abs($sisaHari).' hr lalu' : $sisaHari.' hari' }}
                                </span>
                            </div>
                        </td>
                        <td><span class="badge {{ $badge }}">{{ $label }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-state-icon">🛡️</div>
                                <div class="empty-state-title">Tidak ada data garansi</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($this->warranties->hasPages())
        <div style="margin-top:1rem;display:flex;justify-content:center;">{{ $this->warranties->links() }}</div>
    @endif
</div>
</div>
