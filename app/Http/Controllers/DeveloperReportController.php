<?php

namespace App\Http\Controllers;

use App\Models\ProjectRequest;
use App\Models\Queue;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DeveloperReportController extends Controller
{
    /**
     * Display developer report dashboard with interactive charts and metrics.
     */
    public function index(Request $request)
    {
        $reportData = $this->buildDeveloperReportData($request);

        return view('reports.developer-reports', $reportData);
    }

    /**
     * Export customized developer report as PDF.
     */
    public function exportPdf(Request $request)
    {
        $reportData = $this->buildDeveloperReportData($request, false);
        $settings = $this->loadSystemSettings();

        $dompdfPublicPath = $this->resolveDompdfPublicPath();

        config([
            'dompdf.public_path' => $dompdfPublicPath,
            'dompdf.options.chroot' => realpath(dirname($dompdfPublicPath)) ?: base_path(),
        ]);

        $logoBase64 = $this->imagePathToBase64($settings['app_logo'] ?? '');
        $supervisorSigBase64 = $this->imagePathToBase64($settings['it_supervisor_signature'] ?? '');

        $devSigBase64 = null;
        if ($reportData['selectedDeveloper']) {
            $devSigBase64 = $this->imagePathToBase64($reportData['selectedDeveloper']->signature_image ?? '');
        }

        $pdf = Pdf::loadView('reports.developer-pdf', array_merge($reportData, [
            'settings' => $settings,
            'logoBase64' => $logoBase64,
            'devSigBase64' => $devSigBase64,
            'supervisorSigBase64' => $supervisorSigBase64,
            'generatedAt' => now()->translatedFormat('d F Y H:i'),
            'generator' => auth()->user()->name,
        ]));

        $pdf->setPaper('A4', 'landscape');

        $devSlug = $reportData['selectedDeveloper'] ? str_replace(' ', '_', $reportData['selectedDeveloper']->name) : 'Semua_Developer';
        $fileName = 'Laporan_Kinerja_Developer_' . $devSlug . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Export developer report data as CSV / Excel compatible file.
     */
    public function exportCsv(Request $request)
    {
        $reportData = $this->buildDeveloperReportData($request, false);

        $devSlug = $reportData['selectedDeveloper'] ? str_replace(' ', '_', $reportData['selectedDeveloper']->name) : 'Semua_Developer';
        $fileName = 'laporan-developer-' . $devSlug . '-' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ];

        return response()->streamDownload(function () use ($reportData) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['LAPORAN KINERJA DAN PENUGASAN DEVELOPER']);
            fputcsv($handle, ['Developer', $reportData['selectedDeveloper'] ? $reportData['selectedDeveloper']->name : 'Semua Developer']);
            fputcsv($handle, ['Periode', $reportData['periodLabel']]);
            fputcsv($handle, ['Rentang Tanggal', $reportData['startDate']->format('d/m/Y') . ' - ' . $reportData['endDate']->format('d/m/Y')]);
            fputcsv($handle, ['Dicetak Oleh', auth()->user()->name]);
            fputcsv($handle, ['Tanggal Ekspor', now()->translatedFormat('d F Y H:i')]);
            fputcsv($handle, []);

            // Summary KPI
            fputcsv($handle, ['RINGKASAN KINERJA (KPI)']);
            fputcsv($handle, ['Total Penugasan', $reportData['summary']['total_assigned']]);
            fputcsv($handle, ['Selesai (Resolved/Closed)', $reportData['summary']['resolved_count']]);
            fputcsv($handle, ['Sedang Dikerjakan (In Progress)', $reportData['summary']['in_progress_count']]);
            fputcsv($handle, ['Tingkat Penyelesaian (%)', $reportData['summary']['completion_rate'] . '%']);
            fputcsv($handle, ['Tiket Melewati SLA', $reportData['summary']['overdue_count']]);
            fputcsv($handle, ['Kepatuhan SLA (%)', $reportData['summary']['sla_compliance_rate'] . '%']);
            fputcsv($handle, ['Rata-rata Waktu Pengerjaan (MTTR)', $reportData['summary']['avg_resolution_hours'] . ' Jam']);
            fputcsv($handle, []);

            // Ticket Details
            fputcsv($handle, ['DAFTAR TIKET & PENUGASAN']);
            fputcsv($handle, [
                'No. Tiket',
                'Nama Proyek / Masalah',
                'Kategori',
                'Klien / Pemohon',
                'Developer Ditugaskan',
                'Prioritas',
                'Status Tiket',
                'Tanggal Masuk',
                'Tanggal Selesai',
                'Durasi (Jam)',
                'Status SLA'
            ]);

            foreach ($reportData['tickets'] as $ticket) {
                $durationHours = '-';
                if ($ticket->resolved_at && $ticket->created_at) {
                    $durationHours = number_format($ticket->created_at->diffInMinutes($ticket->resolved_at) / 60, 1);
                }

                $slaStatus = 'Normal';
                if ($ticket->sla_resolution_due_at) {
                    if (in_array($ticket->ticket_status, ['resolved', 'closed'])) {
                        $slaStatus = $ticket->resolved_at && $ticket->resolved_at <= $ticket->sla_resolution_due_at ? 'Tepat Waktu' : 'Terlambat';
                    } elseif (now() > $ticket->sla_resolution_due_at) {
                        $slaStatus = 'Overdue';
                    }
                }

                fputcsv($handle, [
                    $ticket->ticket_number,
                    $ticket->project_name,
                    $ticket->ticket_category === 'technical_support' ? 'Technical Support' : 'Project Request',
                    $ticket->client ? $ticket->client->name : '-',
                    $ticket->developer ? $ticket->developer->name : ($ticket->queue && $ticket->queue->assignedTo ? $ticket->queue->assignedTo->name : '-'),
                    ucfirst($ticket->impact ?? 'Normal'),
                    $ticket->ticket_status,
                    $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '-',
                    $ticket->resolved_at ? $ticket->resolved_at->format('d/m/Y H:i') : '-',
                    $durationHours,
                    $slaStatus
                ]);
            }

            fclose($handle);
        }, $fileName, $headers);
    }

    /**
     * Build report data, metrics, and chart series.
     */
    private function buildDeveloperReportData(Request $request, bool $paginate = true): array
    {
        $user = auth()->user();

        // 1. Developer Selection & Security Check
        $developerId = $request->input('developer_id', 'all');
        if ($user->isDeveloper()) {
            // Developers can only see their own performance report
            $developerId = (string) $user->id;
        }

        $selectedDeveloper = null;
        if ($developerId !== 'all' && is_numeric($developerId)) {
            $selectedDeveloper = User::find($developerId);
            if (!$selectedDeveloper) {
                $developerId = 'all';
            }
        }

        // 2. Available Developers list for dropdown
        $developers = User::whereIn('role', ['developer', 'admin', 'super_admin'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // 3. Date & Period Filter
        $period = $request->input('period', 'this_month'); // today, this_week, this_month, this_year, custom
        $dateFromInput = $request->input('date_from');
        $dateToInput = $request->input('date_to');

        $now = Carbon::now();
        $startDate = null;
        $endDate = null;
        $periodLabel = '';

        switch ($period) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $periodLabel = 'Hari Ini (' . $startDate->translatedFormat('d M Y') . ')';
                break;
            case 'this_week':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                $periodLabel = 'Minggu Ini (' . $startDate->translatedFormat('d M') . ' - ' . $endDate->translatedFormat('d M Y') . ')';
                break;
            case 'this_month':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                $periodLabel = 'Bulan Ini (' . $startDate->translatedFormat('F Y') . ')';
                break;
            case 'this_year':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                $periodLabel = 'Tahun Ini (' . $startDate->format('Y') . ')';
                break;
            case 'custom':
                if ($dateFromInput) {
                    try {
                        $startDate = Carbon::parse($dateFromInput)->startOfDay();
                    } catch (\Throwable $e) {
                        $startDate = $now->copy()->startOfMonth();
                    }
                } else {
                    $startDate = $now->copy()->startOfMonth();
                }

                if ($dateToInput) {
                    try {
                        $endDate = Carbon::parse($dateToInput)->endOfDay();
                    } catch (\Throwable $e) {
                        $endDate = $now->copy()->endOfDay();
                    }
                } else {
                    $endDate = $now->copy()->endOfDay();
                }

                $periodLabel = 'Periode ' . $startDate->translatedFormat('d M Y') . ' s/d ' . $endDate->translatedFormat('d M Y');
                break;
            default:
                $period = 'this_month';
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                $periodLabel = 'Bulan Ini (' . $startDate->translatedFormat('F Y') . ')';
                break;
        }

        // Category filter
        $category = $request->input('category', 'all'); // all, project, technical_support

        // 4. Base Query Builder
        $query = ProjectRequest::with(['client', 'developer', 'queue.assignedTo'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($developerId !== 'all') {
            $query->where(function ($q) use ($developerId) {
                $q->where('developer_id', $developerId)
                  ->orWhereHas('queue', function ($subQ) use ($developerId) {
                      $subQ->where('assigned_to', $developerId);
                  });
            });
        }

        if ($category === 'technical_support') {
            $query->where('ticket_category', 'technical_support');
        } elseif ($category === 'project') {
            $query->where('ticket_category', '!=', 'technical_support');
        }

        // Clone base query for KPI calculations
        $allMatchingTickets = (clone $query)->get();

        $totalAssigned = $allMatchingTickets->count();
        $resolvedCount = $allMatchingTickets->whereIn('ticket_status', ['resolved', 'closed'])->count();
        $inProgressCount = $allMatchingTickets->whereIn('ticket_status', ['in_progress', 'pending_user', 'paused'])->count();
        $openCount = $allMatchingTickets->where('ticket_status', 'open')->count();

        // SLA Overdue calculation
        $overdueCount = $allMatchingTickets->filter(function ($t) {
            if (!$t->sla_resolution_due_at) {
                return false;
            }
            if (in_array($t->ticket_status, ['resolved', 'closed'])) {
                return $t->resolved_at && $t->resolved_at > $t->sla_resolution_due_at;
            }
            return now() > $t->sla_resolution_due_at;
        })->count();

        // MTTR (Mean Time to Resolution in hours)
        $resolvedWithTimes = $allMatchingTickets->filter(function ($t) {
            return in_array($t->ticket_status, ['resolved', 'closed']) && $t->resolved_at && $t->created_at;
        });

        $avgResolutionHours = 0;
        if ($resolvedWithTimes->count() > 0) {
            $totalMinutes = $resolvedWithTimes->sum(function ($t) {
                return $t->created_at->diffInMinutes($t->resolved_at);
            });
            $avgResolutionHours = round($totalMinutes / ($resolvedWithTimes->count() * 60), 1);
        }

        // Completion Rate & SLA Compliance
        $completionRate = $totalAssigned > 0 ? round(($resolvedCount / $totalAssigned) * 100, 1) : 0;
        $slaComplianceRate = $totalAssigned > 0 ? round((($totalAssigned - $overdueCount) / $totalAssigned) * 100, 1) : 100;

        $summary = [
            'total_assigned' => $totalAssigned,
            'resolved_count' => $resolvedCount,
            'in_progress_count' => $inProgressCount,
            'open_count' => $openCount,
            'overdue_count' => $overdueCount,
            'completion_rate' => $completionRate,
            'avg_resolution_hours' => $avgResolutionHours,
            'sla_compliance_rate' => max(0, $slaComplianceRate),
        ];

        // 5. Chart Data Generation
        // Chart A: Developer Comparison Breakdown
        $devPerformanceQuery = User::whereIn('role', ['developer', 'admin', 'super_admin'])
            ->where('status', 'active');

        if ($developerId !== 'all') {
            $devPerformanceQuery->where('id', $developerId);
        }

        $devStats = $devPerformanceQuery->get()->map(function ($dev) use ($startDate, $endDate, $category) {
            $devQuery = ProjectRequest::whereBetween('created_at', [$startDate, $endDate])
                ->where(function ($q) use ($dev) {
                    $q->where('developer_id', $dev->id)
                      ->orWhereHas('queue', function ($subQ) use ($dev) {
                          $subQ->where('assigned_to', $dev->id);
                      });
                });

            if ($category === 'technical_support') {
                $devQuery->where('ticket_category', 'technical_support');
            } elseif ($category === 'project') {
                $devQuery->where('ticket_category', '!=', 'technical_support');
            }

            $devTickets = $devQuery->get();
            $total = $devTickets->count();
            $resolved = $devTickets->whereIn('ticket_status', ['resolved', 'closed'])->count();
            $inProgress = $devTickets->whereIn('ticket_status', ['in_progress', 'pending_user', 'paused'])->count();

            return [
                'id' => $dev->id,
                'name' => $dev->name,
                'total' => $total,
                'resolved' => $resolved,
                'in_progress' => $inProgress,
                'rate' => $total > 0 ? round(($resolved / $total) * 100, 1) : 0,
            ];
        })->sortByDesc('total')->values();

        // Chart B: Timeline / Trend Series
        $timelineLabels = [];
        $timelineTotalSeries = [];
        $timelineResolvedSeries = [];

        if ($period === 'today') {
            // Group by 4-hour intervals
            for ($h = 0; $h < 24; $h += 4) {
                $slotStart = $startDate->copy()->addHours($h);
                $slotEnd = $slotStart->copy()->addHours(4)->subSecond();
                $timelineLabels[] = $slotStart->format('H:i') . ' - ' . $slotEnd->format('H:i');

                $timelineTotalSeries[] = $allMatchingTickets->filter(function ($t) use ($slotStart, $slotEnd) {
                    return $t->created_at >= $slotStart && $t->created_at <= $slotEnd;
                })->count();

                $timelineResolvedSeries[] = $allMatchingTickets->filter(function ($t) use ($slotStart, $slotEnd) {
                    return $t->resolved_at && $t->resolved_at >= $slotStart && $t->resolved_at <= $slotEnd;
                })->count();
            }
        } elseif ($period === 'this_week') {
            // Group by Day of Week
            for ($d = 0; $d < 7; $d++) {
                $day = $startDate->copy()->addDays($d);
                $timelineLabels[] = $day->translatedFormat('l (d/m)');

                $timelineTotalSeries[] = $allMatchingTickets->filter(function ($t) use ($day) {
                    return $t->created_at && $t->created_at->isSameDay($day);
                })->count();

                $timelineResolvedSeries[] = $allMatchingTickets->filter(function ($t) use ($day) {
                    return $t->resolved_at && $t->resolved_at->isSameDay($day);
                })->count();
            }
        } elseif ($period === 'this_year') {
            // Group by Month
            for ($m = 1; $m <= 12; $m++) {
                $monthDate = Carbon::createFromDate($startDate->year, $m, 1);
                $timelineLabels[] = $monthDate->translatedFormat('M Y');

                $timelineTotalSeries[] = $allMatchingTickets->filter(function ($t) use ($m, $startDate) {
                    return $t->created_at && $t->created_at->year == $startDate->year && $t->created_at->month == $m;
                })->count();

                $timelineResolvedSeries[] = $allMatchingTickets->filter(function ($t) use ($m, $startDate) {
                    return $t->resolved_at && $t->resolved_at->year == $startDate->year && $t->resolved_at->month == $m;
                })->count();
            }
        } else {
            // Default (this_month or custom range): Group by intervals or days
            $daysDiff = max(1, $startDate->diffInDays($endDate));
            $step = $daysDiff > 31 ? 7 : ($daysDiff > 14 ? 3 : 1);

            $currentPointer = $startDate->copy();
            while ($currentPointer <= $endDate) {
                $intervalEnd = $currentPointer->copy()->addDays($step - 1)->endOfDay();
                if ($intervalEnd > $endDate) {
                    $intervalEnd = $endDate->copy();
                }

                $label = $step === 1 ? $currentPointer->translatedFormat('d M') : $currentPointer->format('d/m') . '-' . $intervalEnd->format('d/m');
                $timelineLabels[] = $label;

                $timelineTotalSeries[] = $allMatchingTickets->filter(function ($t) use ($currentPointer, $intervalEnd) {
                    return $t->created_at >= $currentPointer->copy()->startOfDay() && $t->created_at <= $intervalEnd;
                })->count();

                $timelineResolvedSeries[] = $allMatchingTickets->filter(function ($t) use ($currentPointer, $intervalEnd) {
                    return $t->resolved_at && $t->resolved_at >= $currentPointer->copy()->startOfDay() && $t->resolved_at <= $intervalEnd;
                })->count();

                $currentPointer->addDays($step);
            }
        }

        // Chart C: Status Distribution Data
        $statusCounts = [
            'Selesai / Ditutup' => $resolvedCount,
            'Sedang Dikerjakan' => $allMatchingTickets->where('ticket_status', 'in_progress')->count(),
            'Menunggu Pengguna' => $allMatchingTickets->where('ticket_status', 'pending_user')->count(),
            'Dijeda' => $allMatchingTickets->where('ticket_status', 'paused')->count(),
            'Terbuka / Baru' => $openCount,
        ];

        // 6. Tickets List
        $ticketsQuery = (clone $query)->latest();
        $tickets = $paginate ? $ticketsQuery->paginate(15)->appends($request->query()) : $ticketsQuery->get();

        return [
            'developers' => $developers,
            'selectedDeveloper' => $selectedDeveloper,
            'developerId' => $developerId,
            'period' => $period,
            'periodLabel' => $periodLabel,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dateFromInput' => $startDate->toDateString(),
            'dateToInput' => $endDate->toDateString(),
            'category' => $category,
            'summary' => $summary,
            'devStats' => $devStats,
            'statusCounts' => $statusCounts,
            'timelineLabels' => $timelineLabels,
            'timelineTotalSeries' => $timelineTotalSeries,
            'timelineResolvedSeries' => $timelineResolvedSeries,
            'tickets' => $tickets,
        ];
    }

    /**
     * Load application settings for report header and signatures.
     */
    private function loadSystemSettings(): array
    {
        $defaults = [
            'app_name' => config('app.name', 'Antrian Project'),
            'app_logo' => '',
            'app_favicon' => '',
            'company_department_name' => 'DEPARTEMEN INFORMATION TECHNOLOGY',
            'company_subtitle' => 'Laporan Produktivitas dan Kinerja Developer',
            'company_address' => 'Jl. Raya Perusahaan No. 123',
            'company_phone' => '(021) 1234567',
            'company_city' => 'Purbalingga',
            'head_of_it_name' => 'Head of IT / Manager',
            'head_of_it_title' => 'IT Officer / Supervisor',
            'it_supervisor_signature' => '',
        ];

        $path = storage_path('app/system-settings.json');

        if (!File::exists($path)) {
            return $defaults;
        }

        $decoded = json_decode(File::get($path), true);

        if (!is_array($decoded)) {
            return $defaults;
        }

        return array_merge($defaults, $decoded);
    }

    /**
     * Resolve public path for DomPDF asset handling.
     */
    private function resolveDompdfPublicPath(): string
    {
        $candidates = [];

        if (app()->bound('path.public')) {
            $candidates[] = (string) app('path.public');
        }

        $candidates[] = base_path('public');
        $candidates[] = base_path('public_html');

        if (!empty($_SERVER['DOCUMENT_ROOT'])) {
            $candidates[] = (string) $_SERVER['DOCUMENT_ROOT'];
        }

        if (!empty($_SERVER['SCRIPT_FILENAME'])) {
            $candidates[] = dirname((string) $_SERVER['SCRIPT_FILENAME']);
        }

        foreach ($candidates as $candidate) {
            if (!$candidate) {
                continue;
            }

            $resolved = realpath($candidate) ?: $candidate;

            if (is_dir($resolved)) {
                return $resolved;
            }
        }

        return base_path();
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
