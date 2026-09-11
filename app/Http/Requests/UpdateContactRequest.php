<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        $contact = $this->route('contact');
        return $this->user()->can('update', $contact);
    }

    public function rules(): array
    {
        $contact = $this->route('contact');
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
            'niss' => ['nullable', 'string', 'max:50', Rule::unique('contacts', 'niss')->ignore($contact->id)],
            'tags' => ['nullable'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
