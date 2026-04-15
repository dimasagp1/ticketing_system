@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">Super Admin</a></li>
    <li class="breadcrumb-item active">Laporan Teknis Harian</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-start align-items-md-center flex-column flex-md-row mb-4 pt-2">
    <div>
        <h3 class="mb-1 font-weight-bold text-dark">Laporan Operasional Tiket Teknis</h3>
        <p class="text-muted mb-0 font-weight-500">Ringkasan harian untuk wifi, printer, komputer, instalasi software, dan dukungan umum.</p>
    </div>

    <form action="{{ route('super-admin.reports.technical') }}" method="GET" class="d-flex flex-column flex-md-row align-items-md-center mt-3 mt-md-0 bg-white p-2 rounded shadow-sm border border-light">
        <div class="form-group mb-2 mb-md-0 mr-md-2">
            <label class="small text-muted mb-1 d-block">Tanggal</label>
            <input type="date" name="date" value="{{ $reportData['selectedDateInput'] }}" class="form-control form-control-sm" />
        </div>

        <div class="form-group mb-2 mb-md-0 mr-md-2">
            <label class="small text-muted mb-1 d-block">Subkategori</label>
            <select name="subcategory" class="form-control form-control-sm">
                <option value="all" {{ $reportData['selectedSubcategory'] === 'all' ? 'selected' : '' }}>Semua</option>
                @foreach($reportData['allowedSubcategories'] as $subcategory)
                    <option value="{{ $subcategory }}" {{ $reportData['selectedSubcategory'] === $subcategory ? 'selected' : '' }}>
                        {{ \App\Models\ProjectRequest::technicalSubcategoryLabels()[$subcategory] ?? ucfirst(str_replace('_', ' ', $subcategory)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="pt-md-4 d-flex">
            <button type="submit" class="btn btn-primary btn-sm px-3">
                <i class="fas fa-filter mr-1"></i> Terapkan
            </button>
            <a
                href="{{ route('super-admin.reports.technical.export.csv', ['date' => $reportData['selectedDateInput'], 'subcategory' => $reportData['selectedSubcategory']]) }}"
                class="btn btn-success btn-sm px-3 ml-2"
            >
                <i class="fas fa-file-csv mr-1"></i> CSV
            </a>
            <a
                href="{{ route('super-admin.reports.technical.export.pdf', ['date' => $reportData['selectedDateInput'], 'subcategory' => $reportData['selectedSubcategory']]) }}"
                class="btn btn-danger btn-sm px-3 ml-2"
            >
                <i class="fas fa-file-pdf mr-1"></i> PDF
            </a>
        </div>
    </form>
</div>

<div class="row">
    <div class="col-lg-2 col-sm-6 mb-3">
        <div class="support-stat-card">
            <small class="text-muted d-block">Total Tiket</small>
            <div class="support-stat-value">{{ number_format($reportData['summary']['total_tickets']) }}</div>
        </div>
    </div>
    <div class="col-lg-2 col-sm-6 mb-3">
        <div class="support-stat-card">
            <small class="text-muted d-block">Terselesaikan/Ditutup</small>
            <div class="support-stat-value text-success">{{ number_format($reportData['summary']['resolved_tickets']) }}</div>
        </div>
    </div>
    <div class="col-lg-2 col-sm-6 mb-3">
        <div class="support-stat-card">
            <small class="text-muted d-block">Antrean Aktif</small>
            <div class="support-stat-value text-warning">{{ number_format($reportData['summary']['backlog_tickets']) }}</div>
        </div>
    </div>
    <div class="col-lg-2 col-sm-6 mb-3">
        <div class="support-stat-card">
            <small class="text-muted d-block">Melewati SLA</small>
            <div class="support-stat-value text-danger">{{ number_format($reportData['summary']['overdue_tickets']) }}</div>
        </div>
    </div>
    <div class="col-lg-2 col-sm-6 mb-3">
        <div class="support-stat-card">
            <small class="text-muted d-block">Rata-rata FRT (menit)</small>
            <div class="support-stat-value text-info">{{ number_format($reportData['summary']['frt_minutes'], 2) }}</div>
        </div>
    </div>
    <div class="col-lg-2 col-sm-6 mb-3">
        <div class="support-stat-card">
            <small class="text-muted d-block">Kepatuhan SLA</small>
            <div class="support-stat-value text-primary">{{ number_format($reportData['summary']['sla_compliance_rate'], 2) }}%</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card support-shell-card h-100">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Distribusi Tiket Teknis</h3>
                <small class="text-muted">Subkategori vs prioritas (dampak)</small>
            </div>
            <div class="card-body">
                <div class="position-relative w-100" style="height: 320px;">
                    <canvas id="technicalDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card support-shell-card h-100">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">MTTR</h3>
                <small class="text-muted">Rata-rata waktu penyelesaian</small>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center flex-column">
                <div class="display-4 font-weight-bold text-dark mb-1">{{ number_format($reportData['summary']['mttr_hours'], 2) }}</div>
                <div class="text-muted">jam</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card support-shell-card h-100">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Performa Teknisi</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-4 border-bottom-0">Teknisi</th>
                                <th class="border-bottom-0 text-center">Total</th>
                                <th class="pr-4 border-bottom-0 text-right">Terselesaikan</th>
                            </tr>
                                <th class="pl-4 border-bottom-0">Nomor Tiket</th>
                        <tbody>
                            @forelse($reportData['technicianPerformance'] as $row)
                                <tr>
                                    <td class="pl-4 font-weight-600 text-dark">{{ $row->technician_name }}</td>
                                    <td class="text-center">{{ (int) $row->total_tickets }}</td>
                                    <td class="pr-4 text-right text-success font-weight-500">{{ (int) $row->resolved_tickets }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Belum ada data teknisi pada periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card support-shell-card h-100">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Daftar Tiket Melewati SLA</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-4 border-bottom-0">Ticket</th>
                                <th class="border-bottom-0">Subkategori</th>
                                <th class="pr-4 border-bottom-0 text-right">Batas SLA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportData['overdueList'] as $item)
                                <tr>
                                    <td class="pl-4">
                                        <div class="font-weight-600 text-dark">{{ $item->ticket_number }}</div>
                                        <small class="text-muted">{{ $item->project_name }}</small>
                                    </td>
                                    <td>{{ $item->technical_subcategory_label ?? 'Tidak diklasifikasikan' }}</td>
                                    <td class="pr-4 text-right text-danger font-weight-500">
                                        {{ optional($item->sla_resolution_due_at)->format('d M Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Tidak ada tiket yang melewati SLA.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const subcategoryLabels = {!! json_encode($reportData['subcategoryBreakdown']->pluck('label')) !!};
    const subcategoryValues = {!! json_encode($reportData['subcategoryBreakdown']->pluck('total')) !!};

    const priorityLabels = {!! json_encode($reportData['priorityBreakdown']->pluck('label')) !!};
    const priorityValues = {!! json_encode($reportData['priorityBreakdown']->pluck('total')) !!};

    const subcategoryLabelMap = {
        wifi: 'Wifi',
        printer: 'Printer',
        komputer: 'Komputer',
        software_install: 'Instalasi Software',
        supporting: 'Dukungan Umum',
        unclassified: 'Tidak diklasifikasikan',
    };

    const priorityLabelMap = {
        critical: 'Kritis',
        high: 'Tinggi',
        medium: 'Sedang',
        low: 'Rendah',
    };

    const localizedSubcategoryLabels = subcategoryLabels.map((label) => subcategoryLabelMap[label] ?? label);
    const localizedPriorityLabels = priorityLabels.map((label) => priorityLabelMap[label] ?? label);

    const chartCtx = document.getElementById('technicalDistributionChart').getContext('2d');
    new Chart(chartCtx, {
        type: 'bar',
        data: {
            labels: localizedSubcategoryLabels,
            datasets: [
                {
                    label: 'Subkategori',
                    data: subcategoryValues,
                    backgroundColor: 'rgba(37, 99, 235, 0.75)',
                    borderColor: '#2563eb',
                    borderWidth: 1,
                    borderRadius: 8,
                },
                {
                    label: 'Prioritas (Dampak)',
                    data: priorityValues,
                    backgroundColor: 'rgba(16, 185, 129, 0.55)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 8,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        title: function(context) {
                            const index = context[0].dataIndex;
                            return localizedSubcategoryLabels[index] ?? '';
                        },
                        label: function(context) {
                            return `${context.dataset.label}: ${context.raw}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
