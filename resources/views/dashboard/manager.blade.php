@php
    $pendingCount = $managerPendingApprovals->count();
    $activeCount = $managerStats['active_tickets_count'] ?? 0;
    $completedCount = $managerStats['completed_this_month'] ?? 0;
    $overdueCount = $managerStats['overdue_count'] ?? 0;
@endphp

<!-- Manager Welcome & Action Banner -->
<div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="w-14 h-14 bg-[#FFE600] text-black border-3 border-black rounded-2xl flex items-center justify-center font-fredoka font-black text-2xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h2 class="font-fredoka font-black text-2xl text-black dark:text-white uppercase mb-0">
                        Dasbor Monitoring Manajerial
                    </h2>
                    <span class="badge bg-[#0055FF] text-white border-2 border-black font-fredoka font-black px-3 py-1 rounded-full text-xs">
                        {{ auth()->user()->role_display_name }}
                    </span>
                </div>
                <p class="font-jakarta font-extrabold text-sm text-muted mb-0">
                    Selamat datang kembali, <strong>{{ auth()->user()->name }}</strong>. Pantau persetujuan dan progres tiket operasional secara real-time.
                </p>
            </div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('approvals.index') }}" class="btn bg-[#FF007A] text-white border-3 border-black font-fredoka font-black rounded-2xl px-4 py-2 text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-[#FFE600] hover:text-black transition-all">
                <i class="fas fa-clipboard-check mr-1"></i> Persetujuan Saya
                @if($pendingCount > 0)
                    <span class="badge badge-warning text-dark ml-1 font-bold">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('project-requests.index') }}" class="btn bg-[#0055FF] text-white border-3 border-black font-fredoka font-black rounded-2xl px-4 py-2 text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-[#00E5FF] hover:text-black transition-all">
                <i class="fas fa-list mr-1"></i> Semua Tiket
            </a>
        </div>
    </div>
</div>

<!-- Pending Approval Alert (If any) -->
@if($pendingCount > 0)
<div class="alert bg-[#FFFBEA] border-3 border-black text-black rounded-2xl p-3 mb-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
        <i class="fas fa-exclamation-circle text-[#FF007A] text-2xl"></i>
        <div>
            <strong class="font-fredoka font-black text-base uppercase">Perhatian Diperlukan:</strong>
            <span class="font-jakarta font-extrabold text-sm ml-1">
                Ada <strong>{{ $pendingCount }} pengajuan tiket</strong> yang sedang menunggu persetujuan Anda sebagai {{ auth()->user()->role_display_name }}.
            </span>
        </div>
    </div>
    <a href="{{ route('approvals.index') }}" class="btn bg-[#FF007A] text-white border-2 border-black font-fredoka font-black rounded-xl text-xs px-3 py-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-black transition-all">
        Proses Sekarang <i class="fas fa-arrow-right ml-1"></i>
    </a>
</div>
@endif

<!-- Executive Stat Cards Row -->
<div class="row">
    <!-- Pending Approvals -->
    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100 flex flex-col justify-between">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-[#FF007A] text-white border-2 border-black font-fredoka font-black px-3 py-1 rounded-full">
                    <i class="fas fa-hourglass-half mr-1"></i> BUTUH REVIEW
                </span>
                <small class="font-fredoka font-black text-[#FF007A]">Persetujuan</small>
            </div>
            <div class="font-fredoka font-black text-4xl text-black dark:text-white my-2">{{ number_format($pendingCount) }}</div>
            <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Menunggu Approval Saya</div>
        </div>
    </div>

    <!-- Active Tickets In Progress -->
    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100 flex flex-col justify-between">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-[#0055FF] text-white border-2 border-black font-fredoka font-black px-3 py-1 rounded-full">
                    <i class="fas fa-cogs mr-1"></i> AKTIF
                </span>
                <small class="font-fredoka font-black text-[#0055FF]">Sedang Berjalan</small>
            </div>
            <div class="font-fredoka font-black text-4xl text-black dark:text-white my-2">{{ number_format($activeCount) }}</div>
            <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Tiket Dalam Pengerjaan IT</div>
        </div>
    </div>

    <!-- Completed This Month -->
    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100 flex flex-col justify-between">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-[#00E5FF] text-black border-2 border-black font-fredoka font-black px-3 py-1 rounded-full">
                    <i class="fas fa-check-circle mr-1"></i> SELESAI
                </span>
                <small class="font-fredoka font-black text-[#0088AA]">Bulan Ini</small>
            </div>
            <div class="font-fredoka font-black text-4xl text-black dark:text-white my-2">{{ number_format($completedCount) }}</div>
            <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Tiket Resolved / Closed</div>
        </div>
    </div>

    <!-- Overdue SLA Alert -->
    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100 flex flex-col justify-between">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge {{ $overdueCount > 0 ? 'bg-[#FF007A] text-white' : 'bg-[#00E5FF] text-black' }} border-2 border-black font-fredoka font-black px-3 py-1 rounded-full">
                    <i class="fas fa-stopwatch mr-1"></i> SLA STATUS
                </span>
                <small class="font-fredoka font-black {{ $overdueCount > 0 ? 'text-[#FF007A]' : 'text-success' }}">
                    {{ $overdueCount > 0 ? 'Perlu Eskalasi' : 'On Track' }}
                </small>
            </div>
            <div class="font-fredoka font-black text-4xl text-black dark:text-white my-2">{{ number_format($overdueCount) }}</div>
            <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Tiket Lewat Tenggat SLA</div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Pending Approval Table -->
    <div class="col-lg-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100">
            <div class="d-flex justify-content-between align-items-center border-b-4 border-black dark:border-white pb-3 mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="w-8 h-8 bg-[#FF007A] text-white border-2 border-black rounded-lg flex items-center justify-center font-fredoka font-black text-sm">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div>
                        <h4 class="font-fredoka font-black text-lg text-black dark:text-white uppercase mb-0">Antrean Persetujuan Saya</h4>
                        <p class="font-jakarta font-extrabold text-xs text-muted mb-0">Tiket menunggu validasi level {{ auth()->user()->role_display_name }}</p>
                    </div>
                </div>
                <a href="{{ route('approvals.index') }}" class="font-fredoka font-black text-xs text-[#0055FF] hover:underline uppercase">Lihat Semua</a>
            </div>

            @if($managerPendingApprovals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="font-fredoka text-xs text-muted uppercase">
                                <th>Tiket</th>
                                <th>Proyek / Kendala</th>
                                <th>Pemohon</th>
                                <th>Urgensi</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="font-jakarta font-bold text-xs">
                            @foreach($managerPendingApprovals->take(6) as $appr)
                            <tr>
                                <td>
                                    <span class="badge bg-black text-white border border-black rounded-lg">
                                        {{ $appr->projectRequest->ticket_number ?? ('#' . $appr->projectRequest->id) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-truncate font-bold" style="max-width: 140px;" title="{{ $appr->projectRequest->project_name }}">
                                        {{ $appr->projectRequest->project_name }}
                                    </div>
                                    <small class="text-muted">{{ $appr->projectRequest->ticket_category_label }}</small>
                                </td>
                                <td>{{ $appr->projectRequest->client->name ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-{{ $appr->projectRequest->urgency == 'critical' || $appr->projectRequest->urgency == 'high' ? 'danger' : 'info' }}">
                                        {{ $appr->projectRequest->urgency_label }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('approvals.show', $appr) }}" class="btn bg-[#0055FF] text-white border-2 border-black font-fredoka font-black rounded-xl text-xs px-3 py-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-[#FFE600] hover:text-black transition-all">
                                        <i class="fas fa-eye mr-1"></i> Tinjau
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="w-16 h-16 bg-[#00E5FF] text-black border-3 border-black rounded-full flex items-center justify-center mx-auto mb-3 font-fredoka font-black text-2xl">
                        <i class="fas fa-check"></i>
                    </div>
                    <h5 class="font-fredoka font-black text-base text-black dark:text-white uppercase mb-1">Semua Persetujuan Beres!</h5>
                    <p class="font-jakarta font-extrabold text-xs text-muted mb-0">Tidak ada pengajuan tiket yang menunggu persetujuan Anda saat ini.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Live Progress Tracking Table -->
    <div class="col-lg-6 mb-4">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] h-100">
            <div class="d-flex justify-content-between align-items-center border-b-4 border-black dark:border-white pb-3 mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="w-8 h-8 bg-[#0055FF] text-white border-2 border-black rounded-lg flex items-center justify-center font-fredoka font-black text-sm">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div>
                        <h4 class="font-fredoka font-black text-lg text-black dark:text-white uppercase mb-0">Live Tracking Tiket Berjalan</h4>
                        <p class="font-jakarta font-extrabold text-xs text-muted mb-0">Progres pengerjaan oleh teknisi / developer</p>
                    </div>
                </div>
                <a href="{{ route('project-requests.index') }}" class="font-fredoka font-black text-xs text-[#0055FF] hover:underline uppercase">Lihat Semua</a>
            </div>

            @if(isset($managerActiveTickets) && $managerActiveTickets->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="font-fredoka text-xs text-muted uppercase">
                                <th>Tiket</th>
                                <th>Teknisi PIC</th>
                                <th>Progres</th>
                                <th>Status</th>
                                <th class="text-right">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="font-jakarta font-bold text-xs">
                            @foreach($managerActiveTickets->take(6) as $ticket)
                            @php
                                $progressVal = $ticket->queue ? (int)$ticket->queue->progress : 0;
                            @endphp
                            <tr>
                                <td>
                                    <div class="text-truncate font-bold" style="max-width: 140px;" title="{{ $ticket->project_name }}">
                                        {{ $ticket->project_name }}
                                    </div>
                                    <small class="text-muted">{{ $ticket->ticket_number ?? ('#' . $ticket->id) }}</small>
                                </td>
                                <td>
                                    @if($ticket->queue && $ticket->queue->assignedTo)
                                        <span class="text-primary font-bold"><i class="fas fa-user-circle mr-1"></i> {{ $ticket->queue->assignedTo->name }}</span>
                                    @else
                                        <span class="text-muted italic"><i class="fas fa-clock mr-1"></i> Belum diassign</span>
                                    @endif
                                </td>
                                <td style="min-width: 110px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 8px; border-radius: 4px; border: 1px solid #000;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressVal }}%" aria-valuenow="{{ $progressVal }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="font-fredoka font-black text-xs">{{ $progressVal }}%</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $ticket->ticket_status_badge_class }}">
                                        {{ $ticket->ticket_status_label }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('project-requests.show', $ticket) }}" class="btn btn-sm btn-light border-2 border-black rounded-lg px-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-[#FFE600] transition-all">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <p class="font-jakarta font-extrabold text-xs text-muted mb-0">Belum ada tiket aktif dalam pengerjaan.</p>
                </div>
            @endif
        </div>
    </div>
</div>
