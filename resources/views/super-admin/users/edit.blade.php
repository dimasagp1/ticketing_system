@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">Super Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('super-admin.users.index') }}">Pengguna</a></li>
    <li class="breadcrumb-item active">Ubah</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card support-shell-card mb-4">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Ubah Informasi Pengguna</h3>
            </div>
            <form action="{{ route('super-admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nama <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Peran <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                                    <option value="client" {{ old('role', $user->role) == 'client' ? 'selected' : '' }}>Klien</option>
                                    {{-- <option value="developer" {{ old('role', $user->role) == 'developer' ? 'selected' : '' }}>Developer</option> --}}
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="super_admin" {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                </select>
                                @error('role')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                    <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Telepon</label>
                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company">Perusahaan</label>
                                <input type="text" name="company" id="company" class="form-control @error('company') is-invalid @enderror" 
                                       value="{{ old('company', $user->company) }}">
                                @error('company')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="bio">Biodata</label>
                        <textarea name="bio" id="bio" rows="3" class="form-control @error('bio') is-invalid @enderror">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <h5 class="font-weight-bold text-dark mb-3">Ubah Kata Sandi (Opsional)</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Kata Sandi Baru</label>
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah kata sandi</small>
                                @error('password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light" style="border-radius: 0 0 1.25rem 1.25rem;">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 0.5rem; font-weight: 500;">
                        <i class="fas fa-save mr-2"></i> Perbarui Pengguna
                    </button>
                    <a href="{{ route('super-admin.users.index') }}" class="btn btn-secondary px-4 shadow-sm" style="border-radius: 0.5rem; font-weight: 500;">
                        <i class="fas fa-times mr-2"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
