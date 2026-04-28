@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('daily-logs.index') }}">Log Harian</a></li>
    <li class="breadcrumb-item active">Edit Log</li>
@endsection

@push('styles')
<style>
    .form-section {
        border: 1px solid #e2e8f0;
        border-radius: .85rem;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.25rem;
        background: #fff;
    }
    .form-section-title {
        font-size: .82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #64748b;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: .4rem;
    }
    .form-label-custom {
        font-size: .85rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: .35rem;
    }
    .source-option { display: none; }
    .source-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .3rem;
        border: 2px solid #e2e8f0;
        border-radius: .75rem;
        padding: .85rem .5rem;
        cursor: pointer;
        transition: all .2s ease;
        font-size: .78rem;
        font-weight: 600;
        color: #64748b;
        text-align: center;
        min-height: 80px;
    }
    .source-label i { font-size: 1.35rem; }
    .source-label:hover { border-color: var(--theme-blue); color: var(--theme-blue); background: rgba(37,99,235,.05); }
    .source-option:checked + .source-label { border-color: var(--theme-blue); background: rgba(37,99,235,.08); color: var(--theme-blue); }
    .status-option { display: none; }
    .status-label {
        display: flex; align-items: center; gap: .5rem;
        border: 2px solid #e2e8f0; border-radius: .65rem;
        padding: .65rem 1rem; cursor: pointer;
        font-weight: 600; font-size: .88rem; color: #64748b; transition: all .2s ease;
    }
    .status-label:hover { border-color: #94a3b8; }
    .status-option:checked + .status-label.status-selesai  { border-color: var(--theme-green); background: rgba(16,185,129,.08); color: var(--theme-green); }
    .status-option:checked + .status-label.status-pending  { border-color: var(--theme-orange); background: rgba(249,115,22,.08); color: var(--theme-orange); }
    .status-option:checked + .status-label.status-eskalasi { border-color: #dc2626; background: rgba(220,38,38,.08); color: #dc2626; }
    .char-counter { font-size: .75rem; color: #94a3b8; text-align: right; margin-top: .2rem; }
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
                <h4 class="mb-0 font-weight-bold" style="font-size:1.1rem;">Edit Log Harian</h4>
                <p class="text-muted mb-0" style="font-size:.82rem;">
                    Dibuat: {{ $dailyLog->created_at->format('d M Y, H:i') }}
                    &nbsp;|&nbsp; Terakhir diubah: {{ $dailyLog->updated_at->diffForHumans() }}
                </p>
            </div>
        </div>

        <form action="{{ route('daily-logs.update', $dailyLog) }}" method="POST" id="editForm">
            @csrf
            @method('PUT')

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
                               value="{{ old('log_date', $dailyLog->log_date->format('Y-m-d')) }}" required>
                        @error('log_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label-custom">Nama Pelapor <span class="text-danger">*</span></label>
                        <input type="text" name="reporter_name" id="reporter_name"
                               class="form-control @error('reporter_name') is-invalid @enderror"
                               value="{{ old('reporter_name', $dailyLog->reporter_name) }}"
                               placeholder="Nama lengkap pelapor" required>
                        @error('reporter_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label-custom">Departemen</label>
                        <input type="text" name="department" id="department"
                               class="form-control @error('department') is-invalid @enderror"
                               value="{{ old('department', $dailyLog->department) }}"
                               placeholder="Mis: HRD, Finance, IT...">
                        @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-12 mb-1">
                        <label class="form-label-custom">Kontak Pelapor <span class="text-muted font-weight-normal">(Opsional)</span></label>
                        <input type="text" name="contact_info" id="contact_info"
                               class="form-control @error('contact_info') is-invalid @enderror"
                               value="{{ old('contact_info', $dailyLog->contact_info) }}"
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
                        $currentSource = old('source', $dailyLog->source);
                    @endphp
                    @foreach($sourceDefs as $src => [$icon, $color])
                    <div class="col-4 col-md-2 mb-3 px-2">
                        <input type="radio" name="source" id="source_{{ Str::slug($src) }}"
                               value="{{ $src }}" class="source-option"
                               {{ $currentSource === $src ? 'checked' : '' }}>
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
                              placeholder="Jelaskan masalah yang dilaporkan..."
                              maxlength="2000" required>{{ old('issue_description', $dailyLog->issue_description) }}</textarea>
                    <div class="char-counter"><span id="issueCount">0</span>/2000</div>
                    @error('issue_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">Tindakan / Solusi yang Dilakukan <span class="text-danger">*</span></label>
                    <textarea name="action_taken" id="action_taken" rows="4"
                              class="form-control @error('action_taken') is-invalid @enderror"
                              placeholder="Jelaskan langkah-langkah penanganan..."
                              maxlength="2000" required>{{ old('action_taken', $dailyLog->action_taken) }}</textarea>
                    <div class="char-counter"><span id="actionCount">0</span>/2000</div>
                    @error('action_taken')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-1">
                    <label class="form-label-custom">Catatan Tambahan <span class="text-muted font-weight-normal">(Opsional)</span></label>
                    <textarea name="notes" id="notes" rows="2"
                              class="form-control @error('notes') is-invalid @enderror"
                              maxlength="1000">{{ old('notes', $dailyLog->notes) }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Bagian 4: Status & Durasi --}}
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-check-circle text-primary"></i> Status & Durasi
                </div>
                <div class="form-row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label-custom">Status Penyelesaian <span class="text-danger">*</span></label>
                        @php $currentStatus = old('status', $dailyLog->status); @endphp
                        <div class="d-flex flex-wrap" style="gap:.5rem;">
                            <div>
                                <input type="radio" name="status" id="status_selesai" value="Selesai" class="status-option"
                                       {{ $currentStatus === 'Selesai' ? 'checked' : '' }}>
                                <label for="status_selesai" class="status-label status-selesai">
                                    <i class="fas fa-check-circle"></i> Selesai
                                </label>
                            </div>
                            <div>
                                <input type="radio" name="status" id="status_pending" value="Pending" class="status-option"
                                       {{ $currentStatus === 'Pending' ? 'checked' : '' }}>
                                <label for="status_pending" class="status-label status-pending">
                                    <i class="fas fa-hourglass-half"></i> Pending
                                </label>
                            </div>
                            <div>
                                <input type="radio" name="status" id="status_eskalasi" value="Eskalasi ke Tiket" class="status-option"
                                       {{ $currentStatus === 'Eskalasi ke Tiket' ? 'checked' : '' }}>
                                <label for="status_eskalasi" class="status-label status-eskalasi">
                                    <i class="fas fa-ticket-alt"></i> Eskalasi ke Tiket
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label-custom">Estimasi Durasi <span class="text-muted font-weight-normal">(menit)</span></label>
                        <div class="input-group">
                            <input type="number" name="duration_minutes" id="duration_minutes"
                                   class="form-control @error('duration_minutes') is-invalid @enderror"
                                   value="{{ old('duration_minutes', $dailyLog->duration_minutes) }}"
                                   placeholder="15" min="1" max="480">
                            <div class="input-group-append">
                                <span class="input-group-text">mnt</span>
                            </div>
                        </div>
                        @error('duration_minutes')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end" style="gap:.5rem;">
                <a href="{{ route('daily-logs.show', $dailyLog) }}" class="btn btn-light border" style="border-radius:.6rem;">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary" style="border-radius:.6rem;" id="btnUpdate">
                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
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

document.getElementById('editForm').addEventListener('submit', function() {
    const btn = document.getElementById('btnUpdate');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
});
</script>
@endpush
