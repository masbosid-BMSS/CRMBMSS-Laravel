<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Followup;
use App\Models\Lead;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class ReportService
{
    /**
     * Hitung window operasional cut-off jam 15:31 s.d. 15:30
     * Khusus Senin: mencakup Sabtu 15:31 s.d. Senin 15:30
     */
    public function getCutoffWindow(string $period = 'today', ?string $customStart = null, ?string $customEnd = null): array
    {
        $now = Carbon::now('Asia/Jakarta');
        $cutoffToday = $now->copy()->setTime(15, 30, 59);

        // Jika sekarang belum lewat 15:30, end period adalah hari ini 15:30, jika sudah lewat maka besok 15:30
        $end = $now->greaterThan($cutoffToday) ? $now->copy()->setTime(15, 30, 59) : $cutoffToday;

        if ($period === 'today') {
            // Cutoff hari ini: 15:31 kemarin s.d. 15:30 hari ini
            // Khusus Senin: dari Sabtu 15:31
            $start = $end->copy()->subDay()->setTime(15, 31, 0);
            if ($end->dayOfWeek === Carbon::MONDAY) {
                $start = $end->copy()->subDays(2)->setTime(15, 31, 0);
            }
        } elseif ($period === '7d') {
            $start = $end->copy()->subDays(7)->setTime(15, 31, 0);
        } elseif ($period === '30d') {
            $start = $end->copy()->subDays(30)->setTime(15, 31, 0);
        } elseif ($period === 'year') {
            $start = $end->copy()->subMonths(11)->startOfMonth()->setTime(0, 0, 0);
        } elseif ($period === 'custom' && $customStart && $customEnd) {
            $start = Carbon::parse($customStart, 'Asia/Jakarta')->subDay()->setTime(15, 31, 0);
            $end = Carbon::parse($customEnd, 'Asia/Jakarta')->setTime(15, 30, 59);
        } else {
            $start = $end->copy()->subDay()->setTime(15, 31, 0);
        }

        return [
            'start' => $start,
            'end' => $end,
            'label' => $start->translatedFormat('d M Y H:i') . ' — ' . $end->translatedFormat('d M Y H:i'),
            'days' => max(1, (int) ceil($start->diffInHours($end) / 24)),
        ];
    }

    public function getTierBenchmark(int $tier): float
    {
        return match ($tier) {
            3 => 3500000.0,
            2 => 2000000.0,
            default => 1000000.0,
        };
    }

    public function getCapacityStatus(int $dbCount): array
    {
        if ($dbCount > 30000) {
            return ['status' => 'Overload', 'class' => 'text-red-600 font-bold', 'badge' => 's-dorm'];
        }
        if ($dbCount > 25000) {
            return ['status' => 'Padat', 'class' => 'text-amber-600 font-semibold', 'badge' => 's-risk'];
        }
        if ($dbCount >= 15000) {
            return ['status' => 'Sehat', 'class' => 'text-emerald-600 font-semibold', 'badge' => 's-active'];
        }
        return ['status' => 'Di bawah ideal', 'class' => 'text-amber-600', 'badge' => 's-risk'];
    }

    public function getEvaluationAdvice(array $metrics): array
    {
        $db = $metrics['db_count'];
        $pct = $metrics['achievement_pct'];
        $follow = $metrics['followup_count'];
        $fTarget = $metrics['followup_target_period'];
        $leads = $metrics['leads_count'];
        $conv = $metrics['conversion_pct'];
        $convT = $metrics['conversion_target_pct'];

        if ($db > 30000) {
            return [
                'priority' => 'TINGGI',
                'area' => 'Redistribusi Database',
                'advice' => 'Database melewati 30.000 kontak. Redistribusikan sebagian database agar follow-up dan retensi tetap terjaga.',
            ];
        }
        if ($db < 15000 && $pct < 100) {
            return [
                'priority' => 'TINGGI',
                'area' => 'Tambah Database',
                'advice' => 'Kapasitas database masih di bawah rentang ideal (15.000-30.000). Tambahkan database/leads berkualitas.',
            ];
        }
        if ($follow < ($fTarget * 0.8) && $pct < 100) {
            return [
                'priority' => 'TINGGI',
                'area' => 'Tingkatkan Follow-up',
                'advice' => 'Database tersedia, tetapi volume follow-up masih di bawah target. Prioritaskan kontak aktif yang belum disentuh.',
            ];
        }
        if ($leads > 0 && $conv < ($convT * 0.8)) {
            return [
                'priority' => 'TINGGI',
                'area' => 'Evaluasi Conversion',
                'advice' => 'Aktivitas lead ada tetapi conversion rendah. Evaluasi segmentasi, pendekatan komunikasi, penawaran program, dan closing.',
            ];
        }
        if ($conv >= $convT && $db < 20000) {
            return [
                'priority' => 'SEDANG',
                'area' => 'Potensi Scale-up',
                'advice' => 'Conversion sehat dan database belum padat. Tambahkan database berkualitas secara bertahap.',
            ];
        }
        if ($pct >= 100) {
            return [
                'priority' => 'BAIK',
                'area' => 'Pertahankan & Scale',
                'advice' => 'Target tercapai. Pertahankan pola kerja dan pertimbangkan peningkatan target secara bertahap.',
            ];
        }
        return [
            'priority' => 'SEDANG',
            'area' => 'Perkuat Retensi',
            'advice' => 'Perolehan belum mencapai target. Prioritaskan donor lama, laporan program, ucapan terima kasih, dan transaksi ulang.',
        ];
    }

    public function getCsMetrics(User $user, array $window): array
    {
        $start = $window['start'];
        $end = $window['end'];
        $days = $window['days'];

        $kpi = $user->kpiTarget;
        $tier = $kpi ? $kpi->tier : 1;
        $dailyTarget = $kpi ? (float) $kpi->daily_target : $this->getTierBenchmark($tier);
        $periodTarget = $dailyTarget * $days;

        $followupDailyTarget = $kpi ? (int) $kpi->followup_target : 150;
        $followupTargetPeriod = $followupDailyTarget * $days;

        $convTarget = $kpi ? (float) $kpi->conversion_target : 8.0;
        $retTarget = $kpi ? (float) $kpi->retention_target : 60.0;

        // DB contact count
        $dbCount = Contact::where('owner_id', $user->id)
            ->where('is_archived', false)
            ->count();

        // Transactions in period
        $revenue = (float) Transaction::where('owner_id', $user->id)
            ->whereBetween('transaction_date', [$start, $end])
            ->where('status', 'Paid')
            ->sum('amount');

        $txCount = Transaction::where('owner_id', $user->id)
            ->whereBetween('transaction_date', [$start, $end])
            ->where('status', 'Paid')
            ->count();

        // Leads in period
        $leadsCount = Lead::where('owner_id', $user->id)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $closedLeadsCount = Lead::where('owner_id', $user->id)
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('stage', ['Donasi'])
            ->count();

        $convPct = $leadsCount > 0 ? round(($closedLeadsCount / $leadsCount) * 100, 1) : 0.0;

        // Follow-ups in period
        $followupCount = Followup::where('owner_id', $user->id)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $achievementPct = $periodTarget > 0 ? round(($revenue / $periodTarget) * 100, 1) : 0.0;
        $revPer1k = $dbCount > 0 ? round(($revenue / $dbCount) * 1000, 0) : 0.0;

        $capInfo = $this->getCapacityStatus($dbCount);

        $metrics = [
            'user' => $user,
            'tier' => $tier,
            'db_count' => $dbCount,
            'daily_target' => $dailyTarget,
            'period_target' => $periodTarget,
            'revenue' => $revenue,
            'tx_count' => $txCount,
            'achievement_pct' => $achievementPct,
            'leads_count' => $leadsCount,
            'closed_leads_count' => $closedLeadsCount,
            'conversion_pct' => $convPct,
            'conversion_target_pct' => $convTarget,
            'retention_target_pct' => $retTarget,
            'followup_count' => $followupCount,
            'followup_target_period' => $followupTargetPeriod,
            'rev_per_1k_db' => $revPer1k,
            'capacity_status' => $capInfo['status'],
            'capacity_class' => $capInfo['class'],
            'capacity_badge' => $capInfo['badge'],
        ];

        $advice = $this->getEvaluationAdvice($metrics);
        $metrics['advice_priority'] = $advice['priority'];
        $metrics['advice_area'] = $advice['area'];
        $metrics['advice_text'] = $advice['advice'];

        return $metrics;
    }
}
