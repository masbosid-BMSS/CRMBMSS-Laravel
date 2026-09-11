<?php

namespace App\Services;

use App\Models\Calculation;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Support\Str;

class ZakatCalculatorService
{
    /**
     * Hitung total harta, nisab, haul, dan zakat bersih
     */
    public function calculate(array $data): array
    {
        $assets = (float) ($data['assets_total'] ?? 0);
        $deductions = (float) ($data['deductions_total'] ?? 0);
        $net = max(0, $assets - $deductions);

        $goldPrice = (float) ($data['gold_price'] ?? (float) config('crm.default_gold_price', 2200000));
        $nisab = $goldPrice * 85.0; // 85 gram emas

        $yearMethod = $data['year_method'] ?? 'h'; // 'h' (Hijriyah: 2.5%) atau 'm' (Masehi: 2.5775%)
        $rate = $yearMethod === 'm' ? 0.025775 : 0.025;

        $isNisabMet = $nisab > 0 && $net >= $nisab;
        $haulStatus = $data['haul_status'] ?? 'yes';
        $isHaulMet = in_array($haulStatus, ['yes', 'na'], true);

        $isWajib = $isNisabMet && $isHaulMet;
        $zakatAmount = $isWajib ? round($net * $rate) : 0;

        return [
            'assets_total' => $assets,
            'deductions_total' => $deductions,
            'net_amount' => $net,
            'gold_price' => $goldPrice,
            'nisab_amount' => $nisab,
            'rate' => $rate,
            'year_method' => $yearMethod,
            'is_nisab_met' => $isNisabMet,
            'haul_status' => $haulStatus,
            'is_haul_met' => $isHaulMet,
            'is_wajib' => $isWajib,
            'zakat_amount' => $zakatAmount,
            'explanation' => $this->generateExplanation($net, $nisab, $rate, $haulStatus, $zakatAmount),
        ];
    }

    public function generateExplanation(float $net, float $nisab, float $rate, string $haul, float $zakat): string
    {
        $ratePercent = number_format($rate * 100, 4, ',', '.') . '%';
        $netFmt = 'Rp ' . number_format($net, 0, ',', '.');
        $nisabFmt = 'Rp ' . number_format($nisab, 0, ',', '.');
        $zakatFmt = 'Rp ' . number_format($zakat, 0, ',', '.');

        if ($zakat <= 0 && $net < $nisab) {
            return "Harta bersih sebesar {$netFmt} masih di bawah nisab {$nisabFmt}, sehingga pada perhitungan ini belum wajib zakat.";
        }

        if ($zakat <= 0 && $haul === 'no') {
            return "Harta bersih sebesar {$netFmt} telah mencapai nisab {$nisabFmt}, namun status haul belum terpenuhi, sehingga pada perhitungan ini belum wajib zakat.";
        }

        return "Harta bersih sebesar {$netFmt} telah mencapai nisab {$nisabFmt} dan syarat haul dinyatakan terpenuhi atau tidak berlaku. Zakat dihitung sebesar {$ratePercent} dari harta bersih, sehingga hasilnya {$zakatFmt}.";
    }

    public function saveCalculation(Contact $contact, User $user, array $data, ?string $existingId = null): Calculation
    {
        $calculated = $this->calculate($data);
        $status = $calculated['zakat_amount'] > 0 ? 'Outstanding' : 'Belum Wajib';

        if ($existingId) {
            $calc = Calculation::findOrFail($existingId);
            $paid = (float) $calc->paid_amount;
            $rem = max(0, $calculated['zakat_amount'] - $paid);
            if ($calculated['zakat_amount'] <= 0) {
                $status = 'Belum Wajib';
            } elseif ($rem === 0.0) {
                $status = 'Lunas';
            } elseif ($paid > 0) {
                $status = 'Sebagian';
            } else {
                $status = 'Outstanding';
            }

            $calc->update([
                'type' => $data['type'] ?? $calc->type,
                'assets_total' => $calculated['assets_total'],
                'deductions_total' => $calculated['deductions_total'],
                'net_amount' => $calculated['net_amount'],
                'nisab_amount' => $calculated['nisab_amount'],
                'rate' => $calculated['rate'],
                'zakat_amount' => $calculated['zakat_amount'],
                'haul_status' => $calculated['haul_status'],
                'status' => $status,
                'calculation_date' => $data['calculation_date'] ?? now()->toDateString(),
                'items' => $data['items'] ?? [],
                'note' => $data['note'] ?? null,
            ]);
        } else {
            $refNo = 'PIZ-' . date('ymd') . '-' . strtoupper(Str::random(4));
            $calc = Calculation::create([
                'id' => (string) Str::uuid(),
                'ref_no' => $refNo,
                'contact_id' => $contact->id,
                'owner_id' => $contact->owner_id,
                'type' => $data['type'] ?? 'Zakat Maal',
                'assets_total' => $calculated['assets_total'],
                'deductions_total' => $calculated['deductions_total'],
                'net_amount' => $calculated['net_amount'],
                'nisab_amount' => $calculated['nisab_amount'],
                'rate' => $calculated['rate'],
                'zakat_amount' => $calculated['zakat_amount'],
                'paid_amount' => 0,
                'haul_status' => $calculated['haul_status'],
                'status' => $status,
                'calculation_date' => $data['calculation_date'] ?? now()->toDateString(),
                'items' => $data['items'] ?? [],
                'note' => $data['note'] ?? null,
            ]);
        }

        // Update contact zakat status
        $contact->update([
            'zakat_status' => $status,
            'last_activity_at' => now(),
        ]);

        return $calc;
    }
}
