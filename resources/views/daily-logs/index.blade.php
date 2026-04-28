@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    <li class="breadcrumb-item active">Log Harian</li>
@endsection

@push('styles')
<style>
    .stat-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .log-source-badge {
        font-size: .72rem;
        font-weight: 600;
        padding: .28em .6em;
        border-radius: .375rem;
        white-space: nowrap;
    }
    .filter-section {
        background: #f8fafc;
        border-radius: .75rem;
        padding: .85rem 1rem;
        border: 1px solid #e2e8f0;
    }

    /* ── Mobile card list (hidden on md+) ── */
    .log-card-item {
        border: 1px solid #e2e8f0;
        border-radius: .85rem;
        padding: .9rem 1rem;
        background: #fff;
        margin-bottom: .65rem;
        transition: box-shadow .2s;
    }
    .log-card-item:last-child { margin-bottom: 0; }
    .log-card-item:active { box-shadow: 0 4px 12px rgba(0,0,0,.08); }

    .log-card-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .4rem;
        margin-bottom: .5rem;
    }
    .log-card-desc {
        font-size: .83rem;
        color: #64748b;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: .6rem;
    }
    .log-card-actions {
        display: flex;
        gap: .35rem;
    }
    .log-card-actions .btn {
        flex: 1;
        font-size: .8rem;
        padding: .35rem .5rem;
        border-radius: .5rem;
    }

    @media (max-width: 767px) {
        .desktop-table { display: none !important; }
        .mobile-cards  { display: block !important; }
        .filter-section { padding: .75rem .85rem; }
        .support-stat-card { padding: .8rem .9rem; }
        .support-stat-value { font-size: 1.3rem; }
        .card-header { padding: .9rem 1rem !important; }
        .card-body   { padding: .85rem 1rem !important; }
    }
    @media (min-width: 768px) {
        .desktop-table { display: block !important; }
        .mobile-cards  { display: none !important; }
    }
</style>
@endpush

