<?php

namespace App\Http\Controllers;

use App\Models\ProjectRequest;
use App\Helpers\SettingsHelper;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class BeritaAcaraController extends Controller
{
    public function exportPdf(ProjectRequest $projectRequest)
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(300);

        try {
            $data = $this->prepareData($projectRequest);

            $pdf = Pdf::loadView('pdf.berita-acara', $data)
                ->setPaper('a4', 'portrait')
                ->setOption([
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'chroot' => [public_path(), storage_path()],
                ]);

            $filename = 'Berita_Acara_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $projectRequest->ticket_number ?? ('ID_' . $projectRequest->id)) . '.pdf';

            return $pdf->download($filename);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('PDF Export Error: ' . $e->getMessage(), [
                'project_request_id' => $projectRequest->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('berita-acara.print', $projectRequest)
                ->with('error', 'Gagal mengunduh file PDF secara langsung. Menampilkan tampilan cetak alternatif.');
        }
    }

    public function printView(ProjectRequest $projectRequest)
    {
        @ini_set('memory_limit', '256M');
        $data = $this->prepareData($projectRequest);

        return view('pdf.berita-acara-print', $data);
    }

    private function prepareData(ProjectRequest $projectRequest): array
    {
        $projectRequest->load(['client', 'queue.assignedTo', 'queue.progressLogs.projectStage', 'requirements']);

        // Settings from SuperAdmin configuration
        $settings = [
            'department_name' => SettingsHelper::get('company_department_name', 'DEPARTEMEN INFORMATION TECHNOLOGY'),
            'subtitle' => SettingsHelper::get('company_subtitle', 'Formulir Layanan Dukungan Teknis dan Infrastruktur'),
            'address' => SettingsHelper::get('company_address', 'Jl. Raya Perusahaan No. 123'),
            'phone' => SettingsHelper::get('company_phone', '(021) 1234567'),
            'city' => SettingsHelper::get('company_city', 'Purbalingga'),
            'head_of_it_name' => SettingsHelper::get('head_of_it_name', 'Head of IT / Manager'),
            'head_of_it_title' => SettingsHelper::get('head_of_it_title', 'IT Officer / Supervisor'),
            'logo_path' => SettingsHelper::get('app_logo', ''),
            'supervisor_signature' => SettingsHelper::get('it_supervisor_signature', ''),
        ];

        // Format Logo to Base64 for clean DomPDF rendering
        $logoBase64 = $this->imagePathToBase64($settings['logo_path']);

        // Digital Signatures
        $developerUser = $projectRequest->queue?->assignedTo;
        $clientUser = $projectRequest->client;

        $devSigBase64 = $this->imagePathToBase64($developerUser?->signature_image);
        $clientSigBase64 = $this->imagePathToBase64($clientUser?->signature_image);
        $supervisorSigBase64 = $this->imagePathToBase64($settings['supervisor_signature']);

        // Fallback for Supervisor signature from Admin user if empty in settings
        if (!$supervisorSigBase64) {
            $adminUser = \App\Models\User::whereIn('role', ['admin', 'super_admin'])->whereNotNull('signature_image')->first();
            if ($adminUser) {
                $supervisorSigBase64 = $this->imagePathToBase64($adminUser->signature_image);
            }
        }

        // Date calculations in Indonesian
        Carbon::setLocale('id');
        $completionDate = $projectRequest->resolved_at ?? $projectRequest->closed_at ?? now();
        
        $daysIndonesian = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $monthsIndonesian = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        $dayName = $daysIndonesian[$completionDate->format('l')] ?? $completionDate->format('l');
        $dayNum = $completionDate->format('d');
        $monthName = $monthsIndonesian[$completionDate->month] ?? $completionDate->format('F');
        $yearNum = $completionDate->format('Y');

        // Formatted BA Number
        $ticketIdPadded = str_pad((string) $projectRequest->id, 3, '0', STR_PAD_LEFT);
        $monthRoman = $romanMonths[$completionDate->month] ?? 'I';
        $baNumber = "{$ticketIdPadded} / BA-IT / {$monthRoman} / {$yearNum}";

        // IT Developer / Engineer Name
        $developerName = $projectRequest->queue?->assignedTo?->name ?? 'Tim Support IT';

        // Fetch End-to-End Progress Logs (Hilir: Chronological history from 0% to 100%)
        $progressLogs = collect();
        if ($projectRequest->queue) {
            $progressLogs = $projectRequest->queue->progressLogs()
                ->with(['projectStage', 'updatedBy'])
                ->orderBy('created_at', 'asc')
                ->get();
        }

        // Requirements (Hulu: Technical specifications & feature checklist)
        $requirements = $projectRequest->requirements;

        // Approval Record (Midstream: Persetujuan Atasan / Manager)
        $latestApproval = $projectRequest->approvals()->where('status', 'approved')->latest()->first();
        $approverName = $latestApproval?->approver?->name ?? $projectRequest->approvedBy?->name ?? 'Sistem / Manager IT';
        $approvedAtFormatted = $projectRequest->approved_at ? $projectRequest->approved_at->format('d M Y H:i') . ' WIB' : ($latestApproval ? $latestApproval->created_at->format('d M Y H:i') . ' WIB' : '-');

        // Action taken summary from progress logs
        $actionTakenList = [];
        foreach ($progressLogs as $log) {
            if (!empty($log->activity_description)) {
                $actionTakenList[] = "• [" . ($log->created_at ? $log->created_at->format('d/m/H:i') : '') . " - " . ($log->progress_percentage ?? 0) . "%] " . trim($log->activity_description);
            }
        }

        $actionTakenText = count($actionTakenList) > 0
            ? implode("\n", array_unique($actionTakenList))
            : ($projectRequest->description ?? 'Pengerjaan dan penanganan kendala teknis telah selesai dilakukan.');

        return [
            'projectRequest' => $projectRequest,
            'settings' => $settings,
            'logoBase64' => $logoBase64,
            'devSigBase64' => $devSigBase64,
            'clientSigBase64' => $clientSigBase64,
            'supervisorSigBase64' => $supervisorSigBase64,
            'developerUser' => $developerUser,
            'clientUser' => $clientUser,
            'baNumber' => $baNumber,
            'dayName' => $dayName,
            'dayNum' => $dayNum,
            'monthName' => $monthName,
            'yearNum' => $yearNum,
            'developerName' => $developerName,
            'progressLogs' => $progressLogs,
            'requirements' => $requirements,
            'approverName' => $approverName,
            'approvedAtFormatted' => $approvedAtFormatted,
            'actionTakenText' => $actionTakenText,
            'completionDate' => $completionDate,
            'formattedDateString' => "{$dayNum} {$monthName} {$yearNum}",
        ];
    }

    private function imagePathToBase64(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $parsedUrl = parse_url($path);
            $path = $parsedUrl['path'] ?? '';
        }

        $path = str_replace('\\', '/', $path);
        $cleanPath = ltrim($path, '/');

        if (str_starts_with($cleanPath, 'storage/')) {
            $cleanPath = substr($cleanPath, 8);
        }
        if (str_starts_with($cleanPath, 'public/')) {
            $cleanPath = substr($cleanPath, 7);
        }

        $candidates = array_unique(array_filter([
            storage_path('app/public/' . $cleanPath),
            public_path('storage/' . $cleanPath),
            public_path($cleanPath),
            base_path($cleanPath),
            $path,
        ]));

        foreach ($candidates as $fullPath) {
            if (!empty($fullPath) && @file_exists($fullPath) && !@is_dir($fullPath)) {
                $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
                $mime = match($ext) {
                    'png' => 'image/png',
                    'jpg', 'jpeg' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'svg' => 'image/svg+xml',
                    default => 'image/png',
                };
                $data = @file_get_contents($fullPath);
                if ($data !== false && strlen($data) > 0) {
                    return 'data:' . $mime . ';base64,' . base64_encode($data);
                }
            }
        }

        return null;
    }
}
