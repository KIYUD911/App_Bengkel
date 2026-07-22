<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1E293B; line-height: 1.5; }

.header { background: #0F172A; color: white; padding: 18px 24px; display: flex; justify-content: space-between; align-items: flex-start; }
.company-name { font-size: 18px; font-weight: 700; letter-spacing: .02em; margin-bottom: 2px; }
.company-info { font-size: 10px; opacity: .8; line-height: 1.6; }
.invoice-badge { background: #2563EB; padding: 6px 14px; border-radius: 6px; font-size: 13px; font-weight: 700; letter-spacing: .05em; margin-bottom: 4px; text-align: center; }
.invoice-meta { font-size: 10px; text-align: right; opacity: .85; line-height: 1.6; }

.body { padding: 18px 24px; }

.info-grid { display: flex; gap: 16px; margin-bottom: 14px; }
.info-box { flex: 1; border: 1px solid #E2E8F0; border-radius: 6px; padding: 10px 12px; }
.info-box-title { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #64748B; margin-bottom: 6px; }
.info-row { display: flex; margin-bottom: 2px; }
.info-label { width: 100px; color: #64748B; flex-shrink: 0; }
.info-value { font-weight: 500; }

.section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748B; margin-bottom: 6px; padding-bottom: 4px; border-bottom: 1.5px solid #E2E8F0; }

table { width: 100%; border-collapse: collapse; }
thead th { background: #1E293B; color: white; padding: 7px 10px; font-size: 10px; font-weight: 600; text-align: left; }
thead th.right { text-align: right; }
tbody td { padding: 8px 10px; border-bottom: 1px solid #F1F5F9; font-size: 10.5px; vertical-align: top; }
tbody td.right { text-align: right; }
tbody tr:nth-child(even) { background: #F8FAFC; }
.warranty-note { font-size: 9px; color: #16A34A; margin-top: 2px; }

.totals { margin-top: 10px; display: flex; justify-content: flex-end; }
.totals-table { width: 260px; border: 1px solid #E2E8F0; border-radius: 6px; overflow: hidden; }
.totals-table td { padding: 6px 12px; border-bottom: 1px solid #F1F5F9; font-size: 11px; }
.totals-table tr:last-child td { background: #1E293B; color: white; font-weight: 700; font-size: 12px; }
.totals-table .right { text-align: right; }

.complaint-box { margin: 14px 0; padding: 10px 12px; border: 1px solid #E2E8F0; border-radius: 6px; }
.text-muted { color: #64748B; }

.payment-info { background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 6px; padding: 10px 12px; margin-top: 12px; display: flex; align-items: center; gap: 16px; }
.payment-badge { background: #16A34A; color: white; padding: 3px 10px; border-radius: 4px; font-weight: 700; font-size: 10px; letter-spacing: .05em; }

.sign-area { margin-top: 24px; display: flex; gap: 20px; }
.sign-box { flex: 1; border: 1px solid #E2E8F0; border-radius: 6px; padding: 10px 12px; }
.sign-title { font-size: 10px; font-weight: 600; color: #64748B; margin-bottom: 50px; }
.sign-line { border-top: 1px solid #94A3B8; padding-top: 4px; font-size: 10px; color: #64748B; text-align: center; }

.footer { background: #F8FAFC; border-top: 1px solid #E2E8F0; padding: 10px 24px; text-align: center; font-size: 9.5px; color: #64748B; }
</style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <div>
        <div class="company-name">CV Masman Sejahtera</div>
        <div class="company-info">
            Jl. Bengkel Sejahtera No. 123, Kota<br>
            Telp: (021) 123-4567 | bengkel@masman.id
        </div>
    </div>
    <div>
        <div class="invoice-badge">INVOICE</div>
        <div class="invoice-meta">
            Dicetak: {{ now()->format('d/m/Y H:i') }}<br>
            No. WO: <strong>{{ $wo->wo_number }}</strong>
        </div>
    </div>
</div>

<div class="body">

    {{-- Info Grid: WO + Pelanggan + Kendaraan --}}
    <div class="info-grid">
        {{-- Info WO --}}
        <div class="info-box">
            <div class="info-box-title">📋 Informasi Work Order</div>
            <div class="info-row"><span class="info-label">No. WO</span><span class="info-value">{{ $wo->wo_number }}</span></div>
            <div class="info-row"><span class="info-label">Tanggal Buat</span><span class="info-value">{{ $wo->created_at->format('d/m/Y H:i') }}</span></div>
            <div class="info-row"><span class="info-label">Status</span><span class="info-value">{{ ucfirst($wo->status) }}</span></div>
            <div class="info-row"><span class="info-label">Kasir</span><span class="info-value">{{ $wo->user?->name ?? '-' }}</span></div>
        </div>

        {{-- Info Pelanggan --}}
        <div class="info-box">
            <div class="info-box-title">👤 Pelanggan</div>
            <div class="info-row"><span class="info-label">Nama</span><span class="info-value">{{ $wo->customer?->name ?? '-' }}</span></div>
            <div class="info-row"><span class="info-label">Telepon</span><span class="info-value">{{ $wo->customer?->phone ?? '-' }}</span></div>
            <div class="info-row"><span class="info-label">Alamat</span><span class="info-value">{{ $wo->customer?->address ?? '-' }}</span></div>
        </div>

        {{-- Info Kendaraan --}}
        <div class="info-box">
            <div class="info-box-title">🚗 Kendaraan</div>
            <div class="info-row"><span class="info-label">Plat Nomor</span><span class="info-value" style="font-weight:700;">{{ $wo->vehicle?->license_plate ?? '-' }}</span></div>
            <div class="info-row"><span class="info-label">Merek/Model</span><span class="info-value">{{ $wo->vehicle?->brand }} {{ $wo->vehicle?->model }}</span></div>
            <div class="info-row"><span class="info-label">Tahun</span><span class="info-value">{{ $wo->vehicle?->year ?? '-' }}</span></div>
            <div class="info-row"><span class="info-label">VIN</span><span class="info-value">{{ $wo->vehicle?->vin ?? '-' }}</span></div>
        </div>
    </div>

    {{-- Keluhan & Solusi --}}
    @if($wo->complaint || $wo->mechanic_notes)
    <div class="complaint-box">
        <div style="display:flex;gap:16px;">
            @if($wo->complaint)
            <div style="flex:1;">
                <div class="section-title">Keluhan Pelanggan</div>
                <p>{{ $wo->complaint ?: '-' }}</p>
            </div>
            @endif
            @if($wo->mechanic_notes)
            <div style="flex:1;">
                <div class="section-title">Solusi / Catatan Mekanik</div>
                <p>{{ $wo->mechanic_notes ?: '-' }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Tabel Sparepart --}}
    <div class="section-title" style="margin-top:4px;">Daftar Sparepart & Jasa</div>
    <table>
        <thead>
            <tr>
                <th style="width:30px;">No</th>
                <th>Nama Sparepart</th>
                <th class="right" style="width:60px;">Qty</th>
                <th class="right" style="width:110px;">Harga Satuan</th>
                <th class="right" style="width:120px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wo->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    {{ $item->sparePart?->name ?? '-' }}
                    @if($item->warranty_days > 0)
                        <div class="warranty-note">✅ Garansi {{ $item->warranty_days }} hari s/d {{ $item->warranty_end_date?->format('d/m/Y') }}</div>
                    @endif
                </td>
                <td class="right">{{ $item->quantity }} {{ $item->sparePart?->unit }}</td>
                <td class="right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="totals">
        <table class="totals-table">
            <tr><td>Subtotal Sparepart</td><td class="right">Rp {{ number_format($wo->total_parts_cost, 0, ',', '.') }}</td></tr>
            <tr><td>Biaya Jasa Mekanik</td><td class="right">Rp {{ number_format($wo->labour_cost, 0, ',', '.') }}</td></tr>
            <tr><td>GRAND TOTAL</td><td class="right">Rp {{ number_format($wo->grand_total, 0, ',', '.') }}</td></tr>
        </table>
    </div>

    {{-- Info Pembayaran --}}
    @if($wo->paid_at)
    <div class="payment-info">
        <span class="payment-badge">LUNAS</span>
        <span>Metode: <strong>{{ ucfirst($wo->payment_method ?? '-') }}</strong></span>
        <span>Tanggal Bayar: <strong>{{ $wo->paid_at->format('d/m/Y H:i') }}</strong></span>
    </div>
    @endif

    {{-- Tanda Tangan --}}
    <div class="sign-area">
        <div class="sign-box">
            <div class="sign-title">Hormat kami,</div>
            <div class="sign-line">( {{ $wo->user?->name ?? 'Kasir' }} )</div>
        </div>
        <div class="sign-box">
            <div class="sign-title">Pelanggan,</div>
            <div class="sign-line">( {{ $wo->customer?->name ?? '____________________' }} )</div>
        </div>
        <div class="sign-box" style="flex:.5;">
            <div class="sign-title" style="margin-bottom:35px;font-size:9px;color:#94A3B8;">
                Stempel / Tanda Tangan Bengkel
            </div>
            <div class="sign-line">( Stempel )</div>
        </div>
    </div>

</div>

{{-- Footer --}}
<div class="footer">
    ⚙️ Garansi berlaku sesuai ketentuan yang tertera di atas. Terima kasih telah mempercayakan kendaraan Anda kepada kami.<br>
    CV Masman Sejahtera — Bengkel Terpercaya Anda
</div>

</body>
</html>
