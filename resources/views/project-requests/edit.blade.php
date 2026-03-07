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
                                    <option value="access" {{ old('ticket_category', $projectRequest->ticket_category) === 'access' ? 'selected' : '' }}>Access</option>
                                    <option value="bug" {{ old('ticket_category', $projectRequest->ticket_category) === 'bug' ? 'selected' : '' }}>Bug</option>
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="urgency">Urgensi <span class="text-danger">*</span></label>
                                <select name="urgency" id="urgency" class="form-control @error('urgency') is-invalid @enderror" required>
                                    <option value="low" {{ old('urgency', $projectRequest->urgency) === 'low' ? 'selected' : '' }}>Rendah</option>
                                    <option value="medium" {{ old('urgency', $projectRequest->urgency) === 'medium' ? 'selected' : '' }}>Sedang</option>
                                    <option value="high" {{ old('urgency', $projectRequest->urgency) === 'high' ? 'selected' : '' }}>Tinggi</option>
                                    <option value="critical" {{ old('urgency', $projectRequest->urgency) === 'critical' ? 'selected' : '' }}>Critical</option>
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
                                  required>{{ old('description', $projectRequest->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
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
@endsection
