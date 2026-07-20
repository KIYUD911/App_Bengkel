<div>
@php $user = auth()->user(); @endphp

{{-- ═══════════════════════════════════════════════════════════
     OWNER DASHBOARD
═══════════════════════════════════════════════════════════ --}}
@if($user->isOwner())

<div class="page-header">
    <div>
        <h2 class="page-title">Dashboard Owner</h2>
        <p class="page-subtitle">Selamat datang, {{ $user->name }}. Berikut ringkasan operasional hari ini.</p>
    </div>
    <div style="font-size: var(--text-sm); color: var(--text-muted); background: white; border: 1px solid var(--border); padding: .4rem .875rem; border-radius: var(--radius-sm);">
        📅 {{ now()->isoFormat('dddd, D MMMM Y') }}
    </div>
</div>

{{-- Stat Cards --}}
<div class="grid grid-4" style="margin-bottom: 1.5rem;">

    <div class="stat-card">
        <div class="stat-icon blue">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">Rp {{ number_format($this->todayRevenue, 0, ',', '.') }}</div>
            <div class="stat-label">Pendapatan Hari Ini</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon amber">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $this->activeWoCount }}</div>
            <div class="stat-label">WO Aktif (Pending + Proses)</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $this->completedWoThisMonth }}</div>
            <div class="stat-label">WO Selesai Bulan Ini</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon purple">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $this->vipCustomerCount }}</div>
            <div class="stat-label">Pelanggan VIP</div>
        </div>
    </div>

</div>

<div class="grid grid-2" style="margin-bottom: 1.5rem;">

    {{-- Revenue Chart --}}
    <div class="card" style="grid-column: span 1;">
        <div class="card-header">
            <span class="card-title">📈 Pendapatan 7 Hari Terakhir</span>
        </div>
        @php
            $chartData = $this->revenueChart;
            $maxVal = collect($chartData)->max('value') ?: 1;
        @endphp
        <div x-data="revenueChart({{ json_encode($chartData) }})" x-init="drawChart()" style="position: relative;">
            <canvas id="revenueCanvas" width="100%" height="200" style="width:100%; height:200px;"></canvas>
        </div>
        <div style="display: flex; justify-content: space-between; margin-top: .75rem;">
            @foreach($chartData as $point)
                <span style="font-size: .6875rem; color: var(--text-muted); text-align: center; flex: 1;">{{ $point['label'] }}</span>
            @endforeach
        </div>
    </div>

    {{-- Critical Stock --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">⚠️ Stok Kritis</span>
            <span class="badge badge-danger">{{ $this->criticalStockCount }} item</span>
        </div>
        @if($this->criticalStockCount === 0)
            <div class="empty-state" style="padding: 2rem;">
                <div class="empty-state-title" style="font-size: var(--text-sm);">Semua stok aman ✅</div>
            </div>
        @else
            @foreach(\App\Models\SparePart::lowStock(5)->orderBy('quantity_available')->take(5)->get() as $part)
                <div style="display: flex; align-items: center; justify-content: space-between; padding: .625rem 0; border-bottom: 1px solid var(--border);">
                    <div>
                        <div style="font-size: var(--text-sm); font-weight: 500;">{{ $part->name }}</div>
                        <div style="font-size: var(--text-xs); color: var(--text-muted);">{{ $part->part_code }}</div>
                    </div>
                    <span class="badge badge-danger">{{ $part->quantity_available }} {{ $part->unit }}</span>
                </div>
            @endforeach
            @if($this->criticalStockCount > 5)
                <div style="text-align: center; margin-top: .75rem;">
                    <a href="{{ route('spare-parts.index') }}" class="btn btn-sm btn-secondary">Lihat semua →</a>
                </div>
            @endif
        @endif
    </div>

</div>

@endif

{{-- ═══════════════════════════════════════════════════════════
     KASIR DASHBOARD
═══════════════════════════════════════════════════════════ --}}
@if($user->isKasir())

<div class="page-header">
    <div>
        <h2 class="page-title">Dashboard Kasir</h2>
        <p class="page-subtitle">Selamat datang, {{ $user->name }}. Ada {{ $this->pendingWoCount }} WO menunggu diproses.</p>
    </div>
</div>

{{-- Quick Stats --}}
<div class="grid grid-3" style="margin-bottom: 1.5rem;">

    <div class="stat-card">
        <div class="stat-icon amber">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $this->pendingWoCount }}</div>
            <div class="stat-label">WO Menunggu Diproses</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $this->inProgressWoCount }}</div>
            <div class="stat-label">WO Sedang Dikerjakan</div>
        </div>
    </div>

    <div class="stat-card" style="background: linear-gradient(135deg, var(--primary) 0%, #7C3AED 100%); border-color: transparent;">
        <div style="flex: 1;">
            <div style="font-size: var(--text-sm); color: rgba(255,255,255,.85); margin-bottom: .75rem;">Aksi Cepat</div>
            <div style="display: flex; flex-direction: column; gap: .5rem;">
                <a href="{{ route('work-orders.create') }}" class="btn" style="background: rgba(255,255,255,.2); color: white; border-color: rgba(255,255,255,.3); justify-content: center;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Buat Work Order
                </a>
                <a href="{{ route('direct-sales.create') }}" class="btn" style="background: rgba(255,255,255,.15); color: white; border-color: rgba(255,255,255,.25); justify-content: center;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Penjualan Langsung
                </a>
            </div>
        </div>
    </div>

