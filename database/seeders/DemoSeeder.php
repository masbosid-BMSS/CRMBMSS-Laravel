<?php

namespace Database\Seeders;

use App\Models\Calculation;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Followup;
use App\Models\Lead;
use App\Models\PaymentMethod;
use App\Models\Program;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    /**
     * Seeder untuk data demo fungsional lengkap (5 CS, 120+ kontak detail,
     * transaksi historis 12 bulan, follow-up, zakat, leads)
     */
    public function run(): void
    {
        $this->call(DatabaseSeeder::class);

        $csList = User::where('role', 'admin')->get();
        if ($csList->isEmpty()) return;

        $programs = Program::all();
        $methods = PaymentMethod::all();
        $campaigns = Campaign::all();

        $names = [
            'Ahmad Fauzi', 'Siti Maryam', 'Bambang Riyadi', 'Nur Aisyah', 'Rizal Hidayat',
            'Fajar Nugroho', 'Hendra Saputra', 'Yuni Astuti', 'Arif Wibowo', 'Lina Marlina',
            'Nadia Putri', 'Rangga Pratama', 'Dewi Lestari', 'Agus Salim', 'Rahmawati',
            'Hafiz Akbar', 'Maya Sari', 'Dedi Kurniawan', 'Fitri Handayani', 'Rudi Hartono'
        ];

        $cities = ['Bandung', 'Jakarta', 'Bekasi', 'Yogyakarta', 'Surabaya', 'Semarang', 'Depok', 'Bogor', 'Solo', 'Tangerang'];
        $statuses = ['Loyal', 'Aktif', 'Aktif', 'Baru', 'At Risk', 'Dormant'];
        $relations = ['Normal', 'Normal', 'Normal', 'Normal', 'Untrust', 'Bosan', 'Blokir'];
        $sources = ['Ngalamat', 'Meta Ads', 'Referral', 'Webinar', 'Landing Page'];

        $contacts = [];
        $seq = 1;

        // Buat 100 kontak realistis
        for ($i = 0; $i < 100; $i++) {
            $cs = $csList[$i % $csList->count()];
            $wa = $cs->waAccounts()->first();
            $niss = 'NISS-' . str_pad((string) $seq++, 8, '0', STR_PAD_LEFT);
            $name = $names[$i % count($names)] . ' ' . ($i + 1);
            $rel = $relations[$i % count($relations)];
            $prog = $programs[$i % $programs->count()]->name;

            $c = Contact::firstOrCreate(
                ['niss' => $niss],
                [
                    'id' => (string) Str::uuid(),
                    'name' => $name,
                    'phone' => '08' . str_pad((string) (1200000000 + $i * 7919), 10, '0'),
                    'city' => $cities[$i % count($cities)],
                    'owner_id' => $cs->id,
                    'wa_account_id' => $wa?->id,
                    'status' => $statuses[$i % count($statuses)],
                    'relation_status' => $rel,
                    'relationship_note' => $rel !== 'Normal' ? "Catatan relasi untuk kondisi donatur status {$rel}." : null,
                    'zakat_status' => $i % 7 === 0 ? 'Outstanding' : ($i % 5 === 0 ? 'Lunas' : 'Belum Dihitung'),
                    'ltv' => ($i % 10 + 1) * 250000,
                    'program' => $prog,
                    'source' => $sources[$i % count($sources)],
                    'tags' => ['#Demo', "#{$prog}"],
                    'notes' => 'Kontak demo hasil seeder sistem CRM BMSS.',
                    'last_activity_at' => now()->subDays($i % 15),
                    'created_at' => now()->subDays($i % 40),
                ]
            );

            $contacts[] = $c;
        }

        // Buat 150 Transaksi historis (12 bulan terakhir + 7 hari terakhir)
        foreach ($contacts as $idx => $contact) {
            $txCount = ($idx % 3) + 1;
            for ($k = 0; $k < $txCount; $k++) {
                $type = ($idx + $k) % 4 === 0 ? 'Zakat' : 'Infak';
                $amt = $type === 'Zakat' ? 2500000 : (100000 * (($idx % 10) + 1));
                $d = now()->subDays(($idx * 3 + $k * 5) % 180);

                Transaction::create([
                    'id' => 'TRX-' . strtoupper(Str::random(6)),
                    'contact_id' => $contact->id,
                    'owner_id' => $contact->owner_id,
                    'recorded_by_id' => $contact->owner_id,
                    'type' => $type,
                    'program' => $contact->program ?: 'Infaq Umum',
                    'campaign_id' => $campaigns->isNotEmpty() ? $campaigns[$idx % $campaigns->count()]->id : null,
                    'amount' => $amt,
                    'status' => 'Paid',
                    'payment_method' => $methods->isNotEmpty() ? $methods[$k % $methods->count()]->name : 'BCA',
                    'transaction_date' => $d,
                    'created_at' => $d,
                ]);
            }
        }

        // Buat Zakat Calculations
        foreach (array_slice($contacts, 0, 15) as $zIdx => $contact) {
            $assets = 250000000 + ($zIdx * 50000000);
            $net = $assets;
            $nisab = 187000000;
            $zakat = round($net * 0.025);
            $paid = $zIdx % 2 === 0 ? $zakat : round($zakat / 2);
            $st = $paid >= $zakat ? 'Lunas' : 'Outstanding';

            Calculation::create([
                'id' => (string) Str::uuid(),
                'ref_no' => 'PIZ-' . date('ymd') . '-' . strtoupper(Str::random(4)),
                'contact_id' => $contact->id,
                'owner_id' => $contact->owner_id,
                'type' => 'Zakat Maal',
                'assets_total' => $assets,
                'deductions_total' => 0,
                'net_amount' => $net,
                'nisab_amount' => $nisab,
                'rate' => 0.025,
                'zakat_amount' => $zakat,
                'paid_amount' => $paid,
                'haul_status' => 'yes',
                'status' => $st,
                'calculation_date' => now()->subDays($zIdx * 2)->toDateString(),
                'items' => [
                    ['label' => 'Saldo Bank', 'value' => $assets * 0.6],
                    ['label' => 'Investasi', 'value' => $assets * 0.4],
                ],
                'note' => 'Perhitungan demo zakat terintegrasi.',
            ]);

            $contact->update(['zakat_status' => $st]);
        }

        // Buat Follow-ups
        foreach (array_slice($contacts, 0, 25) as $fIdx => $contact) {
            $status = $fIdx % 5 === 0 ? 'Overdue' : ($fIdx % 3 === 0 ? 'Today' : 'Scheduled');
            $sched = match ($status) {
                'Overdue' => now()->subDays(2),
                'Today' => now()->addHours(2),
                default => now()->addDays(2),
            };

            Followup::create([
                'id' => (string) Str::uuid(),
                'contact_id' => $contact->id,
                'owner_id' => $contact->owner_id,
                'reason' => $fIdx % 3 === 0 ? 'Zakat' : 'Repeat Donation',
                'title' => 'Follow-up donasi rutin & sapaan ' . ($fIdx + 1),
                'priority' => 'Normal',
                'scheduled_at' => $sched,
                'status' => $status,
                'notes' => 'Pengingat otomatis demo.',
            ]);
        }

        // Buat Leads di Pipeline
        $stages = ['Lead Baru', 'Contacted', 'Interested', 'Follow-up', 'Donasi'];
        for ($l = 0; $l < 25; $l++) {
            $cs = $csList[$l % $csList->count()];
            $stg = $stages[$l % count($stages)];

            Lead::create([
                'id' => (string) Str::uuid(),
                'name' => 'Calon Donatur ' . ($l + 1),
                'phone' => '0857' . str_pad((string) (10000000 + $l * 123), 8, '0'),
                'city' => $cities[$l % count($cities)],
                'source' => $sources[$l % count($sources)],
                'owner_id' => $cs->id,
                'stage' => $stg,
                'interest' => $programs[$l % $programs->count()]->name,
                'potential_amount' => 250000 * (($l % 4) + 1),
                'status' => $stg === 'Donasi' ? 'Donasi' : 'Aktif',
                'created_at' => now()->subDays($l % 10),
            ]);
        }
    }
}
