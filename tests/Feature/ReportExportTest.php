<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ExcelExportService;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_report_xlsx_generates_valid_file(): void
    {
        $master = User::create([
            'id' => 'u_master',
            'name' => 'Master Admin',
            'username' => 'bmssmanfaat',
            'password' => bcrypt('bismillah100'),
            'role' => 'master',
            'status' => 'active',
        ]);

        $reportService = app(ReportService::class);
        $excelService = app(ExcelExportService::class);

        $tempFile = tempnam(sys_get_temp_dir(), 'test_export_') . '.xlsx';
        $path = $excelService->exportReport('today', null, null, 'all', $tempFile);

        $this->assertFileExists($path);
        $this->assertGreaterThan(0, filesize($path));

        if (file_exists($path)) {
            unlink($path);
        }
    }
}
