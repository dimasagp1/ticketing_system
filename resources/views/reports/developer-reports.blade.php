@extends('layouts.app')

@section('breadcrumb')
    @if(auth()->user()->isSuperAdmin())
        <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">Super Admin</a></li>
    @elseif(auth()->user()->isManager())
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Manager</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dasbor</a></li>
    @endif
    <li class="breadcrumb-item active">Laporan Developer</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-start align-items-lg-center flex-column flex-lg-row mb-4 pt-2 gap-3">
    <div>
        <h3 class="mb-1 font-weight-bold text-dark d-flex align-items-center">
            <span class="mr-2">👨‍💻</span> Laporan Kinerja & Beban Kerja Developer
        </h3>
        <p class="text-muted mb-0 font-weight-500">
            Analitik produktivitas, pemantauan waktu penyelesaian tiket (MTTR), kepatuhan SLA, dan rekap penugasan.
        </p>
    </div>

    {{-- Action buttons: PDF & CSV --}}
    <div class="d-flex flex-wrap align-items-center gap-2 mt-2 mt-lg-0">
        <a href="{{ route('reports.developer.export.pdf', request()->query()) }}" class="btn btn-danger btn-sm shadow-sm d-flex align-items-center px-3 py-2 font-weight-600" style="border-radius: 0.5rem;">
            <i class="fas fa-file-pdf mr-2"></i> Ekspor PDF Resmi
        </a>
        <a href="{{ route('reports.developer.export.csv', request()->query()) }}" class="btn btn-success btn-sm shadow-sm d-flex align-items-center px-3 py-2 font-weight-600" style="border-radius: 0.5rem;">
            <i class="fas fa-file-excel mr-2"></i> Ekspor Excel/CSV
        </a>
    </div>
</div>

