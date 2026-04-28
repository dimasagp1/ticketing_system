@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('daily-logs.index') }}">Log Harian</a></li>
    <li class="breadcrumb-item active">Tambah Log</li>
@endsection

@push('styles')
<style>
    .form-section {
        border: 1px solid #e2e8f0;
        border-radius: .85rem;
        padding: 1.1rem 1.25rem;
        margin-bottom: 1rem;
        background: #fff;
    }
    .form-section-title {
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
    .form-label-custom {
        font-size: .84rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: .3rem;
        display: block;
    }
    .source-option { display: none; }
    .source-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .25rem;
        border: 2px solid #e2e8f0;
        border-radius: .65rem;
        padding: .7rem .4rem;
        cursor: pointer;
        transition: all .2s ease;
        font-size: .72rem;
        font-weight: 600;
        color: #64748b;
        text-align: center;
        min-height: 70px;
    }
    .source-label i { font-size: 1.25rem; }
    .source-label:hover { border-color: var(--theme-blue); color: var(--theme-blue); background: rgba(37,99,235,.05); }
    .source-option:checked + .source-label { border-color: var(--theme-blue); background: rgba(37,99,235,.08); color: var(--theme-blue); }
    .status-option { display: none; }
    .status-label {
        display: flex;
        align-items: center;
        gap: .45rem;
        border: 2px solid #e2e8f0;
        border-radius: .6rem;
        padding: .6rem .85rem;
        cursor: pointer;
        font-weight: 600;
        font-size: .84rem;
        color: #64748b;
        transition: all .2s ease;
        white-space: nowrap;
    }
    .status-label:hover { border-color: #94a3b8; }
    .status-option:checked + .status-label.status-selesai  { border-color: var(--theme-green);  background: rgba(16,185,129,.08);  color: var(--theme-green);  }
    .status-option:checked + .status-label.status-pending   { border-color: var(--theme-orange); background: rgba(249,115,22,.08); color: var(--theme-orange); }
    .status-option:checked + .status-label.status-eskalasi  { border-color: #dc2626;             background: rgba(220,38,38,.08);  color: #dc2626;             }
    .char-counter { font-size: .73rem; color: #94a3b8; text-align: right; margin-top: .15rem; }
    .form-action-bar { display: flex; justify-content: flex-end; gap: .5rem; }
    @media (max-width: 575px) {
        .form-section { padding: .85rem .9rem; }
        .form-action-bar { flex-direction: column-reverse; }
        .form-action-bar .btn { width: 100%; }
        .status-group { flex-direction: column; }
        .status-group > div { width: 100%; }
        .status-group .status-label { width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-10 col-xl-8">

        <div class="d-flex align-items-center mb-3" style="gap:.75rem;">
            <a href="{{ route('daily-logs.index') }}" class="btn btn-light border btn-sm" style="border-radius:.5rem;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h4 class="mb-0 font-weight-bold" style="font-size:1.1rem;">Tambah Log Harian</h4>
                <p class="text-muted mb-0" style="font-size:.82rem;">Catat komplain / kendala yang ditangani di luar tiket resmi</p>
            </div>
        </div>

        <form action="{{ route('daily-logs.store') }}" method="POST" id="logForm">
            @csrf

            {{-- Bagian 1: Info Dasar --}}
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-info-circle text-primary"></i> Informasi Dasar
                </div>
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label-custom">Tanggal Penanganan <span class="text-danger">*</span></label>
                        <input type="date" name="log_date" id="log_date"
                               class="form-control @error('log_date') is-invalid @enderror"
                               value="{{ old('log_date', date('Y-m-d')) }}" required>
                        @error('log_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label-custom">Nama Pelapor <span class="text-danger">*</span></label>
                        <input type="text" name="reporter_name" id="reporter_name"
                               class="form-control @error('reporter_name') is-invalid @enderror"
                               value="{{ old('reporter_name') }}"
                               placeholder="Nama lengkap pelapor" required>
                        @error('reporter_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label-custom">Departemen</label>
                        <input type="text" name="department" id="department"
                               class="form-control @error('department') is-invalid @enderror"
                               value="{{ old('department') }}"
                               placeholder="Mis: HRD, Finance, IT...">
                        @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-12 mb-1">
                        <label class="form-label-custom">Kontak Pelapor <span class="text-muted font-weight-normal">(Opsional)</span></label>
                        <input type="text" name="contact_info" id="contact_info"
                               class="form-control @error('contact_info') is-invalid @enderror"
                               value="{{ old('contact_info') }}"
                               placeholder="No. HP / email / extension pelapor">
                        @error('contact_info')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Bagian 2: Sumber Komplain --}}
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-satellite-dish text-primary"></i> Sumber Komplain <span class="text-danger">*</span>
                </div>
                @error('source')<div class="alert alert-danger py-2 mb-3" style="border-radius:.5rem;">{{ $message }}</div>@enderror
                <div class="row" style="gap:0;">
                    @php
                        $sourceDefs = [
                            'WhatsApp'   => ['fab fa-whatsapp', '#22c55e'],
                            'Telepon'    => ['fas fa-phone', '#3b82f6'],
                            'Tatap Muka' => ['fas fa-user', '#f97316'],
                            'Email'      => ['fas fa-envelope', '#8b5cf6'],
                            'Teams/Chat' => ['fas fa-comment-dots', '#06b6d4'],
                            'Lainnya'    => ['fas fa-ellipsis-h', '#94a3b8'],
                        ];
                    @endphp
                    @foreach($sourceDefs as $src => [$icon, $color])
                    <div class="col-4 col-md-2 mb-3 px-2">
                        <input type="radio" name="source" id="source_{{ Str::slug($src) }}"
                               value="{{ $src }}" class="source-option"
                               {{ old('source') === $src ? 'checked' : '' }}>
                        <label for="source_{{ Str::slug($src) }}" class="source-label w-100">
                            <i class="{{ $icon }}" style="color:{{ $color }};"></i>
                            {{ $src }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Bagian 3: Detail Masalah --}}
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-exclamation-triangle text-primary"></i> Detail Masalah & Penanganan
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">Deskripsi Masalah / Komplain <span class="text-danger">*</span></label>
                    <textarea name="issue_description" id="issue_description" rows="4"
                              class="form-control @error('issue_description') is-invalid @enderror"
                              placeholder="Jelaskan masalah yang dilaporkan secara singkat dan jelas..."
                              maxlength="2000" required>{{ old('issue_description') }}</textarea>
                    <div class="char-counter"><span id="issueCount">0</span>/2000</div>
                    @error('issue_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">Tindakan / Solusi yang Dilakukan <span class="text-danger">*</span></label>
                    <textarea name="action_taken" id="action_taken" rows="4"
                              class="form-control @error('action_taken') is-invalid @enderror"
                              placeholder="Jelaskan langkah-langkah penanganan yang Anda lakukan..."
                              maxlength="2000" required>{{ old('action_taken') }}</textarea>
                    <div class="char-counter"><span id="actionCount">0</span>/2000</div>
                    @error('action_taken')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-1">
                    <label class="form-label-custom">Catatan Tambahan <span class="text-muted font-weight-normal">(Opsional)</span></label>
                    <textarea name="notes" id="notes" rows="2"
                              class="form-control @error('notes') is-invalid @enderror"
                              placeholder="Catatan internal, referensi, atau info tambahan..."
                              maxlength="1000">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Bagian 4: Status & Durasi --}}
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-check-circle text-primary"></i> Status & Durasi
                </div>
                <div class="form-row">
                    <div class="col-12 col-md-8 mb-3">
                        <label class="form-label-custom">Status Penyelesaian <span class="text-danger">*</span></label>
                        @error('status')<div class="alert alert-danger py-2 mb-2" style="border-radius:.5rem;">{{ $message }}</div>@enderror
                        <div class="d-flex flex-wrap status-group" style="gap:.5rem;">
                            <div>
                                <input type="radio" name="status" id="status_selesai" value="Selesai" class="status-option"
                                       {{ old('status', 'Selesai') === 'Selesai' ? 'checked' : '' }}>
                                <label for="status_selesai" class="status-label status-selesai">
                                    <i class="fas fa-check-circle"></i> Selesai
                                </label>
                            </div>
                            <div>
                                <input type="radio" name="status" id="status_pending" value="Pending" class="status-option"
                                       {{ old('status') === 'Pending' ? 'checked' : '' }}>
                                <label for="status_pending" class="status-label status-pending">
                                    <i class="fas fa-hourglass-half"></i> Pending
                                </label>
                            </div>
                            <div>
                                <input type="radio" name="status" id="status_eskalasi" value="Eskalasi ke Tiket" class="status-option"
                                       {{ old('status') === 'Eskalasi ke Tiket' ? 'checked' : '' }}>
                                <label for="status_eskalasi" class="status-label status-eskalasi">
                                    <i class="fas fa-ticket-alt"></i> Eskalasi ke Tiket
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <label class="form-label-custom">Estimasi Durasi <span class="text-muted font-weight-normal">(menit, opsional)</span></label>
                        <div class="input-group">
                            <input type="number" name="duration_minutes" id="duration_minutes"
                                   class="form-control @error('duration_minutes') is-invalid @enderror"
                                   value="{{ old('duration_minutes') }}"
                                   placeholder="15" min="1" max="480">
                            <div class="input-group-append">
                                <span class="input-group-text">mnt</span>
                            </div>
                        </div>
                        <small class="text-muted">Maks 480 menit (8 jam)</small>
                        @error('duration_minutes')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="form-action-bar">
                <a href="{{ route('daily-logs.index') }}" class="btn btn-light border" style="border-radius:.6rem;">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary" style="border-radius:.6rem;" id="btnSimpan">
                    <i class="fas fa-save mr-1"></i> Simpan Log
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Char counters
function updateCount(textareaId, counterId) {
    const ta = document.getElementById(textareaId);
    const counter = document.getElementById(counterId);
    if (ta && counter) {
        counter.textContent = ta.value.length;
        ta.addEventListener('input', () => counter.textContent = ta.value.length);
    }
}
updateCount('issue_description', 'issueCount');
updateCount('action_taken', 'actionCount');

// Prevent double submit
document.getElementById('logForm').addEventListener('submit', function() {
    const btn = document.getElementById('btnSimpan');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
});
</script>
@endpush
