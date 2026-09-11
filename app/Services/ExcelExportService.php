<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Lead;
use App\Models\Transaction;
use App\Models\User;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

class ExcelExportService
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Export Full Multi-sheet XLSX:
     * RINGKASAN, KINERJA CS, SARAN & EVALUASI, PROGRAM, TRANSAKSI, LEADS
     */
    public function exportReport(string $period = 'today', ?string $customStart = null, ?string $customEnd = null, ?string $csFilter = null, string $filePath): string
    {
        $window = $this->reportService->getCutoffWindow($period, $customStart, $customEnd);
        $start = $window['start'];
        $end = $window['end'];

        $writer = new Writer();
        $writer->openToFile($filePath);

        $csQuery = User::where('role', 'admin')->where('status', 'active');
        if ($csFilter && $csFilter !== 'all') {
            $csQuery->where('id', $csFilter);
        }
        $csList = $csQuery->get();

        $metricsList = [];
        foreach ($csList as $cs) {
            $metricsList[] = $this->reportService->getCsMetrics($cs, $window);
        }

        $totalRevenue = array_sum(array_column($metricsList, 'revenue'));
        $totalTarget = array_sum(array_column($metricsList, 'period_target'));
        $totalTx = array_sum(array_column($metricsList, 'tx_count'));
        $totalLeads = array_sum(array_column($metricsList, 'leads_count'));
        $activeCsCount = count(array_filter($metricsList, fn ($m) => $m['revenue'] > 0));

        // 1. Sheet RINGKASAN
        $sheet = $writer->getCurrentSheet();
        $sheet->setName('RINGKASAN');
        $writer->addRow(Row::fromValues(['LAPORAN', 'PERIODE / NILAI']));
        $writer->addRow(Row::fromValues(['Sistem', 'CRM BMSS - Baitulmaal Sejuta Santri']));
        $writer->addRow(Row::fromValues(['Periode Cutoff', $window['label']]));
        $writer->addRow(Row::fromValues(['Leads Baru', $totalLeads]));
        $writer->addRow(Row::fromValues(['Jumlah Transaksi', $totalTx]));
        $writer->addRow(Row::fromValues(['Total Perolehan Infaq', $totalRevenue]));
        $writer->addRow(Row::fromValues(['Target Periode', $totalTarget]));
        $writer->addRow(Row::fromValues(['Capaian Target (%)', $totalTarget > 0 ? round(($totalRevenue / $totalTarget) * 100, 1) . '%' : '0%']));
        $writer->addRow(Row::fromValues(['CS Aktif Bertransaksi', $activeCsCount]));

        // 2. Sheet KINERJA CS
        $sheetKinerja = $writer->addNewSheetAndMakeItCurrent();
        $sheetKinerja->setName('KINERJA CS');
        $writer->addRow(Row::fromValues([
            'CS', 'Tier', 'Database', 'Target Harian (Rp)', 'Target Periode (Rp)',
            'Perolehan (Rp)', 'Capaian (%)', 'Leads', 'Follow-up', 'Closing',
            'Conversion (%)', 'Rp per 1K DB', 'Kapasitas', 'Status'
        ]));
        foreach ($metricsList as $m) {
            $writer->addRow(Row::fromValues([
                $m['user']->name,
                'Tier ' . $m['tier'],
                $m['db_count'],
                $m['daily_target'],
                $m['period_target'],
                $m['revenue'],
                $m['achievement_pct'],
                $m['leads_count'],
                $m['followup_count'],
                $m['closed_leads_count'],
                $m['conversion_pct'],
                $m['rev_per_1k_db'],
                $m['capacity_status'],
                $m['achievement_pct'] >= 100 ? 'Tercapai' : ($m['achievement_pct'] >= 80 ? 'Perlu Dorongan' : 'Perlu Evaluasi'),
            ]));
        }

        // 3. Sheet SARAN & EVALUASI
        $sheetSaran = $writer->addNewSheetAndMakeItCurrent();
        $sheetSaran->setName('SARAN & EVALUASI');
        $writer->addRow(Row::fromValues(['CS', 'Prioritas', 'Area', 'Temuan', 'Saran & Evaluasi']));
        foreach ($metricsList as $m) {
            $writer->addRow(Row::fromValues([
                $m['user']->name,
                $m['advice_priority'],
                $m['advice_area'],
                "Capaian {$m['achievement_pct']}%; DB {$m['db_count']}; FU {$m['followup_count']}; Conv {$m['conversion_pct']}%",
                $m['advice_text'],
            ]));
        }

        // 4. Sheet PROGRAM
        $sheetProgram = $writer->addNewSheetAndMakeItCurrent();
        $sheetProgram->setName('PROGRAM');
        $writer->addRow(Row::fromValues(['Program', 'Jumlah Transaksi', 'Total Infaq (Rp)', 'Donatur Unik']));

        $programAgg = Transaction::whereBetween('transaction_date', [$start, $end])
            ->where('status', 'Paid')
            ->when($csFilter && $csFilter !== 'all', fn ($q) => $q->where('owner_id', $csFilter))
            ->selectRaw('program, count(*) as tx_count, sum(amount) as total_amount, count(distinct contact_id) as donor_count')
            ->groupBy('program')
            ->get();

        foreach ($programAgg as $pa) {
            $writer->addRow(Row::fromValues([
                $pa->program ?: 'Umum / Lainnya',
                $pa->tx_count,
                (float) $pa->total_amount,
                $pa->donor_count,
            ]));
        }

        // 5. Sheet TRANSAKSI
        $sheetTrx = $writer->addNewSheetAndMakeItCurrent();
        $sheetTrx->setName('TRANSAKSI');
        $writer->addRow(Row::fromValues(['ID Transaksi', 'Waktu', 'Nama Donatur', 'CS Owner', 'Jenis', 'Program', 'Nominal (Rp)', 'Status', 'Metode Pembayaran']));

        Transaction::with(['contact', 'owner'])
            ->whereBetween('transaction_date', [$start, $end])
            ->when($csFilter && $csFilter !== 'all', fn ($q) => $q->where('owner_id', $csFilter))
            ->orderByDesc('transaction_date')
            ->chunk(500, function ($transactions) use ($writer) {
                foreach ($transactions as $tx) {
                    $writer->addRow(Row::fromValues([
                        $tx->id,
                        $tx->transaction_date?->format('Y-m-d H:i:s'),
                        $tx->contact?->name ?: '-',
                        $tx->owner?->name ?: '-',
                        $tx->type,
                        $tx->program,
                        (float) $tx->amount,
                        $tx->status,
                        $tx->payment_method,
                    ]));
                }
            });

        // 6. Sheet LEADS
        $sheetLeads = $writer->addNewSheetAndMakeItCurrent();
        $sheetLeads->setName('LEADS');
        $writer->addRow(Row::fromValues(['Waktu', 'Nama Lead', 'Kota', 'Sumber', 'CS Owner', 'Tahap / Stage', 'Minat Program', 'Potensi (Rp)', 'Status']));

        Lead::with('owner')
            ->whereBetween('created_at', [$start, $end])
            ->when($csFilter && $csFilter !== 'all', fn ($q) => $q->where('owner_id', $csFilter))
            ->orderByDesc('created_at')
            ->chunk(500, function ($leads) use ($writer) {
                foreach ($leads as $l) {
                    $writer->addRow(Row::fromValues([
                        $l->created_at?->format('Y-m-d H:i:s'),
                        $l->name,
                        $l->city ?: '-',
                        $l->source ?: '-',
                        $l->owner?->name ?: '-',
                        $l->stage,
                        $l->interest,
                        (float) $l->potential_amount,
                        $l->status,
                    ]));
                }
            });

        $writer->close();
        return $filePath;
    }

    /**
     * Export Contacts XLSX
     */
    public function exportContacts(User $user, string $filePath): string
    {
        $writer = new Writer();
        $writer->openToFile($filePath);

        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Kontak');
        $writer->addRow(Row::fromValues([
            'NISS', 'Nama Donatur', 'WhatsApp Donatur', 'Kota', 'CS Owner',
            'Slot WA CS', 'Nomor WA CS', 'Label WA CS', 'Status Donor', 'Status Relasi',
            'Catatan Relasi', 'Status Zakat', 'LTV (Rp)', 'Program Favorit', 'Sumber', 'Tags', 'Catatan'
        ]));

        $query = Contact::with(['owner', 'waAccount'])->where('is_archived', false);
        if (! $user->isMaster()) {
            $query->where('owner_id', $user->id);
        }

        $query->chunk(500, function ($contacts) use ($writer) {
            foreach ($contacts as $c) {
                $writer->addRow(Row::fromValues([
                    $c->niss,
                    $c->name,
                    $c->phone,
                    $c->city ?: '',
                    $c->owner?->name ?: '',
                    $c->waAccount?->slot ?: '',
                    $c->waAccount?->phone ?: '',
                    $c->waAccount?->label ?: '',
                    $c->status,
                    $c->relation_status,
                    $c->relationship_note ?: '',
                    $c->zakat_status,
                    (float) $c->ltv,
                    $c->program ?: '',
                    $c->source ?: '',
                    is_array($c->tags) ? implode(', ', $c->tags) : ($c->tags ?: ''),
                    $c->notes ?: '',
                ]));
            }
        });

        $writer->close();
        return $filePath;
    }
}
