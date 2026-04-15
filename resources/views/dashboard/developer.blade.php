@php
    $assigned = auth()->user()->assignedQueues();
    $assignedCount = $assigned->count();
    $completedCount = auth()->user()->assignedQueues()->where('status', 'Completed')->count();
    $inProgressCount = auth()->user()->assignedQueues()->where('status', 'In Progress')->count();
    $pendingCount = auth()->user()->assignedQueues()->where('status', 'Pending')->count();
    $activeChats = auth()->user()->developerConversations()->where('status', 'active')->count();
    $workloadBase = max($assignedCount, 1);
@endphp

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <p class="text-muted mb-0 font-weight-500">Pantau antrian yang ditugaskan, progres pengerjaan, dan percakapan aktif.</p>
    </div>
    <a href="{{ route('chat.index') }}" class="btn btn-primary px-4 shadow-sm" style="border-radius: 0.5rem; font-weight: 500;">
        <i class="fas fa-comments mr-2"></i> Buka Chat
    </a>
</div>

<div class="row">
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="support-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-primary"><i class="fas fa-layer-group"></i></span>
                <small class="text-muted">Ditugaskan</small>
            </div>
            <div class="support-stat-value">{{ $assignedCount }}</div>
            <div class="support-stat-label">Total Antrian</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="support-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-info"><i class="fas fa-spinner"></i></span>
                <small class="text-info">Berjalan</small>
            </div>
            <div class="support-stat-value">{{ $inProgressCount }}</div>
            <div class="support-stat-label">Sedang Dikerjakan</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="support-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-success"><i class="fas fa-check-circle"></i></span>
                <small class="text-success">Selesai</small>
            </div>
            <div class="support-stat-value">{{ $completedCount }}</div>
            <div class="support-stat-label">Terselesaikan</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-3">
        <div class="support-stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-warning"><i class="fas fa-comments"></i></span>
                <small class="text-warning">Aktif</small>
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
                <h3 class="card-title mb-0">Proyek Tugas Saya</h3>
                <a href="{{ route('queues.index') }}" class="btn btn-link btn-sm">Lihat Antrian</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-4 border-bottom-0">Nama Proyek</th>
                                <th class="d-none d-md-table-cell border-bottom-0">Client</th>
                                <th class="border-bottom-0">Status</th>
                                <th class="border-bottom-0">Progres</th>
                                <th class="border-bottom-0">Deadline</th>
                                <th class="pr-4 border-bottom-0 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(auth()->user()->assignedQueues()->latest()->take(8)->get() as $queue)
                                    <tr>
                                        <td class="pl-4 font-weight-600 text-dark">{{ $queue->project_name }}</td>
                                        <td class="d-none d-md-table-cell text-muted">{{ $queue->client_name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $queue->status == 'Completed' ? 'success' : ($queue->status == 'In Progress' ? 'primary' : ($queue->status == 'Pending' ? 'warning' : 'secondary')) }}">
                                                {{ $queue->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress progress-xs w-100 mr-2">
                                                    <div class="progress-bar bg-{{ $queue->status == 'Completed' ? 'success' : 'primary' }}" style="width: {{ $queue->progress }}%"></div>
                                                </div>
                                                <small class="font-weight-600">{{ $queue->progress }}%</small>
                                            </div>
                                        </td>
                                        <td class="text-muted small"><i class="far fa-clock mr-1"></i> {{ $queue->deadline ? $queue->deadline->format('d M Y') : '-' }}</td>
                                        <td class="pr-4 text-right">
                                            <a href="{{ route('progress.show', $queue) }}" class="btn btn-sm btn-light text-info" title="Lihat Progres"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada proyek yang ditugaskan.</td>
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
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Distribusi Beban Kerja</h3>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1"><small class="text-muted font-weight-600">Sedang Dikerjakan</small><small class="font-weight-600">{{ round(($inProgressCount / $workloadBase) * 100) }}%</small></div>
                    <div class="progress progress-xs"><div class="progress-bar bg-primary" style="width: {{ round(($inProgressCount / $workloadBase) * 100) }}%"></div></div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1"><small class="text-muted font-weight-600">Menunggu</small><small class="font-weight-600">{{ round(($pendingCount / $workloadBase) * 100) }}%</small></div>
                    <div class="progress progress-xs"><div class="progress-bar bg-warning" style="width: {{ round(($pendingCount / $workloadBase) * 100) }}%"></div></div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1"><small class="text-muted font-weight-600">Terselesaikan</small><small class="font-weight-600">{{ round(($completedCount / $workloadBase) * 100) }}%</small></div>
                    <div class="progress progress-xs"><div class="progress-bar bg-success" style="width: {{ round(($completedCount / $workloadBase) * 100) }}%"></div></div>
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
                <a href="{{ route('queues.index') }}" class="btn btn-light btn-block mb-2 font-weight-500 text-left px-3"><i class="fas fa-list text-primary mr-2" style="width: 20px;"></i> Buka Papan Antrian</a>
                <a href="{{ route('chat.index') }}" class="btn btn-light btn-block font-weight-500 text-left px-3"><i class="fas fa-comments text-info mr-2" style="width: 20px;"></i> Balas Chat</a>
            </div>
        </div>
    </div>
</div>
