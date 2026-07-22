<div>

{{-- ═══════════════════════════════════════════════
     OWNER DASHBOARD
═══════════════════════════════════════════════ --}}
@if(auth()->user()->isOwner())

<div class="page-header">
    <div>
        <h2 class="page-title">👑 Dashboard Owner</h2>
        <p class="page-subtitle">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
    </div>
</div>

{{-- Stat Cards (5 metrics) --}}
<div class="grid grid-4" style="margin-bottom:1.25rem;">
    <div class="stat-card" style="background:linear-gradient(135deg,#1D4ED8,#2563EB);border:none;color:white;">
        <div class="stat-icon" style="background:rgba(255,255,255,.2);color:white;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value" style="color:white;">Rp {{ number_format($this->todayRevenue, 0, ',', '.') }}</div>
            <div class="stat-label" style="color:rgba(255,255,255,.75);">Pendapatan Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $this->todayNewWo }}</div>
            <div class="stat-label">WO Baru Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $this->todayCompletedWo }}</div>
            <div class="stat-label">WO Selesai Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $this->todayDirectSales }}</div>
            <div class="stat-label">Penjualan Langsung Hari Ini</div>
        </div>
    </div>
</div>

{{-- Row 2: Active WO + VIP + Critical --}}
<div class="grid grid-3" style="margin-bottom:1.25rem;">
    <div class="stat-card">
        <div class="stat-icon purple">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $this->activeWoCount }}</div>
            <div class="stat-label">WO Aktif (Pending + In Progress)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FDF4FF;color:#7E22CE;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $this->vipCustomerCount }}</div>
            <div class="stat-label">Pelanggan VIP</div>
        </div>
    </div>
    <div class="stat-card" style="{{ $this->criticalStockCount > 0 ? 'border-color:var(--danger);' : '' }}">
        <div class="stat-icon red">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value" style="{{ $this->criticalStockCount > 0 ? 'color:var(--danger)' : '' }}">{{ $this->criticalStockCount }}</div>
            <div class="stat-label">Stok Kritis (qty &lt; 5)</div>
        </div>
    </div>
</div>

{{-- Line Chart 30 Hari --}}
@php $chartData = $this->revenueChart; @endphp
<div class="card" style="margin-bottom:1.25rem;" x-data="dashLineChart(@js($chartData))" x-init="draw()">
    <div class="card-header">
        <span class="card-title">📈 Pendapatan 30 Hari Terakhir</span>
        <a href="{{ route('reports.revenue') }}" class="btn btn-sm btn-secondary">Lihat Laporan Lengkap</a>
    </div>
    <canvas x-ref="canvas" style="width:100%;height:220px;display:block;"></canvas>
</div>

