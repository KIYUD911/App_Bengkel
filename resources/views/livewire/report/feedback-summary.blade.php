<div>
<div class="page-header">
    <div>
        <h2 class="page-title">⭐ Ringkasan Feedback Pelanggan</h2>
        <p class="page-subtitle">Rekapitulasi penilaian kualitas layanan bengkel</p>
    </div>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div class="flex gap-3" style="align-items:flex-end;">
        <div style="min-width:145px;">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" wire:model.live="dateFrom" class="form-input" />
        </div>
        <div style="min-width:145px;">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" wire:model.live="dateTo" class="form-input" />
        </div>
        @if($dateFrom || $dateTo)
        <div>
            <label class="form-label" style="opacity:0">r</label>
            <button wire:click="$set('dateFrom','');$set('dateTo','')" class="btn btn-secondary">Reset</button>
        </div>
        @endif
    </div>
</div>

@php $stats = $this->stats; @endphp

<div class="grid grid-2" style="margin-bottom:1.5rem;align-items:start;">

    {{-- Kartu Rata-rata --}}
    <div class="card" style="text-align:center;padding:2rem;">
        <div style="font-size:var(--text-xs);font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-bottom:1rem;">Rata-rata Rating</div>
        <div style="font-size:4rem;font-weight:800;color:var(--warning);line-height:1;">{{ number_format($stats['average'], 1) }}</div>
        <div style="margin:.5rem 0 .75rem;">
            @for($i = 1; $i <= 5; $i++)
                <span style="font-size:1.75rem;color:{{ $i <= round($stats['average']) ? '#F59E0B' : '#D1D5DB' }};">★</span>
            @endfor
        </div>
        <div style="font-size:var(--text-sm);color:var(--text-muted);">dari {{ $stats['total'] }} ulasan</div>
    </div>

    {{-- Distribusi Rating (Bar Chart SVG) --}}
    <div class="card">
        <div class="card-header"><span class="card-title">Distribusi Rating</span></div>
        <div style="display:flex;flex-direction:column;gap:.625rem;">
            @foreach(array_reverse([1,2,3,4,5]) as $star)
                @php
                    $d   = $stats['distribution'][$star] ?? ['count'=>0,'pct'=>0];
                    $col = match(true) {
                        $star >= 4 => '#16A34A',
                        $star == 3 => '#D97706',
                        default    => '#DC2626',
                    };
                @endphp
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <span style="font-size:var(--text-sm);color:var(--warning);width:60px;flex-shrink:0;">
                        {{ str_repeat('★', $star) }}{{ str_repeat('☆', 5-$star) }}
                    </span>
                    {{-- SVG progress bar --}}
                    <svg width="100%" height="16" style="flex:1;">
                        <rect x="0" y="2" width="100%" height="12" rx="6" fill="#F1F5F9"/>
                        <rect x="0" y="2" width="{{ $d['pct'] }}%" height="12" rx="6" fill="{{ $col }}" style="transition:width .5s;"/>
                    </svg>
                    <span style="font-size:var(--text-xs);color:var(--text-muted);width:50px;flex-shrink:0;text-align:right;">
                        {{ $d['count'] }} ({{ $d['pct'] }}%)
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- 10 Komentar Terbaru --}}
<div class="card">
    <div class="card-header"><span class="card-title">💬 10 Komentar Terbaru</span></div>
    @forelse($stats['recent'] as $fb)
        <div style="padding:.875rem;border-bottom:1px solid var(--border);">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;">
                <div>
                    <div style="font-size:1.25rem;color:var(--warning);margin-bottom:.25rem;">
                        {{ str_repeat('★', $fb->rating) }}{{ str_repeat('☆', 5-$fb->rating) }}
                    </div>
                    @if($fb->comment)
                        <p style="font-style:italic;color:var(--text-secondary);font-size:var(--text-sm);">"{{ $fb->comment }}"</p>
                    @else
                        <p style="color:var(--text-muted);font-size:var(--text-sm);">— Tidak ada komentar —</p>
                    @endif
                    <div style="font-size:var(--text-xs);color:var(--text-muted);margin-top:.25rem;">
                        👤 {{ $fb->customer?->name ?? 'Anonim' }}
                        @if($fb->workOrder)
                            · WO <a href="{{ route('work-orders.show', $fb->work_order_id) }}" style="font-family:monospace;color:var(--primary);">{{ $fb->workOrder->wo_number }}</a>
                        @endif
                    </div>
                </div>
                <div style="font-size:var(--text-xs);color:var(--text-muted);white-space:nowrap;flex-shrink:0;">
                    {{ $fb->created_at?->format('d/m/Y') }}
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state" style="padding:2rem;">
            <div class="empty-state-icon">⭐</div>
            <div class="empty-state-title">Belum ada feedback</div>
        </div>
    @endforelse
</div>
</div>
