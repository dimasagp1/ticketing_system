<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProjectRequest;
use App\Models\Queue;
use App\Models\ChatConversation;
use App\Models\ActivityLog;
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

        return back()->with('success', 'Settings updated successfully!');
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
