<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Proyek</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
            color: #0056b3;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        .summary {
            margin-bottom: 20px;
        }
        .summary table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary td {
            padding: 5px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .summary strong {
            color: #0056b3;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        table.data-table th {
            background-color: #0056b3;
            color: #fff;
            font-weight: bold;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .status-badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            color: #fff;
            text-align: center;
            display: inline-block;
        }
        .status-open { background-color: #dc3545; }
        .status-in-progress { background-color: #17a2b8; }
        .status-pending-user { background-color: #ffc107; color: #000; }
        .status-paused { background-color: #343a40; }
        .status-resolved { background-color: #28a745; }
        .status-closed { background-color: #6c757d; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN STATUS PROYEK & ANTIAN TIKET</h2>
        <p>Periode: {{ $periodLabel }}</p>
    </div>

    <div class="summary">
        <table>
            <tr>
                <td><strong>Total Proyek:</strong> {{ $stats['total'] }}</td>
                <td><strong>Menunggu (Terbuka):</strong> {{ $stats['pending'] }}</td>
                <td><strong>Sedang Dikerjakan:</strong> {{ $stats['in_progress'] }}</td>
                <td><strong>Selesai (Terselesaikan/Ditutup):</strong> {{ $stats['completed'] }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">No. Tiket</th>
                <th width="25%">Nama Proyek / Judul</th>
                <th width="15%">Klien</th>
                <th width="15%">Developer Ditugaskan</th>
                <th width="10%">Status</th>
                <th width="15%">Tanggal Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $index => $project)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $project->ticket_number }}</td>
                    <td>{{ $project->project_name }}</td>
                    <td>{{ $project->client ? $project->client->name : '-' }}</td>
                    <td>{{ $project->developer ? $project->developer->name : '-' }}</td>
                    <td>
                        @php
                            $badgeClass = '';
                            $statusLabel = '';
                            switch($project->ticket_status) {
                                case 'open': $badgeClass = 'status-open'; $statusLabel = 'Terbuka'; break;
                                case 'in_progress': $badgeClass = 'status-in-progress'; $statusLabel = 'Sedang Dikerjakan'; break;
                                case 'pending_user': $badgeClass = 'status-pending-user'; $statusLabel = 'Menunggu Pengguna'; break;
                                case 'paused': $badgeClass = 'status-paused'; $statusLabel = 'Dijeda'; break;
                                case 'resolved': $badgeClass = 'status-resolved'; $statusLabel = 'Terselesaikan'; break;
                                case 'closed': $badgeClass = 'status-closed'; $statusLabel = 'Ditutup'; break;
                                default: $badgeClass = 'status-open'; $statusLabel = $project->ticket_status; break;
                            }
                        @endphp
                        <span class="status-badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td>{{ $project->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data proyek pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: {{ $generator }} pada {{ $generatedAt }}
    </div>

</body>
</html>
