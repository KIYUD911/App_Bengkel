<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Invoice {{ $wo->wo_number }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1E293B; }
    .header { background: #0F172A; color: white; padding: 20px 25px; }
    .header h1 { font-size: 20px; font-weight: 700; }
    .header p { font-size: 10px; color: #94A3B8; margin-top: 2px; }
    .badge { display: inline-block; background: #2563EB; color: white; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: 700; margin-top: 6px; }
    .section { padding: 16px 25px; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
    .info-block p.label { font-size: 9px; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 3px; }
    .info-block p.value { font-weight: 600; font-size: 11px; }
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #F1F5F9; padding: 7px 10px; font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; color: #64748B; text-align: left; border-bottom: 1px solid #E2E8F0; }
    tbody td { padding: 8px 10px; border-bottom: 1px solid #F1F5F9; font-size: 10px; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .total-section { padding: 12px 25px; background: #F8FAFC; border-top: 2px solid #E2E8F0; }
    .total-row { display: flex; justify-content: space-between; padding: 3px 0; font-size: 11px; }
    .total-row.grand { font-size: 14px; font-weight: 700; color: #2563EB; border-top: 2px solid #2563EB; margin-top: 6px; padding-top: 8px; }
    .footer { padding: 14px 25px; text-align: center; color: #94A3B8; font-size: 9px; border-top: 1px solid #E2E8F0; }
    .divider { border: none; border-top: 1px solid #E2E8F0; margin: 0; }
</style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <h1>CV Masman Sejahtera</h1>
    <p>Bengkel Kendaraan Bermotor · Jl. Contoh No. 123</p>
    <span class="badge">INVOICE SERVIS</span>
</div>

{{-- Info WO & Pelanggan --}}
<div class="section">
    <div class="info-grid">
        <div class="info-block">
            <p class="label">No. Work Order</p>
            <p class="value" style="font-size:14px;font-family:monospace;">{{ $wo->wo_number }}</p>
            <p class="label" style="margin-top:8px;">Tanggal</p>
            <p class="value">{{ $wo->created_at->format('d F Y') }}</p>
            @if($wo->paid_at)
            <p class="label" style="margin-top:8px;">Tanggal Bayar</p>
            <p class="value">{{ $wo->paid_at->format('d F Y, H:i') }}</p>
            @endif
        </div>
        <div class="info-block">
            <p class="label">Pelanggan</p>
            <p class="value">{{ $wo->customer->name ?? 'Walk-in' }}</p>
            @if($wo->customer?->phone)
            <p style="color:#64748B;font-size:10px;margin-top:2px;">{{ $wo->customer->phone }}</p>
            @endif
            <p class="label" style="margin-top:8px;">Kendaraan</p>
            <p class="value">{{ $wo->vehicle->brand ?? '' }} {{ $wo->vehicle->model ?? '-' }}</p>
            <p style="color:#64748B;font-size:10px;">{{ $wo->vehicle->license_plate ?? '' }} · {{ $wo->vehicle->year ?? '' }}</p>
        </div>
    </div>

    <div style="background:#FFF7ED;border:1px solid #FDE68A;border-radius:4px;padding:8px 12px;margin-bottom:12px;">
        <span style="font-size:9px;color:#92400E;font-weight:700;">KELUHAN: </span>
        <span style="font-size:10px;">{{ $wo->complaint }}</span>
    </div>

    {{-- Tabel Item --}}
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Sparepart</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Subtotal</th>
                <th class="text-center">Garansi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wo->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->sparePart->name ?? '-' }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right" style="font-weight:600;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                <td class="text-center">{{ $item->warranty_days > 0 ? $item->warranty_days.' hr' : '-' }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4" style="padding:8px 10px;">Biaya Jasa</td>
                <td class="text-right" style="font-weight:600;padding:8px 10px;">Rp {{ number_format($wo->labour_cost, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- Total --}}
<div class="total-section">
    <div class="total-row"><span>Total Sparepart</span><span>Rp {{ number_format($wo->total_parts_cost, 0, ',', '.') }}</span></div>
    <div class="total-row"><span>Biaya Jasa</span><span>Rp {{ number_format($wo->labour_cost, 0, ',', '.') }}</span></div>
    <div class="total-row grand"><span>GRAND TOTAL</span><span>Rp {{ number_format($wo->grand_total, 0, ',', '.') }}</span></div>
    @if($wo->payment_method)
    <div class="total-row" style="margin-top:6px;color:#64748B;"><span>Metode Pembayaran</span><span style="font-weight:600;">{{ ucfirst($wo->payment_method) }}</span></div>
    @endif
</div>

{{-- Footer --}}
<div class="footer">
    <p>Terima kasih telah mempercayakan kendaraan Anda kepada CV Masman Sejahtera</p>
    <p style="margin-top:3px;">Simpan invoice ini sebagai bukti garansi servis</p>
    <p style="margin-top:6px;font-size:8px;">Dicetak: {{ now()->format('d/m/Y H:i') }} · Kasir: {{ $wo->user->name ?? '-' }}</p>
</div>

</body>
</html>
