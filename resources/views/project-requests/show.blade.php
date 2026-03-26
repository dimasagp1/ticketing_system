@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('project-requests.index') }}">Permintaan Proyek</a></li>
    <li class="breadcrumb-item active">{{ $projectRequest->project_name }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Project Details -->
        <div class="card support-shell-card mb-4">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Informasi Proyek</h3>
                <div class="card-tools">
                    @if(auth()->user()->isClient() && in_array($projectRequest->status, ['draft', 'revision_requested']))
                        <a href="{{ route('project-requests.edit', $projectRequest) }}" class="btn btn-warning px-3 shadow-sm" style="border-radius: 0.5rem; font-weight: 500;">
                            <i class="fas fa-edit mr-1"></i> Ubah
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                <table class="table table-borderless detail-table mb-0">
                    <tr>
                        <th width="200">Nomor Tiket:</th>
                        <td><span class="badge badge-dark">{{ $projectRequest->ticket_number ?? '-' }}</span></td>
                    </tr>
                    <tr>
                        <th width="200">Nama Proyek:</th>
                        <td>{{ $projectRequest->project_name }}</td>
                    </tr>
                    <tr>
                        <th>Kategori:</th>
                        <td>{{ ucfirst(str_replace('_', ' ', $projectRequest->ticket_category ?? 'incident')) }}</td>
                    </tr>
                    <tr>
                        <th>Klien:</th>
                        <td>{{ $projectRequest->client->name }}</td>
                    </tr>
                    <tr>
                        <th>Dampak / Urgensi:</th>
                        <td>
                            <span class="badge badge-light">{{ strtoupper($projectRequest->impact ?? 'medium') }}</span>
                            <span class="badge badge-light">{{ strtoupper($projectRequest->urgency ?? 'medium') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Durasi:</th>
                        <td>{{ $projectRequest->estimated_duration ? $projectRequest->estimated_duration . ' hari' : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi:</th>
                        <td>{{ $projectRequest->description }}</td>
                    </tr>
                    <tr>
                        <th>SLA Respon:</th>
                        <td>{{ $projectRequest->sla_response_due_at ? $projectRequest->sla_response_due_at->format('d M Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>SLA Penyelesaian:</th>
                        <td>{{ $projectRequest->sla_resolution_due_at ? $projectRequest->sla_resolution_due_at->format('d M Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Diselesaikan Pada:</th>
                        <td>{{ $projectRequest->resolved_at ? $projectRequest->resolved_at->format('d M Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Ditutup Pada:</th>
                        <td>{{ $projectRequest->closed_at ? $projectRequest->closed_at->format('d M Y H:i') : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Requirements Files -->
        <div class="card support-shell-card mb-4">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Berkas Kebutuhan</h3>
            </div>
            <div class="card-body p-0">
                @if($projectRequest->requirements->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="pl-4 border-bottom-0">Nama Berkas</th>
                                    <th class="d-none d-md-table-cell border-bottom-0">Ukuran</th>
                                    <th class="d-none d-md-table-cell border-bottom-0">Versi</th>
                                    <th class="d-none d-lg-table-cell border-bottom-0">Diunggah</th>
                                    <th class="pr-4 border-bottom-0">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($projectRequest->requirements as $req)
                                <tr>
                                        <td class="pl-4"><i class="fas {{ $req->file_icon_class }} mr-2"></i> {{ $req->file_name }}</td>
                                        <td class="d-none d-md-table-cell">{{ $req->file_size_formatted }}</td>
                                        <td class="d-none d-md-table-cell">
                                            @if($req->is_current_version)
                                                <span class="badge badge-success">v{{ $req->version }}</span>
                                            @else
                                                <span class="badge badge-secondary">v{{ $req->version }}</span>
                                            @endif
                                        </td>
                                        <td class="d-none d-lg-table-cell">{{ $req->created_at->format('d M Y') }}</td>
                                        <td class="pr-4">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-light text-info requirement-preview-btn"
                                                data-toggle="modal"
                                                data-target="#requirementPreviewModal"
                                                data-url="{{ route('project-requirements.view', $req) }}"
                                                data-download-url="{{ route('project-requirements.download', $req) }}"
                                                data-name="{{ $req->file_name }}"
                                                data-mime="{{ $req->file_type ?? '' }}"
                                                data-previewable="{{ $req->is_previewable ? '1' : '0' }}"
                                                title="Lihat"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="{{ route('project-requirements.download', $req) }}" class="btn btn-sm btn-light text-primary">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Belum ada berkas kebutuhan yang diunggah.</p>
                @endif
            </div>
        </div>

        <!-- Revisions -->
        @if($projectRequest->revisions->count() > 0)
            <div class="card support-shell-card mb-4">
                <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                    <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Riwayat Revisi</h3>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    @foreach($projectRequest->revisions as $revision)
                        <div class="callout callout-{{ $revision->status == 'approved' ? 'success' : 'warning' }}">
                            <h5>Revisi #{{ $revision->revision_number }}</h5>
                            <p><strong>Perubahan Diminta:</strong><br>{{ $revision->requested_changes }}</p>
                            @if($revision->client_response)
                                <p><strong>Respon Klien:</strong><br>{{ $revision->client_response }}</p>
                            @endif
                            <small class="text-muted">Diminta oleh {{ $revision->requestedBy->name }} pada {{ $revision->created_at->format('d M Y H:i') }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        <!-- Status Card -->
        <div class="card support-shell-card mb-4">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Status</h3>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                <div class="text-center mb-3">
                    <span class="badge badge-{{ $projectRequest->request_status_badge_class }} badge-lg px-3 py-2" style="font-size: 0.9rem;">{{ $projectRequest->request_status_label }}</span>
                </div>

                <div class="mb-3 text-center">
                    <span class="badge badge-{{ $projectRequest->ticket_status_badge_class }}">{{ $projectRequest->ticket_status_label }}</span>
                </div>

                @if(auth()->user()->hasRole(['admin', 'super_admin']) && $projectRequest->status !== 'draft' && in_array($projectRequest->ticket_status, \App\Models\ProjectRequest::pausableTicketStatuses(), true))
                    <form action="{{ route('project-requests.pause', $projectRequest) }}" method="POST" class="mt-2" id="pause-form-{{ $projectRequest->id }}">
                        @csrf
                        <button type="button" class="btn btn-warning text-dark btn-block font-weight-500 shadow-sm" style="border-radius: 0.5rem;" onclick="confirmAction('pause-form-{{ $projectRequest->id }}', 'Pause tiket?', 'Tiket akan dijeda sementara sampai dijalankan kembali.', 'Ya, pause', '#f97316', 'warning')">
                            <i class="fas fa-pause-circle"></i> Pause Tiket
                        </button>
                    </form>
                @elseif(auth()->user()->hasRole(['admin', 'super_admin']) && $projectRequest->status !== 'draft' && in_array($projectRequest->ticket_status, \App\Models\ProjectRequest::playableTicketStatuses(), true))
                    <form action="{{ route('project-requests.play', $projectRequest) }}" method="POST" class="mt-2" id="play-form-{{ $projectRequest->id }}">
                        @csrf
                        <button type="button" class="btn btn-success btn-block font-weight-500 shadow-sm" style="border-radius: 0.5rem;" onclick="confirmAction('play-form-{{ $projectRequest->id }}', 'Jalankan kembali tiket?', 'Tiket akan dilanjutkan kembali ke proses kerja aktif.', 'Ya, jalankan', '#10b981', 'question')">
                            <i class="fas fa-play-circle"></i> Play Tiket
                        </button>
                    </form>
                @endif

                @if(in_array($projectRequest->status, ['draft', 'revision_requested']) && auth()->user()->isClient())
                    <form action="{{ route('project-requests.submit', $projectRequest) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-{{ $projectRequest->status == 'revision_requested' ? 'primary' : 'success' }} btn-block font-weight-500 shadow-sm" style="border-radius: 0.5rem;">
                            <i class="fas fa-paper-plane mr-1"></i> 
                            {{ $projectRequest->status == 'revision_requested' ? 'Kirim Revisi' : 'Ajukan Persetujuan' }}
                        </button>
                    </form>
                @endif

                @if($projectRequest->queue_id)
                    <a href="{{ route('progress.show', $projectRequest->queue_id) }}" class="btn btn-info btn-block mt-3 font-weight-500 shadow-sm" style="border-radius: 0.5rem;">
                        <i class="fas fa-tasks mr-1"></i> Lihat Progres
                    </a>
                @endif

                @if(auth()->user()->hasRole(['admin', 'super_admin']) && $projectRequest->status !== 'draft' && in_array($projectRequest->ticket_status, \App\Models\ProjectRequest::resolvableTicketStatuses(), true))
                    <form action="{{ route('project-requests.resolve', $projectRequest) }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-check-circle"></i> Selesaikan Tiket
                        </button>
                    </form>
                @endif

                @if((auth()->user()->hasRole(['admin', 'super_admin']) || (auth()->user()->isClient() && auth()->id() === $projectRequest->client_id)) && in_array($projectRequest->ticket_status, ['resolved', 'cancelled']))
                    <form action="{{ route('project-requests.close', $projectRequest) }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-block font-weight-500 shadow-sm" style="border-radius: 0.5rem;">
                            <i class="fas fa-lock mr-1"></i> Tutup Tiket
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Timeline -->
        <div class="card support-shell-card mb-4">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Linimasa</h3>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                <div class="timeline mt-2">
                    <div class="time-label">
                        <span class="bg-secondary">{{ $projectRequest->created_at->format('d M Y') }}</span>
                    </div>
                    <div>
                        <i class="fas fa-plus bg-primary"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Dibuat</h3>
                        </div>
                    </div>

                    @if($projectRequest->submitted_at)
                        <div class="time-label">
                            <span class="bg-warning">{{ $projectRequest->submitted_at->format('d M Y') }}</span>
                        </div>
                        <div>
                            <i class="fas fa-paper-plane bg-warning"></i>
                            <div class="timeline-item">
                                <h3 class="timeline-header">Diajukan</h3>
                            </div>
                        </div>
                    @endif

                    @if($projectRequest->approved_at)
                        <div class="time-label">
                            <span class="bg-success">{{ $projectRequest->approved_at->format('d M Y') }}</span>
                        </div>
                        <div>
                            <i class="fas fa-check bg-success"></i>
                            <div class="timeline-item">
                                <h3 class="timeline-header">Disetujui oleh {{ $projectRequest->approvedBy->name }}</h3>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.partials.requirement-preview-modal')
@endsection