@section('content')
{{-- ── Stats Row ── --}}
<div class="row">
    <div class="col-6 col-md-3 mb-3">
        <div class="support-stat-card d-flex align-items-center" style="gap:.8rem;">
            <div class="stat-icon-wrap" style="background:rgba(37,99,235,.12);">
                <i class="fas fa-calendar-day" style="color:var(--theme-blue);"></i>
            </div>
            <div>
                <div class="support-stat-value">{{ $todayCount }}</div>
                <div class="support-stat-label">Hari Ini</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="support-stat-card d-flex align-items-center" style="gap:.8rem;">
            <div class="stat-icon-wrap" style="background:rgba(16,185,129,.12);">
                <i class="fas fa-calendar-alt" style="color:var(--theme-green);"></i>
            </div>
            <div>
                <div class="support-stat-value">{{ $monthCount }}</div>
                <div class="support-stat-label">Bulan Ini</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="support-stat-card d-flex align-items-center" style="gap:.8rem;">
            <div class="stat-icon-wrap" style="background:rgba(249,115,22,.12);">
                <i class="fas fa-clock" style="color:var(--theme-orange);"></i>
            </div>
            <div>
                <div class="support-stat-value">{{ $pendingCount }}</div>
                <div class="support-stat-label">Pending</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="support-stat-card d-flex align-items-center" style="gap:.8rem;">
            <div class="stat-icon-wrap" style="background:rgba(100,116,139,.1);">
                <i class="fas fa-hourglass-half" style="color:#64748b;"></i>
            </div>
            <div>
                <div class="support-stat-value">
                    @if($totalDuration >= 60)
                        {{ floor($totalDuration / 60) }}j {{ $totalDuration % 60 }}m
                    @else
                        {{ $totalDuration }}m
                    @endif
                </div>
                <div class="support-stat-label">Durasi Bulan Ini</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card support-shell-card">

            {{-- Card Header --}}
            <div class="card-header border-0 bg-white d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem; padding:1rem 1.25rem .6rem;">
                <div>
                    <h3 class="card-title mb-0 font-weight-bold" style="font-size:1.05rem;">
                        <i class="fas fa-book-open mr-2 text-primary"></i>Log Harian – Komplain Ad-Hoc
                    </h3>
                    <p class="text-muted mb-0 d-none d-md-block" style="font-size:.78rem;margin-top:.2rem;">Penanganan komplain di luar pengajuan tiket resmi</p>
                </div>
                <a href="{{ route('daily-logs.create') }}" class="btn btn-primary btn-sm shadow-sm" style="border-radius:.6rem;white-space:nowrap;">
                    <i class="fas fa-plus mr-1"></i> Tambah Log
                </a>
            </div>

            {{-- Filter (collapsible on mobile) --}}
            <div class="card-body pt-2 pb-0" style="padding-left:1.25rem;padding-right:1.25rem;">

                {{-- Toggle button (mobile only) --}}
                <div class="d-md-none mb-2">
                    <button class="btn btn-outline-secondary btn-sm btn-block" type="button"
                            data-toggle="collapse" data-target="#filterCollapse"
                            aria-expanded="false" style="border-radius:.6rem;font-size:.82rem;">
                        <i class="fas fa-filter mr-1"></i>
                        {{ request()->anyFilled(['date_from','date_to','status','source','month']) ? 'Filter Aktif ▾' : 'Filter ▾' }}
                    </button>
                </div>

                <div class="collapse d-md-block" id="filterCollapse">
                    <form action="{{ route('daily-logs.index') }}" method="GET" id="filterForm">
                        <div class="filter-section mb-3">
                            <div class="form-row align-items-end">
                                <div class="col-6 col-md-2 mb-2">
                                    <label class="small font-weight-600 text-muted mb-1 d-block">Dari</label>
                                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-6 col-md-2 mb-2">
                                    <label class="small font-weight-600 text-muted mb-1 d-block">Sampai</label>
                                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-6 col-md-2 mb-2">
                                    <label class="small font-weight-600 text-muted mb-1 d-block">Status</label>
                                    <select name="status" class="form-control form-control-sm">
                                        <option value="">Semua</option>
                                        @foreach(\App\Models\DailyLog::statuses() as $s)
                                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 col-md-2 mb-2">
                                    <label class="small font-weight-600 text-muted mb-1 d-block">Sumber</label>
                                    <select name="source" class="form-control form-control-sm">
                                        <option value="">Semua</option>
                                        @foreach(\App\Models\DailyLog::sources() as $src)
                                            <option value="{{ $src }}" {{ request('source') === $src ? 'selected' : '' }}>{{ $src }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 col-md-2 mb-2">
                                    <label class="small font-weight-600 text-muted mb-1 d-block">Bulan</label>
                                    <input type="month" name="month" class="form-control form-control-sm" value="{{ request('month') }}">
                                </div>
                                <div class="col-6 col-md-2 mb-2 d-flex" style="gap:.3rem;">
                                    <button type="submit" class="btn btn-primary btn-sm flex-fill" style="border-radius:.5rem;">
                                        <i class="fas fa-filter"></i>
                                    </button>
                                    <a href="{{ route('daily-logs.index') }}" class="btn btn-light border btn-sm flex-fill" style="border-radius:.5rem;" title="Reset">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ════ DESKTOP TABLE ════ --}}
            <div class="desktop-table table-responsive" style="padding:0 1.25rem 1.25rem;">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width:100px;">Tanggal</th>
                            <th>Pelapor</th>
                            <th class="d-none d-lg-table-cell">Dept.</th>
                            <th>Sumber</th>
                            <th>Masalah</th>
                            <th class="d-none d-lg-table-cell">Durasi</th>
                            <th>Status</th>
                            <th style="width:110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>
                                <span class="font-weight-600" style="font-size:.85rem;">{{ $log->log_date->format('d M Y') }}</span>
                                <br><small class="text-muted">{{ $log->log_date->diffForHumans() }}</small>
                            </td>
                            <td>
                                <span class="font-weight-600">{{ $log->reporter_name }}</span>
                                @if($log->contact_info)
                                    <br><small class="text-muted">{{ $log->contact_info }}</small>
                                @endif
                            </td>
                            <td class="d-none d-lg-table-cell text-muted" style="font-size:.85rem;">{{ $log->department ?? '-' }}</td>
                            <td>
                                <span class="log-source-badge badge badge-secondary">
                                    <i class="{{ $log->source_icon }} mr-1"></i>{{ $log->source }}
                                </span>
                            </td>
                            <td style="max-width:240px;">
                                <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;font-size:.85rem;">
                                    {{ $log->issue_description }}
                                </span>
                            </td>
                            <td class="d-none d-lg-table-cell" style="white-space:nowrap;font-size:.85rem;">
                                @if($log->duration_minutes)
                                    <i class="fas fa-stopwatch text-muted mr-1"></i>
                                    @php $d = $log->duration_minutes; @endphp
                                    {{ $d >= 60 ? floor($d/60).'j '.($d%60).'m' : $d.'m' }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $log->status_badge_class }}">{{ $log->status }}</span>
                            </td>
                            <td>
                                <div class="d-flex" style="gap:.25rem;">
                                    <a href="{{ route('daily-logs.show', $log) }}" class="btn btn-sm btn-light border" style="border-radius:.45rem;" title="Detail"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('daily-logs.edit', $log) }}" class="btn btn-sm btn-warning" style="border-radius:.45rem;" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                                    <button class="btn btn-sm btn-danger btn-delete" style="border-radius:.45rem;"
                                        data-id="{{ $log->id }}" data-name="{{ $log->reporter_name }}" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $log->id }}" action="{{ route('daily-logs.destroy', $log) }}" method="POST" class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-book-open fa-2x text-muted mb-2 d-block"></i>
                                <span class="text-muted">Belum ada log. <a href="{{ route('daily-logs.create') }}">Tambah sekarang</a>.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ════ MOBILE CARD LIST ════ --}}
            <div class="mobile-cards" style="padding:0 .85rem 1rem;">
                @forelse($logs as $log)
                <div class="log-card-item">
                    <div class="log-card-meta">
                        <div>
                            <span class="font-weight-bold" style="font-size:.92rem;">{{ $log->reporter_name }}</span>
                            @if($log->department)
                                <span class="text-muted" style="font-size:.78rem;"> · {{ $log->department }}</span>
                            @endif
                        </div>
                        <span class="badge badge-{{ $log->status_badge_class }}" style="font-size:.7rem;white-space:nowrap;">{{ $log->status }}</span>
                    </div>

                    <div class="d-flex align-items-center mb-2" style="gap:.5rem;flex-wrap:wrap;">
                        <small class="text-muted"><i class="fas fa-calendar-day mr-1"></i>{{ $log->log_date->format('d M Y') }}</small>
                        <span class="log-source-badge badge badge-secondary" style="font-size:.68rem;">
                            <i class="{{ $log->source_icon }} mr-1"></i>{{ $log->source }}
                        </span>
                        @if($log->duration_minutes)
                            @php $d = $log->duration_minutes; @endphp
                            <small class="text-muted"><i class="fas fa-stopwatch mr-1"></i>{{ $d >= 60 ? floor($d/60).'j '.($d%60).'m' : $d.'m' }}</small>
                        @endif
                    </div>

                    <div class="log-card-desc">{{ $log->issue_description }}</div>

                    <div class="log-card-actions">
                        <a href="{{ route('daily-logs.show', $log) }}" class="btn btn-light border">
                            <i class="fas fa-eye mr-1"></i>Detail
                        </a>
                        <a href="{{ route('daily-logs.edit', $log) }}" class="btn btn-warning">
                            <i class="fas fa-pencil-alt mr-1"></i>Edit
                        </a>
                        <button class="btn btn-danger btn-delete"
                            data-id="{{ $log->id }}" data-name="{{ $log->reporter_name }}">
                            <i class="fas fa-trash"></i>
                        </button>
                        <form id="delete-form-m-{{ $log->id }}" action="{{ route('daily-logs.destroy', $log) }}" method="POST" class="d-none">
                            @csrf @method('DELETE')
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="fas fa-book-open fa-2x text-muted mb-2 d-block"></i>
                    <span class="text-muted">Belum ada log. <a href="{{ route('daily-logs.create') }}">Tambah sekarang</a>.</span>
                </div>
                @endforelse
            </div>

            @if($logs->hasPages())
            <div class="card-footer bg-white border-0" style="padding:.5rem 1.25rem 1rem;">
                {{ $logs->links() }}
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.btn-delete').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const id   = this.dataset.id;
        const name = this.dataset.name;
        Swal.fire({
            title: 'Hapus Log?',
            html: `Log komplain dari <b>${name}</b> akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                // try desktop form first, fallback to mobile form
                const form = document.getElementById('delete-form-' + id)
                          || document.getElementById('delete-form-m-' + id);
                if (form) form.submit();
            }
        });
    });
});
</script>
@endpush
