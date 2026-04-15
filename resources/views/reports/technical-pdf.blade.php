<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Teknis Harian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #1f2937;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .header h2 {
            margin: 0;
            color: #1e40af;
            font-size: 18px;
        }
        .header p {
            margin: 4px 0 0;
            color: #475569;
            font-size: 11px;
        }
        .section-title {
            margin-top: 14px;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: bold;
            color: #111827;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #e5edff;
            color: #1e3a8a;
            font-weight: bold;
        }
        .kpi td {
            background: #f8fafc;
            width: 25%;
        }
        .muted {
            color: #6b7280;
        }
        .footer {
            margin-top: 20px;
            font-size: 10px;
            color: #6b7280;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN OPERASIONAL TIKET TEKNIS</h2>
        <p>Tanggal: {{ $reportData['selectedDateInput'] }} | Subkategori: {{ ucfirst(str_replace('_', ' ', $reportData['selectedSubcategory'])) }}</p>
    </div>

    <div class="section-title">Ringkasan KPI</div>
    <table class="kpi">
        <tr>
            <td><strong>Total Tiket</strong><br>{{ $reportData['summary']['total_tickets'] }}</td>
            <td><strong>Terselesaikan/Ditutup</strong><br>{{ $reportData['summary']['resolved_tickets'] }}</td>
            <td><strong>Antrean Aktif</strong><br>{{ $reportData['summary']['backlog_tickets'] }}</td>
            <td><strong>Melewati SLA</strong><br>{{ $reportData['summary']['overdue_tickets'] }}</td>
        </tr>
        <tr>
            <td><strong>Rata-rata FRT (menit)</strong><br>{{ number_format($reportData['summary']['frt_minutes'], 2) }}</td>
            <td><strong>Rata-rata MTTR (jam)</strong><br>{{ number_format($reportData['summary']['mttr_hours'], 2) }}</td>
            <td><strong>Kepatuhan SLA</strong><br>{{ number_format($reportData['summary']['sla_compliance_rate'], 2) }}%</td>
            <td class="muted">Sumber data: ticket_category = technical_support (dukungan teknis)</td>
        </tr>
    </table>

    <div class="section-title">Rincian Subkategori</div>
    <table>
        <thead>
            <tr>
                <th width="70%">Subkategori</th>
                <th width="30%">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData['subcategoryBreakdown'] as $row)
                <tr>
                    <td>{{ ucfirst(str_replace('_', ' ', $row->label)) }}</td>
                    <td>{{ $row->total }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Performa Teknisi</div>
    <table>
        <thead>
            <tr>
                <th width="60%">Teknisi</th>
                <th width="20%">Total Tiket</th>
                <th width="20%">Terselesaikan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData['technicianPerformance'] as $row)
                <tr>
                    <td>{{ $row->technician_name }}</td>
                    <td>{{ $row->total_tickets }}</td>
                    <td>{{ $row->resolved_tickets }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Tidak ada data teknisi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Daftar Tiket Melewati SLA</div>
    <table>
        <thead>
            <tr>
                <th width="15%">Tiket</th>
                <th width="35%">Judul</th>
                <th width="15%">Subkategori</th>
                <th width="10%">Status</th>
                <th width="25%">Batas SLA</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData['overdueList'] as $item)
                <tr>
                    <td>{{ $item->ticket_number }}</td>
                    <td>{{ $item->project_name }}</td>
                    <td>{{ \App\Models\ProjectRequest::technicalSubcategoryLabels()[$item->technical_subcategory] ?? 'Tidak diklasifikasikan' }}</td>
                    <td>{{ $item->ticket_status }}</td>
                    <td>{{ optional($item->sla_resolution_due_at)->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Tidak ada tiket yang melewati SLA.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Digenerate oleh {{ $generator }} pada {{ $generatedAt }}
    </div>
</body>
</html>
