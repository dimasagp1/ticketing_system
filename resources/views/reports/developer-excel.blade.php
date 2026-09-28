<table>
    {{-- Header Title --}}
    <tr>
        <th colspan="13" style="font-size: 14pt; font-weight: bold; text-align: center; height: 35px; vertical-align: middle;">
            LAPORAN KINERJA &amp; BEBAN KERJA DEVELOPER
        </th>
    </tr>
    <tr>
        <td colspan="13" style="text-align: center; color: #4b5563; font-size: 10pt;">
            Sistem Antrean &amp; Manajemen Tiket Departemen IT
        </td>
    </tr>
    <tr>
        <td colspan="13"></td>
    </tr>

    {{-- Meta Information --}}
    <tr>
        <td style="font-weight: bold; width: 5%;">Developer:</td>
        <td colspan="4" style="font-weight: bold; color: #1e3a8a;">
            {{ $selectedDeveloper ? $selectedDeveloper->name : 'Semua Tim Developer (Global)' }}
        </td>
        <td style="font-weight: bold; width: 12%;">Periode:</td>
        <td colspan="7">{{ $periodLabel }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Kategori Tiket:</td>
        <td colspan="4">
            {{ $category === 'technical_support' ? 'Technical Support' : ($category === 'project' ? 'Project Request' : 'Semua Kategori') }}
        </td>
        <td style="font-weight: bold;">Rentang Tanggal:</td>
        <td colspan="7">{{ $startDate->format('d/m/Y') }} s/d {{ $endDate->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Dicetak Oleh:</td>
        <td colspan="4">{{ auth()->user()->name }}</td>
        <td style="font-weight: bold;">Waktu Ekspor:</td>
        <td colspan="7">{{ now()->translatedFormat('d F Y H:i') }}</td>
    </tr>
    <tr>
        <td colspan="13"></td>
    </tr>

    {{-- Summary KPI --}}
    <tr>
        <th colspan="13" style="font-weight: bold; background-color: #f1f5f9; color: #1e293b; font-size: 11pt; height: 25px; vertical-align: middle;">
            RINGKASAN KINERJA &amp; KPI
        </th>
    </tr>
    <tr>
        <td style="font-weight: bold; background-color: #f8fafc;">Total Penugasan:</td>
        <td style="font-weight: bold; color: #2563eb;">{{ $summary['total_assigned'] }} Tiket</td>
        <td></td>
        <td style="font-weight: bold; background-color: #f8fafc;">Rasio Penyelesaian:</td>
        <td style="font-weight: bold; color: #0d9488;">{{ $summary['completion_rate'] }}%</td>
        <td></td>
        <td style="font-weight: bold; background-color: #f8fafc;">Kepatuhan SLA:</td>
        <td style="font-weight: bold; color: {{ $summary['sla_compliance_rate'] >= 85 ? '#16a34a' : '#dc2626' }};">{{ $summary['sla_compliance_rate'] }}%</td>
        <td colspan="5"></td>
    </tr>
    <tr>
        <td style="font-weight: bold; background-color: #f8fafc;">Selesai (Resolved/Closed):</td>
        <td style="font-weight: bold; color: #16a34a;">{{ $summary['resolved_count'] }} Tiket</td>
        <td></td>
        <td style="font-weight: bold; background-color: #f8fafc;">Sedang Dikerjakan:</td>
        <td style="font-weight: bold; color: #d97706;">{{ $summary['in_progress_count'] }} Tiket</td>
        <td></td>
        <td style="font-weight: bold; background-color: #f8fafc;">Melewati Batas SLA:</td>
        <td style="font-weight: bold; color: #dc2626;">{{ $summary['overdue_count'] }} Tiket</td>
        <td colspan="5"></td>
    </tr>
    <tr>
        <td style="font-weight: bold; background-color: #f8fafc;">Rata-rata MTTR:</td>
        <td style="font-weight: bold; color: #4f46e5;">{{ $summary['avg_resolution_hours'] }} Jam</td>
        <td colspan="11"></td>
    </tr>
    <tr>
        <td colspan="13"></td>
    </tr>

    {{-- Detailed Tickets Table --}}
    <tr>
        <th style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; height: 28px; vertical-align: middle;">No</th>
        <th style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle;">No. Tiket</th>
        <th style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; vertical-align: middle;">Nama Proyek / Judul Masalah</th>
        <th style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; vertical-align: middle;">Deskripsi Masalah / Proyek</th>
        <th style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; vertical-align: middle;">Kategori</th>
        <th style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; vertical-align: middle;">Klien / Pemohon</th>
        <th style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; vertical-align: middle;">Developer Ditugaskan</th>
        <th style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle;">Prioritas</th>
        <th style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle;">Status Tiket</th>
        <th style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle;">Tanggal Masuk</th>
        <th style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle;">Tanggal Selesai</th>
        <th style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle;">Durasi (Jam)</th>
        <th style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle;">Status SLA</th>
    </tr>
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

            $durationHours = '-';
            if ($ticket->resolved_at && $ticket->created_at) {
                $durationHours = number_format($ticket->created_at->diffInMinutes($ticket->resolved_at) / 60, 1);
            }

            $statusLabel = match($ticket->ticket_status) {
                'resolved' => 'Selesai',
                'closed' => 'Ditutup',
                'in_progress' => 'Pengerjaan',
                'pending_user' => 'Menunggu User',
                'paused' => 'Dijeda',
                default => 'Terbuka',
            };

            $slaStatus = 'Normal';
            if ($ticket->sla_resolution_due_at) {
                if ($isResolved) {
                    $slaStatus = $ticket->resolved_at && $ticket->resolved_at <= $ticket->sla_resolution_due_at ? 'Tepat Waktu' : 'Terlambat';
                } elseif (now() > $ticket->sla_resolution_due_at) {
                    $slaStatus = 'Overdue';
                } else {
                    $slaStatus = 'Sesuai SLA';
                }
            }

            $cleanDesc = $ticket->description ? trim(strip_tags($ticket->description)) : '-';
        @endphp
        <tr>
            <td style="text-align: center; vertical-align: top;">{{ $index + 1 }}</td>
            <td style="font-weight: bold; vertical-align: top;">{{ $ticket->ticket_number }}</td>
            <td style="vertical-align: top;">{{ $ticket->project_name }}</td>
            <td style="vertical-align: top;">{{ $cleanDesc }}</td>
            <td style="vertical-align: top;">{{ $ticket->ticket_category === 'technical_support' ? 'Technical Support' : 'Project Request' }}</td>
            <td style="vertical-align: top;">{{ $ticket->client ? $ticket->client->name : '-' }}</td>
            <td style="vertical-align: top;">{{ $devUser ? $devUser->name : '-' }}</td>
            <td style="text-align: center; vertical-align: top;">{{ ucfirst($ticket->impact ?? 'Normal') }}</td>
            <td style="text-align: center; vertical-align: top;">{{ $statusLabel }}</td>
            <td style="text-align: center; vertical-align: top;">{{ $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '-' }}</td>
            <td style="text-align: center; vertical-align: top;">{{ $ticket->resolved_at ? $ticket->resolved_at->format('d/m/Y H:i') : '-' }}</td>
            <td style="text-align: center; vertical-align: top;">{{ $durationHours }}</td>
            <td style="text-align: center; vertical-align: top;">{{ $slaStatus }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="13" style="text-align: center; color: #64748b; padding: 15px;">
                Tidak ada data tiket yang ditemukan untuk kriteria filter ini.
            </td>
        </tr>
    @endforelse
</table>
