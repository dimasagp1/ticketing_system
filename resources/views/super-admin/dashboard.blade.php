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
                <div>
                    <h3 class="card-title mb-0">Ringkasan Tiket Teknis Harian</h3>
                    <small class="text-muted">Periode {{ $technicalSummary['date_label'] }} (kategori dukungan teknis)</small>
                </div>
                <a href="{{ route('super-admin.reports.technical', ['date' => now()->toDateString()]) }}" class="btn btn-outline-primary btn-sm">Buka Laporan Teknis</a>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-3">
                        <small class="d-block text-muted">Total</small>
                        <span class="h5 font-weight-bold mb-0">{{ $technicalSummary['total'] }}</span>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <small class="d-block text-muted">Terselesaikan</small>
                        <span class="h5 font-weight-bold text-success mb-0">{{ $technicalSummary['resolved'] }}</span>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <small class="d-block text-muted">Antrean Aktif</small>
                        <span class="h5 font-weight-bold text-warning mb-0">{{ $technicalSummary['backlog'] }}</span>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <small class="d-block text-muted">Melewati SLA</small>
                        <span class="h5 font-weight-bold text-danger mb-0">{{ $technicalSummary['overdue'] }}</span>
                    </div>
                </div>
                <div class="row text-center border-top pt-3 mt-1">
                    <div class="col-md-4 col-12 mb-2 mb-md-0">
                        <small class="text-muted d-block">Rata-rata FRT (menit)</small>
                        <strong>{{ number_format($technicalSummary['frt_minutes'], 2) }}</strong>
                    </div>
                    <div class="col-md-4 col-12 mb-2 mb-md-0">
                        <small class="text-muted d-block">Rata-rata MTTR (jam)</small>
                        <strong>{{ number_format($technicalSummary['mttr_hours'], 2) }}</strong>
                    </div>
                    <div class="col-md-4 col-12">
                        <small class="text-muted d-block">Kepatuhan SLA</small>
                        <strong class="text-primary">{{ number_format($technicalSummary['sla_compliance_rate'], 2) }}%</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card support-shell-card mb-3">
            <div class="card-header border-0 bg-white">
                <h3 class="card-title mb-0">Subkategori Teknis Teratas</h3>
            </div>
            <div class="card-body">
                @php
                    $totalTechnical = max($technicalSummary['total'], 1);
                @endphp
                @forelse($technicalSubcategoryBreakdown->take(5) as $item)
                    @php
                        $percentage = (int) round(($item->total / $totalTechnical) * 100);
                    @endphp
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <small>{{ \App\Models\ProjectRequest::technicalSubcategoryLabels()[$item->label] ?? ucfirst(str_replace('_', ' ', $item->label)) }}</small>
                            <small class="font-weight-600">{{ $item->total }} ({{ $percentage }}%)</small>
                        </div>
                        <div class="progress progress-xs mt-1">
                            <div class="progress-bar bg-info" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Belum ada tiket teknis hari ini.</p>
                @endforelse
            </div>
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
                                            {{ strtoupper($ticket->impact_label ?? 'SEDANG') }}
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
                        'Insiden/Bug' => \App\Models\ProjectRequest::whereIn('ticket_category', ['incident', 'bug'])->count(),
                        'Permintaan Layanan' => \App\Models\ProjectRequest::where('ticket_category', 'service_request')->count(),
                        'Akses' => \App\Models\ProjectRequest::where('ticket_category', 'access')->count(),
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
                        <small class="d-block text-muted">Terbuka</small>
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
                    <small class="text-muted d-block">Antrean Aktif</small>
                    <strong>{{ $stats['active_queues'] }}</strong>
                    <div class="progress progress-xs mt-1"><div class="progress-bar bg-primary" style="width: {{ $stats['total_queues'] > 0 ? round(($stats['active_queues'] / $stats['total_queues']) * 100) : 0 }}%"></div></div>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Persetujuan Menunggu</small>
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

