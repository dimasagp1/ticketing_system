@php
    $totalUsers = \App\Models\User::count();
    $totalTickets = \App\Models\ProjectRequest::count();
    $openTickets = \App\Models\ProjectRequest::where('ticket_status', 'open')->count();
    $resolvedTickets = \App\Models\ProjectRequest::where('ticket_status', 'resolved')->count();
    $pendingApprovals = \App\Models\ProjectApproval::pending()->count();
    $activeQueues = \App\Models\Queue::where('status', 'In Progress')->count();
    $totalQueues = \App\Models\Queue::count();
    $overdueTickets = \App\Models\ProjectRequest::whereIn('ticket_status', \App\Models\ProjectRequest::slaTrackedTicketStatuses())
        ->whereNotNull('sla_resolution_due_at')
        ->where('sla_resolution_due_at', '<', now())
        ->count();
@endphp

<!-- ACC Ticketing Alert Banner -->
<x-acc-alert :count="$pendingApprovalTickets->count()" :route="route('approvals.index')" />

<!-- Stat Cards Row -->
<div class="row">
    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100 flex flex-col justify-between">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-[#FFE600] text-black border-2 border-black font-fredoka font-black px-3 py-1 rounded-full">
                    <i class="fas fa-users mr-1"></i> USER
                </span>
                <small class="font-jakarta font-extrabold text-muted">Sistem</small>
            </div>
            <div class="font-fredoka font-black text-4xl text-black dark:text-white my-2">{{ number_format($totalUsers) }}</div>
            <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Total User Terdaftar</div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100 flex flex-col justify-between">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-[#FF007A] text-white border-2 border-black font-fredoka font-black px-3 py-1 rounded-full">
                    <i class="fas fa-hourglass-half mr-1"></i> TIKET
                </span>
                <small class="font-fredoka font-black text-[#FF007A]">{{ $pendingApprovalTickets->count() }} Pending</small>
            </div>
            <div class="font-fredoka font-black text-4xl text-black dark:text-white my-2">{{ number_format($totalTickets) }}</div>
            <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Total Tiket Masuk</div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100 flex flex-col justify-between">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-[#0055FF] text-white border-2 border-black font-fredoka font-black px-3 py-1 rounded-full">
                    <i class="fas fa-layer-group mr-1"></i> AKTIF
                </span>
                <small class="font-fredoka font-black text-[#0055FF]">{{ $activeQueues }} Queue</small>
            </div>
            <div class="font-fredoka font-black text-4xl text-black dark:text-white my-2">{{ $openTickets }}</div>
            <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Tiket Terbuka</div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100 flex flex-col justify-between">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-[#00E676] text-black border-2 border-black font-fredoka font-black px-3 py-1 rounded-full">
                    <i class="fas fa-check-circle mr-1"></i> SLA
                </span>
                <small class="font-fredoka font-black text-[#FF007A]">{{ $overdueTickets }} Late</small>
            </div>
            <div class="font-fredoka font-black text-4xl text-black dark:text-white my-2">{{ $overdueTickets }}</div>
            <div class="font-jakarta font-extrabold text-xs text-muted uppercase">SLA Terlewat</div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="row">
    <!-- Left Column: Tables -->
    <div class="col-lg-8 mb-4 space-y-6">
        
        <!-- 1. Daftar Tiket Menunggu Persetujuan (Pending Approvals) -->
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212]">
            <div class="d-flex justify-content-between align-items-center border-b-4 border-black pb-3 mb-3">
                <div class="d-flex items-center gap-2">
                    <h3 class="font-fredoka font-black text-xl text-black dark:text-white uppercase mb-0">
                        Tiket Menunggu Persetujuan ⏳
                    </h3>
                    <span class="badge bg-[#FF007A] text-white border-2 border-black font-fredoka font-black px-2.5 py-1 rounded-full text-xs">
                        {{ $pendingApprovalTickets->count() }} Belum Disetujui
                    </span>
                </div>
                <a href="{{ route('approvals.index') }}" class="btn btn-sm !bg-[#FF007A] !text-white border-2 border-black font-fredoka font-black px-3 py-1 rounded-full shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:!bg-[#FFE600] hover:!text-black transition-all">
                    Tinjau Semua
                </a>
            </div>
            <div class="table-responsive">
                <table class="table border-3 border-black rounded-2xl w-100 text-sm font-jakarta font-extrabold">
                    <thead>
                        <tr class="bg-[#FF007A] text-white font-fredoka font-black uppercase border-b-3 border-black">
                            <th class="p-3">TICKET #</th>
                            <th class="p-3">NAMA PROYEK</th>
                            <th class="p-3">KLIEN</th>
                            <th class="p-3">TANGGAL REQUEST</th>
                            <th class="p-3">STATUS</th>
                            <th class="p-3 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingApprovalTickets as $req)
                            <tr>
                                <td class="p-3"><span class="badge bg-black text-white font-fredoka font-black">{{ $req->ticket_number ?? '#'.$req->id }}</span></td>
                                <td class="p-3 font-fredoka font-black text-black dark:text-white">{{ $req->title }}</td>
                                <td class="p-3 text-xs">{{ $req->client->name ?? '-' }}</td>
                                <td class="p-3 font-mono text-xs">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-3">
                                    <span class="badge bg-[#FFE600] text-black border-2 border-black font-fredoka font-black text-xs px-2 py-1">
                                        Menunggu Persetujuan
                                    </span>
                                </td>
                                <td class="p-3 text-right">
                                    <a href="{{ route('approvals.index') }}" class="btn btn-sm !bg-[#0055FF] !text-white border-2 border-black font-fredoka font-black rounded-xl px-3 py-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:!bg-[#FFE600] hover:!text-black transition-all">
                                        <i class="fas fa-check-circle mr-1"></i> Process
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center p-4 font-jakarta font-extrabold text-muted">
                                    Tidak ada tiket yang menunggu persetujuan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Active Projects Table (Sorted: Approved Tickets First + Request Date) -->
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212]">
            <div class="d-flex justify-content-between align-items-center border-b-4 border-black pb-3 mb-3">
                <h3 class="font-fredoka font-black text-xl text-black dark:text-white uppercase mb-0">
                    Project & Tiket Aktif Terbaru ⚡
                </h3>
                <a href="{{ route('project-requests.index') }}" class="btn btn-sm !bg-[#FFE600] !text-black border-2 border-black font-fredoka font-black px-3 py-1 rounded-full shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:!bg-[#FF007A] hover:!text-white transition-all">
                    Lihat Semua
                </a>
            </div>
            <div class="table-responsive">
                <table class="table border-3 border-black rounded-2xl w-100 text-sm font-jakarta font-extrabold">
                    <thead>
                        <tr class="bg-[#FFE600] text-black font-fredoka font-black uppercase border-b-3 border-black">
                            <th class="p-3">TICKET #</th>
                            <th class="p-3">NAMA PROYEK</th>
                            <th class="p-3">KLIEN</th>
                            <th class="p-3">TANGGAL REQUEST</th>
                            <th class="p-3">PERSETUJUAN</th>
                            <th class="p-3">STATUS TIKET</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeProjectRequests as $req)
                            <tr>
                                <td class="p-3"><span class="badge bg-black text-white font-fredoka font-black">{{ $req->ticket_number ?? '#'.$req->id }}</span></td>
                                <td class="p-3 font-fredoka font-black text-black dark:text-white">
                                    <a href="{{ route('project-requests.show', $req) }}" class="text-black dark:text-white hover:text-[#FF007A]">
                                        {{ $req->title }}
                                    </a>
                                </td>
                                <td class="p-3 text-xs">{{ $req->client->name ?? '-' }}</td>
                                <td class="p-3 font-mono text-xs">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-3">
                                    @if($req->status === 'approved')
                                        <span class="badge bg-[#00E676] text-black border-2 border-black font-fredoka font-black text-xs px-2 py-1">
                                            Disetujui
                                        </span>
                                    @elseif($req->status === 'submitted')
                                        <span class="badge bg-[#FFE600] text-black border-2 border-black font-fredoka font-black text-xs px-2 py-1">
                                            Menunggu
                                        </span>
                                    @else
                                        <span class="badge bg-[#FF007A] text-white border-2 border-black font-fredoka font-black text-xs px-2 py-1">
                                            {{ ucfirst($req->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <span class="badge bg-[#0055FF] text-white border-2 border-black font-fredoka font-black text-xs px-2 py-1">
                                        {{ $req->ticket_status_label }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center p-4 font-jakarta font-extrabold text-muted">
                                    Belum ada project aktif.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Right Side Widgets -->
    <div class="col-lg-4 mb-4 space-y-6">
        <!-- Role Distribution Card -->
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212]">
            <h3 class="font-fredoka font-black text-xl text-black dark:text-white uppercase border-b-4 border-black pb-3 mb-3">Distribusi Role</h3>
            <div class="font-jakarta font-extrabold text-sm space-y-3">
                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Klien</span>
                        <span class="font-fredoka font-black">60%</span>
                    </div>
                    <div class="progress border-2 border-black rounded-full h-3 overflow-hidden">
                        <div class="progress-bar bg-[#0055FF]" role="progressbar" style="width: 60%"></div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Admin</span>
                        <span class="font-fredoka font-black">20%</span>
                    </div>
                    <div class="progress border-2 border-black rounded-full h-3 overflow-hidden">
                        <div class="progress-bar bg-[#00E676]" role="progressbar" style="width: 20%"></div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Super Admin</span>
                        <span class="font-fredoka font-black">20%</span>
                    </div>
                    <div class="progress border-2 border-black rounded-full h-3 overflow-hidden">
                        <div class="progress-bar bg-[#FF007A]" role="progressbar" style="width: 20%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Queue & SLA Card -->
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212]">
            <h3 class="font-fredoka font-black text-xl text-black dark:text-white uppercase border-b-4 border-black pb-3 mb-3">Snapshot Queue & SLA</h3>
            <div class="font-jakarta font-extrabold text-sm space-y-2">
                <div>
                    <span class="text-xs text-muted uppercase block">Queue Aktif</span>
                    <span class="font-fredoka font-black text-2xl text-black dark:text-white">{{ $activeQueues }}</span>
                </div>
                <div class="border-top-2 border-dashed border-black pt-2">
                    <span class="text-xs text-muted uppercase block">Approval Pending</span>
                    <span class="font-fredoka font-black text-2xl text-[#FF007A]">{{ $pendingApprovalTickets->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
