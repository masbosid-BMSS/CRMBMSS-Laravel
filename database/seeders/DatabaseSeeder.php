<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\PaymentMethod;
use App\Models\Program;
use App\Models\Setting;
use App\Models\User;
use App\Models\WaAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed baseline data untuk mode production
     */
    public function run(): void
    {
        // 1. Settings Default
        Setting::set('org_name', 'Baitulmaal Sejuta Santri');
        Setting::set('monthly_target', 1000000000);
        Setting::set('org_email', 'admin@bmss.local');

        // 2. Master Admin
        $master = User::firstOrCreate(
            ['username' => 'bmssmanfaat'],
            [
                'id' => 'u_master',
                'name' => 'Master Admin',
                'password' => Hash::make('bismillah100'),
                'role' => 'master',
                'status' => 'active',
                'must_change_password' => false,
            ]
        );

        // 3. Admin / CS
        $csList = [
            ['id' => 'u_nisa', 'name' => 'Nisa', 'username' => 'nisa.cs', 'tier' => 1, 'daily' => 1000000, 'fu' => 150, 'conv' => 8.0, 'ret' => 60.0, 'start' => '2025-08-01'],
            ['id' => 'u_rani', 'name' => 'Rani', 'username' => 'rani.cs', 'tier' => 2, 'daily' => 2000000, 'fu' => 180, 'conv' => 8.0, 'ret' => 62.0, 'start' => '2024-02-01'],
            ['id' => 'u_fikri', 'name' => 'Fikri', 'username' => 'fikri.cs', 'tier' => 3, 'daily' => 3500000, 'fu' => 200, 'conv' => 9.0, 'ret' => 65.0, 'start' => '2023-06-01'],
            ['id' => 'u_dina', 'name' => 'Dina', 'username' => 'dina.cs', 'tier' => 3, 'daily' => 4000000, 'fu' => 220, 'conv' => 9.0, 'ret' => 68.0, 'start' => '2022-09-01'],
            ['id' => 'u_yusuf', 'name' => 'Yusuf', 'username' => 'yusuf.cs', 'tier' => 2, 'daily' => 2300000, 'fu' => 180, 'conv' => 8.0, 'ret' => 62.0, 'start' => '2024-01-15'],
        ];

        foreach ($csList as $cs) {
            $user = User::firstOrCreate(
                ['username' => $cs['username']],
                [
                    'id' => $cs['id'],
                    'name' => $cs['name'],
                    'password' => Hash::make('admin12345'),
                    'role' => 'admin',
                    'status' => 'active',
                    'must_change_password' => false,
                ]
            );

            // 5 slot WhatsApp
            for ($slot = 1; $slot <= 5; $slot++) {
                WaAccount::firstOrCreate(
                    ['user_id' => $user->id, 'slot' => $slot],
                    [
                        'id' => "wa_{$user->id}_{$slot}",
                        'label' => "WA {$slot}",
                        'phone' => "08125" . substr($user->id, 2, 2) . "00{$slot}",
                        'capacity' => 5000,
                        'status' => 'Aktif',
                    ]
                );
            }

            // Target KPI
            $user->kpiTarget()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'id' => (string) Str::uuid(),
                    'tier' => $cs['tier'],
                    'daily_target' => $cs['daily'],
                    'followup_target' => $cs['fu'],
                    'conversion_target' => $cs['conv'],
                    'retention_target' => $cs['ret'],
                    'start_date' => $cs['start'],
                ]
            );
        }

        // 4. Programs Global
        $programs = [
            ['id' => 'pr_zakat', 'name' => 'Zakat', 'category' => 'Zakat', 'desc' => 'Penyaluran dana zakat maal dan fitrah.'],
            ['id' => 'pr_guru', 'name' => 'Guru Ngaji', 'category' => 'Sedekah', 'desc' => 'Dukungan kafalah dan kesejahteraan guru ngaji Al-Qur’an.'],
            ['id' => 'pr_sumur', 'name' => 'Sumur', 'category' => 'Sedekah', 'desc' => 'Pembangunan sarana air bersih & sumur bor santri.'],
            ['id' => 'pr_subuh', 'name' => 'Sedekah Subuh', 'category' => 'Sedekah', 'desc' => 'Sedekah harian untuk keberkahan awal pagi.'],
            ['id' => 'pr_wakaf', 'name' => 'Wakaf', 'category' => 'Wakaf', 'desc' => 'Wakaf produktif, fasilitas pesantren, dan masjid.'],
            ['id' => 'pr_santri', 'name' => 'Santri', 'category' => 'Sedekah', 'desc' => 'Bantuan makan dan kebutuhan pangan santri penghafal Quran.'],
            ['id' => 'pr_kemanusiaan', 'name' => 'Kemanusiaan', 'category' => 'Sosial', 'desc' => 'Tanggap bencana dan bantuan sosial darurat.'],
        ];

        foreach ($programs as $p) {
            Program::firstOrCreate(
                ['name' => $p['name']],
                [
                    'id' => $p['id'],
                    'category' => $p['category'],
                    'description' => $p['desc'],
                    'status' => 'Aktif',
                ]
            );
        }

        // 5. Payment Methods
        $methods = [
            ['id' => 'pm_bca', 'name' => 'BCA', 'cat' => 'Bank Transfer', 'detail' => 'Rekening BCA BMSS No. 123456789'],
            ['id' => 'pm_mandiri', 'name' => 'Mandiri', 'cat' => 'Bank Transfer', 'detail' => 'Rekening Mandiri BMSS No. 987654321'],
            ['id' => 'pm_bsi', 'name' => 'BSI', 'cat' => 'Bank Transfer', 'detail' => 'Rekening BSI Maslahat No. 555666777'],
            ['id' => 'pm_qris', 'name' => 'QRIS', 'cat' => 'QRIS', 'detail' => 'QRIS Dinamis BMSS'],
            ['id' => 'pm_cash', 'name' => 'Tunai', 'cat' => 'Tunai', 'detail' => 'Kas / Pembayaran Kantor'],
        ];

        foreach ($methods as $m) {
            PaymentMethod::firstOrCreate(
                ['name' => $m['name']],
                [
                    'id' => $m['id'],
                    'category' => $m['cat'],
                    'detail' => $m['detail'],
                    'is_active' => true,
                ]
            );
        }

        // 6. Campaigns Global
        $campaigns = [
            ['id' => 'cp1', 'name' => 'Sedekah Guru Ngaji', 'prog' => 'Guru Ngaji', 'target' => 2000000000, 'ach' => 1820000000, 'don' => 6230, 'new' => 740, 'days' => 20, 'accent' => 'navy'],
            ['id' => 'cp2', 'name' => 'Sumur #58', 'prog' => 'Sumur', 'target' => 1600000000, 'ach' => 1450000000, 'don' => 4810, 'new' => 520, 'days' => 18, 'accent' => 'gold'],
            ['id' => 'cp3', 'name' => 'Zakat Maal September', 'prog' => 'Zakat', 'target' => 2000000000, 'ach' => 1750000000, 'don' => 3910, 'new' => 610, 'days' => 20, 'accent' => 'red'],
            ['id' => 'cp4', 'name' => 'Gerakan Sedekah Subuh', 'prog' => 'Sedekah Subuh', 'target' => 1500000000, 'ach' => 1300534500, 'don' => 5231, 'new' => 824, 'days' => 30, 'accent' => 'red'],
        ];

        foreach ($campaigns as $c) {
            $prog = Program::where('name', $c['prog'])->first();
            Campaign::firstOrCreate(
                ['name' => $c['name']],
                [
                    'id' => $c['id'],
                    'program_id' => $prog?->id,
                    'target_amount' => $c['target'],
                    'achieved_amount' => $c['ach'],
                    'donors_count' => $c['don'],
                    'new_donors_count' => $c['new'],
                    'days_remaining' => $c['days'],
                    'is_global' => true,
                    'status' => 'Aktif',
                    'description' => "Campaign fundraising global BMSS untuk {$c['name']}.",
                    'accent_color' => $c['accent'],
                ]
            );
        }
    }
}
