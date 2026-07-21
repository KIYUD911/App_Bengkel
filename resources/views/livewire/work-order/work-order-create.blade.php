<div>
<div class="page-header">
    <div>
        <h2 class="page-title">Buat Work Order Baru</h2>
        <p class="page-subtitle">Isi data servis kendaraan pelanggan</p>
    </div>
    <a href="{{ route('work-orders.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

{{-- Step Indicator --}}
<div style="display:flex; align-items:center; gap:.5rem; margin-bottom:1.5rem;">
    @foreach(['Pilih Pelanggan','Pilih Kendaraan','Keluhan & Biaya'] as $i => $label)
        @php $stepNum = $i + 1; @endphp
        <div style="display:flex;align-items:center;gap:.5rem;">
            <div style="width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:var(--text-sm);
                {{ $step >= $stepNum ? 'background:var(--primary);color:white;' : 'background:var(--surface);color:var(--text-muted);border:2px solid var(--border);' }}">
                {{ $stepNum }}
            </div>
            <span style="font-size:var(--text-sm);font-weight:{{ $step === $stepNum ? '600' : '400' }};color:{{ $step === $stepNum ? 'var(--text)' : 'var(--text-muted)' }};">
                {{ $label }}
            </span>
        </div>
        @if($i < 2)
            <div style="flex:1;height:2px;background:{{ $step > $stepNum ? 'var(--primary)' : 'var(--border)' }};border-radius:2px;min-width:30px;"></div>
        @endif
    @endforeach
</div>

{{-- Error / Info Alert --}}
@if($errorMessage)
    <div class="alert alert-danger">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ $errorMessage }}
    </div>
@endif

