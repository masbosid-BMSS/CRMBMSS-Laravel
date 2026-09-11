<?php

namespace App\Http\Controllers;

use App\Services\ExcelExportService;
use Illuminate\Http\Request;

class ReportExportController extends Controller
{
    protected ExcelExportService $excelService;

    public function __construct(ExcelExportService $excelService)
    {
        $this->excelService = $excelService;
    }

    public function export(Request $request)
    {
        $period = $request->query('period', 'today');
        $start = $request->query('start');
        $end = $request->query('end');
        $cs = $request->query('cs', 'all');

        $tempFile = tempnam(sys_get_temp_dir(), 'bmss_report_') . '.xlsx';
        $this->excelService->exportReport($period, $start, $end, $cs, $tempFile);

        $filename = 'Laporan_CRM_BMSS_' . date('Y-m-d') . '.xlsx';

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
