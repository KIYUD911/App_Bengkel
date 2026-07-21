<div>
<div class="page-header">
    <div>
        <h2 class="page-title">🗑️ Recycle Bin</h2>
        <p class="page-subtitle">Data yang telah dihapus — dapat dipulihkan atau dihapus permanen</p>
    </div>
</div>

{{-- Confirm Modal --}}
@if($showConfirm)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:500;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div class="card" style="max-width:420px;width:100%;text-align:center;padding:2rem;animation:modalPop .25s ease;">
        <div style="font-size:3.5rem;margin-bottom:.75rem;">🗑️</div>
        <h3 style="font-weight:700;margin-bottom:.5rem;color:var(--danger);">Hapus Permanen</h3>
        <p style="font-size:var(--text-sm);color:var(--text-muted);line-height:1.6;">
            Data "<strong>{{ $confirmName }}</strong>" akan dihapus secara permanen dan tidak dapat dikembalikan.
        </p>
        <div class="flex gap-2" style="margin-top:1.5rem;justify-content:center;">
            <button wire:click="cancelConfirm" class="btn btn-secondary">Batal</button>
            <button wire:click="forceDelete" class="btn btn-danger" wire:loading.attr="disabled">
                <span wire:loading.remove>🗑️ Ya, Hapus Permanen</span>
                <span wire:loading>Menghapus...</span>
            </button>
        </div>
    </div>
</div>
<style>@keyframes modalPop { from{transform:scale(.95);opacity:0} to{transform:scale(1);opacity:1} }</style>
@endif

{{-- Tabs --}}
@php
    $tabs = [
        'customers'         => ['label' => '👤 Pelanggan',    'count' => $this->deletedCustomers->count()],
        'customer_vehicles' => ['label' => '🚗 Kendaraan',    'count' => $this->deletedVehicles->count()],
        'spare_parts'       => ['label' => '📦 Sparepart',    'count' => $this->deletedSpareParts->count()],
        'work_orders'       => ['label' => '📋 Work Order',   'count' => $this->deletedWorkOrders->count()],
    ];
@endphp

<div style="display:flex;border-bottom:2px solid var(--border);margin-bottom:1.25rem;gap:.25rem;flex-wrap:wrap;">
    @foreach($tabs as $tab => $info)
        <button wire:click="$set('activeTab','{{ $tab }}')"
            style="padding:.625rem 1rem;border:none;background:none;font-size:var(--text-sm);font-weight:{{ $activeTab===$tab ? '600' : '400' }};color:{{ $activeTab===$tab ? 'var(--primary)' : 'var(--text-muted)' }};border-bottom:2px solid {{ $activeTab===$tab ? 'var(--primary)' : 'transparent' }};margin-bottom:-2px;cursor:pointer;display:flex;align-items:center;gap:.5rem;">
            {{ $info['label'] }}
            @if($info['count'] > 0)
                <span class="badge badge-danger" style="font-size:.625rem;min-width:20px;">{{ $info['count'] }}</span>
            @endif
        </button>
    @endforeach
</div>

@php
    $items = match($activeTab) {
        'customers'         => $this->deletedCustomers,
        'customer_vehicles' => $this->deletedVehicles,
        'spare_parts'       => $this->deletedSpareParts,
        'work_orders'       => $this->deletedWorkOrders,
        default             => collect(),
    };
@endphp

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Info Tambahan</th>
                    <th>Dihapus Pada</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    @php
                        $name = match($activeTab) {
                            'customers'         => $item->name,
                            'customer_vehicles' => $item->license_plate . ' (' . $item->brand . ' ' . $item->model . ')',
                            'spare_parts'       => $item->name . ' [' . $item->part_code . ']',
                            'work_orders'       => $item->wo_number,
                            default             => 'Data #' . $item->id,
                        };
                        $extra = match($activeTab) {
                            'customers'         => ($item->phone ?? '-') . ($item->is_vip ? ' · ⭐ VIP' : ''),
                            'customer_vehicles' => 'Pelanggan: ' . ($item->customer?->name ?? '—'),
                            'spare_parts'       => 'Stok: ' . $item->quantity_available . ' ' . $item->unit,
                            'work_orders'       => 'Pelanggan: ' . ($item->customer?->name ?? '—') . ' · ' . $item->status,
                            default             => '-',
                        };
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight:600;">{{ $name }}</div>
                        </td>
                        <td style="font-size:var(--text-sm);color:var(--text-muted);">{{ $extra }}</td>
                        <td style="font-size:var(--text-sm);">{{ $item->deleted_at?->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="flex gap-2" style="justify-content:center;">
                                <button wire:click="restore({{ $item->id }}, '{{ $activeTab }}')"
                                    class="btn btn-sm btn-success"
                                    wire:loading.attr="disabled">
                                    ↺ Pulihkan
                                </button>
                                <button wire:click="confirmForceDelete({{ $item->id }}, '{{ $activeTab }}', '{{ addslashes($name) }}')"
                                    class="btn btn-sm btn-danger">
                                    🗑️ Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state" style="padding:2.5rem;">
                                <div class="empty-state-icon">✅</div>
                                <div class="empty-state-title">Tidak ada data terhapus</div>
                                <div class="empty-state-text">Semua data pada kategori ini dalam kondisi aktif.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
