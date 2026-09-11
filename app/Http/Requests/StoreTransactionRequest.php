<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contact_id' => ['required', 'exists:contacts,id'],
            'type' => ['required', 'in:Zakat,Infak,Sedekah,Wakaf,Lainnya'],
            'program' => ['nullable', 'string', 'max:100'],
            'campaign_id' => ['nullable', 'exists:campaigns,id'],
            'calculation_id' => ['nullable', 'exists:calculations,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string', 'max:100'],
            'transaction_date' => ['nullable', 'date'],
        ];
    }
}
