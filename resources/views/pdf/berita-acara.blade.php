<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Pengerjaan IT - {{ $projectRequest->ticket_number ?? ('ID-' . $projectRequest->id) }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #111;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat Header */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .kop-logo-td {
            width: 110px;
            vertical-align: middle;
            text-align: center;
        }

        .kop-logo-img {
            max-width: 90px;
            max-height: 80px;
        }

        .kop-logo-placeholder {
            width: 80px;
            height: 60px;
            border: 2px dashed #666;
            line-height: 60px;
            text-align: center;
            font-size: 9pt;
            font-weight: bold;
            color: #555;
        }

        .kop-text-td {
            text-align: center;
            vertical-align: middle;
        }

        .kop-title {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .kop-subtitle {
            font-size: 10pt;
            font-weight: normal;
            margin-top: 3px;
        }

        .kop-address {
            font-size: 9pt;
            color: #333;
            margin-top: 2px;
        }

        .kop-divider {
            border: none;
            border-top: 3px double #000;
            margin-top: 6px;
            margin-bottom: 18px;
        }

        /* Document Title */
        .doc-header {
            text-align: center;
            margin-bottom: 18px;
        }

        .doc-title {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin: 0;
        }

        .doc-number {
            font-size: 10.5pt;
            margin-top: 4px;
        }

        .opening-text {
            font-size: 10.5pt;
            margin-bottom: 15px;
            text-align: justify;
        }

        /* Section Banners */
        .section-header {
            background-color: #f3f4f6;
            border-left: 4px solid #0055FF;
            font-weight: bold;
            font-size: 11pt;
            padding: 5px 10px;
            margin-top: 14px;
            margin-bottom: 10px;
        }

        /* Key-Value Tables */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
            font-size: 10.5pt;
        }

        .info-table td.label {
            width: 180px;
            font-weight: bold;
        }

        .info-table td.colon {
            width: 15px;
            text-align: center;
        }

        /* Checkbox styling */
        .checkbox-group {
            display: inline-block;
            margin-right: 15px;
        }

        .checkbox-box {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            text-align: center;
            line-height: 11px;
            font-size: 9pt;
            font-weight: bold;
            margin-right: 4px;
            vertical-align: middle;
        }

        /* Description / Text Boxes */
        .text-box {
            border: 1px solid #777;
            padding: 8px 12px;
            min-height: 55px;
            font-size: 10pt;
            white-space: pre-wrap;
            word-wrap: break-word;
            margin-bottom: 10px;
            background-color: #fafafa;
        }

        /* Status Inline */
        .status-row {
            margin-top: 8px;
            margin-bottom: 12px;
            font-size: 10.5pt;
        }

        .status-label {
            font-weight: bold;
            margin-right: 10px;
        }

        /* Signatures Section */
        .signature-wrapper {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-date {
            text-align: right;
            font-size: 10.5pt;
            margin-bottom: 15px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .signature-table td {
            width: 33.33%;
            vertical-align: top;
            padding: 0 5px;
        }

        .signature-title {
            font-size: 10.5pt;
            margin-bottom: 60px;
        }

        .signature-name {
            font-weight: bold;
            font-size: 10.5pt;
            text-decoration: underline;
        }

        .signature-role {
            font-size: 9.5pt;
            color: #333;
            margin-top: 2px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

    <!-- Kop Surat Header -->
    <table class="kop-table">
        <tr>
            <td class="kop-logo-td">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" class="kop-logo-img" alt="Logo">
                @else
                    <div class="kop-logo-placeholder">LOGO SINI</div>
                @endif
            </td>
            <td class="kop-text-td">
                <div class="kop-title">{{ $settings['department_name'] }}</div>
                <div class="kop-subtitle">{{ $settings['subtitle'] }}</div>
                <div class="kop-address">{{ $settings['address'] }}, Telepon: {{ $settings['phone'] }}</div>
            </td>
        </tr>
    </table>

    <hr class="kop-divider">

    <!-- Document Header -->
    <div class="doc-header">
        <div class="doc-title">BERITA ACARA PENGERJAAN SUPPORTING IT</div>
        <div class="doc-number">Nomor: {{ $baNumber }}</div>
    </div>

    <!-- Opening Text -->
    <div class="opening-text">
        Pada hari ini, <strong>{{ $dayName }}</strong> tanggal <strong>{{ $dayNum }}</strong> bulan <strong>{{ $monthName }}</strong> tahun <strong>{{ $yearNum }}</strong>, telah dilakukan pengerjaan dan penanganan dukungan teknis IT dengan rincian sebagai berikut:
    </div>

    <!-- 1. Informasi Permohonan & Identitas Pengaju (Hulu) -->
    <div class="section-header">1. Informasi Permohonan & Identitas Pengaju (Hulu)</div>
    <table class="info-table">
        <tr>
            <td class="label">Nomor Tiket / BA</td>
            <td class="colon">:</td>
            <td><strong>{{ $projectRequest->ticket_number ?? ('#ID-' . $projectRequest->id) }}</strong></td>
        </tr>
        <tr>
            <td class="label">Nama Pengaju (User)</td>
            <td class="colon">:</td>
            <td>{{ $projectRequest->client->name ?? '-' }} ({{ $projectRequest->client->email ?? '' }})</td>
        </tr>
        <tr>
            <td class="label">Departemen / Unit Kerja</td>
            <td class="colon">:</td>
            <td>{{ $projectRequest->client->department ?? ($projectRequest->client->company ?? '') }}</td>
        </tr>
        <tr>
            <td class="label">Lokasi / Ruangan</td>
            <td class="colon">:</td>
            <td>{{ $projectRequest->location_detail ?: ($projectRequest->client->location ?? ($projectRequest->client->company ?? '')) }}</td>
        </tr>
        <tr>
            <td class="label">Waktu Pengajuan Tiket</td>
            <td class="colon">:</td>
            <td>{{ $projectRequest->created_at ? $projectRequest->created_at->format('d M Y H:i') : '-' }} WIB</td>
        </tr>
        <tr>
            <td class="label">Kategori Layanan IT</td>
            <td class="colon">:</td>
            <td>
                @php
                    $cat = strtolower((string) $projectRequest->ticket_category);
                    $sub = strtolower((string) $projectRequest->technical_subcategory);
                    $isHardware = in_array($cat, ['incident', 'technical_support']) && in_array($sub, ['printer', 'komputer', 'wifi']);
                    $isSoftware = in_array($cat, ['service_request', 'access', 'bug']) || in_array($sub, ['software_install', 'supporting']);
                    $isOther = !$isHardware && !$isSoftware;
                @endphp
                <span class="checkbox-group">
                    <span class="checkbox-box">{{ $isHardware ? '✓' : '' }}</span> Hardware / Jaringan
                </span>
                <span class="checkbox-group">
                    <span class="checkbox-box">{{ $isSoftware ? '✓' : '' }}</span> Software / Sistem
                </span>
                <span class="checkbox-group">
                    <span class="checkbox-box">{{ $isOther ? '✓' : '' }}</span> Lain-lain
                </span>
            </td>
        </tr>
    </table>

    <div style="font-weight: bold; font-size: 10pt; margin-top: 6px; margin-bottom: 4px;">Detail Kendala / Permintaan Awal:</div>
    <div class="text-box">
        {{ $projectRequest->description ?? '-' }}
    </div>

    @if($requirements->count() > 0)
        <div style="font-weight: bold; font-size: 10pt; margin-top: 6px; margin-bottom: 4px;">Spesifikasi Fitur / Requirements yang Diajukan:</div>
        <table class="info-table" style="border: 1px solid #ddd; margin-bottom: 10px;">
            <tr style="background-color: #f3f4f6; font-weight: bold;">
                <td style="width: 25px; border-bottom: 1px solid #ddd;">#</td>
                <td style="border-bottom: 1px solid #ddd;">Judul Kebutuhan / Fitur</td>
                <td style="width: 100px; border-bottom: 1px solid #ddd;">Tipe</td>
                <td style="width: 80px; border-bottom: 1px solid #ddd;">Prioritas</td>
            </tr>
            @foreach($requirements as $idx => $req)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td><strong>{{ $req->title }}</strong>@if($req->description) <br><small style="color:#555;">{{ $req->description }}</small>@endif</td>
                    <td>{{ ucfirst($req->category ?? 'Functional') }}</td>
                    <td>{{ ucfirst($req->priority ?? 'Medium') }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    <!-- 2. Persetujuan & Penugasan (Midstream Process) -->
    <div class="section-header">2. Persetujuan & Penugasan Tim IT (Proses Persetujuan)</div>
    <table class="info-table">
        <tr>
            <td class="label">Disetujui Oleh (Approver)</td>
            <td class="colon">:</td>
            <td>{{ $approverName }} (Waktu Persetujuan: {{ $approvedAtFormatted }})</td>
        </tr>
        <tr>
            <td class="label">Pelaksana IT (Developer)</td>
            <td class="colon">:</td>
            <td><strong>{{ $developerName }}</strong></td>
        </tr>
        <tr>
            <td class="label">Prioritas & SLA Deadline</td>
            <td class="colon">:</td>
            <td>
                Tingkat Prioritas: <strong>{{ ucfirst($projectRequest->priority ?? 'Normal') }}</strong> | 
                Target Selesai: <strong>{{ $projectRequest->queue?->deadline ? $projectRequest->queue->deadline->format('d M Y') : '-' }}</strong>
            </td>
        </tr>
    </table>

    <!-- 3. Rekam Tahapan Pengerjaan & Log Aktivitas (Hilir) -->
    <div class="section-header">3. Rekam Tahapan & Log Aktivitas Pengerjaan (Hilir / Pelaksanaan)</div>
    @if($progressLogs->count() > 0)
        <table class="info-table" style="border: 1px solid #0055FF; margin-bottom: 12px;">
            <tr style="background-color: #0055FF; color: #ffffff; font-weight: bold; font-size: 9.5pt;">
                <td style="width: 20px; text-align: center;">No</td>
                <td style="width: 105px;">Waktu Log</td>
                <td style="width: 110px;">Tahapan Pengerjaan</td>
                <td style="width: 35px; text-align: center;">%</td>
                <td>Deskripsi Pengerjaan / Activity Log</td>
                <td style="width: 90px;">Pelaksana</td>
            </tr>
            @foreach($progressLogs as $index => $log)
                <tr style="background-color: {{ $index % 2 === 0 ? '#ffffff' : '#f9fafb' }}; border-bottom: 1px solid #e5e7eb; font-size: 9.5pt;">
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td><strong>{{ $log->projectStage?->name ?? 'Update Progres' }}</strong></td>
                    <td style="text-align: center; font-weight: bold; color: #0055FF;">{{ $log->progress_percentage }}%</td>
                    <td>{{ $log->activity_description }}</td>
                    <td>{{ $log->updatedBy?->name ?? $developerName }}</td>
                </tr>
            @endforeach
        </table>
    @else
        <div class="text-box">
            {!! nl2br(e($actionTakenText)) !!}
        </div>
    @endif

    <!-- 4. Solusi & Status Akhir Penyelesaian -->
    <div class="section-header">4. Solusi & Status Akhir Penyelesaian (Resolution & Handover)</div>
    <div class="text-box">
        <strong>Ringkasan Penanganan & Hasil Solusi Akhir:</strong><br>
        {{ $projectRequest->solution ?? $actionTakenText }}
    </div>

    <table class="info-table" style="margin-top: 8px;">
        <tr>
            <td class="label">Waktu Penyelesaian Akhir</td>
            <td class="colon">:</td>
            <td><strong>{{ (isset($completionDate) && $completionDate) ? $completionDate->format('d F Y - H:i') : ($projectRequest->resolved_at ? $projectRequest->resolved_at->format('d F Y - H:i') : now()->format('d F Y - H:i')) }} WIB</strong></td>
        </tr>
        <tr>
            <td class="label">Status Akhir Dokumen</td>
            <td class="colon">:</td>
            <td>
                @php
                    $tStatus = strtolower((string) $projectRequest->ticket_status);
                    $reqStatus = strtolower((string) $projectRequest->status);
                    $isResolved = in_array($tStatus, ['resolved', 'closed']) || $reqStatus === 'approved';
                    $isMonitoring = $tStatus === 'in_progress';
                    $isPending = !$isResolved && !$isMonitoring;
                @endphp
                <span class="checkbox-group">
                    <span class="checkbox-box">{{ $isResolved ? '✓' : '' }}</span> Selesai & Serah Terima (Resolved)
                </span>
                <span class="checkbox-group">
                    <span class="checkbox-box">{{ $isMonitoring ? '✓' : '' }}</span> Dalam Pemantauan
                </span>
                <span class="checkbox-group">
                    <span class="checkbox-box">{{ $isPending ? '✓' : '' }}</span> Menunggu Tindak Lanjut
                </span>
            </td>
        </tr>
    </table>

    <!-- 5. Catatan Tambahan / Rekomendasi -->
    <div class="section-header">5. Catatan Tambahan / Rekomendasi Pemeliharaan</div>
    <div class="text-box">
        {{ $projectRequest->notes ?? '' }}
    </div>

    <!-- Signatures Section -->
    <div class="signature-wrapper">
        <div class="signature-date">
            {{ $settings['city'] }}, {{ $formattedDateString }}
        </div>

        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-title">Dibuat Oleh,</div>
                    <div class="signature-box" style="height: 100px; line-height: 100px; text-align: center; margin-bottom: 5px;">
                        @if(!empty($devSigBase64))
                            <img src="{{ $devSigBase64 }}" style="height: 95px; width: 220px; max-width: 100%; vertical-align: middle;" alt="TTD Developer">
                        @else
                            <div style="height: 80px;"></div>
                        @endif
                    </div>
                    <div class="signature-name">( {{ $developerName }} )</div>
                    <div class="signature-role">IT Developer / Engineer</div>
                </td>
                <td>
                    <div class="signature-title">Disetujui Oleh,</div>
                    <div class="signature-box" style="height: 100px; line-height: 100px; text-align: center; margin-bottom: 5px;">
                        @if(!empty($clientSigBase64))
                            <img src="{{ $clientSigBase64 }}" style="height: 95px; width: 220px; max-width: 100%; vertical-align: middle;" alt="TTD User">
                        @else
                            <div style="height: 80px;"></div>
                        @endif
                    </div>
                    <div class="signature-name">( {{ $projectRequest->client->name ?? 'Pengaju / User' }} )</div>
                    <div class="signature-role">Pengaju / User</div>
                </td>
                <td>
                    <div class="signature-title">Mengetahui,</div>
                    <div class="signature-box" style="height: 100px; line-height: 100px; text-align: center; margin-bottom: 5px;">
                        @if(!empty($supervisorSigBase64))
                            <img src="{{ $supervisorSigBase64 }}" style="height: 95px; width: 220px; max-width: 100%; vertical-align: middle;" alt="TTD Supervisor">
                        @else
                            <div style="height: 80px;"></div>
                        @endif
                    </div>
                    <div class="signature-name">( {{ $settings['head_of_it_name'] }} )</div>
                    <div class="signature-role">{{ $settings['head_of_it_title'] ?? 'IT Officer / Supervisor' }}</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
