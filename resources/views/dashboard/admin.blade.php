@php
    $totalTickets = \App\Models\ProjectRequest::count();
    $openTickets = \App\Models\ProjectRequest::where('ticket_status', 'open')->count();
    $inProgressTickets = \App\Models\ProjectRequest::where('ticket_status', 'in_progress')->count();
    $pendingUserTickets = \App\Models\ProjectRequest::where('ticket_status', 'pending_user')->count();
    $resolvedTickets = \App\Models\ProjectRequest::where('ticket_status', 'resolved')->count();
    $overdueTickets = \App\Models\ProjectRequest::whereIn('ticket_status', ['open', 'in_progress', 'pending_user'])
        ->whereNotNull('sla_resolution_due_at')
        ->where('sla_resolution_due_at', '<', now())
        ->count();
    $pendingApprovals = \App\Models\ProjectApproval::pending()->count();
    $activeQueues = \App\Models\Queue::where('status', 'In Progress')->count();
    $totalQueues = \App\Models\Queue::count();
    $totalWork = max($inProgressTickets + $pendingUserTickets + $openTickets + $overdueTickets, 1);
@endphp

<div class="d-flex justify-content-between align-items-start align-items-md-center flex-column flex-md-row mb-4 mt-2">
    <div>
        <p class="text-muted mb-0 font-weight-500">Pantau tiket masuk, SLA, dan performa antrian tim.</p>
    </div>
    <div class="d-flex align-items-center mt-3 mt-md-0">
        <a href="{{ route('approvals.index') }}" class="btn btn-primary px-4 shadow-sm" style="border-radius: 0.5rem; font-weight: 500;">
            <i class="fas fa-check-circle mr-2"></i> Tinjau Approval
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="support-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-primary"><i class="fas fa-ticket-alt"></i></span>
                <small class="text-success">+{{ $totalTickets > 0 ? round(($openTickets / $totalTickets) * 100) : 0 }}%</small>
            </div>
            <div class="support-stat-value">{{ number_format($totalTickets) }}</div>
            <div class="support-stat-label">Total Tiket</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="support-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-warning"><i class="fas fa-hourglass-half"></i></span>
                <small class="text-danger">{{ $pendingApprovals }}</small>
            </div>
            <div class="support-stat-value">{{ $openTickets }}</div>
            <div class="support-stat-label">Tiket Terbuka</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="support-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-info"><i class="fas fa-tools"></i></span>
                <small class="text-info">{{ $inProgressTickets }}</small>
            </div>
            <div class="support-stat-value">{{ $pendingUserTickets }}</div>
            <div class="support-stat-label">Menunggu User</div>
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
                <h3 class="card-title mb-0">Tiket Terbaru</h3>
                <a href="{{ route('project-requests.index') }}" class="btn btn-link btn-sm">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-4 border-bottom-0">ID</th>
                                <th class="border-bottom-0">Subjek</th>
                                <th class="d-none d-md-table-cell border-bottom-0">Client</th>
                                <th class="border-bottom-0">Prioritas</th>
                                <th class="border-bottom-0">Status</th>
                                <th class="pr-4 d-none d-lg-table-cell border-bottom-0">Diperbarui</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(\App\Models\ProjectRequest::with('client')->latest()->take(8)->get() as $ticket)
                                <tr>
                                    <td class="pl-4"><strong>{{ $ticket->ticket_number ?? ('#' . $ticket->id) }}</strong></td>
                                    <td><a href="{{ route('project-requests.show', $ticket) }}" class="font-weight-600 text-dark">{{ \Illuminate\Support\Str::limit($ticket->project_name, 32) }}</a></td>
                                    <td class="d-none d-md-table-cell text-muted">{{ $ticket->client?->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ ($ticket->impact === 'critical' || $ticket->urgency === 'critical' || $ticket->impact === 'high' || $ticket->urgency === 'high') ? 'danger' : (($ticket->impact === 'medium' || $ticket->urgency === 'medium') ? 'warning' : 'secondary') }}">
                                            {{ strtoupper($ticket->impact ?? 'MED') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $ticket->ticket_status_badge_class }}">{{ $ticket->ticket_status_label }}</span>
                                    </td>
                                    <td class="pr-4 d-none d-lg-table-cell text-muted small">{{ $ticket->updated_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada tiket terbaru.</td>
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
            <div class="card-header border-0 bg-white">
                <h3 class="card-title mb-0">Distribusi Kategori</h3>
            </div>
            <div class="card-body">
                @php
                    $categoryTotal = max(\App\Models\ProjectRequest::count(), 1);
                    $categoryData = [
                        'Incident/Bug' => \App\Models\ProjectRequest::whereIn('ticket_category', ['incident', 'bug'])->count(),
                        'Service Request' => \App\Models\ProjectRequest::where('ticket_category', 'service_request')->count(),
                        'Access' => \App\Models\ProjectRequest::where('ticket_category', 'access')->count(),
                        'Lainnya' => \App\Models\ProjectRequest::where('ticket_category', 'other')->count(),
                    ];
                @endphp
                @foreach($categoryData as $label => $count)
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <small>{{ $label }}</small>
                            <small>{{ round(($count / $categoryTotal) * 100) }}%</small>
                        </div>
                        <div class="progress progress-xs">
                            <div class="progress-bar bg-primary" style="width: {{ round(($count / $categoryTotal) * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card support-shell-card mb-3">
            <div class="card-header border-0 bg-white">
                <h3 class="card-title mb-0">Distribusi Beban Kerja</h3>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-2">
                        <small class="d-block text-muted">Diproses</small>
                        <span class="font-weight-bold">{{ round(($inProgressTickets / $totalWork) * 100) }}%</span>
                        <div class="progress progress-xs mt-1"><div class="progress-bar bg-primary" style="width: {{ round(($inProgressTickets / $totalWork) * 100) }}%"></div></div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <small class="d-block text-muted">Unqueued/Open</small>
                        <span class="font-weight-bold">{{ round(($openTickets / $totalWork) * 100) }}%</span>
                        <div class="progress progress-xs mt-1"><div class="progress-bar bg-warning" style="width: {{ round(($openTickets / $totalWork) * 100) }}%"></div></div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <small class="d-block text-muted">Menunggu User</small>
                        <span class="font-weight-bold">{{ round(($pendingUserTickets / $totalWork) * 100) }}%</span>
                        <div class="progress progress-xs mt-1"><div class="progress-bar bg-info" style="width: {{ round(($pendingUserTickets / $totalWork) * 100) }}%"></div></div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <small class="d-block text-muted">Escalated</small>
                        <span class="font-weight-bold text-danger">{{ round(($overdueTickets / $totalWork) * 100) }}%</span>
                        <div class="progress progress-xs mt-1"><div class="progress-bar bg-danger" style="width: {{ round(($overdueTickets / $totalWork) * 100) }}%"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card support-shell-card mb-3">
            <div class="card-header border-0 bg-white">
                <h3 class="card-title mb-0">Performa Tim</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <tbody>
                            @forelse(\App\Models\User::whereIn('role', ['admin', 'super_admin'])->get() as $agent)
                                @php
                                    $agentActive = \App\Models\Queue::where('assigned_to', $agent->id)->whereIn('status', ['Assigned', 'In Progress'])->count();
                                    $agentSolved = \App\Models\Queue::where('assigned_to', $agent->id)->where('status', 'Completed')->count();
                                @endphp
                                <tr>
                                    <td class="pl-4">
                                        <strong class="text-dark">{{ $agent->name }}</strong><br>
                                        <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $agent->role)) }}</small>
                                    </td>
                                    <td class="pr-4 text-right">
                                        <small class="text-muted d-block">{{ $agentSolved }} selesai</small>
                                        <small class="text-warning font-weight-600">{{ $agentActive }} aktif</small>
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="text-center py-4 text-muted">Belum ada data performa.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-danger shadow-sm">
            <div class="card-header border-0">
                <h3 class="card-title mb-0">Antrian Terlama</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-4 border-bottom-0">Ticket</th>
                                <th class="border-bottom-0">Client</th>
                                <th class="border-bottom-0">Status</th>
                                <th class="pr-4 border-bottom-0 text-right">Wait Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(\App\Models\ProjectRequest::with('client')->whereIn('ticket_status', ['open', 'in_progress', 'pending_user'])->whereNotNull('sla_resolution_due_at')->orderBy('sla_resolution_due_at')->take(6)->get() as $ticket)
                                <tr>
                                    <td class="pl-4"><a href="{{ route('project-requests.show', $ticket) }}" class="font-weight-600 text-dark">{{ $ticket->ticket_number ?? ('#' . $ticket->id) }}</a></td>
                                    <td>{{ $ticket->client?->name ?? '-' }}</td>
                                    <td><span class="badge badge-{{ $ticket->ticket_status_badge_class }}">{{ $ticket->ticket_status_label }}</span></td>
                                    <td class="pr-4 text-right"><span class="{{ $ticket->sla_resolution_due_at->isPast() ? 'text-danger font-weight-bold' : 'text-muted small' }}"><i class="far fa-clock mr-1"></i> {{ $ticket->created_at->diffForHumans() }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Tidak ada tiket dalam watchlist.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-info shadow-sm">
            <div class="card-header border-0">
                <h3 class="card-title mb-0">Ringkasan Antrian & Status</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Queue Aktif</small>
                    <strong>{{ $activeQueues }}</strong>
                    <div class="progress progress-xs mt-1"><div class="progress-bar bg-primary" style="width: {{ $totalQueues > 0 ? round(($activeQueues / $totalQueues) * 100) : 0 }}%"></div></div>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Approval Pending</small>
                    <strong>{{ $pendingApprovals }}</strong>
                    <div class="progress progress-xs mt-1"><div class="progress-bar bg-warning" style="width: {{ $totalTickets > 0 ? round(($pendingApprovals / $totalTickets) * 100) : 0 }}%"></div></div>
                </div>
                <div class="mb-1">
                    <small class="text-muted d-block">SLA Terlewat</small>
                    <strong class="text-danger">{{ $overdueTickets }}</strong>
                    <div class="progress progress-xs mt-1"><div class="progress-bar bg-danger" style="width: {{ max(min($overdueTickets * 10, 100), 0) }}%"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>
