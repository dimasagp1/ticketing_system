@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    <li class="breadcrumb-item active">Antrian</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card support-shell-card mb-4">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Semua Antrian Proyek</h3>
                <div class="card-tools">
                    <a href="{{ route('queues.index') }}" class="btn btn-sm btn-light border font-weight-500 shadow-sm" style="border-radius: 0.5rem;">Atur Ulang</a>
                </div>
            </div>
            <div class="card-body px-4 pb-4 pt-2 border-bottom bg-light">
                <form action="{{ route('queues.index') }}" method="GET">
                    <div class="form-row">
                        <div class="col-md-3 mb-2">
                            <input type="text" name="search" class="form-control" placeholder="Cari tiket/proyek/klien..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            <select name="queue_status" class="form-control">
                                <option value="">Status Antrian</option>
                                @foreach(['Pending', 'In Progress', 'On Hold', 'Completed', 'Cancelled'] as $status)
                                    <option value="{{ $status }}" {{ request('queue_status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 mb-2">
                            <select name="priority" class="form-control">
                                <option value="">Prioritas</option>
                                @foreach(['Rendah', 'Sedang', 'Tinggi'] as $priority)
                                    <option value="{{ $priority }}" {{ request('priority') === $priority ? 'selected' : '' }}>{{ $priority }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <select name="assigned" class="form-control">
                                <option value="">Penugasan</option>
                                <option value="assigned" {{ request('assigned') === 'assigned' ? 'selected' : '' }}>Ditugaskan</option>
                                <option value="unassigned" {{ request('assigned') === 'unassigned' ? 'selected' : '' }}>Belum Ditugaskan</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <select name="ticket_status" class="form-control">
                                <option value="">Status Tiket</option>
                                @foreach(\App\Models\ProjectRequest::ticketStatusLabels() as $value => $label)
                                    <option value="{{ $value }}" {{ request('ticket_status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 mb-2">
                            <select name="sla_filter" class="form-control">
                                <option value="">SLA</option>
                                <option value="overdue" {{ request('sla_filter') === 'overdue' ? 'selected' : '' }}>Terlambat</option>
                                <option value="today" {{ request('sla_filter') === 'today' ? 'selected' : '' }}>Jatuh Tempo Hari Ini</option>
                                <option value="at_risk_24h" {{ request('sla_filter') === 'at_risk_24h' ? 'selected' : '' }}>Risiko 24 Jam</option>
                            </select>
                        </div>
                        <div class="col-md-1 mb-2">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body px-4 pb-4 pt-2 table-responsive p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Ticket</th>
                            <th>Proyek</th>
                            <th>Client</th>
                            <th>Petugas</th>
                            <th>Status</th>
                            <th>Status Tiket</th>
                            <th>SLA Jatuh Tempo</th>
                            <th>Progress</th>
                            <th>Batas Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($queues as $queue)
                            <tr>
                                <td>{{ $queue->id }}</td>
                                <td>{{ $queue->projectRequest?->ticket_number ?? '-' }}</td>
                                <td>{{ $queue->project_name }}</td>
                                <td>{{ $queue->client_name }}</td>
                                <td>{{ $queue->assignedTo->name ?? 'Belum Ditugaskan' }}</td>
                                <td>
                                    <span class="badge badge-{{ $queue->status == 'Completed' ? 'success' : ($queue->status == 'In Progress' ? 'primary' : 'secondary') }}">
                                        {{ $queue->status }}
                                    </span>
                                </td>
                                <td>
                                    @if($queue->projectRequest)
                                        <span class="badge badge-{{ $queue->projectRequest->ticket_status_badge_class }}">{{ $queue->projectRequest->ticket_status_label }}</span>
                                    @else
                                        <span class="badge badge-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($queue->projectRequest?->sla_resolution_due_at)
                                        <span class="{{ $queue->projectRequest->sla_resolution_due_at->isPast() && in_array($queue->projectRequest->ticket_status, \App\Models\ProjectRequest::slaTrackedTicketStatuses(), true) ? 'text-danger font-weight-bold' : '' }}">
                                            {{ $queue->projectRequest->sla_resolution_due_at->format('d M Y H:i') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="progress progress-xs">
                                        <div class="progress-bar bg-success" style="width: {{ $queue->progress }}%"></div>
                                    </div>
                                    <small>{{ $queue->progress }}%</small>
                                </td>
                                <td>{{ $queue->deadline ? $queue->deadline->format('d M Y') : '-' }}</td>
                                <td>
                                    <a href="{{ route('progress.show', $queue) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">Belum ada proyek dalam antrian.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
                {{ $queues->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
