<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan · CV Masman Sejahtera</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #0F172A; color: #E2E8F0; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { text-align: center; padding: 2rem; max-width: 480px; }
        .code { font-size: 7rem; font-weight: 800; background: linear-gradient(135deg, #2563EB, #7C3AED); -webkit-background-clip: text; -webkit-text-fill-color: transparent; line-height: 1; }
        .emoji { font-size: 4rem; margin: .5rem 0 1rem; }
        h1 { font-size: 1.5rem; font-weight: 700; color: #F1F5F9; margin-bottom: .75rem; }
        p { color: #94A3B8; line-height: 1.6; margin-bottom: 1.5rem; }
        .btn-group { display: flex; gap: .75rem; justify-content: center; flex-wrap: wrap; }
        .btn { display: inline-flex; align-items: center; gap: .4rem; padding: .75rem 1.5rem; border-radius: 8px; font-family: inherit; font-size: .9rem; font-weight: 600; cursor: pointer; text-decoration: none; }
        .btn-primary { background: #2563EB; color: white; border: none; }
        .btn-primary:hover { background: #1D4ED8; }
        .btn-outline { background: transparent; color: #94A3B8; border: 1px solid rgba(255,255,255,.15); }
        .btn-outline:hover { color: white; border-color: rgba(255,255,255,.3); }
        .divider { height: 1px; background: rgba(255,255,255,.08); margin: 1.5rem 0; }
    </style>
</head>
<body>
<div class="container">
    <div class="code">404</div>
    <div class="emoji">🔍</div>
    <h1>Halaman Tidak Ditemukan</h1>
    <p>Halaman yang Anda cari tidak ada atau telah dipindahkan. Periksa kembali URL yang Anda masukkan.</p>
    <div class="divider"></div>
    <div class="btn-group">
        <a href="{{ url('/dashboard') }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Ke Dashboard
        </a>
        <a href="javascript:history.back()" class="btn btn-outline">← Kembali</a>
    </div>
</div>
</body>
</html>