<div class="row">
    <div class="col-12">
        <div class="card support-shell-card mb-3">
            <div class="card-header border-0 bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title mb-0">Linimasa Alur Tiket End-to-End</h3>
                    <small class="text-muted">Memetakan seluruh alur dari pengajuan sampai penutupan ({{ $flowRangeLabel }})</small>
                </div>
                <div class="btn-group btn-group-sm" role="group" aria-label="Filter periode linimasa">
                    <a href="{{ request()->fullUrlWithQuery(['flow_range' => '7d']) }}" class="btn {{ $flowRange === '7d' ? 'btn-primary' : 'btn-outline-primary' }}">7H</a>
                    <a href="{{ request()->fullUrlWithQuery(['flow_range' => '30d']) }}" class="btn {{ $flowRange === '30d' ? 'btn-primary' : 'btn-outline-primary' }}">30H</a>
                    <a href="{{ request()->fullUrlWithQuery(['flow_range' => '90d']) }}" class="btn {{ $flowRange === '90d' ? 'btn-primary' : 'btn-outline-primary' }}">90H</a>
                    <a href="{{ request()->fullUrlWithQuery(['flow_range' => 'all']) }}" class="btn {{ $flowRange === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">ALL</a>
                </div>
            </div>
            <div class="card-body">
                @php
                    $totalFlowTickets = max($flowTotalTickets, 1);
                    $doneTickets = $flowCounts['resolved'] + $flowCounts['closed'];
                    $activeTickets = $flowCounts['open'] + $flowCounts['in_progress'] + $flowCounts['pending_user'] + $flowCounts['paused'];
                    $approvalStageTickets = $flowCounts['submitted'] + $flowCounts['under_review'] + $flowCounts['revision_requested'];
                    $blockedOutcomeTickets = $flowCounts['rejected'] + $flowCounts['cancelled'];

                    $donePercent = (int) round(($doneTickets / $totalFlowTickets) * 100);
                    $activePercent = (int) round(($activeTickets / $totalFlowTickets) * 100);
                    $approvalPercent = (int) round(($approvalStageTickets / $totalFlowTickets) * 100);
                    $blockedPercent = (int) round(($blockedOutcomeTickets / $totalFlowTickets) * 100);

                    $mainFlow = [
                        ['label' => '1. Diajukan', 'icon' => 'fas fa-paper-plane', 'badge' => 'warning', 'count' => $flowCounts['submitted'], 'desc' => 'Tiket baru dikirim oleh client.'],
                        ['label' => '2. Ditinjau', 'icon' => 'fas fa-search', 'badge' => 'info', 'count' => $flowCounts['under_review'], 'desc' => 'Admin menilai kebutuhan dan prioritas.'],
                        ['label' => '3. Revisi Diminta', 'icon' => 'fas fa-pen', 'badge' => 'primary', 'count' => $flowCounts['revision_requested'], 'desc' => 'Menunggu perbaikan data dari pemohon.'],
                        ['label' => '4. Disetujui', 'icon' => 'fas fa-check-circle', 'badge' => 'success', 'count' => $flowCounts['approved'], 'desc' => 'Approval selesai, siap masuk operasional.'],
                        ['label' => '5. Terbuka (Antrean)', 'icon' => 'fas fa-inbox', 'badge' => 'primary', 'count' => $flowCounts['open'], 'desc' => 'Masuk antrean kerja.'],
                        ['label' => '6. Diproses', 'icon' => 'fas fa-cogs', 'badge' => 'info', 'count' => $flowCounts['in_progress'], 'desc' => 'Tim mengerjakan tiket.'],
                        ['label' => '7. Menunggu User', 'icon' => 'fas fa-user-clock', 'badge' => 'warning', 'count' => $flowCounts['pending_user'], 'desc' => 'Menunggu feedback/konfirmasi user.'],
                        ['label' => '8. Dijeda', 'icon' => 'fas fa-pause-circle', 'badge' => 'dark', 'count' => $flowCounts['paused'], 'desc' => 'Pengerjaan dihentikan sementara.'],
                        ['label' => '9. Terselesaikan', 'icon' => 'fas fa-flag-checkered', 'badge' => 'success', 'count' => $flowCounts['resolved'], 'desc' => 'Pekerjaan selesai.'],
                        ['label' => '10. Ditutup', 'icon' => 'fas fa-lock', 'badge' => 'secondary', 'count' => $flowCounts['closed'], 'desc' => 'Tiket ditutup menjadi arsip.'],
                    ];

                    $outcomeFlow = [
                        ['label' => 'Ditolak', 'icon' => 'fas fa-times-circle', 'badge' => 'danger', 'count' => $flowCounts['rejected'], 'desc' => 'Outcome dari tahap review/approval.'],
                        ['label' => 'Dibatalkan', 'icon' => 'fas fa-ban', 'badge' => 'dark', 'count' => $flowCounts['cancelled'], 'desc' => 'Outcome dari tahap operasional.'],
                    ];
                @endphp

                <div class="row mb-3">
                    <div class="col-lg-3 col-sm-6 mb-2 mb-lg-0">
                        <div class="p-2 border rounded bg-light h-100">
                            <small class="text-muted d-block">Tingkat Selesai</small>
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>{{ $donePercent }}%</strong>
                                <small class="text-success">{{ $doneTickets }} tiket</small>
                            </div>
                            <div class="progress progress-xs mt-2">
                                <div class="progress-bar bg-success" style="width: {{ $donePercent }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-2 mb-lg-0">
                        <div class="p-2 border rounded bg-light h-100">
                            <small class="text-muted d-block">Dalam Proses</small>
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>{{ $activePercent }}%</strong>
                                <small class="text-info">{{ $activeTickets }} tiket</small>
                            </div>
                            <div class="progress progress-xs mt-2">
                                <div class="progress-bar bg-info" style="width: {{ $activePercent }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-2 mb-sm-0">
                        <div class="p-2 border rounded bg-light h-100">
                            <small class="text-muted d-block">Tahap Approval</small>
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>{{ $approvalPercent }}%</strong>
                                <small class="text-warning">{{ $approvalStageTickets }} tiket</small>
                            </div>
                            <div class="progress progress-xs mt-2">
                                <div class="progress-bar bg-warning" style="width: {{ $approvalPercent }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="p-2 border rounded bg-light h-100">
                            <small class="text-muted d-block">Outcome Non-Lanjut</small>
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>{{ $blockedPercent }}%</strong>
                                <small class="text-danger">{{ $blockedOutcomeTickets }} tiket</small>
                            </div>
                            <div class="progress progress-xs mt-2">
                                <div class="progress-bar bg-danger" style="width: {{ $blockedPercent }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    .flow-scroll {
                        overflow-x: auto;
                        padding-bottom: 0.25rem;
                    }

                    .flow-track {
                        display: flex;
                        align-items: stretch;
                        min-width: 1080px;
                    }

                    .flow-step {
                        width: 220px;
                        min-width: 220px;
                        border: 1px solid #e2e8f0;
                        border-radius: 0.7rem;
                        background: #f8fafc;
                        padding: 0.8rem 0.85rem;
                    }

                    .flow-step-head {
                        display: flex;
                        justify-content: space-between;
                        align-items: flex-start;
                        gap: 0.4rem;
                        margin-bottom: 0.35rem;
                    }

                    .flow-step-title {
                        font-weight: 600;
                        color: #1f2d3d;
                        font-size: 0.88rem;
                        line-height: 1.2;
                    }

                    .flow-step-desc {
                        color: #64748b;
                        font-size: 0.77rem;
                        line-height: 1.35;
                        margin-bottom: 0;
                    }

                    .flow-connector {
                        width: 34px;
                        min-width: 34px;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        color: #94a3b8;
                        font-size: 0.92rem;
                    }

                    .flow-outcome-track {
                        display: grid;
                        grid-template-columns: repeat(2, minmax(220px, 1fr));
                        gap: 0.75rem;
                    }

                    @media (max-width: 768px) {
                        .flow-step {
                            width: 200px;
                            min-width: 200px;
                        }

                        .flow-track {
                            min-width: 930px;
                        }

                        .flow-outcome-track {
                            grid-template-columns: 1fr;
                        }
                    }
                </style>

                <div class="mb-3">
                    <small class="text-uppercase text-muted font-weight-600">Alur Utama</small>
                </div>

                <div class="flow-scroll mb-4">
                    <div class="flow-track">
                        @foreach($mainFlow as $index => $stage)
                            <div class="flow-step">
                                <div class="flow-step-head">
                                    <div class="flow-step-title">
                                        <i class="{{ $stage['icon'] }} text-{{ $stage['badge'] }} mr-1"></i>{{ $stage['label'] }}
                                    </div>
                                    <span class="badge badge-{{ $stage['badge'] }}">{{ $stage['count'] }}</span>
                                </div>
                                <p class="flow-step-desc">{{ $stage['desc'] }}</p>
                            </div>

                            @if($index < count($mainFlow) - 1)
                                <div class="flow-connector"><i class="fas fa-arrow-right"></i></div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="mb-2">
                    <small class="text-uppercase text-muted font-weight-600">Outcome Cabang</small>
                </div>

                <div class="flow-outcome-track">
                    @foreach($outcomeFlow as $stage)
                        <div class="flow-step">
                            <div class="flow-step-head">
                                <div class="flow-step-title">
                                    <i class="{{ $stage['icon'] }} text-{{ $stage['badge'] }} mr-1"></i>{{ $stage['label'] }}
                                </div>
                                <span class="badge badge-{{ $stage['badge'] }}">{{ $stage['count'] }}</span>
                            </div>
                            <p class="flow-step-desc">{{ $stage['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
