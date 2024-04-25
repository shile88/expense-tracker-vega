<?php

namespace App\Http\Requests;

use App\Http\Middleware\CheckIsPremiumUser;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountRequest extends FormRequest
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
            'expense_start_date' => ['nullable','date','after_or_equal:today', new CheckIsPremiumUser],
            'expense_end_date' => ['nullable','date','after_or_equal:expense_start_date', new CheckIsPremiumUser],
            'expense_budget' => ['nullable', 'integer', 'min:10', new CheckIsPremiumUser],
            'type' => 'nullable|string|in:checking,savings,business',
        ];
    }
}
