<div>
<div class="page-header">
    <div>
        <h2 class="page-title">⭐ Feedback Pelanggan</h2>
        <p class="page-subtitle">WO: <strong>{{ $workOrder->wo_number }}</strong> · {{ $workOrder->customer?->name }}</p>
    </div>
    <a href="{{ route('work-orders.show', $workOrder) }}" class="btn btn-secondary">← Kembali ke WO</a>
</div>

<div style="max-width:520px;">

    {{-- Sudah ada feedback --}}
    @if($workOrder->feedback || $submitted)
        <div class="card" style="text-align:center;padding:2rem;">
            <div style="font-size:3rem;margin-bottom:.75rem;">✅</div>
            <h3 style="font-weight:700;margin-bottom:.5rem;">Feedback Sudah Diberikan</h3>
            <div style="font-size:2rem;color:var(--warning);margin:.75rem 0;">
                {{ $workOrder->feedback?->stars ?? str_repeat('★', $rating) }}
            </div>
            @if($workOrder->feedback?->comment)
                <p style="font-style:italic;color:var(--text-secondary);">"{{ $workOrder->feedback->comment }}"</p>
            @endif
            <a href="{{ route('work-orders.show', $workOrder) }}" class="btn btn-primary" style="margin-top:1.25rem;">Kembali ke WO</a>
        </div>

    {{-- WO belum completed --}}
    @elseif($workOrder->status !== 'completed')
        <div class="alert alert-warning">WO harus berstatus <strong>Selesai (completed)</strong> untuk memberikan feedback.</div>

    {{-- Form feedback --}}
    @else
        <div class="card"
            x-data="{
                hover: 0,
                selected: @entangle('rating').live,
                setHover(v) { this.hover = v; },
                clearHover() { this.hover = 0; },
                isActive(v) { return (this.hover > 0 ? this.hover : this.selected) >= v; }
            }"
        >
            <div class="card-header"><span class="card-title">Berikan Penilaian Anda</span></div>

            <div class="form-group">
                <label class="form-label">Rating <span class="required">*</span></label>
                <div style="display:flex;gap:.25rem;margin-top:.25rem;">
                    @for($i = 1; $i <= 5; $i++)
                        <button
                            type="button"
                            @mouseenter="setHover({{ $i }})"
                            @mouseleave="clearHover()"
                            @click="selected = {{ $i }}; $wire.setRating({{ $i }})"
                            style="background:none;border:none;cursor:pointer;padding:.25rem;font-size:2.5rem;line-height:1;transition:transform .15s;"
                            :style="isActive({{ $i }}) ? 'color:#F59E0B;transform:scale(1.15)' : 'color:#D1D5DB;'"
                        >★</button>
                    @endfor
                </div>
                @if($rating > 0)
                    <div style="font-size:var(--text-sm);color:var(--text-muted);margin-top:.25rem;">
                        {{ ['','Sangat Buruk','Buruk','Cukup','Bagus','Sangat Bagus'][$rating] }}
                    </div>
                @endif
                @error('rating') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Komentar (opsional)</label>
                <textarea wire:model="comment" class="form-textarea" rows="4"
                    placeholder="Ceritakan pengalaman servis Anda..."></textarea>
                @error('comment') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <button wire:click="submit" class="btn btn-primary" wire:loading.attr="disabled"
                :class="selected === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                :disabled="selected === 0">
                <span wire:loading.remove>⭐ Kirim Feedback</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    @endif

</div>
</div>
