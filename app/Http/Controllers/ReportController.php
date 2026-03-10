<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Export project reports as PDF based on the given filter
     */
    public function exportPdf(Request $request)
    {
        $filter = $request->input('period', 'monthly'); // daily, weekly, monthly, yearly
        $date = Carbon::now();
        $startDate = null;
        $endDate = null;
        $periodLabel = '';

        switch ($filter) {
            case 'daily':
                $startDate = $date->copy()->startOfDay();
                $endDate = $date->copy()->endOfDay();
                $periodLabel = 'Harian (' . $startDate->format('d M Y') . ')';
                break;
            case 'weekly':
                $startDate = $date->copy()->startOfWeek();
                $endDate = $date->copy()->endOfWeek();
                $periodLabel = 'Mingguan (' . $startDate->format('d M') . ' - ' . $endDate->format('d M Y') . ')';
                break;
            case 'monthly':
                $startDate = $date->copy()->startOfMonth();
                $endDate = $date->copy()->endOfMonth();
                $periodLabel = 'Bulanan (' . $startDate->format('F Y') . ')';
                break;
            case 'yearly':
            default:
                $filter = 'yearly';
                $startDate = $date->copy()->startOfYear();
                $endDate = $date->copy()->endOfYear();
                $periodLabel = 'Tahunan (' . $startDate->format('Y') . ')';
                break;
        }

        $query = ProjectRequest::with('client', 'developer')
            ->whereBetween('created_at', [$startDate, $endDate]);

        // If the user isn't super admin, restrict according to their role (optional, typically reports are for admins)
        if (auth()->user()->isClient()) {
            $query->where('client_id', auth()->id());
        } elseif (auth()->user()->isDeveloper()) {
            $query->where('developer_id', auth()->id());
        }

        $projects = $query->latest()->get();

        $stats = [
            'total' => $projects->count(),
            'completed' => $projects->whereIn('ticket_status', ['resolved', 'closed'])->count(),
            'in_progress' => $projects->whereIn('ticket_status', ['in_progress', 'pending_user'])->count(),
            'pending' => $projects->where('ticket_status', 'open')->count()
        ];

        $data = [
            'projects' => $projects,
            'stats' => $stats,
            'periodLabel' => $periodLabel,
            'filter' => $filter,
            'generatedAt' => Carbon::now()->format('d M Y H:i:s'),
            'generator' => auth()->user()->name
        ];

        // Fix for "Cannot resolve public path" on hosting
        config([
            'dompdf.public_path' => public_path(),
            'dompdf.options.chroot' => base_path(),
        ]);

        $pdf = Pdf::loadView('reports.project-pdf', $data);

        // Optional: set paper to landscape if needed
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('Laporan_Proyek_' . ucfirst($filter) . '_' . date('Ymd') . '.pdf');
    }
}
