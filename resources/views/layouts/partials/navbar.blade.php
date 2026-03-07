<nav class="main-header navbar navbar-expand navbar-light elevation-0">
    <!-- Left area -->
    <ul class="navbar-nav">
        <li class="nav-item d-flex align-items-center">
            <a href="{{ route('dashboard') }}" class="brand-head ml-1">
                @if(\App\Helpers\SettingsHelper::get('app_logo'))
                    <span class="brand-icon"><img src="{{ asset('storage/' . \App\Helpers\SettingsHelper::get('app_logo')) }}" alt="Logo" style="height: 28px; width: auto; max-width: 140px; object-fit: contain;"></span>
                @else
                    <span class="brand-icon"><i class="fas fa-life-ring"></i></span>
                    <span>{{ \App\Helpers\SettingsHelper::get('app_name', config('app.name', 'Antrian Project')) }}</span>
                @endif
            </a>
        </li>
    </ul>

    <form class="form-inline ml-2 mr-auto support-search" action="{{ route('project-requests.index') }}" method="GET">
        <div class="input-group input-group-sm w-100 shadow-sm" style="border-radius: 20px; overflow: hidden;">
            <div class="input-group-prepend">
                <span class="input-group-text bg-light border-0 text-muted pl-3"><i class="fas fa-search"></i></span>
            </div>
            <input type="text" name="search" class="form-control border-0 bg-light shadow-none" placeholder="Cari tiket..." value="{{ request('search') }}">
        </div>
    </form>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link top-icon-btn" data-toggle="dropdown" href="#" id="notification-bell">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge" id="notification-count" style="display: none;">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="notification-dropdown">
                <span class="dropdown-item dropdown-header" id="notification-header">0 Notifikasi</span>
                <div class="dropdown-divider"></div>
                <div id="notification-list">
                    <div class="text-center p-3 text-muted">
                        <i class="fas fa-spinner fa-spin"></i> Memuat...
                    </div>
                </div>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">Lihat Semua Notifikasi</a>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link top-icon-btn" href="#" id="dark-mode-toggle" title="Toggle Dark Mode">
                <i class="far fa-moon" id="dark-mode-icon"></i>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link top-icon-btn" href="{{ route('chat.index') }}" title="Chat">
                <i class="far fa-comment-alt"></i>
            </a>
        </li>

        <li class="nav-item d-none d-md-flex align-items-center">
            <span class="top-divider"></span>
        </li>

        <!-- User Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link text-muted" data-toggle="dropdown" href="#">
                <span class="d-none d-md-inline user-meta">
                    <span class="user-meta-name">{{ auth()->user()->name }}</span>
                    <span class="user-meta-role">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</span>
                </span>
                <img src="{{ auth()->user()->avatar_url }}" alt="User" class="user-avatar-pill">
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow-sm border-0 mt-2" style="border-radius: 1rem; overflow: hidden;">
                <span class="dropdown-item dropdown-header bg-light">
                    <strong class="text-dark">{{ auth()->user()->name }}</strong><br>
                    <small class="text-muted">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</small>
                </span>
                <div class="dropdown-divider"></div>
                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> Profil
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>
