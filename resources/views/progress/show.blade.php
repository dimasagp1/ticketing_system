@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    @if(auth()->user()->isClient())
        <li class="breadcrumb-item"><a href="{{ route('project-requests.index') }}">My Projects</a></li>
    @elseif(auth()->user()->isDeveloper())
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Antrian Saya</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('queues.index') }}">Queues</a></li>
    @endif
    <li class="breadcrumb-item active">{{ $queue->project_name }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        <!-- Project Info -->
        <div class="card support-shell-card mb-4">
            <div class="card-body box-profile px-4 pb-4 pt-4">
                <h3 class="profile-username font-weight-bold text-center">{{ $queue->project_name }}</h3>
                <p class="text-muted text-center">{{ $queue->assignedTo->name ?? 'Unassigned' }}</p>
                <div class="text-center mb-3">
                    @if($queue->status == 'Completed')
                        <span class="badge badge-success">Completed</span>
                    @elseif($queue->status == 'In Progress')
                        <span class="badge badge-primary">In Progress</span>
                    @else
                        <span class="badge badge-secondary">{{ $queue->status }}</span>
                    @endif
                </div>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Progress</b> <a class="float-right">{{ $queue->progress }}%</a>
                    </li>
                    <li class="list-group-item">
                        <b>Deadline</b> <a class="float-right">{{ $queue->deadline ? $queue->deadline->format('d M Y') : '-' }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Remaining</b> <a class="float-right">{{ $queue->getDaysRemaining() ?? '-' }} days</a>
                    </li>
                </ul>

                @if($queue->projectRequest)
                    <a href="{{ route('project-requests.show', $queue->projectRequest) }}" class="btn btn-primary btn-block font-weight-500 shadow-sm mt-3" style="border-radius: 0.5rem;"><b>Lihat Detail Proyek</b></a>
                    <a href="{{ route('berita-acara.pdf', $queue->projectRequest) }}" class="btn btn-outline-danger btn-block font-weight-bold shadow-sm mt-2" style="border-radius: 0.5rem;">
                        <i class="fas fa-file-pdf mr-1"></i> Export Berita Acara (PDF)
                    </a>
                    <button type="button" class="btn btn-outline-dark btn-block font-weight-bold shadow-sm mt-2" style="border-radius: 0.5rem;" onclick="openBeritaAcaraModal('{{ route('berita-acara.print', $queue->projectRequest) }}')">
                        <i class="fas fa-print mr-1"></i> Pratinjau / Cetak BA
                    </button>
                @endif
            </div>
        </div>

        <!-- Description -->
        <div class="card support-shell-card mb-4">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Description</h3>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                <p class="text-muted mb-0">{{ $queue->description }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <!-- Progress Steps -->
        <div class="card support-shell-card mb-4">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <ul class="nav nav-pills mt-2" style="gap: 0.5rem;">
                    <li class="nav-item"><a class="nav-link active" href="#timeline" data-toggle="tab">Timeline & Activity</a></li>
                    @if(auth()->user()->hasRole(['developer', 'admin', 'super_admin']))
                        <li class="nav-item"><a class="nav-link" href="#update" data-toggle="tab">Update Progress</a></li>
                    @endif
                </ul>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                <div class="tab-content mt-3">
                    <div class="active tab-pane" id="timeline">
                        <!-- The timeline -->
                        <div class="timeline timeline-inverse">
                            @forelse($queue->progressLogs()->orderBy('created_at', 'desc')->get() as $log)
                                <div class="time-label">
                                    <span class="bg-{{ $log->completed_at ? 'success' : 'primary' }}">
                                        {{ $log->created_at->format('d M Y') }}
                                    </span>
                                </div>
                                
                                <div>
                                    <i class="fas fa-{{ $log->projectStage->icon ?? 'tasks' }} bg-primary"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i> {{ $log->created_at->format('H:i') }}</span>
                                        <h3 class="timeline-header"><a href="#">{{ $log->updatedBy->name }}</a> updated: {{ $log->projectStage->name }}</h3>
                                        <div class="timeline-body">
                                            <p>{{ $log->activity_description }}</p>
                                            
                                            {{-- Lampiran Progres (Opsional) --}}
                                            @if($log->attachment_path)
                                                <div class="mt-2 mb-3 p-2.5 bg-light border rounded-xl d-inline-flex align-items-center gap-2 shadow-sm flex-wrap">
                                                    <i class="fas {{ $log->file_icon_class }} fa-lg mr-1"></i>
                                                    <span class="font-weight-bold text-sm mr-2 text-dark">{{ $log->attachment_name }}</span>
                                                    <a href="{{ route('progress.attachment.view', $log) }}" target="_blank" class="btn btn-xs btn-info px-2 py-1" style="border-radius: 0.4rem;">
                                                        <i class="fas fa-eye mr-1"></i> Lihat
                                                    </a>
                                                    <a href="{{ route('progress.attachment.download', $log) }}" class="btn btn-xs btn-primary px-2 py-1" style="border-radius: 0.4rem;">
                                                        <i class="fas fa-download mr-1"></i> Unduh
                                                    </a>
                                                </div>
                                            @endif

                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-success" style="width: {{ $log->progress_percentage }}%"></div>
                                            </div>
                                            <small class="badge badge-light border mt-1">{{ $log->progress_percentage }}% Complete</small>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div>
                                    <i class="fas fa-info bg-gray"></i>
                                    <div class="timeline-item">
                                        <h3 class="timeline-header">Belum ada aktivitas tercatat</h3>
                                        <div class="timeline-body text-muted">Belum ada update dari tim. Aktivitas akan muncul di sini.</div>
                                    </div>
                                </div>
                            @endforelse
                
                            @if($queue->created_at)
                                <div class="time-label">
                                    <span class="bg-secondary">{{ $queue->created_at->format('d M Y') }}</span>
                                </div>
                                <div>
                                    <i class="fas fa-plus bg-secondary"></i>
                                    <div class="timeline-item">
                                        <h3 class="timeline-header">Project Created/Queued</h3>
                                    </div>
                                </div>
                            @endif
                            
                            <div>
                                <i class="far fa-clock bg-gray"></i>
                            </div>
                        </div>
                    </div>
                   
                    @if(auth()->user()->hasRole(['developer', 'admin', 'super_admin']))
                        <div class="tab-pane" id="update">
                            <form action="{{ route('progress.update-stage', $queue) }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
                                @csrf
                                <div class="form-group row">
                                    <label for="stage_id" class="col-sm-2 col-form-label">Current Stage</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="stage_id" id="stage_id">
                                            @foreach($stages as $stage)
                                                <option value="{{ $stage->id }}" {{ ($currentStage->project_stage_id ?? '') == $stage->id ? 'selected' : '' }}>
                                                    {{ $stage->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="progress_percentage" class="col-sm-2 col-form-label">Progress %</label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" id="progress_percentage" name="progress_percentage" 
                                               min="{{ $queue->progress }}" max="100" value="{{ $queue->progress }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="activity_description" class="col-sm-2 col-form-label">Activity Description</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="activity_description" name="activity_description" rows="3" placeholder="Describe the work done..."></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="attachment" class="col-sm-2 col-form-label">Lampiran (Opsional)</label>
                                    <div class="col-sm-10">
                                        <input type="file" class="form-control-file border p-2 rounded-lg bg-light" id="attachment" name="attachment">
                                        <small class="form-text text-muted">Unggah tangkapan layar, screenshot, bukti pengerjaan, atau dokumen terkait (Maksimal 10MB)</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="offset-sm-2 col-sm-10">
                                        <button type="submit" class="btn btn-danger font-weight-bold px-4" style="border-radius: 0.5rem;">
                                            <i class="fas fa-paper-plane mr-1"></i> Update Progress
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            <hr>
                            
                            <h4>Quick Log</h4>
                            <form action="{{ route('progress.log-activity', $queue) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Add Activity Log (without changing stage)</label>
                                    <textarea class="form-control" name="activity_description" rows="2" placeholder="Quick update..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-secondary btn-sm">Log Activity</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
</div>

<!-- Modal Pratinjau Berita Acara -->
@if($queue->projectRequest)
<div class="modal fade" id="modalBeritaAcara" tabindex="-1" role="dialog" aria-labelledby="modalBeritaAcaraTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width: 90vw;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="modal-title font-weight-bold mb-0" id="modalBeritaAcaraTitle">
                    <i class="fas fa-file-contract text-warning mr-2"></i> Pratinjau Berita Acara IT
                </h5>
                <div class="d-flex align-items-center">
                    <a href="{{ route('berita-acara.pdf', $queue->projectRequest) }}" class="btn btn-sm btn-danger font-weight-bold mr-2" style="border-radius: 6px;">
                        <i class="fas fa-file-pdf mr-1"></i> Unduh PDF
                    </a>
                    <button type="button" class="btn btn-sm btn-warning font-weight-bold mr-2 text-dark" style="border-radius: 6px;" onclick="document.getElementById('iframeBeritaAcara').contentWindow.print()">
                        <i class="fas fa-print mr-1"></i> Cetak Dokumen
                    </button>
                    <button type="button" class="close text-white opacity-100 ml-2" data-dismiss="modal" aria-label="Close" style="outline: none;">
                        <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body p-0" style="height: 80vh; background: #e5e7eb;">
                <iframe id="iframeBeritaAcara" src="" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    function openBeritaAcaraModal(url) {
        var iframe = document.getElementById('iframeBeritaAcara');
        if (iframe) {
            iframe.src = url;
        }
        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
            window.jQuery('#modalBeritaAcara').modal('show');
        } else {
            var modalEl = document.getElementById('modalBeritaAcara');
            if (modalEl) {
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
                document.body.classList.add('modal-open');
            }
        }
    }
</script>
@endpush

@push('js')
<script>
    if (typeof openBeritaAcaraModal === 'undefined') {
        function openBeritaAcaraModal(url) {
            var iframe = document.getElementById('iframeBeritaAcara');
            if (iframe) {
                iframe.src = url;
            }
            if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
                window.jQuery('#modalBeritaAcara').modal('show');
            }
        }
    }
</script>
@endpush
