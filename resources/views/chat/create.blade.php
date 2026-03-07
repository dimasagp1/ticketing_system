@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('chat.index') }}">Chat</a></li>
    <li class="breadcrumb-item active">Baru</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="alert alert-light border">
            <i class="fas fa-comments text-primary"></i>
            Mulai percakapan baru untuk diskusi teknis, revisi, atau klarifikasi kebutuhan proyek.
        </div>

        <div class="card support-shell-card mb-4">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Buat Percakapan Baru</h3>
            </div>
            <form action="{{ route('chat.store') }}" method="POST">
                @csrf
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="form-group">
                        <label for="subject">Subjek <span class="text-danger">*</span></label>
                        <input type="text" name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" 
                               value="{{ old('subject') }}" required>
                        @error('subject')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                        <div class="form-group">
                            <label for="client_id">Client</label>
                            <select name="client_id" id="client_id" class="form-control @error('client_id') is-invalid @enderror">
                                <option value="">Pilih Klien</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="project_request_id">Permintaan Proyek Terkait (Opsional)</label>
                        <select name="project_request_id" id="project_request_id" class="form-control @error('project_request_id') is-invalid @enderror">
                            <option value="">Pilih Permintaan Proyek</option>
                            @foreach($projectRequests as $request)
                                <option value="{{ $request->id }}" {{ old('project_request_id') == $request->id ? 'selected' : '' }}>
                                    {{ $request->project_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_request_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="message">Pesan Awal <span class="text-danger">*</span></label>
                        <textarea name="message" id="message" rows="5" class="form-control @error('message') is-invalid @enderror" 
                                  required>{{ old('message') }}</textarea>
                        @error('message')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer bg-light" style="border-radius: 0 0 1.25rem 1.25rem;">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 0.5rem; font-weight: 500;">
                        <i class="fas fa-paper-plane mr-2"></i> Mulai Percakapan
                    </button>
                    <a href="{{ route('chat.index') }}" class="btn btn-secondary px-4 shadow-sm" style="border-radius: 0.5rem; font-weight: 500;">
                        <i class="fas fa-times mr-2"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
