<div>
<div class="page-header">
    <div>
        <h2 class="page-title" style="font-family:monospace;">{{ $workOrder->wo_number }}</h2>
        <p class="page-subtitle">{{ $workOrder->created_at->format('d F Y, H:i') }}</p>
    </div>
    <div class="flex gap-2">
        @php
            $sc = ['pending'=>'badge status-pending','in_progress'=>'badge status-in_progress','completed'=>'badge status-completed','cancelled'=>'badge status-cancelled'];
            $sl = ['pending'=>'⏳ Pending','in_progress'=>'🔧 Dikerjakan','completed'=>'✅ Selesai','cancelled'=>'❌ Dibatalkan'];
        @endphp
        <span class="{{ $sc[$workOrder->status] ?? 'badge badge-gray' }}" style="font-size:var(--text-sm);padding:.4rem .875rem;">
            {{ $sl[$workOrder->status] ?? $workOrder->status }}
        </span>
        <a href="{{ route('work-orders.index') }}" class="btn btn-secondary">← Kembali</a>
    </div>
</div>

{{-- Alerts --}}
@if($successMessage)
    <div class="alert alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)" x-transition>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ $successMessage }}
    </div>
@endif
@if($errorMessage)
    <div class="alert alert-danger">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ $errorMessage }}
        <button wire:click="$set('errorMessage', null)" style="margin-left:auto;background:none;border:none;cursor:pointer;color:inherit;font-size:1.25rem;line-height:1;">×</button>
    </div>
@endif

