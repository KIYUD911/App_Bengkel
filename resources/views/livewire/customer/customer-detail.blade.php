<div>
<div class="page-header">
    <div style="display:flex;align-items:center;gap:.75rem;">
        <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,var(--primary),#7C3AED);color:white;display:flex;align-items:center;justify-content:center;font-size:1.25rem;font-weight:700;">
            {{ strtoupper(substr($customer->name, 0, 2)) }}
        </div>
        <div>
            <h2 class="page-title">
                {{ $customer->name }}
                @if($customer->is_vip)
                    <span class="badge" style="background:#FDF4FF;color:#7E22CE;border:1px solid #E9D5FF;font-size:var(--text-sm);margin-left:.5rem;">⭐ VIP</span>
                @endif
            </h2>
            <p class="page-subtitle">{{ $customer->phone }} @if($customer->email) · {{ $customer->email }} @endif</p>
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-secondary">Edit</a>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">← Kembali</a>
    </div>
</div>

@if($successMessage)
    <div class="alert alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,3000)" x-transition>{{ $successMessage }}</div>
@endif

{{-- Stat Cards --}}
<div class="grid grid-3" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-icon blue"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
        <div class="stat-content"><div class="stat-value">{{ $customer->visit_count }}×</div><div class="stat-label">Total Kunjungan</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
        <div class="stat-content"><div class="stat-value" style="font-size:1.1rem;">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</div><div class="stat-label">Total Belanja</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon {{ $customer->is_vip ? 'purple' : 'amber' }}">
            <span style="font-size:1.5rem;">{{ $customer->is_vip ? '⭐' : '👤' }}</span>
        </div>
        <div class="stat-content"><div class="stat-value">{{ $customer->is_vip ? 'VIP' : 'Regular' }}</div><div class="stat-label">Status Member</div></div>
    </div>
</div>

{{-- Tabs --}}
<div style="display:flex;border-bottom:2px solid var(--border);margin-bottom:1.25rem;gap:.25rem;">
    @foreach(['vehicles'=>'🚗 Kendaraan','history'=>'📋 Riwayat Servis','warranties'=>'🛡️ Garansi Aktif','feedbacks'=>'⭐ Feedback'] as $tab => $label)
        <button wire:click="$set('activeTab','{{ $tab }}')"
            style="padding:.625rem 1rem;border:none;background:none;font-size:var(--text-sm);font-weight:{{ $activeTab===$tab ? '600' : '400' }};color:{{ $activeTab===$tab ? 'var(--primary)' : 'var(--text-muted)' }};border-bottom:2px solid {{ $activeTab===$tab ? 'var(--primary)' : 'transparent' }};margin-bottom:-2px;cursor:pointer;transition:all var(--transition);">
            {{ $label }}
        </button>
    @endforeach
</div>

{{-- Tab: Kendaraan --}}
@if($activeTab === 'vehicles')
    <div class="card">
        <div class="card-header">
            <span class="card-title">Kendaraan Terdaftar</span>
            <button wire:click="$toggle('showVehicleForm')" class="btn btn-sm btn-primary">+ Tambah</button>
        </div>
        @if($showVehicleForm)
            <div style="padding:1rem;background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);margin-bottom:1rem;">
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Plat Nomor <span class="required">*</span></label>
                        <input type="text" wire:model="newLicensePlate" class="form-input" placeholder="B 1234 ABC" />
                        @error('newLicensePlate') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipe</label>
                        <select wire:model="newVehicleType" class="form-select">
                            <option>Motor</option><option>Mobil</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Merek <span class="required">*</span></label>
                        <input type="text" wire:model="newBrand" class="form-input" />
                        @error('newBrand') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Model <span class="required">*</span></label>
                        <input type="text" wire:model="newModel" class="form-input" />
                        @error('newModel') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tahun <span class="required">*</span></label>
                        <input type="number" wire:model="newYear" class="form-input" min="1990" max="{{ date('Y')+1 }}" />
                        @error('newYear') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex gap-2">
                    <button wire:click="saveVehicle" class="btn btn-primary btn-sm">Simpan</button>
                    <button wire:click="$toggle('showVehicleForm')" class="btn btn-secondary btn-sm">Batal</button>
                </div>
            </div>
        @endif
        @forelse($customer->vehicles as $v)
            <div style="display:flex;align-items:center;gap:.75rem;padding:.75rem;background:var(--surface);border-radius:var(--radius-sm);margin-bottom:.5rem;border:1px solid var(--border);">
                <span style="font-size:1.75rem;">🏍️</span>
                <div>
                    <div style="font-weight:600;">{{ $v->brand }} {{ $v->model }} ({{ $v->year }})</div>
                    <div style="font-size:var(--text-xs);color:var(--text-muted);">{{ $v->license_plate }} · {{ $v->vehicle_type }}</div>
                </div>
            </div>
        @empty
            <div class="empty-state" style="padding:2rem;"><div class="empty-state-text">Belum ada kendaraan.</div></div>
        @endforelse
    </div>
