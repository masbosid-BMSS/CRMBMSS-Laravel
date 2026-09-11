<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:100'],
            'source' => ['nullable', 'string', 'max:100'],
            'program' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:Baru,Aktif,Loyal,At Risk,Dormant'],
            'relation_status' => ['required', 'in:Normal,Blokir,Untrust,Bosan'],
            'relationship_note' => ['nullable', 'string'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'wa_account_id' => ['nullable', 'exists:wa_accounts,id'],
            'niss' => ['nullable', 'string', 'max:50', 'unique:contacts,niss'],
            'tags' => ['nullable'],
            'notes' => ['nullable', 'string'],
            // Follow-up generator checkbox option
            'make_followup' => ['nullable', 'boolean'],
            'followup_date' => ['nullable', 'date'],
            'followup_reason' => ['nullable', 'string'],
            'followup_priority' => ['nullable', 'in:Normal,Tinggi,Rendah'],
            'followup_title' => ['nullable', 'string', 'max:255'],
        ];
    }
}
