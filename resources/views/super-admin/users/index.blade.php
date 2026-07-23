@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ auth()->user()->isSuperAdmin() ? route('super-admin.dashboard') : route('dashboard') }}">{{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Dasbor' }}</a></li>
    <li class="breadcrumb-item active">Pengguna</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] mb-4">
            
            <!-- Card Header -->
            <div class="d-flex justify-content-between align-items-center border-b-4 border-black dark:border-white pb-3 mb-3 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="w-10 h-10 bg-[#0055FF] text-white border-2 border-black rounded-full flex items-center justify-center font-fredoka font-black">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h3 class="font-fredoka font-black text-xl text-black dark:text-white uppercase mb-0">Semua Pengguna</h3>
                        <p class="font-jakarta font-extrabold text-xs text-muted mb-0">{{ auth()->user()->isSuperAdmin() ? 'Kelola seluruh akun sistem' : 'Lihat akun pengguna' }}</p>
                    </div>
                    <!-- Filter Toggle Button -->
                    <button class="btn bg-[#FFE600] text-black border-2 border-black font-fredoka font-black rounded-xl text-xs px-3 py-1 ml-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-[#FF007A] hover:text-white transition-all" type="button" data-toggle="collapse" data-target="#userFilterCollapse" aria-expanded="false" aria-controls="userFilterCollapse">
                        <i class="fas fa-filter mr-1"></i> Filter Pengguna
                    </button>
                </div>

                <div class="card-tools">
                    @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('super-admin.users.create') }}" class="btn bg-[#0055FF] text-white border-3 border-black font-fredoka font-black rounded-2xl px-4 py-2 text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-[#FF007A] active:translate-x-1 active:translate-y-1 transition-all">
                        <i class="fas fa-plus mr-1"></i> Tambah Pengguna
                    </a>
                    @endif
                </div>
            </div>

            <!-- Collapsible Filter Form (Only expands when clicked or when filters are active) -->
            <div class="collapse {{ request()->anyFilled(['search', 'role', 'status']) ? 'show' : '' }} mb-4" id="userFilterCollapse">
                <form method="GET" class="p-3 bg-[#FFFBEA] dark:bg-[#1a1a1a] border-3 border-black dark:border-white rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] font-jakarta font-extrabold text-sm">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <select name="role" class="form-control border-2 border-black rounded-xl">
                                <option value="">Semua Role</option>
                                <option value="client" {{ request('role') == 'client' ? 'selected' : '' }}>Client</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="developer" {{ request('role') == 'developer' ? 'selected' : '' }}>Developer</option>
                                <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <select name="status" class="form-control border-2 border-black rounded-xl">
                                <option value="">Semua Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="text" name="search" class="form-control border-2 border-black rounded-xl" placeholder="Cari pengguna..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="submit" class="btn bg-[#0055FF] text-white border-2 border-black font-fredoka font-black rounded-xl w-full py-1.5">Terapkan</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table Container (Flush right below header) -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 border-3 border-black dark:border-white rounded-2xl w-100 font-jakarta font-extrabold text-sm">
                    <thead>
                        <tr class="bg-[#FFE600] text-black font-fredoka font-black uppercase border-b-3 border-black">
                            <th class="p-3">ID</th>
                            <th class="p-3">NAMA</th>
                            <th class="p-3">EMAIL</th>
                            <th class="p-3">ROLE</th>
                            <th class="p-3">STATUS</th>
                            <th class="p-3">DIBUAT</th>
                            <th class="p-3 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $userItem)
                            <tr>
                                <td class="p-3"><span class="badge bg-black text-white font-fredoka font-black">#{{ $userItem->id }}</span></td>
                                <td class="p-3 font-fredoka font-black text-black dark:text-white">{{ $userItem->name }}</td>
                                <td class="p-3 font-mono text-xs">{{ $userItem->email }}</td>
                                <td class="p-3">
                                    <span class="badge bg-[#0055FF] text-white border-2 border-black font-fredoka font-black px-2 py-1">
                                        {{ ucfirst(str_replace('_', ' ', $userItem->role)) }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    @if($userItem->status === 'active')
                                        <span class="badge bg-[#00E676] text-black border-2 border-black font-fredoka font-black px-2 py-1">Aktif</span>
                                    @elseif($userItem->status === 'inactive')
                                        <span class="badge bg-gray-400 text-black border-2 border-black font-fredoka font-black px-2 py-1">Tidak Aktif</span>
                                    @else
                                        <span class="badge bg-[#FF007A] text-white border-2 border-black font-fredoka font-black px-2 py-1">Ditangguhkan</span>
                                    @endif
                                </td>
                                <td class="p-3 font-mono text-xs text-muted">{{ $userItem->created_at->format('d M Y') }}</td>
                                <td class="p-3 text-right">
                                    <div class="btn-group">
                                        <a href="{{ route('super-admin.users.show', $userItem) }}" class="btn btn-sm bg-[#FFE600] text-black border-2 border-black font-fredoka font-black rounded-xl px-2 py-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mr-1" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(auth()->user()->isSuperAdmin())
                                        <a href="{{ route('super-admin.users.edit', $userItem) }}" class="btn btn-sm bg-[#00E676] text-black border-2 border-black font-fredoka font-black rounded-xl px-2 py-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mr-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('super-admin.users.destroy', $userItem) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm bg-[#FF007A] text-white border-2 border-black font-fredoka font-black rounded-xl px-2 py-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center p-4 font-jakarta font-extrabold text-muted">
                                    Tidak ada data pengguna yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $users->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
