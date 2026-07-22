<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1E293B; line-height: 1.5; }

.header { background: #0F172A; color: white; padding: 14px 18px; }
.company-name { font-size: 15px; font-weight: 700; }
.company-sub { font-size: 9px; opacity: .7; margin-top: 2px; }
.receipt-badge { display: inline-block; background: #16A34A; color: white; padding: 3px 10px; border-radius: 4px; font-size: 10px; font-weight: 700; letter-spacing: .06em; margin-top: 6px; }

.body { padding: 14px 18px; }

.meta-grid { display: flex; gap: 10px; margin-bottom: 12px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; padding: 10px 12px; }
.meta-col { flex: 1; }
.meta-row { display: flex; margin-bottom: 3px; font-size: 10.5px; }
.meta-label { color: #64748B; width: 90px; flex-shrink: 0; }
.meta-value { font-weight: 600; }

table { width: 100%; border-collapse: collapse; }
thead th { background: #1E293B; color: white; padding: 6px 8px; font-size: 10px; font-weight: 600; text-align: left; }
thead th.right { text-align: right; }
tbody td { padding: 7px 8px; border-bottom: 1px solid #F1F5F9; font-size: 10.5px; }
tbody td.right { text-align: right; }
tbody tr:nth-child(even) { background: #F8FAFC; }

.grand-total { margin-top: 10px; text-align: right; border-top: 2px solid #1E293B; padding-top: 8px; }
.grand-total-label { font-size: 10px; color: #64748B; }
.grand-total-value { font-size: 16px; font-weight: 700; color: #16A34A; }

.payment-row { margin-top: 10px; font-size: 10.5px; display: flex; gap: 16px; }
.payment-badge { background: #16A34A; color: white; padding: 2px 8px; border-radius: 3px; font-size: 9px; font-weight: 700; }

.footer { margin-top: 16px; border-top: 1px dashed #CBD5E1; padding-top: 10px; text-align: center; color: #64748B; font-size: 9.5px; line-height: 1.6; }
</style>
</head>
<body>

<div class="header">
    <div class="company-name">CV Masman Sejahtera</div>
    <div class="company-sub">Jl. Bengkel Sejahtera No. 123 · Telp (021) 123-4567</div>
    <div class="receipt-badge">STRUK PENJUALAN</div>
</div>

<div class="body">

    {{-- Meta --}}
    <div class="meta-grid">
        <div class="meta-col">
            <div class="meta-row"><span class="meta-label">No. Struk</span><span class="meta-value">{{ $sale->sale_number }}</span></div>
            <div class="meta-row"><span class="meta-label">Tanggal</span><span class="meta-value">{{ $sale->paid_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</span></div>
            <div class="meta-row"><span class="meta-label">Kasir</span><span class="meta-value">{{ $sale->user?->name ?? '-' }}</span></div>
        </div>
        <div class="meta-col">
            <div class="meta-row"><span class="meta-label">Pelanggan</span><span class="meta-value">{{ $sale->buyer_name }}</span></div>
            @if($sale->customer)
            <div class="meta-row"><span class="meta-label">Telepon</span><span class="meta-value">{{ $sale->customer?->phone ?? '-' }}</span></div>
            <div class="meta-row"><span class="meta-label">Status</span><span class="meta-value">{{ $sale->customer->is_vip ? '⭐ VIP' : 'Regular' }}</span></div>
            @endif
        </div>
    </div>

    {{-- Items --}}
    <table>
        <thead>
            <tr>
                <th>Nama Part</th>
                <th class="right" style="width:50px;">Qty</th>
                <th class="right" style="width:110px;">Harga</th>
                <th class="right" style="width:120px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td>{{ $item->sparePart?->name ?? '-' }}</td>
                <td class="right">{{ $item->quantity }} {{ $item->sparePart?->unit }}</td>
                <td class="right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Grand Total --}}
    <div class="grand-total">
        <div class="grand-total-label">GRAND TOTAL</div>
        <div class="grand-total-value">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</div>
    </div>

    {{-- Pembayaran --}}
    <div class="payment-row">
        <span class="payment-badge">LUNAS</span>
        <span>Metode: <strong>{{ ucfirst($sale->payment_method ?? '-') }}</strong></span>
    </div>

    @if($sale->notes)
    <div style="margin-top:10px;font-size:10px;color:#64748B;font-style:italic;">Catatan: {{ $sale->notes }}</div>
    @endif

</div>

<div class="footer">
    ✅ Terima kasih atas pembelian Anda!<br>
    Barang yang sudah dibeli tidak dapat dikembalikan.<br>
    <strong>CV Masman Sejahtera</strong>
</div>

</body>
</html>
