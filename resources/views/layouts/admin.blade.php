<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'SurveySays') — SurveySays</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --maroon: #7B1213; --maroon-d: #550D0E; --maroon-l: #9B2224;
      --gold: #C9A84C; --gold-l: #E2C47A;
      --cream: #FAF6EF;
      --sidebar-w: 240px;
      --topbar-h: 60px;
    }
    body { font-family: 'Lato', sans-serif; background: #f4f4f6; color: #222; display: flex; min-height: 100vh; }

    /* Sidebar */
    .sidebar { width: var(--sidebar-w); background: var(--maroon-d); color: #fff; display: flex; flex-direction: column; position: fixed; top: 0; left: 0; bottom: 0; z-index: 100; }
    .sidebar-logo { padding: 20px 18px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid rgba(255,255,255,.1); }
    .sidebar-seal { width: 36px; height: 36px; background: var(--gold); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: var(--maroon-d); flex-shrink: 0; }
    .sidebar-brand { font-family: 'Playfair Display', serif; font-size: 15px; color: #fff; line-height: 1.2; }
    .sidebar-sub { font-size: 10px; color: rgba(255,255,255,.5); text-transform: uppercase; letter-spacing: .08em; }
    .sidebar-nav { flex: 1; padding: 16px 0; overflow-y: auto; }
    .nav-label { font-size: 9px; text-transform: uppercase; letter-spacing: .12em; color: rgba(255,255,255,.4); padding: 14px 18px 6px; }
    .nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 18px; color: rgba(255,255,255,.75); text-decoration: none; font-size: 13px; transition: background .15s, color .15s; }
    .nav-item:hover { background: rgba(255,255,255,.08); color: #fff; }
    .nav-item.active { background: rgba(201,168,76,.15); color: var(--gold-l); border-left: 3px solid var(--gold); }
    .nav-item svg { width: 15px; height: 15px; stroke: currentColor; flex-shrink: 0; }
    .sidebar-footer { padding: 16px 18px; border-top: 1px solid rgba(255,255,255,.1); }
    .user-info .user-name { font-size: 13px; font-weight: 700; color: #fff; }
    .user-info .user-role { font-size: 10px; color: rgba(255,255,255,.5); text-transform: uppercase; letter-spacing: .06em; }
    .btn-logout { display: block; margin-top: 10px; text-align: center; padding: 7px; background: rgba(255,255,255,.08); color: rgba(255,255,255,.7); border-radius: 4px; text-decoration: none; font-size: 12px; transition: background .15s; }
    .btn-logout:hover { background: rgba(255,255,255,.15); color: #fff; }

    /* Main */
    .main-wrap { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
    .topbar { height: var(--topbar-h); background: #fff; border-bottom: 1px solid #e8e8e8; display: flex; align-items: center; justify-content: space-between; padding: 0 28px; position: sticky; top: 0; z-index: 50; }
    .topbar-title { font-family: 'Playfair Display', serif; font-size: 18px; color: var(--maroon-d); }
    .topbar-actions { display: flex; align-items: center; gap: 12px; }
    .main-content { padding: 28px; flex: 1; }

    /* Flash */
    .flash { padding: 12px 18px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; }
    .flash-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .flash-error   { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .flash-info    { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }

    /* Cards */
    .card { background: #fff; border-radius: 8px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
    .card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
    .card-title { font-family: 'Playfair Display', serif; font-size: 17px; color: var(--maroon-d); }

    /* Stats */
    .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,.07); }
    .stat-label { font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: #888; margin-bottom: 6px; }
    .stat-value { font-size: 28px; font-weight: 700; color: var(--maroon-d); line-height: 1; }

    /* Buttons */
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 5px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; border: none; transition: all .15s; }
    .btn-primary { background: var(--maroon); color: #fff; }
    .btn-primary:hover { background: var(--maroon-d); }
    .btn-secondary { background: #f0f0f0; color: #444; }
    .btn-secondary:hover { background: #e0e0e0; }
    .btn-gold { background: var(--gold); color: #fff; }
    .btn-gold:hover { background: #b8973b; }
    .btn-danger { background: #dc3545; color: #fff; }
    .btn-danger:hover { background: #c82333; }
    .btn-sm { padding: 5px 10px; font-size: 12px; }

    /* Table */
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th { text-align: left; padding: 10px 14px; background: #f8f8f8; color: #555; font-size: 11px; text-transform: uppercase; letter-spacing: .06em; border-bottom: 2px solid #eee; }
    td { padding: 12px 14px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #fafafa; }

    /* Badges */
    .badge { display: inline-block; padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
    .badge-draft    { background: #e9ecef; color: #555; }
    .badge-active   { background: #d4edda; color: #155724; }
    .badge-closed   { background: #f8d7da; color: #721c24; }
    .badge-complete { background: #d4edda; color: #155724; }
    .badge-partial  { background: #fff3cd; color: #856404; }
    .badge-admin    { background: #f0e6ff; color: #5a2d91; }
    .badge-researcher { background: #e6f3ff; color: #1a5f9e; }

    /* Forms */
    .form-group { margin-bottom: 18px; }
    label { display: block; font-size: 12px; font-weight: 700; color: #555; margin-bottom: 5px; text-transform: uppercase; letter-spacing: .04em; }
    input[type=text], input[type=email], input[type=password], input[type=number], input[type=date], input[type=datetime-local], select, textarea {
      width: 100%; padding: 9px 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; font-family: inherit; outline: none; transition: border-color .15s;
    }
    input:focus, select:focus, textarea:focus { border-color: var(--maroon); }
    textarea { resize: vertical; min-height: 80px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-check { display: flex; align-items: center; gap: 8px; }
    .form-check input { width: auto; }

    /* Pagination */
    .pagination { display: flex; gap: 4px; margin-top: 20px; }
    .pagination a, .pagination span { padding: 6px 12px; border-radius: 4px; font-size: 13px; text-decoration: none; border: 1px solid #ddd; color: #444; }
    .pagination a:hover { background: var(--maroon); color: #fff; border-color: var(--maroon); }
    .pagination .active span { background: var(--maroon); color: #fff; border-color: var(--maroon); }
  </style>
  @stack('styles')
</head>
<body>
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="sidebar-seal">S</div>
    <div>
      <div class="sidebar-brand">SurveySays</div>
      <div class="sidebar-sub">Data Collection</div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-label">Overview</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <svg viewBox="0 0 16 16" fill="none" stroke-width="1.5"><rect x="1" y="1" width="6" height="6" rx="1"/><rect x="9" y="1" width="6" height="6" rx="1"/><rect x="1" y="9" width="6" height="6" rx="1"/><rect x="9" y="9" width="6" height="6" rx="1"/></svg>
      Dashboard
    </a>
    <div class="nav-label">Surveys</div>
    <a href="{{ route('admin.surveys.index') }}" class="nav-item {{ request()->routeIs('admin.surveys.*') ? 'active' : '' }}">
      <svg viewBox="0 0 16 16" fill="none" stroke-width="1.5"><rect x="1" y="2" width="14" height="12" rx="1"/><line x1="4" y1="6" x2="12" y2="6"/><line x1="4" y1="9" x2="9" y2="9"/></svg>
      My Surveys
    </a>
    @if(auth()->user()->isAdmin())
    <div class="nav-label">Admin</div>
    <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
      <svg viewBox="0 0 16 16" fill="none" stroke-width="1.5"><circle cx="6" cy="5" r="3"/><path d="M1 14c0-2.8 2.2-5 5-5h1"/><line x1="11" y1="10" x2="11" y2="14"/><line x1="9" y1="12" x2="13" y2="12"/></svg>
      Users
    </a>
    @endif
    <div class="nav-label">Help</div>
    <a href="{{ route('admin.docs') }}" class="nav-item {{ request()->routeIs('admin.docs') ? 'active' : '' }}">
      <svg viewBox="0 0 16 16" fill="none" stroke-width="1.5"><rect x="2" y="1" width="12" height="14" rx="1"/><line x1="5" y1="5" x2="11" y2="5"/><line x1="5" y1="8" x2="11" y2="8"/><line x1="5" y1="11" x2="8" y2="11"/></svg>
      Documentation
    </a>
  </nav>
  <div class="sidebar-footer">
    <div class="user-info">
      <div class="user-name">{{ auth()->user()->name }}</div>
      <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
    </div>
    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button type="submit" class="btn-logout">Log Out</button>
    </form>
  </div>
</aside>

<div class="main-wrap">
  <header class="topbar">
    <h1 class="topbar-title">@yield('title', 'Dashboard')</h1>
    <div class="topbar-actions">@yield('topbar-actions')</div>
  </header>
  <div class="main-content">
    @if(session('success'))
      <div class="flash flash-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="flash flash-error">{{ session('error') }}</div>
    @endif
    @yield('content')
  </div>
</div>
</body>
</html>
