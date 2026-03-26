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

<div class="d-flex justify-content-between align-items-start align-items-md-center flex-column flex-md-row mb-4 mt-2">
    <div>
        <p class="text-muted mb-0 font-weight-500">Visibilitas menyeluruh untuk user, tiket, queue, dan kualitas layanan.</p>
    </div>
    <div class="d-flex align-items-center mt-3 mt-md-0">
        <a href="{{ route('super-admin.users.index') }}" class="btn btn-primary px-4 shadow-sm" style="border-radius: 0.5rem; font-weight: 500;">
            <i class="fas fa-users-cog mr-2"></i> Kelola User
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="support-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-primary"><i class="fas fa-users"></i></span>
                <small class="text-muted">Sistem</small>
            </div>
            <div class="support-stat-value">{{ number_format($totalUsers) }}</div>
            <div class="support-stat-label">Total User</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="support-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-warning"><i class="fas fa-hourglass-half"></i></span>
                <small class="text-danger">{{ $pendingApprovals }}</small>
            </div>
            <div class="support-stat-value">{{ number_format($totalTickets) }}</div>
            <div class="support-stat-label">Total Tiket</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="support-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-info"><i class="fas fa-layer-group"></i></span>
                <small class="text-info">{{ $activeQueues }}</small>
            </div>
            <div class="support-stat-value">{{ $openTickets }}</div>
            <div class="support-stat-label">Tiket Terbuka</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="support-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-success"><i class="fas fa-check-circle"></i></span>
                <small class="text-success">{{ $resolvedTickets }}</small>
            </div>
            <div class="support-stat-value">{{ $overdueTickets }}</div>
            <div class="support-stat-label">SLA Terlewat</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card support-shell-card mb-3">
            <div class="card-header border-0 d-flex justify-content-between align-items-center bg-white">
                <h3 class="card-title mb-0">Project Aktif Terbaru</h3>
                <a href="{{ route('queues.index') }}" class="btn btn-link btn-sm">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-4 border-bottom-0">Project</th>
                                <th class="d-none d-md-table-cell border-bottom-0">Client</th>
                                <th class="border-bottom-0">Status</th>
                                <th class="pr-4 border-bottom-0">Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(\App\Models\Queue::with('projectRequest.client')->latest()->take(8)->get() as $queue)
                                    <tr>
                                        <td class="pl-4"><a href="{{ route('progress.show', $queue) }}" class="font-weight-600 text-dark">{{ $queue->project_name }}</a></td>
                                        <td class="d-none d-md-table-cell text-muted">{{ $queue->projectRequest->client->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $queue->status == 'Completed' ? 'success' : ($queue->status == 'In Progress' ? 'primary' : 'secondary') }}">
                                                {{ $queue->status }}
                                            </span>
                                        </td>
                                        <td class="pr-4">
                                            <div class="d-flex align-items-center">
                                                <div class="progress progress-xs w-100 mr-2">
                                                    <div class="progress-bar bg-{{ $queue->status == 'Completed' ? 'success' : 'primary' }}" style="width: {{ $queue->progress }}%"></div>
                                                </div>
                                                <small class="font-weight-600">{{ $queue->progress }}%</small>
                                            </div>
                                        </td>
                                    </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada project aktif.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card support-shell-card mb-3">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Distribusi Role</h3>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                @php
                    $roleData = [
                        'Klien' => \App\Models\User::where('role', 'client')->count(),
                        // 'Developer' => \App\Models\User::where('role', 'developer')->count(),
                        'Admin' => \App\Models\User::where('role', 'admin')->count(),
                        'Super Admin' => \App\Models\User::where('role', 'super_admin')->count(),
                    ];
                    $roleColors = ['Klien' => 'primary', 'Admin' => 'success', 'Super Admin' => 'warning'];
                    $roleTotal = max(array_sum($roleData), 1);
                @endphp
                @foreach($roleData as $label => $count)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted font-weight-600">{{ $label }}</small>
                            <small class="font-weight-600">{{ round(($count / $roleTotal) * 100) }}%</small>
                        </div>
                        <div class="progress progress-xs">
                            <div class="progress-bar bg-{{ $roleColors[$label] }}" style="width: {{ round(($count / $roleTotal) * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card support-shell-card mb-4">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Snapshot Queue & SLA</h3>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                <div class="mb-4">
                    <small class="text-muted d-block font-weight-600">Queue Aktif</small>
                    <strong class="text-dark" style="font-size: 1.25rem;">{{ $activeQueues }}</strong>
                    <div class="progress progress-xs mt-2"><div class="progress-bar bg-primary" style="width: {{ $totalQueues > 0 ? round(($activeQueues / $totalQueues) * 100) : 0 }}%"></div></div>
                </div>
                <div class="mb-4">
                    <small class="text-muted d-block font-weight-600">Approval Pending</small>
                    <strong class="text-dark" style="font-size: 1.25rem;">{{ $pendingApprovals }}</strong>
                    <div class="progress progress-xs mt-2"><div class="progress-bar bg-warning" style="width: {{ $totalTickets > 0 ? round(($pendingApprovals / $totalTickets) * 100) : 0 }}%"></div></div>
                </div>
                <div class="mb-1">
                    <small class="text-muted d-block font-weight-600">SLA Terlewat</small>
                    <strong class="text-danger" style="font-size: 1.25rem;">{{ $overdueTickets }}</strong>
                    <div class="progress progress-xs mt-2"><div class="progress-bar bg-danger" style="width: {{ max(min($overdueTickets * 10, 100), 0) }}%"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card support-shell-card mb-4">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Aktivitas Terbaru</h3>
                <a href="{{ route('super-admin.activity-logs') }}" class="btn btn-light btn-sm font-weight-500">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-4 border-bottom-0">Pengguna</th>
                                <th class="border-bottom-0">Aksi</th>
                                <th class="d-none d-md-table-cell border-bottom-0">Deskripsi</th>
                                <th class="pr-4 border-bottom-0 text-right">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(\App\Models\ActivityLog::with('user')->latest()->take(8)->get() as $log)
                                <tr>
                                    <td class="pl-4 font-weight-600 text-dark">{{ $log->user ? $log->user->name : 'Sistem' }}</td>
                                    <td><span class="badge badge-info">{{ $log->action }}</span></td>
                                    <td class="d-none d-md-table-cell text-muted">{{ $log->description }}</td>
                                    <td class="pr-4 text-right text-muted small"><i class="far fa-clock mr-1"></i> {{ $log->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted mb-2"><i class="fas fa-history fa-3x" style="opacity: 0.2;"></i></div>
                                        <p class="mb-0 text-muted">Belum ada log aktivitas.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
