@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('daily-logs.index') }}">Log Harian</a></li>
    <li class="breadcrumb-item active">Detail Log</li>
@endsection

@push('styles')
<style>
    .detail-card {
        border: 1px solid #e2e8f0;
        border-radius: .85rem;
        padding: 1.1rem 1.25rem;
        margin-bottom: 1rem;
        background: #fff;
    }
    .detail-card-title {
        font-size: .78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #64748b;
        margin-bottom: .85rem;
        display: flex;
        align-items: center;
        gap: .4rem;
    }
    .detail-row {
        display: flex;
        flex-wrap: wrap;
        padding: .55rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .detail-row:last-child { border-bottom: 0; }
    .detail-label {
        width: 155px;
        flex-shrink: 0;
        font-size: .8rem;
        font-weight: 600;
        color: #94a3b8;
        padding-right: .75rem;
    }
    .detail-value {
        flex: 1;
        font-size: .86rem;
        color: #1f2d3d;
        min-width: 0;
    }
    .source-chip {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .3em .7em;
        border-radius: 2rem;
        font-size: .8rem;
        font-weight: 600;
        background: #f1f5f9;
        color: #374151;
    }
    .content-block {
        background: #f8fafc;
        border-radius: .6rem;
        padding: .75rem .9rem;
        font-size: .86rem;
        line-height: 1.65;
        color: #374151;
        white-space: pre-wrap;
        word-break: break-word;
    }
    .show-header-actions {
        display: flex;
        gap: .4rem;
        flex-shrink: 0;
    }
    @media (max-width: 575px) {
        .detail-label { width: 100%; margin-bottom: .1rem; font-size: .75rem; }
        .detail-row { flex-direction: column; padding: .5rem 0; }
        .show-top-bar { flex-direction: column; align-items: stretch !important; }
        .show-header-actions .btn { flex: 1; font-size: .82rem; }
        .status-banner { flex-wrap: wrap; }
        .status-banner .ml-auto { margin-left: 0 !important; width: 100%; margin-top: .4rem; }
        .detail-card { padding: .85rem .9rem; }
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-10 col-xl-8">

        {{-- Header --}}
        <div class="d-flex align-items-start justify-content-between mb-3 show-top-bar" style="gap:.65rem;">
            <div class="d-flex align-items-center" style="gap:.65rem;">
                <a href="{{ route('daily-logs.index') }}" class="btn btn-light border btn-sm" style="border-radius:.5rem;flex-shrink:0;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h4 class="mb-0 font-weight-bold" style="font-size:1.05rem;">Detail Log Harian</h4>
                    <p class="text-muted mb-0" style="font-size:.78rem;">
                        ID #{{ $dailyLog->id }} &nbsp;|&nbsp; Dicatat {{ $dailyLog->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
            <div class="show-header-actions">
                <a href="{{ route('daily-logs.edit', $dailyLog) }}" class="btn btn-warning btn-sm" style="border-radius:.55rem;">
                    <i class="fas fa-pencil-alt mr-1"></i> Edit
                </a>
                <button type="button" class="btn btn-danger btn-sm" id="btnDelete" style="border-radius:.55rem;">
                    <i class="fas fa-trash mr-1"></i> Hapus
                </button>
                <form id="deleteForm" action="{{ route('daily-logs.destroy', $dailyLog) }}" method="POST" class="d-none">
                    @csrf @method('DELETE')
                </form>
            </div>
        </div>

        {{-- Status Banner --}}
        <div class="alert status-banner
            @if($dailyLog->status === 'Selesai') alert-success
            @elseif($dailyLog->status === 'Pending') alert-warning
            @else alert-danger
            @endif mb-3 d-flex align-items-center" style="border-radius:.75rem; gap:.5rem;flex-wrap:wrap;">
            @if($dailyLog->status === 'Selesai')
                <i class="fas fa-check-circle fa-lg"></i>
            @elseif($dailyLog->status === 'Pending')
                <i class="fas fa-hourglass-half fa-lg"></i>
            @else
                <i class="fas fa-ticket-alt fa-lg"></i>
            @endif
            <span class="font-weight-bold">Status: {{ $dailyLog->status }}</span>
            @if($dailyLog->duration_minutes)
                @php
                    $dur = $dailyLog->duration_minutes;
                    $durLabel = $dur >= 60
                        ? floor($dur / 60) . ' jam ' . ($dur % 60) . ' menit'
                        : $dur . ' menit';
                @endphp
                <span class="ml-auto" style="font-size:.82rem;">
                    <i class="fas fa-stopwatch mr-1"></i>
                    Durasi: {{ $durLabel }}
                </span>
            @endif
        </div>

        {{-- Info Pelapor --}}
        <div class="detail-card">
            <div class="detail-card-title">
                <i class="fas fa-user text-primary"></i> Informasi Pelapor
            </div>
            <div class="detail-row">
                <div class="detail-label">Tanggal Penanganan</div>
                <div class="detail-value font-weight-bold">
                    {{ $dailyLog->log_date->translatedFormat('l, d F Y') }}
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Nama Pelapor</div>
                <div class="detail-value font-weight-bold">{{ $dailyLog->reporter_name }}</div>
            </div>
            @if($dailyLog->department)
            <div class="detail-row">
                <div class="detail-label">Departemen</div>
                <div class="detail-value">{{ $dailyLog->department }}</div>
            </div>
            @endif
            @if($dailyLog->contact_info)
            <div class="detail-row">
                <div class="detail-label">Kontak</div>
                <div class="detail-value">{{ $dailyLog->contact_info }}</div>
            </div>
            @endif
            <div class="detail-row">
                <div class="detail-label">Sumber Komplain</div>
                <div class="detail-value">
                    <span class="source-chip">
                        <i class="{{ $dailyLog->source_icon }}"></i>
                        {{ $dailyLog->source }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Detail Masalah --}}
        <div class="detail-card">
            <div class="detail-card-title">
                <i class="fas fa-exclamation-triangle text-primary"></i> Detail Masalah
            </div>
            <div class="mb-1 small font-weight-600 text-muted">Deskripsi Masalah / Komplain</div>
            <div class="content-block mb-3">{{ $dailyLog->issue_description }}</div>

            <div class="mb-1 small font-weight-600 text-muted">Tindakan / Solusi yang Dilakukan</div>
            <div class="content-block">{{ $dailyLog->action_taken }}</div>

            @if($dailyLog->notes)
            <div class="mt-3">
                <div class="mb-1 small font-weight-600 text-muted">Catatan Tambahan</div>
                <div class="content-block" style="background:#fffbeb; border:1px solid #fde68a;">{{ $dailyLog->notes }}</div>
            </div>
            @endif
        </div>

        {{-- Meta --}}
        <div class="text-muted text-center mb-4" style="font-size:.78rem;">
            Dicatat oleh <strong>{{ $dailyLog->user->name }}</strong>
            &middot; {{ $dailyLog->created_at->format('d M Y, H:i') }}
            @if($dailyLog->updated_at->ne($dailyLog->created_at))
                &middot; Diedit {{ $dailyLog->updated_at->diffForHumans() }}
            @endif
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('btnDelete').addEventListener('click', function() {
    Swal.fire({
        title: 'Hapus Log?',
        html: 'Log ini akan dihapus permanen dan tidak bisa dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm').submit();
        }
    });
});
</script>
@endpush
