{{-- ══════════════════════════════════════════════════
     TOP MENU — Desktop: horizontal tab bar
                Mobile : label halaman aktif (hamburger ada di navbar)
══════════════════════════════════════════════════ --}}

<div class="top-menu-wrap">
    <div class="container-fluid">

        {{-- Desktop horizontal list --}}
        <div class="top-menu-scroll d-none d-md-block flex-grow-1">
            <ul class="top-menu-list">
                <li>
                    <a href="{{ route('dashboard') }}" class="top-menu-link {{ request()->routeIs('dashboard') || request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                        <span>Dasbor</span>
                    </a>
                </li>

                @if(auth()->user()->isSuperAdmin())
                    <li><a href="{{ route('project-requests.index') }}" class="top-menu-link {{ request()->routeIs('project-requests.*') ? 'active' : '' }}"><span>Tiket</span></a></li>
                    <li><a href="{{ route('super-admin.reports') }}" class="top-menu-link {{ request()->routeIs('super-admin.reports*') ? 'active' : '' }}"><span>Laporan</span></a></li>
                    <li><a href="{{ route('super-admin.reports.technical') }}" class="top-menu-link {{ request()->routeIs('super-admin.reports.technical') ? 'active' : '' }}"><span>Laporan Teknis</span></a></li>
                    <li><a href="{{ route('super-admin.users.index') }}" class="top-menu-link {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}"><span>Aset</span></a></li>
                    <li><a href="{{ route('super-admin.activity-logs') }}" class="top-menu-link {{ request()->routeIs('super-admin.activity-logs') ? 'active' : '' }}"><span>Knowledge Base</span></a></li>
                    <li><a href="{{ route('super-admin.settings') }}" class="top-menu-link {{ request()->routeIs('super-admin.settings') ? 'active' : '' }}"><span>Pengaturan</span></a></li>

                @elseif(auth()->user()->canApproveProjects())
                    <li><a href="{{ route('queues.index') }}" class="top-menu-link {{ request()->routeIs('queues.*') ? 'active' : '' }}"><span>Antrian</span></a></li>
                    <li><a href="{{ route('approvals.index') }}" class="top-menu-link {{ request()->routeIs('approvals.*') ? 'active' : '' }}"><span>Persetujuan</span></a></li>
                    <li><a href="{{ route('project-requests.index') }}" class="top-menu-link {{ request()->routeIs('project-requests.*') ? 'active' : '' }}"><span>Semua Tiket</span></a></li>
                    <li><a href="{{ route('chat.index') }}" class="top-menu-link {{ request()->routeIs('chat.*') ? 'active' : '' }}"><span>Chat</span></a></li>
                    <li><a href="{{ route('daily-logs.index') }}" class="top-menu-link {{ request()->routeIs('daily-logs.*') ? 'active' : '' }}"><span>Log Harian</span></a></li>
                    <li><a href="{{ route('super-admin.users.index') }}" class="top-menu-link {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}"><span>Pengguna</span></a></li>

                @elseif(auth()->user()->isDeveloper())
                    <li><a href="{{ route('project-requests.index') }}" class="top-menu-link {{ request()->routeIs('project-requests.*') ? 'active' : '' }}"><span>Tiket</span></a></li>
                    <li><a href="{{ route('chat.index') }}" class="top-menu-link {{ request()->routeIs('chat.*') ? 'active' : '' }}"><span>Chat</span></a></li>
                    <li><a href="{{ route('daily-logs.index') }}" class="top-menu-link {{ request()->routeIs('daily-logs.*') ? 'active' : '' }}"><span>Log Harian</span></a></li>

                @elseif(auth()->user()->isClient())
                    <li><a href="{{ route('project-requests.index') }}" class="top-menu-link {{ request()->routeIs('project-requests.*') ? 'active' : '' }}"><span>Tiket</span></a></li>
                    <li><a href="{{ route('project-requests.create') }}" class="top-menu-link {{ request()->routeIs('project-requests.create') ? 'active' : '' }}"><span>Buat Tiket</span></a></li>
                    <li><a href="{{ route('chat.index') }}" class="top-menu-link {{ request()->routeIs('chat.*') ? 'active' : '' }}"><span>Chat</span></a></li>
                @endif

                <li><a href="{{ route('profile.edit') }}" class="top-menu-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"><span>Profil</span></a></li>
            </ul>
        </div>

        {{-- Active page label (mobile only) --}}
        <span class="d-md-none mobile-active-label flex-grow-1 text-center font-weight-700" style="font-size:.88rem;color:#1f2d3d;">
            @php
                $pageLabel = 'Dasbor';
                if (request()->routeIs('project-requests.*'))  $pageLabel = auth()->user()->isClient() ? 'Tiket Saya' : 'Semua Tiket';
                elseif (request()->routeIs('queues.*'))         $pageLabel = 'Antrian';
                elseif (request()->routeIs('approvals.*'))      $pageLabel = 'Persetujuan';
                elseif (request()->routeIs('chat.*'))           $pageLabel = 'Chat';
                elseif (request()->routeIs('daily-logs.*'))     $pageLabel = 'Log Harian';
                elseif (request()->routeIs('super-admin.users.*')) $pageLabel = 'Pengguna';
                elseif (request()->routeIs('super-admin.reports*')) $pageLabel = 'Laporan';
                elseif (request()->routeIs('super-admin.settings')) $pageLabel = 'Pengaturan';
                elseif (request()->routeIs('super-admin.activity-logs')) $pageLabel = 'Knowledge Base';
                elseif (request()->routeIs('profile.*'))        $pageLabel = 'Profil';
                elseif (request()->routeIs('dashboard') || request()->routeIs('super-admin.dashboard')) $pageLabel = 'Dasbor';
            @endphp
            {{ $pageLabel }}
        </span>

    </div>
