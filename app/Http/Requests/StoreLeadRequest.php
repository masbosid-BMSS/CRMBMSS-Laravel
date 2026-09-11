<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:100'],
            'source' => ['nullable', 'string', 'max:100'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'stage' => ['required', 'in:Lead Baru,Contacted,Interested,Follow-up,Donasi'],
            'interest' => ['nullable', 'string', 'max:100'],
            'potential_amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
