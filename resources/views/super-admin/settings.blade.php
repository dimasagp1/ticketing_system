@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">Super Admin</a></li>
    <li class="breadcrumb-item active">Pengaturan</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0" style="border-radius: 1.25rem;">
            <div class="card-header border-0 bg-white pt-4 pb-2 px-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 48px; height: 48px; background-color: rgba(37, 99, 235, 0.1);">
                        <i class="fas fa-cog fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.25rem;">Pengaturan Umum</h3>
                        <p class="text-muted mb-0 small">Kelola preferensi utama aplikasi</p>
                    </div>
                </div>
            </div>
            <form action="{{ route('super-admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body px-4 py-4">
                    <div class="form-group mb-4">
                        <label class="font-weight-600 text-dark">Nama / Judul Aplikasi</label>
                        <input type="text" name="app_name" class="form-control form-control-lg border-light shadow-none bg-light" value="{{ old('app_name', $settings['app_name'] ?? config('app.name')) }}">
                        <small class="form-text text-muted mt-1">Ditampilkan pada judul tab browser dan area tertentu di aplikasi.</small>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group mb-4">
                                <label class="font-weight-600 text-dark">Logo Aplikasi</label>
                                @if(!empty($settings['app_logo']))
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $settings['app_logo']) }}" alt="App Logo" class="img-thumbnail" style="max-height: 80px;">
                                    </div>
                                @endif
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="app_logo" name="app_logo" accept="image/*">
                                    <label class="custom-file-label" for="app_logo">Pilih file logo...</label>
                                </div>
                                <small class="form-text text-muted mt-1">Gunakan gambar transparan (PNG/SVG) untuk hasil terbaik. (Maks. 2MB)</small>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group mb-4">
                                <label class="font-weight-600 text-dark">Favicon</label>
                                @if(!empty($settings['app_favicon']))
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $settings['app_favicon']) }}" alt="App Favicon" class="img-thumbnail" style="max-height: 48px;">
                                    </div>
                                @endif
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="app_favicon" name="app_favicon" accept="image/x-icon,image/png,image/jpeg">
                                    <label class="custom-file-label" for="app_favicon">Pilih file favicon...</label>
                                </div>
                                <small class="form-text text-muted mt-1">Disarankan berukuran persegi (mis. 32x32 atau 64x64 piksel). (Maks. 1MB)</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-600 text-dark">Email Default Admin</label>
                        <input type="email" name="admin_email" class="form-control form-control-lg border-light shadow-none bg-light" value="{{ old('admin_email', $settings['admin_email']) }}">
                        <small class="form-text text-muted mt-1">Digunakan sebagai email sistem utama.</small>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group mb-4">
                                <label class="font-weight-600 text-dark">Proyek per Halaman</label>
                                <input type="number" name="per_page" class="form-control form-control-lg border-light shadow-none bg-light" value="{{ old('per_page', $settings['per_page']) }}" min="5" max="100">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group mb-4">
                                <label class="font-weight-600 text-dark">Jendela Notifikasi (hari)</label>
                                <input type="number" name="notification_window_days" class="form-control form-control-lg border-light shadow-none bg-light" value="{{ old('notification_window_days', $settings['notification_window_days'] ?? 3) }}" min="1" max="30">
                                <small class="form-text text-muted mt-1">Notifikasi approval/progress ditampilkan dari rentang hari terakhir ini.</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 border-light">

                    <h5 class="font-weight-bold mb-3" style="font-size: 1rem;">Opsi Tambahan</h5>

                    <div class="form-group mb-4">
                        <div class="custom-control custom-switch custom-switch-md">
                            <input type="checkbox" class="custom-control-input" id="email_notifications" name="email_notifications" value="1" {{ old('email_notifications', $settings['email_notifications']) ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-500" for="email_notifications">Kirim notifikasi email ke pengguna</label>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <div class="custom-control custom-switch custom-switch-md">
                            <input type="checkbox" class="custom-control-input" id="maintenance_mode" name="maintenance_mode" value="1" {{ old('maintenance_mode', $settings['maintenance_mode']) ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-500 text-danger" for="maintenance_mode">Aktifkan Mode Maintenance</label>
                        </div>
                        <small class="form-text text-muted ml-5">Hanya Super Admin yang bisa login jika ini aktif.</small>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 px-4 pb-4 pt-0 text-right">
                    <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 0.5rem; font-weight: 500;">
                        <i class="fas fa-save mr-2"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });
</script>
@endpush
