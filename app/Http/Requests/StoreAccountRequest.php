<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'balance' => 'nullable|integer',
            'type' => 'required|string|in:checking,savings,business|unique:accounts,type,NULL,id,deleted_at,NULL',
            'expense_end_date' => 'nullable|date|after_or_equal:today',
            'expense_budget' => 'nullable|integer|min:10',
        ];
    }

    public function messages(): array
    {
        return [
            'type.unique' => 'Account already exists with same type',
            'type.in' => 'Allowed account type are checking, savings, business',
        ];
    }
}