</div>

{{-- ══════════════════════════════════════════════════
     MOBILE DRAWER OVERLAY
══════════════════════════════════════════════════ --}}
<div id="mobileMenuOverlay" class="mobile-drawer-overlay d-md-none" aria-hidden="true"></div>

<nav id="mobileDrawer" class="mobile-drawer d-md-none" aria-label="Menu navigasi mobile">
    {{-- Drawer Header --}}
    <div class="mobile-drawer-header">
        <div class="d-flex align-items-center" style="gap:.5rem;">
            <div class="brand-icon" style="width:28px;height:28px;border-radius:6px;background:var(--theme-orange);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;">
                <i class="fas fa-life-ring"></i>
            </div>
            <span style="font-weight:800;font-size:.95rem;color:#1f2d3d;">
                {{ \App\Helpers\SettingsHelper::get('app_name', config('app.name', 'Antrian Project')) }}
            </span>
        </div>
        <button id="mobileDrawerClose" class="btn btn-sm btn-light border" style="border-radius:.5rem;">
            <i class="fas fa-times"></i>
        </button>
    </div>

    {{-- User info strip --}}
    <div class="mobile-drawer-user">
        <img src="{{ auth()->user()->avatar_url }}" alt="User" class="user-avatar-pill" style="width:36px;height:36px;border-radius:10px;">
        <div>
            <div style="font-weight:700;font-size:.88rem;color:#1f2d3d;">{{ auth()->user()->name }}</div>
            <div style="font-size:.72rem;color:#94a3b8;">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</div>
        </div>
    </div>

    {{-- Nav items --}}
    <ul class="mobile-drawer-nav">
        <li>
            <a href="{{ route('dashboard') }}" class="mobile-drawer-link {{ request()->routeIs('dashboard') || request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i><span>Dasbor</span>
            </a>
        </li>

        @if(auth()->user()->isSuperAdmin())
            <li><a href="{{ route('project-requests.index') }}" class="mobile-drawer-link {{ request()->routeIs('project-requests.*') ? 'active' : '' }}"><i class="fas fa-ticket-alt"></i><span>Tiket</span></a></li>
            <li><a href="{{ route('super-admin.reports') }}" class="mobile-drawer-link {{ request()->routeIs('super-admin.reports*') ? 'active' : '' }}"><i class="fas fa-chart-bar"></i><span>Laporan</span></a></li>
            <li><a href="{{ route('super-admin.reports.technical') }}" class="mobile-drawer-link {{ request()->routeIs('super-admin.reports.technical') ? 'active' : '' }}"><i class="fas fa-tools"></i><span>Laporan Teknis</span></a></li>
            <li><a href="{{ route('super-admin.users.index') }}" class="mobile-drawer-link {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}"><i class="fas fa-users"></i><span>Aset / Pengguna</span></a></li>
            <li><a href="{{ route('super-admin.activity-logs') }}" class="mobile-drawer-link {{ request()->routeIs('super-admin.activity-logs') ? 'active' : '' }}"><i class="fas fa-book"></i><span>Knowledge Base</span></a></li>
            <li><a href="{{ route('super-admin.settings') }}" class="mobile-drawer-link {{ request()->routeIs('super-admin.settings') ? 'active' : '' }}"><i class="fas fa-cog"></i><span>Pengaturan</span></a></li>

        @elseif(auth()->user()->canApproveProjects())
            <li><a href="{{ route('queues.index') }}" class="mobile-drawer-link {{ request()->routeIs('queues.*') ? 'active' : '' }}"><i class="fas fa-layer-group"></i><span>Antrian</span></a></li>
            <li><a href="{{ route('approvals.index') }}" class="mobile-drawer-link {{ request()->routeIs('approvals.*') ? 'active' : '' }}"><i class="fas fa-check-double"></i><span>Persetujuan</span></a></li>
            <li><a href="{{ route('project-requests.index') }}" class="mobile-drawer-link {{ request()->routeIs('project-requests.*') ? 'active' : '' }}"><i class="fas fa-ticket-alt"></i><span>Semua Tiket</span></a></li>
            <li><a href="{{ route('chat.index') }}" class="mobile-drawer-link {{ request()->routeIs('chat.*') ? 'active' : '' }}"><i class="fas fa-comments"></i><span>Chat</span></a></li>
            <li><a href="{{ route('daily-logs.index') }}" class="mobile-drawer-link {{ request()->routeIs('daily-logs.*') ? 'active' : '' }}"><i class="fas fa-book-open"></i><span>Log Harian</span></a></li>
            <li><a href="{{ route('super-admin.users.index') }}" class="mobile-drawer-link {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}"><i class="fas fa-users"></i><span>Pengguna</span></a></li>

        @elseif(auth()->user()->isDeveloper())
            <li><a href="{{ route('project-requests.index') }}" class="mobile-drawer-link {{ request()->routeIs('project-requests.*') ? 'active' : '' }}"><i class="fas fa-ticket-alt"></i><span>Tiket</span></a></li>
            <li><a href="{{ route('chat.index') }}" class="mobile-drawer-link {{ request()->routeIs('chat.*') ? 'active' : '' }}"><i class="fas fa-comments"></i><span>Chat</span></a></li>
            <li><a href="{{ route('daily-logs.index') }}" class="mobile-drawer-link {{ request()->routeIs('daily-logs.*') ? 'active' : '' }}"><i class="fas fa-book-open"></i><span>Log Harian</span></a></li>

        @elseif(auth()->user()->isClient())
            <li><a href="{{ route('project-requests.index') }}" class="mobile-drawer-link {{ request()->routeIs('project-requests.*') ? 'active' : '' }}"><i class="fas fa-ticket-alt"></i><span>Tiket Saya</span></a></li>
            <li><a href="{{ route('project-requests.create') }}" class="mobile-drawer-link {{ request()->routeIs('project-requests.create') ? 'active' : '' }}"><i class="fas fa-plus-circle"></i><span>Buat Tiket</span></a></li>
            <li><a href="{{ route('chat.index') }}" class="mobile-drawer-link {{ request()->routeIs('chat.*') ? 'active' : '' }}"><i class="fas fa-comments"></i><span>Chat</span></a></li>
        @endif

        <li class="mobile-drawer-divider"></li>
        <li><a href="{{ route('profile.edit') }}" class="mobile-drawer-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"><i class="fas fa-user-circle"></i><span>Profil Saya</span></a></li>
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="mobile-drawer-link mobile-drawer-logout w-100">
                    <i class="fas fa-sign-out-alt"></i><span>Keluar</span>
                </button>
            </form>
        </li>
    </ul>
