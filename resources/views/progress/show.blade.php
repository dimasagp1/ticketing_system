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
<style>
    /* ==========================================================================
       MODERN TIMELINE & PROGRESS REDESIGN
       ========================================================================== */
    .progress-page-wrapper {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* Overall Summary Card */
    .project-hero-card {
        background: #ffffff;
        border: 1px solid #e2e8f0 !important;
        border-radius: 1.25rem !important;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05) !important;
        position: relative;
        overflow: hidden;
    }

    .project-hero-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #0055FF, #00E676, #FF6B00);
    }

    /* Modern Tabs */
    .timeline-nav-pills {
        background: #f1f5f9;
        padding: 0.35rem;
        border-radius: 0.85rem;
        display: inline-flex;
        border: 1px solid #e2e8f0;
    }

    .timeline-nav-pills .nav-link {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-weight: 700 !important;
        font-size: 0.875rem;
        color: #64748b !important;
        padding: 0.55rem 1.15rem;
        border-radius: 0.65rem !important;
        border: none !important;
        box-shadow: none !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .timeline-nav-pills .nav-link:hover {
        color: #0f172a !important;
        background: rgba(255, 255, 255, 0.6);
    }

    .timeline-nav-pills .nav-link.active {
        background: #ffffff !important;
        color: #0055FF !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    }

    .timeline-nav-pills .nav-link .badge-counter {
        background: #e2e8f0;
        color: #475569;
        font-size: 0.725rem;
        font-weight: 800;
        padding: 0.2rem 0.55rem;
        border-radius: 9999px;
        border: none !important;
        box-shadow: none !important;
    }

    .timeline-nav-pills .nav-link.active .badge-counter {
        background: #dbeafe;
        color: #1d4ed8;
    }

    /* Modern Timeline Architecture */
    .modern-timeline-container {
        position: relative;
        padding-left: 2rem;
        margin-top: 1rem;
    }

    .modern-timeline-container::before {
        content: '';
        position: absolute;
        top: 15px;
        bottom: 25px;
        left: 17px;
        width: 3px;
        background: linear-gradient(180deg, #0055FF 0%, #00E676 50%, #cbd5e1 100%);
        border-radius: 9999px;
    }

    /* Date Group Marker */
    .timeline-date-group {
        position: relative;
        margin-bottom: 1.75rem;
        margin-top: 1rem;
    }

    .timeline-date-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        background: #0f172a;
        color: #ffffff;
        font-size: 0.8rem;
        font-weight: 700;
        padding: 0.35rem 0.9rem;
        border-radius: 9999px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.15);
        margin-left: -0.5rem;
        z-index: 2;
        position: relative;
    }

    .timeline-date-chip.is-today {
        background: linear-gradient(135deg, #0055FF, #2563eb);
    }

    /* Timeline Entry / Card */
    .timeline-entry {
        position: relative;
        margin-bottom: 2rem;
    }

    .timeline-node-icon {
        position: absolute;
        left: -2rem;
        top: 10px;
        transform: translateX(-50%);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 0.875rem;
        box-shadow: 0 0 0 4px #ffffff, 0 2px 8px rgba(0, 0, 0, 0.12);
        z-index: 3;
        transition: transform 0.2s ease;
    }

    .timeline-entry:hover .timeline-node-icon {
        transform: translateX(-50%) scale(1.1);
    }

    /* Timeline Card Box */
    .timeline-card-box {
        background: #ffffff;
        border: 1px solid #e2e8f0 !important;
        border-radius: 1rem !important;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04) !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .timeline-card-box:hover {
        border-color: #cbd5e1 !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.07) !important;
        transform: translateY(-2px);
    }

    /* Timeline Card Header */
    .timeline-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.75rem;
        padding-bottom: 0.85rem;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 0.85rem;
        flex-wrap: wrap;
    }

    .timeline-user-meta {
        display: flex;
        align-items: center;
        gap: 0.65rem;
    }

    .timeline-user-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0055FF, #6366f1);
        color: #ffffff;
        font-weight: 800;
        font-size: 0.825rem;
        display: flex;
        align-items: center;
        justify-content: center;
        text-transform: uppercase;
        box-shadow: 0 2px 6px rgba(0, 85, 255, 0.2);
    }

    .timeline-user-name {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.95rem;
        margin-bottom: 0.1rem;
    }

    .timeline-stage-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
    }

    .timeline-time-meta {
        font-size: 0.8rem;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-weight: 600;
    }

    /* Timeline Body & Description */
    .timeline-description {
        color: #334155;
        font-size: 0.925rem;
        line-height: 1.6;
        margin-bottom: 1rem;
        white-space: pre-line;
    }

    /* Redesigned Attachment Card (Clean, modern, crisp) */
    .timeline-attachment-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.85rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        transition: all 0.2s ease;
    }

    .timeline-attachment-card:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .timeline-file-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 0;
        flex: 1;
    }

    .timeline-file-icon-box {
        width: 40px;
        height: 40px;
        border-radius: 0.5rem;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .timeline-file-name {
        font-weight: 700;
        font-size: 0.875rem;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 320px;
    }

    .timeline-file-type {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 600;
    }

    .timeline-attachment-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-shrink: 0;
    }

    /* Override Global Button style on attachment buttons for modern look */
    .btn-attachment-preview,
    .btn-attachment-download {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-size: 0.8rem !important;
        font-weight: 700 !important;
        border-radius: 0.5rem !important;
        padding: 0.4rem 0.85rem !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.35rem !important;
        transition: all 0.15s ease !important;
        text-decoration: none !important;
        cursor: pointer;
    }

    .btn-attachment-preview {
        background: #eff6ff !important;
        color: #1d4ed8 !important;
        border: 1px solid #bfdbfe !important;
        box-shadow: none !important;
    }

    .btn-attachment-preview:hover {
        background: #dbeafe !important;
        color: #1e40af !important;
        border-color: #93c5fd !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 2px 6px rgba(29, 78, 216, 0.15) !important;
    }

    .btn-attachment-download {
        background: #f0fdf4 !important;
        color: #15803d !important;
        border: 1px solid #bbf7d0 !important;
        box-shadow: none !important;
    }

    .btn-attachment-download:hover {
        background: #dcfce7 !important;
        color: #166534 !important;
        border-color: #86efac !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 2px 6px rgba(21, 128, 61, 0.15) !important;
    }

    /* Timeline Progress Track & Badge */
    .timeline-progress-section {
        background: #f8fafc;
        border-radius: 0.65rem;
        padding: 0.65rem 0.85rem;
        border: 1px solid #f1f5f9;
    }

    .timeline-progress-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.35rem;
    }

    .timeline-progress-label {
        font-size: 0.775rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .timeline-progress-badge {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-size: 0.775rem !important;
        font-weight: 800 !important;
        padding: 0.2rem 0.65rem !important;
        border-radius: 9999px !important;
        border: none !important;
        box-shadow: none !important;
    }

    .timeline-progress-bar-track {
        height: 6px;
        background: #e2e8f0;
        border-radius: 9999px;
        overflow: hidden;
    }

    .timeline-progress-bar-fill {
        height: 100%;
        border-radius: 9999px;
        background: linear-gradient(90deg, #0055FF, #00E676);
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Milestone Bottom Entry */
    .timeline-milestone-box {
        background: #f8fafc;
        border: 1px dashed #cbd5e1 !important;
        border-radius: 0.85rem !important;
        padding: 0.85rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        color: #475569;
        font-size: 0.875rem;
        font-weight: 600;
        box-shadow: none !important;
    }

    /* Sidebar Custom Styling */
    .project-sidebar-card {
        background: #ffffff;
        border: 1px solid #e2e8f0 !important;
        border-radius: 1.25rem !important;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05) !important;
        overflow: hidden;
    }

    .project-sidebar-header {
        padding: 1.25rem 1.25rem 0.5rem 1.25rem;
    }

    .stat-metric-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    /* Update Form Styling */
    .form-section-card {
        background: #ffffff;
        border: 1px solid #e2e8f0 !important;
        border-radius: 1rem !important;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03) !important;
    }

    .form-section-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .custom-range-slider {
        -webkit-appearance: none;
        width: 100%;
        height: 8px;
        border-radius: 5px;
        background: #e2e8f0;
        outline: none;
    }

    .custom-range-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #0055FF;
        cursor: pointer;
        border: 3px solid #ffffff;
        box-shadow: 0 2px 6px rgba(0, 85, 255, 0.4);
        transition: transform 0.15s ease;
    }

    .custom-range-slider::-webkit-slider-thumb:hover {
        transform: scale(1.15);
    }

    .file-dropzone-styled {
        border: 2px dashed #cbd5e1;
        background: #f8fafc;
        border-radius: 0.85rem;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .file-dropzone-styled:hover {
        border-color: #0055FF;
        background: #eff6ff;
    }

    .preset-pill-btn {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 9999px;
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .preset-pill-btn:hover {
        background: #0055FF;
        color: #ffffff;
        border-color: #0055FF;
    }
</style>

<div class="progress-page-wrapper">
    <!-- Top Stepper Pipeline (Overall Stage Progress) -->
    <div class="card project-hero-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <span class="badge badge-dark px-2.5 py-1 text-xs font-weight-bold" style="border-radius: 6px; box-shadow: none;">
                            <i class="fas fa-hashtag mr-1"></i>{{ $queue->projectRequest->ticket_number ?? ('PRJ-' . str_pad($queue->id, 4, '0', STR_PAD_LEFT)) }}
                        </span>
                        <h2 class="h4 font-weight-800 text-dark mb-0 font-fredoka">{{ $queue->project_name }}</h2>
                    </div>
                    <p class="text-muted text-sm mb-0">
                        <i class="far fa-calendar-alt mr-1 text-primary"></i> Dimulai: <strong>{{ $queue->created_at ? $queue->created_at->format('d M Y') : '-' }}</strong>
                        @if($queue->deadline)
                            <span class="mx-2">•</span>
                            <i class="far fa-clock mr-1 text-warning"></i> Target Selesai: <strong>{{ $queue->deadline->format('d M Y') }}</strong>
                        @endif
                    </p>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if($queue->status == 'Completed')
                        <span class="stat-metric-badge bg-success text-white shadow-sm" style="font-size: 0.85rem; padding: 0.4rem 1rem;">
                            <i class="fas fa-check-circle"></i> Completed (100%)
                        </span>
                    @elseif($queue->status == 'In Progress')
                        <span class="stat-metric-badge bg-primary text-white shadow-sm" style="font-size: 0.85rem; padding: 0.4rem 1rem;">
                            <i class="fas fa-spinner fa-spin"></i> In Progress ({{ $queue->progress }}%)
                        </span>
                    @else
                        <span class="stat-metric-badge bg-secondary text-white shadow-sm" style="font-size: 0.85rem; padding: 0.4rem 1rem;">
                            <i class="fas fa-hourglass-start"></i> {{ $queue->status }} ({{ $queue->progress }}%)
                        </span>
                    @endif

                    @if($queue->projectRequest)
                        <a href="{{ route('project-requests.show', $queue->projectRequest) }}" class="btn btn-sm btn-outline-dark font-weight-bold px-3 py-1.5" style="border-radius: 0.5rem; font-family: 'Plus Jakarta Sans', sans-serif !important;">
                            <i class="fas fa-external-link-alt mr-1"></i> Detail Tiket
                        </a>
                    @endif
                </div>
            </div>

            <!-- Global Progress Bar -->
            <div class="mt-2">
                <div class="d-flex justify-content-between align-items-center mb-1 text-xs font-weight-bold text-muted">
                    <span>PROGRESS KESELURUHAN</span>
                    <span class="text-primary font-weight-800">{{ $queue->progress }}%</span>
                </div>
                <div class="progress" style="height: 10px; border-radius: 9999px; background: #e2e8f0; border: none; box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                         role="progressbar" 
                         style="width: {{ $queue->progress }}%; background: linear-gradient(90deg, #0055FF, #00E676) !important; border-radius: 9999px;" 
                         aria-valuenow="{{ $queue->progress }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Project Details & Quick Actions -->
        <div class="col-lg-4 col-md-5 mb-4">
            <!-- Project Overview Card -->
            <div class="card project-sidebar-card mb-4">
                <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                    <h5 class="card-title font-weight-bold mb-0 text-dark" style="font-size: 1.05rem;">
                        <i class="fas fa-info-circle text-primary mr-1"></i> Ringkasan Proyek
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Assigned Developer Profile -->
                    <div class="p-3 bg-light rounded-xl mb-3 border d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary text-white font-weight-800 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            {{ strtoupper(substr($queue->assignedTo->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-xs text-muted font-weight-bold d-block text-uppercase">Teknisi / Developer</span>
                            <strong class="text-dark font-weight-bold text-sm text-truncate d-block">{{ $queue->assignedTo->name ?? 'Belum Ditugaskan' }}</strong>
                            <small class="text-muted d-block">{{ $queue->assignedTo->email ?? '-' }}</small>
                        </div>
                    </div>

                    <!-- Meta Information List -->
                    <ul class="list-group list-group-flush border rounded-xl overflow-hidden mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2.5 bg-white text-sm">
                            <span class="text-muted"><i class="fas fa-layer-group mr-2 text-info"></i>Tahap Aktif</span>
                            <span class="font-weight-bold text-dark">{{ $currentStage->projectStage->name ?? ($queue->status == 'Completed' ? 'Selesai' : 'Inisiasi') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2.5 bg-white text-sm">
                            <span class="text-muted"><i class="far fa-calendar-check mr-2 text-success"></i>Tenggat Waktu</span>
                            <span class="font-weight-bold text-dark">{{ $queue->deadline ? $queue->deadline->format('d M Y') : '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2.5 bg-white text-sm">
                            <span class="text-muted"><i class="fas fa-hourglass-half mr-2 text-warning"></i>Sisa Waktu</span>
                            @php
                                $daysRem = $queue->getDaysRemaining();
                            @endphp
                            @if($daysRem !== null)
                                @if($daysRem < 0)
                                    <span class="badge badge-danger px-2 py-1" style="box-shadow: none; border: none;">Terlambat {{ abs($daysRem) }} hari</span>
                                @elseif($daysRem <= 3)
                                    <span class="badge badge-warning text-dark px-2 py-1" style="box-shadow: none; border: none;">{{ $daysRem }} hari lagi</span>
                                @else
                                    <span class="badge badge-success px-2 py-1" style="box-shadow: none; border: none;">{{ $daysRem }} hari lagi</span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2.5 bg-white text-sm">
                            <span class="text-muted"><i class="fas fa-chart-line mr-2 text-primary"></i>Total Log</span>
                            <span class="font-weight-bold text-dark">{{ $queue->progressLogs()->count() }} Aktivitas</span>
                        </li>
                    </ul>

                    <!-- Action Buttons -->
                    @if($queue->projectRequest)
                        <div class="d-flex flex-column gap-2 mt-3">
                            <a href="{{ route('project-requests.show', $queue->projectRequest) }}" class="btn btn-primary btn-block font-weight-bold shadow-sm py-2" style="border-radius: 0.65rem;">
                                <i class="fas fa-file-alt mr-1"></i> Lihat Detail Proyek
                            </a>
                            <a href="{{ route('berita-acara.pdf', $queue->projectRequest) }}" class="btn btn-outline-danger btn-block font-weight-bold shadow-sm py-2" style="border-radius: 0.65rem;">
                                <i class="fas fa-file-pdf mr-1"></i> Export Berita Acara (PDF)
                            </a>
                            <button type="button" class="btn btn-outline-dark btn-block font-weight-bold shadow-sm py-2" style="border-radius: 0.65rem;" onclick="openBeritaAcaraModal('{{ route('berita-acara.print', $queue->projectRequest) }}')">
                                <i class="fas fa-print mr-1"></i> Pratinjau / Cetak BA
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Project Description Card -->
            <div class="card project-sidebar-card mb-4">
                <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                    <h5 class="card-title font-weight-bold mb-0 text-dark" style="font-size: 1.05rem;">
                        <i class="fas fa-align-left text-muted mr-1"></i> Deskripsi Pekerjaan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-0 text-justify" style="text-align: justify; text-justify: inter-word; white-space: pre-line; line-height: 1.6; font-size: 0.9rem;">
                        {{ $queue->description ?: 'Tidak ada deskripsi rinci untuk proyek ini.' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Column: Interactive Timeline & Update Form -->
        <div class="col-lg-8 col-md-7 mb-4">
            <div class="card project-sidebar-card">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <!-- Segmented Modern Tabs -->
                    <ul class="nav timeline-nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="#timeline" data-toggle="tab">
                                <i class="fas fa-stream text-primary"></i> Timeline & Riwayat
                                <span class="badge-counter">{{ $queue->progressLogs()->count() }}</span>
                            </a>
                        </li>
                        @if(auth()->user()->hasRole(['developer', 'admin', 'super_admin']))
                            <li class="nav-item">
                                <a class="nav-link" href="#update" data-toggle="tab">
                                    <i class="fas fa-plus-circle text-success"></i> Perbarui Progres
                                </a>
                            </li>
                        @endif
                    </ul>

                    <div class="text-muted text-xs font-weight-bold d-none d-sm-block">
                        <i class="fas fa-sync-alt mr-1"></i> Terakhir diupdate: {{ $queue->updated_at ? $queue->updated_at->diffForHumans() : '-' }}
                    </div>
                </div>

                <div class="card-body px-4 pb-4 pt-2">
                    <div class="tab-content mt-3">
                        <!-- TAB 1: MODERN TIMELINE -->
                        <div class="active tab-pane" id="timeline">
                            @php
                                $logs = $queue->progressLogs()
                                    ->with(['projectStage', 'updatedBy'])
                                    ->orderBy('created_at', 'desc')
                                    ->get();
                                $groupedLogs = $logs->groupBy(function($item) {
                                    return $item->created_at->format('Y-m-d');
                                });
                            @endphp

                            <div class="modern-timeline-container">
                                @forelse($groupedLogs as $dateKey => $dateLogs)
                                    @php
                                        $dateObj = \Carbon\Carbon::parse($dateKey);
                                        $isToday = $dateObj->isToday();
                                        $isYesterday = $dateObj->isYesterday();
                                    @endphp

                                    <!-- Date Section Chip -->
                                    <div class="timeline-date-group">
                                        <span class="timeline-date-chip {{ $isToday ? 'is-today' : '' }}">
                                            <i class="far fa-calendar-alt"></i>
                                            @if($isToday)
                                                Hari Ini ({{ $dateObj->format('d M Y') }})
                                            @elseif($isYesterday)
                                                Kemarin ({{ $dateObj->format('d M Y') }})
                                            @else
                                                {{ $dateObj->format('d M Y') }}
                                            @endif
                                        </span>
                                    </div>

                                    <!-- Entries for this date -->
                                    @foreach($dateLogs as $log)
                                        @php
                                            $stage = $log->projectStage;
                                            $rawIcon = $stage->icon ?? 'fas fa-tasks';
                                            if (!str_starts_with($rawIcon, 'fa-') && !str_starts_with($rawIcon, 'fas ') && !str_starts_with($rawIcon, 'far ') && !str_starts_with($rawIcon, 'fab ')) {
                                                $iconClass = 'fas fa-' . $rawIcon;
                                            } else {
                                                $iconClass = $rawIcon;
                                            }
                                            $stageColor = $stage->color ?? '#0055FF';
                                            $userName = $log->updatedBy->name ?? 'Tim Developer';
                                            $userInitial = strtoupper(substr($userName, 0, 1));
                                        @endphp

                                        <div class="timeline-entry">
                                            <!-- Marker Node -->
                                            <div class="timeline-node-icon" style="background: {{ $stageColor }};">
                                                <i class="{{ $iconClass }}"></i>
                                            </div>

                                            <!-- Card Box -->
                                            <div class="timeline-card-box">
                                                <!-- Header -->
                                                <div class="timeline-card-header">
                                                    <div class="timeline-user-meta">
                                                        <div class="timeline-user-avatar">
                                                            {{ $userInitial }}
                                                        </div>
                                                        <div>
                                                            <div class="timeline-user-name">{{ $userName }}</div>
                                                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                                <span class="timeline-stage-tag" style="background: {{ $stageColor }}15; color: {{ $stageColor }}; border-color: {{ $stageColor }}40;">
                                                                    <i class="{{ $iconClass }} text-xs"></i> {{ $stage->name ?? 'Update Progres' }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="timeline-time-meta">
                                                        <i class="far fa-clock"></i> {{ $log->created_at->format('H:i') }} WIB
                                                    </div>
                                                </div>

                                                <!-- Description -->
                                                <div class="timeline-description">
                                                    {{ $log->activity_description }}
                                                </div>

                                                <!-- Attachment (Clean, Modern Card) -->
                                                @if($log->attachment_path)
                                                    <div class="timeline-attachment-card">
                                                        <div class="timeline-file-info">
                                                            <div class="timeline-file-icon-box">
                                                                <i class="fas {{ $log->file_icon_class }}"></i>
                                                            </div>
                                                            <div style="min-width: 0;">
                                                                <div class="timeline-file-name" title="{{ $log->attachment_name }}">
                                                                    {{ $log->attachment_name }}
                                                                </div>
                                                                <div class="timeline-file-type">
                                                                    {{ strtoupper(pathinfo($log->attachment_name, PATHINFO_EXTENSION) ?: 'FILE') }}
                                                                    @if($log->attachment_type)
                                                                        • {{ $log->attachment_type }}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="timeline-attachment-actions">
                                                            <button type="button"
                                                                class="btn-attachment-preview requirement-preview-btn"
                                                                data-toggle="modal"
                                                                data-target="#requirementPreviewModal"
                                                                data-url="{{ route('progress.attachment.view', $log) }}"
                                                                data-download-url="{{ route('progress.attachment.download', $log) }}"
                                                                data-name="{{ $log->attachment_name }}"
                                                                data-mime="{{ $log->attachment_type ?? '' }}"
                                                                data-previewable="{{ $log->is_previewable ? '1' : '0' }}"
                                                                title="Lihat Pratinjau Lampiran">
                                                                <i class="fas fa-eye"></i> Lihat
                                                            </button>

                                                            <a href="{{ route('progress.attachment.download', $log) }}" 
                                                               class="btn-attachment-download" 
                                                               title="Unduh Lampiran">
                                                                <i class="fas fa-download"></i> Unduh
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Progress Indicator at this log -->
                                                <div class="timeline-progress-section">
                                                    <div class="timeline-progress-meta">
                                                        <span class="timeline-progress-label">Progres Tahap</span>
                                                        <span class="timeline-progress-badge badge-light text-primary font-weight-bold">
                                                            {{ $log->progress_percentage }}% Selesai
                                                        </span>
                                                    </div>
                                                    <div class="timeline-progress-bar-track">
                                                        <div class="timeline-progress-bar-fill" style="width: {{ $log->progress_percentage }}%;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @empty
                                    <div class="text-center py-5 bg-light rounded-xl border mb-4">
                                        <div class="mb-3">
                                            <i class="fas fa-clipboard-list text-muted" style="font-size: 3rem; opacity: 0.4;"></i>
                                        </div>
                                        <h5 class="font-weight-bold text-dark">Belum ada aktivitas tercatat</h5>
                                        <p class="text-muted text-sm max-w-md mx-auto">
                                            Aktivitas dan pembaruan progres dari tim developer akan ditampilkan secara terstruktur di sini.
                                        </p>
                                    </div>
                                @endforelse

                                <!-- Initial Milestone: Project Created -->
                                @if($queue->created_at)
                                    <div class="timeline-date-group">
                                        <span class="timeline-date-chip">
                                            <i class="far fa-flag"></i> {{ $queue->created_at->format('d M Y') }}
                                        </span>
                                    </div>
                                    <div class="timeline-entry mb-0">
                                        <div class="timeline-node-icon" style="background: #64748b;">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="timeline-milestone-box">
                                            <i class="fas fa-rocket text-primary fa-lg"></i>
                                            <div>
                                                <strong>Proyek Dimulai / Masuk Antrian Sistem</strong>
                                                <span class="text-muted d-block text-xs">Tiket proyek resmi dibuat dan didaftarkan ke antrian pengerjaan.</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- TAB 2: UPDATE PROGRESS FORM (Developers/Admins) -->
                        @if(auth()->user()->hasRole(['developer', 'admin', 'super_admin']))
                            <div class="tab-pane" id="update">
                                <!-- Form 1: Update Stage & Progress -->
                                <div class="form-section-card">
                                    <div class="form-section-title">
                                        <i class="fas fa-tasks text-primary"></i> Perbarui Tahapan & Persentase Progres
                                    </div>

                                    <form action="{{ route('progress.update-stage', $queue) }}" method="POST" enctype="multipart/form-data">
                                        @csrf

                                        <!-- Stage Selection -->
                                        <div class="form-group mb-4">
                                            <label for="stage_id" class="font-weight-bold text-dark text-sm mb-1">
                                                Tahapan Proyek Saat Ini <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control font-weight-bold" name="stage_id" id="stage_id" required style="border-radius: 0.65rem;">
                                                @foreach($stages as $stage)
                                                    <option value="{{ $stage->id }}" {{ ($currentStage->project_stage_id ?? '') == $stage->id ? 'selected' : '' }}>
                                                        {{ $stage->order }}. {{ $stage->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">Pilih fase tahapan yang sedang dikerjakan saat ini.</small>
                                        </div>

                                        <!-- Progress Slider + Number Input -->
                                        <div class="form-group mb-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label for="progress_percentage" class="font-weight-bold text-dark text-sm mb-0">
                                                    Persentase Capaian Progres <span class="text-danger">*</span>
                                                </label>
                                                <span class="badge badge-primary px-3 py-1 font-weight-800" id="progressValBadge" style="font-size: 0.95rem; border-radius: 9999px; box-shadow: none;">
                                                    {{ $queue->progress }}%
                                                </span>
                                            </div>

                                            <div class="d-flex align-items-center gap-3">
                                                <input type="range" 
                                                       class="custom-range-slider flex-1" 
                                                       id="progressRange" 
                                                       min="0" 
                                                       max="100" 
                                                       value="{{ $queue->progress }}" 
                                                       oninput="syncProgressInput(this.value)">
                                                
                                                <input type="number" 
                                                       class="form-control text-center font-weight-bold" 
                                                       id="progress_percentage" 
                                                       name="progress_percentage" 
                                                       min="0" 
                                                       max="100" 
                                                       value="{{ $queue->progress }}" 
                                                       style="width: 85px; border-radius: 0.65rem;"
                                                       oninput="syncProgressSlider(this.value)" 
                                                       required>
                                            </div>

                                            <!-- Preset Quick Percentage Buttons -->
                                            <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
                                                <span class="text-xs text-muted font-weight-bold">Pintasan:</span>
                                                <button type="button" class="preset-pill-btn" onclick="setProgressValue(25)">25%</button>
                                                <button type="button" class="preset-pill-btn" onclick="setProgressValue(50)">50%</button>
                                                <button type="button" class="preset-pill-btn" onclick="setProgressValue(75)">75%</button>
                                                <button type="button" class="preset-pill-btn" onclick="setProgressValue(100)">100% (Selesai)</button>
                                            </div>
                                        </div>

                                        <!-- Activity Description -->
                                        <div class="form-group mb-4">
                                            <label for="activity_description" class="font-weight-bold text-dark text-sm mb-1">
                                                Deskripsi & Catatan Pengerjaan <span class="text-danger">*</span>
                                            </label>
                                            <textarea class="form-control" 
                                                      id="activity_description" 
                                                      name="activity_description" 
                                                      rows="4" 
                                                      placeholder="Jelaskan detail kemajuan, modul yang selesai, atau kendala teknis..." 
                                                      style="border-radius: 0.65rem;" 
                                                      required></textarea>
                                            <small class="form-text text-muted">Deskripsi ini akan otomatis dikirimkan sebagai notifikasi ke klien dan manajemen.</small>
                                        </div>

                                        <!-- File Attachment Dropzone -->
                                        <div class="form-group mb-4">
                                            <label class="font-weight-bold text-dark text-sm mb-1">
                                                Lampiran Bukti / Tangkapan Layar (Opsional)
                                            </label>
                                            <div class="file-dropzone-styled" onclick="document.getElementById('attachmentInput').click()">
                                                <input type="file" class="d-none" id="attachmentInput" name="attachment" onchange="handleFileSelected(this)">
                                                <i class="fas fa-cloud-upload-alt text-primary fa-2x mb-2"></i>
                                                <p class="font-weight-bold text-sm mb-1 text-dark" id="fileSelectedText">
                                                    Klik untuk mengunggah tangkapan layar atau dokumen pendukung
                                                </p>
                                                <small class="text-muted d-block">Mendukung Gambar (PNG, JPG), Dokumen PDF, ZIP (Maksimal 10MB)</small>
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <button type="submit" class="btn btn-primary btn-block font-weight-800 py-2.5 shadow-sm" style="border-radius: 0.75rem; font-size: 0.95rem;">
                                            <i class="fas fa-paper-plane mr-2"></i> Simpan & Perbarui Progres Proyek
                                        </button>
                                    </form>
                                </div>

                                <!-- Form 2: Quick Activity Note -->
                                <div class="form-section-card">
                                    <div class="form-section-title">
                                        <i class="fas fa-sticky-note text-warning"></i> Catatan Cepat (Tanpa Mengubah Tahap)
                                    </div>

                                    <form action="{{ route('progress.log-activity', $queue) }}" method="POST">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <textarea class="form-control" 
                                                      name="activity_description" 
                                                      rows="2" 
                                                      placeholder="Tulis catatan aktivitas tambahan pada tahap aktif..." 
                                                      style="border-radius: 0.65rem;" 
                                                      required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-outline-dark font-weight-bold px-4" style="border-radius: 0.5rem;">
                                            <i class="fas fa-plus mr-1"></i> Tambah Catatan Cepat
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pratinjau Berita Acara -->
@if($queue->projectRequest)
<div class="modal fade" id="modalBeritaAcara" tabindex="-1" role="dialog" aria-labelledby="modalBeritaAcaraTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width: 90vw;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
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

@include('layouts.partials.requirement-preview-modal')

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

    function syncProgressInput(val) {
        var numInput = document.getElementById('progress_percentage');
        var badge = document.getElementById('progressValBadge');
        if (numInput) numInput.value = val;
        if (badge) badge.textContent = val + '%';
    }

    function syncProgressSlider(val) {
        var rangeInput = document.getElementById('progressRange');
        var badge = document.getElementById('progressValBadge');
        if (rangeInput) rangeInput.value = val;
        if (badge) badge.textContent = val + '%';
    }

    function setProgressValue(val) {
        var numInput = document.getElementById('progress_percentage');
        var rangeInput = document.getElementById('progressRange');
        var badge = document.getElementById('progressValBadge');
        if (numInput) numInput.value = val;
        if (rangeInput) rangeInput.value = val;
        if (badge) badge.textContent = val + '%';
    }

    function handleFileSelected(input) {
        var textEl = document.getElementById('fileSelectedText');
        if (input.files && input.files[0]) {
            var file = input.files[0];
            var sizeKb = Math.round(file.size / 1024);
            textEl.innerHTML = '<span class="text-success"><i class="fas fa-check-circle mr-1"></i> ' + file.name + ' (' + sizeKb + ' KB)</span>';
        }
    }
</script>
@endpush
