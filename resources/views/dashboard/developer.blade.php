@php
    $assigned = auth()->user()->assignedQueues();
    $assignedCount = $assigned->count();
    $completedCount = auth()->user()->assignedQueues()->where('status', 'Completed')->count();
    $inProgressCount = auth()->user()->assignedQueues()->where('status', 'In Progress')->count();
    $pendingCount = auth()->user()->assignedQueues()->where('status', 'Pending')->count();
    $activeChats = auth()->user()->developerConversations()->where('status', 'active')->count();
@endphp

<!-- Quick Action Button Row -->
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('queues.index') }}" class="btn btn-primary font-fredoka font-black border-4 border-black dark:border-white rounded-2xl px-4 py-2.5 bg-[#0055FF] text-white hover:bg-[#FFE600] hover:text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_#FFE600] active:translate-x-1 active:translate-y-1 transition-all inline-flex items-center gap-2 select-none">
        <i class="fas fa-layer-group"></i> Lihat Semua Queue ⚡
    </a>
</div>

<!-- Stat Cards Row -->
<div class="row">
    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100 flex flex-col justify-between">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-[#FFE600] text-black border-2 border-black font-fredoka font-black px-3 py-1 rounded-full">
                    <i class="fas fa-tasks mr-1"></i> DITUGASKAN
                </span>
                <small class="font-fredoka font-black text-[#0055FF]">Tugas</small>
            </div>
            <div class="font-fredoka font-black text-4xl text-black dark:text-white my-2">{{ number_format($assignedCount) }}</div>
            <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Total Queue Ditugaskan</div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100 flex flex-col justify-between">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-[#0055FF] text-white border-2 border-black font-fredoka font-black px-3 py-1 rounded-full">
                    <i class="fas fa-spinner fa-spin mr-1"></i> DIPROSES
                </span>
                <small class="font-fredoka font-black text-[#0055FF]">In Progress</small>
            </div>
            <div class="font-fredoka font-black text-4xl text-black dark:text-white my-2">{{ $inProgressCount }}</div>
            <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Queue Sedang Dikerjakan</div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100 flex flex-col justify-between">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-[#00E676] text-black border-2 border-black font-fredoka font-black px-3 py-1 rounded-full">
                    <i class="fas fa-check-circle mr-1"></i> SELESAI
                </span>
                <small class="font-fredoka font-black text-[#00E676]">Done</small>
            </div>
            <div class="font-fredoka font-black text-4xl text-black dark:text-white my-2">{{ $completedCount }}</div>
            <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Queue Selesai Dikerjakan</div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100 flex flex-col justify-between">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-[#FF007A] text-white border-2 border-black font-fredoka font-black px-3 py-1 rounded-full">
                    <i class="fas fa-comments mr-1"></i> CHAT
                </span>
                <small class="font-fredoka font-black text-[#FF007A]">Aktif</small>
            </div>
            <div class="font-fredoka font-black text-4xl text-black dark:text-white my-2">{{ $activeChats }}</div>
            <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Percakapan Aktif</div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="row">
    <!-- Left Column: Tables -->
    <div class="col-lg-8 mb-4 space-y-6">
        
        <!-- 1. Queue Ditugaskan Kepada Saya -->
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212]">
            <div class="d-flex justify-content-between align-items-center border-b-4 border-black pb-3 mb-3">
                <h3 class="font-fredoka font-black text-xl text-black dark:text-white uppercase mb-0">
                    Queue Ditugaskan Kepada Saya ⚡
                </h3>
                <a href="{{ route('queues.index') }}" class="btn btn-sm bg-[#FFE600] text-black border-2 border-black font-fredoka font-black px-3 py-1 rounded-full shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-[#FF007A] hover:text-white">
                    Lihat Semua Queue
                </a>
            </div>
            <div class="table-responsive">
                <table class="table border-3 border-black rounded-2xl w-100 text-sm font-jakarta font-extrabold">
                    <thead>
                        <tr class="bg-[#FFE600] text-black font-fredoka font-black uppercase border-b-3 border-black">
                            <th class="p-3">QUEUE #</th>
                            <th class="p-3">NAMA TASK / PROYEK</th>
                            <th class="p-3">STATUS</th>
                            <th class="p-3">TANGGAL ASSIGN</th>
                            <th class="p-3 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assigned->take(6)->get() as $queue)
                            <tr>
                                <td class="p-3"><span class="badge bg-black text-white font-fredoka font-black">#{{ $queue->id }}</span></td>
                                <td class="p-3 font-fredoka font-black text-black dark:text-white">
                                    {{ $queue->projectRequest->title ?? 'Queue Item' }}
                                </td>
                                <td class="p-3">
                                    <span class="badge bg-[#0055FF] text-white border-2 border-black font-fredoka font-black text-xs px-2 py-1">
                                        {{ $queue->status }}
                                    </span>
                                </td>
                                <td class="p-3 font-mono text-xs">{{ $queue->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-3 text-right">
                                    <a href="{{ route('queues.show', $queue) }}" class="btn btn-sm bg-[#FF007A] text-white border-2 border-black font-fredoka font-black rounded-xl px-3 py-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-[#FFE600] hover:text-black">
                                        <i class="fas fa-edit"></i> Update
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center p-4 font-jakarta font-extrabold text-muted">
                                    Belum ada queue yang ditugaskan kepada Anda.
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
        <!-- Developer Tools Card -->
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212]">
            <h3 class="font-fredoka font-black text-xl text-black dark:text-white uppercase border-b-4 border-black pb-3 mb-3">Developer Quick Menu 🛠️</h3>
            <div class="space-y-3 font-jakarta font-extrabold">
                <a href="{{ route('daily-logs.index') }}" class="btn btn-primary font-fredoka font-black border-3 border-black rounded-2xl w-full py-2.5 bg-[#00E676] text-black hover:bg-[#FFE600] shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] flex items-center justify-between px-4">
                    <span><i class="fas fa-clipboard-list mr-2"></i> Log Pengerjaan Harian</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
                <a href="{{ route('chat.index') }}" class="btn btn-primary font-fredoka font-black border-3 border-black rounded-2xl w-full py-2.5 bg-[#0055FF] text-white hover:bg-[#FFE600] hover:text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] flex items-center justify-between px-4">
                    <span><i class="fas fa-comments mr-2"></i> Chat Diskusi Klien</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
