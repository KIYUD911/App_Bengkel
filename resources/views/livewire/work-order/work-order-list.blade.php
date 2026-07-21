<div>
{{-- Header --}}
<div class="page-header">
    <div>
        <h2 class="page-title">Work Order</h2>
        <p class="page-subtitle">Manajemen pekerjaan servis kendaraan</p>
    </div>
    <a href="{{ route('work-orders.create') }}" class="btn btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Buat WO Baru
    </a>
</div>

{{-- Filter Bar --}}
<div class="card" style="margin-bottom: 1.25rem;">
    <div class="flex gap-3" style="flex-wrap: wrap; align-items: flex-end;">

        {{-- Search --}}
        <div style="flex: 1; min-width: 200px;">
            <label class="form-label">Cari</label>
            <div style="position: relative;">
                <svg style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:var(--text-muted);" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="form-input" style="padding-left: 2.5rem;"
                    placeholder="No WO / Nama Pelanggan..." />
            </div>
        </div>

        {{-- Status Filter --}}
        <div style="min-width: 160px;">
            <label class="form-label">Status</label>
            <select wire:model.live="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="in_progress">Dikerjakan</option>
                <option value="completed">Selesai</option>
                <option value="cancelled">Dibatalkan</option>
            </select>
        </div>

        {{-- Date From --}}
        <div style="min-width: 145px;">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" wire:model.live="dateFrom" class="form-input" />
        </div>

        {{-- Date To --}}
        <div style="min-width: 145px;">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" wire:model.live="dateTo" class="form-input" />
        </div>

        {{-- Reset --}}
        @if($search || $status || $dateFrom || $dateTo)
        <div>
            <label class="form-label" style="opacity:0;">reset</label>
            <button wire:click="$set('search',''); $set('status',''); $set('dateFrom',''); $set('dateTo','')"
                class="btn btn-secondary">Reset</button>
        </div>
        @endif

    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="table-wrap" wire:loading.class="opacity-50">
        <table>
            <thead>
                <tr>
                    <th>No. WO</th>
                    <th>Pelanggan</th>
                    <th>Kendaraan</th>
                    <th>Status</th>
                    <th>Grand Total</th>
                    <th>Kasir</th>
                    <th>Tanggal</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->workOrders as $wo)
                    <tr>
                        <td>
                            <a href="{{ route('work-orders.show', $wo) }}"
                                style="font-weight:700; color:var(--primary); font-family:monospace;">
                                {{ $wo->wo_number }}
                            </a>
                        </td>
                        <td>
                            <div style="font-weight:500;">{{ $wo->customer->name ?? '-' }}</div>
                            <div style="font-size:var(--text-xs);color:var(--text-muted);">{{ $wo->customer->phone ?? '' }}</div>
                        </td>
                        <td style="font-size:var(--text-sm);">
                            <div>{{ $wo->vehicle->brand ?? '' }} {{ $wo->vehicle->model ?? '-' }}</div>
                            <div style="font-size:var(--text-xs);color:var(--text-muted);">{{ $wo->vehicle->license_plate ?? '' }}</div>
                        </td>
                        <td>
                            @php
                                $statusCfg = [
                                    'pending'     => ['label'=>'⏳ Pending',    'class'=>'badge status-pending'],
                                    'in_progress' => ['label'=>'🔧 Dikerjakan', 'class'=>'badge status-in_progress'],
                                    'completed'   => ['label'=>'✅ Selesai',    'class'=>'badge status-completed'],
                                    'cancelled'   => ['label'=>'❌ Dibatalkan', 'class'=>'badge status-cancelled', 'style'=>'text-decoration:line-through;'],
                                ];
                                $cfg = $statusCfg[$wo->status] ?? ['label'=>$wo->status,'class'=>'badge badge-gray','style'=>''];
                            @endphp
                            <span class="{{ $cfg['class'] }}" style="{{ $cfg['style'] ?? '' }}">{{ $cfg['label'] }}</span>
                        </td>
                        <td style="font-weight:600;">Rp {{ number_format($wo->grand_total, 0, ',', '.') }}</td>
                        <td style="font-size:var(--text-sm);">{{ $wo->user->name ?? '-' }}</td>
                        <td style="font-size:var(--text-xs);color:var(--text-muted);">
                            {{ $wo->created_at->format('d/m/Y') }}<br>
                            {{ $wo->created_at->format('H:i') }}
                        </td>
                        <td style="text-align:center;">
                            <div class="flex gap-2" style="justify-content:center;">
                                <a href="{{ route('work-orders.show', $wo) }}" class="btn btn-sm btn-secondary">Detail</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-state-icon">📋</div>
                                <div class="empty-state-title">Tidak ada Work Order</div>
                                <div class="empty-state-text">Belum ada WO yang sesuai filter.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($this->workOrders->hasPages())
        <div style="margin-top: 1rem; display: flex; justify-content: center;">
            {{ $this->workOrders->links() }}
        </div>
    @endif
</div>
</div>
