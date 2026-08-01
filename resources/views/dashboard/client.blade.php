@php
    $requests = auth()->user()->projectRequests();
    $totalRequests = $requests->count();
    $approvedRequests = auth()->user()->projectRequests()->where('status', 'approved')->count();
    $pendingRequests = auth()->user()->projectRequests()->whereIn('status', ['draft', 'submitted', 'revision_requested'])->count();
    $activeTickets = auth()->user()->projectRequests()->whereIn('ticket_status', \App\Models\ProjectRequest::activeTicketStatuses())->count();
    $activeChats = auth()->user()->clientConversations()->where('status', 'active')->count();
@endphp

@if($pendingApprovalTickets->count() > 0)
    <div class="mb-4 p-4 border-4 border-black bg-[#0055FF] text-white rounded-3xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-[#FFE600] text-black border-3 border-black flex items-center justify-center font-fredoka font-black text-2xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] shrink-0">
                ℹ️
            </div>
            <div>
                <h4 class="font-fredoka font-black text-lg text-white mb-0 uppercase tracking-wide">
                    STATUS PENGAJUAN: {{ $pendingApprovalTickets->count() }} TIKET DALAM PROSES VERIFIKASI / PERSETUJUAN
                </h4>
                <p class="font-jakarta font-extrabold text-xs text-white/90 mb-0 mt-0.5">
                    Tiket Anda sedang ditinjau oleh tim Admin. Anda dapat melihat status persetujuannya di tabel di bawah.
                </p>
            </div>
        </div>
    </div>
@endif

