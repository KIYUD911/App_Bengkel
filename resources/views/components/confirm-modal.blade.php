@props([
    'title'      => 'Konfirmasi',
    'message'    => 'Apakah Anda yakin?',
    'confirmText'=> 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'type'       => 'danger',   // danger | warning | primary
    'show'       => false,
    'wireConfirm'=> null,
])

@php
$btnClass = match($type) {
    'warning' => 'btn-warning',
    'primary' => 'btn-primary',
    default   => 'btn-danger',
};
$emoji = match($type) {
    'warning' => '⚠️',
    'primary' => 'ℹ️',
    default   => '🗑️',
};
@endphp

{{--
    Cara pakai:
    <x-confirm-modal
        title="Hapus Data"
        message="Data yang dihapus tidak dapat dikembalikan."
        confirm-text="Ya, Hapus"
        type="danger"
        :show="$showDeleteModal"
        wire:confirm="deleteItem"
        wire:cancel="$set('showDeleteModal', false)"
    />
--}}

@if($show)
<div
    x-data
    style="position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:500;display:flex;align-items:center;justify-content:center;padding:1rem;"
    x-show="true"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
>
    <div class="card"
        style="max-width:440px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:modalPop .25s ease;"
        @click.stop
    >
        <div style="text-align:center;padding:1rem 0 .5rem;">
            <div style="font-size:3rem;margin-bottom:.5rem;">{{ $emoji }}</div>
            <h3 style="font-size:1.125rem;font-weight:700;color:var(--text);margin-bottom:.5rem;">{{ $title }}</h3>
            <p style="font-size:var(--text-sm);color:var(--text-muted);line-height:1.6;">{{ $message }}</p>
        </div>

        {{ $slot }}

        <div class="flex gap-2" style="margin-top:1.25rem;">
            {{ $cancel ?? '' }}
            {{ $confirm ?? '' }}
        </div>
    </div>
</div>
<style>
@keyframes modalPop {
    from { transform: scale(.95); opacity: 0; }
    to   { transform: scale(1);   opacity: 1; }
}
</style>
@endif
