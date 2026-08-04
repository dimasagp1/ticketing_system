<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Berita Acara - {{ $projectRequest->ticket_number ?? ('ID-' . $projectRequest->id) }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #111;
        }

        .no-print-bar {
            background-color: #111827;
            color: #fff;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .no-print-bar .title {
            font-weight: bold;
            font-size: 1.1rem;
        }

        .no-print-bar .actions {
            display: flex;
            gap: 10px;
        }

        .btn-print-action {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 0.9rem;
            cursor: pointer;
            border: 2px solid #000;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .btn-print-action.primary {
            background-color: #FFE600;
            color: #000;
        }

        .btn-print-action.primary:hover {
            background-color: #FF007A;
            color: #fff;
        }

        .btn-print-action.secondary {
            background-color: #0055FF;
            color: #fff;
        }

        .btn-print-action.secondary:hover {
            background-color: #00E676;
            color: #000;
        }

        .paper-container {
            max-width: 210mm;
            margin: 30px auto;
            background: #fff;
            padding: 20mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-radius: 4px;
            box-sizing: border-box;
        }

        /* Printable Styles matching PDF */
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

        .section-header {
            background-color: #f3f4f6;
            border-left: 4px solid #0055FF;
            font-weight: bold;
            font-size: 11pt;
            padding: 5px 10px;
            margin-top: 14px;
            margin-bottom: 10px;
        }

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

        .checkbox-group {
            display: inline-block;
            margin-right: 15px;
        }

        .checkbox-box {
            display: inline-block;
            width: 13px;
            height: 13px;
            border: 1px solid #000;
            text-align: center;
            line-height: 12px;
            font-size: 9pt;
            font-weight: bold;
            margin-right: 4px;
            vertical-align: middle;
        }

        .text-box {
            border: 1px solid #777;
            padding: 8px 12px;
            min-height: 55px;
            font-size: 10pt;
            white-space: pre-wrap;
            word-wrap: break-word;
            margin-bottom: 10px;
            background-color: #fafafa;
            text-align: justify;
            text-justify: inter-word;
        }

        .status-row {
            margin-top: 8px;
            margin-bottom: 12px;
            font-size: 10.5pt;
        }

        .status-label {
            font-weight: bold;
            margin-right: 10px;
        }

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

        @media print {
            .no-print-bar {
                display: none !important;
            }
            body {
                background: #fff;
            }
            .paper-container {
                margin: 0;
                padding: 0;
                box-shadow: none;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div class="title">
            <i class="fas fa-file-contract mr-2"></i> Pratinjau Berita Acara Pengerjaan Supporting IT
        </div>
        <div class="actions">
            <button onclick="window.print()" class="btn-print-action primary">
                <i class="fas fa-print"></i> Cetak Dokumen
            </button>
            <a href="{{ route('berita-acara.pdf', $projectRequest) }}" class="btn-print-action secondary">
                <i class="fas fa-file-pdf"></i> Unduh PDF
            </a>
        </div>
    </div>

    <div class="paper-container">
        <!-- Kop Surat Header -->
        <table class="kop-table">
            <tr>
                <td class="kop-logo-td">
                    @if(!empty($settings['logo_path']))
                        <img src="{{ asset('storage/' . $settings['logo_path']) }}" class="kop-logo-img" alt="Logo">
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
                            @if(!empty($developerUser?->signature_image))
                                <img src="{{ asset('storage/' . $developerUser->signature_image) }}" style="height: 95px; width: 220px; max-width: 100%; vertical-align: middle; object-fit: contain; transform: scale(1.4); display: inline-block;" alt="TTD Developer">
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
                            @if(!empty($clientUser?->signature_image))
                                <img src="{{ asset('storage/' . $clientUser->signature_image) }}" style="height: 95px; width: 220px; max-width: 100%; vertical-align: middle; object-fit: contain; transform: scale(1.4); display: inline-block;" alt="TTD User">
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
                            @if(!empty($settings['supervisor_signature']))
                                <img src="{{ asset('storage/' . $settings['supervisor_signature']) }}" style="height: 95px; width: 220px; max-width: 100%; vertical-align: middle; object-fit: contain; transform: scale(1.4); display: inline-block;" alt="TTD Supervisor">
                            @elseif(!empty($supervisorSigBase64))
                                <img src="{{ $supervisorSigBase64 }}" style="height: 95px; width: 220px; max-width: 100%; vertical-align: middle; object-fit: contain; transform: scale(1.4); display: inline-block;" alt="TTD Supervisor">
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
    </div>

</body>
</html>
