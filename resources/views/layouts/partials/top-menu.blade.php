<div class="top-menu-wrap">
    <div class="container-fluid">
        <div class="top-menu-scroll">
            <ul class="top-menu-list">
                <li>
                    <a href="{{ route('dashboard') }}" class="top-menu-link {{ request()->routeIs('dashboard') || request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                        <span>Dasbor</span>
                    </a>
                </li>

                @if(auth()->user()->isSuperAdmin())
                    <li>
                        <a href="{{ route('project-requests.index') }}" class="top-menu-link {{ request()->routeIs('project-requests.*') ? 'active' : '' }}">
                            <span>Tiket</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('super-admin.reports') }}" class="top-menu-link {{ request()->routeIs('super-admin.reports*') ? 'active' : '' }}">
                            <span>Laporan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('super-admin.reports.technical') }}" class="top-menu-link {{ request()->routeIs('super-admin.reports.technical') ? 'active' : '' }}">
                            <span>Laporan Teknis</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('super-admin.users.index') }}" class="top-menu-link {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}">
                            <span>Aset</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('super-admin.activity-logs') }}" class="top-menu-link {{ request()->routeIs('super-admin.activity-logs') ? 'active' : '' }}">
                            <span>Knowledge Base</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('super-admin.settings') }}" class="top-menu-link {{ request()->routeIs('super-admin.settings') ? 'active' : '' }}">
                            <span>Pengaturan</span>
                        </a>
                    </li>
                @elseif(auth()->user()->canApproveProjects())
                    <li>
                        <a href="{{ route('queues.index') }}" class="top-menu-link {{ request()->routeIs('queues.*') ? 'active' : '' }}">
                            <span>Antrian</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('approvals.index') }}" class="top-menu-link {{ request()->routeIs('approvals.*') ? 'active' : '' }}">
                            <span>Persetujuan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('project-requests.index') }}" class="top-menu-link {{ request()->routeIs('project-requests.*') ? 'active' : '' }}">
                            <span>Semua Tiket</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('chat.index') }}" class="top-menu-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                            <span>Chat</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('super-admin.users.index') }}" class="top-menu-link {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}">
                            <span>Pengguna</span>
                        </a>
                    </li>
                @elseif(auth()->user()->isDeveloper())
                    <li>
                        <a href="{{ route('project-requests.index') }}" class="top-menu-link {{ request()->routeIs('project-requests.*') ? 'active' : '' }}">
                            <span>Tiket</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('chat.index') }}" class="top-menu-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                            <span>Chat</span>
                        </a>
                    </li>
                @elseif(auth()->user()->isClient())
                    <li>
                        <a href="{{ route('project-requests.index') }}" class="top-menu-link {{ request()->routeIs('project-requests.*') ? 'active' : '' }}">
                            <span>Tiket</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('project-requests.create') }}" class="top-menu-link {{ request()->routeIs('project-requests.create') ? 'active' : '' }}">
                            <span>Buat Tiket</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('chat.index') }}" class="top-menu-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                            <span>Chat</span>
                        </a>
                    </li>
                @endif

                <li>
                    <a href="{{ route('profile.edit') }}" class="top-menu-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <span>Profil</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
