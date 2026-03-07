@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    <li class="breadcrumb-item active">Chat</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 1.25rem;">
            <div class="card-header border-0 bg-white pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center mr-3 d-none d-sm-flex" style="width: 48px; height: 48px; background-color: rgba(37, 99, 235, 0.1);">
                        <i class="fas fa-comments fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.25rem;">Percakapan Saya</h3>
                        <p class="text-muted mb-0 small mt-1">Kelola tiket dan pesan</p>
                    </div>
                </div>
                <div class="card-tools m-0">
                    <a href="{{ route('chat.create') }}" class="btn btn-primary shadow-sm" style="border-radius: 0.5rem; font-weight: 500;">
                        <i class="fas fa-plus mr-1"></i> <span class="d-none d-sm-inline">Percakapan Baru</span>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-4 border-bottom-0">Subjek</th>
                                @if(auth()->user()->isClient())
                                    <th class="border-bottom-0">Developer</th>
                                @else
                                    <th class="border-bottom-0">Klien</th>
                                @endif
                                <th class="d-none d-lg-table-cell border-bottom-0">Proyek</th>
                                <th class="border-bottom-0">Status</th>
                                <th class="d-none d-md-table-cell border-bottom-0">Terkahir</th>
                                <th class="pr-4 border-bottom-0 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($conversations as $conversation)
                                <tr>
                                    <td class="pl-4">
                                        <div class="font-weight-600 text-dark">{{ $conversation->subject }}</div>
                                        @if($conversation->getUnreadMessagesCount(auth()->id()) > 0)
                                            <span class="badge badge-danger mt-1">{{ $conversation->getUnreadMessagesCount(auth()->id()) }} pesan baru</span>
                                        @endif
                                    </td>
                                    @if(auth()->user()->isClient())
                                        <td>
                                            <span class="text-muted d-block small">Developer</span>
                                            {{ $conversation->developer ? $conversation->developer->name : 'Belum ditugaskan' }}
                                        </td>
                                    @else
                                        <td>
                                            <span class="text-muted d-block small">Klien</span>
                                            <div class="font-weight-500">{{ $conversation->client->name }}</div>
                                        </td>
                                    @endif
                                    <td class="d-none d-lg-table-cell text-muted">
                                        @if($conversation->projectRequest)
                                            {{ $conversation->projectRequest->project_name }}
                                        @elseif($conversation->queue)
                                            {{ $conversation->queue->project_name }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($conversation->status == 'active')
                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-circle text-xs mr-1"></i> Aktif</span>
                                        @else
                                            <span class="badge badge-secondary px-2 py-1"><i class="fas fa-check text-xs mr-1"></i> Ditutup</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-md-table-cell text-muted small">
                                        {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : '-' }}
                                    </td>
                                    <td class="pr-4 text-right">
                                        <a href="{{ route('chat.show', $conversation) }}" class="btn btn-light btn-sm rounded-circle shadow-sm" title="Buka Percakapan" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0;">
                                            <i class="fas fa-chevron-right text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted mb-2"><i class="fas fa-comments fa-3x" style="opacity: 0.2;"></i></div>
                                        <h6 class="font-weight-bold text-dark mb-1">Belum ada percakapan.</h6>
                                        <p class="text-muted small mb-3">Mulai komunikasi mengenai tiket Anda di sini.</p>
                                        <a href="{{ route('chat.create') }}" class="btn btn-outline-primary btn-sm" style="border-radius: 0.5rem;">Mulai percakapan baru</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($conversations->hasPages())
            <div class="card-footer bg-white border-top border-light px-4 py-3">
                {{ $conversations->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
