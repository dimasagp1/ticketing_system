@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">Super Admin</a></li>
    <li class="breadcrumb-item active">Log Aktivitas</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card support-shell-card mb-4">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Log Aktivitas Sistem</h3>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                <div class="table-responsive">
                    <table class="table table-hover align-middle data-table mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-4 border-bottom-0">ID</th>
                                <th class="border-bottom-0">Pengguna</th>
                                <th class="border-bottom-0">Aksi</th>
                                <th class="d-none d-md-table-cell border-bottom-0">Deskripsi</th>
                                <th class="d-none d-lg-table-cell border-bottom-0">Alamat IP</th>
                                <th class="pr-4 border-bottom-0 text-right">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td class="pl-4">{{ $log->id }}</td>
                                    <td class="font-weight-600 text-dark">{{ $log->user ? $log->user->name : 'Sistem' }}</td>
                                    <td><span class="badge badge-info">{{ $log->action }}</span></td>
                                    <td class="d-none d-md-table-cell text-muted">{{ $log->description }}</td>
                                    <td class="d-none d-lg-table-cell text-muted">{{ $log->ip_address }}</td>
                                    <td class="pr-4 text-right text-muted small"><i class="far fa-clock mr-1"></i> {{ $log->created_at->format('d M Y H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