</div>

{{-- 5 WO Terbaru --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">📋 5 Work Order Terbaru</span>
        <a href="{{ route('work-orders.index') }}" class="btn btn-sm btn-secondary">Lihat Semua →</a>
    </div>
    @if($this->recentWorkOrders->isEmpty())
        <div class="empty-state"><div class="empty-state-text">Belum ada Work Order.</div></div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No. WO</th>
                        <th>Pelanggan</th>
                        <th>Kendaraan</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->recentWorkOrders as $wo)
                        <tr>
                            <td><a href="{{ route('work-orders.show', $wo) }}" style="font-weight: 600; color: var(--primary);">{{ $wo->wo_number }}</a></td>
                            <td>{{ $wo->customer->name ?? '-' }}</td>
                            <td style="font-size: var(--text-xs);">{{ $wo->vehicle->brand ?? '' }} {{ $wo->vehicle->model ?? '-' }}</td>
                            <td>
                                <span class="badge status-{{ $wo->status }}">
                                    {{ match($wo->status) {
                                        'pending'     => '⏳ Pending',
                                        'in_progress' => '🔧 Dikerjakan',
                                        'completed'   => '✅ Selesai',
                                        'cancelled'   => '❌ Dibatalkan',
                                        default       => $wo->status,
                                    } }}
                                </span>
                            </td>
                            <td style="font-weight: 600;">Rp {{ number_format($wo->grand_total, 0, ',', '.') }}</td>
                            <td style="font-size: var(--text-xs); color: var(--text-muted);">{{ $wo->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endif

{{-- ═══════════════════════════════════════════════════════════
     STAF GUDANG DASHBOARD
═══════════════════════════════════════════════════════════ --}}
@if($user->isStafGudang())

<div class="page-header">
    <div>
        <h2 class="page-title">Dashboard Staf Gudang</h2>
        <p class="page-subtitle">Selamat datang, {{ $user->name }}. Monitor stok dan mutasi barang hari ini.</p>
    </div>
</div>

<div class="grid grid-3" style="margin-bottom: 1.5rem;">

    <div class="stat-card">
        <div class="stat-icon {{ $this->criticalStockCount > 0 ? 'red' : 'green' }}">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value" style="{{ $this->criticalStockCount > 0 ? 'color: var(--danger);' : '' }}">{{ $this->criticalStockCount }}</div>
            <div class="stat-label">Item Stok Kritis (qty ≤ 5)</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $this->totalSparePartCount }}</div>
            <div class="stat-label">Total Item di Katalog</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $this->todayStockIn }}</div>
            <div class="stat-label">Unit Masuk Hari Ini</div>
        </div>
    </div>

