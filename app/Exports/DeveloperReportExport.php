<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DeveloperReportExport implements FromView, ShouldAutoSize, WithTitle, WithEvents
{
    protected array $reportData;

    public function __construct(array $reportData)
    {
        $this->reportData = $reportData;
    }

    public function view(): View
    {
        return view('reports.developer-excel', $this->reportData);
    }

    public function title(): string
    {
        return 'Laporan Kinerja Developer';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Enable wrap text for column D (Deskripsi Masalah / Proyek) and C (Nama Proyek)
                $sheet->getStyle('C')->getAlignment()->setWrapText(true);
                $sheet->getStyle('D')->getAlignment()->setWrapText(true);

                // Set max column width for description so it stays well-proportioned
                $sheet->getColumnDimension('C')->setWidth(30);
                $sheet->getColumnDimension('D')->setWidth(50);
            },
        ];
    }
}
