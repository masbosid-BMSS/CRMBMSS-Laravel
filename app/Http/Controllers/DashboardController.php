<?php

namespace App\Http\Controllers;

use App\Models\Calculation;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Followup;
use App\Models\Lead;
use App\Models\Program;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $isMaster = $user->isMaster();
        $period = $request->query('period', 'today');
        $customStart = $request->query('start');
        $customEnd = $request->query('end');

        $window = $this->reportService->getCutoffWindow($period, $customStart, $customEnd);
        $start = $window['start'];
        $end = $window['end'];

        // Scoped models
        $contactQuery = Contact::active();
        $txQuery = Transaction::whereBetween('transaction_date', [$start, $end])->where('status', 'Paid');
        $calcQuery = Calculation::whereIn('status', ['Outstanding', 'Sebagian']);
        $followQuery = Followup::where('status', '!=', 'Completed');

        if (! $isMaster) {
            $contactQuery->where('owner_id', $user->id);
            $txQuery->where('owner_id', $user->id);
            $calcQuery->where('owner_id', $user->id);
            $followQuery->where('owner_id', $user->id);
        }

        $totalContacts = $contactQuery->count();
        $newContacts = (clone $contactQuery)->whereBetween('created_at', [$start, $end])->count();
        $totalFund = (float) $txQuery->sum('amount');
        $totalOutstandingZakat = (float) $calcQuery->get()->sum(fn ($z) => $z->remaining_amount);
        $activeFollowups = $followQuery->count();

        // Target Bulanan
        $monthlyTarget = (float) config('crm.monthly_target', 1000000000);
        $nowMonth = Carbon::now('Asia/Jakarta');
        $monthStart = $nowMonth->copy()->startOfMonth();
        $monthEnd = $nowMonth->copy()->endOfMonth();
        $currentMonthFund = (float) Transaction::whereBetween('transaction_date', [$monthStart, $monthEnd])
            ->where('status', 'Paid')
            ->when(! $isMaster, fn ($q) => $q->where('owner_id', $user->id))
            ->sum('amount');

        $monthPct = $monthlyTarget > 0 ? round(($currentMonthFund / $monthlyTarget) * 100, 1) : 0.0;
        $daysRemaining = max(1, $nowMonth->diffInDays($monthEnd));
        $neededDaily = $monthlyTarget > $currentMonthFund ? round(($monthlyTarget - $currentMonthFund) / $daysRemaining) : 0;

        // Infaq Chart Series
        $days = [];
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::now('Asia/Jakarta')->subDays($i);
            $dayStart = $d->copy()->startOfDay();
            $dayEnd = $d->copy()->endOfDay();
            $chartLabels[] = $d->translatedFormat('d M');

            $amount = (float) Transaction::whereBetween('transaction_date', [$dayStart, $dayEnd])
                ->where('status', 'Paid')
                ->where('type', 'Infak')
                ->when(! $isMaster, fn ($q) => $q->where('owner_id', $user->id))
                ->sum('amount');
            $chartData[] = $amount;
        }

        // Priority Follow-ups
        $priorityFollowups = Followup::with('contact')
            ->where('status', '!=', 'Completed')
            ->when(! $isMaster, fn ($q) => $q->where('owner_id', $user->id))
            ->orderBy('scheduled_at')
            ->take(4)
            ->get();

        // Issues / Butuh Perhatian
        $overdueCount = Followup::where('status', 'Overdue')
            ->when(! $isMaster, fn ($q) => $q->where('owner_id', $user->id))
            ->count();

        $untrustCount = Contact::active()->where('relation_status', 'Untrust')
            ->when(! $isMaster, fn ($q) => $q->where('owner_id', $user->id))
            ->count();

        $boredCount = Contact::active()->where('relation_status', 'Bosan')
            ->when(! $isMaster, fn ($q) => $q->where('owner_id', $user->id))
            ->count();

        $blockedCount = Contact::active()->where('relation_status', 'Blokir')
            ->when(! $isMaster, fn ($q) => $q->where('owner_id', $user->id))
            ->count();

        // CS KPI Table
        $csUsers = User::where('role', 'admin')->where('status', 'active')->get();
        $csMetrics = [];
        foreach ($csUsers as $cs) {
            $csMetrics[] = $this->reportService->getCsMetrics($cs, $window);
        }

        // Leads vs Transaksi counts in period
        $leadsCountPeriod = Lead::whereBetween('created_at', [$start, $end])
            ->when(! $isMaster, fn ($q) => $q->where('owner_id', $user->id))
            ->count();

        $txCountPeriod = (clone $txQuery)->count();

        // Program breakdown in period
        $programsBreakdown = Transaction::whereBetween('transaction_date', [$start, $end])
            ->where('status', 'Paid')
            ->when(! $isMaster, fn ($q) => $q->where('owner_id', $user->id))
            ->selectRaw('program, sum(amount) as total')
            ->groupBy('program')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        return view('dashboard.index', compact(
            'period',
            'window',
            'totalContacts',
            'newContacts',
            'totalFund',
            'totalOutstandingZakat',
            'activeFollowups',
            'monthlyTarget',
            'currentMonthFund',
            'monthPct',
            'daysRemaining',
            'neededDaily',
            'chartLabels',
            'chartData',
            'priorityFollowups',
            'overdueCount',
            'untrustCount',
            'boredCount',
            'blockedCount',
            'csMetrics',
            'leadsCountPeriod',
            'txCountPeriod',
            'programsBreakdown'
        ));
    }
}
