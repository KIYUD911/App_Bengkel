<div class="login-card" wire:key="login-form">

    {{-- Logo & Header --}}
    <div class="login-header">
        <div class="login-logo">
            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="48" height="48" rx="12" fill="#2563EB"/>
                <path d="M12 28L20 36L36 12" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="24" cy="20" r="6" stroke="white" stroke-width="2.5" fill="none"/>
            </svg>
        </div>
        <h1 class="login-title">CV Masman Sejahtera</h1>
        <p class="login-subtitle">Sistem Informasi Manajemen Bengkel</p>
    </div>

    {{-- Error Alert --}}
    @if($errorMessage)
        <div class="alert alert-danger" wire:key="error-{{ $errorMessage }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ $errorMessage }}
        </div>
    @endif

    {{-- Form --}}
    <form wire:submit="login" class="login-form" id="login-form" novalidate>

        {{-- Email Field --}}
        <div class="form-group" id="email-group">
            <label for="email" class="form-label">Alamat Email</label>
            <div class="input-wrapper">
                <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                <input
                    id="email"
                    type="email"
                    wire:model.live="email"
                    class="form-input {{ $errors->has('email') ? 'input-error' : '' }}"
                    placeholder="contoh@email.com"
                    autocomplete="email"
                    autofocus
                />
            </div>
            @error('email')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        {{-- Password Field --}}
        <div class="form-group" id="password-group">
            <label for="password" class="form-label">Password</label>
            <div class="input-wrapper" x-data="{ show: false }">
                <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input
                    id="password"
                    :type="show ? 'text' : 'password'"
                    wire:model="password"
                    class="form-input {{ $errors->has('password') ? 'input-error' : '' }}"
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                />
                <button type="button" class="toggle-password" @click="show = !show" tabindex="-1">
                    <svg x-show="!show" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg x-show="show" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                </button>
            </div>
            @error('password')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="form-check" id="remember-group">
            <label class="check-label">
                <input type="checkbox" wire:model="remember" class="check-input" id="remember"/>
                <span class="check-custom"></span>
                Ingat saya selama 30 hari
            </label>
        </div>

        {{-- Submit Button --}}
        <button
            type="submit"
            class="btn-login"
            id="btn-login"
            wire:loading.attr="disabled"
            wire:loading.class="btn-loading"
        >
            <span wire:loading.remove>Masuk ke Sistem</span>
            <span wire:loading class="loading-text">
                <svg class="spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                Memverifikasi...
            </span>
        </button>

    </form>

    {{-- Footer --}}
    <div class="login-footer">
        <p>© {{ date('Y') }} CV Masman Sejahtera · Sistem Informasi Bengkel</p>
    </div>

</div>
