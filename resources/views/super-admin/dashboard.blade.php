@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item active">Super Admin</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-start align-items-md-center flex-column flex-md-row mb-4 mt-2">
    <div>
        <h3 class="mb-1 font-weight-bold text-dark">Ringkasan Dukungan</h3>
        <p class="text-muted mb-0 font-weight-500">Selamat datang kembali, {{ auth()->user()->name }}. Berikut kondisi operasional hari ini.</p>
    </div>
    <a href="{{ route('project-requests.create') }}" class="btn btn-primary px-4 shadow-sm mt-3 mt-md-0 d-flex align-items-center" style="border-radius: 0.5rem; font-weight: 500;">
        <i class="fas fa-plus mr-2"></i> Tiket Baru
    </a>
</div>

<div class="row">
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="support-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-primary"><i class="fas fa-ticket-alt"></i></span>
                <small class="text-success">+{{ $stats['open_tickets'] > 0 ? round(($stats['open_tickets'] / max($stats['total_requests'], 1)) * 100) : 0 }}%</small>
            </div>
            <div class="support-stat-value">{{ number_format($stats['total_requests']) }}</div>
            <div class="support-stat-label">Total Tiket</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="support-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-warning"><i class="fas fa-folder-open"></i></span>
                <small class="text-danger">-{{ $stats['overdue_tickets'] }}</small>
            </div>
            <div class="support-stat-value">{{ $stats['open_tickets'] }}</div>
            <div class="support-stat-label">Tiket Terbuka</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="support-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-info"><i class="fas fa-spinner"></i></span>
                <small class="text-success">{{ $stats['in_progress_tickets'] }}</small>
            </div>
            <div class="support-stat-value">{{ $stats['pending_user_tickets'] }}</div>
            <div class="support-stat-label">Menunggu User</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="support-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-success"><i class="fas fa-check-circle"></i></span>
                <small class="text-success">+{{ $stats['due_today_tickets'] }}</small>
            </div>
            <div class="support-stat-value">{{ $stats['resolved_tickets'] }}</div>
            <div class="support-stat-label">Selesai Hari Ini</div>
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
                                <th class="border-bottom-0">Pemohon</th>
                                <th class="border-bottom-0">Prioritas</th>
                                <th class="border-bottom-0">Status</th>
                                <th class="pr-4 border-bottom-0">Diperbarui</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(\App\Models\ProjectRequest::with('client')->latest()->take(8)->get() as $ticket)
                                <tr>
                                    <td class="pl-4"><strong>{{ $ticket->ticket_number ?? ('#' . $ticket->id) }}</strong></td>
                                    <td>
                                        <a href="{{ route('project-requests.show', $ticket) }}" class="font-weight-600 text-dark">{{ \Illuminate\Support\Str::limit($ticket->project_name, 32) }}</a>
                                    </td>
                                    <td class="text-muted">{{ $ticket->client?->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ ($ticket->impact === 'critical' || $ticket->urgency === 'critical' || $ticket->impact === 'high' || $ticket->urgency === 'high') ? 'danger' : (($ticket->impact === 'medium' || $ticket->urgency === 'medium') ? 'warning' : 'secondary') }}">
                                            {{ strtoupper($ticket->impact ?? 'MED') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $ticket->ticket_status_badge_class }}">{{ $ticket->ticket_status_label }}</span>
                                    </td>
                                    <td class="pr-4 text-muted small">{{ $ticket->updated_at->diffForHumans() }}</td>
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
                    $totalCategory = max(\App\Models\ProjectRequest::count(), 1);
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
                            <small>{{ round(($count / $totalCategory) * 100) }}%</small>
                        </div>
                        <div class="progress progress-xs">
                            <div class="progress-bar bg-primary" style="width: {{ round(($count / $totalCategory) * 100) }}%"></div>
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
                @php
                    $inProgress = $stats['in_progress_tickets'];
                    $pendingUser = $stats['pending_user_tickets'];
                    $open = $stats['open_tickets'];
                    $escalated = $stats['overdue_tickets'];
                    $totalWork = max($inProgress + $pendingUser + $open + $escalated, 1);
                @endphp
                <div class="row text-center">
                    <div class="col-md-3 mb-2">
                        <small class="d-block text-muted">Diproses</small>
                        <span class="font-weight-bold">{{ round(($inProgress / $totalWork) * 100) }}%</span>
                        <div class="progress progress-xs mt-1"><div class="progress-bar bg-primary" style="width: {{ round(($inProgress / $totalWork) * 100) }}%"></div></div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <small class="d-block text-muted">Open</small>
                        <span class="font-weight-bold">{{ round(($open / $totalWork) * 100) }}%</span>
                        <div class="progress progress-xs mt-1"><div class="progress-bar bg-warning" style="width: {{ round(($open / $totalWork) * 100) }}%"></div></div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <small class="d-block text-muted">Menunggu User</small>
                        <span class="font-weight-bold">{{ round(($pendingUser / $totalWork) * 100) }}%</span>
                        <div class="progress progress-xs mt-1"><div class="progress-bar bg-info" style="width: {{ round(($pendingUser / $totalWork) * 100) }}%"></div></div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <small class="d-block text-muted">Escalated</small>
                        <span class="font-weight-bold text-danger">{{ round(($escalated / $totalWork) * 100) }}%</span>
                        <div class="progress progress-xs mt-1"><div class="progress-bar bg-danger" style="width: {{ round(($escalated / $totalWork) * 100) }}%"></div></div>
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
                            @forelse($supportAgents as $agent)
                                <tr>
                                    <td class="pl-4">
                                        <strong>{{ $agent->name }}</strong><br>
                                        <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $agent->role)) }}</small>
                                    </td>
                                    <td class="pr-4 text-right">
                                        <small class="text-muted d-block">{{ $agent->completed_queues }} selesai</small>
                                        <small class="text-warning font-weight-600">{{ $agent->active_queues }} aktif</small>
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
                                <th class="pl-4 border-bottom-0">Tiket</th>
                                <th class="border-bottom-0">Client</th>
                                <th class="border-bottom-0">Status</th>
                                <th class="pr-4 border-bottom-0">Waktu Tunggu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($slaWatchlist as $ticket)
                                <tr>
                                    <td class="pl-4"><a href="{{ route('project-requests.show', $ticket) }}" class="font-weight-600 text-dark">{{ $ticket->ticket_number ?? ('#' . $ticket->id) }}</a></td>
                                    <td>{{ $ticket->client?->name ?? '-' }}</td>
                                    <td><span class="badge badge-{{ $ticket->ticket_status_badge_class }}">{{ $ticket->ticket_status_label }}</span></td>
                                    <td class="pr-4">
                                        @if($ticket->sla_resolution_due_at)
                                            <span class="{{ $ticket->sla_resolution_due_at->isPast() ? 'text-danger font-weight-bold' : 'text-muted small' }}">
                                                <i class="far fa-clock mr-1"></i> {{ $ticket->created_at->diffForHumans() }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
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
                    <strong>{{ $stats['active_queues'] }}</strong>
                    <div class="progress progress-xs mt-1"><div class="progress-bar bg-primary" style="width: {{ $stats['total_queues'] > 0 ? round(($stats['active_queues'] / $stats['total_queues']) * 100) : 0 }}%"></div></div>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Approval Pending</small>
                    <strong>{{ $stats['pending_requests'] }}</strong>
                    <div class="progress progress-xs mt-1"><div class="progress-bar bg-warning" style="width: {{ $stats['total_requests'] > 0 ? round(($stats['pending_requests'] / $stats['total_requests']) * 100) : 0 }}%"></div></div>
                </div>
                <div class="mb-1">
                    <small class="text-muted d-block">SLA Terlewat</small>
                    <strong class="text-danger">{{ $stats['overdue_tickets'] }}</strong>
                    <div class="progress progress-xs mt-1"><div class="progress-bar bg-danger" style="width: {{ max(min($stats['overdue_tickets'] * 10, 100), 0) }}%"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
