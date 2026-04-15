<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProjectRequest;
use App\Models\Queue;
use App\Models\ChatConversation;
use App\Models\ActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $flowRange = request()->query('flow_range', '30d');
        $allowedFlowRanges = ['7d', '30d', '90d', 'all'];
        if (!in_array($flowRange, $allowedFlowRanges, true)) {
            $flowRange = '30d';
        }

        $flowRangeStart = match ($flowRange) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            default => null,
        };

        $flowRangeLabel = match ($flowRange) {
            '7d' => '7 Hari Terakhir',
            '30d' => '30 Hari Terakhir',
            '90d' => '90 Hari Terakhir',
            default => 'Semua Waktu',
        };

        $flowBaseQuery = ProjectRequest::query();
        if ($flowRangeStart) {
            $flowBaseQuery->where('created_at', '>=', $flowRangeStart);
        }

        $flowCounts = [
            'submitted' => (clone $flowBaseQuery)->where('status', 'submitted')->count(),
            'under_review' => (clone $flowBaseQuery)->where('status', 'under_review')->count(),
            'revision_requested' => (clone $flowBaseQuery)->where('status', 'revision_requested')->count(),
            'rejected' => (clone $flowBaseQuery)->where('status', 'rejected')->count(),
            'approved' => (clone $flowBaseQuery)->where('status', 'approved')->count(),
            'open' => (clone $flowBaseQuery)->where('ticket_status', 'open')->count(),
            'in_progress' => (clone $flowBaseQuery)->where('ticket_status', 'in_progress')->count(),
            'pending_user' => (clone $flowBaseQuery)->where('ticket_status', 'pending_user')->count(),
            'paused' => (clone $flowBaseQuery)->where('ticket_status', 'paused')->count(),
            'resolved' => (clone $flowBaseQuery)->where('ticket_status', 'resolved')->count(),
            'closed' => (clone $flowBaseQuery)->where('ticket_status', 'closed')->count(),
            'cancelled' => (clone $flowBaseQuery)->where('ticket_status', 'cancelled')->count(),
        ];

        $flowTotalTickets = max((clone $flowBaseQuery)->count(), 1);

        // Statistics
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'total_clients' => User::where('role', 'client')->count(),
            'total_developers' => User::where('role', 'developer')->count(),
            'total_admins' => User::whereIn('role', ['admin', 'super_admin'])->count(),
            
            'total_requests' => ProjectRequest::count(),
            'pending_requests' => ProjectRequest::where('status', 'submitted')->count(),
            'approved_requests' => ProjectRequest::where('status', 'approved')->count(),
            
            'total_queues' => Queue::count(),
            'active_queues' => Queue::where('status', 'In Progress')->count(),
            'completed_queues' => Queue::where('status', 'Completed')->count(),
            
            'total_conversations' => ChatConversation::count(),
            'active_conversations' => ChatConversation::where('status', 'active')->count(),

            'open_tickets' => ProjectRequest::where('ticket_status', 'open')->count(),
            'in_progress_tickets' => ProjectRequest::where('ticket_status', 'in_progress')->count(),
            'pending_user_tickets' => ProjectRequest::where('ticket_status', 'pending_user')->count(),
            'paused_tickets' => ProjectRequest::where('ticket_status', 'paused')->count(),
            'resolved_tickets' => ProjectRequest::where('ticket_status', 'resolved')->count(),
            'overdue_tickets' => ProjectRequest::whereIn('ticket_status', ProjectRequest::slaTrackedTicketStatuses())
                ->whereNotNull('sla_resolution_due_at')
                ->where('sla_resolution_due_at', '<', now())
                ->count(),
            'due_today_tickets' => ProjectRequest::whereIn('ticket_status', ProjectRequest::slaTrackedTicketStatuses())
                ->whereDate('sla_resolution_due_at', now()->toDateString())
                ->count(),
        ];

        $technicalTodayStart = now()->startOfDay();
        $technicalTodayEnd = now()->endOfDay();

        $technicalTodayBase = ProjectRequest::query()
            ->where('ticket_category', 'technical_support')
            ->whereBetween('created_at', [$technicalTodayStart, $technicalTodayEnd]);

        $technicalResolvedScope = (clone $technicalTodayBase)
            ->whereNotNull('resolved_at')
            ->whereNotNull('sla_resolution_due_at');

        $technicalResolvedCount = (clone $technicalResolvedScope)->count();
        $technicalSlaCompliant = (clone $technicalResolvedScope)
            ->whereColumn('resolved_at', '<=', 'sla_resolution_due_at')
            ->count();

        $technicalSummary = [
            'date_label' => $technicalTodayStart->translatedFormat('d M Y'),
            'total' => (clone $technicalTodayBase)->count(),
            'resolved' => (clone $technicalTodayBase)->whereIn('ticket_status', ['resolved', 'closed'])->count(),
            'backlog' => (clone $technicalTodayBase)->whereIn('ticket_status', ProjectRequest::activeTicketStatuses())->count(),
            'overdue' => (clone $technicalTodayBase)
                ->whereIn('ticket_status', ProjectRequest::slaTrackedTicketStatuses())
                ->whereNotNull('sla_resolution_due_at')
                ->where('sla_resolution_due_at', '<', now())
                ->count(),
            'frt_minutes' => round((float) ((clone $technicalTodayBase)
                ->whereNotNull('first_responded_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_responded_at)) as avg_minutes')
                ->value('avg_minutes') ?? 0), 2),
            'mttr_hours' => round((float) ((clone $technicalTodayBase)
                ->whereNotNull('resolved_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, resolved_at)) / 60 as avg_hours')
                ->value('avg_hours') ?? 0), 2),
            'sla_compliance_rate' => $technicalResolvedCount > 0
                ? round(($technicalSlaCompliant / $technicalResolvedCount) * 100, 2)
                : 0,
        ];

        $technicalSubcategoryBreakdown = (clone $technicalTodayBase)
            ->selectRaw("COALESCE(technical_subcategory, 'unclassified') as label, COUNT(*) as total")
            ->groupBy('label')
            ->orderByDesc('total')
            ->get();

        // Recent activities
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(20)
            ->get();

        // Chart data - Projects by status
        $projectsByStatus = ProjectRequest::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Chart data - Queues by status
        $queuesByStatus = Queue::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Recent users
        $recentUsers = User::latest()->take(5)->get();

        $slaWatchlist = ProjectRequest::with('client')
            ->whereIn('ticket_status', ProjectRequest::slaTrackedTicketStatuses())
            ->whereNotNull('sla_resolution_due_at')
            ->orderBy('sla_resolution_due_at')
            ->take(10)
            ->get();

        $supportAgents = User::whereIn('role', ['admin', 'super_admin'])
            ->where('status', 'active')
            ->withCount([
                'assignedQueues as active_queues' => function ($query) {
                    $query->whereIn('status', ['Pending', 'In Progress', 'On Hold']);
                },
                'assignedQueues as completed_queues' => function ($query) {
                    $query->where('status', 'Completed');
                },
            ])
            ->orderByDesc('active_queues')
            ->get();

        return view('super-admin.dashboard', compact(
            'stats',
            'recentActivities',
            'projectsByStatus',
            'queuesByStatus',
            'recentUsers',
            'slaWatchlist',
            'supportAgents',
            'technicalSummary',
            'technicalSubcategoryBreakdown',
            'flowCounts',
            'flowTotalTickets',
            'flowRange',
            'flowRangeLabel'
        ));
    }

    public function activityLogs(Request $request)
    {
        $query = ActivityLog::with('user');

        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->paginate(50);
        $users = User::orderBy('name')->get();
        $actions = ActivityLog::distinct('action')->pluck('action');

        return view('super-admin.activity-logs', compact('logs', 'users', 'actions'));
    }

    public function settings()
    {
        $settings = $this->loadSystemSettings();

        return view('super-admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:100',
            'admin_email' => 'required|email|max:255',
            'per_page' => 'required|integer|min:5|max:100',
            'email_notifications' => 'nullable|boolean',
            'notification_window_days' => 'required|integer|min:1|max:30',
            'maintenance_mode' => 'nullable|boolean',
            'app_logo' => 'nullable|image|max:2048', // max 2MB
            'app_favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:1024', // max 1MB
        ]);

        $settings = $this->loadSystemSettings();

        if ($request->hasFile('app_logo')) {
            if (!$request->file('app_logo')->isValid()) {
                return back()->withErrors(['app_logo' => 'File logo gagal diunggah. Pastikan ukuran file sesuai batas.'])->withInput();
            }
            // Hapus logo lama jika ada
            if (!empty($settings['app_logo'])) {
                $oldLogoPath = storage_path('app/public/' . ltrim($settings['app_logo'], '/'));
                if (File::exists($oldLogoPath)) {
                    File::delete($oldLogoPath);
                }
            }
            $logoFile = $request->file('app_logo');
            $logoName = \Illuminate\Support\Str::random(40) . '.' . $logoFile->getClientOriginalExtension();
            $logoPath = 'settings/' . $logoName;
            \Illuminate\Support\Facades\Storage::disk('public')->put($logoPath, file_get_contents($logoFile->getPathname()));
            $settings['app_logo'] = $logoPath;
        }

        if ($request->hasFile('app_favicon')) {
            if (!$request->file('app_favicon')->isValid()) {
                return back()->withErrors(['app_favicon' => 'File favicon gagal diunggah. Pastikan ukuran file sesuai batas.'])->withInput();
            }
            // Hapus favicon lama jika ada
            if (!empty($settings['app_favicon'])) {
                $oldFaviconPath = storage_path('app/public/' . ltrim($settings['app_favicon'], '/'));
                if (File::exists($oldFaviconPath)) {
                    File::delete($oldFaviconPath);
                }
            }
            $faviconFile = $request->file('app_favicon');
            $faviconName = \Illuminate\Support\Str::random(40) . '.' . $faviconFile->getClientOriginalExtension();
            $faviconPath = 'settings/' . $faviconName;
            \Illuminate\Support\Facades\Storage::disk('public')->put($faviconPath, file_get_contents($faviconFile->getPathname()));
            $settings['app_favicon'] = $faviconPath;
        }

        $settings['app_name'] = $validated['app_name'];
        $settings['admin_email'] = $validated['admin_email'];
        $settings['per_page'] = (int) $validated['per_page'];
        $settings['email_notifications'] = (bool) ($request->boolean('email_notifications'));
        $settings['notification_window_days'] = (int) $validated['notification_window_days'];
        $settings['maintenance_mode'] = (bool) ($request->boolean('maintenance_mode'));
        $settings['updated_at'] = now()->toDateTimeString();
        $settings['updated_by'] = auth()->id();

        File::put($this->settingsFilePath(), json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        ActivityLog::log('update_settings', 'Updated system settings', null, $settings);

        return back()->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function reports(Request $request)
    {
        $selectedYear = (int) $request->input('year', now()->year);
        $availableYears = ProjectRequest::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->values();

        if ($availableYears->isEmpty()) {
            $availableYears = collect([now()->year]);
        }

        if (!$availableYears->contains($selectedYear)) {
            $selectedYear = (int) $availableYears->first();
        }

        $monthlyProjects = ProjectRequest::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('count(*) as count')
        )
        ->whereYear('created_at', $selectedYear)
        ->groupBy('year', 'month')
        ->orderBy('month')
        ->get();

        $monthlyResolved = ProjectRequest::select(
            DB::raw('MONTH(resolved_at) as month'),
            DB::raw('count(*) as count')
        )
        ->whereNotNull('resolved_at')
        ->whereYear('resolved_at', $selectedYear)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $monthlyClosed = ProjectRequest::select(
            DB::raw('MONTH(closed_at) as month'),
            DB::raw('count(*) as count')
        )
        ->whereNotNull('closed_at')
        ->whereYear('closed_at', $selectedYear)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $createdMap = $monthlyProjects->pluck('count', 'month');
        $resolvedMap = $monthlyResolved->pluck('count', 'month');
        $closedMap = $monthlyClosed->pluck('count', 'month');

        $months = collect(range(1, 12));
        $monthLabels = $months->map(fn ($month) => Carbon::createFromDate($selectedYear, $month, 1)->translatedFormat('M Y'));
        $monthlyIncomingSeries = $months->map(fn ($month) => (int) ($createdMap[$month] ?? 0));
        $monthlyResolvedSeries = $months->map(fn ($month) => (int) ($resolvedMap[$month] ?? 0));
        $monthlyClosedSeries = $months->map(fn ($month) => (int) ($closedMap[$month] ?? 0));

        $runningTotal = 0;
        $monthlyCumulativeSeries = $monthlyIncomingSeries->map(function ($count) use (&$runningTotal) {
            $runningTotal += $count;
            return $runningTotal;
        });

        $reportSummary = [
            'total_tiket_tahun' => $monthlyIncomingSeries->sum(),
            'total_selesai_tahun' => $monthlyResolvedSeries->sum(),
            'total_ditutup_tahun' => $monthlyClosedSeries->sum(),
            'kumulatif_akhir_tahun' => $monthlyCumulativeSeries->last() ?? 0,
        ];

        $developerPerformance = User::where('role', 'developer')
            ->withCount(['assignedQueues as completed_projects' => function($query) {
                $query->where('status', 'Completed');
            }])
            ->withCount('assignedQueues as total_projects')
            ->get();

        $clientActivity = User::where('role', 'client')
            ->withCount('projectRequests')
            ->orderBy('project_requests_count', 'desc')
            ->take(10)
            ->get();

        return view('super-admin.reports', compact(
            'monthlyProjects',
            'developerPerformance',
            'clientActivity',
            'availableYears',
            'selectedYear',
            'monthLabels',
            'monthlyIncomingSeries',
            'monthlyResolvedSeries',
            'monthlyClosedSeries',
            'monthlyCumulativeSeries',
            'reportSummary'
        ));
    }

    public function technicalReports(Request $request)
    {
        $reportData = $this->buildTechnicalReportData($request);

        return view('super-admin.reports-technical', compact(
            'reportData'
        ));
    }

    public function exportTechnicalReportCsv(Request $request)
    {
        $reportData = $this->buildTechnicalReportData($request);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ];

        $fileName = 'laporan-teknis-harian-' . $reportData['selectedDateInput'] . '.csv';

        return response()->streamDownload(function () use ($reportData) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Laporan Operasional Ticket Teknis']);
            fputcsv($handle, ['Tanggal', $reportData['selectedDateInput']]);
            fputcsv($handle, ['Subkategori Filter', $reportData['selectedSubcategory']]);
            fputcsv($handle, []);

            fputcsv($handle, ['RINGKASAN KPI']);
            fputcsv($handle, ['Total Ticket', $reportData['summary']['total_tickets']]);
            fputcsv($handle, ['Resolved/Closed', $reportData['summary']['resolved_tickets']]);
            fputcsv($handle, ['Backlog Aktif', $reportData['summary']['backlog_tickets']]);
            fputcsv($handle, ['Overdue', $reportData['summary']['overdue_tickets']]);
            fputcsv($handle, ['Average FRT (menit)', $reportData['summary']['frt_minutes']]);
            fputcsv($handle, ['Average MTTR (jam)', $reportData['summary']['mttr_hours']]);
            fputcsv($handle, ['SLA Compliance (%)', $reportData['summary']['sla_compliance_rate']]);
            fputcsv($handle, []);

            fputcsv($handle, ['BREAKDOWN SUBKATEGORI']);
            fputcsv($handle, ['Subkategori', 'Total']);
            foreach ($reportData['subcategoryBreakdown'] as $row) {
                fputcsv($handle, [$row->label, $row->total]);
            }
            fputcsv($handle, []);

            fputcsv($handle, ['PERFORMA TEKNISI']);
            fputcsv($handle, ['Teknisi', 'Total Ticket', 'Resolved']);
            foreach ($reportData['technicianPerformance'] as $row) {
                fputcsv($handle, [$row->technician_name, $row->total_tickets, $row->resolved_tickets]);
            }
            fputcsv($handle, []);

            fputcsv($handle, ['OVERDUE LIST']);
            fputcsv($handle, ['Ticket Number', 'Judul', 'Subkategori', 'Status', 'SLA Due']);
            foreach ($reportData['overdueList'] as $item) {
                fputcsv($handle, [
                    $item->ticket_number,
                    $item->project_name,
                    $item->technical_subcategory,
                    $item->ticket_status,
                    optional($item->sla_resolution_due_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $fileName, $headers);
    }

    public function exportTechnicalReportPdf(Request $request)
    {
        $reportData = $this->buildTechnicalReportData($request);

        config([
            'dompdf.public_path' => public_path(),
            'dompdf.options.chroot' => base_path(),
        ]);

        $pdf = Pdf::loadView('reports.technical-pdf', [
            'reportData' => $reportData,
            'generatedAt' => now()->format('d M Y H:i:s'),
            'generator' => auth()->user()->name,
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('Laporan_Teknis_Harian_' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportMonthlyCumulativeReport(Request $request)
    {
        $selectedYear = (int) $request->input('year', now()->year);

        $monthlyCreated = ProjectRequest::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('count(*) as count')
        )
            ->whereYear('created_at', $selectedYear)
            ->groupBy('month')
            ->pluck('count', 'month');

        $monthlyResolved = ProjectRequest::select(
            DB::raw('MONTH(resolved_at) as month'),
            DB::raw('count(*) as count')
        )
            ->whereNotNull('resolved_at')
            ->whereYear('resolved_at', $selectedYear)
            ->groupBy('month')
            ->pluck('count', 'month');

        $monthlyClosed = ProjectRequest::select(
            DB::raw('MONTH(closed_at) as month'),
            DB::raw('count(*) as count')
        )
            ->whereNotNull('closed_at')
            ->whereYear('closed_at', $selectedYear)
            ->groupBy('month')
            ->pluck('count', 'month');

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ];

        $fileName = 'laporan-bulanan-kumulatif-' . $selectedYear . '.csv';

        return response()->streamDownload(function () use ($selectedYear, $monthlyCreated, $monthlyResolved, $monthlyClosed) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Periode', 'Tiket Masuk', 'Tiket Selesai', 'Tiket Ditutup', 'Kumulatif Tiket Masuk']);

            $runningTotal = 0;

            foreach (range(1, 12) as $month) {
                $incoming = (int) ($monthlyCreated[$month] ?? 0);
                $resolved = (int) ($monthlyResolved[$month] ?? 0);
                $closed = (int) ($monthlyClosed[$month] ?? 0);
                $runningTotal += $incoming;

                fputcsv($handle, [
                    Carbon::createFromDate($selectedYear, $month, 1)->translatedFormat('F Y'),
                    $incoming,
                    $resolved,
                    $closed,
                    $runningTotal,
                ]);
            }

            fclose($handle);
        }, $fileName, $headers);
    }

    private function settingsFilePath(): string
    {
        return storage_path('app/system-settings.json');
    }

    private function buildTechnicalReportData(Request $request): array
    {
        $selectedDateInput = (string) $request->input('date', now()->toDateString());
        $allowedSubcategories = ['wifi', 'printer', 'komputer', 'software_install', 'supporting'];
        $selectedSubcategory = (string) $request->input('subcategory', 'all');

        if (!in_array($selectedSubcategory, array_merge(['all'], $allowedSubcategories), true)) {
            $selectedSubcategory = 'all';
        }

        try {
            $selectedDate = Carbon::parse($selectedDateInput)->startOfDay();
        } catch (\Throwable $e) {
            $selectedDate = now()->startOfDay();
            $selectedDateInput = $selectedDate->toDateString();
        }

        $start = $selectedDate->copy()->startOfDay();
        $end = $selectedDate->copy()->endOfDay();

        $baseQuery = ProjectRequest::query()
            ->where('ticket_category', 'technical_support')
            ->whereBetween('created_at', [$start, $end]);

        if ($selectedSubcategory !== 'all') {
            $baseQuery->where('technical_subcategory', $selectedSubcategory);
        }

        $totalTickets = (clone $baseQuery)->count();
        $resolvedTickets = (clone $baseQuery)->whereIn('ticket_status', ['resolved', 'closed'])->count();
        $backlogTickets = (clone $baseQuery)->whereIn('ticket_status', ProjectRequest::activeTicketStatuses())->count();

        $overdueTickets = (clone $baseQuery)
            ->whereIn('ticket_status', ProjectRequest::slaTrackedTicketStatuses())
            ->whereNotNull('sla_resolution_due_at')
            ->where('sla_resolution_due_at', '<', now())
            ->count();

        $frtMinutes = (float) ((clone $baseQuery)
            ->whereNotNull('first_responded_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_responded_at)) as avg_minutes')
            ->value('avg_minutes') ?? 0);

        $mttrHours = (float) ((clone $baseQuery)
            ->whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, resolved_at)) / 60 as avg_hours')
            ->value('avg_hours') ?? 0);

        $slaResolvedScope = (clone $baseQuery)
            ->whereNotNull('resolved_at')
            ->whereNotNull('sla_resolution_due_at');

        $slaResolvedCount = (clone $slaResolvedScope)->count();
        $slaCompliantCount = (clone $slaResolvedScope)->whereColumn('resolved_at', '<=', 'sla_resolution_due_at')->count();

        $slaComplianceRate = $slaResolvedCount > 0
            ? round(($slaCompliantCount / $slaResolvedCount) * 100, 2)
            : 0;

        $subcategoryBreakdown = (clone $baseQuery)
            ->selectRaw("COALESCE(technical_subcategory, 'unclassified') as label, COUNT(*) as total")
            ->groupBy('label')
            ->orderByDesc('total')
            ->get();

        $priorityBreakdown = (clone $baseQuery)
            ->selectRaw('impact as label, COUNT(*) as total')
            ->groupBy('label')
            ->orderByRaw("FIELD(label, 'critical', 'high', 'medium', 'low')")
            ->get();

        $technicianPerformance = DB::table('project_requests as pr')
            ->leftJoin('users as u', 'u.id', '=', 'pr.developer_id')
            ->where('pr.ticket_category', 'technical_support')
            ->whereBetween('pr.created_at', [$start, $end])
            ->when($selectedSubcategory !== 'all', function ($query) use ($selectedSubcategory) {
                $query->where('pr.technical_subcategory', $selectedSubcategory);
            })
            ->selectRaw("COALESCE(u.name, 'Belum Ditugaskan') as technician_name")
            ->selectRaw('COUNT(*) as total_tickets')
            ->selectRaw("SUM(CASE WHEN pr.ticket_status IN ('resolved', 'closed') THEN 1 ELSE 0 END) as resolved_tickets")
            ->groupBy('technician_name')
            ->orderByDesc('total_tickets')
            ->limit(10)
            ->get();

        $overdueList = ProjectRequest::with(['client', 'developer'])
            ->where('ticket_category', 'technical_support')
            ->whereIn('ticket_status', ProjectRequest::slaTrackedTicketStatuses())
            ->whereNotNull('sla_resolution_due_at')
            ->where('sla_resolution_due_at', '<', now())
            ->orderBy('sla_resolution_due_at')
            ->take(10)
            ->get();

        $summary = [
            'total_tickets' => $totalTickets,
            'resolved_tickets' => $resolvedTickets,
            'backlog_tickets' => $backlogTickets,
            'overdue_tickets' => $overdueTickets,
            'frt_minutes' => round($frtMinutes, 2),
            'mttr_hours' => round($mttrHours, 2),
            'sla_compliance_rate' => $slaComplianceRate,
        ];

        return [
            'summary' => $summary,
            'selectedDateInput' => $selectedDateInput,
            'selectedSubcategory' => $selectedSubcategory,
            'allowedSubcategories' => $allowedSubcategories,
            'subcategoryBreakdown' => $subcategoryBreakdown,
            'priorityBreakdown' => $priorityBreakdown,
            'technicianPerformance' => $technicianPerformance,
            'overdueList' => $overdueList,
        ];
    }

    private function defaultSystemSettings(): array
    {
        return [
            'app_name' => config('app.name', 'Antrian Project'),
            'app_logo' => '',
            'app_favicon' => '',
            'admin_email' => 'admin@antrian.com',
            'per_page' => 15,
            'email_notifications' => true,
            'notification_window_days' => 3,
            'maintenance_mode' => false,
        ];
    }

    private function loadSystemSettings(): array
    {
        $defaults = $this->defaultSystemSettings();
        $path = $this->settingsFilePath();

        if (!File::exists($path)) {
            return $defaults;
        }

        $decoded = json_decode(File::get($path), true);

        if (!is_array($decoded)) {
            return $defaults;
        }

        return array_merge($defaults, $decoded);
    }
}