<div class="grid grid-2" style="align-items:start;">

    {{-- Kiri: Info WO + Action Buttons --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;">

        {{-- Info WO --}}
        <div class="card">
            <div class="card-header"><span class="card-title">📋 Informasi Work Order</span></div>
            <table style="width:100%;">
                <tbody>
                    <tr><td style="padding:.5rem 0;color:var(--text-muted);font-size:var(--text-sm);width:40%;">Pelanggan</td><td style="font-weight:500;">{{ $workOrder->customer->name ?? '-' }} @if($workOrder->customer?->is_vip)<span class="badge" style="background:#FDF4FF;color:#7E22CE;border:1px solid #E9D5FF;font-size:.65rem;margin-left:.35rem;">⭐ VIP</span>@endif</td></tr>
                    <tr><td style="padding:.5rem 0;color:var(--text-muted);font-size:var(--text-sm);">Kendaraan</td><td>{{ $workOrder->vehicle->brand ?? '' }} {{ $workOrder->vehicle->model ?? '-' }} ({{ $workOrder->vehicle->year ?? '' }})<br><span style="font-size:var(--text-xs);color:var(--text-muted);">{{ $workOrder->vehicle->license_plate ?? '' }}</span></td></tr>
                    <tr><td style="padding:.5rem 0;color:var(--text-muted);font-size:var(--text-sm);">Kasir</td><td>{{ $workOrder->user->name ?? '-' }}</td></tr>
                    <tr><td style="padding:.5rem 0;color:var(--text-muted);font-size:var(--text-sm);">Keluhan</td><td style="font-style:italic;">{{ $workOrder->complaint }}</td></tr>
                    @if($workOrder->mechanic_notes)
                    <tr><td style="padding:.5rem 0;color:var(--text-muted);font-size:var(--text-sm);">Catatan Mekanik</td><td>{{ $workOrder->mechanic_notes }}</td></tr>
                    @endif
                    @if($workOrder->status === 'cancelled')
                    <tr><td style="padding:.5rem 0;color:var(--danger);font-size:var(--text-sm);">Alasan Cancel</td><td style="color:var(--danger);">{{ $workOrder->cancel_reason }}</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Ringkasan Biaya --}}
        <div class="card">
            <div class="card-header"><span class="card-title">💰 Ringkasan Biaya</span></div>
            <table style="width:100%;">
                <tbody>
                    <tr><td style="padding:.5rem 0;color:var(--text-muted);font-size:var(--text-sm);">Biaya Jasa</td><td style="text-align:right;font-weight:500;">Rp {{ number_format($workOrder->labour_cost, 0, ',', '.') }}</td></tr>
                    <tr><td style="padding:.5rem 0;color:var(--text-muted);font-size:var(--text-sm);">Total Sparepart</td><td style="text-align:right;font-weight:500;">Rp {{ number_format($workOrder->total_parts_cost, 0, ',', '.') }}</td></tr>
                    <tr style="border-top:2px solid var(--border);">
                        <td style="padding:.75rem 0;font-weight:700;font-size:var(--text-lg);">GRAND TOTAL</td>
                        <td style="text-align:right;font-weight:700;font-size:var(--text-lg);color:var(--primary);">Rp {{ number_format($workOrder->grand_total, 0, ',', '.') }}</td>
                    </tr>
                    @if($workOrder->paid_at)
                    <tr><td style="padding:.25rem 0;font-size:var(--text-xs);color:var(--success);">✅ Dibayar via {{ ucfirst($workOrder->payment_method) }}</td><td style="text-align:right;font-size:var(--text-xs);color:var(--text-muted);">{{ $workOrder->paid_at->format('d/m/Y H:i') }}</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Action Buttons --}}
        <div class="card">
            <div class="card-header"><span class="card-title">⚡ Aksi</span></div>
            <div style="display:flex;flex-direction:column;gap:.5rem;">

                {{-- Update Status --}}
                @if($workOrder->status === 'pending')
                    <button wire:click="updateStatus('in_progress')" class="btn btn-primary" wire:confirm="Ubah status WO ke 'Dikerjakan'?">
                        🔧 Mulai Kerjakan
                    </button>
                @endif
                @if($workOrder->status === 'in_progress')
                    <button wire:click="updateStatus('completed')" class="btn btn-success" wire:confirm="Tandai WO sebagai 'Selesai'?">
                        ✅ Tandai Selesai
                    </button>
                @endif

                {{-- Proses Pembayaran --}}
                @if($workOrder->status === 'completed' && !$workOrder->paid_at)
                    <button wire:click="$set('showPaymentModal', true)" class="btn btn-primary">
                        💳 Proses Pembayaran
                    </button>
                @endif

                {{-- Cetak Invoice --}}
                @if($workOrder->paid_at)
                    <button wire:click="downloadInvoice" class="btn btn-secondary">
                        🖨️ Cetak Invoice PDF
                    </button>
                @endif

                {{-- Feedback --}}
                @if($workOrder->status === 'completed' && !$workOrder->feedback)
                    <button wire:click="$toggle('showFeedbackForm')" class="btn btn-secondary">
                        ⭐ Tambah Feedback
                    </button>
                @endif

                {{-- Cancel --}}
                @if(in_array($workOrder->status, ['pending', 'in_progress']))
                    <button wire:click="openCancelModal" class="btn btn-danger">
                        ❌ Batalkan Work Order
                    </button>
                @endif

            </div>

            {{-- Feedback terpasang --}}
            @if($workOrder->feedback)
                <div style="margin-top:1rem;padding:.75rem;background:var(--success-light);border-radius:var(--radius-sm);">
                    <div style="font-size:var(--text-sm);font-weight:600;color:var(--success);">Feedback: {{ $workOrder->feedback->stars }}</div>
                    @if($workOrder->feedback->comment)
                        <div style="font-size:var(--text-xs);color:var(--text-secondary);margin-top:.25rem;">"{{ $workOrder->feedback->comment }}"</div>
                    @endif
                </div>
            @endif
        </div>

    </div>

    {{-- Kanan: Tabel Item + Form Tambah --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;">

        {{-- Daftar Sparepart --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">🔩 Item Sparepart</span>
                <span class="badge badge-gray">{{ $workOrder->items->count() }} item</span>
            </div>
            @if($workOrder->items->isEmpty())
                <div class="empty-state" style="padding:2rem;">
                    <div class="empty-state-icon">📦</div>
                    <div class="empty-state-text">Belum ada item sparepart</div>
                </div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Part</th>
                                <th style="text-align:center;">Qty</th>
                                <th style="text-align:right;">Harga</th>
                                <th style="text-align:right;">Subtotal</th>
                                <th>Garansi</th>
                                @if(in_array($workOrder->status, ['pending','in_progress']))
                                <th></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workOrder->items as $item)
                                <tr>
                                    <td>
                                        <div style="font-weight:500;">{{ $item->sparePart->name ?? '-' }}</div>
                                        <div style="font-size:var(--text-xs);color:var(--text-muted);">{{ $item->sparePart->part_code ?? '' }}</div>
                                    </td>
                                    <td style="text-align:center;">{{ $item->quantity }}</td>
                                    <td style="text-align:right;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td style="text-align:right;font-weight:600;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    <td style="font-size:var(--text-xs);">
                                        @if($item->warranty_days > 0)
                                            <span class="badge {{ $item->isWarrantyActive() ? 'badge-success' : 'badge-gray' }}">
                                                {{ $item->warranty_days }} hari
                                                @if(!$item->isWarrantyActive()) (Kadaluarsa) @endif
                                            </span>
                                        @else
                                            <span style="color:var(--text-muted);">—</span>
                                        @endif
                                    </td>
                                    @if(in_array($workOrder->status, ['pending','in_progress']))
                                    <td>
                                        <button wire:click="removeItem({{ $item->id }})"
                                            wire:confirm="Hapus item ini? Stok akan dikembalikan."
                                            class="btn btn-sm btn-danger">Hapus</button>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Form Tambah Item (hanya jika WO masih aktif) --}}
        @if(in_array($workOrder->status, ['pending', 'in_progress']))
        <div class="card">
            <div class="card-header"><span class="card-title">➕ Tambah Sparepart</span></div>

            {{-- Part Search --}}
            <div class="form-group" style="position:relative;">
                <label class="form-label">Cari Sparepart</label>
                <input type="text" wire:model.live.debounce.300ms="partSearch"
                    class="form-input" placeholder="Ketik nama atau kode part..."
                    autocomplete="off" />

                @if(count($partResults) > 0)
                    <div style="position:absolute;top:100%;left:0;right:0;background:white;border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-md);z-index:50;margin-top:.25rem;max-height:240px;overflow-y:auto;">
                        @foreach($partResults as $p)
                            <div wire:click="selectPart({{ $p['id'] }})"
                                style="padding:.625rem 1rem;cursor:pointer;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);"
                                onmouseover="this.style.background='var(--surface)'" onmouseout="this.style.background='white'">
                                <div>
                                    <div style="font-weight:500;font-size:var(--text-sm);">{{ $p['name'] }}</div>
                                    <div style="font-size:var(--text-xs);color:var(--text-muted);">{{ $p['part_code'] }} · Stok: {{ $p['quantity_available'] }} {{ $p['unit'] }}</div>
                                </div>
                                <div style="text-align:right;">
                                    <div style="font-weight:600;font-size:var(--text-sm);">Rp {{ number_format($p['selling_price'], 0, ',', '.') }}</div>
                                    @if($p['quantity_available'] <= 5)
                                        <span class="badge badge-danger" style="font-size:.6rem;">Stok Kritis</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Part terpilih + Qty --}}
            @if($selectedPart)
                <div style="background:var(--primary-light);border:1px solid #BFDBFE;border-radius:var(--radius-sm);padding:.75rem 1rem;margin-bottom:1rem;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <div style="font-weight:600;">{{ $selectedPart['name'] }}</div>
                            <div style="font-size:var(--text-xs);color:var(--text-muted);">
                                Stok tersedia: <strong>{{ $selectedPart['quantity_available'] }} {{ $selectedPart['unit'] }}</strong>
                                · Harga: <strong>Rp {{ number_format($selectedPart['selling_price'], 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        <button wire:click="$set('selectedPart', null); $set('selectedPartId', null); $set('partSearch', '')"
                            style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:1.25rem;">×</button>
                    </div>
                </div>

                <div class="flex gap-3" style="align-items:flex-end;">
                    <div class="form-group" style="width:120px;margin-bottom:0;">
                        <label class="form-label">Qty</label>
                        <input type="number" wire:model.live="itemQty" class="form-input"
                            min="1" max="{{ $selectedPart['quantity_available'] }}" />
                    </div>
                    @if($itemQty > 0 && $selectedPart)
                    <div style="font-size:var(--text-sm);color:var(--text-muted);padding-bottom:.6rem;">
                        = Rp {{ number_format($selectedPart['selling_price'] * $itemQty, 0, ',', '.') }}
                    </div>
                    @endif
                    <div style="padding-bottom:0;">
                        <button wire:click="addItem" class="btn btn-primary" wire:loading.attr="disabled"
                            @if($itemQty > $selectedPart['quantity_available']) disabled title="Stok tidak mencukupi" @endif>
                            <span wire:loading.remove>Tambahkan</span>
                            <span wire:loading>...</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
        @endif

        {{-- Feedback Form --}}
        @if($showFeedbackForm)
            <div class="card">
                <div class="card-header"><span class="card-title">⭐ Form Feedback Pelanggan</span></div>
                <div class="form-group">
                    <label class="form-label">Rating (1-5)</label>
                    <div class="flex gap-2">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" wire:click="$set('feedbackRating', {{ $i }})"
                                style="font-size:1.75rem;background:none;border:none;cursor:pointer;opacity:{{ $feedbackRating >= $i ? '1' : '0.3' }};">⭐</button>
                        @endfor
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Komentar (opsional)</label>
                    <textarea wire:model="feedbackComment" class="form-textarea" rows="2" placeholder="Ceritakan pengalaman servis..."></textarea>
                </div>
                <div class="flex gap-2">
                    <button wire:click="submitFeedback" class="btn btn-primary btn-sm">Kirim Feedback</button>
                    <button wire:click="$toggle('showFeedbackForm')" class="btn btn-secondary btn-sm">Batal</button>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ═══ MODAL CANCEL ════════════════════════════════════════════ --}}
@if($showCancelModal)
    <div style="position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:200;display:flex;align-items:center;justify-content:center;padding:1rem;">
        <div class="card" style="max-width:500px;width:100%;box-shadow:var(--shadow-lg);" @click.outside="$wire.set('showCancelModal', false)">
            <div style="text-align:center;padding:1rem 0;">
                <div style="font-size:3rem;">⚠️</div>
                <h3 style="margin-top:.5rem;color:var(--danger);">Batalkan Work Order?</h3>
                <p style="font-size:var(--text-sm);color:var(--text-muted);margin-top:.5rem;">
                    Tindakan ini akan membatalkan <strong>{{ $workOrder->wo_number }}</strong> dan mengembalikan stok semua sparepart.
                </p>
            </div>

            {{-- Ringkasan stok yang dikembalikan --}}
            @if($workOrder->items->isNotEmpty())
                <div style="background:var(--warning-light);border:1px solid #FDE68A;border-radius:var(--radius-sm);padding:.875rem;margin-bottom:1rem;">
                    <div style="font-size:var(--text-xs);font-weight:600;color:var(--warning);margin-bottom:.5rem;">STOK YANG AKAN DIKEMBALIKAN:</div>
                    @foreach($workOrder->items as $item)
                        <div style="display:flex;justify-content:space-between;font-size:var(--text-xs);">
                            <span>{{ $item->sparePart->name ?? '?' }}</span>
                            <span style="font-weight:600;">+{{ $item->quantity }} {{ $item->sparePart->unit ?? '' }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="form-group">
                <label class="form-label">Alasan Pembatalan <span class="required">*</span></label>
                <textarea wire:model.live="cancelReason" class="form-textarea" rows="3"
                    placeholder="Jelaskan alasan pembatalan (minimal 10 karakter)..."></textarea>
                <span class="form-hint">{{ strlen($cancelReason) }}/10 karakter minimum</span>
            </div>

            @if($errorMessage)
                <div class="alert alert-danger" style="margin-bottom:1rem;">{{ $errorMessage }}</div>
            @endif

            <div class="flex gap-2">
                <button wire:click="$set('showCancelModal', false)" class="btn btn-secondary" style="flex:1;">Kembali</button>
                <button wire:click="confirmCancel" class="btn btn-danger" style="flex:1;"
                    wire:loading.attr="disabled"
                    {{ strlen($cancelReason) < 10 ? 'disabled' : '' }}>
                    <span wire:loading.remove>❌ Batalkan WO</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </div>
        </div>
    </div>
@endif

{{-- ═══ MODAL PAYMENT ═══════════════════════════════════════════ --}}
@if($showPaymentModal)
    <div style="position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:200;display:flex;align-items:center;justify-content:center;padding:1rem;">
        <div class="card" style="max-width:400px;width:100%;box-shadow:var(--shadow-lg);">
            <div class="card-header"><span class="card-title">💳 Proses Pembayaran</span></div>
            <div style="margin-bottom:1rem;">
                <div style="font-size:var(--text-xl);font-weight:700;text-align:center;color:var(--primary);">
                    Rp {{ number_format($workOrder->grand_total, 0, ',', '.') }}
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Metode Pembayaran</label>
                <div style="display:flex;gap:.75rem;">
                    @foreach(['tunai'=>'💵 Tunai','transfer'=>'🏦 Transfer','kartu'=>'💳 Kartu'] as $val => $lab)
                        <label style="flex:1;display:flex;flex-direction:column;align-items:center;padding:.75rem;border:2px solid {{ $paymentMethod===$val ? 'var(--primary)' : 'var(--border)' }};border-radius:var(--radius);cursor:pointer;background:{{ $paymentMethod===$val ? 'var(--primary-light)' : 'white' }};"
                            wire:click="$set('paymentMethod','{{ $val }}')">
                            <span style="font-size:1.25rem;">{{ explode(' ', $lab)[0] }}</span>
                            <span style="font-size:var(--text-xs);font-weight:500;margin-top:.25rem;">{{ explode(' ', $lab)[1] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="flex gap-2">
                <button wire:click="$set('showPaymentModal', false)" class="btn btn-secondary" style="flex:1;">Batal</button>
                <button wire:click="processPayment" class="btn btn-success" style="flex:1;" wire:loading.attr="disabled">
                    <span wire:loading.remove>✅ Konfirmasi Bayar</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </div>
        </div>
    </div>
@endif

</div>
