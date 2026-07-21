@props([
    'type'    => 'info',
    'message' => '',
    'dismiss' => true,
    'timeout' => 5000,
])

@php
$styles = [
    'success' => 'alert-success',
    'error'   => 'alert-danger',
    'warning' => 'alert-warning',
    'info'    => 'alert-info',
];
$icons = [
    'success' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
    'error'   => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
    'warning' => '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
    'info'    => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
];
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-init="@if($timeout > 0) setTimeout(() => show = false, {{ $timeout }}) @endif"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="alert {{ $styles[$type] ?? 'alert-info' }}"
    {{ $attributes }}
>
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;">
        {!! $icons[$type] ?? $icons['info'] !!}
    </svg>
    <span style="flex:1;">{{ $message ?: $slot }}</span>
    @if($dismiss)
        <button type="button" @click="show = false"
            style="background:none;border:none;cursor:pointer;color:inherit;opacity:.6;font-size:1.25rem;line-height:1;padding:0;margin-left:.5rem;flex-shrink:0;">
            &times;
        </button>
    @endif
</div>
