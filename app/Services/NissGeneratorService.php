<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class NissGeneratorService
{
    /**
     * Generate an atomic NISS identifier (e.g. NISS-00000001)
     * using database row locking to prevent race condition.
     */
    public function generate(): string
    {
        return DB::transaction(function () {
            // Lock or create sequence record
            $seq = DB::table('niss_sequences')
                ->where('name', 'contact')
                ->lockForUpdate()
                ->first();

            if (! $seq) {
                // Determine highest existing sequence from contacts
                $maxExisting = 0;
                $allNiss = DB::table('contacts')->pluck('niss');
                foreach ($allNiss as $n) {
                    if (preg_match('/(\d+)$/', $n, $matches)) {
                        $maxExisting = max($maxExisting, (int) $matches[1]);
                    }
                }

                DB::table('niss_sequences')->insert([
                    'name' => 'contact',
                    'current_value' => $maxExisting,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $nextVal = $maxExisting + 1;
            } else {
                $nextVal = $seq->current_value + 1;
            }

            DB::table('niss_sequences')
                ->where('name', 'contact')
                ->update([
                    'current_value' => $nextVal,
                    'updated_at' => now(),
                ]);

            return 'NISS-' . str_pad((string) $nextVal, 8, '0', STR_PAD_LEFT);
        });
    }
}
