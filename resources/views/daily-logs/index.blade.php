@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    <li class="breadcrumb-item active">Log Harian</li>
@endsection

@push('styles')
<style>
    .stat-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .log-source-badge {
        font-size: .72rem;
        font-weight: 600;
        padding: .28em .6em;
        border-radius: .375rem;
    }
    .log-row-hover:hover {
        background: #f8fafc;
        cursor: default;
    }
    .filter-section {
        background: #f8fafc;
        border-radius: .85rem;
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        border: 1px solid #e2e8f0;
    }
</style>
@endpush

@section('content')
<div class="row">
    {{-- Stats Row --}}
    <div class="col-6 col-md-3 mb-3">
        <div class="support-stat-card d-flex align-items-center gap-3" style="gap:.9rem;">
            <div class="stat-icon-wrap bg-primary" style="background:rgba(37,99,235,.12) !important;">
                <i class="fas fa-calendar-day" style="color:var(--theme-blue);"></i>
            </div>
            <div>
                <div class="support-stat-value">{{ $todayCount }}</div>
                <div class="support-stat-label">Hari Ini</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="support-stat-card d-flex align-items-center" style="gap:.9rem;">
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
        <div class="support-stat-card d-flex align-items-center" style="gap:.9rem;">
            <div class="stat-icon-wrap" style="background:rgba(249,115,22,.12);">
                <i class="fas fa-clock" style="color:var(--theme-orange);"></i>
            </div>
            <div>
                <div class="support-stat-value">{{ $pendingCount }}</div>
                <div class="support-stat-label">Masih Pending</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="support-stat-card d-flex align-items-center" style="gap:.9rem;">
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
                <div class="support-stat-label">Total Waktu Bulan Ini</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card support-shell-card">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2 d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                <div>
                    <h3 class="card-title mb-0 font-weight-bold" style="font-size:1.15rem;">
                        <i class="fas fa-book-open mr-2 text-primary"></i>Log Harian – Komplain Ad-Hoc
                    </h3>
                    <p class="text-muted mb-0 mt-1" style="font-size:.82rem;">Pencatatan penanganan komplain di luar pengajuan tiket resmi</p>
                </div>
                <div class="card-tools">
                    <a href="{{ route('daily-logs.create') }}" class="btn btn-primary btn-sm shadow-sm" style="border-radius:.6rem;">
                        <i class="fas fa-plus mr-1"></i> Tambah Log
                    </a>
                </div>
            </div>

            {{-- Filter --}}
            <div class="card-body px-4 pb-0 pt-3">
                <form action="{{ route('daily-logs.index') }}" method="GET" id="filterForm">
                    <div class="filter-section">
                        <div class="form-row align-items-end">
                            <div class="col-6 col-md-2 mb-2">
                                <label class="small font-weight-600 text-muted mb-1">Dari Tanggal</label>
                                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-6 col-md-2 mb-2">
                                <label class="small font-weight-600 text-muted mb-1">Sampai Tanggal</label>
                                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-6 col-md-2 mb-2">
                                <label class="small font-weight-600 text-muted mb-1">Status</label>
                                <select name="status" class="form-control form-control-sm">
                                    <option value="">Semua Status</option>
                                    @foreach(\App\Models\DailyLog::statuses() as $s)
                                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-2 mb-2">
                                <label class="small font-weight-600 text-muted mb-1">Sumber</label>
                                <select name="source" class="form-control form-control-sm">
                                    <option value="">Semua Sumber</option>
                                    @foreach(\App\Models\DailyLog::sources() as $src)
                                        <option value="{{ $src }}" {{ request('source') === $src ? 'selected' : '' }}>{{ $src }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-2 mb-2">
                                <label class="small font-weight-600 text-muted mb-1">Bulan Cepat</label>
                                <input type="month" name="month" class="form-control form-control-sm" value="{{ request('month') }}">
                            </div>
                            <div class="col-6 col-md-2 mb-2 d-flex" style="gap:.35rem;">
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

            {{-- Table --}}
            <div class="card-body px-4 pb-4 pt-0 table-responsive p-0">
                <table class="table table-hover mb-0" id="dailyLogTable">
                    <thead class="bg-light">
                        <tr>
                            <th width="90">Tanggal</th>
                            <th>Pelapor</th>
                            <th class="d-none d-md-table-cell">Dept.</th>
                            <th>Sumber</th>
                            <th>Masalah</th>
                            <th class="d-none d-md-table-cell">Durasi</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr class="log-row-hover">
                            <td>
                                <span class="font-weight-600" style="font-size:.88rem;">
                                    {{ $log->log_date->format('d M Y') }}
                                </span>
                                <br>
                                <small class="text-muted">{{ $log->log_date->diffForHumans() }}</small>
                            </td>
                            <td>
                                <span class="font-weight-600">{{ $log->reporter_name }}</span>
                                @if($log->contact_info)
                                    <br><small class="text-muted">{{ $log->contact_info }}</small>
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell text-muted" style="font-size:.88rem;">
                                {{ $log->department ?? '-' }}
                            </td>
                            <td>
                                <span class="log-source-badge badge badge-secondary">
                                    <i class="{{ $log->source_icon }} mr-1"></i>{{ $log->source }}
                                </span>
                            </td>
                            <td style="max-width:260px;">
                                <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;font-size:.88rem;">
                                    {{ $log->issue_description }}
                                </span>
                            </td>
                            <td class="d-none d-md-table-cell" style="white-space:nowrap;">
                                @if($log->duration_minutes)
                                    <i class="fas fa-stopwatch text-muted mr-1"></i>
                                    {{ $log->duration_minutes >= 60 ? floor($log->duration_minutes/60).'j '.($log->duration_minutes%60).'m' : $log->duration_minutes.'m' }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $log->status_badge_class }}">
                                    {{ $log->status }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex" style="gap:.3rem;">
                                    <a href="{{ route('daily-logs.show', $log) }}"
                                       class="btn btn-sm btn-light border" style="border-radius:.5rem;" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('daily-logs.edit', $log) }}"
                                       class="btn btn-sm btn-warning" style="border-radius:.5rem;" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <button type="button"
                                        class="btn btn-sm btn-danger btn-delete" style="border-radius:.5rem;"
                                        data-id="{{ $log->id }}"
                                        data-name="{{ $log->reporter_name }}"
                                        title="Hapus">
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
                                <span class="text-muted">Belum ada log. <a href="{{ route('daily-logs.create') }}">Tambah log pertama Anda</a>.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
            <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
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
                document.getElementById('delete-form-' + id).submit();
            }
        });
    });
});
</script>
@endpush
