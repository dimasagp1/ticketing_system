<aside class="main-sidebar sidebar-light-primary elevation-0">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link d-flex align-items-center">
        <i class="fas fa-life-ring text-primary mr-2"></i>
        <span class="sidebar-brand-title">
            <strong>HelpDesk Pro</strong>
            <small>Dukungan TI Perusahaan</small>
        </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center border-bottom">
            <div class="image">
                <img src="{{ auth()->user()->avatar_url }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="{{ route('profile.edit') }}" class="d-block">{{ auth()->user()->name }}</a>
                <small class="text-muted d-block">
                    <i class="fas fa-circle text-success" style="font-size: 8px;"></i>
                    {{ ucfirst(auth()->user()->role) }}
                </small>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar nav-flat nav-compact flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dasbor</p>
                    </a>
                </li>

                @if(auth()->user()->isClient())
                    <!-- Client Menu -->
                    <li class="nav-header">PROYEK SAYA</li>
                    <li class="nav-item">
                        <a href="{{ route('project-requests.index') }}" class="nav-link {{ request()->routeIs('project-requests.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-ticket-alt"></i>
                            <p>Permintaan Saya</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('project-requests.create') }}" class="nav-link">
                            <i class="nav-icon fas fa-plus"></i>
                            <p>Tiket Baru</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('chat.index') }}" class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-comments"></i>
                            <p>Chat</p>
                        </a>
                    </li>
                @endif

                @if(auth()->user()->isDeveloper())
                    <!-- Developer Menu -->
                    <li class="nav-header">PROYEK</li>
                    <li class="nav-item">
                        <a href="{{ route('project-requests.index') }}" class="nav-link {{ request()->routeIs('project-requests.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tasks"></i>
                            <p>Semua Proyek</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('chat.index') }}" class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-comments"></i>
                            <p>Chat</p>
                        </a>
                    </li>
                @endif

                @if(auth()->user()->canApproveProjects())
                    <!-- Admin Menu -->
                    <li class="nav-header">MANAJEMEN</li>
                    <li class="nav-item">
                        <a href="{{ route('queues.index') }}" class="nav-link {{ request()->routeIs('queues.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-layer-group"></i>
                            <p>Papan Antrian</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('approvals.index') }}" class="nav-link {{ request()->routeIs('approvals.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-check-circle"></i>
                            <p>
                                Persetujuan
                                <span class="badge badge-warning right">Baru</span>
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('project-requests.index') }}" class="nav-link {{ request()->routeIs('project-requests.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-folder"></i>
                            <p>Semua Permintaan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('chat.index') }}" class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-comments"></i>
                            <p>Chat</p>
                        </a>
                    </li>
                @endif

                @if(auth()->user()->isSuperAdmin())
                    <!-- Super Admin Menu -->
                    <li class="nav-header">SUPER ADMIN</li>
                    <li class="nav-item">
                        <a href="{{ route('super-admin.dashboard') }}" class="nav-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Dasbor Admin</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('super-admin.users.index') }}" class="nav-link {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Manajemen Pengguna</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('super-admin.activity-logs') }}" class="nav-link {{ request()->routeIs('super-admin.activity-logs') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Log Aktivitas</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('super-admin.reports') }}" class="nav-link {{ request()->routeIs('super-admin.reports') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Laporan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('super-admin.settings') }}" class="nav-link {{ request()->routeIs('super-admin.settings') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Pengaturan</p>
                        </a>
                    </li>
                @endif

                @if(auth()->user()->isAdmin())
                    <li class="nav-header">ADMIN</li>
                    <li class="nav-item">
                        <a href="{{ route('super-admin.users.index') }}" class="nav-link {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-check"></i>
                            <p>Aktivasi Pengguna</p>
                        </a>
                    </li>
                @endif

                <!-- Common Menu -->
                <li class="nav-header">AKUN</li>
                <li class="nav-item">
                    <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>Profil Saya</p>
                    </a>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="#" onclick="event.preventDefault(); this.closest('form').submit();" class="nav-link">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Keluar</p>
                        </a>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>
