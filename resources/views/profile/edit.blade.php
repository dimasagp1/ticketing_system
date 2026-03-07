@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    <li class="breadcrumb-item active">Profil</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Profile Card -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle" 
                         src="{{ auth()->user()->avatar_url }}" 
                         alt="User profile picture">
                </div>
                <h3 class="profile-username text-center">{{ auth()->user()->name }}</h3>
                <p class="text-muted text-center">{{ ucfirst(auth()->user()->role) }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Email</b> <span class="float-right">{{ auth()->user()->email }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Status</b> 
                        <span class="float-right">
                            @if(auth()->user()->status == 'active')
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst(auth()->user()->status) }}</span>
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>Bergabung Sejak</b> <span class="float-right">{{ auth()->user()->created_at->format('M Y') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Update Profile Information -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Profil</h3>
            </div>
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="avatar">Foto Profil</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="avatar" class="custom-file-input @error('avatar') is-invalid @enderror" id="avatar" accept="image/*">
                                <label class="custom-file-label" for="avatar">Pilih gambar...</label>
                            </div>
                        </div>
                        <small id="avatar-file-name" class="form-text text-info">Belum ada file dipilih.</small>
                        <small class="form-text text-muted">Format: JPG, PNG, GIF. Maksimal 2MB.</small>
                        @error('avatar')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" 
                               value="{{ old('phone', auth()->user()->phone) }}">
                        @error('phone')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="company">Perusahaan</label>
                        <input type="text" name="company" id="company" class="form-control @error('company') is-invalid @enderror" 
                               value="{{ old('company', auth()->user()->company) }}">
                        @error('company')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="bio">Biodata</label>
                        <textarea name="bio" id="bio" rows="3" class="form-control @error('bio') is-invalid @enderror">{{ old('bio', auth()->user()->bio) }}</textarea>
                        @error('bio')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Update Password -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ubah Kata Sandi</h3>
            </div>
            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="current_password">Kata Sandi Saat Ini</label>
                        <div class="input-group">
                            <input type="password" name="current_password" id="current_password" 
                                   class="form-control @error('current_password') is-invalid @enderror" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary js-password-toggle" data-target="current_password" aria-label="Tampilkan kata sandi saat ini">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        @error('current_password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Kata Sandi Baru</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" 
                                   class="form-control @error('password') is-invalid @enderror" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary js-password-toggle" data-target="password" aria-label="Tampilkan kata sandi baru">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                   class="form-control" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary js-password-toggle" data-target="password_confirmation" aria-label="Tampilkan konfirmasi kata sandi">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key"></i> Perbarui Kata Sandi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.getElementById('avatar');
        var label = document.querySelector('label[for="avatar"].custom-file-label');
        var info = document.getElementById('avatar-file-name');

        if (input) {
            input.addEventListener('change', function () {
                var selectedFile = (input.files && input.files.length > 0) ? input.files[0].name : '';
                var fallbackName = input.value ? input.value.split('\\').pop() : '';
                var fileName = selectedFile || fallbackName;

                if (label) {
                    label.textContent = fileName || 'Pilih gambar...';
                    label.classList.toggle('selected', !!fileName);
                }

                if (info) {
                    info.textContent = fileName ? ('File dipilih: ' + fileName) : 'Belum ada file dipilih.';
                }
            });
        }

        var toggles = document.querySelectorAll('.js-password-toggle');
        toggles.forEach(function (button) {
            button.addEventListener('click', function () {
                var targetId = button.getAttribute('data-target');
                var passwordInput = document.getElementById(targetId);
                if (!passwordInput) {
                    return;
                }

                var icon = button.querySelector('i');
                var reveal = passwordInput.type === 'password';
                passwordInput.type = reveal ? 'text' : 'password';

                if (icon) {
                    icon.classList.toggle('fa-eye', !reveal);
                    icon.classList.toggle('fa-eye-slash', reveal);
                }
            });
        });
    });
</script>
@endpush
