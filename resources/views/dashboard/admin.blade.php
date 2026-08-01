@php
    $totalTickets = \App\Models\ProjectRequest::count();
    $openTickets = \App\Models\ProjectRequest::where('ticket_status', 'open')->count();
    $inProgressTickets = \App\Models\ProjectRequest::where('ticket_status', 'in_progress')->count();
    $pendingUserTickets = \App\Models\ProjectRequest::where('ticket_status', 'pending_user')->count();
    $resolvedTickets = \App\Models\ProjectRequest::where('ticket_status', 'resolved')->count();
    $overdueTickets = \App\Models\ProjectRequest::whereIn('ticket_status', \App\Models\ProjectRequest::slaTrackedTicketStatuses())
        ->whereNotNull('sla_resolution_due_at')
        ->where('sla_resolution_due_at', '<', now())
        ->count();
    $activeQueues = \App\Models\Queue::where('status', 'In Progress')->count();
    $totalQueues = \App\Models\Queue::count();
@endphp

<!-- ACC Ticketing Alert Banner -->
<x-acc-alert :count="$pendingApprovalTickets->count()" :route="route('approvals.index')" />

<!-- Stat Cards Row -->
<div class="row">
    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100 flex flex-col justify-between">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-[#FFE600] text-black border-2 border-black font-fredoka font-black px-3 py-1 rounded-full">
                    <i class="fas fa-ticket-alt mr-1"></i> TOTAL
                </span>
                <small class="font-fredoka font-black text-[#0055FF]">Sistem</small>
            </div>
            <div class="font-fredoka font-black text-4xl text-black dark:text-white my-2">{{ number_format($totalTickets) }}</div>
            <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Total Tiket Masuk</div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100 flex flex-col justify-between">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-[#FF007A] text-white border-2 border-black font-fredoka font-black px-3 py-1 rounded-full">
                    <i class="fas fa-hourglass-half mr-1"></i> PENDING
                </span>
                <small class="font-fredoka font-black text-[#FF007A]">{{ $pendingApprovalTickets->count() }} Approval</small>
            </div>
            <div class="font-fredoka font-black text-4xl text-black dark:text-white my-2">{{ $openTickets }}</div>
            <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Tiket Terbuka</div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100 flex flex-col justify-between">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-[#0055FF] text-white border-2 border-black font-fredoka font-black px-3 py-1 rounded-full">
                    <i class="fas fa-tools mr-1"></i> WORKFLOW
                </span>
                <small class="font-fredoka font-black text-[#0055FF]">{{ $inProgressTickets }} Diproses</small>
            </div>
            <div class="font-fredoka font-black text-4xl text-black dark:text-white my-2">{{ $pendingUserTickets }}</div>
            <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Menunggu Response User</div>
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
        
        <!-- 1. Tiket Menunggu Persetujuan (Pending Approvals) -->
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
        <!-- Team Performance Card -->
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212]">
            <h3 class="font-fredoka font-black text-xl text-black dark:text-white uppercase border-b-4 border-black dark:border-white pb-3 mb-3">Performa Tim IT</h3>
            <div class="space-y-3 font-jakarta font-extrabold text-sm">
                @foreach(\App\Models\User::whereIn('role', ['admin', 'super_admin', 'developer'])->take(5)->get() as $staff)
                    <div class="d-flex justify-content-between align-items-center p-3 bg-[#FFFBEA] dark:bg-[#1f2937] border-2 border-black dark:border-white rounded-2xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_#FFE600]">
                        <div>
                            <span class="font-fredoka font-black text-black dark:text-white block leading-tight text-base">{{ $staff->name }}</span>
                            <small class="font-jakarta font-bold text-gray-700 dark:text-gray-300 uppercase text-[11px] block mt-0.5">{{ str_replace('_', ' ', $staff->role) }}</small>
                        </div>
                        <span class="badge !bg-[#00E676] !text-black border-2 border-black font-fredoka font-black px-2.5 py-1 text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            Aktif
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- SLA Snapshot Card -->
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212]">
            <h3 class="font-fredoka font-black text-xl text-black dark:text-white uppercase border-b-4 border-black dark:border-white pb-3 mb-3">Snapshot Queue & SLA</h3>
            <div class="font-jakarta font-extrabold text-sm space-y-2">
                <div>
                    <span class="text-xs text-gray-600 dark:text-gray-300 font-bold uppercase block">Queue Aktif</span>
                    <span class="font-fredoka font-black text-2xl text-black dark:text-white">{{ $activeQueues }}</span>
                </div>
                <div class="border-top-2 border-dashed border-black dark:border-white pt-2">
                    <span class="text-xs text-gray-600 dark:text-gray-300 font-bold uppercase block">Approval Pending</span>
                    <span class="font-fredoka font-black text-2xl text-[#FF007A]">{{ $pendingApprovalTickets->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
