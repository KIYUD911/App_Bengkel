<div>
<div class="page-header">
    <div>
        <h2 class="page-title">📊 Laporan Pendapatan</h2>
        <p class="page-subtitle">Rekapitulasi pendapatan dari Work Order dan Penjualan Langsung</p>
    </div>
    <div class="flex gap-2">
        <button wire:click="exportCsv" class="btn btn-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            CSV
        </button>
        <button wire:click="exportExcel" class="btn btn-success">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Excel
        </button>
    </div>
</div>

{{-- Period Filter --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div class="flex gap-3" style="align-items:flex-end;flex-wrap:wrap;">
        <div>
            <label class="form-label">Periode</label>
            <div style="display:flex;border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden;">
                @foreach(['today'=>'Hari Ini','this_week'=>'Minggu Ini','this_month'=>'Bulan Ini','last_month'=>'Bulan Lalu','custom'=>'Custom'] as $val => $lab)
                    <button wire:click="$set('period','{{ $val }}')"
                        style="padding:.5rem .75rem;font-size:var(--text-xs);font-weight:500;border:none;cursor:pointer;white-space:nowrap;background:{{ $period===$val ? 'var(--primary)' : 'white' }};color:{{ $period===$val ? 'white' : 'var(--text-secondary)' }};border-right:1px solid var(--border);">
                        {{ $lab }}
                    </button>
                @endforeach
            </div>
        </div>
        @if($period === 'custom')
        <div>
            <label class="form-label">Dari</label>
            <input type="date" wire:model.live="dateFrom" class="form-input" style="width:155px;" />
        </div>
        <div>
            <label class="form-label">Sampai</label>
            <input type="date" wire:model.live="dateTo" class="form-input" style="width:155px;" />
        </div>
        @else
        <div style="font-size:var(--text-sm);color:var(--text-muted);padding-bottom:.4rem;">
            {{ $dateFrom }} → {{ $dateTo }}
        </div>
        @endif
    </div>
</div>

@php $s = $this->summary; @endphp

{{-- Stat Cards --}}
<div class="grid grid-4" style="margin-bottom:1.25rem;">
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">Rp {{ number_format($s['grand_total'], 0, ',', '.') }}</div>
            <div class="stat-label">Total Pendapatan</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $s['wo_count'] }}</div>
            <div class="stat-label">Transaksi WO · Rp {{ number_format($s['wo_revenue'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $s['ds_count'] }}</div>
            <div class="stat-label">Penjualan Langsung · Rp {{ number_format($s['ds_revenue'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">Rp {{ number_format($s['avg_per_tx'], 0, ',', '.') }}</div>
            <div class="stat-label">Rata-rata per Transaksi</div>
        </div>
    </div>
</div>

{{-- Bar Chart (Canvas API) --}}
@php $chartData = $this->chartData; @endphp

<div class="card" style="margin-bottom:1.25rem;" x-data="revenueChart(@js($chartData))" x-init="draw()">
    <div class="card-header">
        <span class="card-title">📈 Grafik Pendapatan Harian</span>
        <div style="display:flex;gap:.75rem;font-size:var(--text-xs);">
            <span style="display:flex;align-items:center;gap:.35rem;"><span style="width:12px;height:12px;background:#2563EB;border-radius:2px;display:inline-block;"></span> Work Order</span>
            <span style="display:flex;align-items:center;gap:.35rem;"><span style="width:12px;height:12px;background:#16A34A;border-radius:2px;display:inline-block;"></span> Penjualan</span>
        </div>
    </div>
    <div style="position:relative;overflow-x:auto;">
        <canvas id="revenueBarChart" style="width:100%;height:260px;display:block;" x-ref="canvas"></canvas>
    </div>
</div>

{{-- Tabel Transaksi --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">📋 Detail Transaksi</span>
        <span class="badge badge-gray">{{ $this->transactions->count() }} transaksi</span>
    </div>
    <div class="table-wrap" wire:loading.class="opacity-50">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>No Transaksi</th>
                    <th>Tipe</th>
                    <th>Pelanggan</th>
                    <th style="text-align:right;">Grand Total</th>
                    <th>Metode Bayar</th>
                    <th>Kasir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->transactions as $tx)
                <tr>
                    <td style="white-space:nowrap;font-size:var(--text-sm);">{{ $tx['date'] }}</td>
                    <td>
                        <span class="badge {{ $tx['type']==='WO' ? 'badge-primary' : 'badge-success' }}" style="font-family:monospace;">
                            {{ $tx['type'] }}
                        </span>
                        <span style="font-family:monospace;font-size:var(--text-xs);margin-left:.35rem;">{{ $tx['number'] }}</span>
                    </td>
                    <td>{{ $tx['type'] === 'WO' ? 'Work Order' : 'Penjualan Langsung' }}</td>
                    <td>{{ $tx['customer'] }}</td>
                    <td style="text-align:right;font-weight:600;">Rp {{ number_format($tx['total'], 0, ',', '.') }}</td>
                    <td>{{ $tx['method'] }}</td>
                    <td style="font-size:var(--text-sm);color:var(--text-muted);">{{ $tx['cashier'] }}</td>
                </tr>
                @empty
                <tr><td colspan="7">
                    <div class="empty-state"><div class="empty-state-icon">📊</div><div class="empty-state-title">Belum ada transaksi</div></div>
                </td></tr>
                @endforelse
            </tbody>
            @if($this->transactions->count() > 0)
            <tfoot>
                <tr style="background:var(--primary-light);">
                    <td colspan="4" style="font-weight:700;padding:.875rem 1rem;font-size:var(--text-sm);">TOTAL</td>
                    <td style="text-align:right;font-weight:700;color:var(--primary);font-size:var(--text-base);">
                        Rp {{ number_format($s['grand_total'], 0, ',', '.') }}
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@push('scripts')
<script>
function revenueChart(data) {
    return {
        data: data,
        draw() {
            const canvas = this.$refs.canvas;
            if (!canvas) return;

            // Set actual pixel size
            const W = canvas.offsetWidth || 800;
            const H = 260;
            canvas.width  = W;
            canvas.height = H;

            const ctx  = canvas.getContext('2d');
            const PAD  = { top: 20, right: 20, bottom: 50, left: 70 };
            const chartW = W - PAD.left - PAD.right;
            const chartH = H - PAD.top - PAD.bottom;

            if (!data.length) {
                ctx.fillStyle = '#94A3B8';
                ctx.font = '14px Inter, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('Tidak ada data untuk periode ini', W / 2, H / 2);
                return;
            }

            // Calculate max value
            const maxVal = Math.max(...data.map(d => d.wo + d.ds), 1);
            const barW   = chartW / data.length;
            const groupW = barW * 0.8;
            const singleW = groupW / 2;

            // Y-axis grid lines
            const gridLines = 5;
            ctx.strokeStyle = '#E2E8F0';
            ctx.lineWidth   = 1;
            ctx.font        = '11px Inter, sans-serif';
            ctx.fillStyle   = '#94A3B8';
            ctx.textAlign   = 'right';

            for (let i = 0; i <= gridLines; i++) {
                const y   = PAD.top + chartH - (i / gridLines) * chartH;
                const val = (maxVal * i / gridLines);
                ctx.beginPath();
                ctx.moveTo(PAD.left, y);
                ctx.lineTo(PAD.left + chartW, y);
                ctx.stroke();
                const label = val >= 1_000_000 ? (val / 1_000_000).toFixed(1) + 'jt' :
                              val >= 1000     ? (val / 1000).toFixed(0) + 'rb' :
                              val.toFixed(0);
                ctx.fillText(label, PAD.left - 8, y + 4);
            }

            // Bars
            data.forEach((d, i) => {
                const x    = PAD.left + i * barW + (barW - groupW) / 2;

                // WO bar (blue)
                const woH  = (d.wo / maxVal) * chartH;
                ctx.fillStyle = '#2563EB';
                ctx.fillRect(x, PAD.top + chartH - woH, singleW, woH);

                // DS bar (green)
                const dsH  = (d.ds / maxVal) * chartH;
                ctx.fillStyle = '#16A34A';
                ctx.fillRect(x + singleW, PAD.top + chartH - dsH, singleW, dsH);

                // X-axis label — show every Nth label to avoid overlap
                const showEvery = Math.ceil(data.length / 20);
                if (i % showEvery === 0) {
                    ctx.fillStyle = '#475569';
                    ctx.textAlign = 'center';
                    ctx.font      = '10px Inter, sans-serif';
                    ctx.fillText(d.label, x + groupW / 2, H - PAD.bottom + 16);
                }
            });

            // Axes
            ctx.strokeStyle = '#CBD5E1';
            ctx.lineWidth   = 1.5;
            ctx.beginPath();
            ctx.moveTo(PAD.left, PAD.top);
            ctx.lineTo(PAD.left, PAD.top + chartH);
            ctx.lineTo(PAD.left + chartW, PAD.top + chartH);
            ctx.stroke();
        }
    };
}
</script>
@endpush
</div>
