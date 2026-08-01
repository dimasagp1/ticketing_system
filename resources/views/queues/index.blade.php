@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    <li class="breadcrumb-item active">Antrian</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] mb-4">
            
            {{-- Card Header: Title on Left, Filter Toggle next to Title, Reset Button on Far Right --}}
            <div class="d-flex justify-content-between align-items-center border-b-4 border-black dark:border-white pb-3 mb-3 flex-wrap gap-2 w-100">
                <div class="d-flex items-center gap-3">
                    <h3 class="font-fredoka font-black text-xl text-black dark:text-white uppercase mb-0">
                        Semua Antrian Proyek 📋
                    </h3>
                    {{-- Filter Toggle Button --}}
                    <button class="btn !bg-[#FFE600] !text-black border-2 border-black font-fredoka font-black rounded-xl text-xs px-3 py-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:!bg-[#FF007A] hover:!text-white transition-all" type="button" data-toggle="collapse" data-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                        <i class="fas fa-filter mr-1"></i> Filter Antrian
                    </button>
                </div>

                {{-- Reset Button pushed to Far Right Edge --}}
                <div class="ml-auto ms-auto">
                    <a href="{{ route('queues.index') }}" class="btn !bg-gray-200 !text-black border-2 border-black font-fredoka font-black rounded-xl px-3 py-1.5 text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:!bg-[#FF007A] hover:!text-white transition-all d-inline-flex items-center gap-1">
                        <i class="fas fa-undo"></i> Atur Ulang
                    </a>
                </div>
            </div>

            {{-- Collapsible Filter Form (Only expands when clicked or when active filters exist - ZERO GAP when collapsed) --}}
            <div class="collapse {{ request()->anyFilled(['search', 'queue_status', 'priority', 'assigned', 'ticket_status', 'sla_filter']) ? 'show' : '' }} mb-4" id="filterCollapse">
                <form action="{{ route('queues.index') }}" method="GET" class="p-3 bg-[#FFFBEA] dark:bg-[#1a1a1a] border-3 border-black dark:border-white rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] font-jakarta font-extrabold text-sm">
                    <div class="form-row">
                        <div class="col-md-3 mb-2">
                            <input type="text" name="search" class="form-control border-2 border-black rounded-xl" placeholder="Cari tiket/proyek/klien..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            <select name="queue_status" class="form-control border-2 border-black rounded-xl">
                                <option value="">Status Antrian</option>
                                @foreach(['Pending', 'In Progress', 'On Hold', 'Completed', 'Cancelled'] as $status)
                                    <option value="{{ $status }}" {{ request('queue_status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 mb-2">
                            <select name="priority" class="form-control border-2 border-black rounded-xl">
                                <option value="">Prioritas</option>
                                @foreach(['Rendah', 'Sedang', 'Tinggi'] as $priority)
                                    <option value="{{ $priority }}" {{ request('priority') === $priority ? 'selected' : '' }}>{{ $priority }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <select name="assigned" class="form-control border-2 border-black rounded-xl">
                                <option value="">Penugasan</option>
                                <option value="assigned" {{ request('assigned') === 'assigned' ? 'selected' : '' }}>Ditugaskan</option>
                                <option value="unassigned" {{ request('assigned') === 'unassigned' ? 'selected' : '' }}>Belum Ditugaskan</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <select name="ticket_status" class="form-control border-2 border-black rounded-xl">
                                <option value="">Status Tiket</option>
                                @foreach(\App\Models\ProjectRequest::ticketStatusLabels() as $value => $label)
                                    <option value="{{ $value }}" {{ request('ticket_status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 mb-2">
                            <select name="sla_filter" class="form-control border-2 border-black rounded-xl">
                                <option value="">SLA</option>
                                <option value="overdue" {{ request('sla_filter') === 'overdue' ? 'selected' : '' }}>Terlambat</option>
                                <option value="today" {{ request('sla_filter') === 'today' ? 'selected' : '' }}>Jatuh Tempo Hari Ini</option>
                                <option value="at_risk_24h" {{ request('sla_filter') === 'at_risk_24h' ? 'selected' : '' }}>Risiko 24 Jam</option>
                            </select>
                        </div>
                        <div class="col-md-1 mb-2">
                            <button type="submit" class="btn !bg-[#0055FF] !text-white border-2 border-black font-fredoka font-black rounded-xl px-3 py-1.5 w-100">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Table Container --}}
            <div class="table-responsive">
                <table class="table border-3 border-black dark:border-white rounded-2xl w-100 text-sm font-jakarta font-extrabold mb-0">
                    <thead>
                        <tr class="bg-[#FFE600] text-black font-fredoka font-black uppercase border-b-3 border-black">
                            <th class="p-3">ID</th>
                            <th class="p-3">TICKET #</th>
                            <th class="p-3">PROYEK</th>
                            <th class="p-3 d-none d-lg-table-cell">CLIENT</th>
                            <th class="p-3 d-none d-md-table-cell">PETUGAS</th>
                            <th class="p-3">STATUS</th>
                            <th class="p-3 d-none d-md-table-cell">STATUS TIKET</th>
                            <th class="p-3 d-none d-xl-table-cell">SLA JATUH TEMPO</th>
                            <th class="p-3 d-none d-sm-table-cell">PROGRESS</th>
                            <th class="p-3 d-none d-lg-table-cell">BATAS WAKTU</th>
                            <th class="p-3 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($queues as $queue)
                            <tr class="border-b-2 border-black">
                                <td class="p-3"><span class="badge bg-black text-white font-fredoka font-black">#{{ $queue->id }}</span></td>
                                <td class="p-3"><span class="font-fredoka font-black text-sm text-[#0055FF] dark:text-[#FFE600]">{{ $queue->projectRequest?->ticket_number ?? '-' }}</span></td>
                                <td class="p-3 font-fredoka font-black text-black dark:text-white">{{ $queue->project_name }}</td>
                                <td class="p-3 d-none d-lg-table-cell text-xs">{{ $queue->client_name }}</td>
                                <td class="p-3 d-none d-md-table-cell">
                                    @if(auth()->user()->canApproveProjects())
                                        <form action="{{ route('queues.assign', $queue) }}" method="POST" class="d-inline-block">
                                            @csrf
                                            <select name="assigned_to" class="custom-select custom-select-sm font-weight-bold border-2 border-black rounded-xl text-xs" onchange="this.form.submit()" style="max-width: 180px;">
                                                <option value="">-- Belum Ditugaskan --</option>
                                                @foreach($developers as $dev)
                                                    <option value="{{ $dev->id }}" {{ $queue->assigned_to == $dev->id ? 'selected' : '' }}>
                                                        {{ $dev->name }} ({{ $dev->assigned_queues_count }} Aktif)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    @else
                                        <span class="badge bg-white text-black border border-black font-fredoka font-black px-2 py-1 text-xs">{{ $queue->assignedTo->name ?? 'Belum Ditugaskan' }}</span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <span class="badge bg-[#0055FF] text-white border-2 border-black font-fredoka font-black text-xs px-2 py-1">
                                        {{ $queue->status }}
                                    </span>
                                </td>
                                <td class="p-3 d-none d-md-table-cell">
                                    @if($queue->projectRequest)
                                        <span class="badge bg-[#FF007A] text-white border-2 border-black font-fredoka font-black text-xs px-2 py-1">{{ $queue->projectRequest->ticket_status_label }}</span>
                                    @else
                                        <span class="badge bg-gray-400 text-black border border-black font-fredoka font-black text-xs px-2 py-1">-</span>
                                    @endif
                                </td>
                                <td class="p-3 d-none d-xl-table-cell font-mono text-xs">
                                    @if($queue->projectRequest?->sla_resolution_due_at)
                                        <span class="{{ $queue->projectRequest->sla_resolution_due_at->isPast() && in_array($queue->projectRequest->ticket_status, \App\Models\ProjectRequest::slaTrackedTicketStatuses(), true) ? 'text-[#FF007A] font-bold' : '' }}">
                                            {{ $queue->projectRequest->sla_resolution_due_at->format('d M Y H:i') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="p-3 d-none d-sm-table-cell">
                                    <div class="progress border-2 border-black rounded-full h-3 overflow-hidden" style="width: 80px;">
                                        <div class="progress-bar bg-[#00E676]" role="progressbar" style="width: {{ $queue->progress }}%"></div>
                                    </div>
                                    <span class="font-fredoka font-bold text-xs">{{ $queue->progress }}%</span>
                                </td>
                                <td class="p-3 d-none d-lg-table-cell font-mono text-xs">{{ $queue->deadline ? $queue->deadline->format('d M Y') : '-' }}</td>
                                <td class="p-3 text-right">
                                    <a href="{{ route('progress.show', $queue) }}" class="btn btn-sm !bg-[#00E676] !text-black border-2 border-black font-fredoka font-black rounded-xl px-3 py-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:!bg-[#FFE600] transition-all">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center p-4 font-jakarta font-extrabold text-muted">Belum ada proyek dalam antrian.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($queues->hasPages())
                <div class="card-footer bg-transparent border-top-0 px-4 py-3">
                    {{ $queues->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
