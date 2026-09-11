<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\WaAccount;

class WhatsAppService
{
    /**
     * Memformat nomor telepon menjadi nomor internasional tanpa + (e.g. 6281234567890)
     */
    public function normalizePhone(?string $phone): string
    {
        if (! $phone) return '';
        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        }
        return $digits;
    }

    /**
     * Generate link wa.me dengan validasi relasi Do Not Contact / Blokir
     */
    public function generateLink(Contact $contact, string $text = ''): ?string
    {
        if ($contact->relation_status === 'Blokir') {
            return null; // Ditolak
        }

        $phone = $this->normalizePhone($contact->phone);
        if (! $phone) return null;

        $url = "https://wa.me/{$phone}";
        if ($text !== '') {
            $url .= '?text=' . urlencode($text);
        }

        return $url;
    }

    /**
     * Validasi kapasitas nomor WA sebelum memasukkan kontak
     */
    public function validateCapacity(?string $waAccountId, int $delta = 1, ?string $contactId = null): bool
    {
        if (! $waAccountId) return true;

        $wa = WaAccount::find($waAccountId);
        if (! $wa || $wa->status !== 'Aktif') return false;

        $currentAssigned = Contact::where('wa_account_id', $waAccountId)
            ->where('is_archived', false)
            ->when($contactId, fn ($q) => $q->where('id', '!=', $contactId))
            ->count();

        return ($currentAssigned + $delta) <= $wa->capacity;
    }
}
