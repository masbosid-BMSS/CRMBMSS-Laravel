<?php

namespace Tests\Feature;

use App\Services\ZakatCalculatorService;
use Tests\TestCase;

class ZakatCalculatorTest extends TestCase
{
    public function test_zakat_maal_calculated_correctly_when_above_nisab(): void
    {
        $service = new ZakatCalculatorService();
        $calc = $service->calculate([
            'assets_total' => 300000000,
            'deductions_total' => 50000000,
            'gold_price' => 2000000, // Nisab = 85 * 2.000.000 = 170.000.000. Net = 250.000.000
            'year_method' => 'h',
            'haul_status' => 'yes',
        ]);

        $this->assertTrue($calc['is_wajib']);
        $this->assertEquals(250000000, $calc['net_amount']);
        $this->assertEquals(6250000, $calc['zakat_amount']); // 2.5% of 250M
    }

    public function test_zakat_maal_not_obligatory_when_below_nisab(): void
    {
        $service = new ZakatCalculatorService();
        $calc = $service->calculate([
            'assets_total' => 100000000,
            'deductions_total' => 0,
            'gold_price' => 2000000, // Nisab = 170.000.000. Net = 100.000.000
            'year_method' => 'h',
            'haul_status' => 'yes',
        ]);

        $this->assertFalse($calc['is_wajib']);
        $this->assertEquals(0, $calc['zakat_amount']);
    }
}