{{-- Filter Card --}}
<div class="card shadow-sm border-0 mb-4" style="border-radius: 0.75rem;">
    <div class="card-body p-3 bg-white" style="border-radius: 0.75rem;">
        <form action="{{ route('reports.developer') }}" method="GET" id="reportFilterForm" class="row g-3 align-items-end">
            
            {{-- Developer Filter (Only visible for non-developers or as selector) --}}
            <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                <label class="small font-weight-bold text-muted mb-1 d-block"><i class="fas fa-user-cog mr-1"></i> Developer</label>
                @if(auth()->user()->isDeveloper())
                    <input type="text" class="form-control form-control-sm bg-light font-weight-bold" value="{{ auth()->user()->name }}" readonly>
                    <input type="hidden" name="developer_id" value="{{ auth()->user()->id }}">
                @else
                    <select name="developer_id" class="form-control form-control-sm font-weight-600 border-gray" onchange="document.getElementById('reportFilterForm').submit()">
                        <option value="all" {{ $developerId === 'all' ? 'selected' : '' }}>🌟 Semua Developer</option>
                        @foreach($developers as $dev)
                            <option value="{{ $dev->id }}" {{ (string)$developerId === (string)$dev->id ? 'selected' : '' }}>
                                👤 {{ $dev->name }} ({{ $dev->role_display_name }})
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            {{-- Period Filter --}}
            <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                <label class="small font-weight-bold text-muted mb-1 d-block"><i class="far fa-calendar-alt mr-1"></i> Rentang Periode</label>
                <select name="period" id="periodSelect" class="form-control form-control-sm font-weight-600 border-gray" onchange="toggleCustomDates()">
                    <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="this_week" {{ $period === 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="this_month" {{ $period === 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="this_year" {{ $period === 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
                    <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Kustom Rentang Tanggal</option>
                </select>
            </div>

            {{-- Custom Date Range inputs (hidden unless period=custom) --}}
            <div class="col-md-2 col-sm-6 mb-2 mb-md-0 custom-date-group" style="{{ $period === 'custom' ? '' : 'display: none;' }}">
                <label class="small font-weight-bold text-muted mb-1 d-block">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ $dateFromInput }}" class="form-control form-control-sm">
            </div>

            <div class="col-md-2 col-sm-6 mb-2 mb-md-0 custom-date-group" style="{{ $period === 'custom' ? '' : 'display: none;' }}">
                <label class="small font-weight-bold text-muted mb-1 d-block">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ $dateToInput }}" class="form-control form-control-sm">
            </div>

            {{-- Category Filter --}}
            <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                <label class="small font-weight-bold text-muted mb-1 d-block"><i class="fas fa-tags mr-1"></i> Kategori</label>
                <select name="category" class="form-control form-control-sm font-weight-600 border-gray">
                    <option value="all" {{ $category === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                    <option value="project" {{ $category === 'project' ? 'selected' : '' }}>Project & Feature</option>
                    <option value="technical_support" {{ $category === 'technical_support' ? 'selected' : '' }}>Technical Support</option>
                </select>
            </div>

            {{-- Submit button --}}
            <div class="col-md-1 col-sm-6 mb-2 mb-md-0">
                <button type="submit" class="btn btn-primary btn-sm btn-block font-weight-600 shadow-sm">
                    <i class="fas fa-filter mr-1"></i> Terapkan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- KPI Summary Cards --}}
<div class="row mb-4">
    <div class="col-xl-2 col-lg-4 col-sm-6 mb-3">
        <div class="card h-100 border-0 shadow-sm bg-white" style="border-radius: 0.75rem; border-left: 4px solid #2563eb !important;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <small class="text-muted font-weight-bold text-uppercase" style="font-size: 0.75rem;">Total Ditugaskan</small>
                    <span class="badge badge-light-primary text-primary p-1"><i class="fas fa-tasks"></i></span>
                </div>
                <div class="h3 font-weight-bold text-dark mb-0">{{ number_format($summary['total_assigned']) }}</div>
                <small class="text-muted font-weight-500">Tiket masuk di periode ini</small>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-lg-4 col-sm-6 mb-3">
        <div class="card h-100 border-0 shadow-sm bg-white" style="border-radius: 0.75rem; border-left: 4px solid #10b981 !important;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <small class="text-muted font-weight-bold text-uppercase" style="font-size: 0.75rem;">Selesai (Resolved)</small>
                    <span class="badge badge-light-success text-success p-1"><i class="fas fa-check-circle"></i></span>
                </div>
                <div class="h3 font-weight-bold text-success mb-0">{{ number_format($summary['resolved_count']) }}</div>
                <small class="text-success font-weight-600"><i class="fas fa-percentage mr-1"></i>{{ $summary['completion_rate'] }}% Tingkat Selesai</small>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-lg-4 col-sm-6 mb-3">
        <div class="card h-100 border-0 shadow-sm bg-white" style="border-radius: 0.75rem; border-left: 4px solid #f59e0b !important;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <small class="text-muted font-weight-bold text-uppercase" style="font-size: 0.75rem;">Sedang Dikerjakan</small>
                    <span class="badge badge-light-warning text-warning p-1"><i class="fas fa-spinner fa-spin"></i></span>
                </div>
                <div class="h3 font-weight-bold text-warning mb-0">{{ number_format($summary['in_progress_count']) }}</div>
                <small class="text-muted font-weight-500">Antrean tugas aktif</small>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-lg-4 col-sm-6 mb-3">
        <div class="card h-100 border-0 shadow-sm bg-white" style="border-radius: 0.75rem; border-left: 4px solid #ef4444 !important;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <small class="text-muted font-weight-bold text-uppercase" style="font-size: 0.75rem;">Melewati SLA</small>
                    <span class="badge badge-light-danger text-danger p-1"><i class="fas fa-exclamation-triangle"></i></span>
                </div>
                <div class="h3 font-weight-bold text-danger mb-0">{{ number_format($summary['overdue_count']) }}</div>
                <small class="text-danger font-weight-500">Tiket berisiko overdue</small>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-lg-4 col-sm-6 mb-3">
        <div class="card h-100 border-0 shadow-sm bg-white" style="border-radius: 0.75rem; border-left: 4px solid #6366f1 !important;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <small class="text-muted font-weight-bold text-uppercase" style="font-size: 0.75rem;">Rata-rata MTTR</small>
                    <span class="badge badge-light-info text-info p-1"><i class="fas fa-stopwatch"></i></span>
                </div>
                <div class="h3 font-weight-bold text-dark mb-0">{{ $summary['avg_resolution_hours'] }} <span class="text-muted font-weight-normal" style="font-size: 0.9rem;">Jam</span></div>
                <small class="text-muted font-weight-500">Waktu penyelesaian</small>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-lg-4 col-sm-6 mb-3">
        <div class="card h-100 border-0 shadow-sm bg-white" style="border-radius: 0.75rem; border-left: 4px solid #06b6d4 !important;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <small class="text-muted font-weight-bold text-uppercase" style="font-size: 0.75rem;">Kepatuhan SLA</small>
                    <span class="badge badge-light-primary text-primary p-1"><i class="fas fa-shield-alt"></i></span>
                </div>
                <div class="h3 font-weight-bold text-primary mb-0">{{ $summary['sla_compliance_rate'] }}%</div>
                <small class="text-muted font-weight-500">Target resolusi tepat waktu</small>
            </div>
        </div>
    </div>
</div>

{{-- Charts Section --}}
<div class="row mb-4">
    {{-- Chart 1: Timeline Trend --}}
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 0.75rem;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title font-weight-bold text-dark mb-0">📈 Tren Produktivitas & Penyelesaian Tiket</h5>
                    <small class="text-muted">Perbandingan tiket masuk vs tiket terselesaikan ({{ $periodLabel }})</small>
                </div>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                <div class="position-relative w-100" style="height: 320px;">
                    <canvas id="developerTimelineChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart 2: Status Doughnut --}}
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 0.75rem;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                <h5 class="card-title font-weight-bold text-dark mb-0">🍩 Distribusi Status Tugas</h5>
                <small class="text-muted">Proporsi status pengerjaan tiket developer</small>
            </div>
            <div class="card-body px-4 pb-4 pt-2 d-flex align-items-center justify-content-center">
                <div class="position-relative w-100" style="height: 280px;">
                    <canvas id="developerStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart 3 & Developer Leaderboard (If multiple developers shown) --}}
@if($developerId === 'all' && $devStats->count() > 1)
<div class="row mb-4">
    <div class="col-lg-7 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 0.75rem;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                <h5 class="card-title font-weight-bold text-dark mb-0">📊 Perbandingan Produktivitas Antar Tim Developer</h5>
                <small class="text-muted">Total penugasan vs tiket selesai per developer</small>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                <div class="position-relative w-100" style="height: 300px;">
                    <canvas id="developerComparisonChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 0.75rem;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                <h5 class="card-title font-weight-bold text-dark mb-0">🏆 Peringkat Penyelesaian Tim</h5>
                <small class="text-muted">Rasio penyelesaian tertinggi pada periode ini</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-4 border-0">Developer</th>
                                <th class="text-center border-0">Total</th>
                                <th class="text-center border-0">Selesai</th>
                                <th class="pr-4 text-right border-0">Rasio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($devStats as $dev)
                                <tr>
                                    <td class="pl-4 font-weight-600 text-dark">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle mr-2 bg-light-primary text-primary font-weight-bold" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                                                {{ substr($dev['name'], 0, 1) }}
                                            </div>
                                            <span>{{ $dev['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center font-weight-600">{{ $dev['total'] }}</td>
                                    <td class="text-center font-weight-600 text-success">{{ $dev['resolved'] }}</td>
                                    <td class="pr-4 text-right">
                                        <span class="badge {{ $dev['rate'] >= 80 ? 'badge-success' : ($dev['rate'] >= 50 ? 'badge-warning' : 'badge-secondary') }} px-2 py-1">
                                            {{ $dev['rate'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Detailed Tickets List --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius: 0.75rem;">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title font-weight-bold text-dark mb-0">📋 Rincian Daftar Tiket & Penugasan Developer</h5>
            <small class="text-muted">Menampilkan daftar tiket yang masuk dalam filter periode {{ $periodLabel }}</small>
        </div>
        <span class="badge badge-light-primary text-primary font-weight-bold px-3 py-2" style="font-size: 0.85rem; border-radius: 0.5rem;">
            Total: {{ $tickets instanceof \Illuminate\Pagination\LengthAwarePaginator ? $tickets->total() : $tickets->count() }} Tiket
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="pl-4" style="width: 5%;">No</th>
                        <th style="width: 14%;">No. Tiket</th>
                        <th style="width: 25%;">Nama Proyek / Masalah</th>
                        <th style="width: 15%;">Klien / Pemohon</th>
                        <th style="width: 15%;">Petugas / Developer</th>
                        <th class="text-center" style="width: 12%;">Status</th>
                        <th class="text-center" style="width: 15%;">SLA Resolution</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $index => $ticket)
                        <tr>
                            <td class="pl-4 text-muted">
                                {{ $tickets instanceof \Illuminate\Pagination\LengthAwarePaginator ? $tickets->firstItem() + $index : $index + 1 }}
                            </td>
                            <td>
                                <a href="{{ route('project-requests.show', $ticket) }}" class="font-weight-bold text-primary hover-underline">
                                    {{ $ticket->ticket_number }}
                                </a>
                                @if($ticket->ticket_category === 'technical_support')
                                    <span class="badge badge-info ml-1" style="font-size: 0.7rem;">Teknis</span>
                                @else
                                    <span class="badge badge-primary ml-1" style="font-size: 0.7rem;">Proyek</span>
                                @endif
                            </td>
                            <td>
                                <div class="font-weight-600 text-dark text-truncate" style="max-width: 320px;" title="{{ $ticket->project_name }}">
                                    {{ $ticket->project_name }}
                                </div>
                                <small class="text-muted">Dibuat: {{ $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '-' }}</small>
                            </td>
                            <td>
                                <div class="font-weight-600 text-dark">{{ $ticket->client ? $ticket->client->name : '-' }}</div>
                                <small class="text-muted">{{ $ticket->client && $ticket->client->company ? $ticket->client->company : '' }}</small>
                            </td>
                            <td>
                                @php
                                    $devUser = $ticket->developer ?? ($ticket->queue ? $ticket->queue->assignedTo : null);
                                @endphp
                                @if($devUser)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle mr-2 bg-light-primary text-primary font-weight-bold" style="width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">
                                            {{ substr($devUser->name, 0, 1) }}
                                        </div>
                                        <span class="font-weight-600 text-dark">{{ $devUser->name }}</span>
                                    </div>
                                @else
                                    <span class="badge badge-light text-muted">Belum Ditugaskan</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $badge = match($ticket->ticket_status) {
                                        'resolved' => 'badge-success',
                                        'closed' => 'badge-secondary',
                                        'in_progress' => 'badge-warning',
                                        'pending_user' => 'badge-info',
                                        'paused' => 'badge-dark',
                                        default => 'badge-danger',
                                    };
                                    $label = match($ticket->ticket_status) {
                                        'resolved' => 'Selesai',
                                        'closed' => 'Ditutup',
                                        'in_progress' => 'Pengerjaan',
                                        'pending_user' => 'Menunggu User',
                                        'paused' => 'Dijeda',
                                        default => 'Terbuka',
                                    };
                                @endphp
                                <span class="badge {{ $badge }} px-2 py-1 font-weight-600">{{ $label }}</span>
                            </td>
                            <td class="text-center">
                                @if($ticket->sla_resolution_due_at)
                                    @php
                                        $isResolved = in_array($ticket->ticket_status, ['resolved', 'closed']);
                                        $isOverdue = $isResolved 
                                            ? ($ticket->resolved_at && $ticket->resolved_at > $ticket->sla_resolution_due_at)
                                            : (now() > $ticket->sla_resolution_due_at);
                                    @endphp
                                    <div class="small font-weight-600 {{ $isOverdue ? 'text-danger' : 'text-success' }}">
                                        {{ $ticket->sla_resolution_due_at->format('d/m/Y H:i') }}
                                    </div>
                                    <small class="badge {{ $isOverdue ? 'badge-danger' : 'badge-light-success text-success' }} px-1">
                                        {{ $isOverdue ? 'Terlambat / Overdue' : 'Sesuai SLA' }}
                                    </small>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-gray-300 d-block"></i>
                                Tidak ada data tiket yang ditemukan untuk kriteria filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets instanceof \Illuminate\Pagination\LengthAwarePaginator && $tickets->hasPages())
            <div class="px-4 py-3 border-top bg-white d-flex justify-content-end">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function toggleCustomDates() {
        const period = document.getElementById('periodSelect').value;
        const customGroups = document.querySelectorAll('.custom-date-group');
        if (period === 'custom') {
            customGroups.forEach(el => el.style.display = 'block');
        } else {
            customGroups.forEach(el => el.style.display = 'none');
            document.getElementById('reportFilterForm').submit();
        }
    }

    // Chart 1: Timeline Trend
    const timelineCtx = document.getElementById('developerTimelineChart').getContext('2d');
    new Chart(timelineCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($timelineLabels) !!},
            datasets: [
                {
                    label: 'Tiket Masuk / Ditugaskan',
                    data: {!! json_encode($timelineTotalSeries) !!},
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2.5,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                },
                {
                    label: 'Tiket Terselesaikan',
                    data: {!! json_encode($timelineResolvedSeries) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2.5,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: { position: 'top' },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });

    // Chart 2: Status Distribution
    const statusCtx = document.getElementById('developerStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($statusCounts)) !!},
            datasets: [{
                data: {!! json_encode(array_values($statusCounts)) !!},
                backgroundColor: [
                    '#10b981', // Selesai / Ditutup
                    '#f59e0b', // Sedang Dikerjakan
                    '#06b6d4', // Menunggu User
                    '#4b5563', // Dijeda
                    '#ef4444', // Terbuka / Baru
                ],
                borderWidth: 2,
                borderColor: '#ffffff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, padding: 15 }
                }
            },
            cutout: '65%'
        }
    });

    @if($developerId === 'all' && $devStats->count() > 1)
    // Chart 3: Developer Comparison (Bar Chart)
    const compCtx = document.getElementById('developerComparisonChart').getContext('2d');
    new Chart(compCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($devStats->pluck('name')) !!},
            datasets: [
                {
                    label: 'Total Penugasan',
                    data: {!! json_encode($devStats->pluck('total')) !!},
                    backgroundColor: '#3b82f6',
                    borderRadius: 4,
                },
                {
                    label: 'Selesai (Resolved)',
                    data: {!! json_encode($devStats->pluck('resolved')) !!},
                    backgroundColor: '#10b981',
                    borderRadius: 4,
                },
                {
                    label: 'Sedang Dikerjakan',
                    data: {!! json_encode($devStats->pluck('in_progress')) !!},
                    backgroundColor: '#f59e0b',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            },
            plugins: {
                legend: { position: 'top' }
            }
        }
    });
    @endif
</script>
@endpush
@endsection
