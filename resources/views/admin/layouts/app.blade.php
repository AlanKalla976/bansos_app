<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — SPK Bantuan Sosial</title>
    <link rel="icon" href="{{ asset('images/logo-pemkot.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
    /* ── TOKENS ── */
    :root {
        --primary:        #1E3A5F;
        --primary-dark:   #152C49;
        --primary-light:  #2A4F7A;
        --secondary:      #2D6A4F;
        --secondary-dark: #1B4332;
        --accent:         #52B788;
        --gold:           #F9C74F;
        --danger:         #DC2626;
        --warning:        #D97706;
        --info:           #0284C7;
        --success:        #059669;
        --white:          #FFFFFF;
        --bg:             #F0F4F8;
        --bg-card:        #FFFFFF;
        --border:         #E2E8F0;
        --text:           #1E293B;
        --text-muted:     #64748B;
        --text-light:     #94A3B8;
        --sidebar-w:      270px;
        --topbar-h:       64px;
        --radius-sm:      8px;
        --radius-md:      12px;
        --radius-lg:      16px;
        --shadow-sm:      0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
        --shadow-md:      0 4px 12px rgba(0,0,0,.08), 0 2px 4px rgba(0,0,0,.04);
        --shadow-lg:      0 8px 24px rgba(0,0,0,.10);
        --shadow-hover:   0 12px 32px rgba(30,58,95,.15);
        --transition:     all .2s cubic-bezier(.4,0,.2,1);
    }

    *, *::before, *::after { box-sizing: border-box; }
    body {
        background: var(--bg);
        font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
        font-size: .9rem;
        color: var(--text);
        margin: 0;
        -webkit-font-smoothing: antialiased;
    }

    /* ── SIDEBAR ── */
    .sidebar {
        position: fixed; top: 0; left: 0;
        width: var(--sidebar-w); height: 100vh;
        background: linear-gradient(175deg, var(--primary) 0%, #0F2540 55%, var(--secondary-dark) 100%);
        display: flex; flex-direction: column;
        z-index: 1050;
        transition: transform .3s cubic-bezier(.4,0,.2,1);
        overflow: hidden;
        box-shadow: 4px 0 20px rgba(0,0,0,.18);
    }
    .sidebar-brand {
        display: flex; align-items: center; gap: .75rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(255,255,255,.08);
        flex-shrink: 0; text-decoration: none;
    }
    .sidebar-logo {
        width: 46px; height: 46px;
        border-radius: 12px;
        background: rgba(255,255,255,.95);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; overflow: hidden; padding: 3px;
    }
    .sidebar-logo img { width: 40px; height: 40px; object-fit: contain; }
    .sidebar-logo .logo-fallback {
        display: none; width: 40px; height: 40px;
        align-items: center; justify-content: center;
        color: var(--gold); font-size: 1.4rem;
    }
    .brand-text { overflow: hidden; }
    .brand-name { font-size: .78rem; font-weight: 800; color: #fff; display: block; line-height: 1.3; letter-spacing: .5px; }
    .brand-sub  { font-size: .62rem; color: var(--gold); display: block; font-weight: 600; letter-spacing: .5px; text-transform: uppercase; }

    .sidebar-nav {
        flex: 1; overflow-y: auto; overflow-x: hidden;
        padding: .5rem 0 1rem;
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,.08) transparent;
    }
    .sidebar-nav::-webkit-scrollbar { width: 3px; }
    .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); border-radius: 4px; }

    .nav-section { padding: 1rem 1.25rem .3rem; }
    .nav-section-label {
        font-size: .58rem; font-weight: 700;
        letter-spacing: 2px; text-transform: uppercase;
        color: rgba(255,255,255,.28);
        display: flex; align-items: center; gap: .4rem;
    }
    .nav-section-label::after { content: ''; flex: 1; height: 1px; background: rgba(255,255,255,.08); }

    .sidebar-nav .nav-link {
        display: flex; align-items: center; gap: .7rem;
        padding: .52rem 1.25rem;
        color: rgba(255,255,255,.60);
        font-size: .82rem; font-weight: 500;
        text-decoration: none;
        border-left: 3px solid transparent;
        border-radius: 0 8px 8px 0;
        margin: .05rem .75rem .05rem 0;
        transition: var(--transition);
        white-space: nowrap;
    }
    .sidebar-nav .nav-link .nav-icon { font-size: 1rem; width: 20px; text-align: center; flex-shrink: 0; }
    .sidebar-nav .nav-link:hover {
        color: #fff;
        background: rgba(255,255,255,.08);
        border-left-color: rgba(82,183,136,.6);
        padding-left: 1.45rem;
    }
    .sidebar-nav .nav-link.active {
        color: #fff;
        background: linear-gradient(90deg, rgba(82,183,136,.25) 0%, rgba(82,183,136,.05) 100%);
        border-left-color: var(--accent);
        font-weight: 600;
    }
    .sidebar-nav .nav-link.active .nav-icon { color: var(--accent); }

    .sidebar-footer {
        padding: .85rem 1.25rem;
        border-top: 1px solid rgba(255,255,255,.08);
        flex-shrink: 0; background: rgba(0,0,0,.12);
    }
    .sidebar-user { display: flex; align-items: center; gap: .65rem; }
    .sidebar-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        background: linear-gradient(135deg, var(--accent), var(--secondary));
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .88rem; color: #fff;
        flex-shrink: 0; border: 2px solid rgba(255,255,255,.2);
    }
    .sidebar-user-info { overflow: hidden; flex: 1; }
    .sidebar-user-name { font-size: .8rem; font-weight: 600; color: #fff; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sidebar-user-role { font-size: .65rem; color: rgba(255,255,255,.4); }
    .sidebar-logout-btn {
        background: transparent; border: none;
        color: rgba(255,255,255,.4); font-size: 1rem;
        cursor: pointer; padding: .3rem; border-radius: 6px;
        transition: var(--transition); flex-shrink: 0;
    }
    .sidebar-logout-btn:hover { color: #fff; background: rgba(220,38,38,.3); }

    .sidebar-backdrop {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,.5); z-index: 1040;
        backdrop-filter: blur(2px);
    }
    .sidebar-backdrop.show { display: block; }

    /* ── TOPBAR ── */
    .topbar {
        position: fixed; top: 0;
        left: var(--sidebar-w); right: 0;
        height: var(--topbar-h);
        background: var(--white);
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center;
        justify-content: space-between;
        padding: 0 1.5rem;
        z-index: 1030;
        transition: left .3s cubic-bezier(.4,0,.2,1);
        box-shadow: var(--shadow-sm);
    }
    .topbar-left  { display: flex; align-items: center; gap: .75rem; }
    .topbar-right { display: flex; align-items: center; gap: .5rem; }
    .page-title { font-size: 1rem; font-weight: 700; color: var(--primary); margin: 0; line-height: 1.2; }
    .breadcrumb { font-size: .72rem; margin: 0; color: var(--text-muted); }
    .breadcrumb-item + .breadcrumb-item::before { color: var(--border); }

    .sidebar-toggle {
        display: none;
        background: none; border: 1px solid var(--border);
        font-size: 1.1rem; color: var(--primary);
        cursor: pointer; padding: .35rem .5rem;
        border-radius: var(--radius-sm); line-height: 1;
        transition: var(--transition);
    }
    .sidebar-toggle:hover { background: var(--bg); border-color: var(--primary); }

    .topbar-icon-btn {
        position: relative;
        width: 36px; height: 36px;
        border-radius: var(--radius-sm);
        background: var(--bg); border: 1px solid var(--border);
        color: var(--text-muted);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 1rem;
        transition: var(--transition); text-decoration: none;
    }
    .topbar-icon-btn:hover { background: var(--primary); border-color: var(--primary); color: #fff; }
    .topbar-icon-btn .badge-dot {
        position: absolute; top: 5px; right: 5px;
        width: 8px; height: 8px;
        background: var(--danger); border-radius: 50%; border: 2px solid #fff;
    }

    .topbar-profile {
        display: flex; align-items: center; gap: .5rem;
        cursor: pointer; padding: .3rem .5rem;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border); background: var(--bg);
        transition: var(--transition);
    }
    .topbar-profile:hover { background: #fff; border-color: var(--primary); }
    .topbar-avatar {
        width: 32px; height: 32px; border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex; align-items: center; justify-content: center;
        font-size: .75rem; font-weight: 700; color: #fff; flex-shrink: 0;
    }
    .topbar-username { font-size: .8rem; font-weight: 600; color: var(--text); max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    /* ── LAYOUT ── */
    .main-wrapper {
        margin-left: var(--sidebar-w);
        padding-top: var(--topbar-h);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    .content-area {
        padding: 1.5rem;
        flex: 1;
    }

    /* ── FOOTER ── */
    .main-footer {
        text-align: center;
        padding: .85rem 1.5rem;
        font-size: .72rem;
        color: var(--text-light);
        border-top: 1px solid var(--border);
        background: #fff;
        flex-shrink: 0;
    }

    /* ── PAGE COMPONENTS ── */
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; }
    .page-header-title { font-size: 1.25rem; font-weight: 700; color: var(--primary); margin: 0 0 .2rem; }
    .page-header-sub { font-size: .78rem; color: var(--text-muted); margin: 0; }

    .page-card { background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; transition: var(--transition); }
    .page-card:hover { box-shadow: var(--shadow-md); }
    .card-head { padding: 1rem 1.25rem; border-bottom: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .5rem; }
    .card-head-title { display: flex; align-items: center; gap: .5rem; font-weight: 700; color: var(--primary); font-size: .88rem; margin: 0; }
    .card-head-title i { color: var(--accent); }
    .card-body-inner { padding: 1.25rem; }

    /* ── STAT CARDS ── */
    .stat-card { background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); padding: 1.2rem; display: flex; align-items: center; gap: 1rem; height: 100%; transition: var(--transition); cursor: default; }
    .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-hover); border-color: transparent; }
    .stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; }
    .stat-icon.navy   { background: linear-gradient(135deg,#DBEAFE,#BFDBFE); color: #1E3A5F; }
    .stat-icon.blue   { background: linear-gradient(135deg,#E0F2FE,#BAE6FD); color: #0284C7; }
    .stat-icon.teal   { background: linear-gradient(135deg,#CCFBF1,#99F6E4); color: #0D9488; }
    .stat-icon.orange { background: linear-gradient(135deg,#FFEDD5,#FED7AA); color: #EA580C; }
    .stat-icon.purple { background: linear-gradient(135deg,#EDE9FE,#DDD6FE); color: #7C3AED; }
    .stat-icon.green  { background: linear-gradient(135deg,#D1FAE5,#A7F3D0); color: #059669; }
    .stat-icon.red    { background: linear-gradient(135deg,#FEE2E2,#FECACA); color: #DC2626; }
    .stat-icon.gold   { background: linear-gradient(135deg,#FEF3C7,#FDE68A); color: #D97706; }
    .stat-icon.yellow { background: linear-gradient(135deg,#FEF3C7,#FDE68A); color: #D97706; }
    .stat-val { font-size: 1.75rem; font-weight: 800; color: var(--primary); line-height: 1; }
    .stat-lbl { font-size: .72rem; color: var(--text-muted); margin-top: .25rem; font-weight: 500; }

    /* ── TABLE ── */
    .table { margin: 0; font-size: .83rem; }
    .table thead tr { background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); }
    .table thead th { color: rgba(255,255,255,.9); font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; padding: .85rem 1rem; border: none; white-space: nowrap; }
    .table tbody tr { border-bottom: 1px solid #F8FAFC; transition: background .15s; }
    .table tbody tr:nth-child(even) { background: #FAFCFF; }
    .table tbody tr:hover { background: #EEF4FF !important; }
    .table tbody td { padding: .75rem 1rem; vertical-align: middle; border: none; color: var(--text); }

    /* ── BADGES ── */
    .badge-status { display: inline-flex; align-items: center; gap: .3rem; padding: .3rem .75rem; border-radius: 20px; font-size: .7rem; font-weight: 700; white-space: nowrap; }
    .badge-status::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; opacity: .7; }
    .badge-menunggu     { background: #FEF3C7; color: #92400E; }
    .badge-diverifikasi { background: #DBEAFE; color: #1E40AF; }
    .badge-ditolak      { background: #FEE2E2; color: #991B1B; }
    .badge-layak        { display: inline-flex; align-items: center; gap: .3rem; background: #D1FAE5; color: #065F46; border-radius: 20px; padding: .28rem .7rem; font-size: .7rem; font-weight: 700; }
    .badge-tidaklayak   { display: inline-flex; align-items: center; gap: .3rem; background: #FEE2E2; color: #991B1B; border-radius: 20px; padding: .28rem .7rem; font-size: .7rem; font-weight: 700; }

    /* ── BUTTONS ── */
    .btn { font-weight: 500; font-size: .83rem; border-radius: var(--radius-sm); transition: var(--transition); display: inline-flex; align-items: center; gap: .35rem; }
    .btn-primary { background: var(--primary); border-color: var(--primary); color: #fff; }
    .btn-primary:hover { background: var(--primary-light); border-color: var(--primary-light); box-shadow: 0 4px 12px rgba(30,58,95,.3); color: #fff; }
    .btn-success { background: var(--secondary); border-color: var(--secondary); color: #fff; }
    .btn-success:hover { background: var(--secondary-dark); border-color: var(--secondary-dark); box-shadow: 0 4px 12px rgba(45,106,79,.3); color: #fff; }
    .btn-warning { background: #F59E0B; border-color: #F59E0B; color: #fff; }
    .btn-warning:hover { background: #D97706; border-color: #D97706; color: #fff; }
    .btn-danger { background: var(--danger); border-color: var(--danger); color: #fff; }
    .btn-danger:hover { background: #B91C1C; border-color: #B91C1C; color: #fff; }
    .btn-info { background: var(--info); border-color: var(--info); color: #fff; }
    .btn-info:hover { background: #0369A1; color: #fff; }
    .btn-outline-primary { color: var(--primary); border-color: var(--primary); }
    .btn-outline-primary:hover { background: var(--primary); color: #fff; }
    .btn-outline-secondary { color: var(--text-muted); border-color: var(--border); }
    .btn-outline-secondary:hover { background: var(--bg); color: var(--text); }
    .btn-sm { font-size: .75rem; padding: .3rem .65rem; }
    .btn-action { width: 32px; height: 32px; padding: 0; justify-content: center; border-radius: var(--radius-sm); }

    /* ── FORMS ── */
    .form-label { font-size: .82rem; font-weight: 600; color: var(--text); margin-bottom: .4rem; }
    .form-control, .form-select { font-size: .85rem; border-color: var(--border); border-radius: var(--radius-sm); padding: .55rem .85rem; transition: var(--transition); color: var(--text); }
    .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(30,58,95,.12); }
    .input-group-text { background: #F8FAFC; border-color: var(--border); color: var(--text-muted); font-size: .85rem; }
    .form-control.is-invalid { border-color: var(--danger); }
    .form-control.is-invalid:focus { box-shadow: 0 0 0 3px rgba(220,38,38,.12); }
    .invalid-feedback { font-size: .75rem; }

    /* ── ALERTS ── */
    .alert { border-radius: var(--radius-md); border: none; font-size: .85rem; font-weight: 500; padding: .85rem 1rem; }
    .alert-success { background: #ECFDF5; color: #065F46; border-left: 4px solid var(--accent); }
    .alert-danger  { background: #FEF2F2; color: #991B1B; border-left: 4px solid var(--danger); }
    .alert-warning { background: #FFFBEB; color: #92400E; border-left: 4px solid var(--gold); }
    .alert-info    { background: #EFF6FF; color: #1E40AF; border-left: 4px solid var(--info); }

    /* ── FILTER BAR ── */
    .filter-bar { background: #F8FAFC; border-radius: var(--radius-md); padding: 1rem; border: 1px solid var(--border); margin-bottom: 1rem; }

    /* ── PAGINATION ── */
    .pagination { gap: .2rem; margin: 0; }
    .page-link { border-radius: var(--radius-sm) !important; font-size: .78rem; font-weight: 500; color: var(--primary); border-color: var(--border); padding: .38rem .65rem; transition: var(--transition); }
    .page-link:hover { background: var(--primary); border-color: var(--primary); color: #fff; }
    .page-item.active .page-link { background: var(--primary); border-color: var(--primary); }
    .page-item.disabled .page-link { color: var(--text-light); }

    /* ── RANK BADGE ── */
    .rank-badge { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; font-weight: 800; font-size: .8rem; }
    .rank-1 { background: #FEF3C7; color: #92400E; }
    .rank-2 { background: #F1F5F9; color: #475569; }
    .rank-3 { background: #FFEDD5; color: #9A3412; }
    .rank-n { background: #EDE9FE; color: #5B21B6; }

    /* ── EMPTY STATE ── */
    .empty-state { text-align: center; padding: 3rem 1rem; color: var(--text-light); }
    .empty-state i { font-size: 3rem; display: block; margin-bottom: 1rem; opacity: .5; }
    .empty-state p { font-size: .85rem; margin: 0 0 1rem; }

    /* ── WELCOME BANNER (user) ── */
    .welcome-banner {
        background: linear-gradient(135deg, var(--primary) 0%, #0F2540 55%, var(--secondary-dark) 100%);
        border-radius: var(--radius-lg);
        padding: 1.75rem 2rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-md);
    }
    .welcome-banner-content { position: relative; z-index: 1; }

    /* ── RESPONSIVE ── */
    @media (max-width: 991px) {
        .sidebar { transform: translateX(-100%); }
        .sidebar.open { transform: translateX(0); }
        .topbar { left: 0 !important; }
        .main-wrapper { margin-left: 0; }
        .sidebar-toggle { display: flex !important; align-items: center; }
    }
    @media (max-width: 576px) {
        .content-area { padding: 1rem; }
        .page-header-title { font-size: 1.1rem; }
    }
    </style>

    @stack('styles')
</head>
<body>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

@include('admin.layouts.sidebar')

<div class="main-wrapper" id="mainWrapper">

    @include('admin.layouts.topnavbar')

    <main class="content-area">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')

    </main>

    <footer class="main-footer">
        &copy; {{ date('Y') }} <strong style="color:var(--primary);">SPK Bantuan Sosial</strong>
        — Kelurahan Harjamukti &nbsp;·&nbsp; All rights reserved
    </footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar  = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const toggle   = document.getElementById('sidebarToggle');

    if (toggle)   toggle.addEventListener('click', () => { sidebar.classList.add('open'); backdrop.classList.add('show'); });
    if (backdrop) backdrop.addEventListener('click', () => { sidebar.classList.remove('open'); backdrop.classList.remove('show'); });

    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(a => {
            const b = bootstrap.Alert.getOrCreateInstance(a);
            if (b) b.close();
        });
    }, 5000);
</script>
@stack('scripts')
</body>
</html>