<div class="card" style="max-width: 720px;">

    {{-- ══ STEP 1 — PILIH PELANGGAN ══════════════════════════════ --}}
    @if($step === 1)
        <div>
            <div class="card-header">
                <span class="card-title">Step 1: Pilih Pelanggan</span>
            </div>

            {{-- Search box --}}
            <div class="form-group" style="position:relative;">
                <label class="form-label">Cari Pelanggan <span class="required">*</span></label>
                <input type="text" wire:model.live.debounce.300ms="customerSearch"
                    class="form-input" placeholder="Ketik nama atau nomor telepon..."
                    autocomplete="off" />

                {{-- Dropdown hasil --}}
                @if(count($customerResults) > 0)
                    <div style="position:absolute;top:100%;left:0;right:0;background:white;border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-md);z-index:50;margin-top:.25rem;">
                        @foreach($customerResults as $c)
                            <div wire:click="selectCustomer({{ $c['id'] }})"
                                style="padding:.75rem 1rem;cursor:pointer;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);"
                                onmouseover="this.style.background='var(--surface)'" onmouseout="this.style.background='white'">
                                <div>
                                    <div style="font-weight:500;">{{ $c['name'] }}</div>
                                    <div style="font-size:var(--text-xs);color:var(--text-muted);">{{ $c['phone'] }}</div>
                                </div>
                                @if($c['is_vip'])
                                    <span class="badge" style="background:#FDF4FF;color:#7E22CE;border:1px solid #E9D5FF;">⭐ VIP</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Pelanggan terpilih --}}
            @if($selectedCustomer)
                <div class="alert alert-success" style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <strong>{{ $selectedCustomer->name }}</strong>
                        <span style="margin-left:.5rem;color:var(--text-muted);">{{ $selectedCustomer->phone }}</span>
                        @if($selectedCustomer->is_vip)
                            <span class="badge" style="background:#FDF4FF;color:#7E22CE;border:1px solid #E9D5FF;margin-left:.5rem;">⭐ VIP</span>
                        @endif
                    </div>
                    <button wire:click="$set('selectedCustomer', null); $set('selectedCustomerId', null); $set('customerSearch', '')"
                        class="btn btn-sm btn-secondary">Ganti</button>
                </div>
            @endif

            {{-- Tombol Pelanggan Baru --}}
            @if(!$selectedCustomer)
                <button type="button" wire:click="$toggle('showNewCustomer')" class="btn btn-secondary" style="margin-top:.5rem;">
                    + Pelanggan Baru
                </button>
            @endif

            {{-- Form Pelanggan Baru --}}
            @if($showNewCustomer)
                <div style="margin-top:1rem;padding:1rem;background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);">
                    <div style="font-weight:600;margin-bottom:.75rem;">Data Pelanggan Baru</div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label">Nama <span class="required">*</span></label>
                            <input type="text" wire:model="newCustomerName" class="form-input" placeholder="Nama lengkap" />
                            @error('newCustomerName') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Telepon <span class="required">*</span></label>
                            <input type="text" wire:model="newCustomerPhone" class="form-input" placeholder="08xx..." />
                            @error('newCustomerPhone') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="saveNewCustomer" class="btn btn-primary btn-sm">Simpan</button>
                        <button wire:click="$toggle('showNewCustomer')" class="btn btn-secondary btn-sm">Batal</button>
                    </div>
                </div>
            @endif

            <div style="margin-top:1.5rem;">
                <button wire:click="nextStep" class="btn btn-primary" wire:loading.attr="disabled">
                    Lanjut → Pilih Kendaraan
                </button>
            </div>
        </div>
    @endif

    {{-- ══ STEP 2 — PILIH KENDARAAN ══════════════════════════════ --}}
    @if($step === 2)
        <div>
            <div class="card-header">
                <span class="card-title">Step 2: Pilih Kendaraan</span>
                <span style="font-size:var(--text-sm);color:var(--text-muted);">Pelanggan: <strong>{{ $selectedCustomer->name }}</strong></span>
            </div>

            @if(count($vehicles) > 0)
                <div style="display:flex;flex-direction:column;gap:.5rem;margin-bottom:1rem;">
                    @foreach($vehicles as $v)
                        <label style="display:flex;align-items:center;gap:.75rem;padding:.875rem;border:2px solid {{ $selectedVehicleId == $v['id'] ? 'var(--primary)' : 'var(--border)' }};border-radius:var(--radius);cursor:pointer;background:{{ $selectedVehicleId == $v['id'] ? 'var(--primary-light)' : 'white' }};"
                            wire:click="$set('selectedVehicleId', {{ $v['id'] }})">
                            <div style="width:20px;height:20px;border-radius:50%;border:2px solid {{ $selectedVehicleId == $v['id'] ? 'var(--primary)' : 'var(--border)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                @if($selectedVehicleId == $v['id'])
                                    <div style="width:10px;height:10px;border-radius:50%;background:var(--primary);"></div>
                                @endif
                            </div>
                            <div>
                                <div style="font-weight:600;">{{ $v['brand'] }} {{ $v['model'] }} ({{ $v['year'] }})</div>
                                <div style="font-size:var(--text-xs);color:var(--text-muted);">{{ $v['license_plate'] }} · {{ $v['vehicle_type'] }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            @else
                <div class="alert alert-warning">Pelanggan ini belum memiliki kendaraan terdaftar.</div>
            @endif

            {{-- Tombol Tambah Kendaraan Baru --}}
            <button type="button" wire:click="$toggle('showNewVehicle')" class="btn btn-secondary btn-sm">+ Tambah Kendaraan Baru</button>

            {{-- Form Kendaraan Baru --}}
            @if($showNewVehicle)
                <div style="margin-top:.75rem;padding:1rem;background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);">
                    <div style="font-weight:600;margin-bottom:.75rem;">Data Kendaraan Baru</div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label">Plat Nomor <span class="required">*</span></label>
                            <input type="text" wire:model="newLicensePlate" class="form-input" placeholder="B 1234 ABC" />
                            @error('newLicensePlate') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tipe Kendaraan</label>
                            <select wire:model="newVehicleType" class="form-select">
                                <option>Motor</option>
                                <option>Mobil</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Merek <span class="required">*</span></label>
                            <input type="text" wire:model="newBrand" class="form-input" placeholder="Honda, Yamaha..." />
                            @error('newBrand') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Model <span class="required">*</span></label>
                            <input type="text" wire:model="newModel" class="form-input" placeholder="Vario, NMAX..." />
                            @error('newModel') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tahun <span class="required">*</span></label>
                            <input type="number" wire:model="newYear" class="form-input" min="1990" max="{{ date('Y')+1 }}" />
                            @error('newYear') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="saveNewVehicle" class="btn btn-primary btn-sm">Simpan Kendaraan</button>
                        <button wire:click="$toggle('showNewVehicle')" class="btn btn-secondary btn-sm">Batal</button>
                    </div>
                </div>
            @endif

            <div class="flex gap-2" style="margin-top:1.5rem;">
                <button wire:click="prevStep" class="btn btn-secondary">← Kembali</button>
                <button wire:click="nextStep" class="btn btn-primary" wire:loading.attr="disabled">Lanjut → Isi Keluhan</button>
            </div>
        </div>
    @endif

    {{-- ══ STEP 3 — KELUHAN & BIAYA ═══════════════════════════════ --}}
    @if($step === 3)
        <div>
            <div class="card-header">
                <span class="card-title">Step 3: Keluhan & Biaya Servis</span>
            </div>

            <div class="form-group">
                <label class="form-label">Keluhan Pelanggan <span class="required">*</span></label>
                <textarea wire:model.live="complaint" class="form-textarea" rows="3"
                    placeholder="Jelaskan keluhan kendaraan secara detail (minimal 10 karakter)..."></textarea>
                @error('complaint') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Mekanik</label>
                <textarea wire:model="mechanicNotes" class="form-textarea" rows="2"
                    placeholder="Catatan tambahan dari mekanik (opsional)..."></textarea>
            </div>

            <div class="form-group" style="max-width:250px;">
                <label class="form-label">Biaya Jasa (Labour Cost) <span class="required">*</span></label>
                <div style="position:relative;">
                    <span style="position:absolute;left:.875rem;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:var(--text-sm);">Rp</span>
                    <input type="number" wire:model="labourCost" class="form-input" style="padding-left:2.5rem;" min="0" placeholder="0" />
                </div>
                @error('labourCost') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="flex gap-2" style="margin-top:1.5rem;">
                <button wire:click="prevStep" class="btn btn-secondary">← Kembali</button>
                <button wire:click="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>✅ Buat Work Order</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>
        </div>
    @endif

</div>
</div>
