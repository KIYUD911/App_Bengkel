<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Struk {{ $sale->sale_number }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #1E293B; width: 226px; }
    .center { text-align: center; }
    .bold { font-weight: 700; }
    h1 { font-size: 12px; font-weight: 700; }
    .divider { border: none; border-top: 1px dashed #CBD5E1; margin: 5px 0; }
    table { width: 100%; }
    td { padding: 2px 0; vertical-align: top; }
    td.right { text-align: right; }
    .total-row td { font-weight: 700; font-size: 11px; border-top: 1px solid #1E293B; padding-top: 4px; }
</style>
</head>
<body>

<div class="center" style="padding: 8px 0;">
    <h1>CV MASMAN SEJAHTERA</h1>
    <p>Bengkel Kendaraan Bermotor</p>
    <p>Jl. Contoh No. 123 · Telp: 08xxx</p>
</div>

<hr class="divider">

<table>
    <tr><td>No. Struk</td><td class="right bold">{{ $sale->sale_number }}</td></tr>
    <tr><td>Tanggal</td><td class="right">{{ $sale->paid_at?->format('d/m/Y H:i') }}</td></tr>
    <tr><td>Kasir</td><td class="right">{{ $sale->user->name ?? '-' }}</td></tr>
    <tr><td>Pembeli</td><td class="right">{{ $sale->buyer_name ?? 'Walk-in' }}</td></tr>
</table>

<hr class="divider">

@foreach($sale->items as $item)
<table style="margin-bottom:3px;">
    <tr><td colspan="2" class="bold">{{ $item->sparePart->name ?? '-' }}</td></tr>
    <tr>
        <td>{{ $item->quantity }} × Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
        <td class="right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
    </tr>
</table>
@endforeach

<hr class="divider">

<table>
    <tr class="total-row">
        <td>TOTAL</td>
        <td class="right">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td>Pembayaran</td>
        <td class="right bold">{{ ucfirst($sale->payment_method) }}</td>
    </tr>
</table>

<hr class="divider">

<div class="center" style="padding: 8px 0;">
    <p class="bold">Terima kasih atas kunjungan Anda!</p>
    <p style="margin-top:3px;">Barang yang sudah dibeli tidak dapat dikembalikan</p>
</div>

</body>
</html>