<!-- Quick Action Button Row -->
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('project-requests.create') }}" class="btn btn-primary font-fredoka font-black border-4 border-black dark:border-white rounded-2xl px-4 py-2.5 bg-[#FF007A] text-white hover:bg-[#FFE600] hover:text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_#FFE600] active:translate-x-1 active:translate-y-1 transition-all inline-flex items-center gap-2 select-none">
        <i class="fas fa-plus"></i> Buat Tiket Baru 🚀
    </a>
</div>

<!-- Stat Cards Row -->
<div class="row">
    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100 flex flex-col justify-between">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-[#FFE600] text-black border-2 border-black font-fredoka font-black px-3 py-1 rounded-full">
                    <i class="fas fa-ticket-alt mr-1"></i> SEMUA
                </span>
                <small class="font-fredoka font-black text-[#0055FF]">Saya</small>
            </div>
            <div class="font-fredoka font-black text-4xl text-black dark:text-white my-2">{{ number_format($totalRequests) }}</div>
            <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Total Permintaan Saya</div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100 flex flex-col justify-between">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-[#00E676] text-black border-2 border-black font-fredoka font-black px-3 py-1 rounded-full">
                    <i class="fas fa-check-circle mr-1"></i> DISETUJUI
                </span>
                <small class="font-fredoka font-black text-[#00E676]">Disetujui</small>
            </div>
            <div class="font-fredoka font-black text-4xl text-black dark:text-white my-2">{{ $approvedRequests }}</div>
            <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Permintaan Disetujui</div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100 flex flex-col justify-between">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-[#FF007A] text-white border-2 border-black font-fredoka font-black px-3 py-1 rounded-full">
                    <i class="fas fa-hourglass-half mr-1"></i> MENUNGGU
                </span>
                <small class="font-fredoka font-black text-[#FF007A]">Pending</small>
            </div>
            <div class="font-fredoka font-black text-4xl text-black dark:text-white my-2">{{ $pendingRequests }}</div>
            <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Menunggu Persetujuan</div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100 flex flex-col justify-between">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-[#0055FF] text-white border-2 border-black font-fredoka font-black px-3 py-1 rounded-full">
                    <i class="fas fa-comments mr-1"></i> CHAT
                </span>
                <small class="font-fredoka font-black text-[#0055FF]">Support</small>
            </div>
            <div class="font-fredoka font-black text-4xl text-black dark:text-white my-2">{{ $activeChats }}</div>
            <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Chat Aktif</div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="row">
    <!-- Left Column: Tables -->
    <div class="col-lg-8 mb-4 space-y-6">
        
        <!-- 1. Tiket Saya Menunggu Persetujuan (Pending Approvals) -->
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212]">
            <div class="d-flex justify-content-between align-items-center border-b-4 border-black pb-3 mb-3">
                <div class="d-flex items-center gap-2">
                    <h3 class="font-fredoka font-black text-xl text-black dark:text-white uppercase mb-0">
                        Tiket Saya Menunggu Persetujuan ⏳
                    </h3>
                    <span class="badge bg-[#FF007A] text-white border-2 border-black font-fredoka font-black px-2.5 py-1 rounded-full text-xs">
                        {{ $pendingApprovalTickets->count() }} Belum Disetujui
                    </span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table border-3 border-black rounded-2xl w-100 text-sm font-jakarta font-extrabold">
                    <thead>
                        <tr class="bg-[#FF007A] text-white font-fredoka font-black uppercase border-b-3 border-black">
                            <th class="p-3">TICKET #</th>
                            <th class="p-3">NAMA PROYEK</th>
                            <th class="p-3">TANGGAL REQUEST</th>
                            <th class="p-3">STATUS</th>
                            <th class="p-3 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingApprovalTickets as $req)
                            <tr>
                                <td class="p-3"><span class="badge bg-black text-white font-fredoka font-black">{{ $req->ticket_number ?? '#'.$req->id }}</span></td>
                                <td class="p-3 font-fredoka font-black text-black dark:text-white">{{ $req->title }}</td>
                                <td class="p-3 font-mono text-xs">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-3">
                                    <span class="badge bg-[#FFE600] text-black border-2 border-black font-fredoka font-black text-xs px-2 py-1">
                                        Menunggu Persetujuan
                                    </span>
                                </td>
                                <td class="p-3 text-right">
                                    <a href="{{ route('project-requests.show', $req) }}" class="btn btn-sm bg-[#0055FF] text-white border-2 border-black font-fredoka font-black rounded-xl px-3 py-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-[#FFE600] hover:text-black">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center p-4 font-jakarta font-extrabold text-muted">
                                    Tidak ada tiket Anda yang sedang menunggu persetujuan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Active Projects Table (Sorted: Approved Tickets First + Request Date) -->
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212]">
            <div class="d-flex justify-content-between align-items-center border-b-4 border-black pb-3 mb-3">
                <h3 class="font-fredoka font-black text-xl text-black dark:text-white uppercase mb-0">
                    Tiket & Project Aktif Saya ⚡
                </h3>
                <a href="{{ route('project-requests.index') }}" class="btn btn-sm bg-[#FFE600] text-black border-2 border-black font-fredoka font-black px-3 py-1 rounded-full shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-[#FF007A] hover:text-white">
                    Lihat Semua Tiket
                </a>
            </div>
            <div class="table-responsive">
                <table class="table border-3 border-black rounded-2xl w-100 text-sm font-jakarta font-extrabold">
                    <thead>
                        <tr class="bg-[#FFE600] text-black font-fredoka font-black uppercase border-b-3 border-black">
                            <th class="p-3">TICKET #</th>
                            <th class="p-3">NAMA PROYEK</th>
                            <th class="p-3">TANGGAL REQUEST</th>
                            <th class="p-3">PERSETUJUAN</th>
                            <th class="p-3">STATUS TIKET</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeProjectRequests as $req)
                            <tr>
                                <td class="p-3"><span class="badge bg-black text-white font-fredoka font-black">{{ $req->ticket_number ?? '#'.$req->id }}</span></td>
                                <td class="p-3 font-fredoka font-black text-black dark:text-white">
                                    <a href="{{ route('project-requests.show', $req) }}" class="text-black dark:text-white hover:text-[#FF007A]">
                                        {{ $req->title }}
                                    </a>
                                </td>
                                <td class="p-3 font-mono text-xs">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-3">
                                    @if($req->status === 'approved')
                                        <span class="badge bg-[#00E676] text-black border-2 border-black font-fredoka font-black text-xs px-2 py-1">
                                            Disetujui
                                        </span>
                                    @elseif($req->status === 'submitted')
                                        <span class="badge bg-[#FFE600] text-black border-2 border-black font-fredoka font-black text-xs px-2 py-1">
                                            Menunggu
                                        </span>
                                    @else
                                        <span class="badge bg-[#FF007A] text-white border-2 border-black font-fredoka font-black text-xs px-2 py-1">
                                            {{ ucfirst($req->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <span class="badge bg-[#0055FF] text-white border-2 border-black font-fredoka font-black text-xs px-2 py-1">
                                        {{ $req->ticket_status_label }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center p-4 font-jakarta font-extrabold text-muted">
                                    Belum ada tiket aktif.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Right Side Widgets -->
    <div class="col-lg-4 mb-4 space-y-6">
        <!-- Quick Support Card -->
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212]">
            <h3 class="font-fredoka font-black text-xl text-black dark:text-white uppercase border-b-4 border-black pb-3 mb-3">Dukungan & Bantuan 💡</h3>
            <p class="font-jakarta font-extrabold text-xs text-muted mb-3">
                Membutuhkan bantuan teknis langsung dari Tim IT Support? Gunakan fitur chat langsung di bawah ini.
            </p>
            <a href="{{ route('chat.index') }}" class="btn btn-primary font-fredoka font-black border-3 border-black rounded-2xl w-full py-2.5 bg-[#0055FF] text-white hover:bg-[#FFE600] hover:text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                <i class="fas fa-comments mr-2"></i> Buka Layanan Chat
            </a>
        </div>
    </div>
</div>
