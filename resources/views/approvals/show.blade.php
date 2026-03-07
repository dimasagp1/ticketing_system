@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('approvals.index') }}">Persetujuan</a></li>
    <li class="breadcrumb-item active">Tinjau</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card support-shell-card mb-4">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Informasi Proyek</h3>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                <table class="table table-borderless detail-table mb-0">
                    <tr>
                        <th width="200">Nama Proyek:</th>
                        <td>{{ $approval->projectRequest->project_name }}</td>
                    </tr>
                    <tr>
                        <th>Klien:</th>
                        <td>{{ $approval->projectRequest->client->name }} ({{ $approval->projectRequest->client->email }})</td>
                    </tr>
                    <tr>
                        <th>Durasi:</th>
                        <td>{{ $approval->projectRequest->estimated_duration ? $approval->projectRequest->estimated_duration . ' hari' : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi:</th>
                        <td>{{ $approval->projectRequest->description }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card support-shell-card mb-4">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Berkas Kebutuhan</h3>
            </div>
            <div class="card-body p-0">
                @if($approval->projectRequest->requirements->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="pl-4 border-bottom-0">Nama Berkas</th>
                                    <th class="d-none d-md-table-cell border-bottom-0">Ukuran</th>
                                    <th class="pr-4 border-bottom-0">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($approval->projectRequest->requirements as $req)
                                <tr>
                                        <td class="pl-4"><i class="fas fa-file text-primary mr-2"></i> {{ $req->file_name }}</td>
                                        <td class="d-none d-md-table-cell">{{ $req->file_size_formatted }}</td>
                                        <td class="pr-4">
                                            <a href="{{ route('project-requirements.download', $req) }}" class="btn btn-sm btn-light text-primary">
                                                <i class="fas fa-download mr-1"></i> Unduh
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Belum ada berkas diunggah</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card support-shell-card mb-4" style="border-top: 3px solid var(--theme-green) !important;">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Setujui Proyek</h3>
            </div>
            <form action="{{ route('approvals.approve', $approval) }}" method="POST">
                @csrf
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="form-group mb-0">
                        <label class="text-muted font-weight-500">Komentar (Opsional)</label>
                        <textarea name="comments" class="form-control" rows="3" placeholder="Tambahkan komentar persetujuan..." style="border-radius: 0.5rem;"></textarea>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
                    <button type="submit" class="btn btn-success btn-block font-weight-500 shadow-sm" style="border-radius: 0.5rem;">
                        <i class="fas fa-check mr-2"></i> Setujui & Buat Antrian
                    </button>
                </div>
            </form>
        </div>

        <div class="card support-shell-card mb-4" style="border-top: 3px solid var(--theme-orange) !important;">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Tolak Proyek</h3>
            </div>
            <form action="{{ route('approvals.reject', $approval) }}" method="POST">
                @csrf
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="form-group mb-0">
                        <label class="text-muted font-weight-500">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="comments" class="form-control" rows="3" placeholder="Jelaskan alasan proyek ditolak..." required style="border-radius: 0.5rem;"></textarea>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
                    <button type="submit" class="btn btn-danger btn-block font-weight-500 shadow-sm" style="border-radius: 0.5rem;">
                        <i class="fas fa-times mr-2"></i> Tolak Proyek
                    </button>
                </div>
            </form>
        </div>

        <div class="card support-shell-card mb-4" style="border-top: 3px solid var(--theme-blue) !important;">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Minta Revisi</h3>
            </div>
            <form action="{{ route('approvals.request-revision', $approval) }}" method="POST">
                @csrf
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="form-group mb-0">
                        <label class="text-muted font-weight-500">Catatan Revisi <span class="text-danger">*</span></label>
                        <textarea name="revision_notes" class="form-control" rows="3" placeholder="Jelaskan bagian yang perlu direvisi..." required style="border-radius: 0.5rem;"></textarea>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
                    <button type="submit" class="btn btn-warning btn-block font-weight-500 shadow-sm" style="border-radius: 0.5rem;">
                        <i class="fas fa-edit mr-2"></i> Kirim Permintaan Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
