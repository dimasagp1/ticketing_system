@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ auth()->user()->isSuperAdmin() ? route('super-admin.dashboard') : route('dashboard') }}">{{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Dasbor' }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('super-admin.users.index') }}">Pengguna</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card support-shell-card mb-4">
            <div class="card-body box-profile px-4 pb-4 pt-4">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle" 
                         src="{{ $user->avatar_url }}" 
                         alt="User profile picture">
                </div>
                <h3 class="profile-username text-center">{{ $user->name }}</h3>
                <p class="text-muted text-center">{{ ucfirst($user->role) }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Email</b> <span class="float-right">{{ $user->email }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Telepon</b> <span class="float-right">{{ $user->phone ?? '-' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Perusahaan</b> <span class="float-right">{{ $user->company ?? '-' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Status</b> 
                        <span class="float-right">
                            @if($user->status == 'active')
                                <span class="badge badge-success">Aktif</span>
                            @elseif($user->status == 'suspended')
                                <span class="badge badge-danger">Ditangguhkan</span>
                            @else
                                <span class="badge badge-secondary">Tidak Aktif</span>
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>Bergabung Sejak</b> <span class="float-right">{{ $user->created_at->format('d M Y') }}</span>
                    </li>
                </ul>

                @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('super-admin.users.edit', $user) }}" class="btn btn-primary btn-block font-weight-500 shadow-sm mt-4" style="border-radius: 0.5rem;">
                    <i class="fas fa-edit mr-2"></i> Ubah Pengguna
                </a>
                @endif

                @if(auth()->user()->canActivateUsers() && $user->status !== 'active' && !(auth()->user()->isAdmin() && $user->isSuperAdmin()))
                <form action="{{ route('super-admin.users.activate', $user) }}" method="POST" class="mt-2" id="activate-form-{{ $user->id }}">
                    @csrf
                    <button type="button" class="btn btn-success btn-block font-weight-500 shadow-sm" style="border-radius: 0.5rem;" onclick="confirmAction('activate-form-{{ $user->id }}', 'Aktifkan akun?', 'Akun ini akan diaktifkan kembali agar dapat masuk ke sistem.', 'Ya, aktifkan', '#10b981', 'question')">
                        <i class="fas fa-user-check mr-2"></i> Aktifkan Akun
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card support-shell-card mb-4">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Bio</h3>
            </div>
            <div class="card-body">
                <p>{{ $user->bio ?? 'Belum ada bio.' }}</p>
            </div>
        </div>

        @if($user->isClient())
            <div class="card support-shell-card mb-4">
                <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                    <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Permintaan Proyek</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="pl-4 border-bottom-0">Nama Proyek</th>
                                    <th class="border-bottom-0">Status</th>
                                    <th class="pr-4 d-none d-md-table-cell border-bottom-0">Diajukan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($user->projectRequests()->latest()->take(5)->get() as $request)
                                    <tr>
                                        <td class="pl-4 font-weight-600 text-dark">{{ $request->project_name }}</td>
                                        <td><span class="badge badge-{{ $request->request_status_badge_class }}">{{ $request->request_status_label }}</span></td>
                                        <td class="pr-4 d-none d-md-table-cell">{{ $request->created_at->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Belum ada permintaan proyek</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @if($user->isDeveloper())
            <div class="card support-shell-card mb-4">
                <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                    <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Proyek Ditugaskan</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="pl-4 border-bottom-0">Nama Proyek</th>
                                    <th class="border-bottom-0">Status</th>
                                    <th class="pr-4 d-none d-md-table-cell border-bottom-0">Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($user->assignedQueues()->latest()->take(5)->get() as $queue)
                                    <tr>
                                        <td class="pl-4 font-weight-600 text-dark">{{ $queue->project_name }}</td>
                                        <td><span class="badge badge-info">{{ $queue->status }}</span></td>
                                        <td class="pr-4 d-none d-md-table-cell">{{ $queue->progress }}%</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Belum ada proyek ditugaskan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
