<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyLogController extends Controller
{
    /**
     * Display a listing of the daily logs for the authenticated user.
     */
    public function index(Request $request)
    {
        $query = DailyLog::where('user_id', Auth::id())
            ->orderBy('log_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('log_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('log_date', '<=', $request->date_to);
        }

        // Filter by month shortcut
        if ($request->filled('month')) {
            $query->whereYear('log_date', substr($request->month, 0, 4))
                  ->whereMonth('log_date', substr($request->month, 5, 2));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by source
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $logs = $query->paginate(15)->withQueryString();

        // Stats for today & this month
        $todayCount    = DailyLog::where('user_id', Auth::id())->whereDate('log_date', today())->count();
        $monthCount    = DailyLog::where('user_id', Auth::id())
                            ->whereYear('log_date', now()->year)
                            ->whereMonth('log_date', now()->month)
                            ->count();
        $pendingCount  = DailyLog::where('user_id', Auth::id())->where('status', 'Pending')->count();
        $totalDuration = DailyLog::where('user_id', Auth::id())
                            ->whereYear('log_date', now()->year)
                            ->whereMonth('log_date', now()->month)
                            ->sum('duration_minutes');

        return view('daily-logs.index', compact(
            'logs', 'todayCount', 'monthCount', 'pendingCount', 'totalDuration'
        ));
    }

    /**
     * Show the form for creating a new daily log.
     */
    public function create()
    {
        return view('daily-logs.create');
    }

    /**
     * Store a newly created daily log in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'log_date'          => 'required|date',
            'reporter_name'     => 'required|string|max:255',
            'department'        => 'nullable|string|max:255',
            'contact_info'      => 'nullable|string|max:255',
            'source'            => 'required|in:' . implode(',', DailyLog::sources()),
            'issue_description' => 'required|string',
            'action_taken'      => 'required|string',
            'status'            => 'required|in:' . implode(',', DailyLog::statuses()),
            'duration_minutes'  => 'nullable|integer|min:1|max:480',
            'notes'             => 'nullable|string|max:1000',
        ], [
            'log_date.required'          => 'Tanggal penanganan wajib diisi.',
            'reporter_name.required'     => 'Nama pelapor wajib diisi.',
            'source.required'            => 'Sumber komplain wajib dipilih.',
            'issue_description.required' => 'Deskripsi masalah wajib diisi.',
            'action_taken.required'      => 'Tindakan yang diambil wajib diisi.',
            'status.required'            => 'Status penyelesaian wajib dipilih.',
        ]);

        $validated['user_id'] = Auth::id();

        DailyLog::create($validated);

        return redirect()->route('daily-logs.index')
            ->with('success', 'Log harian berhasil disimpan.');
    }

    /**
     * Display the specified daily log.
     */
    public function show(DailyLog $dailyLog)
    {
        $this->authorizeOwner($dailyLog);
        return view('daily-logs.show', compact('dailyLog'));
    }

    /**
     * Show the form for editing the specified daily log.
     */
    public function edit(DailyLog $dailyLog)
    {
        $this->authorizeOwner($dailyLog);
        return view('daily-logs.edit', compact('dailyLog'));
    }

    /**
     * Update the specified daily log in storage.
     */
    public function update(Request $request, DailyLog $dailyLog)
    {
        $this->authorizeOwner($dailyLog);

        $validated = $request->validate([
            'log_date'          => 'required|date',
            'reporter_name'     => 'required|string|max:255',
            'department'        => 'nullable|string|max:255',
            'contact_info'      => 'nullable|string|max:255',
            'source'            => 'required|in:' . implode(',', DailyLog::sources()),
            'issue_description' => 'required|string',
            'action_taken'      => 'required|string',
            'status'            => 'required|in:' . implode(',', DailyLog::statuses()),
            'duration_minutes'  => 'nullable|integer|min:1|max:480',
            'notes'             => 'nullable|string|max:1000',
        ]);

        $dailyLog->update($validated);

        return redirect()->route('daily-logs.index')
            ->with('success', 'Log harian berhasil diperbarui.');
    }

    /**
     * Remove the specified daily log from storage.
     */
    public function destroy(DailyLog $dailyLog)
    {
        $this->authorizeOwner($dailyLog);
        $dailyLog->delete();

        return redirect()->route('daily-logs.index')
            ->with('success', 'Log harian berhasil dihapus.');
    }

    /**
     * Ensure the authenticated user owns this log.
     */
    private function authorizeOwner(DailyLog $dailyLog): void
    {
        if ($dailyLog->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
