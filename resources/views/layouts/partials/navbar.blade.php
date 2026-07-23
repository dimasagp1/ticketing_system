<nav class="main-header navbar navbar-expand border-b-4 border-black bg-[#FFE600] dark:bg-[#121212] dark:border-white px-3 py-2 shadow-[0px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[0px_4px_0px_0px_#FFE600] flex items-center justify-between z-50">
    <!-- Left area -->
    <ul class="navbar-nav flex items-center gap-2">
        {{-- Mobile Hamburger --}}
        <li class="nav-item d-flex d-md-none align-items-center">
            <button class="btn p-0 rounded-xl border-3 border-black bg-white dark:bg-[#222] text-black dark:text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] d-inline-flex align-items-center justify-content-center" id="mobileMenuToggle" aria-label="Buka menu navigasi" style="width: 38px; height: 38px;">
                <i class="fas fa-bars text-base leading-none m-0" id="mobileMenuIcon"></i>
            </button>
        </li>
        <li class="nav-item d-flex align-items-center">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 bg-[#FFE600] border-3 border-black px-3 py-1.5 rounded-2xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 transition-transform select-none">
                @if(\App\Helpers\SettingsHelper::get('app_logo'))
                    <img src="{{ asset('storage/' . \App\Helpers\SettingsHelper::get('app_logo')) }}" alt="Logo" class="h-8 w-auto max-w-[160px] object-contain">
                @else
                    <span class="w-7 h-7 bg-[#FF007A] text-white border-2 border-black rounded-full flex items-center justify-center font-fredoka font-black text-sm shrink-0">
                        <i class="fas fa-life-ring"></i>
                    </span>
                    <span class="font-fredoka font-black text-base md:text-lg text-black text-stroke-sm leading-none drop-shadow-[1px_1px_0px_#0055FF]">
                        {{ \App\Helpers\SettingsHelper::get('app_name', config('app.name', 'Antrian Project')) }}
                    </span>
                @endif
            </a>
        </li>
    </ul>

    <!-- Search Form -->
    <form class="form-inline ml-2 mr-auto support-search max-w-md w-full hidden sm:flex" action="{{ route('project-requests.index') }}" method="GET">
        <div class="input-group input-group-sm w-full border-3 border-black dark:border-white rounded-full bg-white dark:bg-[#1a1a1a] shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_#FFE600] overflow-hidden p-1">
            <div class="input-group-prepend">
                <span class="input-group-text bg-[#FFE600] border-2 border-black rounded-full text-black font-black px-3 d-inline-flex align-items-center justify-content-center"><i class="fas fa-search"></i></span>
            </div>
            <input type="text" name="search" class="form-control border-0 bg-transparent text-black dark:text-white font-jakarta font-extrabold px-3 focus:outline-none shadow-none text-sm" placeholder="Cari tiket..." value="{{ request('search') }}">
        </div>
    </form>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto flex items-center gap-2">
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link p-0 bg-white dark:bg-[#222] text-black dark:text-white border-3 border-black dark:border-white rounded-2xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_#FFE600] hover:bg-[#FFE600] cursor-pointer d-inline-flex align-items-center justify-content-center" data-toggle="dropdown" href="#" id="notification-bell" title="Notifikasi" style="width: 38px; height: 38px; display: inline-flex !important; align-items: center !important; justify-content: center !important;">
                <i class="far fa-bell text-base leading-none m-0"></i>
                <span class="badge bg-[#FF007A] text-white border-2 border-black rounded-full text-[10px] font-black ml-1" id="notification-count" style="display: none;">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right border-4 border-black dark:border-[#FFE600] rounded-3xl p-3 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_#FFE600] bg-white dark:bg-[#121212]" id="notification-dropdown">
                <span class="dropdown-item dropdown-header font-fredoka font-black text-black dark:text-white uppercase" id="notification-header">0 Notifikasi</span>
                <div class="dropdown-divider border-t-2 border-black"></div>
                <div id="notification-list" class="font-jakarta font-bold text-sm">
                    <div class="text-center p-3 text-gray-500">
                        <i class="fas fa-spinner fa-spin"></i> Memuat...
                    </div>
                </div>
                <div class="dropdown-divider border-t-2 border-black"></div>
                <a href="#" class="dropdown-item dropdown-footer font-fredoka font-black text-center text-[#0055FF] dark:text-[#FFE600]">Lihat Semua Notifikasi</a>
            </div>
        </li>

        <!-- Dark Mode Toggle Button -->
        <li class="nav-item">
            <a class="nav-link p-0 bg-[#9000FF] text-white border-3 border-black dark:border-white rounded-2xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_#FFE600] hover:bg-[#FFE600] hover:text-black cursor-pointer d-inline-flex align-items-center justify-content-center" href="#" id="dark-mode-toggle" title="Toggle Dark Mode" style="width: 38px; height: 38px; display: inline-flex !important; align-items: center !important; justify-content: center !important;">
                <i class="far fa-moon text-base leading-none m-0" id="dark-mode-icon"></i>
            </a>
        </li>

        <!-- Chat Link -->
        <li class="nav-item">
            <a class="nav-link p-0 bg-[#00E676] text-black border-3 border-black dark:border-white rounded-2xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_#FFE600] hover:bg-[#FF007A] hover:text-white cursor-pointer d-inline-flex align-items-center justify-content-center" href="{{ route('chat.index') }}" title="Chat" style="width: 38px; height: 38px; display: inline-flex !important; align-items: center !important; justify-content: center !important;">
                <i class="far fa-comment-alt text-base leading-none m-0"></i>
            </a>
        </li>

        <!-- User Profile Badge & Dropdown Menu -->
        <li class="nav-item dropdown ml-1">
            <a class="nav-link d-inline-flex align-items-center cursor-pointer" data-toggle="dropdown" href="#" role="button" style="background-color: #FFE600; border: 3px solid #000000; border-radius: 1.25rem; padding: 0.25rem 0.65rem; box-shadow: 3px 3px 0px 0px #000000; height: 38px; text-decoration: none; white-space: nowrap;">
                <span class="d-inline-flex align-items-center justify-content-center text-white font-fredoka font-black rounded-circle mr-1.5" style="width: 26px; height: 26px; background-color: #FF007A; border: 2px solid #000000; font-size: 0.72rem; flex-shrink: 0; line-height: 1;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </span>
                <span class="font-fredoka font-black text-dark text-uppercase mr-1.5" style="font-size: 0.78rem; line-height: 1; white-space: nowrap; color: #000000 !important;">
                    {{ auth()->user()->name }}
                </span>
                <span class="badge bg-black text-white font-fredoka font-black text-uppercase" style="font-size: 0.62rem; border-radius: 9999px; padding: 0.2rem 0.45rem; line-height: 1; white-space: nowrap;">
                    {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                </span>
            </a>
            
            <!-- TOONWORLD High-Contrast Profile Dropdown Menu -->
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right border-4 border-black dark:border-[#FFE600] rounded-3xl p-3 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_#FFE600] bg-white dark:bg-[#121212] mt-2">
                <div class="bg-[#FFE600] dark:bg-[#222] border-3 border-black dark:border-white rounded-2xl p-3 text-center mb-3 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                    <span class="font-fredoka font-black text-base text-black dark:text-white uppercase block leading-tight mb-1">
                        {{ auth()->user()->name }}
                    </span>
                    <span class="bg-[#FF007A] text-white text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase border border-black inline-block">
                        {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                    </span>
                </div>
                
                <a href="{{ route('profile.edit') }}" class="dropdown-item font-fredoka font-black text-sm py-2.5 px-3 rounded-xl text-black dark:text-white hover:bg-[#FFE600] hover:text-black flex items-center gap-2 mb-1">
                    <span class="w-6 h-6 rounded-lg bg-[#0055FF] text-white flex items-center justify-center text-xs border border-black">
                        <i class="fas fa-user"></i>
                    </span>
                    <span>Profil Saya</span>
                </a>
                
                <div class="dropdown-divider border-t-2 border-black dark:border-white my-2"></div>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item logout-btn font-fredoka font-black text-sm py-2.5 px-3 rounded-xl text-[#FF007A] dark:text-[#FF007A] hover:bg-[#FF007A] hover:text-white flex items-center gap-2 w-full text-left">
                        <span class="w-6 h-6 rounded-lg bg-[#FF007A] text-white flex items-center justify-center text-xs border border-black">
                            <i class="fas fa-sign-out-alt"></i>
                        </span>
                        <span>Keluar / Logout</span>
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>