</nav>

<style>
/* ── Mobile drawer styles ── */
.mobile-active-label {
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

.mobile-drawer-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 1045;
    opacity: 0;
    pointer-events: none;
    transition: opacity .25s ease;
    backdrop-filter: blur(2px);
}
.mobile-drawer-overlay.open {
    opacity: 1;
    pointer-events: all;
}

.mobile-drawer {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 280px;
    max-width: 85vw;
    background: #fff;
    z-index: 1050;
    display: flex !important;
    flex-direction: column;
    transform: translateX(-100%);
    transition: transform .28s cubic-bezier(.4,0,.2,1);
    box-shadow: 4px 0 24px rgba(0,0,0,.12);
    overflow: hidden;
}
.mobile-drawer.open {
    transform: translateX(0);
}

.mobile-drawer-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.1rem;
    border-bottom: 1px solid #f1f5f9;
    flex-shrink: 0;
}

.mobile-drawer-user {
    display: flex;
    align-items: center;
    gap: .65rem;
    padding: .75rem 1.1rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    flex-shrink: 0;
}

.mobile-drawer-nav {
    list-style: none;
    margin: 0;
    padding: .5rem 0;
    overflow-y: auto;
    flex: 1;
    overscroll-behavior: contain;
}

.mobile-drawer-link {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .72rem 1.25rem;
    color: #475569;
    font-weight: 600;
    font-size: .9rem;
    text-decoration: none;
    border-left: 3px solid transparent;
    transition: all .15s ease;
    background: none;
    border-top: 0;
    border-right: 0;
    border-bottom: 0;
    cursor: pointer;
    width: 100%;
    text-align: left;
}
.mobile-drawer-link i {
    width: 20px;
    text-align: center;
    font-size: 1rem;
    color: #94a3b8;
    flex-shrink: 0;
    transition: color .15s ease;
}
.mobile-drawer-link:hover {
    background: #f1f5f9;
    color: #1e293b;
    text-decoration: none;
    border-left-color: #cbd5e1;
}
.mobile-drawer-link:hover i { color: #64748b; }
.mobile-drawer-link.active {
    background: rgba(37,99,235,.08);
    color: var(--theme-blue);
    border-left-color: var(--theme-blue);
}
.mobile-drawer-link.active i { color: var(--theme-blue); }

.mobile-drawer-logout {
    color: #dc2626 !important;
}
.mobile-drawer-logout i { color: #dc2626 !important; }
.mobile-drawer-logout:hover { background: #fef2f2 !important; }

.mobile-drawer-divider {
    height: 1px;
    background: #f1f5f9;
    margin: .35rem .75rem;
}

/* Dark mode */
body.dark-mode .mobile-drawer {
    background: #1e1e1e;
    box-shadow: 4px 0 24px rgba(0,0,0,.4);
}
body.dark-mode .mobile-drawer-header { border-color: #2d2d2d; }
body.dark-mode .mobile-drawer-user   { background: #161616; border-color: #2d2d2d; }
body.dark-mode .mobile-drawer-link   { color: #94a3b8; }
body.dark-mode .mobile-drawer-link:hover { background: #2d2d2d; color: #e2e8f0; }
body.dark-mode .mobile-drawer-link.active { background: rgba(37,99,235,.2); color: #93c5fd; border-left-color: #60a5fa; }
body.dark-mode .mobile-drawer-divider { background: #2d2d2d; }
body.dark-mode .mobile-active-label  { color: var(--theme-dark); }

/* Keep top-menu-wrap height consistent on mobile */
@media (max-width: 767px) {
    .top-menu-wrap .container-fluid {
        min-height: 42px;
        padding-left: .6rem;
        padding-right: .6rem;
    }
}
</style>

<script>
(function () {
    const toggle   = document.getElementById('mobileMenuToggle');
    const icon     = document.getElementById('mobileMenuIcon');
    const drawer   = document.getElementById('mobileDrawer');
    const overlay  = document.getElementById('mobileMenuOverlay');
    const closeBtn = document.getElementById('mobileDrawerClose');

    function openDrawer() {
        drawer.classList.add('open');
        overlay.classList.add('open');
        icon.className = 'fas fa-times';
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        drawer.classList.remove('open');
        overlay.classList.remove('open');
        icon.className = 'fas fa-bars';
        document.body.style.overflow = '';
    }

    if (toggle)   toggle.addEventListener('click', openDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    if (overlay)  overlay.addEventListener('click', closeDrawer);

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDrawer();
    });

    // Close drawer when a nav link is tapped (SPA-like feel)
    if (drawer) {
        drawer.querySelectorAll('a.mobile-drawer-link').forEach(function(link) {
            link.addEventListener('click', closeDrawer);
        });
    }
})();
</script>
