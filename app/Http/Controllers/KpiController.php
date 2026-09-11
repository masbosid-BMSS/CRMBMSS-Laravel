<?php

namespace App\Http\Controllers;

use App\Models\KpiTarget;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KpiController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index()
    {
        if (! Auth::user()->isMaster()) {
            abort(403, 'Hanya Master Admin yang dapat mengakses target & KPI.');
        }

        $admins = User::with(['kpiTarget', 'contacts'])->where('role', 'admin')->where('status', 'active')->get();

        return view('kpi-targets.index', compact('admins'));
    }

    public function updateAll(Request $request)
    {
        if (! Auth::user()->isMaster()) {
            abort(403, 'Akses ditolak.');
        }

        $targets = $request->input('targets', []);

        foreach ($targets as $userId => $item) {
            $user = User::find($userId);
            if (! $user) continue;

            $tier = (int) ($item['tier'] ?? 1);
            $daily = ! empty($item['daily_target']) ? (float) str_replace(['.', ','], ['', '.'], $item['daily_target']) : $this->reportService->getTierBenchmark($tier);

            KpiTarget::updateOrCreate(
                ['user_id' => $userId],
                [
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'tier' => $tier,
                    'start_date' => ! empty($item['start_date']) ? $item['start_date'] : null,
                    'daily_target' => $daily,
                    'followup_target' => (int) ($item['followup_target'] ?? 150),
                    'conversion_target' => (float) ($item['conversion_target'] ?? 8.0),
                    'retention_target' => (float) ($item['retention_target'] ?? 60.0),
                ]
            );
        }

        return redirect()->route('kpi-targets.index')->with('success', 'Target KPI CS berhasil disimpan.');
    }
}
