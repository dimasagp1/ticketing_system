@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('project-requests.index') }}">Permintaan Proyek</a></li>
    <li class="breadcrumb-item active">Buat</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <form action="{{ route('project-requests.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="alert alert-light border mb-3">
                <i class="fas fa-lightbulb text-warning mr-2"></i>
                Isi detail proyek sedetail mungkin agar proses review dan approval lebih cepat.
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
                                       value="{{ old('project_name') }}" required>
                                @error('project_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="ticket_category">Kategori Tiket <span class="text-danger">*</span></label>
                                <select name="ticket_category" id="ticket_category" class="form-control @error('ticket_category') is-invalid @enderror" required>
                                    <option value="incident" {{ old('ticket_category', 'incident') === 'incident' ? 'selected' : '' }}>Insiden</option>
                                    <option value="service_request" {{ old('ticket_category') === 'service_request' ? 'selected' : '' }}>Permintaan Layanan</option>
                                    <option value="access" {{ old('ticket_category') === 'access' ? 'selected' : '' }}>Akses</option>
                                    <option value="bug" {{ old('ticket_category') === 'bug' ? 'selected' : '' }}>Bug</option>
                                    <option value="technical_support" {{ old('ticket_category') === 'technical_support' ? 'selected' : '' }}>Dukungan Teknis</option>
                                    <option value="other" {{ old('ticket_category') === 'other' ? 'selected' : '' }}>Lainnya</option>
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
                                    <option value="low" {{ old('impact') === 'low' ? 'selected' : '' }}>Rendah</option>
                                    <option value="medium" {{ old('impact', 'medium') === 'medium' ? 'selected' : '' }}>Sedang</option>
                                    <option value="high" {{ old('impact') === 'high' ? 'selected' : '' }}>Tinggi</option>
                                    <option value="critical" {{ old('impact') === 'critical' ? 'selected' : '' }}>Kritis</option>
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
                                       value="{{ old('estimated_duration') }}" min="1">
                                @error('estimated_duration')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3" id="technical-subcategory-wrapper" style="display: {{ old('ticket_category') === 'technical_support' ? 'block' : 'none' }};">
                            <div class="form-group">
                                <label for="technical_subcategory">Subkategori Teknis <span class="text-danger">*</span></label>
                                <select name="technical_subcategory" id="technical_subcategory" class="form-control @error('technical_subcategory') is-invalid @enderror">
                                    <option value="">Pilih Subkategori</option>
                                    <option value="wifi" {{ old('technical_subcategory') === 'wifi' ? 'selected' : '' }}>Wifi</option>
                                    <option value="printer" {{ old('technical_subcategory') === 'printer' ? 'selected' : '' }}>Printer</option>
                                    <option value="komputer" {{ old('technical_subcategory') === 'komputer' ? 'selected' : '' }}>Komputer</option>
                                    <option value="software_install" {{ old('technical_subcategory') === 'software_install' ? 'selected' : '' }}>Instalasi Software</option>
                                    <option value="supporting" {{ old('technical_subcategory') === 'supporting' ? 'selected' : '' }}>Dukungan Umum</option>
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
                                    <option value="low" {{ old('urgency') === 'low' ? 'selected' : '' }}>Rendah</option>
                                    <option value="medium" {{ old('urgency', 'medium') === 'medium' ? 'selected' : '' }}>Sedang</option>
                                    <option value="high" {{ old('urgency') === 'high' ? 'selected' : '' }}>Tinggi</option>
                                    <option value="critical" {{ old('urgency') === 'critical' ? 'selected' : '' }}>Kritis</option>
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
                                  class="form-control @error('description') is-invalid @enderror" 
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card support-shell-card mb-4">
                <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                    <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Berkas Kebutuhan</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="requirements">Unggah Lampiran Kebutuhan (PDF, DOCX, Gambar)</label>
                        <div class="custom-file">
                            <input type="file" name="requirements[]" class="custom-file-input @error('requirements.*') is-invalid @enderror" 
                                   id="requirements" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <label class="custom-file-label" for="requirements">Pilih file...</label>
                        </div>
                        <small class="form-text text-muted">
                            Anda dapat mengunggah beberapa file. Maksimal 10MB per file.
                        </small>
                        @error('requirements.*')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div id="file-list" class="mt-2"></div>
                </div>
            </div>

            <div class="card support-shell-card mb-4">
                <div class="card-body bg-light" style="border-radius: 0 0 1.25rem 1.25rem;">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 0.5rem; font-weight: 500;">
                        <i class="fas fa-save mr-2"></i> Simpan sebagai Draf
                    </button>
                    <a href="{{ route('project-requests.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
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

    document.getElementById('ticket_category').addEventListener('change', toggleTechnicalSubcategory);
    toggleTechnicalSubcategory();

    // Show selected files
    document.getElementById('requirements').addEventListener('change', function(e) {
        const fileList = document.getElementById('file-list');
        fileList.innerHTML = '';
        
        if (this.files.length > 0) {
            const label = document.querySelector('.custom-file-label');
            label.textContent = this.files.length + ' file dipilih';
            
            const ul = document.createElement('ul');
            ul.className = 'list-group';
            
            for (let i = 0; i < this.files.length; i++) {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `
                    <span><i class="fas fa-file"></i> ${this.files[i].name}</span>
                    <span class="badge badge-primary">${(this.files[i].size / 1024).toFixed(2)} KB</span>
                `;
                ul.appendChild(li);
            }
            
            fileList.appendChild(ul);
        }
    });
</script>
@endpush
@endsection
