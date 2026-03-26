@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ auth()->user()->isSuperAdmin() ? route('super-admin.dashboard') : route('dashboard') }}">{{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Dasbor' }}</a></li>
    <li class="breadcrumb-item active">Pengguna</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 1.25rem;">
            <div class="card-header border-0 bg-white pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center mr-3 d-none d-sm-flex" style="width: 48px; height: 48px; background-color: rgba(37, 99, 235, 0.1);">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.25rem;">Semua Pengguna</h3>
                        <p class="text-muted mb-0 small mt-1">{{ auth()->user()->isSuperAdmin() ? 'Kelola seluruh akun sistem' : 'Lihat akun pengguna dan aktifkan akun yang diperlukan' }}</p>
                    </div>
                </div>
                <div class="card-tools m-0">
                    @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('super-admin.users.create') }}" class="btn btn-primary shadow-sm" style="border-radius: 0.5rem; font-weight: 500;">
                        <i class="fas fa-plus mr-1"></i> <span class="d-none d-sm-inline">Tambah Pengguna</span>
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                <div class="bg-light px-4 py-3 border-bottom border-light">
                    <form method="GET" class="m-0">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <select name="role" class="form-control">
                                <option value="">Semua Role</option>
                                <option value="client" {{ request('role') == 'client' ? 'selected' : '' }}>Client</option>
                                {{-- <option value="developer" {{ request('role') == 'developer' ? 'selected' : '' }}>Developer</option> --}}
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="developer" {{ request('role') == 'developer' ? 'selected' : '' }}>Developer</option>
                                <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <select name="status" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="text" name="search" class="form-control" placeholder="Cari pengguna..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="submit" class="btn btn-primary btn-block shadow-sm" style="border-radius: 0.5rem; font-weight: 500;">Terapkan</button>
                        </div>
                    </div>
                </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-white">
                            <tr>
                                <th class="pl-4 border-bottom-0">ID</th>
                                <th class="border-bottom-0">Nama</th>
                                <th class="d-none d-md-table-cell border-bottom-0">Email</th>
                                <th class="border-bottom-0">Role</th>
                                <th class="border-bottom-0">Status</th>
                                <th class="d-none d-lg-table-cell border-bottom-0">Dibuat</th>
                                <th class="pr-4 border-bottom-0 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="pl-4 text-muted font-weight-500">{{ $user->id }}</td>
                                    <td><div class="font-weight-600 text-dark">{{ $user->name }}</div></td>
                                    <td class="d-none d-md-table-cell text-muted">{{ $user->email }}</td>
                                    <td>
                                        @if($user->role == 'super_admin')
                                            <span class="badge badge-danger">Super Admin</span>
                                        @elseif($user->role == 'admin')
                                            <span class="badge badge-warning">Admin</span>
                                        @elseif($user->role == 'developer')
                                            <span class="badge badge-info">Developer</span>
                                        @else
                                            <span class="badge badge-secondary">Client</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->status == 'active')
                                            <span class="badge badge-success">Aktif</span>
                                        @elseif($user->status == 'suspended')
                                            <span class="badge badge-danger">Ditangguhkan</span>
                                        @else
                                            <span class="badge badge-secondary">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-lg-table-cell text-muted small">{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="pr-4 text-right">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('super-admin.users.show', $user) }}" class="btn btn-light" title="Lihat">
                                                <i class="fas fa-eye text-info"></i>
                                            </a>
                                            @if(auth()->user()->isSuperAdmin())
                                            <a href="{{ route('super-admin.users.edit', $user) }}" class="btn btn-light" title="Ubah">
                                                <i class="fas fa-edit text-warning"></i>
                                            </a>
                                            @endif
                                            @if(auth()->user()->canActivateUsers() && $user->status !== 'active' && !(auth()->user()->isAdmin() && $user->isSuperAdmin()))
                                                <form action="{{ route('super-admin.users.activate', $user) }}" method="POST" style="display: inline;" id="activate-form-{{ $user->id }}">
                                                    @csrf
                                                    <button type="button" class="btn btn-light" onclick="confirmAction('activate-form-{{ $user->id }}', 'Aktifkan akun?', 'Akun ini akan diaktifkan kembali agar dapat masuk ke sistem.', 'Ya, aktifkan', '#10b981', 'question')" title="Aktifkan">
                                                        <i class="fas fa-user-check text-success"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if(auth()->user()->isSuperAdmin())
                                            @if($user->id != auth()->id())
                                                <form action="{{ route('super-admin.users.destroy', $user) }}" method="POST" style="display: inline;" id="delete-form-{{ $user->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-light" onclick="confirmDelete('delete-form-{{ $user->id }}')" title="Hapus">
                                                        <i class="fas fa-trash text-danger"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted mb-2"><i class="fas fa-users-slash fa-3x" style="opacity: 0.2;"></i></div>
                                        <h6 class="font-weight-bold text-dark mb-1">Pengguna tidak ditemukan.</h6>
                                        <p class="text-muted small mb-0">Coba sesuaikan filter pencarian Anda.</p>
                                    </td>
                                </tr>    
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