{{-- 2-column: WO terbaru + DS terbaru --}}
<div class="grid grid-2" style="margin-bottom:1.25rem;">
    {{-- 5 WO Terbaru --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">📋 5 Work Order Terbaru</span>
            <a href="{{ route('work-orders.index') }}" class="btn btn-sm btn-secondary">Lihat Semua</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>No. WO</th><th>Pelanggan</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($this->recentWorkOrders as $wo)
                    <tr>
                        <td><a href="{{ route('work-orders.show', $wo) }}" style="font-family:monospace;font-weight:700;color:var(--primary);">{{ $wo->wo_number }}</a></td>
                        <td style="font-size:var(--text-sm);">{{ $wo->customer?->name ?? '-' }}</td>
                        <td><span class="badge badge-{{ ['pending'=>'warning','in_progress'=>'info','completed'=>'success','cancelled'=>'gray'][$wo->status] ?? 'gray' }}">{{ ucfirst(str_replace('_',' ',$wo->status)) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- 5 DS Terbaru --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">🛒 5 Penjualan Terbaru</span>
            <a href="{{ route('direct-sales.index') }}" class="btn btn-sm btn-secondary">Lihat Semua</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>No. DS</th><th>Pelanggan</th><th style="text-align:right;">Total</th></tr></thead>
                <tbody>
                    @foreach($this->recentDirectSales as $ds)
                    <tr>
                        <td><span style="font-family:monospace;font-size:var(--text-xs);font-weight:700;">{{ $ds->sale_number }}</span></td>
                        <td style="font-size:var(--text-sm);">{{ $ds->buyer_name }}</td>
                        <td style="text-align:right;font-weight:600;color:var(--success);">Rp {{ number_format($ds->grand_total, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Alert Stok Kritis --}}
@if($this->criticalStockItems->count() > 0)
<div class="card" style="border:1.5px solid var(--danger);">
    <div class="card-header">
        <span class="card-title" style="color:var(--danger);">⚠️ Peringatan Stok Kritis</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Kode</th><th>Nama Sparepart</th><th style="text-align:center;">Stok</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($this->criticalStockItems as $part)
                <tr style="background:#FEF2F2;">
                    <td><code style="font-size:var(--text-xs);">{{ $part->part_code }}</code></td>
                    <td style="font-weight:600;color:var(--danger);">{{ $part->name }}</td>
                    <td style="text-align:center;font-weight:700;color:var(--danger);">{{ $part->quantity_available }} {{ $part->unit }}</td>
                    <td><a href="{{ route('spare-parts.restock', $part) }}" class="btn btn-sm btn-warning">+ Restock</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════
     KASIR DASHBOARD
═══════════════════════════════════════════════ --}}
@elseif(auth()->user()->isKasir())

<div class="page-header">
    <div>
        <h2 class="page-title">💼 Dashboard Kasir</h2>
        <p class="page-subtitle">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
    </div>
</div>

<div class="grid grid-3" style="margin-bottom:1.25rem;">
    <div class="stat-card">
        <div class="stat-icon amber"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
        <div class="stat-content"><div class="stat-value">{{ $this->pendingWoCount }}</div><div class="stat-label">WO Menunggu</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg></div>
        <div class="stat-content"><div class="stat-value">{{ $this->inProgressWoCount }}</div><div class="stat-label">WO Sedang Dikerjakan</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></div>
        <div class="stat-content"><div class="stat-value">{{ $this->todayCompletedWo }}</div><div class="stat-label">WO Selesai Hari Ini</div></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">📋 Work Order Terbaru</span>
        <div class="flex gap-2">
            <a href="{{ route('work-orders.create') }}" class="btn btn-sm btn-primary">+ Buat WO</a>
            <a href="{{ route('work-orders.index') }}" class="btn btn-sm btn-secondary">Lihat Semua</a>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. WO</th><th>Pelanggan</th><th>Kendaraan</th><th>Status</th><th>Tanggal</th></tr></thead>
            <tbody>
                @foreach($this->recentWorkOrders as $wo)
                <tr>
                    <td><a href="{{ route('work-orders.show', $wo) }}" style="font-family:monospace;font-weight:700;color:var(--primary);">{{ $wo->wo_number }}</a></td>
                    <td>{{ $wo->customer?->name ?? '-' }}</td>
                    <td style="font-size:var(--text-sm);">{{ $wo->vehicle?->license_plate ?? '-' }}</td>
                    <td><span class="badge badge-{{ ['pending'=>'warning','in_progress'=>'info','completed'=>'success','cancelled'=>'gray'][$wo->status] ?? 'gray' }}">{{ ucfirst(str_replace('_',' ',$wo->status)) }}</span></td>
                    <td style="font-size:var(--text-sm);">{{ $wo->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     STAF GUDANG DASHBOARD
═══════════════════════════════════════════════ --}}
@else

<div class="page-header">
    <div>
        <h2 class="page-title">🏭 Dashboard Staf Gudang</h2>
        <p class="page-subtitle">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
    </div>
</div>

<div class="grid grid-3" style="margin-bottom:1.25rem;">
    <div class="stat-card">
        <div class="stat-icon blue"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg></div>
        <div class="stat-content"><div class="stat-value">{{ $this->totalSparePartCount }}</div><div class="stat-label">Total Jenis Sparepart</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></div>
        <div class="stat-content"><div class="stat-value">{{ $this->todayStockIn }}</div><div class="stat-label">Unit Masuk Hari Ini</div></div>
    </div>
    <div class="stat-card" style="{{ $this->criticalStockCount > 0 ? 'border-color:var(--danger);' : '' }}">
        <div class="stat-icon red"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div>
        <div class="stat-content">
            <div class="stat-value" style="{{ $this->criticalStockCount > 0 ? 'color:var(--danger)' : '' }}">{{ $this->criticalStockCount }}</div>
            <div class="stat-label">Stok Kritis (qty &lt; 5)</div>
        </div>
    </div>
</div>

@if($this->criticalStockItems->count() > 0)
<div class="card" style="border:1.5px solid var(--danger);">
    <div class="card-header">
        <span class="card-title" style="color:var(--danger);">⚠️ Stok Kritis — Perlu Restock Segera</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Kode</th><th>Nama Sparepart</th><th style="text-align:center;">Stok</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($this->criticalStockItems as $part)
                <tr style="background:#FEF2F2;">
                    <td><code style="font-size:var(--text-xs);">{{ $part->part_code }}</code></td>
                    <td style="font-weight:600;color:var(--danger);">{{ $part->name }}</td>
                    <td style="text-align:center;font-weight:700;color:var(--danger);">{{ $part->quantity_available }} {{ $part->unit }}</td>
                    <td><a href="{{ route('spare-parts.restock', $part) }}" class="btn btn-sm btn-warning">+ Restock</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="card"><div class="empty-state" style="padding:2.5rem;"><div class="empty-state-icon">✅</div><div class="empty-state-title">Semua stok aman</div><div class="empty-state-text">Tidak ada sparepart dengan stok kritis saat ini.</div></div></div>
@endif

@endif

@push('scripts')
<script>
function dashLineChart(data) {
    return {
        draw() {
            const canvas = this.$refs.canvas;
            if (!canvas || !data.length) return;

            const W = canvas.offsetWidth || 900;
            const H = 220;
            canvas.width  = W;
            canvas.height = H;

            const ctx  = canvas.getContext('2d');
            const PAD  = { top: 20, right: 20, bottom: 40, left: 65 };
            const cW   = W - PAD.left - PAD.right;
            const cH   = H - PAD.top - PAD.bottom;

            const maxVal = Math.max(...data.map(d => d.total), 1);
            const stepX  = cW / (data.length - 1);

            // Grid
            ctx.strokeStyle = '#F1F5F9';
            ctx.lineWidth   = 1;
            const gridCount = 4;
            for (let i = 0; i <= gridCount; i++) {
                const y = PAD.top + cH - (i / gridCount) * cH;
                ctx.beginPath(); ctx.moveTo(PAD.left, y); ctx.lineTo(PAD.left + cW, y); ctx.stroke();
                const v = maxVal * i / gridCount;
                ctx.fillStyle = '#94A3B8';
                ctx.font      = '10px Inter, sans-serif';
                ctx.textAlign = 'right';
                ctx.fillText(v >= 1e6 ? (v/1e6).toFixed(1)+'jt' : v >= 1000 ? (v/1000).toFixed(0)+'rb' : v.toFixed(0), PAD.left - 6, y + 4);
            }

            // Area fill
            const gradient = ctx.createLinearGradient(0, PAD.top, 0, PAD.top + cH);
            gradient.addColorStop(0, 'rgba(37,99,235,.18)');
            gradient.addColorStop(1, 'rgba(37,99,235,0)');

            ctx.beginPath();
            data.forEach((d, i) => {
                const x = PAD.left + i * stepX;
                const y = PAD.top + cH - (d.total / maxVal) * cH;
                i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
            });
            ctx.lineTo(PAD.left + (data.length - 1) * stepX, PAD.top + cH);
            ctx.lineTo(PAD.left, PAD.top + cH);
            ctx.closePath();
            ctx.fillStyle = gradient;
            ctx.fill();

            // Line
            ctx.beginPath();
            ctx.strokeStyle = '#2563EB';
            ctx.lineWidth   = 2;
            ctx.lineJoin    = 'round';
            data.forEach((d, i) => {
                const x = PAD.left + i * stepX;
                const y = PAD.top + cH - (d.total / maxVal) * cH;
                i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
            });
            ctx.stroke();

            // Dots + X labels (every 5th)
            data.forEach((d, i) => {
                const x = PAD.left + i * stepX;
                const y = PAD.top + cH - (d.total / maxVal) * cH;

                if (d.total > 0) {
                    ctx.beginPath();
                    ctx.arc(x, y, 3, 0, Math.PI * 2);
                    ctx.fillStyle = '#2563EB';
                    ctx.fill();
                }

                if (i % 5 === 0 || i === data.length - 1) {
                    ctx.fillStyle = '#64748B';
                    ctx.font      = '10px Inter, sans-serif';
                    ctx.textAlign = 'center';
                    ctx.fillText(d.label, x, H - PAD.bottom + 14);
                }
            });

            // Axes
            ctx.strokeStyle = '#E2E8F0'; ctx.lineWidth = 1.5;
            ctx.beginPath();
            ctx.moveTo(PAD.left, PAD.top); ctx.lineTo(PAD.left, PAD.top + cH);
            ctx.lineTo(PAD.left + cW, PAD.top + cH);
            ctx.stroke();
        }
    };
}
</script>
@endpush

</div>
