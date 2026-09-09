<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kinerja Developer - {{ $selectedDeveloper ? $selectedDeveloper->name : 'Semua Developer' }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.2cm 1.2cm 1.2cm 1.2cm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat Header */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .kop-logo-td {
            width: 100px;
            vertical-align: middle;
            text-align: center;
        }

        .kop-logo-img {
            max-width: 85px;
            max-height: 70px;
        }

        .kop-logo-placeholder {
            width: 75px;
            height: 55px;
            border: 2px dashed #9ca3af;
            line-height: 55px;
            text-align: center;
            font-size: 8pt;
            font-weight: bold;
            color: #6b7280;
        }

        .kop-text-td {
            text-align: center;
            vertical-align: middle;
        }

        .kop-title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #111827;
            margin: 0;
        }

        .kop-subtitle {
            font-size: 9.5pt;
            color: #374151;
            margin-top: 2px;
        }

        .kop-address {
            font-size: 8.5pt;
            color: #4b5563;
            margin-top: 2px;
        }

        .kop-divider {
            border: none;
            border-top: 2.5px solid #111827;
            border-bottom: 0.8px solid #111827;
            height: 2px;
            margin-top: 4px;
            margin-bottom: 12px;
        }

        /* Document Title */
        .doc-header {
            text-align: center;
            margin-bottom: 12px;
        }

        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e3a8a;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .doc-subtitle {
            font-size: 9pt;
            color: #4b5563;
            margin-top: 2px;
        }

        /* Metadata info box */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }

        .meta-table td {
            padding: 5px 10px;
            font-size: 8.5pt;
            vertical-align: middle;
        }

        .meta-label {
            font-weight: bold;
            color: #475569;
            width: 15%;
        }

        .meta-value {
            color: #0f172a;
            width: 35%;
        }

        /* KPI Box Table */
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .kpi-table td {
            width: 16.66%;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: center;
            background: #ffffff;
        }

        .kpi-title {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 2px;
        }

        .kpi-value {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
        }

        .kpi-sub {
            font-size: 7.5pt;
            color: #64748b;
            margin-top: 1px;
        }

        /* Section header */
        .section-title {
            font-size: 9.5pt;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 6px;
            text-transform: uppercase;
            border-bottom: 1.5px solid #93c5fd;
            padding-bottom: 3px;
        }

        /* Data table */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 7px;
            font-size: 8pt;
            vertical-align: middle;
        }

        table.data-table th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            font-size: 8pt;
            text-transform: uppercase;
        }

        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 7.5pt;
            font-weight: bold;
            border-radius: 3px;
            text-align: center;
        }

        .badge-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-warning { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-danger { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .badge-info { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .badge-secondary { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

        /* Signatures Section */
        .signature-wrapper {
            margin-top: 16px;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .signature-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 40px;
        }

        .sig-place-date {
            font-size: 8.5pt;
            margin-bottom: 4px;
            color: #334155;
        }

        .sig-title {
            font-size: 9pt;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .sig-space {
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sig-img {
            max-height: 55px;
            max-width: 140px;
        }

        .sig-name {
            font-size: 9.5pt;
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }

        .sig-role {
            font-size: 8pt;
            color: #64748b;
            margin-top: 1px;
        }

        .footer {
            margin-top: 12px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 4px;
            font-size: 7.5pt;
            color: #64748b;
            text-align: right;
        }
    </style>
</head>
<body>

    <!-- Kop Surat Header -->
    <table class="kop-table">
        <tr>
            <td class="kop-logo-td">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" class="kop-logo-img" alt="Logo">
                @else
                    <div class="kop-logo-placeholder">LOGO</div>
                @endif
            </td>
            <td class="kop-text-td">
                <div class="kop-title">{{ $settings['company_department_name'] ?? 'DEPARTEMEN INFORMATION TECHNOLOGY' }}</div>
                <div class="kop-subtitle">{{ $settings['company_subtitle'] ?? 'Laporan Produktivitas dan Kinerja Developer' }}</div>
                <div class="kop-address">{{ $settings['company_address'] ?? 'Jl. Raya Perusahaan No. 123' }}, Telp: {{ $settings['company_phone'] ?? '(021) 1234567' }} - {{ $settings['company_city'] ?? 'Purbalingga' }}</div>
            </td>
        </tr>
    </table>
    <div class="kop-divider"></div>

    <!-- Document Header -->
    <div class="doc-header">
        <div class="doc-title">LAPORAN KINERJA & PENUGASAN DEVELOPER</div>
        <div class="doc-subtitle">Rekapitulasi Tiket, Waktu Resolusi (MTTR), dan Tingkat Kepatuhan SLA</div>
    </div>

    <!-- Meta Information -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">Petugas / Developer:</td>
            <td class="meta-value">{{ $selectedDeveloper ? $selectedDeveloper->name : 'Semua Tim Developer (Global)' }}</td>
            <td class="meta-label">Periode Laporan:</td>
            <td class="meta-value">{{ $periodLabel }}</td>
        </tr>
        <tr>
            <td class="meta-label">Kategori Tiket:</td>
            <td class="meta-value">{{ $category === 'technical_support' ? 'Technical Support' : ($category === 'project' ? 'Project & Feature' : 'Semua Kategori') }}</td>
            <td class="meta-label">Dicetak Oleh:</td>
            <td class="meta-value">{{ $generator }} ({{ $generatedAt }})</td>
        </tr>
    </table>

    <!-- KPI Summary Box -->
    <table class="kpi-table">
        <tr>
            <td>
                <div class="kpi-title">Total Penugasan</div>
                <div class="kpi-value" style="color: #2563eb;">{{ $summary['total_assigned'] }}</div>
                <div class="kpi-sub">Tiket Dikelola</div>
            </td>
            <td>
                <div class="kpi-title">Terselesaikan</div>
                <div class="kpi-value" style="color: #16a34a;">{{ $summary['resolved_count'] }}</div>
                <div class="kpi-sub">Resolved / Closed</div>
            </td>
            <td>
                <div class="kpi-title">Sedang Dikerjakan</div>
                <div class="kpi-value" style="color: #d97706;">{{ $summary['in_progress_count'] }}</div>
                <div class="kpi-sub">Antrean Aktif</div>
            </td>
            <td>
                <div class="kpi-title">Rasio Penyelesaian</div>
                <div class="kpi-value" style="color: #0d9488;">{{ $summary['completion_rate'] }}%</div>
                <div class="kpi-sub">Tingkat Sukses</div>
            </td>
            <td>
                <div class="kpi-title">Rata-rata MTTR</div>
                <div class="kpi-value" style="color: #4f46e5;">{{ $summary['avg_resolution_hours'] }} <span style="font-size: 8pt; font-weight: normal;">Jam</span></div>
                <div class="kpi-sub">Waktu Pengerjaan</div>
            </td>
            <td>
                <div class="kpi-title">Kepatuhan SLA</div>
                <div class="kpi-value" style="color: {{ $summary['sla_compliance_rate'] >= 85 ? '#16a34a' : '#dc2626' }};">{{ $summary['sla_compliance_rate'] }}%</div>
                <div class="kpi-sub">{{ $summary['overdue_count'] }} Overdue</div>
            </td>
        </tr>
    </table>

    <!-- Detailed Tickets Table -->
    <div class="section-title">Rincian Tiket & Tugas yang Ditangani ({{ count($tickets) }} Tiket)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 13%;">No. Tiket</th>
                <th style="width: 25%;">Nama Permintaan / Proyek</th>
                <th style="width: 14%;">Klien / Departemen</th>
                <th style="width: 14%;">Developer</th>
                <th style="width: 10%; text-align: center;">Status</th>
                <th style="width: 10%; text-align: center;">Tgl Selesai</th>
                <th style="width: 10%; text-align: center;">Status SLA</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $index => $ticket)
                @php
                    $devUser = $ticket->developer ?? ($ticket->queue ? $ticket->queue->assignedTo : null);
                    $isResolved = in_array($ticket->ticket_status, ['resolved', 'closed']);
                    $isOverdue = false;
                    if ($ticket->sla_resolution_due_at) {
                        $isOverdue = $isResolved
                            ? ($ticket->resolved_at && $ticket->resolved_at > $ticket->sla_resolution_due_at)
                            : (now() > $ticket->sla_resolution_due_at);
                    }
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td><strong>{{ $ticket->ticket_number }}</strong></td>
                    <td>{{ $ticket->project_name }}</td>
                    <td>{{ $ticket->client ? $ticket->client->name : '-' }}</td>
                    <td>{{ $devUser ? $devUser->name : '-' }}</td>
                    <td style="text-align: center;">
                        @if($isResolved)
                            <span class="badge badge-success">Selesai</span>
                        @elseif($ticket->ticket_status === 'in_progress')
                            <span class="badge badge-warning">Pengerjaan</span>
                        @elseif($ticket->ticket_status === 'pending_user')
                            <span class="badge badge-info">Menunggu User</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($ticket->ticket_status) }}</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        {{ $ticket->resolved_at ? $ticket->resolved_at->format('d/m/Y') : ($isResolved ? $ticket->updated_at->format('d/m/Y') : '-') }}
                    </td>
                    <td style="text-align: center;">
                        @if($ticket->sla_resolution_due_at)
                            <span class="badge {{ $isOverdue ? 'badge-danger' : 'badge-success' }}">
                                {{ $isOverdue ? 'Overdue' : 'Sesuai SLA' }}
                            </span>
                        @else
                            <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #64748b; padding: 15px;">
                        Tidak ada catatan tiket/penugasan pada rentang filter ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Formal Signatures -->
    <div class="signature-wrapper">
        <table class="signature-table">
            <tr>
                <td>
                    <div class="sig-place-date">{{ $settings['company_city'] ?? 'Purbalingga' }}, {{ now()->translatedFormat('d F Y') }}</div>
                    <div class="sig-title">Pelaksana Tugas / Developer,</div>
                    <div class="sig-space">
                        @if(!empty($devSigBase64))
                            <img src="{{ $devSigBase64 }}" class="sig-img" alt="TTD Developer">
                        @endif
                    </div>
                    <div class="sig-name">{{ $selectedDeveloper ? $selectedDeveloper->name : auth()->user()->name }}</div>
                    <div class="sig-role">{{ $selectedDeveloper ? $selectedDeveloper->role_display_name : 'IT Developer' }}</div>
                </td>
                <td>
                    <div class="sig-place-date">Mengetahui & Menyetujui,</div>
                    <div class="sig-title">{{ $settings['head_of_it_title'] ?? 'Head of IT / Supervisor' }},</div>
                    <div class="sig-space">
                        @if(!empty($supervisorSigBase64))
                            <img src="{{ $supervisorSigBase64 }}" class="sig-img" alt="TTD Supervisor">
                        @endif
                    </div>
                    <div class="sig-name">{{ $settings['head_of_it_name'] ?? 'Head of IT / Manager' }}</div>
                    <div class="sig-role">Departemen Information Technology</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Document Footer -->
    <div class="footer">
        Dokumen ini dibuat otomatis oleh Sistem Antrean & Tiket IT pada {{ $generatedAt }}. Validitas dapat dikonfirmasi ke Departemen IT.
    </div>

</body>
</html>