</div>

{{-- Daftar Stok Kritis --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">🚨 Daftar Item Stok Kritis</span>
        <a href="{{ route('spare-parts.index') }}" class="btn btn-sm btn-secondary">Kelola Sparepart →</a>
    </div>
    @if($this->criticalStockItems->isEmpty())
        <div class="empty-state" style="padding: 2.5rem;">
            <div class="empty-state-icon">✅</div>
            <div class="empty-state-title">Semua stok dalam kondisi aman!</div>
            <div class="empty-state-text">Tidak ada item dengan stok ≤ 5 unit.</div>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Sparepart</th>
                        <th>Kategori</th>
                        <th>Stok Tersisa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->criticalStockItems as $part)
                        <tr>
                            <td><code style="font-size: var(--text-xs); background: var(--surface); padding: .2rem .4rem; border-radius: 4px;">{{ $part->part_code }}</code></td>
                            <td style="font-weight: 500;">{{ $part->name }}</td>
                            <td><span class="badge badge-gray">{{ $part->category ?? '-' }}</span></td>
                            <td>
                                <span class="badge badge-danger" style="font-size: .875rem; font-weight: 700;">
                                    {{ $part->quantity_available }} {{ $part->unit }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('spare-parts.restock', $part) }}" class="btn btn-sm btn-success">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    Restock
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endif

@push('scripts')
<script>
function revenueChart(data) {
    return {
        data: data,
        drawChart() {
            const canvas = document.getElementById('revenueCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const W = canvas.clientWidth; canvas.width = W;
            const H = 200; canvas.height = H;
            const pad = { top: 20, right: 16, bottom: 30, left: 16 };
            const chartW = W - pad.left - pad.right;
            const chartH = H - pad.top - pad.bottom;
            const maxVal = Math.max(...data.map(d => d.value), 1);
            const points = data.map((d, i) => ({
                x: pad.left + (i / (data.length - 1)) * chartW,
                y: pad.top + chartH - (d.value / maxVal) * chartH,
                value: d.value
            }));

            // Gradient fill
            const grad = ctx.createLinearGradient(0, pad.top, 0, H - pad.bottom);
            grad.addColorStop(0, 'rgba(37,99,235,.25)');
            grad.addColorStop(1, 'rgba(37,99,235,0)');

            // Area
            ctx.beginPath();
            ctx.moveTo(points[0].x, H - pad.bottom);
            points.forEach(p => ctx.lineTo(p.x, p.y));
            ctx.lineTo(points[points.length-1].x, H - pad.bottom);
            ctx.closePath();
            ctx.fillStyle = grad;
            ctx.fill();

            // Line
            ctx.beginPath();
            ctx.moveTo(points[0].x, points[0].y);
            for (let i = 1; i < points.length; i++) {
                const cp = { x: (points[i-1].x + points[i].x) / 2, y: (points[i-1].y + points[i].y) / 2 };
                ctx.quadraticCurveTo(points[i-1].x, points[i-1].y, cp.x, cp.y);
            }
            ctx.lineTo(points[points.length-1].x, points[points.length-1].y);
            ctx.strokeStyle = '#2563EB';
            ctx.lineWidth = 2.5;
            ctx.stroke();

            // Dots
            points.forEach(p => {
                ctx.beginPath();
                ctx.arc(p.x, p.y, 4, 0, Math.PI * 2);
                ctx.fillStyle = '#2563EB';
                ctx.fill();
                ctx.strokeStyle = 'white';
                ctx.lineWidth = 2;
                ctx.stroke();
            });
        }
    }
}
</script>
@endpush
</div>
