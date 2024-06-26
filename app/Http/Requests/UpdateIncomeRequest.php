<?php

namespace App\Http\Requests;

use App\Rules\TransactionStartRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateIncomeRequest extends FormRequest
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
            'amount' => 'required|integer',
            'schedule_id' => 'nullable|integer|exists:schedules,id',
            'end_date' => 'nullable|date|after_or_equal:today',
            'income_group_id' => 'required|integer|exists:income_groups,id',
            'transaction_start' => [
                'nullable',
                'integer',
                new TransactionStartRule(),
            ],
        ];
    }
}