@endif

{{-- Tab: Riwayat Servis --}}
@if($activeTab === 'history')
    <div class="card">
        <div class="card-header"><span class="card-title">Riwayat Work Order</span></div>
        @forelse($customer->workOrders as $wo)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:.75rem;border-bottom:1px solid var(--border);">
                <div>
                    <a href="{{ route('work-orders.show', $wo) }}" style="font-weight:600;font-family:monospace;color:var(--primary);">{{ $wo->wo_number }}</a>
                    <div style="font-size:var(--text-xs);color:var(--text-muted);">{{ $wo->created_at->format('d M Y') }} · {{ $wo->vehicle?->brand }} {{ $wo->vehicle?->model }}</div>
                    <div style="font-size:var(--text-sm);font-style:italic;">{{ Str::limit($wo->complaint, 60) }}</div>
                </div>
                <div style="text-align:right;">
                    <span class="badge status-{{ $wo->status }}">{{ $wo->status }}</span>
                    <div style="font-weight:700;margin-top:.25rem;">Rp {{ number_format($wo->grand_total, 0, ',', '.') }}</div>
                </div>
            </div>
        @empty
            <div class="empty-state" style="padding:2rem;"><div class="empty-state-text">Belum ada riwayat servis.</div></div>
        @endforelse
    </div>
@endif

{{-- Tab: Garansi Aktif --}}
@if($activeTab === 'warranties')
    <div class="card">
        <div class="card-header"><span class="card-title">Garansi Sparepart Aktif</span></div>
        @forelse($activeWarranties as $item)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:.75rem;border-bottom:1px solid var(--border);">
                <div>
                    <div style="font-weight:500;">{{ $item->sparePart->name ?? '?' }}</div>
                    <div style="font-size:var(--text-xs);color:var(--text-muted);">WO: {{ $item->workOrder?->wo_number }}</div>
                </div>
                <span class="badge badge-success">Berlaku s/d {{ $item->warranty_end_date }}</span>
            </div>
        @empty
            <div class="empty-state" style="padding:2rem;"><div class="empty-state-text">Tidak ada garansi aktif saat ini.</div></div>
        @endforelse
    </div>
@endif

{{-- Tab: Feedback --}}
@if($activeTab === 'feedbacks')
    <div class="card">
        <div class="card-header"><span class="card-title">Feedback Pelanggan</span></div>
        @forelse($customer->feedbacks as $fb)
            <div style="padding:.75rem;border-bottom:1px solid var(--border);">
                <div style="font-size:1.25rem;">{{ $fb->stars }}</div>
                @if($fb->comment)
                    <div style="font-size:var(--text-sm);color:var(--text-secondary);margin-top:.25rem;">"{{ $fb->comment }}"</div>
                @endif
                <div style="font-size:var(--text-xs);color:var(--text-muted);margin-top:.25rem;">{{ $fb->created_at->format('d M Y') }}</div>
            </div>
        @empty
            <div class="empty-state" style="padding:2rem;"><div class="empty-state-text">Belum ada feedback.</div></div>
        @endforelse
    </div>
@endif

</div>
