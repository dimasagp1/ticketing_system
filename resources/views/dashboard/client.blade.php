@php
    $requests = auth()->user()->projectRequests();
    $totalRequests = $requests->count();
    $approvedRequests = auth()->user()->projectRequests()->where('status', 'approved')->count();
    $pendingRequests = auth()->user()->projectRequests()->whereIn('status', ['draft', 'submitted', 'revision_requested'])->count();
    $activeTickets = auth()->user()->projectRequests()->whereIn('ticket_status', ['open', 'in_progress', 'pending_user'])->count();
    $activeChats = auth()->user()->clientConversations()->where('status', 'active')->count();
    $requestBase = max($totalRequests, 1);
@endphp

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <p class="text-muted mb-0 font-weight-500">Pantau tiket, progres antrian, dan komunikasi dukungan Anda.</p>
    </div>
    <a href="{{ route('project-requests.create') }}" class="btn btn-primary px-4 shadow-sm" style="border-radius: 0.5rem; font-weight: 500;">
        <i class="fas fa-plus mr-2"></i> Tiket Baru
    </a>
</div>

<div class="row">
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="support-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-primary"><i class="fas fa-ticket-alt"></i></span>
                <small class="text-muted">All</small>
            </div>
            <div class="support-stat-value">{{ $totalRequests }}</div>
            <div class="support-stat-label">Total Permintaan</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="support-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-success"><i class="fas fa-check-circle"></i></span>
                <small class="text-success">Disetujui</small>
            </div>
            <div class="support-stat-value">{{ $approvedRequests }}</div>
            <div class="support-stat-label">Disetujui</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="support-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-warning"><i class="fas fa-hourglass-half"></i></span>
                <small class="text-warning">Menunggu</small>
            </div>
            <div class="support-stat-value">{{ $pendingRequests }}</div>
            <div class="support-stat-label">Pending</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="support-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-info"><i class="fas fa-comments"></i></span>
                <small class="text-info">Aktif</small>
            </div>
            <div class="support-stat-value">{{ $activeChats }}</div>
            <div class="support-stat-label">Chat Aktif</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card support-shell-card mb-3">
            <div class="card-header border-0 d-flex justify-content-between align-items-center bg-white">
                <h3 class="card-title mb-0">Progres Tiket Saya</h3>
                <a href="{{ route('project-requests.index') }}" class="btn btn-link btn-sm">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-4 border-bottom-0">Tiket</th>
                                <th class="border-bottom-0">Project</th>
                                <th class="border-bottom-0">Status</th>
                                <th class="border-bottom-0">Progress</th>
                                <th class="border-bottom-0">Estimasi Selesai</th>
                                <th class="pr-4 border-bottom-0 text-right">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(auth()->user()->projectRequests()->whereNotNull('queue_id')->with('queue')->latest()->take(8)->get() as $req)
                                @if($req->queue)
                                    <tr>
                                        <td class="pl-4"><strong class="text-dark">{{ $req->ticket_number ?? ('#' . $req->id) }}</strong></td>
                                        <td class="font-weight-600 text-dark">{{ $req->project_name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $req->queue->status === 'Completed' ? 'success' : ($req->queue->status === 'In Progress' ? 'primary' : ($req->queue->status === 'Pending' ? 'warning' : 'secondary')) }}">
                                                {{ $req->queue->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress progress-xs w-100 mr-2">
                                                    <div class="progress-bar bg-{{ $req->queue->status === 'Completed' ? 'success' : 'primary' }}" style="width: {{ $req->queue->progress }}%"></div>
                                                </div>
                                                <small class="font-weight-600">{{ $req->queue->progress }}%</small>
                                            </div>
                                        </td>
                                        <td class="text-muted small">
                                            @if($req->queue->deadline)
                                                <i class="far fa-clock mr-1"></i> {{ $req->queue->deadline->diffForHumans() }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="pr-4 text-right">
                                            <a href="{{ route('progress.show', $req->queue) }}" class="btn btn-sm btn-light text-info" title="Lihat Progress"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Belum ada project aktif.</td>
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
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Ringkasan Permintaan</h3>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1"><small class="text-muted font-weight-600">Disetujui</small><small class="font-weight-600">{{ round(($approvedRequests / $requestBase) * 100) }}%</small></div>
                    <div class="progress progress-xs"><div class="progress-bar bg-success" style="width: {{ round(($approvedRequests / $requestBase) * 100) }}%"></div></div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1"><small class="text-muted font-weight-600">Pending</small><small class="font-weight-600">{{ round(($pendingRequests / $requestBase) * 100) }}%</small></div>
                    <div class="progress progress-xs"><div class="progress-bar bg-warning" style="width: {{ round(($pendingRequests / $requestBase) * 100) }}%"></div></div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1"><small class="text-muted font-weight-600">Tiket Aktif</small><small class="font-weight-600">{{ round(($activeTickets / $requestBase) * 100) }}%</small></div>
                    <div class="progress progress-xs"><div class="progress-bar bg-primary" style="width: {{ round(($activeTickets / $requestBase) * 100) }}%"></div></div>
                </div>
                <div class="mb-0">
                    <div class="d-flex justify-content-between mb-1"><small class="text-muted font-weight-600">Aktivitas Chat</small><small class="font-weight-600">{{ $activeChats }}</small></div>
                    <div class="progress progress-xs"><div class="progress-bar bg-info" style="width: {{ max(min($activeChats * 10, 100), 0) }}%"></div></div>
                </div>
            </div>
        </div>

        <div class="card support-shell-card mb-3">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Aksi Cepat</h3>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                <a href="{{ route('project-requests.create') }}" class="btn btn-light btn-block mb-2 font-weight-500 text-left px-3"><i class="fas fa-plus text-primary mr-2" style="width: 20px;"></i> Buat Tiket Baru</a>
                <a href="{{ route('chat.index') }}" class="btn btn-light btn-block font-weight-500 text-left px-3"><i class="fas fa-comments text-info mr-2" style="width: 20px;"></i> Buka Ruang Chat</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card support-shell-card mb-4">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Permintaan Terbaru</h3>
                <a href="{{ route('project-requests.index') }}" class="btn btn-light btn-sm font-weight-500">Kelola Permintaan</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-4 border-bottom-0">Nama Project</th>
                                <th class="border-bottom-0">Status</th>
                                <th class="border-bottom-0">Diajukan</th>
                                <th class="pr-4 border-bottom-0 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(auth()->user()->projectRequests()->latest()->take(6)->get() as $request)
                                <tr>
                                    <td class="pl-4 font-weight-600 text-dark">{{ $request->project_name }}</td>
                                    <td>
                                        <span class="badge badge-{{ $request->request_status_badge_class }}">{{ $request->request_status_label }}</span>
                                    </td>
                                    <td class="text-muted small">{{ $request->submitted_at ? $request->submitted_at->format('d M Y') : '-' }}</td>
                                    <td class="pr-4 text-right">
                                        <a href="{{ route('project-requests.show', $request) }}" class="btn btn-sm btn-light text-info" title="Lihat"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted mb-2"><i class="fas fa-folder-open fa-3x" style="opacity: 0.2;"></i></div>
                                        <p class="mb-0 text-muted">Belum ada permintaan. <a href="{{ route('project-requests.create') }}" class="font-weight-600">Buat permintaan pertama Anda</a></p>
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
