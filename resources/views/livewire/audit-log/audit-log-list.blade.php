<div>
<div class="page-header">
    <div>
        <h2 class="page-title">🔍 Audit Log</h2>
        <p class="page-subtitle">Rekam jejak semua aktivitas sistem</p>
    </div>
    <button wire:click="exportCsv" class="btn btn-secondary">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export CSV
    </button>
</div>

{{-- Filter Bar --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div class="flex gap-3" style="flex-wrap:wrap;align-items:flex-end;">

        <div style="flex:1;min-width:180px;">
            <label class="form-label">Cari Pengguna</label>
            <input type="text" wire:model.live.debounce.300ms="search" class="form-input" placeholder="Nama user..." />
        </div>

        <div style="min-width:150px;">
            <label class="form-label">Tipe Aksi</label>
            <select wire:model.live="action" class="form-select">
                <option value="">Semua Aksi</option>
                <option value="CREATE">CREATE</option>
                <option value="UPDATE">UPDATE</option>
                <option value="DELETE">DELETE</option>
                <option value="RESTORE">RESTORE</option>
            </select>
        </div>

        <div style="min-width:160px;">
            <label class="form-label">Tabel</label>
            <select wire:model.live="tableName" class="form-select">
                <option value="">Semua Tabel</option>
                @foreach($this->tableNames as $t)
                    <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
            </select>
        </div>

        <div style="min-width:140px;">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" wire:model.live="dateFrom" class="form-input" />
        </div>

        <div style="min-width:140px;">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" wire:model.live="dateTo" class="form-input" />
        </div>

        @if($search || $action || $tableName || $dateFrom || $dateTo)
        <div>
            <label class="form-label" style="opacity:0">r</label>
            <button wire:click="$set('search','');$set('action','');$set('tableName','');$set('dateFrom','');$set('dateTo','')"
                class="btn btn-secondary">Reset</button>
        </div>
        @endif
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="table-wrap" wire:loading.class="opacity-50">
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Pengguna</th>
                    <th>Aksi</th>
                    <th>Tabel</th>
                    <th>Record ID</th>
                    <th>IP Address</th>
                    <th style="text-align:center;">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->logs as $log)
                    @php
                        $actionCfg = [
                            'CREATE'  => ['badge-success', '✚ CREATE'],
                            'UPDATE'  => ['badge-warning', '✎ UPDATE'],
                            'DELETE'  => ['badge-danger',  '✕ DELETE'],
                            'RESTORE' => ['badge-info',    '↺ RESTORE'],
                        ];
                        $cfg = $actionCfg[$log->action] ?? ['badge-gray', $log->action];
                        $isExpanded = $expandedId === $log->id;
                    @endphp
                    <tr
                        wire:click="toggleExpand({{ $log->id }})"
                        style="cursor:pointer;{{ $isExpanded ? 'background:var(--primary-light);' : '' }}"
                    >
                        <td style="white-space:nowrap;">
                            <div style="font-size:var(--text-sm);">{{ $log->created_at?->format('d/m/Y') }}</div>
                            <div style="font-size:var(--text-xs);color:var(--text-muted);">{{ $log->created_at?->format('H:i:s') }}</div>
                        </td>
                        <td>
                            <div style="font-weight:500;font-size:var(--text-sm);">{{ $log->user_name ?? 'System' }}</div>
                            <div style="font-size:var(--text-xs);color:var(--text-muted);">ID: {{ $log->user_id ?? '-' }}</div>
                        </td>
                        <td><span class="badge {{ $cfg[0] }}">{{ $cfg[1] }}</span></td>
                        <td><code style="font-size:var(--text-xs);background:var(--surface);padding:.15rem .4rem;border-radius:4px;">{{ $log->table_name }}</code></td>
                        <td style="font-family:monospace;font-size:var(--text-sm);">#{{ $log->record_id }}</td>
                        <td style="font-size:var(--text-xs);color:var(--text-muted);">{{ $log->ip_address ?? '-' }}</td>
                        <td style="text-align:center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                style="transition:transform .2s;transform:{{ $isExpanded ? 'rotate(180deg)' : 'none' }};color:var(--text-muted);">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </td>
                    </tr>

                    {{-- Expanded Detail Row --}}
                    @if($isExpanded)
                    <tr>
                        <td colspan="7" style="background:#FAFAFA;padding:1rem 1.25rem;">
                            {{-- User Agent --}}
                            @if($log->user_agent)
                            <div style="font-size:var(--text-xs);color:var(--text-muted);margin-bottom:.75rem;">
                                🌐 <strong>User Agent:</strong> {{ $log->user_agent }}
                            </div>
                            @endif

                            {{-- Diff Table --}}
                            @if($log->old_values || $log->new_values)
                                @php
                                    $allKeys = array_unique(array_merge(
                                        array_keys($log->old_values ?? []),
                                        array_keys($log->new_values ?? [])
                                    ));
                                @endphp
                                <div class="grid grid-2" style="gap:1rem;">
                                    {{-- SEBELUM --}}
                                    <div>
                                        <div style="font-size:var(--text-xs);font-weight:700;color:var(--danger);margin-bottom:.5rem;text-transform:uppercase;letter-spacing:.05em;">📄 Sebelum</div>
                                        @if($log->old_values)
                                            <table style="font-size:var(--text-xs);border:1px solid var(--border);border-radius:var(--radius-sm);width:100%;overflow:hidden;">
                                                <tbody>
                                                    @foreach($allKeys as $key)
                                                        @php
                                                            $oldVal = $log->old_values[$key] ?? null;
                                                            $newVal = $log->new_values[$key] ?? null;
                                                            $changed = $oldVal !== $newVal && array_key_exists($key, $log->new_values ?? []);
                                                        @endphp
                                                        <tr style="{{ $changed ? 'background:#FEF2F2;' : '' }}">
                                                            <td style="padding:.35rem .6rem;font-weight:600;color:var(--text-secondary);border-bottom:1px solid var(--border);width:40%;">{{ $key }}</td>
                                                            <td style="padding:.35rem .6rem;border-bottom:1px solid var(--border);font-family:monospace;word-break:break-all;">
                                                                {{ is_array($oldVal) ? json_encode($oldVal) : ($oldVal ?? '—') }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <div style="color:var(--text-muted);font-style:italic;font-size:var(--text-xs);">Tidak ada data sebelumnya (CREATE)</div>
                                        @endif
                                    </div>

                                    {{-- SESUDAH --}}
                                    <div>
                                        <div style="font-size:var(--text-xs);font-weight:700;color:var(--success);margin-bottom:.5rem;text-transform:uppercase;letter-spacing:.05em;">✅ Sesudah</div>
                                        @if($log->new_values)
                                            <table style="font-size:var(--text-xs);border:1px solid var(--border);border-radius:var(--radius-sm);width:100%;overflow:hidden;">
                                                <tbody>
                                                    @foreach($allKeys as $key)
                                                        @php
                                                            $oldVal = $log->old_values[$key] ?? null;
                                                            $newVal = $log->new_values[$key] ?? null;
                                                            $changed = $oldVal !== $newVal && array_key_exists($key, $log->new_values ?? []);
                                                        @endphp
                                                        <tr style="{{ $changed ? 'background:#F0FDF4;' : '' }}">
                                                            <td style="padding:.35rem .6rem;font-weight:600;color:var(--text-secondary);border-bottom:1px solid var(--border);width:40%;">{{ $key }}</td>
                                                            <td style="padding:.35rem .6rem;border-bottom:1px solid var(--border);font-family:monospace;word-break:break-all;">
                                                                @if($changed)
                                                                    <strong style="color:var(--success);">{{ is_array($newVal) ? json_encode($newVal) : ($newVal ?? '—') }}</strong>
                                                                @else
                                                                    {{ is_array($newVal) ? json_encode($newVal) : ($newVal ?? '—') }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <div style="color:var(--text-muted);font-style:italic;font-size:var(--text-xs);">Tidak ada data sesudahnya (DELETE)</div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div style="color:var(--text-muted);font-style:italic;font-size:var(--text-sm);">Tidak ada data perubahan yang tercatat.</div>
                            @endif
                        </td>
                    </tr>
                    @endif

                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-state-icon">📋</div>
                                <div class="empty-state-title">Belum ada audit log</div>
                                <div class="empty-state-text">Log aktivitas akan muncul saat ada perubahan data.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($this->logs->hasPages())
        <div style="margin-top:1rem;display:flex;justify-content:center;">
            {{ $this->logs->links() }}
        </div>
    @endif
</div>
</div>
