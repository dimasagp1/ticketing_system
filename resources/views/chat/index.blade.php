@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    <li class="breadcrumb-item active">Chat</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212]">
            {{-- Card Header: Title on Left, Button pushed to Far Right --}}
            <div class="d-flex justify-content-between align-items-center w-100 border-b-4 border-black pb-3 mb-3 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-[#0055FF] text-white border-3 border-black d-flex items-center justify-center font-fredoka font-black text-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] shrink-0">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div>
                        <h3 class="font-fredoka font-black text-xl text-black dark:text-white uppercase mb-0">Percakapan Saya 💬</h3>
                        <p class="font-jakarta font-extrabold text-xs text-muted mb-0">Kelola tiket dan pesan</p>
                    </div>
                </div>

                {{-- Action Button: Aligned to Far Right --}}
                <div class="ml-auto ms-auto">
                    <a href="{{ route('chat.create') }}" class="btn bg-[#0055FF] hover:bg-[#FFE600] text-white hover:text-black border-3 border-black font-fredoka font-black px-4 py-2 rounded-2xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 transition-all text-sm d-inline-flex items-center gap-2">
                        <i class="fas fa-plus"></i> <span>+ Percakapan Baru</span>
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table border-3 border-black rounded-2xl w-100 text-sm font-jakarta font-extrabold mb-0">
                        <thead>
                            <tr class="bg-[#0055FF] text-white font-fredoka font-black uppercase border-b-3 border-black">
                                <th class="p-3">SUBJEK</th>
                                @if(auth()->user()->isClient())
                                    <th class="p-3">DEVELOPER</th>
                                @else
                                    <th class="p-3">KLIEN</th>
                                @endif
                                <th class="p-3 d-none d-lg-table-cell">PROYEK</th>
                                <th class="p-3">STATUS</th>
                                <th class="p-3 d-none d-md-table-cell">TERAKHIR</th>
                                <th class="p-3 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($conversations as $conversation)
                                <tr class="border-b-2 border-black">
                                    <td class="p-3">
                                        <div class="font-fredoka font-black text-black dark:text-white text-base">{{ $conversation->subject }}</div>
                                        @if($conversation->getUnreadMessagesCount(auth()->id()) > 0)
                                            <span class="badge bg-[#FF007A] text-white border border-black font-fredoka font-black text-xs px-2 py-0.5 mt-1">
                                                {{ $conversation->getUnreadMessagesCount(auth()->id()) }} pesan baru
                                            </span>
                                        @endif
                                    </td>
                                    @if(auth()->user()->isClient())
                                        <td class="p-3">
                                            <span class="text-muted d-block text-xs uppercase">Developer</span>
                                            <span class="font-bold text-black dark:text-white">{{ $conversation->developer ? $conversation->developer->name : 'Belum ditugaskan' }}</span>
                                        </td>
                                    @else
                                        <td class="p-3">
                                            <span class="text-muted d-block text-xs uppercase">Klien</span>
                                            <span class="font-bold text-black dark:text-white">{{ $conversation->client->name }}</span>
                                        </td>
                                    @endif
                                    <td class="p-3 d-none d-lg-table-cell text-muted">
                                        @if($conversation->projectRequest)
                                            {{ $conversation->projectRequest->project_name }}
                                        @elseif($conversation->queue)
                                            {{ $conversation->queue->project_name }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        @if($conversation->status == 'active')
                                            <span class="badge bg-[#00E676] text-black border border-black font-fredoka font-black text-xs px-2 py-1">
                                                <i class="fas fa-circle text-[8px] mr-1"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-gray-500 text-white border border-black font-fredoka font-black text-xs px-2 py-1">
                                                <i class="fas fa-check text-[8px] mr-1"></i> Ditutup
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 d-none d-md-table-cell text-muted font-mono text-xs">
                                        {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : '-' }}
                                    </td>
                                    <td class="p-3 text-right">
                                        <a href="{{ route('chat.show', $conversation) }}" class="btn btn-sm bg-[#FFE600] text-black border-2 border-black font-fredoka font-black px-3 py-1 rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-[#FF007A] hover:text-white transition-all">
                                            Buka <i class="fas fa-chevron-right ml-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center p-5">
                                        <div class="text-muted mb-2"><i class="fas fa-comments fa-3x" style="opacity: 0.3;"></i></div>
                                        <h5 class="font-fredoka font-black text-black dark:text-white mb-1">Belum ada percakapan.</h5>
                                        <p class="font-jakarta font-extrabold text-muted text-xs mb-3">Mulai komunikasi mengenai tiket Anda di sini.</p>
                                        <a href="{{ route('chat.create') }}" class="btn bg-[#0055FF] text-white border-2 border-black font-fredoka font-black px-4 py-2 rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-[#FFE600] hover:text-black transition-all text-xs inline-flex items-center gap-1">
                                            <i class="fas fa-plus"></i> Mulai percakapan baru
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($conversations->hasPages())
            <div class="card-footer bg-transparent border-top-0 px-4 py-3">
                {{ $conversations->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
