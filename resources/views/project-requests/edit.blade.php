@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('project-requests.index') }}">Permintaan Proyek</a></li>
    <li class="breadcrumb-item active">Ubah</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <form action="{{ route('project-requests.update', $projectRequest) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="alert alert-light border mb-3">
                <i class="fas fa-pen text-primary mr-2"></i>
                Perbarui data proyek dan file requirement jika ada revisi terbaru.
            </div>
            
            <div class="card support-shell-card mb-4">
                <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                    <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Informasi Proyek</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="project_name">Nama Proyek <span class="text-danger">*</span></label>
                                <input type="text" name="project_name" id="project_name" 
                                       class="form-control @error('project_name') is-invalid @enderror" 
                                       value="{{ old('project_name', $projectRequest->project_name) }}" required>
                                @error('project_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="ticket_category">Kategori Tiket <span class="text-danger">*</span></label>
                                <select name="ticket_category" id="ticket_category" class="form-control @error('ticket_category') is-invalid @enderror" required>
                                    <option value="incident" {{ old('ticket_category', $projectRequest->ticket_category) === 'incident' ? 'selected' : '' }}>Insiden</option>
                                    <option value="service_request" {{ old('ticket_category', $projectRequest->ticket_category) === 'service_request' ? 'selected' : '' }}>Permintaan Layanan</option>
                                    <option value="access" {{ old('ticket_category', $projectRequest->ticket_category) === 'access' ? 'selected' : '' }}>Akses</option>
                                    <option value="bug" {{ old('ticket_category', $projectRequest->ticket_category) === 'bug' ? 'selected' : '' }}>Bug</option>
                                    <option value="technical_support" {{ old('ticket_category', $projectRequest->ticket_category) === 'technical_support' ? 'selected' : '' }}>Dukungan Teknis</option>
                                    <option value="other" {{ old('ticket_category', $projectRequest->ticket_category) === 'other' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('ticket_category')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="impact">Dampak <span class="text-danger">*</span></label>
                                <select name="impact" id="impact" class="form-control @error('impact') is-invalid @enderror" required>
                                    <option value="low" {{ old('impact', $projectRequest->impact) === 'low' ? 'selected' : '' }}>Rendah</option>
                                    <option value="medium" {{ old('impact', $projectRequest->impact) === 'medium' ? 'selected' : '' }}>Sedang</option>
                                    <option value="high" {{ old('impact', $projectRequest->impact) === 'high' ? 'selected' : '' }}>Tinggi</option>
                                    <option value="critical" {{ old('impact', $projectRequest->impact) === 'critical' ? 'selected' : '' }}>Critical</option>
                                </select>
                                @error('impact')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="estimated_duration">Estimasi Durasi (hari)</label>
                                <input type="number" name="estimated_duration" id="estimated_duration" 
                                       class="form-control @error('estimated_duration') is-invalid @enderror" 
                                       value="{{ old('estimated_duration', $projectRequest->estimated_duration) }}" min="1">
                                @error('estimated_duration')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3" id="technical-subcategory-wrapper" style="display: {{ old('ticket_category', $projectRequest->ticket_category) === 'technical_support' ? 'block' : 'none' }};">
                            <div class="form-group">
                                <label for="technical_subcategory">Subkategori Teknis <span class="text-danger">*</span></label>
                                <select name="technical_subcategory" id="technical_subcategory" class="form-control @error('technical_subcategory') is-invalid @enderror">
                                    <option value="">Pilih Subkategori</option>
                                    <option value="wifi" {{ old('technical_subcategory', $projectRequest->technical_subcategory) === 'wifi' ? 'selected' : '' }}>Wifi</option>
                                    <option value="printer" {{ old('technical_subcategory', $projectRequest->technical_subcategory) === 'printer' ? 'selected' : '' }}>Printer</option>
                                    <option value="komputer" {{ old('technical_subcategory', $projectRequest->technical_subcategory) === 'komputer' ? 'selected' : '' }}>Komputer</option>
                                    <option value="software_install" {{ old('technical_subcategory', $projectRequest->technical_subcategory) === 'software_install' ? 'selected' : '' }}>Instalasi Software</option>
                                    <option value="supporting" {{ old('technical_subcategory', $projectRequest->technical_subcategory) === 'supporting' ? 'selected' : '' }}>Dukungan Umum</option>
                                </select>
                                @error('technical_subcategory')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="urgency">Urgensi <span class="text-danger">*</span></label>
                                <select name="urgency" id="urgency" class="form-control @error('urgency') is-invalid @enderror" required>
                                    <option value="low" {{ old('urgency', $projectRequest->urgency) === 'low' ? 'selected' : '' }}>Rendah</option>
                                    <option value="medium" {{ old('urgency', $projectRequest->urgency) === 'medium' ? 'selected' : '' }}>Sedang</option>
                                    <option value="high" {{ old('urgency', $projectRequest->urgency) === 'high' ? 'selected' : '' }}>Tinggi</option>
                                    <option value="critical" {{ old('urgency', $projectRequest->urgency) === 'critical' ? 'selected' : '' }}>Kritis</option>
                                </select>
                                @error('urgency')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Deskripsi Proyek <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" rows="5" 
                                  class="form-control text-justify @error('description') is-invalid @enderror" 
                                  style="text-align: justify; text-justify: inter-word;"
                                  required>{{ old('description', $projectRequest->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card support-shell-card mb-4">
                <div class="card-header border-0 bg-white pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">
                        <i class="fas fa-user-shield text-primary mr-2"></i>Persetujuan Atasan (Manager Approval)
                    </h3>
                    <span class="badge badge-light border text-muted">Tahap 1 Verifikasi</span>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="alert alert-light border mb-3 text-sm">
                        <i class="fas fa-info-circle text-info mr-1"></i>
                        Pilih level atasan yang akan memberikan persetujuan sebelum tiket diteruskan ke Tim IT.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="manager_role">Jabatan Atasan Penyetuju <span class="text-danger">*</span></label>
                                <select name="manager_role" id="manager_role" class="form-control @error('manager_role') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jabatan Atasan --</option>
                                    <option value="operational_manager" {{ old('manager_role', $projectRequest->manager_role) === 'operational_manager' ? 'selected' : '' }}>Operational Manager (OM)</option>
                                    <option value="general_manager" {{ old('manager_role', $projectRequest->manager_role) === 'general_manager' ? 'selected' : '' }}>General Manager (GM)</option>
                                </select>
                                @error('manager_role')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="manager_id">Nama Pejabat / Manager <span class="text-danger">*</span></label>
                                <select name="manager_id" id="manager_id" class="form-control @error('manager_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Nama Atasan --</option>
                                    @foreach($operationalManagers ?? [] as $om)
                                        <option value="{{ $om->id }}" data-role="operational_manager" {{ old('manager_id', $projectRequest->manager_id) == $om->id ? 'selected' : '' }}>
                                            {{ $om->name }} (Operational Manager)
                                        </option>
                                    @endforeach
                                    @foreach($generalManagers ?? [] as $gm)
                                        <option value="{{ $gm->id }}" data-role="general_manager" {{ old('manager_id', $projectRequest->manager_id) == $gm->id ? 'selected' : '' }}>
                                            {{ $gm->name }} (General Manager)
                                        </option>
                                    @endforeach
                                </select>
                                @error('manager_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="text-muted d-block mt-1">Pilih nama manajer yang akan memvalidasi pengajuan ini.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card support-shell-card mb-4">
                <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                    <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Perbarui File Kebutuhan</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="requirements">Unggah Kebutuhan Baru (Opsional)</label>
                        <div class="custom-file">
                            <input type="file" name="requirements[]" class="custom-file-input @error('requirements.*') is-invalid @enderror" 
                                   id="requirements" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <label class="custom-file-label" for="requirements">Pilih file...</label>
                        </div>
                        <small class="form-text text-muted">
                            Unggah file baru untuk mengganti kebutuhan sebelumnya. Maksimal 10MB per file.
                        </small>
                        @error('requirements.*')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card support-shell-card mb-4">
                <div class="card-body bg-light" style="border-radius: 0 0 1.25rem 1.25rem;">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 0.5rem; font-weight: 500;">
                        <i class="fas fa-{{ $projectRequest->status == 'revision_requested' ? 'paper-plane' : 'save' }} mr-2"></i> 
                        {{ $projectRequest->status == 'revision_requested' ? 'Kirim Revisi' : 'Perbarui Permintaan' }}
                    </button>
                    <a href="{{ route('project-requests.show', $projectRequest) }}" class="btn btn-secondary px-4 shadow-sm" style="border-radius: 0.5rem; font-weight: 500;">
                        <i class="fas fa-times mr-2"></i> Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleTechnicalSubcategory() {
        const ticketCategory = document.getElementById('ticket_category');
        const wrapper = document.getElementById('technical-subcategory-wrapper');
        const subcategory = document.getElementById('technical_subcategory');

        if (!ticketCategory || !wrapper || !subcategory) {
            return;
        }

        const isTechnical = ticketCategory.value === 'technical_support';
        wrapper.style.display = isTechnical ? 'block' : 'none';

        if (!isTechnical) {
            subcategory.value = '';
        }
    }

    function filterManagerOptions() {
        const managerRoleSelect = document.getElementById('manager_role');
        const managerIdSelect = document.getElementById('manager_id');
        if (!managerRoleSelect || !managerIdSelect) return;

        const selectedRole = managerRoleSelect.value;
        const options = managerIdSelect.querySelectorAll('option[data-role]');

        let hasSelectedOption = false;
        options.forEach(opt => {
            if (!selectedRole || opt.getAttribute('data-role') === selectedRole) {
                opt.style.display = '';
                if (opt.selected) hasSelectedOption = true;
            } else {
                opt.style.display = 'none';
                if (opt.selected) opt.selected = false;
            }
        });

        // Auto select first visible option if none is currently selected
        if (!hasSelectedOption && selectedRole) {
            const firstVisible = Array.from(options).find(opt => opt.getAttribute('data-role') === selectedRole);
            if (firstVisible) {
                firstVisible.selected = true;
            }
        }
    }

    document.getElementById('ticket_category').addEventListener('change', toggleTechnicalSubcategory);
    toggleTechnicalSubcategory();

    const managerRoleEl = document.getElementById('manager_role');
    if (managerRoleEl) {
        managerRoleEl.addEventListener('change', filterManagerOptions);
        filterManagerOptions();
    }
</script>
@endpush
@endsection
