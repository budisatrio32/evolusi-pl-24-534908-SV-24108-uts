<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Evolusi PL')</title>
    <style>
        :root { --bg: #f4f5f7; --card: #ffffff; --ink: #1f2933; --muted: #616e7c; --accent: #2563eb; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--ink);
               font-family: system-ui, -apple-system, "Segoe UI", sans-serif; line-height: 1.6; }
        header { background: var(--card); border-bottom: 1px solid #e4e7eb; }
        nav { max-width: 720px; margin: 0 auto; padding: 16px 24px; display: flex; gap: 20px; align-items: center; }
        nav .brand { font-weight: 700; margin-right: auto; }
        nav a { color: var(--muted); text-decoration: none; }
        nav a:hover { color: var(--accent); }
        main { max-width: 720px; margin: 0 auto; padding: 32px 24px; }
        .card { background: var(--card); border: 1px solid #e4e7eb; border-radius: 10px; padding: 24px; }
        h1 { margin-top: 0; font-size: 1.6rem; }
        footer { max-width: 720px; margin: 0 auto; padding: 16px 24px 40px; color: var(--muted); font-size: .85rem; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 16px; }
        .toolbar h1 { margin: 0; }
        .btn { background: var(--accent); color: #fff; border: 0; border-radius: 6px; padding: 8px 14px;
               text-decoration: none; cursor: pointer; font: inherit; }
        .muted { color: var(--muted); }
        .alert { background: #ecfdf5; color: #065f46; border-radius: 6px; padding: 10px 14px; }
        .errors { background: #fef2f2; color: #991b1b; border-radius: 6px; padding: 10px 14px 10px 32px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: .9rem; }
        th, td { text-align: left; padding: 8px 6px; border-bottom: 1px solid #e4e7eb; }
        .aksi { display: flex; gap: 10px; }
        .aksi a { color: var(--accent); }
        .link-danger { background: none; border: 0; padding: 0; color: #dc2626; cursor: pointer; font: inherit; }
        .badge { border-radius: 999px; padding: 2px 10px; font-size: .8rem; }
        .badge.dipinjam { background: #fef3c7; color: #92400e; }
        .badge.dikembalikan { background: #d1fae5; color: #065f46; }
        .form label { display: block; margin-bottom: 14px; font-weight: 600; }
        .form input, .form select { display: block; width: 100%; margin-top: 4px; padding: 8px; font: inherit;
                                    border: 1px solid #cbd2d9; border-radius: 6px; }
    </style>
</head>
<body>
    <header>
        <nav>
            <span class="brand">Evolusi PL</span>
            <a href="{{ route('home') }}">Beranda</a>
            <a href="{{ route('peminjaman.index') }}">Peminjaman</a>
            <a href="{{ route('about') }}">Tentang</a>
        </nav>
    </header>
    <main>@yield('content')</main>
    <footer>Konstruksi dan Evolusi Perangkat Lunak &middot; {{ date('Y') }}</footer>
</body>
</html>
