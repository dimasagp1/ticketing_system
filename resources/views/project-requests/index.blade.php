@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    <li class="breadcrumb-item active">Permintaan Proyek</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card support-shell-card mb-4">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Semua Permintaan Proyek</h3>
                @if(auth()->user()->isClient())
                    <a href="{{ route('project-requests.create') }}" class="btn btn-primary px-3 shadow-sm" style="border-radius: 0.5rem; font-weight: 500;">
                        <i class="fas fa-plus mr-1"></i> Permintaan Baru
                    </a>
                @endif
            </div>
            <div class="card-body px-4 pb-4">
                <form method="GET" action="{{ route('project-requests.index') }}" class="mb-4 p-3 bg-light rounded" style="border: 1px solid #e2e8f0;">
                    <div class="form-row">
                        <div class="col-md-3 mb-2">
                            <input type="text" name="search" class="form-control" placeholder="Cari tiket/proyek/klien..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            @php($requestStatusOptions = \App\Models\ProjectRequest::requestStatusLabels())
                            <select name="status" class="form-control">
                                <option value="">Status Permintaan</option>
                                @foreach($requestStatusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
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
                        <div class="col-md-2 mb-2">
                            <select name="ticket_category" class="form-control">
                                <option value="">Kategori</option>
                                @foreach(
                                    [
                                        'incident' => 'Insiden',
                                        'service_request' => 'Permintaan Layanan',
                                        'access' => 'Akses',
                                        'bug' => 'Bug',
                                        'technical_support' => 'Dukungan Teknis',
                                        'other' => 'Lainnya',
                                    ] as $value => $label
                                )
                                    <option value="{{ $value }}" {{ request('ticket_category') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 mb-2">
                            <select name="impact" class="form-control">
                                <option value="">Dampak</option>
                                @foreach(['low' => 'Rendah', 'medium' => 'Sedang', 'high' => 'Tinggi', 'critical' => 'Kritis'] as $value => $label)
                                    <option value="{{ $value }}" {{ request('impact') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 mb-2">
                            <select name="urgency" class="form-control">
                                <option value="">Urgensi</option>
                                @foreach(['low' => 'Rendah', 'medium' => 'Sedang', 'high' => 'Tinggi', 'critical' => 'Kritis'] as $value => $label)
                                    <option value="{{ $value }}" {{ request('urgency') === $value ? 'selected' : '' }}>{{ $label }}</option>
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
                        <div class="col-md-2 mb-2">
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary btn-block mr-2">
                                    <i class="fas fa-filter"></i> Terapkan
                                </button>
                                <a href="{{ route('project-requests.index') }}" class="btn btn-outline-secondary btn-block">
                                    Atur Ulang
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle data-table mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>ID</th>
                                <th>Ticket</th>
                                <th>Nama Proyek</th>
                                @if(!auth()->user()->isClient())
                                    <th class="d-none d-lg-table-cell">Client</th>
                                @endif
                                <th class="d-none d-lg-table-cell">Kategori</th>
                                <th class="d-none d-xl-table-cell">Dampak/Urgensi</th>
                                <th>Status</th>
                                <th>Status Tiket</th>
                                <th>SLA Due</th>
                                <th class="d-none d-lg-table-cell">Diajukan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>
                                        <span class="badge badge-dark">{{ $request->ticket_number ?? '-' }}</span>
                                    </td>
                                    <td>{{ $request->project_name }}</td>
                                    @if(!auth()->user()->isClient())
                                        <td class="d-none d-lg-table-cell">{{ $request->client->name }}</td>
                                    @endif
                                    <td class="d-none d-lg-table-cell">{{ $request->ticket_category_label }}</td>
                                    <td class="d-none d-xl-table-cell">
                                        <span class="badge badge-light">{{ strtoupper($request->impact ?? 'medium') }}</span>
                                        <span class="badge badge-light">{{ strtoupper($request->urgency ?? 'medium') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $request->request_status_badge_class }}">{{ $request->request_status_label }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $request->ticket_status_badge_class }}">{{ $request->ticket_status_label }}</span>
                                    </td>
                                    <td>
                                        @if($request->sla_resolution_due_at)
                                            <span class="{{ $request->sla_resolution_due_at->isPast() && in_array($request->ticket_status, \App\Models\ProjectRequest::slaTrackedTicketStatuses(), true) ? 'text-danger font-weight-bold' : '' }}">
                                                {{ $request->sla_resolution_due_at->format('d M Y H:i') }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="d-none d-lg-table-cell">{{ $request->submitted_at ? $request->submitted_at->format('d M Y') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('project-requests.show', $request) }}" class="btn btn-light text-info btn-sm mr-1 mb-1" title="Lihat" aria-label="Lihat tiket">
                                            <i class="fas fa-eye"></i><span class="d-none d-xl-inline ml-1">Lihat</span>
                                        </a>
                                        @if(auth()->user()->hasRole(['admin', 'super_admin']) && $request->status !== 'draft' && in_array($request->ticket_status, \App\Models\ProjectRequest::pausableTicketStatuses(), true))
                                            <form action="{{ route('project-requests.pause', $request) }}" method="POST" style="display: inline;" id="pause-form-{{ $request->id }}">
                                                @csrf
                                                <button type="button" class="btn btn-warning text-dark btn-sm mr-1 mb-1" title="Pause Tiket" aria-label="Pause tiket" onclick="confirmAction('pause-form-{{ $request->id }}', 'Pause tiket?', 'Tiket akan dijeda sementara sampai dijalankan kembali.', 'Ya, pause', '#f97316', 'warning')">
                                                    <i class="fas fa-pause"></i><span class="d-none d-xl-inline ml-1">Pause</span>
                                                </button>
                                            </form>
                                        @elseif(auth()->user()->hasRole(['admin', 'super_admin']) && $request->status !== 'draft' && in_array($request->ticket_status, \App\Models\ProjectRequest::playableTicketStatuses(), true))
                                            <form action="{{ route('project-requests.play', $request) }}" method="POST" style="display: inline;" id="play-form-{{ $request->id }}">
                                                @csrf
                                                <button type="button" class="btn btn-success btn-sm mr-1 mb-1" title="Play Tiket" aria-label="Play tiket" onclick="confirmAction('play-form-{{ $request->id }}', 'Jalankan kembali tiket?', 'Tiket akan dilanjutkan kembali ke proses kerja aktif.', 'Ya, jalankan', '#10b981', 'question')">
                                                    <i class="fas fa-play"></i><span class="d-none d-xl-inline ml-1">Play</span>
                                                </button>
                                            </form>
                                        @endif
                                        @if(auth()->user()->isClient() && in_array($request->status, ['draft', 'revision_requested']))
                                            <a href="{{ route('project-requests.edit', $request) }}" class="btn btn-warning btn-sm mr-1 mb-1" title="Ubah" aria-label="Ubah tiket">
                                                <i class="fas fa-edit"></i><span class="d-none d-xl-inline ml-1">Ubah</span>
                                            </a>
                                        @endif
                                        @if(auth()->user()->isClient() && $request->status == 'draft')
                                            <form action="{{ route('project-requests.destroy', $request) }}" method="POST" style="display: inline;" id="delete-form-{{ $request->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm mb-1" onclick="confirmDelete('delete-form-{{ $request->id }}')" title="Hapus" aria-label="Hapus tiket">
                                                    <i class="fas fa-trash"></i><span class="d-none d-xl-inline ml-1">Hapus</span>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
